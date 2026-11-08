@extends('layouts.app')

@section('title', 'Visi & Misi - Pondok Pesantren Babul Hasanah')

@section('content')

@php
    // Ambil seluruh konten sekali jalan (1 query), dengan fallback teks lama.
    $konten = \App\Models\Konten::allWithDefaults();

    $kv = fn (string $key, string $default = '') => $konten[$key] ?? $default;

    $baris = function (string $key, string $default = '') use ($kv): array {
        $raw = (string) $kv($key, $default);
        $hasil = [];
        foreach (preg_split('/\r\n|\r|\n/', $raw) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $bagian = array_map('trim', explode('|', $line, 2));
            $hasil[] = [$bagian[0], $bagian[1] ?? ''];
        }

        return $hasil;
    };

    // Susunan & visibilitas section dari dashboard.
    $layout = \App\Models\Konten::layoutFor('visi');
    $visible = [];
    $order = [];
    foreach ($layout as $i => $section) {
        $visible[$section['id']] = $section['visible'];
        $order[$section['id']] = $i;
    }

    $heroEyebrow = $kv('visi_page_hero_eyebrow', 'Pondok Pesantren Babul Hasanah');
    $heroTitle = $kv('visi_page_hero_title', 'Cita-cita yang jelas,');
    $heroAccent = $kv('visi_page_hero_title_accent', 'langkah yang nyata.');
    $heroDesc = $kv('visi_page_hero_desc', 'Landasan kami dalam membangun generasi unggul, berilmu, dan berakhlak mulia: dari visi, misi, hingga nilai yang kami tanamkan setiap hari.');

    $visi = $kv('visi_page_visi', 'Membangun Generasi Unggul dalam Penguasaan Kitab Kuning, Berakhlak Mulia, dan Berwawasan Luas.');

    $misiHeading = $kv('visi_page_misi_heading', 'Misi pondok pesantren');
    $misiDesc = $kv('visi_page_misi_desc', 'Untuk mewujudkan visi tersebut, kami menjalankan lima misi berikut dengan penuh komitmen.');

    $misiIkon = ['fa-book-quran', 'fa-hand-holding-heart', 'fa-lightbulb', 'fa-house-mosque', 'fa-hands-holding-circle'];
    $misi = [];
    foreach ($baris('visi_page_misi', "Pendalaman Ilmu Agama|Menyelenggarakan pendidikan kitab kuning secara berjenjang dan sistematis dengan sanad keilmuan yang jelas.\nPembinaan Akhlak & Karakter|Menanamkan adab, kedisiplinan, kemandirian, dan keteladanan dalam kehidupan santri sehari-hari.\nPenguatan Wawasan & Keterampilan|Membekali santri dengan wawasan keilmuan, kemampuan berbahasa, dan keterampilan hidup yang relevan dengan zaman.\nLingkungan Pesantren yang Kondusif|Menciptakan suasana belajar yang aman, nyaman, dan islami untuk mendukung tumbuh kembang santri.\nPengabdian kepada Masyarakat|Mengembangkan dakwah dan kemaslahatan umat melalui kegiatan sosial, keagamaan, dan pendidikan.") as $i => $row) {
        $misi[] = [$misiIkon[$i] ?? 'fa-circle', $row[0], $row[1]];
    }

    $nilaiHeading = $kv('visi_page_nilai_heading', 'Nilai-nilai utama');
    $nilaiDesc = $kv('visi_page_nilai_desc', 'Empat pegangan yang kami tanamkan dalam keseharian santri.');

    $nilaiIkon = ['fa-book-quran', 'fa-heart', 'fa-hands-holding-circle', 'fa-people-group'];
    $nilai = [];
    foreach ($baris('visi_page_nilai', "Religius|Berpegang teguh pada Al-Qur’an, Sunnah, dan tradisi ulama salaf.\nIkhlas|Belajar dan mengabdi semata-mata mengharap ridha Allah.\nMandiri|Melatih santri hidup sederhana, disiplin, dan bertanggung jawab.\nUkhuwah|Menjalin persaudaraan yang erat antarsantri dan masyarakat.") as $i => $row) {
        $nilai[] = [$nilaiIkon[$i] ?? 'fa-circle', $row[0], $row[1]];
    }

    $mottoLabel = $kv('visi_page_motto_label', 'Motto Pesantren');
    $motto = $kv('visi_page_motto', '“Berilmu, Beramal,');
    $mottoAccent = $kv('visi_page_motto_accent', 'Berakhlak Mulia”');
@endphp

