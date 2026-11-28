{{--
    TEMPLATE BERSAMA untuk semua halaman jenjang.
    Dipanggil dari mdta.blade.php, mts.blade.php, dst. dengan @include('pages.jenjang._detail', ['p' => $p]).
    Isi ($p) disiapkan di Public\JenjangController@show dari App\Support\DataJenjang,
    bukan ditulis ulang di masing-masing file Blade.
--}}
@php
    $foto = fn ($f) => \App\Support\GambarPublik::banner($f);

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
    /* ====== Dasar: font & palet memakai token yang sama dengan halaman profil ====== */
    .sj-page { --g800:var(--green); --g700:var(--green-2); --g600:var(--emerald-d); --g500:var(--emerald);
        --g50:var(--paper); --gold:#b7791f; --gold-text:#955f0f; --gold-soft:#fdf4e0; --gold-light:#f3d9a0;
        color: var(--ink); font-family: 'Poppins', system-ui, -apple-system, sans-serif; font-size: 15px; line-height: 1.6; }
    .sj-page *, .sj-page *::before, .sj-page *::after { box-sizing: border-box; }
    /* Container mengikuti acuan bersama: maksimal 1200px, margin 5% tiap sisi. */
    .sj-wrap { width: min(1200px, 90%); margin-inline: auto; }
    .sj-narrow { max-width: 900px; margin: 0 auto; }

    .sj-photo { position: relative; overflow: hidden; background: linear-gradient(145deg, var(--green), var(--green-2)); }
    .sj-photo img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block; }
    .sj-photo > i { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 64px; color: rgba(255,255,255,.22); }

    .sj-top { background: linear-gradient(135deg, var(--green) 0%, var(--green-2) 68%, #2a6a4d 100%); padding: 38px 0 58px; color: #fff; }
    .sj-crumb { font-size: 13px; line-height: 1.6; color: rgba(255,255,255,.72); margin-bottom: 26px; overflow-wrap: anywhere; }
    .sj-crumb a { color: #fff; text-decoration: none; }
    .sj-crumb a:hover { color: var(--gold-light); text-decoration: underline; }
    .pg-kode { display: inline-flex; align-items: center; gap: 10px; background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.24); color: var(--gold-light); font-weight: 700; font-size: .8rem; letter-spacing: .12em; text-transform: uppercase; padding: 8px 16px; border-radius: 999px; margin-bottom: 16px; }
    .sj-top h1 { margin: 0 0 16px; font-size: clamp(1.75rem, 4vw, 2.9rem); font-weight: 800; line-height: 1.18; letter-spacing: -.02em; color: #fff; overflow-wrap: anywhere; }
    .sj-top p { margin: 0; max-width: 680px; color: rgba(255,255,255,.85); line-height: 1.8; font-size: 1rem; overflow-wrap: anywhere; }

    .sj-sec { padding: 64px 0; }
    .sj-sec.alt { background: var(--cream); }
    .sj-head { max-width: 680px; margin: 0 0 30px; }
    .sj-head h2 { font-size: clamp(1.5rem, 2.6vw, 2.05rem); font-weight: 800; line-height: 1.3; letter-spacing: -.015em; margin: 0 0 10px; color: var(--g800); }
    .sj-head p { margin: 0; color: var(--muted); line-height: 1.75; font-size: .98rem; }

    /* ====== Banner + pengantar ====== */
    .sj-banner { margin-top: 36px; aspect-ratio: 21 / 9; border-radius: 22px; box-shadow: 0 22px 50px -34px rgba(23, 64, 47, .35); }
    /* Pengantar mengikuti lebar .sj-wrap (sama dengan banner), tidak dibatasi .sj-narrow. */
    .pg-intro { max-width: none; padding: 40px 0 8px; }
    .pg-intro p { margin: 0 0 14px; font-size: 1.05rem; line-height: 1.85; color: var(--ink); overflow-wrap: anywhere; }

    /* ====== Fakta ringkas ====== */
    .fakta { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); margin-top: 40px; border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); }
    .fakta-item { padding: 24px 22px; border-left: 1px solid var(--line); }
    .fakta-item:first-child { border-left: 0; padding-left: 0; }
    .fakta-item i { color: var(--gold-text); font-size: 18px; margin-bottom: 10px; display: block; }
    .fakta-item small { display: block; color: var(--muted); font-size: .9rem; margin-bottom: 4px; overflow-wrap: anywhere; }
    .fakta-item b { display: block; font-size: 1.15rem; line-height: 1.35; color: var(--g800); overflow-wrap: anywhere; }

    /* ====== Kurikulum ====== */
    .kur { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0 22px; }
    .kur-group { padding: 26px 0; border-top: 1px solid var(--line); }
    .kur-group h3 { margin: 0 0 14px; font-size: 1.2rem; color: var(--g800); display: flex; align-items: center; gap: 12px; min-width: 0; }
    .kur-group h3 span { min-width: 0; overflow-wrap: anywhere; }
    .kur-group h3 i { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 16px; background: rgba(18, 184, 134, .12); color: var(--emerald-d); flex: none; }
    .kur-group:nth-child(even) h3 i { background: var(--gold-soft); color: var(--gold-text); }
    .chips { display: flex; flex-wrap: wrap; gap: 8px; }
    .chips span { font-weight: 600; font-size: .95rem; color: var(--g800); background: #fff; border: 1px solid var(--line); padding: 7px 15px; border-radius: 999px; max-width: 100%; overflow-wrap: anywhere; }
    .sj-sec.alt .chips span { background: #fff; }

    /* ====== Capaian + kegiatan ====== */
    .two { display: grid; grid-template-columns: 1.1fr .9fr; gap: 22px; align-items: start; }
    .two h2 { margin: 0 0 22px; font-size: clamp(1.5rem, 2.8vw, 2rem); color: var(--g800); line-height: 1.25; }
    .cek { list-style: none; margin: 0; padding: 0; }
    .cek li { display: grid; grid-template-columns: 30px 1fr; gap: 12px; padding: 12px 0; line-height: 1.7; color: var(--ink); border-bottom: 1px solid var(--line); }
    .cek li:last-child { border-bottom: 0; }
    .cek li span { min-width: 0; overflow-wrap: anywhere; }
    .cek i { color: var(--g600); margin-top: 5px; }
    .kegiatan { background: linear-gradient(135deg, var(--green), var(--green-2)); color: #fff; border-radius: 26px; padding: 34px 32px; }
    .kegiatan h2 { color: #fff; font-size: 1.4rem; margin-bottom: 16px; overflow-wrap: anywhere; }
    .kegiatan ul { list-style: none; margin: 0; padding: 0; }
    .kegiatan li { padding: 10px 0 10px 24px; position: relative; line-height: 1.6; color: rgba(255,255,255,.93); border-bottom: 1px solid rgba(255,255,255,.14); overflow-wrap: anywhere; }
    .kegiatan li:last-child { border-bottom: 0; }
    .kegiatan li::before { content: ""; position: absolute; left: 0; top: 19px; width: 9px; height: 9px; border-radius: 50%; background: var(--gold-light); }

    /* ====== Ketua ====== */
    .ketua { display: grid; grid-template-columns: 200px 1fr; gap: 22px; align-items: center; background: #fff; border-radius: 28px; padding: 36px; box-shadow: 0 20px 44px -26px rgba(23, 64, 47, .35); border: 1px solid var(--line); }
    .ketua .sj-photo { aspect-ratio: 1 / 1; width: 100%; border-radius: 999px 999px 24px 24px; }
    .ketua h3 { margin: 0 0 6px; font-size: 1.5rem; color: var(--g800); overflow-wrap: anywhere; }
    .ketua .peran { display: inline-block; font-weight: 600; font-size: .92rem; color: var(--gold-text); background: var(--gold-soft); padding: 5px 14px; border-radius: 999px; margin-bottom: 16px; }
    .ketua blockquote { margin: 0; padding-left: 20px; border-left: 4px solid var(--gold); color: var(--ink); line-height: 1.85; font-size: 1.05rem; overflow-wrap: anywhere; }

    /* ====== Program lainnya ====== */
    .lain { display: grid; grid-template-columns: repeat(4, 1fr); gap: 22px; }
    .lain a { display: block; text-decoration: none; padding: 18px 20px; border-radius: 16px; background: #fff; border: 1px solid var(--line); color: var(--g800); }
    .lain a:hover { border-color: var(--g500); background: var(--paper); }
    .lain a:focus-visible { outline: 3px solid var(--gold); outline-offset: 2px; }
    .lain b { display: block; font-size: 1.1rem; overflow-wrap: anywhere; }
    .lain small { color: var(--muted); line-height: 1.4; display: block; margin-top: 2px; overflow-wrap: anywhere; }
    .lain a[aria-current="page"] { background: var(--g700); border-color: transparent; color: #fff; pointer-events: none; }
    .lain a[aria-current="page"] small { color: rgba(255,255,255,.85); }

    /* ====== CTA: kotak ajakan sama seperti halaman Alumni ====== */
    .sj-cta { background: linear-gradient(135deg, var(--green), var(--green-2)); color: #fff; border-radius: 32px; padding: 50px; display: flex; justify-content: space-between; align-items: center; gap: 30px; flex-wrap: wrap; box-shadow: var(--shadow-lg); }
    .sj-cta h2 { margin: 0 0 8px; color: #fff; font-size: clamp(22px, 3vw, 32px); font-weight: 800; letter-spacing: -.01em; overflow-wrap: anywhere; }
    .sj-cta p { margin: 0; max-width: 520px; color: rgba(255,255,255,.82); line-height: 1.7; overflow-wrap: anywhere; }
    .sj-btn { display: inline-flex; align-items: center; gap: 10px; background: var(--cream); color: var(--green); font-weight: 600; font-size: 13.5px; padding: 14px 28px; border-radius: 999px; text-decoration: none; white-space: nowrap; transition: all .25s ease; }
    .sj-page .sj-btn:hover { background: #fff; color: var(--green); transform: translateY(-2px); box-shadow: 0 10px 22px rgba(0,0,0,.18); }
    .sj-btn:focus-visible { outline: 3px solid var(--gold-light); outline-offset: 3px; }

    /* ====== Responsif ====== */
    @media (max-width: 960px) {
        .kur, .two { grid-template-columns: 1fr; gap: 36px; }
        .lain { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
        .sj-top { padding: 40px 0 48px; }
        .sj-sec { padding: 48px 0; }
        .sj-banner { aspect-ratio: 16 / 10; border-radius: 18px; }
        .fakta { grid-template-columns: repeat(2, 1fr); }
        .fakta-item, .fakta-item:first-child { padding: 20px 14px; border-left: 0; border-top: 1px solid var(--line); }
        .fakta-item:nth-child(-n+2) { border-top: 0; }
        .ketua { grid-template-columns: 1fr; padding: 26px 22px; gap: 24px; }
        .ketua .sj-photo { max-width: 180px; }
        .sj-cta { padding: 30px 22px; border-radius: 22px; }
        .sj-btn { width: 100%; justify-content: center; }
    }
    @media (max-width: 600px) {
        .sj-top h1 { font-size: 1.55rem; line-height: 1.25; }
        .sj-top p { font-size: .92rem; }
        .pg-kode { font-size: .72rem; padding: 7px 12px; }
        .sj-head h2 { font-size: 1.45rem; }
        .pg-intro p { font-size: .98rem; }
        .kegiatan { padding: 26px 20px; border-radius: 20px; }
        .kegiatan h2 { font-size: 1.25rem; }
        .sj-cta h2 { font-size: 20px; }
    }
    @media (max-width: 480px) {
        .lain { grid-template-columns: 1fr; }
        .fakta { grid-template-columns: 1fr; }
        .fakta-item:nth-child(2) { border-top: 1px solid var(--line); }
        .sj-crumb { font-size: 12px; }
        .sj-banner { aspect-ratio: 4 / 3; }
        .ketua { padding: 24px 18px; gap: 20px; }
        .ketua h3 { font-size: 1.3rem; }
        .ketua blockquote { font-size: .98rem; padding-left: 14px; }
        .lain a { padding: 16px; }
        .sj-cta { padding: 26px 18px; }
    }
</style>

<div class="sj-page">

    {{-- ============ JUDUL ============ --}}
    <section class="sj-top">
        <div class="sj-wrap">
            <div class="sj-crumb">
                <a href="{{ route('home') }}">Beranda</a> <i class="fa-solid fa-chevron-right" style="font-size:10px"></i>
                Jenjang <i class="fa-solid fa-chevron-right" style="font-size:10px"></i> {{ $p['kode'] }}
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
                <img src="{{ $foto($p['banner']) }}" alt="{{ $p['nama'] }}">
                <i class="fa-solid {{ $p['ikon'] }}"></i>
            </div>

            <div class="pg-intro">
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
                        <h3><i class="fa-solid {{ $g['ikon'] }}"></i><span>{{ $g['judul'] }}</span></h3>
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
                        <img src="{{ \App\Support\GambarPublik::ketua($p['ketua']['foto']) }}" alt="{{ $p['ketua']['nama'] }}" loading="lazy">
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
            <nav class="lain" aria-label="Jenjang lainnya">
                @foreach ($semua as $s)
                    <a href="{{ route('jenjang.show', $s[0]) }}" @if ($s[1] === $p['kode']) aria-current="page" @endif>
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
                <div>
                    <h2>Tertarik mendaftar di {{ $p['kode'] }}?</h2>
                    <p>Daftarkan putra-putri Anda secara online, atau hubungi kami untuk informasi syarat dan biaya.</p>
                </div>
                <a href="{{ route('pendaftaran.index') }}" class="sj-btn">Daftar Santri Baru <i
                        class="fa-solid fa-arrow-up-right-from-square" style="font-size:12px"></i></a>
            </div>
        </div>
    </section>
</div>
