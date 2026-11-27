<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\DokumenSantri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

/**
 * Dokumen kelulusan anak.
 *
 * Wali tetap bisa login setelah anak lulus dan mengunduh ijazah/transkrip.
 * Alumni hanya bisa mengunduh; unggah tetap dipegang Admin Jenjang.
 */
class PortalDokumenController extends Controller
{
    public function index(Request $request)
    {
        $anak = $request->user()->daftarAnak()->load(['dokumen', 'alumni']);

        return view('portal.dokumen.index', [
            'anak' => $anak,
            'jenisDokumen' => DokumenSantri::JENIS,
        ]);
    }

    public function unduh(Request $request, DokumenSantri $dokumen)
    {
        $user = $request->user();

        abort_unless($user->memilikiSantri($dokumen->santri_id), 403,
            'Dokumen ini bukan milik akun Anda.');
        Gate::authorize('portal.lihatDokumenSantri', $dokumen);

        abort_unless($dokumen->status === 'published', 404, 'Dokumen belum diterbitkan.');

        abort_unless(
            filled($dokumen->file_path) && Storage::disk('public')->exists($dokumen->file_path),
            404,
            'Berkas dokumen tidak ditemukan.'
        );

        return Storage::disk('public')->download(
            $dokumen->file_path,
            $dokumen->file_name ?: $dokumen->jenis.'.pdf'
        );
    }
}
