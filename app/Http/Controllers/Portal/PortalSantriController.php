<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use Illuminate\Http\Request;

/**
 * Detail anak pada portal wali — hanya baca.
 *
 * Wali tidak punya tombol edit data. Perubahan harus diajukan ke Admin
 * Jenjang lewat menu "Ajukan Perubahan Data".
 */
class PortalSantriController extends Controller
{
    public function show(Request $request, Santri $santri)
    {
        abort_unless($request->user()->memilikiSantri($santri->id), 403,
            'Data santri ini bukan milik akun Anda.');

        $santri->load([
            'kelasModel.waliKelas',
            'pendaftaranOnline',
            'hafalan' => fn ($q) => $q->latest('tanggal'),
            'pelanggaran' => fn ($q) => $q->latest('tanggal'),
            'izin' => fn ($q) => $q->latest(),
            'nilai' => fn ($q) => $q->with('mataPelajaran')->latest(),
            'absensi' => fn ($q) => $q->latest('tanggal')->limit(50),
            'dokumen',
            'riwayatKelas' => fn ($q) => $q->latest(),
            'alumni',
        ]);

        return view('portal.anak.show', [
            'santri' => $santri,
            'anakList' => $request->user()->daftarAnak(),
        ]);
    }
}
