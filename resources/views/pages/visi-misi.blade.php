@extends('layouts.app')

@section('title', 'Visi & Misi - Pondok Pesantren Babul Hasanah')

@section('content')

@php
    /* FOTO: taruh di public/images/profil/ (jika belum ada, tampil blok hijau + ikon). */
    $foto = fn ($f) => asset('images/profil/' . $f);

    $visi = 'Membangun Generasi Unggul dalam Penguasaan Kitab Kuning, Berakhlak Mulia, dan Berwawasan Luas.';

    $misi = [
        ['Pendalaman Ilmu Agama', 'Menyelenggarakan pendidikan kitab kuning secara berjenjang dan sistematis dengan sanad keilmuan yang jelas.'],
        ['Pembinaan Akhlak & Karakter', 'Menanamkan adab, kedisiplinan, kemandirian, dan keteladanan dalam kehidupan santri sehari-hari.'],
        ['Penguatan Wawasan & Keterampilan', 'Membekali santri dengan wawasan keilmuan, kemampuan berbahasa, dan keterampilan hidup yang relevan dengan zaman.'],
        ['Lingkungan Pesantren yang Kondusif', 'Menciptakan suasana belajar yang aman, nyaman, dan islami untuk mendukung tumbuh kembang santri.'],
        ['Pengabdian kepada Masyarakat', 'Mengembangkan dakwah dan kemaslahatan umat melalui kegiatan sosial, keagamaan, dan pendidikan.'],
    ];

    $nilai = [
        ['fa-book-quran',            'Religius', 'Berpegang teguh pada Al-Qur’an, Sunnah, dan tradisi ulama salaf.'],
        ['fa-heart',                 'Ikhlas',   'Belajar dan mengabdi semata-mata mengharap ridha Allah.'],
        ['fa-hands-holding-circle',  'Mandiri',  'Melatih santri hidup sederhana, disiplin, dan bertanggung jawab.'],
        ['fa-people-group',          'Ukhuwah',  'Menjalin persaudaraan yang erat antarsantri dan masyarakat.'],
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

    /* ====== Judul halaman ====== */
    .sj-top { background: var(--g50); padding: 48px 0 40px; border-bottom: 1px solid rgba(6,78,59,.1); }
    .sj-crumb { font-size: 14px; color: #475569; margin-bottom: 14px; }
    .sj-crumb a { color: var(--g700); text-decoration: none; }
    .sj-crumb a:hover { text-decoration: underline; }
    .sj-top h1 { margin: 0 0 10px; font-size: clamp(2rem, 4.4vw, 3rem); line-height: 1.15; color: var(--g800); }
    .sj-top p { margin: 0; max-width: 60ch; color: #475569; line-height: 1.75; font-size: 1.05rem; }

    .sj-sec { padding: 84px 0; }
    .sj-sec.alt { background: var(--g50); }
    .sj-head { max-width: 640px; margin: 0 0 44px; }
    .sj-head h2 { font-size: clamp(1.7rem, 3.2vw, 2.4rem); line-height: 1.25; margin: 0 0 12px; color: var(--g800); }
    .sj-head p { margin: 0; color: #475569; line-height: 1.75; font-size: 1.05rem; }

    /* ====== Visi ====== */
    .visi { background: #0f5132; background: linear-gradient(140deg, #0b3f29, #15803d); color: #fff; border-radius: 30px;
        padding: 64px 56px; display: grid; grid-template-columns: 1.25fr .75fr; gap: 56px; align-items: center;
        position: relative; overflow: hidden; box-shadow: 0 30px 60px -25px rgba(20,83,45,.6); }
    .visi::after { content: ""; position: absolute; right: -90px; bottom: -120px; width: 320px; height: 320px; border-radius: 50%;
        border: 54px solid rgba(255,255,255,.05); pointer-events: none; }
    .visi > * { position: relative; z-index: 1; }
    .visi-label { display: inline-flex; align-items: center; gap: 10px; background: rgba(255,255,255,.14); color: #fde68a;
        padding: 8px 18px; border-radius: 999px; font-weight: 700; font-size: 14px; margin-bottom: 22px; }
    .visi h2 { margin: 0; color: #fff; font-size: clamp(1.5rem, 3.2vw, 2.3rem); line-height: 1.5; font-weight: 700; }
    .visi .sj-photo { aspect-ratio: 4 / 5; max-width: 300px; margin-left: auto; width: 100%; border-radius: 999px 999px 24px 24px; box-shadow: 0 24px 50px rgba(0,0,0,.3); }

    /* ====== Misi (baris bergaris, urut) ====== */
    .misi { counter-reset: misi; }
    .misi-row { display: grid; grid-template-columns: 110px 1fr; gap: 28px; padding: 30px 0; border-top: 1px solid rgba(6,78,59,.16); align-items: start; }
    .misi-row:last-child { border-bottom: 1px solid rgba(6,78,59,.16); }
    .misi-no { font-size: 3.2rem; font-weight: 800; line-height: 1; color: var(--gold-text); opacity: .85; font-variant-numeric: tabular-nums; }
    .misi-row h3 { margin: 4px 0 8px; font-size: 1.3rem; line-height: 1.35; color: var(--g800); }
    .misi-row p { margin: 0; line-height: 1.8; color: #334155; font-size: 1.04rem; max-width: 62ch; }

    /* ====== Nilai ====== */
    .nilai { display: grid; grid-template-columns: repeat(4, 1fr); }
    .nilai-item { padding: 8px 28px; border-left: 1px solid rgba(6,78,59,.16); }
    .nilai-item:first-child { border-left: 0; padding-left: 0; }
    .nilai-ico { width: 56px; height: 56px; border-radius: 18px; display: flex; align-items: center; justify-content: center;
        font-size: 22px; background: var(--gold-soft); color: var(--gold-text); margin-bottom: 18px; }
    .nilai-item:nth-child(even) .nilai-ico { background: #dcfce7; color: var(--g700); }
    .nilai-item h3 { margin: 0 0 8px; font-size: 1.2rem; color: var(--g800); }
    .nilai-item p { margin: 0; color: #475569; line-height: 1.7; }

    /* ====== Motto ====== */
    .motto { text-align: center; padding: 64px 30px; border-radius: 28px; background: var(--g50); border: 2px dashed var(--g500); }
    .motto small { display: block; color: var(--gold-text); font-weight: 700; margin-bottom: 10px; font-size: 1rem; }
    .motto h2 { margin: 0; color: var(--g800); font-size: clamp(1.6rem, 3.6vw, 2.6rem); line-height: 1.35; font-weight: 800; }

    /* ====== CTA ====== */
    .sj-cta { background: #0f5132; background: linear-gradient(135deg, #0f5132, #15803d); color: #fff; border-radius: 28px; padding: 56px 40px; text-align: center; }
    .sj-cta h2 { margin: 0 0 12px; color: #fff; font-size: clamp(1.6rem, 3vw, 2.2rem); }
    .sj-cta p { margin: 0 auto 26px; max-width: 54ch; color: rgba(255,255,255,.9); line-height: 1.7; }
    .sj-btn { display: inline-flex; align-items: center; gap: 10px; background: #fff; color: var(--g800); font-weight: 700; padding: 14px 30px; border-radius: 14px; text-decoration: none; }
    .sj-btn:hover { background: #fde68a; }
    .sj-btn:focus-visible { outline: 3px solid #fde68a; outline-offset: 3px; }

    /* ====== Responsif ====== */
    @media (max-width: 960px) {
        .visi { grid-template-columns: 1fr; padding: 48px 32px; gap: 40px; }
        .visi .sj-photo { margin: 0 auto; max-width: 260px; }
        .nilai { grid-template-columns: repeat(2, 1fr); row-gap: 36px; }
        .nilai-item:nth-child(3) { border-left: 0; padding-left: 0; }
    }
    @media (max-width: 640px) {
        .sj-sec { padding: 60px 0; }
        .visi { padding: 40px 24px; border-radius: 24px; }
        .misi-row { grid-template-columns: 56px 1fr; gap: 16px; padding: 24px 0; }
        .misi-no { font-size: 2.2rem; }
        .nilai { grid-template-columns: 1fr; }
        .nilai-item, .nilai-item:nth-child(3) { border-left: 0; padding: 0; }
        .sj-cta { padding: 44px 24px; }
    }
</style>

<div class="sj-page">

    {{-- ============ JUDUL HALAMAN ============ --}}
    <section class="sj-top">
        <div class="sj-wrap">
            <div class="sj-crumb">
                <a href="{{ url('/') }}">Beranda</a> <i class="fa-solid fa-chevron-right" style="font-size:10px"></i>
                Profil <i class="fa-solid fa-chevron-right" style="font-size:10px"></i> Visi &amp; Misi
            </div>
            <h1>Visi &amp; Misi</h1>
            <p>Landasan cita-cita dan langkah nyata kami dalam membangun generasi unggul, berilmu, dan berakhlak mulia.</p>
        </div>
    </section>

    {{-- ============ VISI ============ --}}
    <section class="sj-sec">
        <div class="sj-wrap">
            <div class="visi">
                <div>
                    <span class="visi-label"><i class="fa-solid fa-eye"></i> Visi kami</span>
                    <h2>{{ $visi }}</h2>
                </div>
                <div class="sj-photo">
                    <img src="{{ $foto('visi.jpg') }}" alt="Santri Babul Hasanah" loading="lazy" onerror="this.remove()">
                    <i class="fa-solid fa-book-quran"></i>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ MISI ============ --}}
    <section class="sj-sec alt">
        <div class="sj-wrap">
            <div class="sj-narrow">
                <div class="sj-head">
                    <h2>Misi pondok pesantren</h2>
                    <p>Untuk mewujudkan visi tersebut, kami menjalankan lima misi berikut dengan penuh komitmen.</p>
                </div>

                <div class="misi">
                    @foreach ($misi as $m)
                        <article class="misi-row">
                            <div class="misi-no">{{ $loop->iteration }}</div>
                            <div>
                                <h3>{{ $m[0] }}</h3>
                                <p>{{ $m[1] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ============ NILAI-NILAI ============ --}}
    <section class="sj-sec">
        <div class="sj-wrap">
            <div class="sj-head">
                <h2>Nilai-nilai utama</h2>
                <p>Empat pegangan yang kami tanamkan dalam keseharian santri.</p>
            </div>
            <div class="nilai">
                @foreach ($nilai as $n)
                    <div class="nilai-item">
                        <div class="nilai-ico"><i class="fa-solid {{ $n[0] }}"></i></div>
                        <h3>{{ $n[1] }}</h3>
                        <p>{{ $n[2] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ MOTTO ============ --}}
    <section style="padding-bottom:0">
        <div class="sj-wrap">
            <div class="motto">
                <small>Motto pesantren</small>
                <h2>“Berilmu, Beramal, Berakhlak Mulia”</h2>
            </div>
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="sj-sec">
        <div class="sj-wrap">
            <div class="sj-cta">
                <h2>Jadilah bagian dari keluarga besar Babul Hasanah</h2>
                <p>Daftarkan putra-putri Anda dan mulailah perjalanan menuntut ilmu yang penuh berkah.</p>
                <a href="{{ url('/pendaftaran') }}" class="sj-btn">Daftar online <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </section>
</div>
@endsection