@extends('layouts.app')

@section('title', 'Struktur Organisasi - Pondok Pesantren Babul Hasanah')

@section('content')

{{-- Data dikelola admin: Dashboard > Struktur Organisasi (controller: Public\StrukturOrganisasiController) --}}

<style>
    .sj-page { --g900:#0a3a24; --g800:#0f5132; --g700:#13693f; --g600:#15803d; --g500:#10b981; --g50:#f0fdf6;
        --gold:#b7791f; --gold-text:#955f0f; --gold-soft:#fdf3dc; --gold-light:#fde68a; --line:#7fc9a4; color:#1e293b; }
    .sj-page *, .sj-page *::before, .sj-page *::after { box-sizing: border-box; }
    .sj-wrap { width: 100%; max-width: 1180px; margin: 0 auto; padding: 0 24px; }
    .sj-page section[id] { scroll-margin-top: 80px; }

    .sj-eyebrow { display: inline-flex; align-items: center; gap: 10px; font-size: 12px; letter-spacing: .14em; text-transform: uppercase; font-weight: 700; color: var(--gold-text); margin-bottom: 14px; }
    .sj-eyebrow::before { content: ""; width: 28px; height: 2px; background: var(--gold); }
    .sj-eyebrow.light { color: var(--gold-light); }
    .sj-eyebrow.light::before { background: var(--gold-light); }

    /* ====== Hero ====== */
    .sj-hero { position: relative; overflow: hidden; color: #fff; padding: 56px 0 72px;
        background:
            radial-gradient(600px 300px at 90% 0%, rgba(253,230,138,.18), transparent 65%),
            radial-gradient(500px 300px at 0% 100%, rgba(16,185,129,.28), transparent 65%),
            linear-gradient(135deg, var(--g900), var(--g700)); }
    .sj-hero::before { content: ""; position: absolute; inset: 0; opacity: .5;
        background-image: radial-gradient(rgba(255,255,255,.14) 1.5px, transparent 1.5px); background-size: 26px 26px;
        -webkit-mask-image: linear-gradient(to left, #000, transparent 70%); mask-image: linear-gradient(to left, #000, transparent 70%); }
    .sj-hero::after { /* bintang segi delapan, penanda identitas pesantren */
        content: ""; position: absolute; right: -60px; bottom: -90px; width: 380px; height: 380px; pointer-events: none;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cg fill='none' stroke='%23ffffff' stroke-opacity='.22' stroke-width='.8'%3E%3Crect x='20' y='20' width='60' height='60'/%3E%3Crect x='20' y='20' width='60' height='60' transform='rotate(45 50 50)'/%3E%3Ccircle cx='50' cy='50' r='30'/%3E%3Ccircle cx='50' cy='50' r='14'/%3E%3C/g%3E%3C/svg%3E") center / contain no-repeat; }
    .sj-hero .sj-wrap { position: relative; z-index: 1; }
    .sj-crumb { font-size: 14px; color: rgba(255,255,255,.75); margin-bottom: 30px; }
    .sj-crumb a { color: #fff; text-decoration: none; }
    .sj-crumb a:hover { text-decoration: underline; }
    .sj-hero h1 { margin: 0 0 18px; font-size: clamp(2.2rem, 5vw, 3.6rem); line-height: 1.12; color: #fff; max-width: 18ch; }
    .sj-hero h1 span { color: var(--gold-light); }
    .sj-hero p { margin: 0 0 30px; max-width: 58ch; font-size: 1.1rem; line-height: 1.8; color: rgba(255,255,255,.88); }
    .sj-nav { display: flex; flex-wrap: wrap; gap: 10px; }
    .sj-nav a { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 999px; font-weight: 600; font-size: .95rem; text-decoration: none;
        color: #fff; background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.25); transition: all .25s ease; }
    .sj-nav a:hover { background: #fff; color: var(--g800); }
    .sj-nav a:focus-visible { outline: 3px solid var(--gold-light); outline-offset: 2px; }

    /* ====== Section umum ====== */
    .sj-sec { padding: 84px 0; }
    .sj-sec.alt { background: var(--g50); }
    .sj-head { max-width: 680px; margin: 0 0 48px; }
    .sj-head.center { margin-left: auto; margin-right: auto; text-align: center; }
    .sj-head.center .sj-eyebrow { justify-content: center; }
    .sj-head h2 { font-size: clamp(1.7rem, 3.2vw, 2.4rem); line-height: 1.25; margin: 0 0 12px; color: var(--g800); }
    .sj-head p { margin: 0; color: #475569; line-height: 1.75; font-size: 1.05rem; }

    /* ====== Bagan: pohon dengan penghubung otomatis (dipakai partial struktur-node) ====== */
    .tree { overflow-x: auto; padding: 4px 0 14px; }
    .tree ul { list-style: none; margin: 0; padding: 34px 0 0; display: flex; justify-content: center; position: relative; }
    .tree > ul { padding-top: 0; min-width: max-content; margin: 0 auto; }
    .tree li { position: relative; padding: 34px 14px 0; text-align: center; }
    .tree > ul > li { padding-top: 0; }
    .tree ul ul::before { content: ""; position: absolute; top: 0; left: 50%; height: 34px; border-left: 2px solid var(--line); }
    .tree li::before, .tree li::after { content: ""; position: absolute; top: 0; right: 50%; width: 50%; height: 34px; border-top: 2px solid var(--line); }
    .tree li::after { right: auto; left: 50%; border-left: 2px solid var(--line); }
    .tree li:only-child::before, .tree li:only-child::after { display: none; }
    .tree li:only-child { padding-top: 0; }
    .tree li:first-child::before, .tree li:last-child::after { border: 0; }
    .tree li:last-child::before { border-right: 2px solid var(--line); border-radius: 0 14px 0 0; }
    .tree li:first-child::after { border-radius: 14px 0 0 0; }
    .tree > ul > li::before, .tree > ul > li::after { display: none; }
    .tree .person::before { content: ""; position: absolute; top: -6px; left: 50%; width: 10px; height: 10px; margin-left: -5px; border-radius: 50%; background: var(--g500); border: 2px solid #fff; box-shadow: 0 0 0 1px var(--line); }
    .tree > ul > li > .person::before { display: none; }

    .person { position: relative; display: inline-block; width: 240px; background: #fff; border-radius: 22px; padding: 26px 18px 22px;
        border: 1px solid rgba(6,78,59,.14); border-top: 4px solid var(--g500); box-shadow: 0 14px 30px -16px rgba(20,83,45,.28); transition: transform .25s ease, box-shadow .25s ease; }
    .person:hover { transform: translateY(-4px); box-shadow: 0 22px 40px -16px rgba(20,83,45,.4); }
    .avatar { position: relative; width: 92px; height: 92px; border-radius: 50%; margin: 0 auto 14px; overflow: hidden;
        display: flex; align-items: center; justify-content: center; font-size: 36px; color: #fff;
        background: linear-gradient(135deg, #10b981, #0f5132); border: 5px solid #dcfce7; }
    .avatar img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
    .person h3 { margin: 0 0 8px; font-size: 1.05rem; line-height: 1.35; color: #0f172a; overflow-wrap: anywhere; }
    .person span { display: inline-block; font-size: 13px; font-weight: 600; color: var(--g700); background: var(--g50); padding: 5px 14px; border-radius: 999px; }
    .person.gold { border-color: rgba(183,121,31,.3); border-top-color: var(--gold); }
    .person.gold .avatar { background: linear-gradient(135deg, #d99a2b, #b7791f); border-color: var(--gold-soft); }
    .person.gold span { color: var(--gold-text); background: var(--gold-soft); }

    /* Pimpinan: kartu paling besar, dengan motif bintang segi delapan */
    .person.top { width: 300px; border: 0; padding: 32px 22px 28px; overflow: hidden;
        background: linear-gradient(140deg, #0b3f29, #15803d); box-shadow: 0 26px 50px -22px rgba(20,83,45,.7); }
    .person.top > * { position: relative; z-index: 1; }
    .person.top::after { content: ""; position: absolute; right: -52px; bottom: -52px; width: 190px; height: 190px; z-index: 0;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cg fill='none' stroke='%23ffffff' stroke-opacity='.28' stroke-width='1'%3E%3Crect x='20' y='20' width='60' height='60'/%3E%3Crect x='20' y='20' width='60' height='60' transform='rotate(45 50 50)'/%3E%3Ccircle cx='50' cy='50' r='30'/%3E%3Ccircle cx='50' cy='50' r='14'/%3E%3C/g%3E%3C/svg%3E") center / contain no-repeat; }
    .person.top h3 { color: #fff; font-size: 1.25rem; }
    .person.top span { color: var(--gold-light); background: rgba(255,255,255,.16); }
    .person.top .avatar { width: 112px; height: 112px; border-color: rgba(255,255,255,.35); background: linear-gradient(135deg, #d99a2b, #b7791f); }

    .tree-empty { text-align: center; color: #64748b; padding: 40px 0; }
    .tree-empty i { display: block; font-size: 32px; color: #a7d8bd; margin-bottom: 12px; }

    /* ====== Ketua jenjang ====== */
    .jenjang { display: grid; grid-template-columns: repeat(4, 1fr); gap: 22px; }
    .jj { background: #fff; border-radius: 22px; overflow: hidden; border: 1px solid rgba(6,78,59,.14); text-align: center; box-shadow: 0 12px 28px -18px rgba(20,83,45,.35); transition: transform .3s ease, box-shadow .3s ease; }
    .jj:hover { transform: translateY(-5px); box-shadow: 0 24px 38px -18px rgba(20,83,45,.45); }
    .jj-head { position: relative; overflow: hidden; background: linear-gradient(140deg, #0b3f29, #15803d); color: #fff; padding: 24px 16px 56px; }
    .jj-head::after { content: ""; position: absolute; right: -30px; top: -30px; width: 110px; height: 110px; border-radius: 50%; border: 18px solid rgba(255,255,255,.07); }
    .jj:nth-child(even) .jj-head { background: linear-gradient(140deg, #8a560d, #c98a2b); }
    .jj-head b { position: relative; display: block; font-size: 1.5rem; line-height: 1.2; }
    .jj-head small { position: relative; display: block; margin-top: 6px; font-size: .85rem; line-height: 1.4; color: rgba(255,255,255,.85); }
    .jj-body { padding: 0 16px 24px; }
    .jj .avatar { width: 88px; height: 88px; margin: -44px auto 12px; border-color: #fff; box-shadow: 0 8px 18px rgba(20,83,45,.22); }
    .jj h3 { margin: 0 0 8px; font-size: 1.02rem; line-height: 1.35; color: #0f172a; overflow-wrap: anywhere; }
    .jj span { display: inline-block; font-size: 13px; font-weight: 600; color: var(--g700); background: var(--g50); padding: 5px 14px; border-radius: 999px; }
    .jj:nth-child(even) span { color: var(--gold-text); background: var(--gold-soft); }

    /* ====== Bidang ====== */
    .bidang { counter-reset: bd; display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .bidang-item { counter-increment: bd; position: relative; overflow: hidden; padding: 26px 24px; background: #fff; border: 1px solid rgba(6,78,59,.12); border-radius: 22px;
        display: grid; grid-template-columns: 52px 1fr; gap: 16px; align-items: start; box-shadow: 0 10px 26px rgba(6,78,59,.06); transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease; }
    .bidang-item::after { content: counter(bd, decimal-leading-zero); position: absolute; right: 16px; top: 4px; font-size: 3.4rem; font-weight: 800; line-height: 1; color: rgba(183,121,31,.12); }
    .bidang-item:hover { transform: translateY(-5px); border-color: var(--g500); box-shadow: 0 18px 36px -18px rgba(20,83,45,.45); }
    .bidang-ico { width: 52px; height: 52px; border-radius: 16px; display: flex; align-items: center; justify-content: center;
        background: #dcfce7; color: var(--g700); font-size: 20px; transition: background .2s ease, color .2s ease; }
    .bidang-item:nth-child(even) .bidang-ico { background: var(--gold-soft); color: var(--gold-text); }
    .bidang-item:hover .bidang-ico { background: var(--g600); color: #fff; }
    .bidang-item:nth-child(even):hover .bidang-ico { background: var(--gold); color: #fff; }
    .bidang-item h3 { margin: 2px 0 6px; font-size: 1.12rem; line-height: 1.35; color: var(--g800); }
    .bidang-item p { margin: 0; color: #475569; line-height: 1.7; }

    /* ====== CTA ====== */
    .sj-cta { background: linear-gradient(135deg, #0f5132, #15803d); color: #fff; border-radius: 28px; padding: 52px 40px; text-align: center; }
    .sj-cta h2 { margin: 0 0 12px; color: #fff; font-size: clamp(1.5rem, 3vw, 2.1rem); }
    .sj-cta p { margin: 0 auto 24px; max-width: 54ch; color: rgba(255,255,255,.9); line-height: 1.7; }
    .sj-btn { display: inline-flex; align-items: center; gap: 10px; background: #fff; color: var(--g800); font-weight: 700; padding: 14px 30px; border-radius: 14px; text-decoration: none; }
    .sj-btn:hover { background: #fde68a; }
    .sj-btn:focus-visible { outline: 3px solid #fde68a; outline-offset: 3px; }

    /* ====== Animasi muncul ====== */
    .sj-rv { opacity: 0; transform: translateY(22px); transition: opacity .7s ease, transform .7s cubic-bezier(.2,.7,.2,1); }
    .sj-rv.in { opacity: 1; transform: none; }

    /* ====== Responsif ====== */
    @media (max-width: 1080px) { .jenjang { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 960px) { .bidang { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 820px) {
        .tree { overflow: visible; }
        .tree > ul { min-width: 0; }
        .tree ul { flex-direction: column; align-items: center; padding-top: 26px; }
        .tree li { padding: 26px 0 0; }
        .tree ul > li:first-child { padding-top: 0; }
        .tree li::before, .tree li::after { display: none; }
        .tree ul ul::before { height: 26px; }
        .person, .person.top { width: 100%; max-width: 320px; }
    }
    @media (max-width: 760px) { .jenjang { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 640px) {
        .sj-sec { padding: 60px 0; }
        .sj-hero { padding: 40px 0 56px; }
        .sj-hero::after { width: 240px; height: 240px; right: -70px; bottom: -70px; }
        .bidang { grid-template-columns: 1fr; }
        .sj-cta { padding: 44px 24px; }
    }
    @media (max-width: 480px) { .jenjang { grid-template-columns: 1fr; } }
    @media (prefers-reduced-motion: reduce) {
        .person, .jj, .bidang-item, .bidang-ico { transition: none; }
        .person:hover, .jj:hover, .bidang-item:hover { transform: none; }
        .sj-rv { opacity: 1 !important; transform: none !important; transition: none !important; }
    }
</style>

<div class="sj-page">

    {{-- ============ HERO ============ --}}
    <header class="sj-hero">
        <div class="sj-wrap">
            <div class="sj-crumb">
                <a href="{{ url('/') }}">Beranda</a> <i class="fa-solid fa-chevron-right" style="font-size:10px"></i>
                Profil <i class="fa-solid fa-chevron-right" style="font-size:10px"></i> Struktur Organisasi
            </div>
            <div class="sj-eyebrow light">Pondok Pesantren Babul Hasanah</div>
            <h1>Berkhidmat dengan <span>penuh amanah.</span></h1>
            <p>Para pengasuh dan pengurus yang berkhidmat menjaga, membimbing, dan mengelola pesantren dengan penuh amanah.</p>
            <nav class="sj-nav" aria-label="Isi halaman">
                <a href="#bagan"><i class="fa-solid fa-sitemap"></i> Pengurus</a>
                @if ($jenjang->isNotEmpty())
                    <a href="#jenjang"><i class="fa-solid fa-graduation-cap"></i> Jenjang</a>
                @endif
                @if ($bidang->isNotEmpty())
                    <a href="#bidang"><i class="fa-solid fa-people-group"></i> Bidang</a>
                @endif
                <a href="#kontak"><i class="fa-solid fa-envelope"></i> Kontak</a>
            </nav>
        </div>
    </header>

    {{-- ============ BAGAN KEPENGURUSAN ============ --}}
    <section class="sj-sec alt" id="bagan">
        <div class="sj-wrap">
            <div class="sj-head center">
                <div class="sj-eyebrow">Kepengurusan</div>
                <h2>Susunan pengurus pesantren</h2>
            </div>

            @if ($bagan->isNotEmpty())
                <div class="tree">
                    <ul>
                        @foreach ($bagan as $akar)
                            @include('pages.partials.struktur-node', ['node' => $akar, 'level' => 0])
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="tree-empty"><i class="fa-solid fa-sitemap"></i>Susunan pengurus belum diisi.</div>
            @endif
        </div>
    </section>

    {{-- ============ KETUA TIAP JENJANG ============ --}}
    @if ($jenjang->isNotEmpty())
        <section class="sj-sec" id="jenjang">
            <div class="sj-wrap">
                <div class="sj-head">
                    <div class="sj-eyebrow">Jenjang Pendidikan</div>
                    <h2>Ketua jenjang pendidikan</h2>
                    <p>{{ $jenjang->count() }} jenjang dan program pendidikan di bawah naungan Yayasan Pondok Pesantren Babul Hasanah (PPBH), masing-masing dipimpin seorang ketua.</p>
                </div>

                <div class="jenjang">
                    @foreach ($jenjang as $j)
                        <article class="jj sj-rv">
                            <div class="jj-head">
                                <b>{{ $j->kode }}</b>
                                <small>{{ $j->keterangan }}</small>
                            </div>
                            <div class="jj-body">
                                <div class="avatar">
                                    <i class="fa-solid {{ $j->ikon ?: 'fa-user-tie' }}"></i>
                                    @if ($j->foto)
                                        <img src="{{ $j->foto_url }}" alt="{{ $j->nama }}" loading="lazy" onerror="this.remove()">
                                    @endif
                                </div>
                                <h3>{{ $j->nama }}</h3>
                                <span>Ketua {{ $j->kode }}</span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ BIDANG & SEKSI ============ --}}
    @if ($bidang->isNotEmpty())
        <section class="sj-sec alt" id="bidang">
            <div class="sj-wrap">
                <div class="sj-head">
                    <div class="sj-eyebrow">Bidang &amp; Seksi</div>
                    <h2>Bidang pelayanan pesantren</h2>
                    <p>Setiap bidang memiliki peran penting dalam menunjang kegiatan belajar dan kehidupan santri.</p>
                </div>

                <div class="bidang">
                    @foreach ($bidang as $b)
                        <div class="bidang-item sj-rv">
                            <div class="bidang-ico"><i class="fa-solid {{ $b->ikon ?: 'fa-circle-check' }}"></i></div>
                            <div>
                                <h3>{{ $b->nama }}</h3>
                                @if ($b->keterangan)
                                    <p>{{ $b->keterangan }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ CTA ============ --}}
    <section class="sj-sec" id="kontak">
        <div class="sj-wrap">
            <div class="sj-cta">
                <h2>Ada yang ingin ditanyakan kepada pengurus?</h2>
                <p>Hubungi kami untuk informasi pendaftaran, kunjungan santri, atau kerja sama.</p>
                <a href="{{ url('/kontak') }}" class="sj-btn">Hubungi kami <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </section>
</div>

<script>
    (function () {
        var els = document.querySelectorAll('.sj-rv');
        if (!('IntersectionObserver' in window)) {
            els.forEach(function (el) { el.classList.add('in'); });
            return;
        }
        var obs = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (en.isIntersecting) { en.target.classList.add('in'); obs.unobserve(en.target); }
            });
        }, { threshold: 0.12 });
        els.forEach(function (el, i) {
            el.style.transitionDelay = ((i % 4) * 70) + 'ms';
            obs.observe(el);
        });
    })();
</script>
@endsection