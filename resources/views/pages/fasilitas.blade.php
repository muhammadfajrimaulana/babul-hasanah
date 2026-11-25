@extends('layouts.app')

@section('title', 'Fasilitas - Pondok Pesantren Babul Hasanah')

@section('content')

@include('pages.partials.profil-dokumen-style')

@php
    $konten = \App\Models\Konten::allWithDefaults();
    $kv = fn (string $key, string $default = '') => $konten[$key] ?? $default;

    $layout = \App\Models\Konten::layoutFor('fasilitas');
    $visible = [];
    foreach ($layout as $section) {
        $visible[$section['id']] = $section['visible'];
    }

    $heading = $kv('fasilitas_heading', 'Fasilitas Pondok Pesantren Babul Hasanah');
    $desc = $kv('fasilitas_desc', 'Sarana dan prasarana yang mendukung kegiatan belajar, ibadah, dan kehidupan santri di Babul Hasanah.');

    // Cadangan untuk data lama (format JSON).
    $legacy = json_decode((string) $kv('fasilitas_photos', '[]'), true);
    $legacy = is_array($legacy) ? array_values($legacy) : [];

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

    $ikon = ['fa-building-columns', 'fa-bed', 'fa-mosque', 'fa-book-open'];
    $fasilitas = [];

    for ($i = 1; $i <= 4; $i++) {
        $path = (string) $kv("fasilitas_{$i}_foto", '');
        if ($path === '' && isset($legacy[$i - 1])) {
            $path = (string) $legacy[$i - 1];
        }

        $fasilitas[] = [
            'ikon' => $ikon[$i - 1],
            'judul' => $kv("fasilitas_{$i}_title", match ($i) {
                1 => 'Gedung Pembelajaran',
                2 => 'Asrama Santri',
                3 => 'Masjid',
                4 => 'Perpustakaan',
            }),
            'deskripsi' => $kv("fasilitas_{$i}_desc", match ($i) {
                1 => 'Gedung belajar dengan ruang majelis, papan tulis, dan perlengkapan pembelajaran untuk pengajian kitab kuning serta pendidikan formal.',
                2 => 'Asrama putra dan putri dengan kamar tidur, area belajar, dan ruang bersama untuk kegiatan harian santri.',
                3 => 'Masjid utama tempat shalat berjamaah, pengajian, dan kegiatan keagamaan santri bersama masyarakat.',
                4 => 'Perpustakaan dengan koleksi kitab kuning, buku pelajaran, dan referensi ilmu pengetahuan untuk santri.',
            }),
            'foto' => $resolveFoto($path),
        ];
    }
@endphp

<div class="pd-page">

    {{-- ============ HERO ============ --}}
    @if ($visible['hero'] ?? true)
        <header class="pd-hero">
            <div class="pd-wrap">
                <nav class="pd-crumb" aria-label="Breadcrumb">
                    <a href="{{ url('/') }}">Beranda</a>
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

    {{-- ============ 4 KARTU FASILITAS ============ --}}
    @if ($visible['cards'] ?? true)
        <section class="pd-section-wrap">
            <div class="pd-wrap">
                <div class="pd-facilities">
                    @foreach ($fasilitas as $f)
                        <article class="pd-facility pd-rv">
                            <div class="pd-facility-media">
                                @if ($f['foto'])
                                    <img src="{{ $f['foto'] }}" alt="{{ $f['judul'] }}" loading="lazy">
                                @else
                                    <div class="pd-facility-ph">
                                        <i class="fa-solid {{ $f['ikon'] }}"></i>
                                        <span>Foto belum tersedia</span>
                                    </div>
                                @endif
                            </div>
                            <div class="pd-facility-body">
                                <h2>{{ $f['judul'] }}</h2>
                                <p>{{ $f['deskripsi'] }}</p>
                            </div>
                        </article>
                    @endforeach
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
