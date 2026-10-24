{{--
    TEMPLATE BERSAMA untuk semua halaman program/jenjang.
    Simpan di: resources/views/pages/program/_detail.blade.php
    Dipanggil dari mdta.blade.php, mts.blade.php, dst. dengan @include('pages.program._detail', ['p' => $p])
    Semua isi (teks, angka, nama) diatur di file masing-masing program, bukan di sini.
--}}
@php
    $foto = fn ($f) => asset('images/program/' . $f);

    $semua = [
        ['mdta',       'MDTA',        'Madrasah Diniyah Takmiliyah'],
        ['pps-wustha', 'PPS Wustha',  'Pondok Pesantren Salafiyah Wustha'],
        ['mts',        'MTs',         'Madrasah Tsanawiyah'],
        ['ma',         'MA',          'Madrasah Aliyah'],
        ['pdf-wustha', 'PDF Wustha',  'Pendidikan Diniyah Formal Wustha'],
        ['pdf-ulya',   'PDF Ulya',    'Pendidikan Diniyah Formal Ulya'],
        ['mahad-aly',  'Ma’had ‘Aly', 'Ma’had ‘Aly'],
    ];
@endphp

<style>
    /* ====== Dasar mandiri (tidak bergantung CSS layout) ====== */
    .sj-page { --g800:#0f5132; --g700:#13693f; --g600:#15803d; --g500:#10b981; --g50:#f0fdf6;
        --gold:#b7791f; --gold-text:#955f0f; --gold-soft:#fdf3dc; color:#1e293b; }
    .sj-page *, .sj-page *::before, .sj-page *::after { box-sizing: border-box; }
    .sj-wrap { width: 100%; max-width: 1180px; margin: 0 auto; padding: 0 24px; }
    .sj-narrow { max-width: 900px; margin: 0 auto; }

    .sj-photo { position: relative; overflow: hidden; background: linear-gradient(145deg, #15803d, #0f5132); }
    .sj-photo img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block; }
    .sj-photo > i { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 64px; color: rgba(255,255,255,.22); }

    .sj-top { background: var(--g50); padding: 48px 0 40px; border-bottom: 1px solid rgba(6,78,59,.1); }
    .sj-crumb { font-size: 14px; color: #475569; margin-bottom: 14px; }
    .sj-crumb a { color: var(--g700); text-decoration: none; }
    .sj-crumb a:hover { text-decoration: underline; }
    .pg-kode { display: inline-flex; align-items: center; gap: 10px; background: var(--g700); color: #fff; font-weight: 800; padding: 8px 18px; border-radius: 12px; margin-bottom: 16px; }
    .sj-top h1 { margin: 0 0 10px; font-size: clamp(2rem, 4.4vw, 3rem); line-height: 1.15; color: var(--g800); }
    .sj-top p { margin: 0; max-width: 62ch; color: #475569; line-height: 1.75; font-size: 1.08rem; }

    .sj-sec { padding: 76px 0; }
    .sj-sec.alt { background: var(--g50); }
    .sj-head { max-width: 640px; margin: 0 0 40px; }
    .sj-head h2 { font-size: clamp(1.6rem, 3vw, 2.2rem); line-height: 1.25; margin: 0 0 10px; color: var(--g800); }
    .sj-head p { margin: 0; color: #475569; line-height: 1.75; font-size: 1.05rem; }

    /* ====== Banner + pengantar ====== */
    .sj-banner { margin-top: 36px; aspect-ratio: 21 / 9; border-radius: 24px; box-shadow: 0 22px 50px rgba(6,78,59,.18); }
    .pg-intro { padding: 40px 0 8px; }
    .pg-intro p { margin: 0 0 14px; font-size: 1.1rem; line-height: 1.85; color: #334155; }

    /* ====== Fakta ringkas ====== */
    .fakta { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); margin-top: 40px; border-top: 1px solid rgba(6,78,59,.16); border-bottom: 1px solid rgba(6,78,59,.16); }
    .fakta-item { padding: 24px 22px; border-left: 1px solid rgba(6,78,59,.16); }
    .fakta-item:first-child { border-left: 0; padding-left: 0; }
    .fakta-item i { color: var(--gold-text); font-size: 18px; margin-bottom: 10px; display: block; }
    .fakta-item small { display: block; color: #475569; font-size: .9rem; margin-bottom: 4px; }
    .fakta-item b { display: block; font-size: 1.15rem; line-height: 1.35; color: var(--g800); }

    /* ====== Kurikulum ====== */
    .kur { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0 56px; }
    .kur-group { padding: 26px 0; border-top: 1px solid rgba(6,78,59,.16); }
    .kur-group h3 { margin: 0 0 14px; font-size: 1.2rem; color: var(--g800); display: flex; align-items: center; gap: 12px; }
    .kur-group h3 i { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 16px; background: #dcfce7; color: var(--g700); flex: none; }
    .kur-group:nth-child(even) h3 i { background: var(--gold-soft); color: var(--gold-text); }
    .chips { display: flex; flex-wrap: wrap; gap: 8px; }
    .chips span { font-weight: 600; font-size: .95rem; color: var(--g800); background: #fff; border: 1px solid rgba(6,78,59,.18); padding: 7px 15px; border-radius: 999px; }
    .sj-sec.alt .chips span { background: #fff; }

    /* ====== Capaian + kegiatan ====== */
    .two { display: grid; grid-template-columns: 1.1fr .9fr; gap: 56px; align-items: start; }
    .two h2 { margin: 0 0 22px; font-size: clamp(1.5rem, 2.8vw, 2rem); color: var(--g800); line-height: 1.25; }
    .cek { list-style: none; margin: 0; padding: 0; }
    .cek li { display: grid; grid-template-columns: 30px 1fr; gap: 12px; padding: 12px 0; line-height: 1.7; color: #334155; border-bottom: 1px solid rgba(6,78,59,.1); }
    .cek li:last-child { border-bottom: 0; }
    .cek i { color: var(--g600); margin-top: 5px; }
    .kegiatan { background: #0f5132; background: linear-gradient(145deg, #0b3f29, #15803d); color: #fff; border-radius: 26px; padding: 34px 32px; }
    .kegiatan h2 { color: #fff; font-size: 1.4rem; margin-bottom: 16px; }
    .kegiatan ul { list-style: none; margin: 0; padding: 0; }
    .kegiatan li { padding: 10px 0 10px 24px; position: relative; line-height: 1.6; color: rgba(255,255,255,.93); border-bottom: 1px solid rgba(255,255,255,.14); }
    .kegiatan li:last-child { border-bottom: 0; }
    .kegiatan li::before { content: ""; position: absolute; left: 0; top: 19px; width: 9px; height: 9px; border-radius: 50%; background: #fde68a; }

    /* ====== Ketua ====== */
    .ketua { display: grid; grid-template-columns: 200px 1fr; gap: 40px; align-items: center; background: #fff; border-radius: 28px; padding: 36px; box-shadow: 0 20px 44px -26px rgba(6,78,59,.4); border: 1px solid rgba(6,78,59,.1); }
    .ketua .sj-photo { aspect-ratio: 1 / 1; width: 100%; border-radius: 999px 999px 24px 24px; }
    .ketua h3 { margin: 0 0 6px; font-size: 1.5rem; color: var(--g800); }
    .ketua .peran { display: inline-block; font-weight: 600; font-size: .92rem; color: var(--gold-text); background: var(--gold-soft); padding: 5px 14px; border-radius: 999px; margin-bottom: 16px; }
    .ketua blockquote { margin: 0; padding-left: 20px; border-left: 4px solid var(--gold); color: #334155; line-height: 1.85; font-size: 1.05rem; }

    /* ====== Program lainnya ====== */
    .lain { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
    .lain a { display: block; text-decoration: none; padding: 18px 20px; border-radius: 16px; background: #fff; border: 1px solid rgba(6,78,59,.14); color: var(--g800); }
    .lain a:hover { border-color: var(--g500); background: var(--g50); }
    .lain a:focus-visible { outline: 3px solid var(--gold); outline-offset: 2px; }
    .lain b { display: block; font-size: 1.1rem; }
    .lain small { color: #475569; line-height: 1.4; display: block; margin-top: 2px; }
    .lain a[aria-current="page"] { background: var(--g700); border-color: transparent; color: #fff; pointer-events: none; }
    .lain a[aria-current="page"] small { color: rgba(255,255,255,.85); }

    /* ====== CTA ====== */
    .sj-cta { background: #0f5132; background: linear-gradient(135deg, #0f5132, #15803d); color: #fff; border-radius: 28px; padding: 56px 40px; text-align: center; }
    .sj-cta h2 { margin: 0 0 12px; color: #fff; font-size: clamp(1.6rem, 3vw, 2.2rem); }
    .sj-cta p { margin: 0 auto 26px; max-width: 54ch; color: rgba(255,255,255,.9); line-height: 1.7; }
    .sj-btn { display: inline-flex; align-items: center; gap: 10px; background: #fff; color: var(--g800); font-weight: 700; padding: 14px 30px; border-radius: 14px; text-decoration: none; }
    .sj-btn:hover { background: #fde68a; }
    .sj-btn:focus-visible { outline: 3px solid #fde68a; outline-offset: 3px; }

    /* ====== Responsif ====== */
    @media (max-width: 960px) {
        .kur, .two { grid-template-columns: 1fr; gap: 36px; }
        .lain { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
        .sj-sec { padding: 56px 0; }
        .sj-banner { aspect-ratio: 16 / 10; border-radius: 18px; }
        .fakta { grid-template-columns: repeat(2, 1fr); }
        .fakta-item, .fakta-item:first-child { padding: 20px 14px; border-left: 0; border-top: 1px solid rgba(6,78,59,.1); }
        .fakta-item:nth-child(-n+2) { border-top: 0; }
        .ketua { grid-template-columns: 1fr; padding: 26px 22px; gap: 24px; }
        .ketua .sj-photo { max-width: 180px; }
        .sj-cta { padding: 44px 24px; }
    }
    @media (max-width: 480px) { .lain { grid-template-columns: 1fr; } .fakta { grid-template-columns: 1fr; } .fakta-item:nth-child(2) { border-top: 1px solid rgba(6,78,59,.1); } }
</style>

<div class="sj-page">

    {{-- ============ JUDUL ============ --}}
    <section class="sj-top">
        <div class="sj-wrap">
            <div class="sj-crumb">
                <a href="{{ url('/') }}">Beranda</a> <i class="fa-solid fa-chevron-right" style="font-size:10px"></i>
                Program <i class="fa-solid fa-chevron-right" style="font-size:10px"></i> {{ $p['kode'] }}
            </div>
            <span class="pg-kode"><i class="fa-solid {{ $p['ikon'] }}"></i> {{ $p['kode'] }}</span>
            <h1>{{ $p['nama'] }}</h1>
            <p>{{ $p['tagline'] }}</p>
        </div>
    </section>

    {{-- ============ BANNER, PENGANTAR, FAKTA ============ --}}
    <section>
        <div class="sj-wrap">
            <div class="sj-photo sj-banner">
                <img src="{{ $foto($p['banner']) }}" alt="{{ $p['nama'] }}" onerror="this.remove()">
                <i class="fa-solid {{ $p['ikon'] }}"></i>
            </div>

            <div class="sj-narrow pg-intro">
                @foreach ($p['deskripsi'] as $para)
                    <p>{{ $para }}</p>
                @endforeach
            </div>

            <div class="fakta">
                @foreach ($p['fakta'] as $f)
                    <div class="fakta-item">
                        <i class="fa-solid {{ $f[0] }}"></i>
                        <small>{{ $f[1] }}</small>
                        <b>{{ $f[2] }}</b>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ YANG DIPELAJARI ============ --}}
    <section class="sj-sec">
        <div class="sj-wrap">
            <div class="sj-head">
                <h2>Yang dipelajari di {{ $p['kode'] }}</h2>
                <p>{{ $p['kurikulum_intro'] }}</p>
            </div>
            <div class="kur">
                @foreach ($p['kurikulum'] as $g)
                    <div class="kur-group">
                        <h3><i class="fa-solid {{ $g['ikon'] }}"></i>{{ $g['judul'] }}</h3>
                        <div class="chips">
                            @foreach ($g['items'] as $it)
                                <span>{{ $it }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ CAPAIAN & KEGIATAN ============ --}}
    <section class="sj-sec alt">
        <div class="sj-wrap">
            <div class="two">
                <div>
                    <h2>Capaian lulusan</h2>
                    <ul class="cek">
                        @foreach ($p['capaian'] as $c)
                            <li><i class="fa-solid fa-circle-check"></i><span>{{ $c }}</span></li>
                        @endforeach
                    </ul>
                </div>
                <div class="kegiatan">
                    <h2>Kegiatan khas</h2>
                    <ul>
                        @foreach ($p['kegiatan'] as $k)
                            <li>{{ $k }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ KETUA ============ --}}
    <section class="sj-sec">
        <div class="sj-wrap">
            <div class="sj-narrow">
                <div class="ketua">
                    <div class="sj-photo">
                        <img src="{{ asset('images/pengurus/' . $p['ketua']['foto']) }}" alt="{{ $p['ketua']['nama'] }}" loading="lazy" onerror="this.remove()">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <div>
                        <h3>{{ $p['ketua']['nama'] }}</h3>
                        <span class="peran">{{ $p['ketua']['jabatan'] }}</span>
                        <blockquote>{{ $p['ketua']['sambutan'] }}</blockquote>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ PROGRAM LAINNYA ============ --}}
    <section class="sj-sec alt">
        <div class="sj-wrap">
            <div class="sj-head">
                <h2>Jenjang dan program lainnya</h2>
            </div>
            <nav class="lain" aria-label="Program lainnya">
                @foreach ($semua as $s)
                    <a href="{{ url('/program/' . $s[0]) }}" @if ($s[1] === $p['kode']) aria-current="page" @endif>
                        <b>{{ $s[1] }}</b><small>{{ $s[2] }}</small>
                    </a>
                @endforeach
            </nav>
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="sj-sec">
        <div class="sj-wrap">
            <div class="sj-cta">
                <h2>Tertarik mendaftar di {{ $p['kode'] }}?</h2>
                <p>Daftarkan putra-putri Anda secara online, atau hubungi kami untuk informasi syarat dan biaya.</p>
                <a href="{{ url('/pendaftaran') }}" class="sj-btn">Daftar online <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </section>
</div>
