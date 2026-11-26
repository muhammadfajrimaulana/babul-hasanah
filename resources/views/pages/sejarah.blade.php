@extends('layouts.app')

@section('title', 'Sejarah - Pondok Pesantren Babul Hasanah')

@section('content')

@include('pages.partials.profil-dokumen-style')

<style>
    /* ====== Halaman sejarah: lebar penuh kanan-kiri ====== */
    .pd-wide .pd-wrap { width: 100%; max-width: none; padding-inline: clamp(18px, 4vw, 64px); }
    .pd-wide .pd-doc { max-width: none; margin: 0; }
    .pd-wide .pd-section[id] { scroll-margin-top: 100px; }

    /* Hero rata kiri */
    .pd-wide .pd-hero-inner { max-width: 1040px; margin-left: 0; margin-right: auto; text-align: left; }
    .pd-wide .pd-crumb { justify-content: flex-start; }
    .pd-wide .pd-hero h1 { margin-left: 0; margin-right: auto; }
    .pd-wide .pd-hero p { margin-left: 0; margin-right: auto; }
    .pd-wide .pd-nav { justify-content: flex-start; }

    /* ====== Area dokumen ====== */
    .pd-doc-area { padding: 56px 0 72px; background: linear-gradient(180deg, #f7f5ef 0%, #fbfaf6 40%, #f7f5ef 100%); }
    .pd-doc-layout { display: grid; grid-template-columns: 320px minmax(0, 1fr); gap: 30px; align-items: start; }

    /* ====== Daftar isi ====== */
    .pd-toc { position: sticky; top: 96px; display: flex; flex-direction: column; gap: 14px; }
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

    /* ====== Pendaftaran (kartu hijau terpusat) ====== */
    .pd-daftar { padding: 8px 0 84px; background: #f7f5ef; }
    .pd-daftar-card { position: relative; overflow: hidden; max-width: 1180px; margin-inline: auto; padding: clamp(46px, 7vw, 82px) clamp(24px, 5vw, 60px); border-radius: 30px; text-align: center; color: #fff; background: radial-gradient(620px 300px at 85% 0%, rgba(255, 255, 255, .10), transparent 65%), linear-gradient(140deg, #0f5132 0%, #15803d 72%, #1a8a48 100%); box-shadow: 0 34px 70px -46px rgba(15, 81, 50, .85); }
    .pd-daftar-card::before { content: ""; position: absolute; inset: 0; opacity: .35; background-image: radial-gradient(rgba(255, 255, 255, .16) 1px, transparent 1px); background-size: 26px 26px; -webkit-mask-image: radial-gradient(ellipse at 50% 0%, #000, transparent 75%); mask-image: radial-gradient(ellipse at 50% 0%, #000, transparent 75%); }
    .pd-daftar-card > * { position: relative; z-index: 1; }
    .pd-daftar-card h2 { margin: 0 auto 14px; max-width: 26ch; font-size: clamp(1.55rem, 3.6vw, 2.45rem); font-weight: 800; line-height: 1.28; letter-spacing: -.01em; color: #fff; }
    .pd-daftar-card p { margin: 0 auto 30px; max-width: 66ch; font-size: clamp(.9rem, 1.4vw, 1.02rem); line-height: 1.85; color: rgba(255, 255, 255, .85); }
    .pd-daftar-btn { display: inline-flex; align-items: center; gap: 10px; padding: 15px 32px; border-radius: 12px; background: #fff; color: #166534; font-weight: 800; font-size: .95rem; text-decoration: none; box-shadow: 0 14px 30px -18px rgba(0, 0, 0, .45); transition: transform .2s ease, background .2s ease; }
    .pd-daftar-btn i { font-size: .9rem; transition: transform .2s ease; }
    .pd-daftar-btn:hover { background: #fde68a; transform: translateY(-2px); }
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
        .pd-daftar-card { border-radius: 24px; }

        /* Daftar isi versi mobile: 4 kartu judul, mengisi lebar layar */
        .pd-toc-card { display: none; }
        .pd-toc-mobile { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px; padding: 2px 0 4px; }
        .pd-toc-chip { display: flex; flex-direction: column; align-items: center; justify-content: flex-start; gap: 6px; width: auto; min-width: 0; padding: 11px 3px 9px; border-radius: 14px; border: 1px solid #eceae2; background: #fff; box-shadow: 0 10px 22px -20px rgba(23, 64, 47, .55); text-align: center; text-decoration: none; transition: transform .18s ease, box-shadow .18s ease; }
        .pd-toc-chip:hover, .pd-toc-chip:active { transform: translateY(-2px); box-shadow: 0 14px 26px -20px rgba(23, 64, 47, .55); }
        .pd-toc-chip i { font-size: .95rem; }
        .pd-toc-chip span { font-size: .55rem; font-weight: 600; line-height: 1.25; letter-spacing: -.01em; overflow-wrap: break-word; color: #6b7280; }
        .pd-toc-chip:nth-child(1) i { color: #d97706; }
        .pd-toc-chip:nth-child(2) i { color: #16a34a; }
        .pd-toc-chip:nth-child(3) i { color: #2563eb; }
        .pd-toc-chip:nth-child(4) i { color: #0d9488; }
    }
    @media (max-width: 560px) {
        .pd-toc-group ol { grid-template-columns: 1fr; }
    }
</style>

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
                <h2>{{ $daftarTitle }}</h2>
                <p>{{ $daftarDesc }}</p>

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
