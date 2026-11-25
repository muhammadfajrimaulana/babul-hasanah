@extends('layouts.app')

@section('title', 'Sejarah - Pondok Pesantren Babul Hasanah')

@section('content')

@include('pages.partials.profil-dokumen-style')

@php
    $konten = \App\Models\Konten::allWithDefaults();
    $kv = fn (string $key, string $default = '') => $konten[$key] ?? $default;

    // Susunan & visibilitas bagian dari dashboard.
    $layout = \App\Models\Konten::layoutFor('sejarah');
    $visible = [];
    $order = [];
    foreach ($layout as $i => $section) {
        $visible[$section['id']] = $section['visible'];
        $order[$section['id']] = $i;
    }

    $eyebrow = $kv('sejarah_doc_eyebrow', 'Profil Pesantren');
    $title = $kv('sejarah_doc_title', 'RIWAYAT PENDIRIAN DAN PERKEMBANGAN PONDOK PESANTREN BABUL HASANAH');
    $intro = $kv('sejarah_doc_intro', 'Menelusuri perjalanan Pondok Pesantren Babul Hasanah sejak masa perintisan hingga tumbuh menjadi lembaga pendidikan Islam yang bersama masyarakat.');
    $tahun = $kv('sejarah_doc_year', '1997');
    $tahunCaption = $kv('sejarah_doc_year_caption', 'Awal perintisan di Padang Lawas');

    $foto = $konten['sejarah_foto'] ?? '';
    $fotoUrl = $foto ? asset('storage/' . preg_replace('#^storage/#', '', $foto)) : null;
    $fotoCaption = $kv('sejarah_foto_caption', 'Komplek Pondok Pesantren Babul Hasanah, tempat pendidikan dan pembinaan santri dari berbagai daerah.');

    $chapters = [
        [
            'id' => 'pendirian',
            'title' => '',
            'sections' => [
                [
                    'title' => $kv('sejarah_bab1_a_title', 'A. Latar Belakang Pendirian'),
                    'body' => $kv('sejarah_bab1_a_body', 'Pondok Pesantren Babul Hasanah lahir dari kepedulian terhadap pendidikan agama di tengah masyarakat Padang Lawas. Pada masa itu, akses pendidikan Islam yang terarah dan berkelanjutan masih terbatas. Anak-anak di sekitar lingkungan pesantren membutuhkan wadah untuk belajar membaca Al-Qur’an, memahami dasar-dasar ibadah, dan menanamkan akhlak mulia. Dari kebutuhan itulah para tokoh masyarakat dan keluarga besar pendiri berinisiatif menghadirkan majelis pendidikan yang sederhana namun berkelanjutan.'),
                ],
                [
                    'title' => $kv('sejarah_bab1_b_title', 'B. Penggagas dan Pendiri Pesantren'),
                    'body' => $kv('sejarah_bab1_b_body', 'Pendirian Babul Hasanah tidak dapat dilepaskan dari peran para ulama, tokoh masyarakat, dan keluarga besar yang memiliki kepedulian terhadap pendidikan Islam. Mereka bersepakat menyiapkan tempat belajar, menghimpun dukungan, dan mengajak masyarakat untuk berpartisipasi. Kebersamaan tersebut menjadi kekuatan awal pesantren. Hingga kini, semangat kebersamaan itu dirawat sebagai bagian dari identitas lembaga.'),
                ],
                [
                    'title' => $kv('sejarah_bab1_c_title', 'C. Alasan dan Tujuan Pendirian'),
                    'body' => $kv('sejarah_bab1_c_body', 'Pendirian pesantren dilatarbelakangi oleh keinginan menyediakan pendidikan Islam yang mudah dijangkau, berakar pada Al-Qur’an dan sunah, serta menghormati tradisi keilmuan ulama. Tujuannya adalah membentuk generasi yang berilmu, berakhlak mulia, mampu mengamalkan ajaran Islam dalam kehidupan sehari-hari, dan memberi manfaat bagi keluarga, masyarakat, serta bangsa.'),
                ],
            ],
        ],
        [
            'id' => 'proses',
            'title' => $kv('sejarah_bab2_title', 'BAB II. PROSES PENDIRIAN PONDOK PESANTREN BABUL HASANAH'),
            'sections' => [
                [
                    'title' => $kv('sejarah_bab2_a_title', 'A. Perintisan pada Tahun 1997'),
                    'body' => $kv('sejarah_bab2_a_body', 'Perintisan dimulai pada tahun 1997 dari kegiatan pengajian sederhana. Keterbatasan sarana tidak menghalangi para perintis untuk memulai. Dari rumah ke rumah, dari majelis ke majelis, kegiatan belajar terus dilanjutkan hingga semakin banyak santri yang bergabung. Tahun 1997 menjadi penanda awal semangat pendidikan yang kemudian menjadi landasan perkembangan pesantren.'),
                ],
                [
                    'title' => $kv('sejarah_bab2_b_title', 'B. Semangat Kebersamaan dan Dukungan Masyarakat'),
                    'body' => $kv('sejarah_bab2_b_body', 'Perkembangan pesantren terjadi karena dukungan masyarakat. Warga bergotong royong menyiapkan tempat, tenaga, dan kebutuhan pendidikan. Dukungan itu tidak hanya berbentuk materi, tetapi juga kepercayaan untuk menitipkan anak-anak mereka belajar di pesantren. Semangat kebersamaan menjadi ciri khas yang terus dijaga.'),
                ],
                [
                    'title' => $kv('sejarah_bab2_c_title', 'C. Makna Nama Babul Hasanah'),
                    'body' => $kv('sejarah_bab2_c_body', 'Nama Babul Hasanah berarti pintu kebaikan. Nama ini mengandung harapan agar setiap santri yang belajar di pesantren keluar sebagai pribadi yang membawa kebaikan bagi keluarga dan masyarakat. Makna tersebut menjadi pengingat bahwa pendidikan di pesantren bukan hanya soal kecakapan intelektual, tetapi juga pembentukan akhlak dan pengabdian.'),
                ],
            ],
        ],
        [
            'id' => 'kepemimpinan',
            'title' => $kv('sejarah_bab3_title', 'BAB III. KEPEMIMPINAN DAN PERKEMBANGAN PESANTREN'),
            'sections' => [
                [
                    'title' => $kv('sejarah_bab3_a_title', 'A. Kepemimpinan KH. Mardin Hasibuan Asshiddiqy, M.M.Pd.'),
                    'body' => $kv('sejarah_bab3_a_body', 'Kepemimpinan KH. Mardin Hasibuan Asshiddiqy, M.M.Pd. menjadi bagian penting dalam perjalanan Babul Hasanah. Beliau memimpin dengan menekankan pentingnya ilmu, adab, dan pengabdian. Arahan beliau menjadi landasan dalam menata pendidikan, membina santri, dan menjaga hubungan baik dengan masyarakat. Kepemimpinan tersebut dijalankan bersama para asatidz, pengurus, dan keluarga besar pesantren.'),
                ],
                [
                    'title' => $kv('sejarah_bab3_b_title', 'B. Pengembangan Pendidikan dan Tradisi Keilmuan'),
                    'body' => $kv('sejarah_bab3_b_body', 'Pesantren terus mengembangkan pendidikan dengan memadukan kajian kitab kuning dan pendidikan formal. Tradisi keilmuan dijaga melalui pengajian kitab, sorogan, bandongan, dan pembinaan hafalan. Pengembangan dilakukan secara bertahap agar tradisi salaf tetap hidup sekaligus santri memiliki bekal menghadapi perkembangan zaman.'),
                ],
                [
                    'title' => $kv('sejarah_bab3_c_title', 'C. Sarana Pendidikan, Kehidupan Keagamaan, dan Kegiatan Santri'),
                    'body' => $kv('sejarah_bab3_c_body', 'Seiring waktu, sarana pendidikan dilengkapi untuk mendukung pembelajaran dan kehidupan santri. Kegiatan keagamaan seperti shalat berjamaah, pengajian, dan peringatan hari besar Islam menjadi bagian dari keseharian. Santri juga dibina melalui kegiatan ekstrakurikuler, latihan pidato, seni Islam, dan kegiatan sosial agar tumbuh mandiri serta peduli terhadap lingkungan.'),
                ],
            ],
        ],
    ];

    // Ikuti urutan dari pengaturan lanjutan admin.
    usort($chapters, fn ($a, $b) => ($order[$a['id']] ?? 99) <=> ($order[$b['id']] ?? 99));
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
                    <span>Sejarah</span>
                </nav>

                <div class="pd-hero-inner">
                    <div class="pd-eyebrow light">{{ $eyebrow }}</div>
                    <h1>{{ $title }}</h1>
                    <p>{{ $intro }}</p>

                    <div class="pd-hero-meta">
                        <b>{{ $tahun }}</b>
                        <span>{{ $tahunCaption }}</span>
                    </div>

                    <nav class="pd-nav" aria-label="Isi halaman">
                        <a href="#pendirian"><i class="fa-solid fa-landmark"></i> BAB I</a>
                        <a href="#proses"><i class="fa-solid fa-seedling"></i> BAB II</a>
                        <a href="#kepemimpinan"><i class="fa-solid fa-people-group"></i> BAB III</a>
                    </nav>
                </div>
            </div>
        </header>
    @endif

    {{-- ============ ISI DOKUMEN ============ --}}
    <section class="pd-section-wrap">
        <div class="pd-wrap">
            <article class="pd-doc">

                {{-- Gambar di tengah + keterangan di bawah --}}
                @if ($fotoUrl)
                    <figure class="pd-doc-figure">
                        <img src="{{ $fotoUrl }}" alt="Foto Pondok Pesantren Babul Hasanah" loading="lazy">
                        <figcaption>{{ $fotoCaption }}</figcaption>
                    </figure>
                @else
                    <div class="pd-doc-placeholder" style="margin-bottom: 18px;">
                        <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Logo Babul Hasanah">
                        <span>Foto pesantren belum ditambahkan. Admin dapat mengunggahnya dari menu
                            Pengelolaan Konten.</span>
                    </div>
                @endif

                @foreach ($chapters as $chapter)
                    @if ($visible[$chapter['id']] ?? true)
                        <section class="pd-chapter pd-rv" id="{{ $chapter['id'] }}">
                            @if (! empty($chapter['title']))
                                <h2 class="pd-chapter-title">{{ $chapter['title'] }}</h2>
                            @endif

                            @foreach ($chapter['sections'] as $section)
                                <div class="pd-section">
                                    <h3 class="pd-section-title">{{ $section['title'] }}</h3>
                                    <div class="pd-prose">{!! nl2br(e($section['body'])) !!}</div>
                                </div>
                            @endforeach
                        </section>
                    @endif
                @endforeach

            </article>
        </div>
    </section>
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
