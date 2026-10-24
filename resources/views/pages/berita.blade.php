@extends('layouts.app')

@section('title', 'Berita Pondok Pesantren Babul Hasanah')

@section('content')

@php
    /*
     * CATATAN: data di bawah masih CONTOH. Nanti ganti dengan data dari database
     * (controller) dan cukup sesuaikan nama field-nya.
     */
    $gambar = asset('assets/img/banner/banner.png');

    $headline = [
        'kategori' => 'Pendidikan',
        'waktu'    => '10 menit lalu',
        'judul'    => 'Pondok Pesantren Babul Hasanah Cetak Generasi Unggul Penguasa Kitab Kuning',
        'ringkas'  => 'Komitmen memadukan kurikulum salafiyah murni dan sistem pendidikan modern membawa hasil gemilang bagi perkembangan santri di Padang Lawas.',
        'url'      => '#',
    ];

    $sub = [
        ['kategori' => 'Pengumuman', 'judul' => 'Jadwal Seleksi Masuk Gelombang 1 Resmi Diumumkan Oleh Panitia', 'waktu' => '2 jam lalu', 'penulis' => 'Admin', 'url' => '#'],
        ['kategori' => 'Kegiatan', 'judul' => 'Santri Gelar Pelatihan Pidato 3 Bahasa (Arab, Inggris, Indonesia)', 'waktu' => '5 jam lalu', 'penulis' => 'Pers Santri', 'url' => '#'],
    ];

    $populer = [
        ['kategori' => 'Akademik', 'judul' => 'Mengenal Metode Bandongan & Sorogan Kitab Kuning di Pesantren', 'url' => '#'],
        ['kategori' => 'Prestasi', 'judul' => 'Alumni Babul Hasanah Berhasil Lanjut Studi ke Universitas Al-Azhar Kairo', 'url' => '#'],
        ['kategori' => 'Fasilitas', 'judul' => 'Pembangunan Gedung Asrama Putra Tahap II Rampung Tepat Waktu', 'url' => '#'],
        ['kategori' => 'Kegiatan', 'judul' => 'Peringatan Maulid Nabi Diisi Dengan Tabligh Akbar Bersama Ulama Tamu', 'url' => '#'],
    ];

    $indeks = [
        ['kategori' => 'Pesantren', 'waktu' => 'Kemarin', 'judul' => 'Pentingnya Menjaga Kedisiplinan Bahasa Di Lingkungan Asrama Pesantren', 'ringkas' => 'Pembiasaan berbahasa Arab dan Inggris harian terbukti efektif meningkatkan kepercayaan diri santri.', 'url' => '#'],
        ['kategori' => 'Akademik', 'waktu' => '23 Agustus 2026', 'judul' => 'Evaluasi Ujian Semester Ganjil Kitab Kuning Tingkat MTs & MA', 'ringkas' => 'Proses penilaian munaqasyah hafalan dan pemahaman teks klasik berjalan dengan lancar.', 'url' => '#'],
        ['kategori' => 'Ekstrakurikuler', 'waktu' => '20 Agustus 2026', 'judul' => 'Tim Hadroh Babul Hasanah Tampil Memukau di Acara Desa Manggis', 'ringkas' => 'Penampilan seni Islami tradisional ini mendapat apresiasi hangat dari warga sekitar.', 'url' => '#'],
    ];

    $tanggal = now()->locale('id')->translatedFormat('l, d F Y');
@endphp

