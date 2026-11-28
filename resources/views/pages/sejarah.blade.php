@extends('layouts.app')

@section('title', 'Sejarah - Pondok Pesantren Babul Hasanah')

@section('content')

@include('pages.partials.profil-dokumen-style')

<style>
    /* ====== Container, font, dan judul mengikuti halaman alumni/jenjang ====== */
    .pd-wide { font-family: 'Poppins', system-ui, sans-serif; font-size: 15px; line-height: 1.6; }
    /* Lebar konten: maksimal 1200px dengan margin 5% tiap sisi (acuan baru semua halaman). */
    .pd-wide .pd-wrap { width: min(1200px, 90%); margin-inline: auto; padding-inline: 0; }
    .pd-wide .pd-doc { max-width: none; margin: 0; }
    .pd-wide .pd-section[id] { scroll-margin-top: 90px; }

    /* Judul hero rata kiri, sejajar dengan tepi konten halaman lain */
    .pd-wide .pd-hero-inner { max-width: 680px; margin-left: 0; margin-right: auto; text-align: left; }
    .pd-wide .pd-crumb { justify-content: flex-start; }
    .pd-wide .pd-hero h1 { margin-left: 0; margin-right: auto; }
    .pd-wide .pd-hero p { margin-left: 0; margin-right: auto; }
    .pd-wide .pd-nav { justify-content: flex-start; }

    /* ====== Area dokumen ====== */
    .pd-doc-area { padding: 56px 0 72px; background: linear-gradient(180deg, var(--cream) 0%, var(--paper) 40%, var(--cream) 100%); }
    .pd-doc-layout { display: grid; grid-template-columns: 320px minmax(0, 1fr); gap: 22px; align-items: start; }

    /* ====== Daftar isi ====== */
    .pd-toc { position: sticky; top: 96px; display: flex; flex-direction: column; gap: 12px; }
    .pd-toc-card { padding: 22px; border-radius: 18px; border: 1px solid var(--line); background: #fff; box-shadow: 0 18px 40px -34px rgba(23, 64, 47, .4); }
    .pd-toc-card > h2, .pd-toc-title { margin: 0 0 14px; font-size: .78rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; color: var(--gold); }
    .pd-toc-mobile { display: none; }
    .pd-toc-group + .pd-toc-group { margin-top: 16px; padding-top: 14px; border-top: 1px dashed var(--line); }
    .pd-toc-chapter { display: flex; align-items: center; gap: 8px; font-size: .86rem; font-weight: 700; line-height: 1.4; color: var(--green); text-decoration: none; }
    .pd-toc-chapter:hover { color: var(--gold); }
    .pd-toc-bab { flex: 0 0 auto; display: inline-flex; padding: 3px 8px; border-radius: 6px; background: var(--gold-soft); color: var(--gold); font-size: .64rem; font-weight: 800; letter-spacing: .06em; }
    .pd-toc-group ol { list-style: none; margin: 8px 0 0; padding: 0; display: flex; flex-direction: column; gap: 1px; }
    .pd-toc-group ol a { display: block; padding: 5px 8px; border-radius: 7px; font-size: .78rem; line-height: 1.45; color: var(--muted); text-decoration: none; transition: background .18s ease, color .18s ease; }
    .pd-toc-group ol a:hover { background: var(--cream); color: var(--green); }
    .pd-toc-note { padding: 18px 20px; border-radius: 16px; color: #fff; background: linear-gradient(135deg, var(--green), var(--green-3)); }
    .pd-toc-note i { color: var(--gold-light); font-size: 1rem; }
    .pd-toc-note p { margin: 8px 0 0; font-size: .8rem; line-height: 1.7; color: rgba(255, 255, 255, .82); }

    /* ====== Dokumen ====== */
    .pd-wide .pd-doc { padding: clamp(24px, 3vw, 52px); }
    .pd-wide .pd-doc-figure { margin-bottom: 26px; }
    .pd-wide .pd-doc-figure img { max-width: min(100%, 860px); max-height: 470px; border-radius: 18px; }
    .pd-wide .pd-doc-figure figcaption { font-size: .85rem; }
    .pd-wide .pd-section { margin-bottom: 30px; }
    .pd-wide .pd-section-title { font-size: 1.08rem; }
    .pd-wide .pd-prose { font-size: 1.02rem; line-height: 1.95; }

    /* ====== CTA: kotak ajakan dua kolom + pill krem seperti alumni/jenjang ====== */
    .pd-daftar { padding: 8px 0 84px; background: var(--cream); }
    .pd-daftar-card { position: relative; overflow: hidden; max-width: none; margin-inline: auto; padding: 50px; border-radius: 32px; display: flex; justify-content: space-between; align-items: center; gap: 30px; flex-wrap: wrap; text-align: left; color: #fff; background: radial-gradient(620px 300px at 85% 0%, rgba(255, 255, 255, .10), transparent 65%), linear-gradient(135deg, var(--green), var(--green-2)); box-shadow: var(--shadow-lg); }
    .pd-daftar-card::before { content: ""; position: absolute; inset: 0; opacity: .35; background-image: radial-gradient(rgba(255, 255, 255, .16) 1px, transparent 1px); background-size: 26px 26px; -webkit-mask-image: radial-gradient(ellipse at 50% 0%, #000, transparent 75%); mask-image: radial-gradient(ellipse at 50% 0%, #000, transparent 75%); }
    .pd-daftar-card > * { position: relative; z-index: 1; }
    .pd-daftar-card h2 { margin: 0 0 8px; max-width: 26ch; font-size: clamp(22px, 3vw, 32px); font-weight: 800; line-height: 1.28; color: #fff; }
    .pd-daftar-card p { margin: 0; max-width: 520px; font-size: 13.5px; line-height: 1.7; color: rgba(255, 255, 255, .82); }
    .pd-daftar-btn { display: inline-flex; align-items: center; gap: 10px; padding: 14px 28px; border-radius: 999px; background: var(--cream); color: var(--green); font-weight: 600; font-size: 13.5px; text-decoration: none; white-space: nowrap; transition: all .25s ease; }
    .pd-daftar-btn i { font-size: .9rem; transition: transform .2s ease; }
    .pd-daftar-btn:hover { background: #fff; color: var(--green); transform: translateY(-2px); box-shadow: 0 10px 22px rgba(0, 0, 0, .18); }
    .pd-daftar-btn:hover i { transform: translateX(3px); }

    /* ====== Responsif ====== */
    @media (max-width: 1100px) {
        .pd-doc-layout { grid-template-columns: 1fr; gap: 20px; }
        .pd-toc { position: static; }
        .pd-toc-group ol { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 860px) {
        .pd-doc-area { padding: 40px 0 52px; }
        .pd-daftar { padding: 4px 0 56px; }
        .pd-daftar-card { padding: 30px 22px; border-radius: 22px; }
        .pd-daftar-btn { width: 100%; justify-content: center; }

        /* Daftar isi versi mobile: 4 kartu judul, mengisi lebar layar */
        .pd-toc-card { display: none; }
        .pd-toc-mobile { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px; padding: 2px 0 4px; }
        .pd-toc-chip { display: flex; flex-direction: column; align-items: center; justify-content: flex-start; gap: 6px; width: auto; min-width: 0; padding: 11px 3px 9px; border-radius: 14px; border: 1px solid var(--line); background: #fff; box-shadow: 0 10px 22px -20px rgba(23, 64, 47, .55); text-align: center; text-decoration: none; transition: transform .18s ease, box-shadow .18s ease; }
        .pd-toc-chip:hover, .pd-toc-chip:active { transform: translateY(-2px); box-shadow: 0 14px 26px -20px rgba(23, 64, 47, .55); }
        .pd-toc-chip i { font-size: .95rem; }
        .pd-toc-chip span { font-size: .55rem; font-weight: 600; line-height: 1.25; letter-spacing: -.01em; overflow-wrap: break-word; color: var(--muted); }
        .pd-toc-chip:nth-child(1) i { color: var(--gold); }
        .pd-toc-chip:nth-child(2) i { color: var(--green-3); }
        .pd-toc-chip:nth-child(3) i { color: var(--green-2); }
        .pd-toc-chip:nth-child(4) i { color: var(--green); }
    }
    @media (max-width: 560px) {
        .pd-toc-group ol { grid-template-columns: 1fr; }
        /* Judul sejarah sangat panjang: kecilkan sampai pas di layar HP. */
        .pd-wide .pd-hero h1 { font-size: 1.45rem; line-height: 1.25; overflow-wrap: anywhere; }
        /* 4 chip terlalu sempit kalau tetap 4 kolom di 320px. */
        .pd-toc-mobile { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .pd-toc-chip { flex-direction: row; justify-content: flex-start; gap: 8px; padding: 12px 10px; text-align: left; }
        .pd-toc-chip span { font-size: .7rem; }
        .pd-wide .pd-doc { padding: 20px 18px; }
        .pd-chapter-title, .pd-section-title, .pd-prose { overflow-wrap: anywhere; }
        .pd-daftar-card h2 { font-size: 20px; }
    }
</style>

@php
    // $konten dan $layout disiapkan di Public\ProfilController@sejarah.
    $kv = fn (string $key, string $default = '') => $konten[$key] ?? $default;
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
    $docNote = str_replace('{tahun}', $tahun, $kv('sejarah_doc_note', 'Sejak {tahun}, Babul Hasanah tumbuh bersama masyarakat sebagai rumah belajar Al-Qur’an, kitab kuning, dan pembinaan akhlak.'));

    $foto = $konten['sejarah_foto'] ?? '';
    $fotoUrl = $foto ? asset('storage/' . preg_replace('#^storage/#', '', $foto)) : null;
    $fotoCaption = $kv('sejarah_foto_caption', 'Komplek Pondok Pesantren Babul Hasanah, tempat pendidikan dan pembinaan santri dari berbagai daerah.');

    $daftarTitle = $kv('sejarah_daftar_title', 'Bergabung bersama Pondok Pesantren Babul Hasanah');
    $daftarDesc = $kv('sejarah_daftar_desc', 'Pondok Pesantren Babul Hasanah membuka pendaftaran santri baru. Sejak perintisan pada tahun 1997, pesantren mendidik santri melalui kajian kitab kuning, pendidikan formal, dan pembinaan akhlak di lingkungan asrama.');
    $daftarButton = $kv('sejarah_daftar_button', 'Daftar Santri Baru');

    $chapters = [
        [
            'id' => 'pendirian',
            'nav' => 'BAB I',
            'short' => 'Riwayat Pendirian',
            'icon' => 'fa-landmark',
            'tab' => 'Pendirian',
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
            'nav' => 'BAB II',
            'short' => 'Proses Pendirian',
            'icon' => 'fa-seedling',
            'tab' => 'Proses',
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
            'nav' => 'BAB III',
            'short' => 'Kepemimpinan & Perkembangan',
            'icon' => 'fa-people-group',
            'tab' => 'Kepemimpinan',
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
                        <a href="#pendaftaran"><i class="fa-solid fa-user-plus"></i> Pendaftaran</a>
                    </nav>
                </div>
            </div>
        </header>
    @endif

    {{-- ============ DOKUMEN SEJARAH ============ --}}
    <section class="pd-doc-area">
        <div class="pd-wrap">
            <div class="pd-doc-layout">

                {{-- Daftar isi --}}
                <aside class="pd-toc">
                    <div class="pd-toc-note">
                        <i class="fa-solid fa-mosque"></i>
                        <p>{{ $docNote }}</p>
                    </div>

                    {{-- Mobile: kartu judul saja, disusun ke samping --}}
                    <nav class="pd-toc-mobile" aria-label="Daftar isi">
                        @foreach ($chapters as $chapter)
                            @if ($visible[$chapter['id']] ?? true)
                                <a href="#{{ $chapter['id'] }}" class="pd-toc-chip">
                                    <i class="fa-solid {{ $chapter['icon'] }}"></i>
                                    <span>{{ $chapter['tab'] }}</span>
                                </a>
                            @endif
                        @endforeach

                        <a href="#pendaftaran" class="pd-toc-chip">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>Pendaftaran</span>
                        </a>
                    </nav>

                    {{-- Desktop: daftar isi lengkap --}}
                    <div class="pd-toc-card">
                        <h2 class="pd-toc-title">Daftar Isi</h2>

                        <div class="pd-toc-body">
                            @foreach ($chapters as $chapter)
                                @if ($visible[$chapter['id']] ?? true)
                                    <div class="pd-toc-group">
                                        <a href="#{{ $chapter['id'] }}" class="pd-toc-chapter">
                                            <span class="pd-toc-bab">{{ $chapter['nav'] }}</span>
                                            {{ $chapter['short'] }}
                                        </a>
                                        <ol>
                                            @foreach ($chapter['sections'] as $section)
                                                <li>
                                                    <a href="#{{ $chapter['id'] }}-{{ $loop->iteration }}">
                                                        {{ $section['title'] }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ol>
                                    </div>
                                @endif
                            @endforeach

                            <div class="pd-toc-group">
                                <a href="#pendaftaran" class="pd-toc-chapter">
                                    <span class="pd-toc-bab"><i class="fa-solid fa-user-plus"></i></span>
                                    Pendaftaran Santri Baru
                                </a>
                            </div>
                        </div>
                    </div>
                </aside>

                {{-- Isi dokumen --}}
                <article class="pd-doc">

                    {{-- Gambar di tengah + keterangan di bawah --}}
                    @if ($fotoUrl)
                        <figure class="pd-doc-figure">
                            <img src="{{ $fotoUrl }}" alt="Foto Pondok Pesantren Babul Hasanah" loading="lazy">
                            <figcaption>{{ $fotoCaption }}</figcaption>
                        </figure>
                    @else
                        <div class="pd-doc-placeholder" style="margin-bottom: 26px;">
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
                                    <div class="pd-section" id="{{ $chapter['id'] }}-{{ $loop->iteration }}">
                                        <h3 class="pd-section-title">{{ $section['title'] }}</h3>
                                        <div class="pd-prose">{!! nl2br(e($section['body'])) !!}</div>
                                    </div>
                                @endforeach
                            </section>
                        @endif
                    @endforeach

                </article>
            </div>
        </div>
    </section>

    {{-- ============ PENDAFTARAN ============ --}}
    <section class="pd-daftar" id="pendaftaran">
        <div class="pd-wrap">
            <div class="pd-daftar-card pd-rv">
                <div>
                    <h2>{{ $daftarTitle }}</h2>
                    <p>{{ $daftarDesc }}</p>
                </div>

                <a href="{{ route('pendaftaran.index') }}" class="pd-daftar-btn">
                    {{ $daftarButton }} <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
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
