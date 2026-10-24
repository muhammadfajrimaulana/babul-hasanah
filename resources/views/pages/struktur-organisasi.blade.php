@extends('layouts.app')

@section('title', 'Struktur Organisasi - Pondok Pesantren Babul Hasanah')

@section('content')

@php
    /* FOTO PENGURUS: taruh di public/images/pengurus/ (jika belum ada, tampil ikon). */
    $foto = fn ($f) => asset('images/pengurus/' . $f);

    /* ==== GANTI NAMA, JABATAN, DAN FOTO DENGAN DATA ASLI ==== */
    $pengasuh = ['nama' => 'KH. Nama Pengasuh', 'jabatan' => 'Pengasuh / Pimpinan',   'ikon' => 'fa-user-tie', 'foto' => 'pengasuh.jpg'];
    $mudir    = ['nama' => 'Ust. Nama Mudir',   'jabatan' => 'Mudir / Kepala Pondok', 'ikon' => 'fa-user',     'foto' => 'mudir.jpg'];

    $pengurus = [
        ['nama' => 'Ust. Nama Sekretaris', 'jabatan' => 'Sekretaris',         'ikon' => 'fa-pen-nib',       'foto' => 'sekretaris.jpg'],
        ['nama' => 'Ust. Nama Bendahara',  'jabatan' => 'Bendahara',          'ikon' => 'fa-wallet',        'foto' => 'bendahara.jpg'],
        ['nama' => 'Ust. Nama Pendidikan', 'jabatan' => 'Kepala Pendidikan',  'ikon' => 'fa-user-graduate', 'foto' => 'pendidikan.jpg'],
    ];

    /* ==== KETUA TIAP JENJANG / PROGRAM PENDIDIKAN (PPBH) — GANTI NAMA & FOTO ==== */
    $jenjang = [
        ['kode' => 'MDTA',        'nama' => 'Madrasah Diniyah Takmiliyah',      'ketua' => 'Ust. Nama Ketua MDTA',        'foto' => 'ketua-mdta.jpg'],
        ['kode' => 'PPS Wustha',  'nama' => 'Pondok Pesantren Salafiyah Wustha', 'ketua' => 'Ust. Nama Ketua PPS Wustha',  'foto' => 'ketua-pps-wustha.jpg'],
        ['kode' => 'MTs',         'nama' => 'Madrasah Tsanawiyah',              'ketua' => 'Ust. Nama Ketua MTs',         'foto' => 'ketua-mts.jpg'],
        ['kode' => 'MA',          'nama' => 'Madrasah Aliyah',                  'ketua' => 'Ust. Nama Ketua MA',          'foto' => 'ketua-ma.jpg'],
        ['kode' => 'PDF Wustha',  'nama' => 'Pendidikan Diniyah Formal Wustha', 'ketua' => 'Ust. Nama Ketua PDF Wustha',  'foto' => 'ketua-pdf-wustha.jpg'],
        ['kode' => 'PDF Ulya',    'nama' => 'Pendidikan Diniyah Formal Ulya',   'ketua' => 'Ust. Nama Ketua PDF Ulya',    'foto' => 'ketua-pdf-ulya.jpg'],
        ['kode' => 'Ma’had ‘Aly', 'nama' => 'Ma’had ‘Aly',                      'ketua' => 'Ust. Nama Ketua Ma’had ‘Aly', 'foto' => 'ketua-mahad-aly.jpg'],
    ];

    $bidang = [
        ['fa-book-open',      'Pendidikan',           'Kurikulum, jadwal, dan evaluasi pembelajaran.'],
        ['fa-shield-heart',   'Keamanan & Disiplin',  'Ketertiban dan pembinaan kedisiplinan santri.'],
        ['fa-language',       'Bahasa',               'Pengembangan bahasa Arab dan Inggris.'],
        ['fa-house-chimney',  'Sarana & Kebersihan',  'Perawatan fasilitas dan lingkungan pondok.'],
        ['fa-kit-medical',    'Kesehatan',            'Pelayanan kesehatan dan pertolongan pertama.'],
        ['fa-bullhorn',       'Humas & Media',        'Informasi, publikasi, dan hubungan masyarakat.'],
    ];
@endphp