<style>
    /* ===== Halaman Berita (memakai variabel warna dari layouts/app) ===== */
    .nw-hero { background: var(--cream); padding: 56px 0 64px; border-radius: 0 0 36px 36px; }
    .nw-eyebrow { font-size: 11px; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: var(--gold); }
    .nw-title { font-size: clamp(28px, 4vw, 40px); font-weight: 700; color: var(--green); line-height: 1.2; margin-top: 8px; }
    .nw-sub { font-size: 14px; color: var(--muted); max-width: 560px; margin-top: 10px; line-height: 1.7; }

    .nw-ticker {
        display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap;
        background: #fff; border: 1px solid var(--line); border-radius: 18px; padding: 12px 18px;
        box-shadow: 0 8px 24px rgba(23, 64, 47, .06); margin-top: 28px; font-size: 12.5px;
    }
    .nw-ticker-left { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .nw-pill {
        background: var(--emerald); color: #fff; font-size: 10px; font-weight: 600; letter-spacing: .08em;
        text-transform: uppercase; padding: 4px 10px; border-radius: 999px; flex: none;
    }
    .nw-ticker-text { color: var(--ink); }
    .nw-ticker-text b { color: var(--emerald-d); font-weight: 600; margin-right: 6px; }
    .nw-date { color: var(--muted); display: inline-flex; align-items: center; gap: 8px; white-space: nowrap; }
    .nw-date i { color: var(--emerald-d); }

    .nw-body { padding: 48px 0 72px; }
    .nw-grid { display: grid; grid-template-columns: minmax(0, 1fr) 340px; gap: 28px; align-items: start; }
    .nw-main { display: grid; grid-template-columns: 1fr 1fr; gap: 22px; }

    .nw-card {
        background: #fff; border: 1px solid var(--line); border-radius: 22px; overflow: hidden;
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .nw-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }
    .nw-card:hover .nw-img img { transform: scale(1.05); }
    .nw-img { overflow: hidden; background: var(--cream); }
    .nw-img img { width: 100%; height: 100%; object-fit: cover; transition: transform .5s ease; display: block; }

    .nw-cat { font-size: 10.5px; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--gold); }
    .nw-meta { font-size: 11.5px; color: var(--muted); }
    .nw-h { color: var(--green); font-weight: 600; line-height: 1.4; transition: color .2s ease; }
    .nw-card:hover .nw-h, .nw-pop:hover .nw-h { color: var(--emerald-d); }
    .nw-p { font-size: 13px; color: var(--muted); line-height: 1.7; }
    .clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }

    /* Headline utama */
    .nw-feature { grid-column: 1 / -1; display: grid; grid-template-columns: 1fr 1fr; }
    .nw-feature .nw-img { position: relative; min-height: 280px; }
    .nw-badge {
        position: absolute; top: 16px; left: 16px; background: var(--green); color: #fff; font-size: 10px;
        font-weight: 600; letter-spacing: .08em; text-transform: uppercase; padding: 5px 12px; border-radius: 999px;
    }
    .nw-feature-body { padding: 28px; display: flex; flex-direction: column; justify-content: space-between; gap: 18px; }
    .nw-feature .nw-h { font-size: 22px; margin: 10px 0; }
    .nw-more { font-size: 13px; font-weight: 600; color: var(--emerald-d); display: inline-flex; align-items: center; gap: 8px; }
    .nw-more i { font-size: 10px; transition: transform .2s ease; }
    .nw-card:hover .nw-more i { transform: translateX(4px); }

    /* Sub headline */
    .nw-item-body { padding: 18px 20px 20px; display: flex; flex-direction: column; gap: 8px; }
    .nw-item .nw-img { height: 150px; }
    .nw-item .nw-h { font-size: 15px; }
    .nw-foot { display: flex; justify-content: space-between; gap: 10px; padding-top: 12px; margin-top: 4px; border-top: 1px solid var(--line); }

    /* Sidebar */
    .nw-side { display: flex; flex-direction: column; gap: 22px; position: sticky; top: 100px; }
    .nw-box { background: #fff; border: 1px solid var(--line); border-radius: 22px; padding: 22px; }
    .nw-box-title { display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 600; color: var(--green); padding-bottom: 14px; border-bottom: 1px solid var(--line); margin-bottom: 6px; }
    .nw-box-title::before { content: ""; width: 4px; height: 18px; border-radius: 4px; background: var(--emerald); }
    .nw-pop { display: flex; gap: 14px; padding: 14px 0; border-bottom: 1px solid var(--line); }
    .nw-pop:last-child { border-bottom: 0; padding-bottom: 0; }
    .nw-num { font-size: 28px; font-weight: 700; line-height: 1; color: var(--line); transition: color .2s ease; min-width: 26px; }
    .nw-pop:hover .nw-num { color: var(--emerald); }
    .nw-pop .nw-h { font-size: 13px; margin-top: 4px; }

    .nw-cta { background: var(--green); color: #cfe0d6; border-radius: 28px; padding: 26px; box-shadow: var(--shadow-lg); }
    .nw-cta .nw-pill { display: inline-block; background: rgba(255, 255, 255, .12); color: var(--emerald-l); }
    .nw-cta h4 { color: #fff; font-size: 18px; font-weight: 600; line-height: 1.35; margin: 14px 0 8px; }
    .nw-cta p { font-size: 12.5px; line-height: 1.7; margin-bottom: 18px; }
    .nw-cta .site-btn { width: 100%; }

    /* Indeks */
    .nw-sec-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin: 56px 0 24px; }
    .nw-sec-title { font-size: 22px; font-weight: 600; color: var(--green); margin-top: 4px; }
    .nw-link { font-size: 13px; font-weight: 600; color: var(--emerald-d); transition: color .2s ease; }
    .nw-link:hover { color: var(--green); }
    .nw-index { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; }
    .nw-index .nw-img { height: 170px; }
    .nw-index .nw-h { font-size: 15px; }

    @media (max-width: 1024px) {
        .nw-grid { grid-template-columns: 1fr; }
        .nw-side { position: static; }
        .nw-index { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 720px) {
        .nw-hero { padding: 40px 0 48px; border-radius: 0 0 28px 28px; }
        .nw-body { padding: 36px 0 56px; }
        .nw-main, .nw-feature, .nw-index { grid-template-columns: 1fr; }
        .nw-feature .nw-img { min-height: 220px; }
        .nw-feature-body { padding: 22px; }
        .nw-feature .nw-h { font-size: 19px; }
    }
</style>

{{-- ===================== HERO / JUDUL HALAMAN ===================== --}}
<section class="nw-hero">
    <div class="site-container">
        <span class="nw-eyebrow">Kabar Pesantren</span>
        <h1 class="nw-title">Berita &amp; Informasi</h1>
        <p class="nw-sub">Ikuti perkembangan kegiatan, prestasi, dan pengumuman terbaru dari Pondok Pesantren Babul Hasanah.</p>

        <div class="nw-ticker">
            <div class="nw-ticker-left">
                <span class="nw-pill">Terbaru</span>
                <span class="nw-ticker-text"><b>Pengumuman:</b>Pendaftaran Santri Baru Tahun Ajaran 2026/2027 Resmi Dibuka!</span>
            </div>
            <span class="nw-date"><i class="fa-regular fa-calendar-days"></i>{{ $tanggal }}</span>
        </div>
    </div>
</section>

{{-- ===================== ISI ===================== --}}
<section class="nw-body">
    <div class="site-container">

        <div class="nw-grid">

            {{-- Headline + sub headline --}}
            <div class="nw-main">

                <a href="{{ $headline['url'] }}" class="nw-card nw-feature">
                    <div class="nw-img">
                        <img src="{{ $gambar }}" alt="{{ $headline['judul'] }}">
                        <span class="nw-badge">Utama</span>
                    </div>
                    <div class="nw-feature-body">
                        <div>
                            <div class="nw-cat">{{ $headline['kategori'] }} <span class="nw-meta">&bull; {{ $headline['waktu'] }}</span></div>
                            <h2 class="nw-h">{{ $headline['judul'] }}</h2>
                            <p class="nw-p clamp-3">{{ $headline['ringkas'] }}</p>
                        </div>
                        <span class="nw-more">Baca Berita <i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>

                @foreach ($sub as $b)
                    <a href="{{ $b['url'] }}" class="nw-card nw-item">
                        <div class="nw-img"><img src="{{ $gambar }}" alt="{{ $b['judul'] }}"></div>
                        <div class="nw-item-body">
                            <span class="nw-cat">{{ $b['kategori'] }}</span>
                            <h3 class="nw-h clamp-2">{{ $b['judul'] }}</h3>
                            <div class="nw-foot nw-meta">
                                <span>{{ $b['waktu'] }}</span>
                                <span>Oleh {{ $b['penulis'] }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach

            </div>

            {{-- Sidebar --}}
            <aside class="nw-side">
                <div class="nw-box">
                    <div class="nw-box-title">Terpopuler</div>
                    @foreach ($populer as $i => $b)
                        <a href="{{ $b['url'] }}" class="nw-pop">
                            <span class="nw-num">{{ $i + 1 }}</span>
                            <div>
                                <span class="nw-cat">{{ $b['kategori'] }}</span>
                                <h4 class="nw-h">{{ $b['judul'] }}</h4>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="nw-cta">
                    <span class="nw-pill">PPDB 2026</span>
                    <h4>Ingin Mendaftarkan Putra-Putri Anda?</h4>
                    <p>Dapatkan informasi lengkap mengenai syarat pendaftaran, biaya, dan fasilitas asrama.</p>
                    <a href="{{ route('pendaftaran.index') }}" class="site-btn">Daftar Online Sekarang</a>
                </div>
            </aside>

        </div>

        {{-- Indeks berita --}}
        <div class="nw-sec-head">
            <div>
                <span class="nw-eyebrow">Arsip</span>
                <h2 class="nw-sec-title">Indeks Berita Terbaru</h2>
            </div>
            <a href="#" class="nw-link">Lihat Semua Berita <i class="fa-solid fa-arrow-right" style="font-size:11px;margin-left:4px"></i></a>
        </div>

        <div class="nw-index">
            @foreach ($indeks as $b)
                <a href="{{ $b['url'] }}" class="nw-card">
                    <div class="nw-img"><img src="{{ $gambar }}" alt="{{ $b['judul'] }}"></div>
                    <div class="nw-item-body">
                        <div class="nw-cat">{{ $b['kategori'] }} <span class="nw-meta">&bull; {{ $b['waktu'] }}</span></div>
                        <h3 class="nw-h clamp-2">{{ $b['judul'] }}</h3>
                        <p class="nw-p clamp-2">{{ $b['ringkas'] }}</p>
                    </div>
                </a>
            @endforeach
        </div>

    </div>
</section>

@endsection