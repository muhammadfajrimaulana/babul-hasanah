@extends('layouts.app')

@section('title', 'Visi & Misi - Pondok Pesantren Babul Hasanah')

@section('content')

@include('pages.partials.profil-dokumen-style')

@php
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

    $layout = \App\Models\Konten::layoutFor('visi');
    $visible = [];
    foreach ($layout as $section) {
        $visible[$section['id']] = $section['visible'];
    }

    $heroEyebrow = $kv('visi_page_hero_eyebrow', 'Pondok Pesantren Babul Hasanah');
    $heroTitle = $kv('visi_page_hero_title', 'Cita-cita yang jelas,');
    $heroAccent = $kv('visi_page_hero_title_accent', 'langkah yang nyata.');
    $heroDesc = $kv('visi_page_hero_desc', 'Landasan kami dalam membangun generasi unggul, berilmu, dan berakhlak mulia: dari visi, misi, hingga nilai yang kami tanamkan setiap hari.');

    $visi = $kv('visi_page_visi', 'Membangun Generasi Unggul dalam Penguasaan Kitab Kuning, Berakhlak Mulia, dan Berwawasan Luas.');

    $misiHeading = $kv('visi_page_misi_heading', 'Misi pondok pesantren');
    $misiDesc = $kv('visi_page_misi_desc', 'Untuk mewujudkan visi tersebut, kami menjalankan lima misi berikut dengan penuh komitmen.');
    $misi = [];
    foreach ($baris('visi_page_misi', "Pendalaman Ilmu Agama|Menyelenggarakan pendidikan kitab kuning secara berjenjang dan sistematis dengan sanad keilmuan yang jelas.\nPembinaan Akhlak & Karakter|Menanamkan adab, kedisiplinan, kemandirian, dan keteladanan dalam kehidupan santri sehari-hari.\nPenguatan Wawasan & Keterampilan|Membekali santri dengan wawasan keilmuan, kemampuan berbahasa, dan keterampilan hidup yang relevan dengan zaman.\nLingkungan Pesantren yang Kondusif|Menciptakan suasana belajar yang aman, nyaman, dan islami untuk mendukung tumbuh kembang santri.\nPengabdian kepada Masyarakat|Mengembangkan dakwah dan kemaslahatan umat melalui kegiatan sosial, keagamaan, dan pendidikan.") as $row) {
        $misi[] = [$row[0], $row[1]];
    }

    $nilaiHeading = $kv('visi_page_nilai_heading', 'Nilai-nilai utama');
    $nilaiDesc = $kv('visi_page_nilai_desc', 'Empat pegangan yang kami tanamkan dalam keseharian santri.');
    $nilaiIkon = ['fa-book-quran', 'fa-heart', 'fa-hands-holding-circle', 'fa-people-group'];
    $nilai = [];
    foreach ($baris('visi_page_nilai', "Religius|Berpegang teguh pada Al-Qur’an, Sunnah, dan tradisi ulama salaf.\nIkhlas|Belajar dan mengabdi semata-mata mengharap ridha Allah.\nMandiri|Melatih santri hidup sederhana, disiplin, dan bertanggung jawab.\nUkhuwah|Menjalin persaudaraan yang erat antarsantri dan masyarakat.") as $i => $row) {
        $nilai[] = [$nilaiIkon[$i] ?? 'fa-circle', $row[0], $row[1]];
    }

    $bab5Title = $kv('visi_page_bab5_title', 'BAB V. VISI, HARAPAN, DAN KEBERLANJUTAN');
    $bab5Body = $kv('visi_page_bab5_body', "Pondok Pesantren Babul Hasanah diharapkan terus berkembang sebagai lembaga pendidikan Islam yang berpegang teguh pada nilai-nilai Al-Qur’an dan sunah, menghormati tradisi keilmuan ulama, serta terbuka terhadap perkembangan pendidikan yang membawa manfaat.\n\nSemangat pendirian pada tahun 1997 perlu menjadi landasan untuk melanjutkan perjuangan pendidikan bagi generasi berikutnya. Pengembangan lembaga hendaknya memperhatikan peningkatan kualitas pembelajaran, kompetensi guru, pembinaan karakter santri, penguatan kajian kitab kuning, penyediaan sarana pendidikan, serta pemanfaatan teknologi secara bertanggung jawab.\n\nPengembangan teknologi tidak harus menghilangkan tradisi pesantren. Sebaliknya, teknologi dapat dimanfaatkan untuk memperluas akses terhadap sumber belajar, mengelola administrasi, mendokumentasikan karya dan kegiatan santri, serta menjaga arsip sejarah lembaga. Pemanfaatannya tetap perlu disertai adab, etika, dan bimbingan guru.\n\nHarapan tersebut sejalan dengan cita-cita membangun generasi yang tidak hanya memiliki kecakapan intelektual, tetapi juga kematangan spiritual, kemuliaan akhlak, dan kepedulian sosial.");

    $mottoLabel = $kv('visi_page_motto_label', 'Motto Pesantren');
    $motto = $kv('visi_page_motto', '“Berilmu, Beramal,');
    $mottoAccent = $kv('visi_page_motto_accent', 'Berakhlak Mulia”');
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
                    <span>Visi &amp; Misi</span>
                </nav>

                <div class="pd-hero-inner">
                    <div class="pd-eyebrow light">{{ $heroEyebrow }}</div>
                    <h1>{{ $heroTitle }} <span>{{ $heroAccent }}</span></h1>
                    <p>{{ $heroDesc }}</p>

                    <nav class="pd-nav" aria-label="Isi halaman">
                        <a href="#visi"><i class="fa-solid fa-eye"></i> Visi</a>
                        <a href="#misi"><i class="fa-solid fa-bullseye"></i> Misi</a>
                        <a href="#nilai"><i class="fa-solid fa-gem"></i> Nilai</a>
                        <a href="#bab5"><i class="fa-solid fa-seedling"></i> BAB V</a>
                        <a href="#motto"><i class="fa-solid fa-star-and-crescent"></i> Motto</a>
                    </nav>
                </div>
            </div>
        </header>
    @endif

    {{-- ============ VISI ============ --}}
    @if ($visible['visi'] ?? true)
        <section class="pd-section-wrap pd-alt" id="visi">
            <div class="pd-wrap">
                <div class="pd-visi-wrap">
                    <div class="pd-visi-copy">
                        <div class="pd-eyebrow">Visi Pesantren</div>
                        <h2>Arah yang kami tuju bersama</h2>
                        <p>Visi menjadi kompas seluruh kegiatan pendidikan, pembinaan, dan pengabdian di Babul
                            Hasanah.</p>
                    </div>

                    <div class="pd-visi-card pd-rv">
                        <i class="fa-solid fa-quote-left"></i>
                        <p>{{ $visi }}</p>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- ============ MISI ============ --}}
    @if ($visible['misi'] ?? true)
        <section class="pd-section-wrap" id="misi">
            <div class="pd-wrap">
                <div class="pd-head">
                    <div class="pd-eyebrow">Misi Pesantren</div>
                    <h2>{{ $misiHeading }}</h2>
                    <p>{{ $misiDesc }}</p>
                </div>

                <div class="pd-numbered">
                    @foreach ($misi as $m)
                        <article class="pd-numbered-item pd-rv">
                            <span class="num">{{ sprintf('%02d', $loop->iteration) }}</span>
                            <div>
                                <h3>{{ $m[0] }}</h3>
                                <p>{{ $m[1] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ NILAI ============ --}}
    @if ($visible['nilai'] ?? true)
        <section class="pd-section-wrap pd-alt" id="nilai">
            <div class="pd-wrap">
                <div class="pd-head">
                    <div class="pd-eyebrow">Nilai Utama</div>
                    <h2>{{ $nilaiHeading }}</h2>
                    <p>{{ $nilaiDesc }}</p>
                </div>

                <div class="pd-feature-grid">
                    @foreach ($nilai as $n)
                        <div class="pd-feature pd-rv">
                            <div class="ico"><i class="fa-solid {{ $n[0] }}"></i></div>
                            <h3>{{ $n[1] }}</h3>
                            <p>{{ $n[2] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ BAB V ============ --}}
    @if ($visible['bab5'] ?? true)
        <section class="pd-section-wrap" id="bab5">
            <div class="pd-wrap">
                <article class="pd-doc">
                    <div class="pd-chapter">
                        <h2 class="pd-chapter-title">{{ $bab5Title }}</h2>
                        <div class="pd-prose">{!! nl2br(e($bab5Body)) !!}</div>
                    </div>
                </article>
            </div>
        </section>
    @endif

    {{-- ============ MOTTO ============ --}}
    @if ($visible['motto'] ?? true)
        <section class="pd-motto" id="motto">
            <div class="pd-wrap">
                <div class="pd-eyebrow light">{{ $mottoLabel }}</div>
                <h2 class="pd-rv">{{ $motto }} <span>{{ $mottoAccent }}</span></h2>
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
