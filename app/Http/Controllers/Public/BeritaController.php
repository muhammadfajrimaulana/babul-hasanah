<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        // Filter kategori opsional (?kategori=Kegiatan) — hanya nilai yang dikenal.
        $kategori = $request->query('kategori');
        if ($kategori !== null && ! in_array($kategori, Berita::KATEGORI, true)) {
            $kategori = null;
        }

        $query = Berita::published()
            ->terbaru()
            ->when($kategori, fn ($q, $k) => $q->where('kategori', $k));

        // Berita terbaru selalu jadi headline di kolom utama.
        $headline = (clone $query)->first();

        $sub = (clone $query)
            ->when($headline, fn ($q) => $q->where('id', '!=', $headline->id))
            ->take(2)
            ->get();

        $sudahTampil = $sub->pluck('id')->push($headline?->id)->filter()->all();

        $populer = Berita::published()
            ->when($kategori, fn ($q, $k) => $q->where('kategori', $k))
            ->whereNotIn('id', $sudahTampil)
            ->orderByDesc('views')
            ->terbaru()
            ->take(4)
            ->get();

        $indeks = (clone $query)
            ->whereNotIn('id', $sudahTampil)
            ->paginate(6)
            ->withQueryString();

        $perKategori = Berita::published()
            ->selectRaw('kategori, count(*) as total')
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->pluck('total', 'kategori');

        $totalBerita = Berita::published()->count();

        return view('pages.berita', compact(
            'headline', 'sub', 'populer', 'indeks',
            'kategori', 'perKategori', 'totalBerita'
        ));
    }

    public function show(Berita $berita)
    {
        abort_unless(
            $berita->status === 'published' &&
            ($berita->published_at === null || $berita->published_at <= now()),
            404
        );

        $berita->increment('views');

        $terkait = Berita::published()
            ->where('id', '!=', $berita->id)
            ->terbaru()
            ->take(3)
            ->get();

        return view('pages.berita-detail', compact('berita', 'terkait'));
    }
}
