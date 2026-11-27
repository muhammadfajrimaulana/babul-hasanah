<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Support\GambarPublik;

class GaleriController extends Controller
{
    public function index()
    {
        $galeri = Album::with('media')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get()
            ->map(function ($a) {
                return [
                    'judul'     => $a->judul,
                    'kategori'  => $a->kategori,
                    'tanggal'   => $a->tanggal?->toDateString(),
                    'deskripsi' => $a->deskripsi,
                    'sampul'    => GambarPublik::dariStorage($a->sampul),
                    'foto'      => $a->media->map(function ($m) {
                        $poster = GambarPublik::dariStorage($m->poster);

                        return match ($m->tipe) {
                            // Berkas upload yang hilang dibuang, jangan dikirim sebagai <img> mati.
                            'foto'  => ($path = GambarPublik::dariStorage($m->path))
                                ? ['gambar' => $path, 'keterangan' => $m->keterangan]
                                : null,
                            'video' => ($path = GambarPublik::dariStorage($m->path))
                                ? ['tipe' => 'video', 'url' => $path, 'poster' => $poster, 'keterangan' => $m->keterangan]
                                : null,
                            default => ['url' => $m->url, 'poster' => $poster, 'keterangan' => $m->keterangan],
                        };
                    })->filter()->values()->all(),
                ];
            })->all();

        return view('pages.galeri', compact('galeri'));
    }
}
