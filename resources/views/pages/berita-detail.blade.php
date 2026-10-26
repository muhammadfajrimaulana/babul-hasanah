@extends('layouts.app')

@section('title', $berita->judul . ' - Babul Hasanah')

@section('content')
<style>
    .bd-wrap { padding: 48px 0 72px; }
    .bd-article { max-width: 780px; margin: 0 auto; }
    .bd-cat { font-size: 11px; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--gold); }
    .bd-title { font-size: clamp(26px, 4vw, 38px); font-weight: 700; color: var(--green); line-height: 1.25; margin: 10px 0 14px; }
    .bd-meta { font-size: 12.5px; color: var(--muted); display: flex; gap: 16px; flex-wrap: wrap; }
    .bd-img { width: 100%; max-height: 440px; object-fit: cover; border-radius: 24px; margin: 28px 0; }
    .bd-body { font-size: 15.5px; line-height: 1.9; color: var(--ink); white-space: pre-line; }
    .bd-rel { max-width: 1000px; margin: 64px auto 0; }
    .bd-rel h3 { font-size: 20px; font-weight: 600; color: var(--green); margin-bottom: 20px; }
    .bd-rel-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .bd-rel a { background: #fff; border: 1px solid var(--line); border-radius: 18px; overflow: hidden; display: block; transition: transform .25s; }
    .bd-rel a:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }
    .bd-rel img { width: 100%; height: 140px; object-fit: cover; }
    .bd-rel div { padding: 14px 16px; font-size: 13.5px; font-weight: 600; color: var(--green); line-height: 1.4; }
    @media (max-width: 720px) { .bd-rel-grid { grid-template-columns: 1fr; } }
</style>

<section class="bd-wrap">
    <div class="site-container">
        <article class="bd-article">
            <a href="{{ route('berita.index') }}" style="font-size:13px;font-weight:600;color:var(--emerald-d)">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Berita
            </a>

            <div style="margin-top:24px">
                <span class="bd-cat">{{ $berita->kategori }}</span>
                <h1 class="bd-title">{{ $berita->judul }}</h1>
                <div class="bd-meta">
                    <span><i class="fa-regular fa-calendar"></i> {{ $berita->published_at->translatedFormat('d F Y') }}</span>
                    <span><i class="fa-regular fa-user"></i> {{ $berita->penulis }}</span>
                    <span><i class="fa-regular fa-eye"></i> {{ number_format($berita->views) }}x dibaca</span>
                </div>
            </div>

            @if ($berita->gambar)
                <img src="{{ $berita->gambar_url }}" alt="{{ $berita->judul }}" class="bd-img">
            @endif

            <div class="bd-body">{{ $berita->isi }}</div>
        </article>

        @if ($terkait->count())
            <div class="bd-rel">
                <h3>Berita Lainnya</h3>
                <div class="bd-rel-grid">
                    @foreach ($terkait as $t)
                        <a href="{{ route('berita.show', $t) }}">
                            <img src="{{ $t->gambar_url }}" alt="{{ $t->judul }}">
                            <div>{{ $t->judul }}</div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