<style>
    .sj-page { --g900:#0a3a24; --g800:#0f5132; --g700:#13693f; --g600:#15803d; --g500:#10b981; --g50:#f0fdf6;
        --gold:#b7791f; --gold-text:#955f0f; --gold-soft:#fdf3dc; --gold-light:#fde68a; color:#1e293b;
        display: flex; flex-direction: column; }
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
    .sj-head h2 { font-size: clamp(1.7rem, 3.2vw, 2.4rem); line-height: 1.25; margin: 0 0 12px; color: var(--g800); }
    .sj-head p { margin: 0; color: #475569; line-height: 1.75; font-size: 1.05rem; }

    /* ====== 1. Visi ====== */
    .sj-story { display: grid; grid-template-columns: 300px 1fr; gap: 64px; align-items: center; }
    .sj-year { position: sticky; top: 100px; }
    .sj-year b { display: block; font-size: clamp(4.5rem, 10vw, 7rem); line-height: .9; font-weight: 800; letter-spacing: -.04em;
        color: transparent; -webkit-text-stroke: 2px var(--gold); }
    .sj-year span { display: block; margin-top: 14px; padding-top: 14px; border-top: 3px solid var(--gold); font-weight: 700; color: var(--g800); line-height: 1.5; max-width: 220px; }
    .sj-visi { position: relative; background: linear-gradient(135deg, var(--g800), var(--g600)); color: #fff; border-radius: 28px; padding: 52px 48px; overflow: hidden;
        box-shadow: 0 26px 56px -24px rgba(20,83,45,.55); }
    .sj-visi::after { content: ""; position: absolute; right: -80px; bottom: -110px; width: 300px; height: 300px; border-radius: 50%; border: 50px solid rgba(255,255,255,.06); }
    .sj-visi > * { position: relative; z-index: 1; }
    .sj-visi i { font-size: 2.2rem; color: var(--gold-light); opacity: .6; display: block; margin-bottom: 16px; }
    .sj-visi h2 { margin: 0; color: #fff; font-size: clamp(1.4rem, 2.8vw, 2.1rem); line-height: 1.55; font-weight: 700; }
    .sj-visi h2 mark { background: none; color: var(--gold-light); }

    /* ====== 2. Misi ====== */
    .sj-culture { display: grid; grid-template-columns: repeat(6, 1fr); gap: 20px; }
    .sj-cc { grid-column: span 2; position: relative; background: #fff; border-radius: 22px; padding: 28px 26px 26px; border: 1px solid rgba(6,78,59,.1);
        box-shadow: 0 10px 28px rgba(6,78,59,.07); overflow: hidden; transition: transform .3s ease, box-shadow .3s ease; }
    .sj-cc:nth-child(n+4) { grid-column: span 3; }
    .sj-cc:hover { transform: translateY(-5px); box-shadow: 0 18px 40px rgba(6,78,59,.14); }
    .sj-cc .no { position: absolute; right: 18px; top: 6px; font-size: 4rem; font-weight: 800; color: rgba(183,121,31,.12); line-height: 1; }
    .sj-cc .ico { width: 54px; height: 54px; border-radius: 16px; display: flex; align-items: center; justify-content: center; background: var(--gold-soft); color: var(--gold-text); font-size: 20px; margin-bottom: 18px; }
    .sj-cc h3 { margin: 0 0 8px; font-size: 1.2rem; line-height: 1.35; color: var(--g800); }
    .sj-cc p { margin: 0; color: #475569; line-height: 1.75; }

    /* ====== 3. Nilai ====== */
    .sj-values { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; }
    .sj-val { background: var(--g50); border: 1px solid rgba(6,78,59,.12); border-radius: 22px; padding: 28px 24px; transition: transform .3s ease, box-shadow .3s ease; }
    .sj-val:hover { transform: translateY(-5px); box-shadow: 0 16px 36px rgba(6,78,59,.12); }
    .sj-val .ico { width: 54px; height: 54px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 21px; background: var(--gold-soft); color: var(--gold-text); margin-bottom: 18px; }
    .sj-val:nth-child(even) .ico { background: #dcfce7; color: var(--g700); }
    .sj-val h3 { margin: 0 0 8px; font-size: 1.2rem; color: var(--g800); }
    .sj-val p { margin: 0; color: #475569; line-height: 1.7; }

    /* ====== 4. Motto ====== */
    .sj-motivasi { position: relative; overflow: hidden; color: #fff; padding: 84px 0; text-align: center;
        background: radial-gradient(600px 300px at 15% 0%, rgba(253,230,138,.16), transparent 65%), linear-gradient(135deg, var(--g900), var(--g700)); }
    .sj-motivasi::before { content: ""; position: absolute; inset: 0; opacity: .4;
        background-image: radial-gradient(rgba(255,255,255,.12) 1.5px, transparent 1.5px); background-size: 26px 26px;
        -webkit-mask-image: linear-gradient(to right, #000, transparent 70%); mask-image: linear-gradient(to right, #000, transparent 70%); }
    .sj-motivasi .sj-wrap { position: relative; z-index: 1; }
    .sj-motivasi .sj-eyebrow { justify-content: center; }
    .sj-motivasi h2 { margin: 0 auto; max-width: 20ch; color: #fff; font-size: clamp(1.9rem, 4.4vw, 3.2rem); line-height: 1.3; font-weight: 800; }
    .sj-motivasi h2 span { color: var(--gold-light); }

    /* ====== Animasi muncul ====== */
    .sj-rv { opacity: 0; transform: translateY(22px); transition: opacity .7s ease, transform .7s cubic-bezier(.2,.7,.2,1); }
    .sj-rv.in { opacity: 1; transform: none; }
    @media (prefers-reduced-motion: reduce) { .sj-rv { opacity: 1 !important; transform: none !important; transition: none !important; } }

    /* ====== Responsif ====== */
    @media (max-width: 960px) {
        .sj-story { grid-template-columns: 1fr; gap: 28px; }
        .sj-year { position: static; display: flex; align-items: flex-end; gap: 18px; }
        .sj-year span { margin: 0 0 8px; padding-top: 0; border-top: 0; border-left: 3px solid var(--gold); padding-left: 14px; }
        .sj-culture { grid-template-columns: repeat(2, 1fr); }
        .sj-cc, .sj-cc:nth-child(n+4) { grid-column: span 1; }
        .sj-cc:last-child { grid-column: span 2; }
        .sj-values { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
        .sj-sec, .sj-motivasi { padding: 60px 0; }
        .sj-hero { padding: 40px 0 56px; }
        .sj-visi { padding: 38px 26px; border-radius: 22px; }
        .sj-culture { grid-template-columns: 1fr; }
        .sj-cc:last-child { grid-column: span 1; }
        .sj-values { grid-template-columns: 1fr; }
    }
</style>

<div class="sj-page">

    {{-- ============ HERO ============ --}}
    @if ($visible['hero'] ?? true)
        <header class="sj-hero" style="order: {{ $order['hero'] ?? 0 }}">
            <div class="sj-wrap">
                <div class="sj-crumb">
                    <a href="{{ url('/') }}">Beranda</a> <i class="fa-solid fa-chevron-right"
                        style="font-size:10px"></i>
                    Profil <i class="fa-solid fa-chevron-right" style="font-size:10px"></i> Visi &amp; Misi
                </div>
                <div class="sj-eyebrow light">{{ $heroEyebrow }}</div>
                <h1>{{ $heroTitle }} <span>{{ $heroAccent }}</span></h1>
                <p>{{ $heroDesc }}</p>
                <nav class="sj-nav" aria-label="Isi halaman">
                    @if ($visible['visi'] ?? true)
                        <a href="#visi"><i class="fa-solid fa-eye"></i> Visi</a>
                    @endif
                    @if ($visible['misi'] ?? true)
                        <a href="#misi"><i class="fa-solid fa-bullseye"></i> Misi</a>
                    @endif
                    @if ($visible['nilai'] ?? true)
                        <a href="#nilai"><i class="fa-solid fa-gem"></i> Nilai</a>
                    @endif
                    @if ($visible['motto'] ?? true)
                        <a href="#motto"><i class="fa-solid fa-star-and-crescent"></i> Motto</a>
                    @endif
                </nav>
            </div>
        </header>
    @endif

    {{-- ============ 1. VISI ============ --}}
    @if ($visible['visi'] ?? true)
        <section class="sj-sec" id="visi" style="order: {{ $order['visi'] ?? 1 }}">
        <div class="sj-wrap">
            <div class="sj-story">
                <div class="sj-year sj-rv">
                    <b>Visi</b>
                    <span>Arah yang kami tuju bersama</span>
                </div>
                <div class="sj-visi sj-rv">
                    <i class="fa-solid fa-quote-left"></i>
                    <h2>{{ $visi }}</h2>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ============ 2. MISI ============ --}}
    @if ($visible['misi'] ?? true)
        <section class="sj-sec alt" id="misi" style="order: {{ $order['misi'] ?? 2 }}">
        <div class="sj-wrap">
            <div class="sj-head">
                <div class="sj-eyebrow">Misi Pesantren</div>
                <h2>{{ $misiHeading }}</h2>
                <p>{{ $misiDesc }}</p>
            </div>

            <div class="sj-culture">
                @foreach ($misi as $m)
                    <article class="sj-cc sj-rv">
                        <span class="no">{{ sprintf('%02d', $loop->iteration) }}</span>
                        <span class="ico"><i class="fa-solid {{ $m[0] }}"></i></span>
                        <h3>{{ $m[1] }}</h3>
                        <p>{{ $m[2] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ============ 3. NILAI-NILAI ============ --}}
    @if ($visible['nilai'] ?? true)
        <section class="sj-sec" id="nilai" style="order: {{ $order['nilai'] ?? 3 }}">
        <div class="sj-wrap">
            <div class="sj-head">
                <div class="sj-eyebrow">Nilai Utama</div>
                <h2>{{ $nilaiHeading }}</h2>
                <p>{{ $nilaiDesc }}</p>
            </div>
            <div class="sj-values">
                @foreach ($nilai as $n)
                    <div class="sj-val sj-rv">
                        <div class="ico"><i class="fa-solid {{ $n[0] }}"></i></div>
                        <h3>{{ $n[1] }}</h3>
                        <p>{{ $n[2] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ============ 4. MOTTO ============ --}}
    @if ($visible['motto'] ?? true)
        <section class="sj-motivasi" id="motto" style="order: {{ $order['motto'] ?? 4 }}">
        <div class="sj-wrap">
            <div class="sj-eyebrow light">{{ $mottoLabel }}</div>
            <h2 class="sj-rv">{{ $motto }} <span>{{ $mottoAccent }}</span></h2>
        </div>
    </section>
    @endif
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