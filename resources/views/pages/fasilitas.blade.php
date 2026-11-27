@extends('layouts.app')

@section('title', 'Fasilitas - Pondok Pesantren Babul Hasanah')

@section('content')

@include('pages.partials.profil-dokumen-style')

<style>
    /* ====== Selaras dengan halaman Sejarah & Visi-Misi: lebar penuh & rata kiri ====== */
    .pd-wide .pd-wrap { width: 100%; max-width: none; padding-inline: clamp(18px, 4vw, 64px); }
    .pd-wide .pd-hero-inner { max-width: 1040px; margin-left: 0; margin-right: auto; text-align: left; }
    .pd-wide .pd-crumb { justify-content: flex-start; }
    .pd-wide .pd-hero h1 { margin-left: 0; margin-right: auto; }
    .pd-wide .pd-hero p { margin-left: 0; margin-right: auto; }
    .pd-wide section[id] { scroll-margin-top: 100px; }

    /* Ritme bagian seragam dengan halaman profil lain */
    .pd-section-wrap { padding: 64px 0; }

    /* ====== Kepala bagian ====== */
    .pd-fac-top { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 28px; }
    .pd-fac-top .pd-eyebrow { margin-bottom: 10px; }
    .pd-fac-title { margin: 0; font-size: clamp(1.4rem, 2.6vw, 1.95rem); font-weight: 800; line-height: 1.25; letter-spacing: -.015em; color: var(--green); }
    .pd-fac-count { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 999px; border: 1px solid #f0dfb4; background: var(--gold-soft); color: #8a5a12; font-size: .8rem; font-weight: 700; }
    .pd-fac-count i { color: var(--gold); }

    /* ====== Kartu utama (editorial) ====== */
    .pd-fac-hero { display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr); overflow: hidden; margin-bottom: 22px; border: 1px solid var(--line); border-radius: 24px; background: #fff; box-shadow: 0 30px 64px -44px rgba(23, 64, 47, .55); }
    .pd-fac-hero-media { position: relative; min-height: 350px; background: var(--cream); }
    .pd-fac-hero-media img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform .8s ease; }
    .pd-fac-hero:hover .pd-fac-hero-media img { transform: scale(1.05); }
    .pd-fac-hero-media::after { content: ""; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(23, 64, 47, 0) 45%, rgba(23, 64, 47, .45) 100%); }
    .pd-fac-hero-media .pd-facility-ph { position: absolute; inset: 0; }
    .pd-fac-hero-body { display: flex; flex-direction: column; justify-content: center; padding: clamp(26px, 4vw, 46px); }
    .pd-fac-hero-body .pd-eyebrow { margin-bottom: 12px; }
    .pd-fac-hero-body h2 { margin: 0 0 12px; font-size: clamp(1.4rem, 2.6vw, 2rem); font-weight: 800; line-height: 1.25; color: var(--green); }
    .pd-fac-hero-body p { margin: 0; font-size: 1rem; line-height: 1.85; color: var(--muted); }
    .pd-fac-chip { position: absolute; left: 18px; top: 18px; z-index: 2; display: inline-flex; align-items: center; gap: 8px; padding: 7px 14px; border-radius: 999px; background: rgba(255, 255, 255, .94); color: var(--green); font-size: .7rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; box-shadow: 0 10px 24px -14px rgba(0, 0, 0, .5); }
    .pd-fac-chip i { color: var(--gold); }

    /* ====== Grid kartu lainnya ====== */
    .pd-facilities { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 20px; }
    .pd-facility-media { overflow: hidden; }
    .pd-facility-media img { transition: transform .6s ease; }
    .pd-facility:hover .pd-facility-media img { transform: scale(1.07); }
    .pd-facility-media::after { content: ""; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(23, 64, 47, 0) 55%, rgba(23, 64, 47, .26) 100%); }
    .pd-facility-num { position: absolute; left: 14px; top: 14px; z-index: 2; display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 9px; border-radius: 10px; background: rgba(23, 64, 47, .82); color: var(--gold-light); font-size: .78rem; font-weight: 800; letter-spacing: .04em; backdrop-filter: blur(4px); }

    /* ====== Keadaan kosong ====== */
    .pd-fac-empty { padding: 42px 24px; border: 1px dashed var(--line); border-radius: 20px; background: #fff; text-align: center; font-size: .94rem; line-height: 1.7; color: var(--muted); }

    /* ====== Ajakan bawah ====== */
    .pd-fac-cta { position: relative; overflow: hidden; padding: 64px 0; color: #fff; background: linear-gradient(135deg, var(--green) 0%, var(--green-2) 65%, #2a6a4d 100%); }
    .pd-fac-cta::before { content: ""; position: absolute; inset: 0; opacity: .5; background-image: radial-gradient(rgba(255, 255, 255, .12) 1px, transparent 1px); background-size: 22px 22px; -webkit-mask-image: radial-gradient(ellipse at center, #000, transparent 78%); mask-image: radial-gradient(ellipse at center, #000, transparent 78%); }
    .pd-fac-cta .pd-wrap { position: relative; z-index: 1; }
    .pd-fac-cta-inner { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 26px; }
    .pd-fac-cta .pd-eyebrow { color: var(--gold-light); }
    .pd-fac-cta h2 { margin: 6px 0 8px; font-size: clamp(1.35rem, 2.8vw, 2rem); font-weight: 800; line-height: 1.3; color: #fff; }
    .pd-fac-cta p { margin: 0; max-width: 56ch; font-size: .96rem; line-height: 1.8; color: rgba(255, 255, 255, .82); }
    .pd-fac-actions { display: flex; flex-wrap: wrap; gap: 12px; }
    .pd-fac-btn { display: inline-flex; align-items: center; gap: 9px; padding: 12px 22px; border-radius: 12px; font-size: .9rem; font-weight: 700; text-decoration: none; transition: transform .2s ease, background .2s ease, color .2s ease; }
    .pd-fac-btn.primary { background: var(--gold); color: #fff; box-shadow: 0 14px 30px -16px rgba(0, 0, 0, .55); }
    .pd-fac-btn.primary:hover { background: #a06a18; transform: translateY(-2px); }
    .pd-fac-btn.ghost { border: 1px solid rgba(255, 255, 255, .35); background: rgba(255, 255, 255, .08); color: #fff; }
    .pd-fac-btn.ghost:hover { background: #fff; color: var(--green); transform: translateY(-2px); }

    @media (max-width: 980px) {
        .pd-fac-hero { grid-template-columns: 1fr; }
        .pd-fac-hero-media { min-height: 0; aspect-ratio: 16 / 10; }
        .pd-facilities { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 860px) {
        .pd-hero { padding: 32px 0 48px; }
        .pd-section-wrap, .pd-fac-cta { padding: 48px 0; }
    }

    @media (max-width: 600px) {
        .pd-facilities { grid-template-columns: 1fr; }
        .pd-fac-hero-media { aspect-ratio: 4 / 3; }
        .pd-fac-cta-inner { flex-direction: column; align-items: flex-start; }
    }
</style>

@php
    // $konten, $layout, dan $semuaKartu disiapkan di Public\ProfilController@fasilitas.
    $kv = fn (string $key, string $default = '') => $konten[$key] ?? $default;

    $visible = [];
    foreach ($layout as $section) {
        $visible[$section['id']] = $section['visible'];
    }

    $heading = $kv('fasilitas_heading', 'Fasilitas Pondok Pesantren Babul Hasanah');
    $desc = $kv('fasilitas_desc', 'Sarana dan prasarana yang mendukung kegiatan belajar, ibadah, dan kehidupan santri di Babul Hasanah.');

    $resolveFoto = function (string $path): ?string {
        $path = trim($path);
        if ($path === '') {
            return null;
        }

        $relative = preg_replace('#^storage/#', '', $path);
        if (! is_file(public_path('storage/' . $relative))) {
            return null;
        }

        return asset('storage/' . $relative);
    };

    // Ikon bergilir untuk kartu ke-1, ke-2, dst.
    $ikonPool = [
        'fa-building-columns', 'fa-bed', 'fa-mosque', 'fa-book-open',
        'fa-utensils', 'fa-futbol', 'fa-kit-medical', 'fa-water',
        'fa-wifi', 'fa-car', 'fa-tree', 'fa-microphone',
    ];
    $ikonUntuk = fn (int $nomor): string => $ikonPool[max(0, $nomor - 1) % count($ikonPool)];

    // Kartu fasilitas bisa ditambah / dihapus dari dashboard.
    $totalKartu = count($semuaKartu);

    // Kartu pertama tampil sebagai kartu utama.
    $kartuUtama = $totalKartu ? array_shift($semuaKartu) : null;
@endphp

<div class="pd-page pd-wide">

    {{-- ============ HERO ============ --}}
    @if ($visible['hero'] ?? true)
        <header class="pd-hero">
            <div class="pd-wrap">
                <nav class="pd-crumb" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}">Beranda</a>
                    <i class="fa-solid fa-chevron-right"></i>
                    <span>Profil</span>
                    <i class="fa-solid fa-chevron-right"></i>
                    <span>Fasilitas</span>
                </nav>

                <div class="pd-hero-inner">
                    <div class="pd-eyebrow light">Sarana &amp; Prasarana</div>
                    <h1>{{ $heading }}</h1>
                    <p>{{ $desc }}</p>
                </div>
            </div>
        </header>
    @endif

    {{-- ============ KARTU FASILITAS ============ --}}
    @if ($visible['cards'] ?? true)
        <section class="pd-section-wrap">
            <div class="pd-wrap">
                <div class="pd-fac-top">
                    <div>
                        <div class="pd-eyebrow">Fasilitas Pesantren</div>
                        <h2 class="pd-fac-title">Ruang belajar &amp; hidup santri</h2>
                    </div>
                    <div class="pd-fac-count">
                        <i class="fa-solid fa-building-columns"></i> {{ $totalKartu }} fasilitas
                    </div>
                </div>

                @if ($totalKartu === 0)
                    <div class="pd-fac-empty">Belum ada fasilitas yang ditampilkan.</div>
                @else
                    @php $fotoUtama = $resolveFoto((string) $kartuUtama['foto']); @endphp

                    <article class="pd-fac-hero pd-rv">
                        <div class="pd-fac-hero-media">
                            @if ($fotoUtama)
                                <img src="{{ $fotoUtama }}" alt="{{ $kartuUtama['title'] ?: 'Fasilitas utama' }}"
                                    loading="lazy">
                            @else
                                <div class="pd-facility-ph">
                                    <i class="fa-solid {{ $ikonUntuk(1) }}"></i>
                                    <span>Foto belum tersedia</span>
                                </div>
                            @endif
                            <span class="pd-fac-chip"><i class="fa-solid {{ $ikonUntuk(1) }}"></i> Fasilitas
                                utama</span>
                        </div>
                        <div class="pd-fac-hero-body">
                            <div class="pd-eyebrow">Fasilitas Utama</div>
                            <h2>{{ $kartuUtama['title'] ?: 'Fasilitas Pondok' }}</h2>
                            @if ($kartuUtama['desc'] !== '')
                                <p>{{ $kartuUtama['desc'] }}</p>
                            @endif
                        </div>
                    </article>

                    @if (count($semuaKartu) > 0)
                        <div class="pd-facilities">
                            @foreach ($semuaKartu as $kartu)
                                @php
                                    $foto = $resolveFoto((string) $kartu['foto']);
                                    $nomor = $loop->iteration + 1;
                                @endphp
                                <article class="pd-facility pd-rv">
                                    <div class="pd-facility-media">
                                        @if ($foto)
                                            <img src="{{ $foto }}" alt="{{ $kartu['title'] ?: 'Fasilitas '.$nomor }}"
                                                loading="lazy">
                                        @else
                                            <div class="pd-facility-ph">
                                                <i class="fa-solid {{ $ikonUntuk($nomor) }}"></i>
                                                <span>Foto belum tersedia</span>
                                            </div>
                                        @endif
                                        <span class="pd-facility-num">{{ sprintf('%02d', $nomor) }}</span>
                                    </div>
                                    <div class="pd-facility-body">
                                        <h2>{{ $kartu['title'] ?: 'Fasilitas '.$nomor }}</h2>
                                        @if ($kartu['desc'] !== '')
                                            <p>{{ $kartu['desc'] }}</p>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>
        </section>

        {{-- ============ AJAKAN ============ --}}
        <section class="pd-fac-cta">
            <div class="pd-wrap">
                <div class="pd-fac-cta-inner">
                    <div>
                        <div class="pd-eyebrow light">Berkunjung</div>
                        <h2>Ingin melihat fasilitas kami langsung?</h2>
                        <p>Silakan datang atau hubungi kami untuk informasi kunjungan, kerja sama, dan pendaftaran santri
                            baru.</p>
                    </div>
                    <div class="pd-fac-actions">
                        <a href="{{ route('kontak') }}" class="pd-fac-btn ghost"><i
                                class="fa-solid fa-location-dot"></i> Hubungi kami</a>
                        <a href="{{ route('pendaftaran.index') }}" class="pd-fac-btn primary">Info pendaftaran <i
                                class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </section>
    @endif
</div>

<script>
    (function () {
        var els = document.querySelectorAll('.pd-rv');
        if (!('IntersectionObserver' in window)) {
            els.forEach(function (el) { el.classList.add('in'); });
            return;
        }
        var obs = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (en.isIntersecting) { en.target.classList.add('in'); obs.unobserve(en.target); }
            });
        }, { threshold: 0.1 });
        els.forEach(function (el) { obs.observe(el); });
    })();
</script>
@endsection
