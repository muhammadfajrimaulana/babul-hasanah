@extends('layouts.app')

@section('title', 'Berita Pondok Pesantren Babul Hasanah')

@section('content')

@php
    $tanggal = now()->locale('id')->translatedFormat('l, d F Y');
@endphp

<style>
    /* ===== Halaman Berita (memakai variabel warna dari layouts/app) ===== */
    .nw-hero {
        position: relative; overflow: hidden; padding: 56px 0 62px; color: #fff;
        background: linear-gradient(125deg, var(--green) 0%, var(--green-2) 55%, #2a6a50 100%);
    }
    .nw-hero::before {
        content: ""; position: absolute; inset: 0; opacity: .10;
        background-image: radial-gradient(#d1fae5 1px, transparent 1px); background-size: 20px 20px;
    }
    .nw-hero::after {
        content: "\f1ea"; font-family: "Font Awesome 6 Free"; font-weight: 900; position: absolute;
        right: 2%; top: 50%; transform: translateY(-50%); pointer-events: none;
        font-size: clamp(120px, 18vw, 230px); color: rgba(255, 255, 255, .06);
    }
    .nw-hero .site-container { position: relative; z-index: 1; }
    .nw-eyebrow {
        display: inline-flex; align-items: center; gap: 10px; font-size: 11px; font-weight: 600;
        letter-spacing: .14em; text-transform: uppercase; color: var(--gold);
    }
    .nw-eyebrow::before { content: ""; width: 26px; height: 1px; background: currentColor; }
    .nw-hero .nw-eyebrow { color: #e6d3a3; }
    .nw-title {
        color: #fff; font-size: clamp(28px, 4.2vw, 44px); font-weight: 700; line-height: 1.18;
        letter-spacing: -.01em; margin: 12px 0 10px;
    }
    .nw-title span { color: #19d68f; }
    .nw-sub { color: rgba(255, 255, 255, .82); max-width: 620px; font-size: 13.5px; line-height: 1.75; }

    .nw-hero-meta { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 22px; }
    .nw-meta-chip {
        display: inline-flex; align-items: center; gap: 8px; font-size: 12px; color: #dcece3;
        background: rgba(255, 255, 255, .08); border: 1px solid rgba(255, 255, 255, .16);
        border-radius: 999px; padding: 8px 14px;
    }
    .nw-meta-chip i { color: #19d68f; font-size: 11px; }
    .nw-meta-chip b { color: #fff; font-weight: 600; }

    .nw-ticker {
        display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap;
        margin-top: 26px; padding: 12px 16px; border-radius: 16px;
        background: rgba(0, 0, 0, .22); border: 1px solid rgba(255, 255, 255, .14);
        backdrop-filter: blur(6px);
    }
    .nw-ticker-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .nw-pill {
        display: inline-flex; align-items: center; gap: 6px; flex: none;
        background: var(--emerald); color: #fff; font-size: 9.5px; font-weight: 700;
        letter-spacing: .09em; text-transform: uppercase; padding: 5px 11px; border-radius: 999px;
    }
    .nw-ticker-text {
        font-size: 12.5px; color: #eaf5ef; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .nw-ticker-text b { color: #19d68f; font-weight: 600; margin-right: 6px; }
    .nw-date { color: #b9d0c4; font-size: 11.5px; display: inline-flex; align-items: center; gap: 8px; white-space: nowrap; }
    .nw-date i { color: #e6d3a3; }

    /* ===== Body & filter ===== */
    .nw-body { padding: 42px 0 72px; }
    .nw-filter { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; margin-bottom: 26px; }
    .nw-chip {
        display: inline-flex; align-items: center; gap: 7px; font-family: inherit; font-size: 12px;
        font-weight: 500; color: var(--muted); background: #fff; border: 1px solid var(--line);
        border-radius: 999px; padding: 8px 16px; text-decoration: none;
        transition: color .2s ease, border-color .2s ease, background .2s ease, transform .2s ease;
    }
    .nw-chip b { font-size: 10.5px; font-weight: 700; opacity: .75; }
    .nw-chip:hover { border-color: var(--green); color: var(--green); transform: translateY(-1px); }
    .nw-chip.active { background: var(--green); border-color: var(--green); color: #fff; }

    /* ===== Layout ===== */
    .nw-grid { display: grid; grid-template-columns: minmax(0, 1fr) 330px; gap: 28px; align-items: start; }
    .nw-main { display: grid; grid-template-columns: 1fr 1fr; gap: 22px; }

    /* ===== Kartu ===== */
    .nw-card {
        position: relative; display: flex; flex-direction: column; overflow: hidden; text-decoration: none;
        background: #fff; border: 1px solid var(--line); border-radius: 22px;
        transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
    }
    .nw-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: #cfe3d8; }
    .nw-img { position: relative; overflow: hidden; background: var(--cream); }
    .nw-img img { display: block; width: 100%; height: 100%; object-fit: cover; transition: transform .6s ease; }
    .nw-card:hover .nw-img img { transform: scale(1.06); }

    .nw-badge {
        position: absolute; top: 14px; left: 14px; z-index: 1; display: inline-flex; align-items: center; gap: 6px;
        background: rgba(23, 64, 47, .92); color: #fff; font-size: 9.5px; font-weight: 700;
        letter-spacing: .09em; text-transform: uppercase; padding: 5px 11px; border-radius: 999px;
        backdrop-filter: blur(4px);
    }
    .nw-badge.is-new { background: #e6f7ef; color: #0e9f73; }
    .nw-badge .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    .nw-cat {
        display: inline-flex; align-items: center; gap: 7px; font-size: 10.5px; font-weight: 700;
        letter-spacing: .1em; text-transform: uppercase; color: var(--gold);
    }
    .nw-cat::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    [data-kat="Kegiatan"] .nw-cat { color: #0e9f73; }
    [data-kat="Prestasi"] .nw-cat { color: #b7791f; }
    [data-kat="Akademik"] .nw-cat { color: #2f6fd0; }
    [data-kat="Pendidikan"] .nw-cat { color: #7a4fd0; }
    [data-kat="Fasilitas"] .nw-cat { color: #0f8a9e; }
    [data-kat="Pesantren"] .nw-cat { color: #17402f; }
    [data-kat="Ekstrakurikuler"] .nw-cat { color: #c73b60; }

    .nw-h {
        color: var(--green); font-weight: 600; line-height: 1.42; letter-spacing: -.01em;
        transition: color .2s ease; margin: 0;
    }
    .nw-card:hover .nw-h, .nw-pop:hover .nw-h, .nw-kat-item:hover .nw-h { color: var(--emerald-d); }
    .nw-p { font-size: 12.8px; color: var(--muted); line-height: 1.7; margin: 0; }
    .clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }

    .nw-foot {
        display: flex; flex-wrap: wrap; align-items: center; gap: 14px; margin-top: auto;
        padding-top: 13px; border-top: 1px dashed var(--line); font-size: 11.5px; color: var(--muted);
    }
    .nw-foot i { color: var(--gold); font-size: 11px; margin-right: 4px; }
    .nw-foot .right { margin-left: auto; display: inline-flex; align-items: center; gap: 6px; }

    /* Headline utama */
    .nw-feature { grid-column: 1 / -1; display: grid; grid-template-columns: 1.05fr 1fr; }
    .nw-feature .nw-img { min-height: 300px; }
    .nw-feature .nw-img::after {
        content: ""; position: absolute; inset: 0;
        background: linear-gradient(90deg, transparent 60%, rgba(23, 64, 47, .10));
    }
    .nw-feature-body { display: flex; flex-direction: column; justify-content: space-between; gap: 16px; padding: 28px 30px; }
    .nw-feature .nw-h { font-size: clamp(20px, 2.2vw, 25px); margin: 10px 0; }
    .nw-more {
        display: inline-flex; align-items: center; gap: 8px; font-size: 12.5px; font-weight: 600;
        color: var(--emerald-d); transition: gap .2s ease;
    }
    .nw-card:hover .nw-more { gap: 12px; }

    /* Sub headline */
    .nw-item .nw-img { height: 160px; }
    .nw-item-body { display: flex; flex-direction: column; gap: 8px; flex: 1; padding: 18px 20px 20px; }
    .nw-item .nw-h { font-size: 15px; }

    /* ===== Sidebar ===== */
    .nw-side { display: flex; flex-direction: column; gap: 20px; position: sticky; top: 100px; }
    .nw-box { background: #fff; border: 1px solid var(--line); border-radius: 20px; padding: 20px; }
    .nw-box-title {
        display: flex; align-items: center; justify-content: space-between; gap: 10px;
        font-size: 13.5px; font-weight: 600; color: var(--green);
        padding-bottom: 14px; border-bottom: 1px solid var(--line); margin-bottom: 4px;
    }
    .nw-box-title span { display: inline-flex; align-items: center; gap: 9px; }
    .nw-box-title span::before { content: ""; width: 4px; height: 16px; border-radius: 4px; background: var(--emerald); }
    .nw-box-title em { font-style: normal; font-size: 10.5px; font-weight: 700; color: var(--muted); }

    .nw-pop { display: flex; gap: 13px; padding: 13px 0; border-bottom: 1px solid var(--line); text-decoration: none; }
    .nw-pop:last-child { border-bottom: 0; padding-bottom: 0; }
    .nw-num { min-width: 24px; font-size: 24px; font-weight: 700; line-height: 1; color: var(--line); transition: color .2s ease; }
    .nw-pop:hover .nw-num { color: var(--emerald); }
    .nw-pop .nw-h { font-size: 12.8px; margin-top: 5px; }
    .nw-views { display: inline-flex; align-items: center; gap: 5px; font-size: 10.5px; color: var(--muted); margin-top: 5px; }
    .nw-views i { font-size: 10px; color: var(--gold); }

    .nw-kat-item {
        display: flex; align-items: center; justify-content: space-between; gap: 10px; text-decoration: none;
        padding: 10px 0; border-bottom: 1px dashed var(--line); font-size: 12.5px; color: var(--ink);
        transition: color .2s ease;
    }
    .nw-kat-item:last-child { border-bottom: 0; padding-bottom: 0; }
    .nw-kat-item:hover { color: var(--emerald-d); }
    .nw-kat-item b {
        font-size: 10.5px; font-weight: 700; color: var(--muted);
        background: var(--cream); border-radius: 999px; padding: 2px 9px;
    }

    .nw-cta {
        position: relative; overflow: hidden; border-radius: 22px; padding: 24px;
        background: linear-gradient(150deg, var(--green), #2a6a50); color: #cfe0d6; box-shadow: var(--shadow-lg);
    }
    .nw-cta::after {
        content: "\f19d"; font-family: "Font Awesome 6 Free"; font-weight: 900; position: absolute;
        right: -10px; bottom: -26px; font-size: 110px; color: rgba(255, 255, 255, .07); pointer-events: none;
    }
    .nw-cta .nw-pill { position: relative; z-index: 1; background: rgba(255, 255, 255, .14); color: #eaf5ef; }
    .nw-cta h4 { position: relative; z-index: 1; color: #fff; font-size: 17px; font-weight: 600; line-height: 1.4; margin: 14px 0 8px; }
    .nw-cta p { position: relative; z-index: 1; font-size: 12.3px; line-height: 1.7; margin-bottom: 18px; }
    .nw-cta .nw-cta-btn {
        position: relative; z-index: 1; display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        width: 100%; background: #fff; color: var(--green); font-size: 12.5px; font-weight: 700;
        padding: 12px 18px; border-radius: 12px; text-decoration: none; transition: transform .2s ease, box-shadow .2s ease;
    }
    .nw-cta .nw-cta-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 24px rgba(0, 0, 0, .22); }

    /* ===== Indeks ===== */
    .nw-sec-head {
        display: flex; align-items: flex-end; justify-content: space-between; gap: 16px;
        flex-wrap: wrap; margin: 54px 0 22px; padding-top: 30px; border-top: 1px solid var(--line);
    }
    .nw-sec-title { font-size: 22px; font-weight: 600; color: var(--green); margin-top: 4px; }
    .nw-sec-note { font-size: 12px; color: var(--muted); }
    .nw-index { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; }
    .nw-index .nw-img { height: 170px; }

    /* ===== Kosong ===== */
    .nw-empty {
        text-align: center; padding: 64px 24px; background: #fff;
        border: 1px dashed var(--line); border-radius: 22px;
    }
    .nw-empty i { display: block; font-size: 34px; color: var(--line); margin-bottom: 14px; }
    .nw-empty h3 { font-size: 17px; color: var(--green); margin-bottom: 6px; }
    .nw-empty p { font-size: 12.8px; color: var(--muted); margin-bottom: 18px; }
    .nw-empty a {
        display: inline-flex; align-items: center; gap: 8px; background: var(--green); color: #fff;
        font-size: 12.5px; font-weight: 600; padding: 11px 20px; border-radius: 12px; text-decoration: none;
    }

    @media (max-width: 1024px) {
        .nw-grid { grid-template-columns: 1fr; }
        .nw-side { position: static; }
        .nw-index { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 720px) {
        .nw-hero { padding: 40px 0 46px; }
        .nw-hero::after { display: none; }
        .nw-body { padding: 32px 0 56px; }
        .nw-main, .nw-feature, .nw-index { grid-template-columns: 1fr; }
        .nw-feature .nw-img { min-height: 210px; }
        .nw-feature-body { padding: 22px; }
        .nw-feature .nw-h { font-size: 19px; }
        .nw-ticker-text { white-space: normal; }
    }
</style>

{{-- ===================== HERO / JUDUL HALAMAN ===================== --}}
<section class="nw-hero">
    <div class="site-container">
        <span class="nw-eyebrow">Kabar Pesantren</span>
        <h1 class="nw-title">Berita &amp; <span>Informasi</span></h1>
        <p class="nw-sub">Ikuti perkembangan kegiatan, prestasi, dan pengumuman terbaru dari Pondok Pesantren Babul Hasanah.</p>

        <div class="nw-hero-meta">
            <span class="nw-meta-chip"><i class="fa-regular fa-newspaper"></i> <b>{{ $totalBerita }}</b> berita terbit</span>
            <span class="nw-meta-chip"><i class="fa-solid fa-tags"></i> <b>{{ $perKategori->count() }}</b> kategori</span>
            <span class="nw-meta-chip"><i class="fa-regular fa-calendar-days"></i> {{ $tanggal }}</span>
        </div>

        <div class="nw-ticker">
            <div class="nw-ticker-left">
                <span class="nw-pill"><i class="fa-solid fa-bolt"></i> Terbaru</span>
                <span class="nw-ticker-text">
                    @if ($headline)
                        <b>{{ $headline->kategori }}:</b>{{ $headline->judul }}
                    @else
                        <b>Informasi:</b>Belum ada berita yang dipublikasikan.
                    @endif
                </span>
            </div>
            @if ($headline)
                <a href="{{ route('berita.show', $headline) }}" class="nw-date" style="text-decoration:none">
                    <i class="fa-solid fa-arrow-right"></i> Baca selengkapnya
                </a>
            @endif
        </div>
    </div>
</section>

{{-- ===================== ISI ===================== --}}
<section class="nw-body">
    <div class="site-container">

        @if ($perKategori->isNotEmpty())
            <nav class="nw-filter" aria-label="Filter kategori berita">
                <a href="{{ route('berita.index') }}" class="nw-chip {{ $kategori ? '' : 'active' }}">
                    Semua <b>{{ $totalBerita }}</b>
                </a>
                @foreach ($perKategori as $nama => $jml)
                    <a href="{{ route('berita.index', ['kategori' => $nama]) }}"
                       class="nw-chip {{ $kategori === $nama ? 'active' : '' }}">
                        {{ $nama }} <b>{{ $jml }}</b>
                    </a>
                @endforeach
            </nav>
        @endif

        @if (!$headline)
            <div class="nw-empty">
                <i class="fa-regular fa-newspaper"></i>
                <h3>{{ $kategori ? 'Belum ada berita pada kategori ' . $kategori : 'Belum ada berita yang dipublikasikan' }}</h3>
                <p>Kabar terbaru dari pesantren akan tampil di sini begitu dipublikasikan oleh admin.</p>
                @if ($kategori)
                    <a href="{{ route('berita.index') }}"><i class="fa-solid fa-arrow-left"></i> Lihat semua berita</a>
                @endif
            </div>
        @else
        <div class="nw-grid">

            <div class="nw-main">
                {{-- Berita terbaru selalu tampil di kolom utama --}}
                <a href="{{ route('berita.show', $headline) }}" class="nw-card nw-feature" data-kat="{{ $headline->kategori }}">
                    <div class="nw-img">
                        <img src="{{ $headline->gambar_url }}" alt="{{ $headline->judul }}" loading="eager">
                        <span class="nw-badge {{ $headline->baru ? 'is-new' : '' }}">
                            @if ($headline->baru)<span class="dot"></span>@endif
                            {{ $headline->baru ? 'Terbaru' : 'Utama' }}
                        </span>
                    </div>
                    <div class="nw-feature-body">
                        <div>
                            <span class="nw-cat">{{ $headline->kategori }}</span>
                            <h2 class="nw-h">{{ $headline->judul }}</h2>
                            <p class="nw-p clamp-3">{{ $headline->ringkasan_otomatis }}</p>
                        </div>
                        <div>
                            <div class="nw-foot">
                                <span><i class="fa-regular fa-calendar"></i>{{ $headline->tanggal_terbit->translatedFormat('d M Y') }}</span>
                                <span><i class="fa-regular fa-clock"></i>{{ $headline->menit_baca }} menit baca</span>
                                <span class="right"><i class="fa-regular fa-eye"></i>{{ number_format($headline->views) }}</span>
                            </div>
                            <span class="nw-more" style="margin-top:14px">Baca Berita <i class="fa-solid fa-arrow-right"></i></span>
                        </div>
                    </div>
                </a>

                @foreach ($sub as $b)
                    <a href="{{ route('berita.show', $b) }}" class="nw-card nw-item" data-kat="{{ $b->kategori }}">
                        <div class="nw-img">
                            <img src="{{ $b->gambar_url }}" alt="{{ $b->judul }}" loading="lazy">
                            @if ($b->baru)
                                <span class="nw-badge is-new"><span class="dot"></span>Baru</span>
                            @endif
                        </div>
                        <div class="nw-item-body">
                            <span class="nw-cat">{{ $b->kategori }}</span>
                            <h3 class="nw-h clamp-2">{{ $b->judul }}</h3>
                            <p class="nw-p clamp-2">{{ $b->ringkasan_otomatis }}</p>
                            <div class="nw-foot">
                                <span><i class="fa-regular fa-calendar"></i>{{ $b->tanggal_terbit->translatedFormat('d M Y') }}</span>
                                <span class="right"><i class="fa-regular fa-eye"></i>{{ number_format($b->views) }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <aside class="nw-side">
                @if ($populer->isNotEmpty())
                    <div class="nw-box">
                        <div class="nw-box-title"><span>Terpopuler</span><em>{{ $populer->count() }}</em></div>
                        @foreach ($populer as $i => $b)
                            <a href="{{ route('berita.show', $b) }}" class="nw-pop" data-kat="{{ $b->kategori }}">
                                <span class="nw-num">{{ $i + 1 }}</span>
                                <div>
                                    <span class="nw-cat">{{ $b->kategori }}</span>
                                    <h4 class="nw-h clamp-2">{{ $b->judul }}</h4>
                                    <span class="nw-views"><i class="fa-regular fa-eye"></i>{{ number_format($b->views) }}x dibaca</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

                @if ($perKategori->isNotEmpty())
                    <div class="nw-box">
                        <div class="nw-box-title"><span>Kategori</span><em>{{ $perKategori->count() }}</em></div>
                        @foreach ($perKategori as $nama => $jml)
                            <a href="{{ route('berita.index', ['kategori' => $nama]) }}" class="nw-kat-item">
                                {{ $nama }} <b>{{ $jml }}</b>
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="nw-cta">
                    <span class="nw-pill">PPDB 2026</span>
                    <h4>Ingin Mendaftarkan Putra-Putri Anda?</h4>
                    <p>Dapatkan informasi lengkap mengenai syarat pendaftaran, biaya, dan fasilitas asrama.</p>
                    <a href="{{ route('pendaftaran.index') }}" class="nw-cta-btn">
                        Daftar Online Sekarang <i class="fa-solid fa-arrow-right" style="font-size:11px"></i>
                    </a>
                </div>
            </aside>
        </div>

        @if ($indeks->count())
            <div class="nw-sec-head">
                <div>
                    <span class="nw-eyebrow">Arsip</span>
                    <h2 class="nw-sec-title">Indeks Berita Terbaru</h2>
                </div>
                <span class="nw-sec-note">Menampilkan {{ $indeks->firstItem() }}–{{ $indeks->lastItem() }} dari {{ $indeks->total() }} berita</span>
            </div>

            <div class="nw-index">
                @foreach ($indeks as $b)
                    <a href="{{ route('berita.show', $b) }}" class="nw-card" data-kat="{{ $b->kategori }}">
                        <div class="nw-img">
                            <img src="{{ $b->gambar_url }}" alt="{{ $b->judul }}" loading="lazy">
                            @if ($b->baru)
                                <span class="nw-badge is-new"><span class="dot"></span>Baru</span>
                            @endif
                        </div>
                        <div class="nw-item-body">
                            <span class="nw-cat">{{ $b->kategori }}</span>
                            <h3 class="nw-h clamp-2">{{ $b->judul }}</h3>
                            <p class="nw-p clamp-2">{{ $b->ringkasan_otomatis }}</p>
                            <div class="nw-foot">
                                <span><i class="fa-regular fa-calendar"></i>{{ $b->tanggal_terbit->translatedFormat('d M Y') }}</span>
                                <span class="right"><i class="fa-regular fa-eye"></i>{{ number_format($b->views) }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div style="margin-top:32px">{{ $indeks->links() }}</div>
        @endif
        @endif

    </div>
</section>

@endsection
