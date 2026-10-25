<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\PrestasiSantri;

class PrestasiController extends Controller
{
    public function index()
    {
        $prestasi = PrestasiSantri::query()
            ->orderByDesc('tahun')
            ->orderByRaw('peringkat = 0, peringkat')   // juara 1, 2, 3 dulu, penghargaan di akhir
            ->orderByDesc('id')
            ->get()
            ->map(fn ($p) => [
                'judul'     => $p->judul,
                'nama'      => $p->nama,
                'kelas'     => $p->kelas,
                'kategori'  => $p->kategori,
                'tingkat'   => $p->tingkat,
                'wilayah'   => $p->wilayah,
                'tahun'     => (int) $p->tahun,
                'peringkat' => (int) $p->peringkat,
                'label'     => $p->label,
                'deskripsi' => $p->deskripsi ?? '',
            ]);

        return view('pages.prestasi', compact('prestasi'));
    }
}
