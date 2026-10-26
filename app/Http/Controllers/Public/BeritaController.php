<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Berita;

class BeritaController extends Controller
{
    public function index()
    {
        $headline = Berita::published()->latest('published_at')->first();

        $sub = Berita::published()
            ->when($headline, fn ($q) => $q->where('id', '!=', $headline->id))
            ->latest('published_at')->take(2)->get();

        $populer = Berita::published()->orderByDesc('views')->take(4)->get();

        $sudahTampil = $sub->pluck('id')->push($headline?->id)->filter();

        $indeks = Berita::published()
            ->whereNotIn('id', $sudahTampil)
            ->latest('published_at')
            ->paginate(6);

        return view('pages.berita', compact('headline', 'sub', 'populer', 'indeks'));
    }

    public function show(Berita $berita)
    {
        abort_unless(
            $berita->status === 'published' && $berita->published_at <= now(),
            404
        );

        $berita->increment('views');

        $terkait = Berita::published()
            ->where('id', '!=', $berita->id)
            ->latest('published_at')->take(3)->get();

        return view('pages.berita-detail', compact('berita', 'terkait'));
    }
}
