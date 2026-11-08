<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Alumni;

class AlumniController extends Controller
{
    public function index()
    {
        $alumni = Alumni::query()
            ->orderByDesc('tahun_lulus')
            ->orderBy('nama')
            ->get()
            ->map(fn (Alumni $a) => [
                'nama' => $a->nama,
                'foto' => $a->foto_url,
                'inisial' => strtoupper(mb_substr($a->nama, 0, 1)),
                'jenis_kelamin' => $a->jenis_kelamin,
                'tahun_lulus' => (int) $a->tahun_lulus,
                'jenjang' => $a->jenjang,
                'kegiatan' => $a->kegiatan,
                'instansi' => $a->instansi,
                'testimoni' => $a->testimoni,
            ]);

        $angkatanList = $alumni->pluck('tahun_lulus')->unique()->sortDesc()->values();
        $kegiatanList = $alumni->pluck('kegiatan')->filter()->unique()->values();

        $statistik = [
            'total' => $alumni->count(),
            'angkatan' => $angkatanList->count(),
            'kuliah' => $alumni->where('kegiatan', 'Kuliah')->count(),
            'bekerja' => $alumni->whereIn('kegiatan', ['Bekerja', 'Wirausaha'])->count(),
        ];

        return view('pages.alumni', compact('alumni', 'angkatanList', 'kegiatanList', 'statistik'));
    }
}
