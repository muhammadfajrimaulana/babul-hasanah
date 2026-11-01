@extends('layouts.app')

@section('title', 'Pembelajaran - Pondok Pesantren Babul Hasanah')

@section('content')

@php
    /* ==== TIGA JALUR BELAJAR ==== */
    $jalur = [
        ['fa-book-open',          'Kajian kitab kuning', 'Santri mempelajari kitab-kitab klasik secara bertahap sesuai kemampuan.'],
        ['fa-school',             'Pendidikan formal',   'Jenjang madrasah dan diniyah formal berjalan beriringan dengan pengajian pesantren.'],
        ['fa-hand-holding-heart', 'Pembinaan karakter',  'Adab, bahasa, dan keterampilan dilatih lewat kegiatan harian dan ekstrakurikuler.'],
    ];

    /* ==== BIDANG KAJIAN & KITAB (dari data lama; silakan tambah/ubah) ==== */
    $kajian = [
        ['ikon' => 'fa-spell-check',      'judul' => 'Nahwu & Shorof',   'isi' => 'Fondasi membaca kitab gundul: tata bahasa dan perubahan kata dalam bahasa Arab.',   'kitab' => ['Jurumiyah', 'Imrithi', 'Alfiyah', 'Amtsilati Tasrifiyah']],
        ['ikon' => 'fa-scale-balanced',   'judul' => 'Fiqih',            'isi' => 'Hukum ibadah dan muamalah menurut mazhab Syafi’i secara mendalam dan praktis.',     'kitab' => ['Safinatun Najah', 'Fathul Qorib', 'Fathul Mu’in']],
        ['ikon' => 'fa-star-and-crescent','judul' => 'Tauhid / Aqidah',  'isi' => 'Memperkokoh keyakinan sesuai ajaran Ahlussunnah wal Jama’ah.',                     'kitab' => ['Aqidatul Awam', 'Kifayatul Awam', 'Tijan Ad-Darari']],
        ['ikon' => 'fa-heart-pulse',      'judul' => 'Akhlak & Tasawuf', 'isi' => 'Menata adab, hati, dan perilaku sebagai penuntut ilmu.',                             'kitab' => ['Ta’lim Muta’allim', 'Akhlaq lil Banin', 'Bidayatul Hidayah']],
        ['ikon' => 'fa-scroll',           'judul' => 'Hadits & Tafsir',  'isi' => 'Memahami sabda Nabi dan kandungan Al-Qur’an melalui kajian para ulama.',            'kitab' => ['Arba’in Nawawi', 'Riyadhus Shalihin', 'Tafsir Jalalain']],
        ['ikon' => 'fa-book-quran',       'judul' => 'Al-Qur’an',        'isi' => 'Tahsin, tajwid, dan program tahfidz bagi santri yang berminat.',                    'kitab' => ['Tahsin', 'Tajwid', 'Tahfidz']],
    ];

    /* ==== JENJANG & PROGRAM (PPBH) — SESUAIKAN DESKRIPSI DENGAN KURIKULUM ASLI ==== */
    $jalurDiniyah = [
        ['MDTA',        'Madrasah Diniyah Takmiliyah',      'Pengenalan dasar membaca Al-Qur’an, ibadah, akhlak, dan pengantar bahasa Arab.'],
        ['PPS Wustha',  'Program pesantren jenjang Wustha', 'Pendalaman nahwu-shorof, fiqih, dan tauhid dengan kitab tingkat menengah.'],
        ['PDF Wustha',  'Pendidikan Diniyah Formal Wustha', 'Pendidikan diniyah formal jenjang Wustha dengan kurikulum kitab terstruktur.'],
        ['PDF Ulya',    'Pendidikan Diniyah Formal Ulya',   'Kajian lanjutan fiqih, tafsir, hadits, serta pelatihan bahtsul masail.'],
        ['Ma’had ‘Aly', 'Pendidikan tinggi pesantren',      'Pendalaman keilmuan tingkat lanjut bagi santri yang ingin menjadi ahli di bidang kitab.'],
    ];
    $jalurMadrasah = [
        ['MTs', 'Madrasah Tsanawiyah', 'Pendidikan menengah pertama yang memadukan kurikulum madrasah dengan kegiatan pesantren.'],
        ['MA',  'Madrasah Aliyah',     'Pendidikan menengah atas yang menyiapkan santri melanjutkan studi dan berkhidmat di masyarakat.'],
    ];

    /* ==== METODE ==== */
    $metode = [
        ['fa-user-group',      'Sorogan',        'Santri membaca kitab satu per satu di hadapan ustadz untuk dikoreksi langsung.'],
        ['fa-chalkboard-user', 'Bandongan',      'Ustadz membacakan dan menjelaskan kitab, santri menyimak dan memberi makna.'],
        ['fa-brain',           'Muhafazhah',     'Menghafal nazham dan matan kitab untuk memperkuat penguasaan ilmu.'],
        ['fa-comments',        'Bahtsul masail', 'Diskusi kajian hukum untuk melatih nalar dan kemampuan merujuk kitab.'],
    ];
