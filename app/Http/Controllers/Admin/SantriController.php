<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\PendaftaranOnline;
use App\Models\Santri;
use App\Support\Akses;
use App\Support\Jenjang;
use App\Support\Kelulusan;
use App\Support\RekapJenjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Jika menggunakan Maatwebsite/Excel, uncomment baris di bawah:
// use App\Imports\SantriImport;
// use Maatwebsite\Excel\Facades\Excel;

class SantriController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $tautanSiap = Kelulusan::siapTautan();
        $batasiJenjang = Akses::dibatasiJenjang($user);

        // Admin Jenjang terkunci pada jenjangnya; role global (Super Admin,
        // Pengasuh, Pembina) bisa memilih satu jenjang atau semua jenjang.
        $filterJenjang = $batasiJenjang
            ? (string) (Akses::jenjangSlug($user) ?? '')
            : (string) (Jenjang::normalisasi($request->input('jenjang')) ?? '');

        $query = Santri::with($tautanSiap ? ['user', 'pendaftaranOnline', 'alumni'] : ['user', 'pendaftaranOnline'])->latest();

        if ($batasiJenjang) {
            $query->whereIn('jenjang_pendidikan', Akses::nilaiJenjangDb($user, Santri::class, 'jenjang_pendidikan') ?: ['__tidak_ada__']);
        } elseif ($filterJenjang !== '') {
            $nilaiJenjang = RekapJenjang::nilaiDb(Santri::class, 'jenjang_pendidikan', $filterJenjang);
            $query->whereIn('jenjang_pendidikan', $nilaiJenjang ?: ['__tidak_ada__']);
        }

        $santris = $query->paginate(10)->withQueryString();

        return view('dashboard.santri.index', [
            'santris' => $santris,
            'kelulusanSiap' => Kelulusan::siap(),
            'tautanSiap' => $tautanSiap,
            'filterJenjang' => $filterJenjang,
            'daftarJenjang' => $batasiJenjang ? [] : Jenjang::semua(),
            'bisaFilterJenjang' => ! $batasiJenjang,
            'namaJenjangAdmin' => $batasiJenjang ? Akses::namaJenjang($user) : null,
        ]);
    }

    public function detail($id)
    {
        $santri = Santri::with(['user', 'pendaftaranOnline'])->findOrFail($id);

        abort_unless(
            Akses::cocokJenjang(auth()->user(), $santri->jenjang_pendidikan),
            403,
            'Data santri ini berada di luar jenjang yang Anda tangani.'
        );

        // Utamakan relasi pendaftaran_online_id; fallback ke email untuk
        // data lama yang dibuat sebelum kolom relasi tersedia.
        $pendaftaranOnline = $santri->pendaftaranOnline
            ?: ($santri->user?->email
                ? PendaftaranOnline::where('email', $santri->user->email)->latest()->first()
                : null);

        $kelulusanSiap = Kelulusan::siap();
        $tautanSiap = Kelulusan::siapTautan();
        $alumniTertaut = $tautanSiap
            ? Alumni::where('santri_id', $santri->id)->first()
            : null;

        return view('dashboard.santri.detail', compact(
            'santri',
            'pendaftaranOnline',
            'kelulusanSiap',
            'tautanSiap',
            'alumniTertaut'
        ));
    }

    /**
     * Tandai santri lulus dan otomatis buatkan data alumni.
     *
     * Tahun lulus opsional; jika kosong dipakai tahun berjalan.
     */
    public function luluskan(Request $request, $id)
    {
        $santri = Santri::with(['user', 'pendaftaranOnline'])->findOrFail($id);

        abort_unless(
            Akses::cocokJenjang(auth()->user(), $santri->jenjang_pendidikan),
            403,
            'Data santri ini berada di luar jenjang yang Anda tangani.'
        );

        if (! Kelulusan::siap()) {
            return back()->with(
                'error',
                'Kolom kelulusan belum tersedia. Jalankan php artisan migrate terlebih dahulu.'
            );
        }

        $data = $request->validate([
            'tahun_lulus' => ['nullable', 'integer', 'min:1998', 'max:'.(now()->year + 1)],
        ], [
            'tahun_lulus.min' => 'Tahun lulus tidak valid.',
            'tahun_lulus.max' => 'Tahun lulus tidak boleh melebihi tahun depan.',
        ]);

        $alumni = Kelulusan::luluskan($santri, $data['tahun_lulus'] ?? null);

        $pesan = $santri->nama_lengkap.' ditandai LULUS'
            .($alumni ? ' dan otomatis masuk ke Data Alumni angkatan '.$alumni->tahun_lulus.'.' : '.');

        if (! $alumni) {
            $pesan .= ' Jalankan php artisan migrate agar data alumni ikut terhubung.';
        }

        return back()->with('success', $pesan);
    }

    /** Batalkan status lulus santri beserta data alumni yang dibuat otomatis. */
    public function batalkanKelulusan($id)
    {
        $santri = Santri::findOrFail($id);

        abort_unless(
            Akses::cocokJenjang(auth()->user(), $santri->jenjang_pendidikan),
            403,
            'Data santri ini berada di luar jenjang yang Anda tangani.'
        );

        if (! Kelulusan::siap()) {
            return back()->with(
                'error',
                'Kolom kelulusan belum tersedia. Jalankan php artisan migrate terlebih dahulu.'
            );
        }

        Kelulusan::batalkan($santri);

        return back()->with(
            'success',
            'Status kelulusan '.$santri->nama_lengkap.' dibatalkan. Data alumni terkait ikut dihapus.'
        );
    }

    /**
     * Hapus data santri. Arsip pendaftarannya tetap tersimpan.
     *
     * Bila santri belum lulus, status pendaftaran dikembalikan ke pending agar
     * bisa diverifikasi ulang. Bila santri sudah lulus/alumni, status arsip
     * "diterima" dipertahankan supaya riwayat PPDB tidak berubah.
     */
    public function destroy($id)
    {
        $santri = Santri::with('pendaftaranOnline')->findOrFail($id);

        abort_unless(
            Akses::cocokJenjang(auth()->user(), $santri->jenjang_pendidikan),
            403,
            'Data santri ini berada di luar jenjang yang Anda tangani.'
        );

        DB::transaction(function () use ($santri) {
            // Status pendaftaran dikembalikan ke pending supaya data bisa
            // diverifikasi ulang bila santri dihapus karena kekeliruan.
            $sudahLulus = ($santri->status_kelulusan ?? null) === 'lulus'
                || (Kelulusan::siapTautan() && $santri->alumni()->exists());

            $santri->pendaftaranOnline?->update([
                'status_pendaftaran' => $sudahLulus ? 'diterima' : 'pending',
            ]);

            $santri->delete();
        });

        return redirect()
            ->route('admin.santri.index')
            ->with('success', 'Data santri berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            // Contoh implementasi jika menggunakan Laravel Excel:
            // Excel::import(new SantriImport, $request->file('file_excel'));

            return redirect()->route('admin.santri.index')
                ->with('success', 'Data santri berhasil diimport ke dalam sistem.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengimport data: '.$e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $filePath = public_path('assets/template/template_data_santri.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        return redirect()->back()->with('error', 'File template import belum tersedia.');
    }
}
