<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\KredensialMail;
use App\Models\Alumni;
use App\Models\PendaftaranOnline;
use App\Models\Santri;
use App\Models\User;
use App\Support\Akademik;
use App\Support\Akses;
use App\Support\AlumniAkun;
use App\Support\CatatanAktivitas;
use App\Support\ImportSantri;
use App\Support\Jenjang;
use App\Support\Kelulusan;
use App\Support\RekapJenjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

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

        // Pencarian memakai parameter `q`, konsisten dengan halaman admin lain.
        $cari = mb_substr(trim((string) $request->input('q', '')), 0, 100);

        $query = Santri::with($tautanSiap ? ['user', 'pendaftaranOnline', 'alumni'] : ['user', 'pendaftaranOnline'])->latest();

        if ($batasiJenjang) {
            $query->whereIn('jenjang_pendidikan', Akses::nilaiJenjangDb($user, Santri::class, 'jenjang_pendidikan') ?: ['__tidak_ada__']);
        } elseif ($filterJenjang !== '') {
            $nilaiJenjang = RekapJenjang::nilaiDb(Santri::class, 'jenjang_pendidikan', $filterJenjang);
            $query->whereIn('jenjang_pendidikan', $nilaiJenjang ?: ['__tidak_ada__']);
        }

        if ($cari !== '') {
            // Kolom hasil migrasi lanjutan diguard agar daftar tetap aman bila
            // database belum dimigrasi sampai akhir.
            $kolomPendaftaran = Schema::hasTable('pendaftaran_online')
                ? array_values(array_filter(
                    ['nisn', 'email', 'no_hp_aktif', 'email_santri'],
                    fn ($kolom) => Schema::hasColumn('pendaftaran_online', $kolom)
                ))
                : [];

            $query->where(function ($w) use ($cari, $kolomPendaftaran) {
                $w->where('nama_lengkap', 'like', "%{$cari}%")
                    ->orWhere('nomor_induk', 'like', "%{$cari}%")
                    ->orWhere('nama_wali', 'like', "%{$cari}%")
                    ->orWhere('kelas', 'like', "%{$cari}%");

                if (Santri::siapKolom('email_santri')) {
                    $w->orWhere('email_santri', 'like', "%{$cari}%");
                }

                $w->orWhereHas('user', function ($u) use ($cari) {
                    $u->where('name', 'like', "%{$cari}%")
                        ->orWhere('email', 'like', "%{$cari}%");
                });

                $w->orWhereHas('pendaftaranOnline', function ($p) use ($cari, $kolomPendaftaran) {
                    $p->where('no_pendaftaran', 'like', "%{$cari}%")
                        ->orWhere('nama_lengkap', 'like', "%{$cari}%")
                        ->orWhere('no_hp_ortu', 'like', "%{$cari}%");

                    foreach ($kolomPendaftaran as $kolom) {
                        $p->orWhere($kolom, 'like', "%{$cari}%");
                    }
                });
            });
        }

        $santris = $query->paginate(10)->withQueryString();

        return view('dashboard.santri.index', [
            'santris' => $santris,
            'kelulusanSiap' => Kelulusan::siap(),
            'tautanSiap' => $tautanSiap,
            'filterJenjang' => $filterJenjang,
            'cari' => $cari,
            'daftarJenjang' => $batasiJenjang ? [] : Jenjang::semua(),
            'bisaFilterJenjang' => ! $batasiJenjang,
            'namaJenjangAdmin' => $batasiJenjang ? Akses::namaJenjang($user) : null,
        ]);
    }

    public function detail($id)
    {
        $santri = Santri::with([
            'user',
            'pendaftaranOnline',
            'waliUsers',
            'kelasModel.waliKelas',
            'hafalan' => fn ($q) => $q->latest('tanggal')->limit(10),
            'pelanggaran' => fn ($q) => $q->latest('tanggal')->limit(10),
            'izin' => fn ($q) => $q->latest()->limit(10),
            'dokumen',
            'riwayatKelas' => fn ($q) => $q->latest(),
            'pengajuanPerubahan' => fn ($q) => $q->latest()->limit(10),
        ])->findOrFail($id);

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

        $hasil = $this->prosesLuluskan($santri, $data['tahun_lulus'] ?? null);

        $pesan = $santri->nama_lengkap.' ditandai LULUS'
            .($hasil['alumni'] ? ' dan otomatis masuk ke Data Alumni angkatan '.$hasil['alumni']->tahun_lulus.'.' : '.');

        if ($hasil['user'] && $hasil['password']) {
            $pesan .= $hasil['email_terkirim']
                ? ' Akun alumni dibuat dan kredensial telah dikirim ke '.$hasil['user']->email.'.'
                : ' Akun alumni dibuat, tetapi email kredensial gagal dikirim ke '.$hasil['user']->email
                    .'. Superadmin dapat memperbaiki email/password lewat menu Pengguna.';
        }

        if (! $hasil['alumni']) {
            $pesan .= ' Jalankan php artisan migrate agar data alumni ikut terhubung.';
        }

        return back()->with('success', $pesan);
    }

    /**
     * Tandai banyak santri lulus sekaligus beserta akun alumni mereka.
     *
     * Santri yang sudah lulus atau berada di luar jenjang pengguna dilewati.
     * Kegagalan pada satu santri tidak menghentikan santri lainnya.
     */
    public function luluskanMassal(Request $request)
    {
        abort_unless(auth()->user()->bisa('santri.ubah'), 403);

        if (! Kelulusan::siap()) {
            return back()->with(
                'error',
                'Kolom kelulusan belum tersedia. Jalankan php artisan migrate terlebih dahulu.'
            );
        }

        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
            'tahun_lulus' => ['nullable', 'integer', 'min:1998', 'max:'.(now()->year + 1)],
        ], [
            'ids.required' => 'Pilih minimal satu santri.',
            'ids.min' => 'Pilih minimal satu santri.',
            'tahun_lulus.min' => 'Tahun lulus tidak valid.',
            'tahun_lulus.max' => 'Tahun lulus tidak boleh melebihi tahun depan.',
        ]);

        $santris = Santri::query()
            ->with(['user', 'pendaftaranOnline'])
            ->whereIn('id', array_values(array_unique($data['ids'])))
            ->get();

        $berhasil = 0;
        $akunDibuat = 0;
        $dilewati = 0;
        $gagal = [];

        foreach ($santris as $santri) {
            if (! Akses::cocokJenjang(auth()->user(), $santri->jenjang_pendidikan)) {
                $dilewati++;
                continue;
            }

            if ($santri->sudah_lulus || $santri->status_kelulusan === Santri::STATUS_LULUS) {
                $dilewati++;
                continue;
            }

            try {
                $hasil = $this->prosesLuluskan($santri, $data['tahun_lulus'] ?? null);
                $berhasil++;

                if ($hasil['user'] && $hasil['password']) {
                    $akunDibuat++;
                }
            } catch (\Throwable $e) {
                report($e);
                $gagal[] = $santri->nama_lengkap;
            }
        }

        $pesan = $berhasil.' santri berhasil ditandai LULUS.';

        if ($akunDibuat > 0) {
            $pesan .= ' '.$akunDibuat.' akun alumni dibuat dan kredensialnya dikirim.';
        }

        if ($dilewati > 0) {
            $pesan .= ' '.$dilewati.' santri dilewati (sudah lulus atau di luar jenjang Anda).';
        }

        if ($gagal !== []) {
            $pesan .= ' Gagal: '.implode(', ', array_slice($gagal, 0, 5))
                .(count($gagal) > 5 ? ' dan '.(count($gagal) - 5).' lainnya' : '').'.';
        }

        if ($santris->isEmpty()) {
            $pesan = 'Tidak ada santri yang dipilih.';
        }

        CatatanAktivitas::catat('luluskan_santri_massal', null, [
            'diminta' => count(array_unique($data['ids'])),
            'berhasil' => $berhasil,
            'akun_dibuat' => $akunDibuat,
            'dilewati' => $dilewati,
            'gagal' => count($gagal),
            'tahun_lulus' => $data['tahun_lulus'] ?? null,
            'nama_gagal' => array_slice($gagal, 0, 5),
        ]);

        return back()->with($berhasil > 0 ? 'success' : 'error', $pesan);
    }

    /**
     * Proses kelulusan satu santri: data alumni, akun alumni, dan kredensial.
     *
     * Dipakai oleh aksi tunggal maupun massal agar perlakuannya identik.
     *
     * @return array{alumni: ?Alumni, user: ?User, password: ?string, email_terkirim: bool}
     */
    private function prosesLuluskan(Santri $santri, ?int $tahun): array
    {
        $alumni = Kelulusan::luluskan($santri, $tahun);

        $user = null;
        $passwordAkun = null;
        $emailTerkirim = false;

        if ($alumni) {
            $hasil = AlumniAkun::pastikan($alumni);
            $user = $hasil['user'];
            $passwordAkun = $hasil['password'];

            if ($passwordAkun) {
                try {
                    Mail::to($user->email, $user->name)->send(new KredensialMail(
                        nama: $user->name,
                        email: $user->email,
                        passwordSementara: $passwordAkun,
                        peran: 'Alumni',
                        judul: 'Akun Portal Alumni & Unduh Ijazah',
                        catatan: 'Akun dibuat saat kelulusan. Kredensial di email langsung dipakai untuk masuk dan mengunduh ijazah.',
                        wajibGanti: false,
                    ));
                    $emailTerkirim = true;
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            CatatanAktivitas::catat('luluskan_santri', $santri, [
                'alumni_id' => $alumni->id,
                'akun_alumni' => $user->email,
                'email_terkirim' => $emailTerkirim,
            ]);
        }

        return [
            'alumni' => $alumni,
            'user' => $user,
            'password' => $passwordAkun,
            'email_terkirim' => $emailTerkirim,
        ];
    }

    /** Batalkan status lulus santri (khusus Superadmin). */
    public function batalkanKelulusan($id)
    {
        abort_unless(Akses::superAdmin(auth()->user()), 403);

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

        CatatanAktivitas::catat('batalkan_kelulusan', $santri, [], 'Dibatalkan oleh Superadmin');

        return back()->with(
            'success',
            'Status kelulusan '.$santri->nama_lengkap.' dibatalkan. Data alumni terkait ikut dihapus.'
        );
    }

    /**
     * Tempat sampah santri — hanya Superadmin.
     *
     * onlyTrashed() melengkapi global scope SoftDeletes sehingga halaman ini
     * menampilkan hanya data yang bisa dipulihkan, bukan seluruh santri.
     */
    public function terhapus()
    {
        abort_unless(Akses::superAdmin(auth()->user()), 403);

        $santris = Santri::onlyTrashed()
            ->latest('deleted_at')
            ->paginate(15);

        return view('dashboard.santri.terhapus', compact('santris'));
    }

    /**
     * Tandai santri sebagai terhapus, bukan hard delete.
     *
     * Hanya Superadmin. Baris database sengaja dipertahankan supaya nilai,
     * izin, absensi, dokumen, data alumni, dan tautan PPDB tidak hilang;
     * status pendaftaran juga tidak diubah agar pemulihan tidak perlu
     * verifikasi ulang.
     */
    public function destroy($id)
    {
        abort_unless(Akses::superAdmin(auth()->user()), 403, 'Admin Jenjang tidak boleh menghapus data. Gunakan fitur Arsipkan.');

        $santri = Santri::findOrFail($id);

        abort_unless(
            Akses::cocokJenjang(auth()->user(), $santri->jenjang_pendidikan),
            403,
            'Data santri ini berada di luar jenjang yang Anda tangani.'
        );

        DB::transaction(function () use ($santri) {
            $santri->delete();

            CatatanAktivitas::catat('hapus_santri', $santri, [
                'nama_lengkap' => $santri->nama_lengkap,
                'jenjang' => $santri->jenjang_pendidikan,
                'pendaftaran_online_id' => $santri->pendaftaran_online_id,
            ], 'Ditandai terhapus; dapat dipulihkan oleh Superadmin.');
        });

        return redirect()
            ->route('admin.santri.index')
            ->with('success', 'Data santri dipindahkan ke Tempat Sampah. Riwayat tetap tersimpan dan dapat dipulihkan.');
    }

    /**
     * Import santri dari CSV native. Nama input dipertahankan `file_excel`
     * agar formulir lama tetap kompatibel, tetapi yang diproses adalah CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => ['required', 'file', 'extensions:csv,txt', 'max:5120'],
        ]);

        try {
            $hasil = ImportSantri::proses(
                $request->file('file_excel')->getRealPath(),
                auth()->user()
            );
        } catch (\Throwable $e) {
            CatatanAktivitas::catat('import_santri_csv_gagal', null, [
                'file' => $request->file('file_excel')->getClientOriginalName(),
                'alasan' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Gagal membaca berkas CSV: '.$e->getMessage());
        }

        CatatanAktivitas::catat('import_santri_csv', null, [
            'file' => $request->file('file_excel')->getClientOriginalName(),
            'berhasil' => $hasil['berhasil'],
            'gagal' => $hasil['gagal'],
            'akun_wali_baru' => $hasil['akunWaliBaru'],
            'email_gagal' => $hasil['emailGagal'],
            'contoh_error' => array_slice($hasil['errors'], 0, 3),
        ]);

        $flash = [
            'success' => "Import selesai: {$hasil['berhasil']} santri tersimpan, {$hasil['gagal']} baris dilewati.",
        ];

        if ($hasil['akunWaliBaru'] > 0) {
            $flash['success'] .= " {$hasil['akunWaliBaru']} akun wali baru dibuat.";
        }

        if ($hasil['emailGagal'] > 0) {
            $flash['error'] = "{$hasil['emailGagal']} email kredensial wali gagal dikirim. "
                .'Password dapat diatur ulang melalui menu Pengguna.';
        }

        if ($hasil['errors'] !== []) {
            $flash['import_errors'] = array_slice($hasil['errors'], 0, 20);
        }

        return redirect()->route('admin.santri.index')->with($flash);
    }

    /** Template CSV dihasilkan langsung, tanpa berkas statis di public/. */
    public function downloadTemplate()
    {
        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, ImportSantri::kolomTemplate(), ';');
        fputcsv($handle, [
            'Siti Aminah',
            '2026-001',
            'MTs',
            '7A',
            'Ahmad Amin',
            'Jl. Pesantren No. 1',
            'wali@example.com',
            'siti@example.com',
            '081234567890',
        ], ';');

        rewind($handle);
        $isi = stream_get_contents($handle);
        fclose($handle);

        return response("\xEF\xBB\xBF".$isi, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_data_santri.csv"',
            'Cache-Control'       => 'no-store',
        ]);
    }
}
