<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Album;
use Illuminate\Support\Facades\Storage;

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
                    'sampul'    => $a->sampul ? Storage::url($a->sampul) : null,
                    'foto'      => $a->media->map(function ($m) {
                        $poster = $m->poster ? Storage::url($m->poster) : null;

                        return match ($m->tipe) {
                            'foto'  => ['gambar' => Storage::url($m->path), 'keterangan' => $m->keterangan],
                            'video' => ['tipe' => 'video', 'url' => Storage::url($m->path), 'poster' => $poster, 'keterangan' => $m->keterangan],
                            default => ['url' => $m->url, 'poster' => $poster, 'keterangan' => $m->keterangan],
                        };
                    })->all(),
                ];
            })->all();

        return view('pages.galeri', compact('galeri'));
    }
}