<style>
    /* ====== Dasar mandiri (tidak bergantung CSS layout) ====== */
    .sj-page { --g800:#0f5132; --g700:#13693f; --g600:#15803d; --g500:#10b981; --g50:#f0fdf6;
        --gold:#b7791f; --gold-text:#955f0f; --gold-soft:#fdf3dc; --line:#7fc9a4; color:#1e293b; }
    .sj-page *, .sj-page *::before, .sj-page *::after { box-sizing: border-box; }
    .sj-wrap { width: 100%; max-width: 1180px; margin: 0 auto; padding: 0 24px; }

    .sj-top { background: var(--g50); padding: 48px 0 40px; border-bottom: 1px solid rgba(6,78,59,.1); }
    .sj-crumb { font-size: 14px; color: #475569; margin-bottom: 14px; }
    .sj-crumb a { color: var(--g700); text-decoration: none; }
    .sj-crumb a:hover { text-decoration: underline; }
    .sj-top h1 { margin: 0 0 10px; font-size: clamp(2rem, 4.4vw, 3rem); line-height: 1.15; color: var(--g800); }
    .sj-top p { margin: 0; max-width: 62ch; color: #475569; line-height: 1.75; font-size: 1.05rem; }

    .sj-sec { padding: 84px 0; }
    .sj-sec.alt { background: var(--g50); }
    .sj-head { max-width: 640px; margin: 0 0 48px; }
    .sj-head.center { margin-left: auto; margin-right: auto; text-align: center; }
    .sj-head h2 { font-size: clamp(1.7rem, 3.2vw, 2.4rem); line-height: 1.25; margin: 0 0 12px; color: var(--g800); }
    .sj-head p { margin: 0; color: #475569; line-height: 1.75; font-size: 1.05rem; }

    /* ====== Bagan: pohon dengan penghubung otomatis ====== */
    .tree { overflow-x: auto; padding-bottom: 8px; }
    .tree ul { list-style: none; margin: 0; padding: 30px 0 0; display: flex; justify-content: center; position: relative; }
    .tree > ul { padding-top: 0; min-width: max-content; margin: 0 auto; }
    .tree li { position: relative; padding: 30px 14px 0; text-align: center; }
    .tree > ul > li { padding-top: 0; }
    /* garis turun dari induk */
    .tree ul ul::before { content: ""; position: absolute; top: 0; left: 50%; height: 30px; border-left: 2px solid var(--line); }
    /* garis datar antar saudara */
    .tree li::before, .tree li::after { content: ""; position: absolute; top: 0; right: 50%; width: 50%; height: 30px; border-top: 2px solid var(--line); }
    .tree li::after { right: auto; left: 50%; border-left: 2px solid var(--line); }
    .tree li:only-child::before, .tree li:only-child::after { display: none; }
    .tree li:only-child { padding-top: 0; }
    .tree li:first-child::before, .tree li:last-child::after { border: 0; }
    .tree li:last-child::before { border-right: 2px solid var(--line); border-radius: 0 14px 0 0; }
    .tree li:first-child::after { border-radius: 14px 0 0 0; }
    .tree > ul > li::before, .tree > ul > li::after { display: none; }

    .person { display: inline-block; width: 240px; background: #fff; border-radius: 22px; padding: 26px 18px 22px;
        border: 1px solid rgba(6,78,59,.14); box-shadow: 0 14px 30px -16px rgba(20,83,45,.28); }
    .avatar { position: relative; width: 92px; height: 92px; border-radius: 50%; margin: 0 auto 14px; overflow: hidden;
        display: flex; align-items: center; justify-content: center; font-size: 36px; color: #fff;
        background: linear-gradient(135deg, #10b981, #0f5132); border: 5px solid #dcfce7; }
    .avatar img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
    .person h3 { margin: 0 0 8px; font-size: 1.05rem; line-height: 1.35; color: #0f172a; }
    .person span { display: inline-block; font-size: 13px; font-weight: 600; color: var(--g700); background: var(--g50); padding: 5px 14px; border-radius: 999px; }
    .person.gold .avatar { background: linear-gradient(135deg, #d99a2b, #b7791f); border-color: var(--gold-soft); }
    .person.gold span { color: var(--gold-text); background: var(--gold-soft); }
    .person.top { width: 290px; background: #0f5132; background: linear-gradient(140deg, #0b3f29, #15803d); border: 0; padding: 30px 20px 26px; }
    .person.top h3 { color: #fff; font-size: 1.2rem; }
    .person.top span { color: #fde68a; background: rgba(255,255,255,.16); }
    .person.top .avatar { width: 108px; height: 108px; border-color: rgba(255,255,255,.3); background: linear-gradient(135deg, #d99a2b, #b7791f); }


    /* ====== Ketua jenjang ====== */
    .jenjang { display: grid; grid-template-columns: repeat(4, 1fr); gap: 22px; }
    .jj { background: #fff; border-radius: 20px; overflow: hidden; border: 1px solid rgba(6,78,59,.14); text-align: center; box-shadow: 0 12px 28px -18px rgba(20,83,45,.35); }
    .jj-head { background: #0f5132; background: linear-gradient(140deg, #0b3f29, #15803d); color: #fff; padding: 22px 16px 54px; }
    .jj:nth-child(even) .jj-head { background: #955f0f; background: linear-gradient(140deg, #8a560d, #c98a2b); }
    .jj-head b { display: block; font-size: 1.5rem; line-height: 1.2; }
    .jj-head small { display: block; margin-top: 6px; font-size: .85rem; line-height: 1.4; color: rgba(255,255,255,.85); }
    .jj-body { padding: 0 16px 24px; }
    .jj .avatar { width: 88px; height: 88px; margin: -44px auto 12px; border-color: #fff; }
    .jj h3 { margin: 0 0 6px; font-size: 1.02rem; line-height: 1.35; color: #0f172a; }
    .jj span { display: inline-block; font-size: 13px; font-weight: 600; color: var(--g700); background: var(--g50); padding: 5px 14px; border-radius: 999px; }
    .jj:nth-child(even) span { color: var(--gold-text); background: var(--gold-soft); }
    @media (max-width: 1080px) { .jenjang { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 760px) { .jenjang { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 480px) { .jenjang { grid-template-columns: 1fr; } }

    /* ====== Bidang ====== */
    .bidang { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0 40px; }
    .bidang-item { padding: 28px 0; border-top: 1px solid rgba(6,78,59,.16); display: grid; grid-template-columns: 52px 1fr; gap: 16px; }
    .bidang-ico { width: 52px; height: 52px; border-radius: 16px; display: flex; align-items: center; justify-content: center;
        background: #dcfce7; color: var(--g700); font-size: 20px; }
    .bidang-item:nth-child(even) .bidang-ico { background: var(--gold-soft); color: var(--gold-text); }
    .bidang-item h3 { margin: 2px 0 6px; font-size: 1.12rem; color: var(--g800); }
    .bidang-item p { margin: 0; color: #475569; line-height: 1.7; }

    /* ====== CTA ====== */
    .sj-cta { background: #0f5132; background: linear-gradient(135deg, #0f5132, #15803d); color: #fff; border-radius: 28px; padding: 52px 40px; text-align: center; }
    .sj-cta h2 { margin: 0 0 12px; color: #fff; font-size: clamp(1.5rem, 3vw, 2.1rem); }
    .sj-cta p { margin: 0 auto 24px; max-width: 54ch; color: rgba(255,255,255,.9); line-height: 1.7; }
    .sj-btn { display: inline-flex; align-items: center; gap: 10px; background: #fff; color: var(--g800); font-weight: 700; padding: 14px 30px; border-radius: 14px; text-decoration: none; }
    .sj-btn:hover { background: #fde68a; }
    .sj-btn:focus-visible { outline: 3px solid #fde68a; outline-offset: 3px; }

    /* ====== Responsif ====== */
    @media (max-width: 960px) { .bidang { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 820px) {
        /* bagan jadi tumpukan vertikal */
        .tree { overflow: visible; }
        .tree > ul { min-width: 0; }
        .tree ul { flex-direction: column; align-items: center; padding-top: 22px; }
        .tree li { padding: 22px 0 0; }
        .tree li::before, .tree li::after { display: none; }
        .tree ul ul::before { height: 22px; }
        .tree li + li { border-top: 0; }
        .person, .person.top { width: 100%; max-width: 320px; }
    }
    @media (max-width: 640px) {
        .sj-sec { padding: 60px 0; }
        .bidang { grid-template-columns: 1fr; }
        .sj-cta { padding: 44px 24px; }
    }
</style>

<div class="sj-page">

    {{-- ============ JUDUL HALAMAN ============ --}}
    <section class="sj-top">
        <div class="sj-wrap">
            <div class="sj-crumb">
                <a href="{{ url('/') }}">Beranda</a> <i class="fa-solid fa-chevron-right" style="font-size:10px"></i>
                Profil <i class="fa-solid fa-chevron-right" style="font-size:10px"></i> Struktur Organisasi
            </div>
            <h1>Struktur Organisasi</h1>
            <p>Para pengasuh dan pengurus yang berkhidmat menjaga, membimbing, dan mengelola pesantren dengan penuh amanah.</p>
        </div>
    </section>

    {{-- ============ BAGAN KEPENGURUSAN ============ --}}
    <section class="sj-sec">
        <div class="sj-wrap">
            <div class="sj-head center">
                <h2>Susunan pengurus pesantren</h2>
                <p>Garis di bawah menunjukkan alur pembinaan dan koordinasi, dari pengasuh hingga pengurus harian.</p>
            </div>

            <div class="tree">
                <ul>
                    <li>
                        <div class="person top">
                            <div class="avatar"><i class="fa-solid {{ $pengasuh['ikon'] }}"></i><img src="{{ $foto($pengasuh['foto']) }}" alt="{{ $pengasuh['nama'] }}" onerror="this.remove()"></div>
                            <h3>{{ $pengasuh['nama'] }}</h3>
                            <span>{{ $pengasuh['jabatan'] }}</span>
                        </div>

                        <ul>
                            <li>
                                <div class="person gold">
                                    <div class="avatar"><i class="fa-solid {{ $mudir['ikon'] }}"></i><img src="{{ $foto($mudir['foto']) }}" alt="{{ $mudir['nama'] }}" onerror="this.remove()"></div>
                                    <h3>{{ $mudir['nama'] }}</h3>
                                    <span>{{ $mudir['jabatan'] }}</span>
                                </div>

                                <ul>
                                    @foreach ($pengurus as $p)
                                        <li>
                                            <div class="person">
                                                <div class="avatar"><i class="fa-solid {{ $p['ikon'] }}"></i><img src="{{ $foto($p['foto']) }}" alt="{{ $p['nama'] }}" loading="lazy" onerror="this.remove()"></div>
                                                <h3>{{ $p['nama'] }}</h3>
                                                <span>{{ $p['jabatan'] }}</span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    {{-- ============ KETUA TIAP JENJANG ============ --}}
    <section class="sj-sec alt">
        <div class="sj-wrap">
            <div class="sj-head">
                <h2>Ketua jenjang pendidikan</h2>
                <p>Tujuh jenjang dan program pendidikan di bawah naungan Yayasan Pondok Pesantren Babul Hasanah (PPBH), masing-masing dipimpin seorang ketua.</p>
            </div>

            <div class="jenjang">
                @foreach ($jenjang as $j)
                    <article class="jj">
                        <div class="jj-head">
                            <b>{{ $j['kode'] }}</b>
                            <small>{{ $j['nama'] }}</small>
                        </div>
                        <div class="jj-body">
                            <div class="avatar"><i class="fa-solid fa-user-tie"></i><img src="{{ $foto($j['foto']) }}" alt="{{ $j['ketua'] }}" loading="lazy" onerror="this.remove()"></div>
                            <h3>{{ $j['ketua'] }}</h3>
                            <span>Ketua {{ $j['kode'] }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ BIDANG & SEKSI ============ --}}
    <section class="sj-sec">
        <div class="sj-wrap">
            <div class="sj-head">
                <h2>Bidang pelayanan pesantren</h2>
                <p>Setiap bidang memiliki peran penting dalam menunjang kegiatan belajar dan kehidupan santri.</p>
            </div>

            <div class="bidang">
                @foreach ($bidang as $b)
                    <div class="bidang-item">
                        <div class="bidang-ico"><i class="fa-solid {{ $b[0] }}"></i></div>
                        <div>
                            <h3>{{ $b[1] }}</h3>
                            <p>{{ $b[2] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="sj-sec">
        <div class="sj-wrap">
            <div class="sj-cta">
                <h2>Ada yang ingin ditanyakan kepada pengurus?</h2>
                <p>Hubungi kami untuk informasi pendaftaran, kunjungan santri, atau kerja sama.</p>
                <a href="{{ url('/kontak') }}" class="sj-btn">Hubungi kami <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </section>
</div>
@endsection