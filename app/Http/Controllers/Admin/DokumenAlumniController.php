<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DokumenSantri;
use App\Models\Santri;
use App\Support\Akademik;
use App\Support\Akses;
use App\Support\CatatanAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Dokumen kelulusan (ijazah, SKL, transkrip, sertifikat).
 *
 * Hanya Admin Jenjang/Superadmin yang mengunggah. Alumni hanya mengunduh.
 */
class DokumenAlumniController extends Controller
{
    public function store(Request $request, int $id)
    {
        abort_unless(auth()->user()->bisa('alumni.ubah'), 403);

        $santri = Akademik::santriQuery(auth()->user())->findOrFail($id);

        $data = $request->validate([
            'jenis' => ['required', Rule::in(array_keys(DokumenSantri::JENIS))],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ], [
            'jenis.required' => 'Jenis dokumen wajib dipilih.',
            'file.required' => 'Pilih berkas dokumen yang akan diunggah.',
            'file.mimes' => 'Dokumen harus PDF atau gambar (JPG/PNG).',
            'file.max' => 'Ukuran dokumen maksimal 10 MB.',
        ]);

        $file = $request->file('file');
        $folder = 'dokumen-santri/'.$santri->id;
        $namaFile = $data['jenis'].'-'.now()->format('YmdHis').'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $namaFile, 'public');

        $dokumen = DokumenSantri::updateOrCreate(
            ['santri_id' => $santri->id, 'jenis' => $data['jenis']],
            [
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'status' => 'published',
                'diunggah_oleh' => auth()->id(),
            ]
        );

        CatatanAktivitas::catat('unggah_dokumen_alumni', $dokumen, [
            'santri_id' => $santri->id,
            'jenis' => $dokumen->jenis,
            'file' => $dokumen->file_name,
        ]);

        return back()->with('success', 'Dokumen '.$dokumen->labelJenis().' berhasil diunggah dan bisa diunduh alumni.');
    }

    public function destroy(DokumenSantri $dokumen)
    {
        abort_unless(auth()->user()->bisa('alumni.ubah'), 403);

        $milikJenjang = Akademik::santriQuery(auth()->user())
            ->whereKey($dokumen->santri_id)
            ->exists();

        abort_unless($milikJenjang, 403, 'Dokumen ini bukan milik jenjang yang Anda tangani.');

        if (filled($dokumen->file_path)) {
            Storage::disk('public')->delete($dokumen->file_path);
        }

        $jenis = $dokumen->labelJenis();
        $dokumen->delete();

        CatatanAktivitas::catat('hapus_dokumen_alumni', null, ['jenis' => $jenis]);

        return back()->with('success', 'Dokumen '.$jenis.' berhasil dihapus.');
    }
}
