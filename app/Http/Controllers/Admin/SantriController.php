<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranOnline;
use App\Support\Akses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Santri;
// Jika menggunakan Maatwebsite/Excel, uncomment baris di bawah:
// use App\Imports\SantriImport;
// use Maatwebsite\Excel\Facades\Excel;

class SantriController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Santri::with(['user', 'pendaftaranOnline'])->latest();

        // Admin Jenjang hanya melihat santri pada jenjangnya.
        if (Akses::dibatasiJenjang($user)) {
            $query->whereIn('jenjang_pendidikan', Akses::nilaiJenjangDb($user, Santri::class, 'jenjang_pendidikan') ?: ['__tidak_ada__']);
        }

        $santris = $query->paginate(10);

        return view('dashboard.santri.index', compact('santris'));
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

        return view('dashboard.santri.detail', compact('santri', 'pendaftaranOnline'));
    }

    /** Hapus data santri. Arsip pendaftarannya dikembalikan ke status pending. */
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
            $santri->pendaftaranOnline?->update(['status_pendaftaran' => 'pending']);

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
                ->with('error', 'Terjadi kesalahan saat mengimport data: ' . $e->getMessage());
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