@endphp

<style>
    .sj-page { --g900:#0a3a24; --g800:#0f5132; --g700:#13693f; --g600:#15803d; --g500:#10b981; --g50:#f0fdf6;
        --gold:#b7791f; --gold-text:#955f0f; --gold-soft:#fdf3dc; --gold-light:#fde68a; color:#1e293b; }
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
    .sj-hero h1 { margin: 0 0 18px; font-size: clamp(2.2rem, 5vw, 3.6rem); line-height: 1.12; color: #fff; max-width: 20ch; }
    .sj-hero h1 span { color: var(--gold-light); }
    .sj-hero p { margin: 0 0 30px; max-width: 60ch; font-size: 1.1rem; line-height: 1.8; color: rgba(255,255,255,.88); }
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

    /* ====== 1. Tiga jalur ====== */
    .sj-lead { display: grid; grid-template-columns: 1fr 1.2fr; gap: 56px; align-items: start; margin-bottom: 48px; }
    .sj-lead h2 { margin: 0; font-size: clamp(1.7rem, 3.2vw, 2.4rem); line-height: 1.3; color: var(--g800); }
    .sj-lead p { margin: 0; font-size: 1.1rem; line-height: 1.9; color: #334155; }
    .sj-lead p::first-letter { font-weight: 800; color: var(--gold-text); }
    .sj-lanes3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .sj-l3 { position: relative; overflow: hidden; background: #fff; border-radius: 24px; padding: 32px 28px 28px; border: 1px solid rgba(6,78,59,.12);
        border-top: 4px solid var(--g500); box-shadow: 0 14px 34px -18px rgba(6,78,59,.3); transition: transform .3s ease, box-shadow .3s ease; }
    .sj-l3:nth-child(2) { border-top-color: var(--gold); }
    .sj-l3:hover { transform: translateY(-5px); box-shadow: 0 24px 44px -18px rgba(6,78,59,.4); }
    .sj-l3 .no { position: absolute; right: 18px; top: 8px; font-size: 4rem; font-weight: 800; line-height: 1; color: rgba(183,121,31,.12); }
    .sj-l3 .ico { width: 56px; height: 56px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 22px; background: #dcfce7; color: var(--g700); margin-bottom: 18px; }
    .sj-l3:nth-child(2) .ico { background: var(--gold-soft); color: var(--gold-text); }
    .sj-l3 h3 { margin: 0 0 8px; font-size: 1.2rem; color: var(--g800); }
    .sj-l3 p { margin: 0; color: #475569; line-height: 1.75; }

    /* ====== 2. Kajian (tab) ====== */
    .kj { display: grid; grid-template-columns: 300px 1fr; gap: 36px; align-items: start; }
    .kj-tabs { display: flex; flex-direction: column; gap: 6px; }
    .kj-tab { display: flex; align-items: center; gap: 14px; text-align: left; border: 0; background: transparent; font: inherit; font-weight: 600; color: var(--g800);
        padding: 15px 18px; border-radius: 16px; cursor: pointer; border-left: 4px solid transparent; transition: background .2s ease; }
    .kj-tab i { width: 22px; text-align: center; color: var(--gold-text); }
    .kj-tab:hover { background: rgba(16,185,129,.12); }
    .kj-tab[aria-selected="true"] { background: #fff; border-left-color: var(--gold); box-shadow: 0 10px 24px -12px rgba(6,78,59,.4); }
    .kj-tab:focus-visible { outline: 3px solid var(--gold); outline-offset: 2px; }
    .kj-panel { position: relative; overflow: hidden; background: #fff; border-radius: 26px; padding: 42px 44px; box-shadow: 0 22px 50px -26px rgba(6,78,59,.4); min-height: 300px; }
    .kj-panel::after { content: ""; position: absolute; right: -60px; top: -60px; width: 200px; height: 200px; border-radius: 50%; border: 36px solid rgba(183,121,31,.07); }
    .kj-panel > * { position: relative; z-index: 1; }
    .kj-panel[hidden] { display: none; }
    .kj-panel .big { width: 64px; height: 64px; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 26px; background: var(--gold-soft); color: var(--gold-text); margin-bottom: 18px; }
    .kj-panel h3 { margin: 0 0 10px; font-size: 1.7rem; color: var(--g800); }
    .kj-panel p { margin: 0 0 26px; color: #475569; line-height: 1.8; font-size: 1.05rem; max-width: 56ch; }
    .kj-panel h4 { margin: 0 0 12px; font-size: .85rem; letter-spacing: .12em; text-transform: uppercase; color: var(--gold-text); }
    .kitab { display: flex; flex-wrap: wrap; gap: 10px; }
    .kitab span { font-weight: 600; font-size: .98rem; color: var(--g800); background: #dcfce7; padding: 9px 18px; border-radius: 999px; }

    /* ====== 3. Jenjang ====== */
    .lane { margin-bottom: 56px; }
    .lane:last-child { margin-bottom: 0; }
    .lane-title { display: flex; align-items: center; gap: 14px; margin: 0 0 6px; font-size: 1.4rem; color: var(--g800); }
    .lane-title i { width: 44px; height: 44px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 18px; background: #dcfce7; color: var(--g700); }
    .lane.madrasah .lane-title i { background: var(--gold-soft); color: var(--gold-text); }
    .lane-note { margin: 0 0 22px 58px; color: #475569; }
    .jj-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
    .jj-card { background: #fff; border-radius: 22px; border: 1px solid rgba(6,78,59,.12); padding: 26px 24px; box-shadow: 0 10px 26px -16px rgba(6,78,59,.3); transition: transform .3s ease, box-shadow .3s ease; }
    .jj-card:hover { transform: translateY(-5px); box-shadow: 0 22px 40px -18px rgba(6,78,59,.4); }
    .jj-code { display: inline-block; font-weight: 800; font-size: 1.05rem; color: #fff; background: linear-gradient(135deg, var(--g800), var(--g600)); padding: 8px 16px; border-radius: 12px; margin-bottom: 16px; }
    .lane.madrasah .jj-code { background: linear-gradient(135deg, #8a560d, #c98a2b); }
    .jj-card h4 { margin: 0 0 8px; font-size: 1.1rem; line-height: 1.4; color: var(--g800); }
    .jj-card p { margin: 0; color: #475569; line-height: 1.75; }
    .lane.madrasah .jj-grid { grid-template-columns: repeat(2, 1fr); }

    /* ====== 4. Metode ====== */
    .metode { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
    .metode-item { position: relative; overflow: hidden; background: #fff; border-radius: 24px; padding: 32px 26px 28px; border: 1px solid rgba(6,78,59,.12); box-shadow: 0 12px 30px -18px rgba(6,78,59,.3); transition: transform .3s ease, box-shadow .3s ease; }
    .metode-item:hover { transform: translateY(-5px); box-shadow: 0 24px 44px -18px rgba(6,78,59,.4); }
    .metode-item .no { display: block; font-size: 3.6rem; font-weight: 800; line-height: 1; margin-bottom: 14px; color: transparent; -webkit-text-stroke: 1.5px var(--gold); }
    .metode-item i { position: absolute; right: 22px; top: 24px; font-size: 24px; color: var(--g600); opacity: .85; }
    .metode-item h3 { margin: 0 0 8px; font-size: 1.25rem; color: var(--g800); }
    .metode-item p { margin: 0; color: #475569; line-height: 1.75; }

    /* ====== CTA ====== */
    .sj-cta { background: linear-gradient(135deg, #0f5132, #15803d); color: #fff; border-radius: 28px; padding: 56px 40px; text-align: center; }
    .sj-cta h2 { margin: 0 0 12px; color: #fff; font-size: clamp(1.6rem, 3vw, 2.2rem); }
    .sj-cta p { margin: 0 auto 26px; max-width: 54ch; color: rgba(255,255,255,.9); line-height: 1.7; }
    .sj-btn { display: inline-flex; align-items: center; gap: 10px; background: #fff; color: var(--g800); font-weight: 700; padding: 14px 30px; border-radius: 14px; text-decoration: none; }
    .sj-btn:hover { background: #fde68a; }
    .sj-btn:focus-visible { outline: 3px solid #fde68a; outline-offset: 3px; }

    /* ====== Animasi muncul ====== */
    .sj-rv { opacity: 0; transform: translateY(22px); transition: opacity .7s ease, transform .7s cubic-bezier(.2,.7,.2,1); }
    .sj-rv.in { opacity: 1; transform: none; }
    @media (prefers-reduced-motion: reduce) {
        .sj-rv { opacity: 1 !important; transform: none !important; transition: none !important; }
        .sj-l3, .jj-card, .metode-item { transition: none; }
        .sj-l3:hover, .jj-card:hover, .metode-item:hover { transform: none; }
    }

    /* ====== Responsif ====== */
    @media (max-width: 1080px) { .metode { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 960px) {
        .sj-lead { grid-template-columns: 1fr; gap: 20px; }
        .sj-lanes3 { grid-template-columns: 1fr; }
        .kj { grid-template-columns: 1fr; gap: 22px; }
        .kj-tabs { flex-direction: row; flex-wrap: wrap; }
        .kj-tab { border-left: 0; border-bottom: 3px solid transparent; padding: 10px 14px; border-radius: 12px; }
        .kj-tab[aria-selected="true"] { border-bottom-color: var(--gold); }
        .jj-grid, .lane.madrasah .jj-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
        .sj-sec { padding: 60px 0; }
        .sj-hero { padding: 40px 0 56px; }
        .kj-panel { padding: 30px 24px; }
        .lane-note { margin-left: 0; }
    }
    @media (max-width: 560px) {
        .metode, .jj-grid, .lane.madrasah .jj-grid { grid-template-columns: 1fr; }
        .sj-cta { padding: 44px 24px; }
    }
</style>

<div class="sj-page">

    {{-- ============ HERO ============ --}}
    <header class="sj-hero">
        <div class="sj-wrap">
            <div class="sj-crumb">
                <a href="{{ url('/') }}">Beranda</a> <i class="fa-solid fa-chevron-right" style="font-size:10px"></i>
                Profil <i class="fa-solid fa-chevron-right" style="font-size:10px"></i> Pembelajaran
            </div>
            <div class="sj-eyebrow light">Pondok Pesantren Babul Hasanah</div>
            <h1>Belajar bertahap, <span>dari kitab hingga akhlak.</span></h1>
            <p>Kurikulum terpadu yang memadukan tradisi keilmuan salaf, pembinaan akhlak, dan kegiatan penunjang untuk membentuk santri berilmu dan beradab.</p>
            <nav class="sj-nav" aria-label="Isi halaman">
                <a href="#jalur"><i class="fa-solid fa-route"></i> Jalur belajar</a>
                <a href="#kajian"><i class="fa-solid fa-book-open-reader"></i> Kajian</a>
                <a href="#jenjang"><i class="fa-solid fa-graduation-cap"></i> Jenjang</a>
                <a href="#metode"><i class="fa-solid fa-chalkboard-user"></i> Metode</a>
            </nav>
        </div>
    </header>

    {{-- ============ 1. TIGA JALUR BELAJAR ============ --}}
    <section class="sj-sec" id="jalur">
        <div class="sj-wrap">
            <div class="sj-lead">
                <div>
                    <div class="sj-eyebrow">Sistem Pembelajaran</div>
                    <h2>Pembelajaran dan kajian kitab kuning</h2>
                </div>
                <p>Seluruh pelajaran di Babul Hasanah disampaikan melalui kitab-kitab klasik yang diajarkan bertahap sesuai kemampuan santri. Pengajian pesantren berjalan bersama pendidikan formal, sehingga santri tumbuh kuat dalam ilmu agama sekaligus siap menghadapi zamannya.</p>
            </div>

            <div class="sj-lanes3">
                @foreach ($jalur as $j)
                    <article class="sj-l3 sj-rv">
                        <span class="no">{{ sprintf('%02d', $loop->iteration) }}</span>
                        <div class="ico"><i class="fa-solid {{ $j[0] }}"></i></div>
                        <h3>{{ $j[1] }}</h3>
                        <p>{{ $j[2] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ 2. BIDANG KAJIAN ============ --}}
    <section class="sj-sec alt">
        <div class="sj-wrap">
            <div class="sj-head">
                <div class="sj-eyebrow">Kajian &amp; Kitab</div>
                <h2>Ilmu yang dipelajari santri</h2>
                <p>Pilih bidang ilmu untuk melihat penjelasan dan kitab yang dikaji.</p>
            </div>

            <div class="kj" id="kajian">
                <div class="kj-tabs" role="tablist" aria-label="Bidang kajian">
                    @foreach ($kajian as $i => $k)
                        <button type="button" class="kj-tab" role="tab" id="kt-{{ $i }}" aria-controls="kp-{{ $i }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}" tabindex="{{ $loop->first ? '0' : '-1' }}">
                            <i class="fa-solid {{ $k['ikon'] }}"></i>{{ $k['judul'] }}
                        </button>
                    @endforeach
                </div>

                <div>
                    @foreach ($kajian as $i => $k)
                        <div class="kj-panel" role="tabpanel" id="kp-{{ $i }}" aria-labelledby="kt-{{ $i }}">
                            <div class="big"><i class="fa-solid {{ $k['ikon'] }}"></i></div>
                            <h3>{{ $k['judul'] }}</h3>
                            <p>{{ $k['isi'] }}</p>
                            <h4>Kitab yang dikaji</h4>
                            <div class="kitab">
                                @foreach ($k['kitab'] as $kt)
                                    <span>{{ $kt }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ============ 3. JENJANG PENDIDIKAN ============ --}}
    <section class="sj-sec" id="jenjang">
        <div class="sj-wrap">
            <div class="sj-head">
                <div class="sj-eyebrow">Jenjang Pendidikan</div>
                <h2>Jenjang dan program pendidikan</h2>
                <p>Tujuh jenjang di bawah naungan Yayasan Pondok Pesantren Babul Hasanah, terbagi dalam dua jalur.</p>
            </div>

            <div class="lane">
                <h3 class="lane-title"><i class="fa-solid fa-kaaba"></i> Jalur diniyah</h3>
                <p class="lane-note">Pendidikan berbasis kitab, dari tingkat dasar hingga Ma’had ‘Aly.</p>
                <div class="jj-grid">
                    @foreach ($jalurDiniyah as $j)
                        <article class="jj-card sj-rv">
                            <span class="jj-code">{{ $j[0] }}</span>
                            <h4>{{ $j[1] }}</h4>
                            <p>{{ $j[2] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>

            <div class="lane madrasah">
                <h3 class="lane-title"><i class="fa-solid fa-school"></i> Jalur madrasah</h3>
                <p class="lane-note">Pendidikan formal yang berjalan bersama kegiatan pesantren.</p>
                <div class="jj-grid">
                    @foreach ($jalurMadrasah as $j)
                        <article class="jj-card sj-rv">
                            <span class="jj-code">{{ $j[0] }}</span>
                            <h4>{{ $j[1] }}</h4>
                            <p>{{ $j[2] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ============ 4. METODE ============ --}}
    <section class="sj-sec alt" id="metode">
        <div class="sj-wrap">
            <div class="sj-head">
                <div class="sj-eyebrow">Metode Pembelajaran</div>
                <h2>Cara santri belajar</h2>
                <p>Empat metode khas pesantren yang dipakai bergantian sesuai jenis kitab dan tingkat santri.</p>
            </div>
            <div class="metode">
                @foreach ($metode as $m)
                    <article class="metode-item sj-rv">
                        <span class="no">{{ sprintf('%02d', $loop->iteration) }}</span>
                        <i class="fa-solid {{ $m[0] }}"></i>
                        <h3>{{ $m[1] }}</h3>
                        <p>{{ $m[2] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="sj-sec">
        <div class="sj-wrap">
            <div class="sj-cta">
                <h2>Siap belajar bersama kami?</h2>
                <p>Mulai langkah pertama menuju ilmu yang bermanfaat dan akhlak yang mulia.</p>
                <a href="{{ url('/pendaftaran') }}" class="sj-btn">Daftar online <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </section>
</div>

<script>
    (function () {
        // Tab kajian
        var root = document.getElementById('kajian');
        if (root) {
            var tabs = Array.prototype.slice.call(root.querySelectorAll('.kj-tab'));
            var panels = tabs.map(function (t) { return document.getElementById(t.getAttribute('aria-controls')); });

            var show = function (i, focus) {
                tabs.forEach(function (t, n) {
                    var on = n === i;
                    t.setAttribute('aria-selected', on ? 'true' : 'false');
                    t.tabIndex = on ? 0 : -1;
                    panels[n].hidden = !on;
                });
                if (focus) tabs[i].focus();
            };

            tabs.forEach(function (t, i) {
                t.addEventListener('click', function () { show(i, false); });
                t.addEventListener('keydown', function (e) {
                    var next = null;
                    if (e.key === 'ArrowDown' || e.key === 'ArrowRight') next = (i + 1) % tabs.length;
                    if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') next = (i - 1 + tabs.length) % tabs.length;
                    if (next !== null) { e.preventDefault(); show(next, true); }
                });
            });

            show(0, false); // tanpa JS, semua panel tetap tampil
        }

        // Animasi muncul saat scroll
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