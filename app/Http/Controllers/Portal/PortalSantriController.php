<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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
        Gate::authorize('portal.lihatAnak', $santri);

        $santri->load([
            'kelasModel.waliKelas',
            'pendaftaranOnline',
            'hafalan' => fn ($q) => $q->latest('tanggal'),
            'pelanggaran' => fn ($q) => $q->latest('tanggal'),
            'izin' => fn ($q) => $q->latest(),
            // Detail anak juga hanya boleh menampilkan nilai yang sudah terbit.
            'nilai' => fn ($q) => $q->with('mataPelajaran.guru')
                ->where('status', 'published')
                ->latest(),
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
