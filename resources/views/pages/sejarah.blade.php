@extends('layouts.app')

@section('title', 'Sejarah - Pondok Pesantren Babul Hasanah')

@section('content')

@php
    // Ambil seluruh konten sekali jalan (1 query), lalu pakai fallback teks lama
    // agar tampilan tidak berubah sebelum diedit dari dashboard.
    $konten = \App\Models\Konten::allWithDefaults();

    $kv = fn (string $key, string $default = '') => $konten[$key] ?? $default;

    // Baris daftar berformat "Judul | Keterangan".
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

    // Susunan & visibilitas section dari dashboard.
    $layout = \App\Models\Konten::layoutFor('sejarah');
    $visible = [];
    $order = [];
    foreach ($layout as $i => $section) {
        $visible[$section['id']] = $section['visible'];
        $order[$section['id']] = $i;
    }

    $heroEyebrow = $kv('sejarah_hero_eyebrow', 'Pondok Pesantren Babul Hasanah');
    $heroTitle = $kv('sejarah_hero_title', 'Dari majelis kecil,');
    $heroAccent = $kv('sejarah_hero_title_accent', 'tumbuh bersama umat.');
    $heroDesc = $kv('sejarah_hero_desc', 'Berakar pada adab, bertumbuh dengan ilmu. Kenali sejarah, budaya, dan kajian yang menjadi denyut kehidupan santri Babul Hasanah.');

    $tahun = $kv('sejarah_tahun', '1998');
    $tahunCaption = $kv('sejarah_tahun_caption', 'Awal sebuah ikhtiar di Padang Lawas');
    $fotoSejarah = $konten['sejarah_foto'] ?? null;
    $sejarahHeading = $kv('sejarah_heading', 'Awal berdirinya Pondok Pesantren Babul Hasanah');
    $sejarahP1 = $kv('sejarah_page_p1', 'Babul Hasanah bermula dari majelis pengajian sederhana di Padang Lawas pada 1998. Para perintisnya didorong kepedulian terhadap pendidikan agama anak-anak dan masyarakat sekitar.');
    $sejarahP2 = $kv('sejarah_page_p2', 'Dari majelis kecil itu, pesantren tumbuh menjadi lembaga pendidikan Islam yang memadukan tradisi keilmuan salaf dengan pendidikan yang relevan bagi masa depan. Setiap langkahnya terpusat pada tumbuh kembang santri, agar kelak menjadi insan berilmu, berakhlak mulia, dan berdaya guna.');
    $maknaNama = $kv('sejarah_makna_nama', 'berarti “pintu kebaikan”: harapan agar setiap santri yang masuk lewat pintu ini keluar sebagai pembawa kebaikan bagi keluarga, masyarakat, dan bangsa.');
    $hinggaKini = $kv('sejarah_hinggakini_text', 'Babul Hasanah melayani santri dari jenjang dasar hingga pendidikan tinggi pesantren, dikelola bersama dewan pengasuh dan tim pendidikan.');

    $jenjang = array_values(array_filter(
        array_map('trim', explode(',', $kv('sejarah_jenjang', 'MDTA, PPS Wustha, MTs, MA, PDF Wustha, PDF Ulya, Ma’had ‘Aly'))),
        fn ($v) => $v !== ''
    ));

    $pilarIkon = ['fa-book-quran', 'fa-hand-holding-heart', 'fa-hands-holding-circle'];
    $pilar = [];
    foreach ($baris('sejarah_pilar', "Kitab kuning|Mendalami ilmu agama lewat kajian turats secara bertahap.\nAkhlak mulia|Adab dan keteladanan menjadi dasar setiap proses belajar.\nPengabdian|Ilmu diamalkan untuk keluarga, masyarakat, dan bangsa.") as $i => $row) {
        $pilar[] = ['ikon' => $pilarIkon[$i] ?? 'fa-circle', 'judul' => $row[0], 'isi' => $row[1]];
    }

    $budayaHeading = $kv('sejarah_budaya_heading', 'Satu pesantren, banyak budaya');
    $budayaDesc = $kv('sejarah_budaya_desc', 'Di sini santri belajar bahwa perbedaan asal daerah bukan jarak. Tradisi pesantren dan seni Islam hidup berdampingan dalam keseharian.');

    $budayaIkon = ['fa-people-group', 'fa-drum', 'fa-microphone-lines', 'fa-hands-holding', 'fa-person-running'];
    $budaya = [];
    foreach ($baris('sejarah_budaya', "Santri dari berbagai daerah|Santri datang dari banyak kota dan suku. Bahasa daerah, makanan, dan kebiasaan yang berbeda justru mempererat persaudaraan.\nHadrah dan rebana|Dilatih rutin dan tampil di Maulid Nabi, haflah, serta undangan masyarakat sekitar.\nMuhadharah|Latihan pidato mingguan dalam bahasa Indonesia, Arab, dan Inggris agar santri berani tampil.\nGotong royong dan piket|Membersihkan asrama, masjid, dan dapur dikerjakan bersama. Kemandirian dilatih dari hal kecil.\nPencak silat|Seni bela diri tradisional untuk melatih disiplin, keberanian, dan sopan santun.") as $i => $row) {
        $budaya[] = ['ikon' => $budayaIkon[$i] ?? 'fa-circle', 'judul' => $row[0], 'isi' => $row[1]];
    }

    $kajianHeading = $kv('sejarah_kajian_heading', 'Kajian dan kitab yang dipelajari');
    $kajianDesc = $kv('sejarah_kajian_desc', 'Santri belajar bertahap, dari dasar bahasa Arab sampai tafsir dan tasawuf.');

    $kajian = [];
    $kajianMeta = [
        'harian' => [
            'judul' => 'Setiap hari',
            'catatan' => 'Dasar-dasar yang dipelajari santri setelah shalat Subuh, Ashar, dan Maghrib.',
            'items' => "Al-Qur’an|Tahsin, tajwid, dan setoran hafalan\nNahwu & Shorof|Jurumiyah, Imrithi, hingga Alfiyah Ibnu Malik\nFiqih|Safinatun Najah, Fathul Qarib\nAkhlak|Ta’lim Muta’allim, Washaya al-Aba’ lil Abna’",
        ],
        'mingguan' => [
            'judul' => 'Setiap pekan',
            'catatan' => 'Kajian yang lebih luas, sebagian terbuka untuk masyarakat umum.',
            'items' => "Tafsir|Tafsir Jalalain\nHadits|Riyadhus Shalihin, Bulughul Maram\nTasawuf|Kajian kitab Ihya’ Ulumuddin\nPengajian umum|Malam Jumat bersama pengasuh dan tamu undangan",
        ],
        'tahunan' => [
            'judul' => 'Sepanjang tahun',
            'catatan' => 'Kegiatan khusus pada waktu-waktu tertentu dalam kalender hijriah.',
            'items' => "Pesantren Kilat Ramadhan|Kajian intensif dan khataman kitab\nMaulid Nabi|Pembacaan maulid, hadrah, dan ceramah\nHaflah Akhirussanah|Wisuda santri dan pentas seni\nIsra’ Mi’raj|Pengajian akbar bersama masyarakat",
        ],
    ];

    foreach ($kajianMeta as $key => $meta) {
        $items = [];
        foreach ($baris("sejarah_kajian_{$key}_items", $meta['items']) as $row) {
            $items[] = [$row[0], $row[1]];
        }
        $kajian[$key] = [
            'judul' => $kv("sejarah_kajian_{$key}_judul", $meta['judul']),
            'catatan' => $kv("sejarah_kajian_{$key}_catatan", $meta['catatan']),
            'items' => $items,
        ];
    }

    $motivasiHeading = $kv('sejarah_motivasi_heading', 'Semangat menuntut ilmu');
    $motivasi = [];
    foreach ($baris('sejarah_motivasi', "“Barang siapa menempuh jalan untuk menuntut ilmu, Allah mudahkan baginya jalan menuju surga.”|HR. Muslim\n“Menuntut ilmu itu wajib bagi setiap muslim.”|HR. Ibnu Majah") as $row) {
        $motivasi[] = [$row[0], $row[1]];
    }
@endphp

<style>
    .sj-page { --g900:#0a3a24; --g800:#0f5132; --g700:#13693f; --g600:#15803d; --g500:#10b981; --g50:#f0fdf6;
        --gold:#b7791f; --gold-text:#955f0f; --gold-soft:#fdf3dc; --gold-light:#fde68a; color:#1e293b;
        display: flex; flex-direction: column; }
    .sj-page *, .sj-page *::before, .sj-page *::after { box-sizing: border-box; }
    .sj-wrap { width: 100%; max-width: 1180px; margin: 0 auto; padding: 0 24px; }
    .sj-page section[id] { scroll-margin-top: 80px; }

    .sj-eyebrow { display: inline-flex; align-items: center; gap: 10px; font-size: 12px; letter-spacing: .14em; text-transform: uppercase; font-weight: 700; color: var(--gold-text); margin-bottom: 14px; }
    .sj-eyebrow::before { content: ""; width: 28px; height: 2px; background: var(--gold); }
    .sj-eyebrow.light { color: var(--gold-light); }
    .sj-eyebrow.light::before { background: var(--gold-light); }

    /* ====== Hero (tanpa gambar, pola CSS) ====== */
    .sj-hero { position: relative; overflow: hidden; color: #fff; padding: 56px 0 72px;
        background:
            radial-gradient(600px 300px at 90% 0%, rgba(253,230,138,.18), transparent 65%),
            radial-gradient(500px 300px at 0% 100%, rgba(16,185,129,.28), transparent 65%),
            linear-gradient(135deg, var(--g900), var(--g700)); }
    .sj-hero::before { content: ""; position: absolute; inset: 0; opacity: .5;
        background-image: radial-gradient(rgba(255,255,255,.14) 1.5px, transparent 1.5px); background-size: 26px 26px;
        -webkit-mask-image: linear-gradient(to left, #000, transparent 70%); mask-image: linear-gradient(to left, #000, transparent 70%); }
    .sj-hero .sj-wrap { position: relative; z-index: 1; }
    .sj-crumb { font-size: 14px; color: rgba(255,255,255,.75); margin-bottom: 30px; }
    .sj-crumb a { color: #fff; text-decoration: none; }
    .sj-crumb a:hover { text-decoration: underline; }
    .sj-hero h1 { margin: 0 0 18px; font-size: clamp(2.2rem, 5vw, 3.6rem); line-height: 1.12; color: #fff; max-width: 18ch; }
    .sj-hero h1 span { color: var(--gold-light); }
    .sj-hero p { margin: 0 0 30px; max-width: 58ch; font-size: 1.1rem; line-height: 1.8; color: rgba(255,255,255,.88); }
    .sj-nav { display: flex; flex-wrap: wrap; gap: 10px; }
    .sj-nav a { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 999px; font-weight: 600; font-size: .95rem; text-decoration: none;
        color: #fff; background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.25); transition: all .25s ease; }
    .sj-nav a:hover { background: #fff; color: var(--g800); }
    .sj-nav a:focus-visible { outline: 3px solid var(--gold-light); outline-offset: 2px; }

    /* ====== Section umum ====== */
    .sj-sec { padding: 84px 0; }
    .sj-sec.alt { background: var(--g50); }
    .sj-head { max-width: 680px; margin: 0 0 48px; }
    .sj-head h2 { font-size: clamp(1.7rem, 3.2vw, 2.4rem); line-height: 1.25; margin: 0 0 12px; color: var(--g800); }
    .sj-head p { margin: 0; color: #475569; line-height: 1.75; font-size: 1.05rem; }

    /* ====== 1. Sejarah ====== */
    .sj-story { display: grid; grid-template-columns: 300px 1fr; gap: 64px; align-items: start; }
    .sj-year { position: sticky; top: 100px; }
    .sj-year b { display: block; font-size: clamp(5rem, 11vw, 8rem); line-height: .9; font-weight: 800; letter-spacing: -.04em;
        color: transparent; -webkit-text-stroke: 2px var(--gold); }
    .sj-year span { display: block; margin-top: 14px; padding-top: 14px; border-top: 3px solid var(--gold); font-weight: 700; color: var(--g800); line-height: 1.5; max-width: 220px; }
    .sj-story-body h2 { margin: 0 0 18px; font-size: clamp(1.6rem, 3vw, 2.2rem); line-height: 1.3; color: var(--g800); }
    .sj-story-body p { margin: 0 0 16px; font-size: 1.08rem; line-height: 1.9; color: #334155; }
    .sj-story-body p.first::first-letter { float: left; font-size: 3.6rem; line-height: .85; padding: 6px 12px 0 0; color: var(--gold-text); font-weight: 800; }

    .sj-foto { margin: 26px 0 30px; border-radius: 18px; overflow: hidden; border: 1px solid rgba(6,78,59,.14);
        background: var(--g50); box-shadow: 0 16px 36px rgba(6,78,59,.12); }
    .sj-foto img { display: block; width: 100%; aspect-ratio: 16 / 9; object-fit: cover; }

    .sj-pillars { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin: 34px 0; }
    .sj-pillar { background: var(--g50); border: 1px solid rgba(6,78,59,.12); border-radius: 18px; padding: 20px; }
    .sj-pillar i { width: 42px; height: 42px; border-radius: 12px; background: var(--gold-soft); color: var(--gold-text); display: flex; align-items: center; justify-content: center; margin-bottom: 12px; }
    .sj-pillar h3 { margin: 0 0 4px; font-size: 1rem; color: var(--g800); }
    .sj-pillar p { margin: 0; font-size: .9rem; line-height: 1.6; color: #475569; }

    .sj-name { position: relative; overflow: hidden; background: linear-gradient(135deg, var(--g800), var(--g600)); color: #fff; border-radius: 22px; padding: 28px 30px; }
    .sj-name::after { content: "\f52f"; font-family: "Font Awesome 6 Free"; font-weight: 900; position: absolute; right: -10px; bottom: -26px; font-size: 120px; opacity: .1; }
    .sj-name small { display: block; font-size: 11px; letter-spacing: .14em; text-transform: uppercase; color: var(--gold-light); font-weight: 700; margin-bottom: 6px; }
    .sj-story-body .sj-name p { position: relative; margin: 0; font-size: 1.1rem; line-height: 1.7; color: #fff; }
    .sj-name strong { color: var(--gold-light); }

    .sj-now { margin-top: 30px; }
    .sj-now h3 { margin: 0 0 10px; font-size: 1.1rem; color: var(--g800); }
    .sj-story-body .sj-now p { margin: 0 0 14px; font-size: 1rem; line-height: 1.8; }
    .sj-chips { display: flex; flex-wrap: wrap; gap: 8px; }
    .sj-chips span { background: #fff; border: 1px solid rgba(6,78,59,.2); color: var(--g800); font-size: .88rem; font-weight: 600; padding: 7px 15px; border-radius: 999px; }

    /* ====== 2. Budaya ====== */
    .sj-culture { display: grid; grid-template-columns: repeat(6, 1fr); gap: 20px; }
    .sj-cc { grid-column: span 2; position: relative; background: #fff; border-radius: 22px; padding: 28px 26px 26px; border: 1px solid rgba(6,78,59,.1);
        box-shadow: 0 10px 28px rgba(6,78,59,.07); overflow: hidden; transition: transform .3s ease, box-shadow .3s ease; }
    .sj-cc:nth-child(n+4) { grid-column: span 3; }
    .sj-cc:hover { transform: translateY(-5px); box-shadow: 0 18px 40px rgba(6,78,59,.14); }
    .sj-cc .no { position: absolute; right: 18px; top: 6px; font-size: 4rem; font-weight: 800; color: rgba(183,121,31,.12); line-height: 1; }
    .sj-cc .ico { width: 54px; height: 54px; border-radius: 16px; display: flex; align-items: center; justify-content: center; background: var(--gold-soft); color: var(--gold-text); font-size: 20px; margin-bottom: 18px; }
    .sj-cc h3 { margin: 0 0 8px; font-size: 1.2rem; color: var(--g800); }
    .sj-cc p { margin: 0; color: #475569; line-height: 1.75; }

    /* ====== 3. Kajian (tab) ====== */
    .sj-tabs { display: inline-flex; gap: 6px; flex-wrap: wrap; padding: 6px; background: var(--g50); border: 1px solid rgba(6,78,59,.12); border-radius: 999px; margin-bottom: 28px; }
    .sj-tab { border: 0; background: transparent; color: var(--g800); font: inherit; font-weight: 600; padding: 10px 24px; border-radius: 999px; cursor: pointer; transition: all .2s ease; }
    .sj-tab:hover { background: rgba(16,185,129,.12); }
    .sj-tab[aria-selected="true"] { background: var(--g700); color: #fff; box-shadow: 0 6px 16px rgba(19,105,63,.3); }
    .sj-tab:focus-visible { outline: 3px solid var(--gold); outline-offset: 2px; }
    .sj-panel[hidden] { display: none; }
    .sj-panel-note { color: #475569; margin: 0 0 22px; max-width: 60ch; line-height: 1.7; }
    .sj-kitab { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
    .sj-kitab div { display: grid; grid-template-columns: 44px 1fr; column-gap: 16px; background: #fff; border-radius: 18px; padding: 22px 24px; border: 1px solid rgba(6,78,59,.1); border-left: 5px solid var(--gold); box-shadow: 0 6px 20px rgba(6,78,59,.07); }
    .sj-kitab em { grid-row: span 2; width: 44px; height: 44px; border-radius: 12px; background: var(--g50); color: var(--g700); font-style: normal; font-weight: 800; display: flex; align-items: center; justify-content: center; }
    .sj-kitab b { display: block; font-size: 1.1rem; color: var(--g800); margin-bottom: 4px; }
    .sj-kitab span { color: #475569; line-height: 1.6; font-size: .98rem; }

    /* ====== 4. Motivasi ====== */
    .sj-motivasi { position: relative; overflow: hidden; color: #fff; padding: 84px 0;
        background: radial-gradient(600px 300px at 15% 0%, rgba(253,230,138,.16), transparent 65%), linear-gradient(135deg, var(--g900), var(--g700)); }
    .sj-motivasi::before { content: ""; position: absolute; inset: 0; opacity: .4;
        background-image: radial-gradient(rgba(255,255,255,.12) 1.5px, transparent 1.5px); background-size: 26px 26px;
        -webkit-mask-image: linear-gradient(to right, #000, transparent 70%); mask-image: linear-gradient(to right, #000, transparent 70%); }
    .sj-motivasi .sj-wrap { position: relative; z-index: 1; }
    .sj-motivasi .sj-head h2 { color: #fff; }
    .sj-quotes { display: grid; grid-template-columns: 1.4fr 1fr; gap: 22px; }
    .sj-quote { position: relative; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.2); border-radius: 24px; padding: 40px 34px 32px; }
    .sj-quote i { position: absolute; top: 18px; left: 22px; font-size: 2rem; color: var(--gold-light); opacity: .55; }
    .sj-quote p { margin: 22px 0 18px; font-size: 1.35rem; line-height: 1.75; font-weight: 500; color: #fff; }
    .sj-quote:nth-child(2) p { font-size: 1.2rem; }
    .sj-quote span { color: var(--gold-light); font-weight: 700; font-size: .95rem; }

    /* ====== Animasi muncul ====== */
    .sj-rv { opacity: 0; transform: translateY(22px); transition: opacity .7s ease, transform .7s cubic-bezier(.2,.7,.2,1); }
    .sj-rv.in { opacity: 1; transform: none; }
    @media (prefers-reduced-motion: reduce) { .sj-rv { opacity: 1 !important; transform: none !important; transition: none !important; } }

    /* ====== Responsif ====== */
    @media (max-width: 960px) {
        .sj-story { grid-template-columns: 1fr; gap: 28px; }
        .sj-year { position: static; display: flex; align-items: flex-end; gap: 18px; }
        .sj-year span { margin: 0 0 8px; padding-top: 0; border-top: 0; border-left: 3px solid var(--gold); padding-left: 14px; }
        .sj-culture { grid-template-columns: repeat(2, 1fr); }
        .sj-cc, .sj-cc:nth-child(n+4) { grid-column: span 1; }
        .sj-cc:last-child { grid-column: span 2; }
        .sj-quotes { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .sj-sec, .sj-motivasi { padding: 60px 0; }
        .sj-hero { padding: 40px 0 56px; }
        .sj-pillars { grid-template-columns: 1fr; }
        .sj-culture { grid-template-columns: 1fr; }
        .sj-cc:last-child { grid-column: span 1; }
        .sj-kitab { grid-template-columns: 1fr; }
        .sj-quote { padding: 36px 24px 26px; }
        .sj-quote p, .sj-quote:nth-child(2) p { font-size: 1.12rem; }
    }
</style>

<div class="sj-page">

    {{-- ============ HERO ============ --}}
    @if ($visible['hero'] ?? true)
        <header class="sj-hero" style="order: {{ $order['hero'] ?? 0 }}">
            <div class="sj-wrap">
                <div class="sj-crumb">
                    <a href="{{ url('/') }}">Beranda</a> <i class="fa-solid fa-chevron-right"
                        style="font-size:10px"></i>
                    Profil <i class="fa-solid fa-chevron-right" style="font-size:10px"></i> Sejarah
                </div>
                <div class="sj-eyebrow light">{{ $heroEyebrow }}</div>
                <h1>{{ $heroTitle }} <span>{{ $heroAccent }}</span></h1>
                <p>{{ $heroDesc }}</p>
                <nav class="sj-nav" aria-label="Isi halaman">
                    @if ($visible['sejarah'] ?? true)
                        <a href="#sejarah"><i class="fa-solid fa-landmark"></i> Sejarah</a>
                    @endif
                    @if ($visible['budaya'] ?? true)
                        <a href="#budaya"><i class="fa-solid fa-people-group"></i> Budaya</a>
                    @endif
                    @if ($visible['kajian'] ?? true)
                        <a href="#kajian"><i class="fa-solid fa-book-open-reader"></i> Kajian</a>
                    @endif
                    @if ($visible['motivasi'] ?? true)
                        <a href="#motivasi"><i class="fa-solid fa-star-and-crescent"></i> Motivasi</a>
                    @endif
                </nav>
            </div>
        </header>
    @endif

    {{-- ============ 1. SEJARAH PESANTREN ============ --}}
    @if ($visible['sejarah'] ?? true)
        <section class="sj-sec" id="sejarah" style="order: {{ $order['sejarah'] ?? 1 }}">
        <div class="sj-wrap">
            <div class="sj-story">
                <div class="sj-year sj-rv">
                    <b>{{ $tahun }}</b>
                    <span>{{ $tahunCaption }}</span>
                </div>

                <div class="sj-story-body">
                    <div class="sj-eyebrow">Sejarah Pesantren</div>
                    <h2>{{ $sejarahHeading }}</h2>
                    <p class="first">{{ $sejarahP1 }}</p>
                    <p>{{ $sejarahP2 }}</p>

                    @if (!empty($fotoSejarah))
                        <figure class="sj-foto sj-rv">
                            <img src="{{ asset('storage/' . $fotoSejarah) }}"
                                alt="Komplek Pondok Pesantren Babul Hasanah" loading="lazy">
                        </figure>
                    @endif

                    <div class="sj-pillars">
                        @foreach ($pilar as $p)
                            <div class="sj-pillar sj-rv">
                                <i class="fa-solid {{ $p['ikon'] }}"></i>
                                <h3>{{ $p['judul'] }}</h3>
                                <p>{{ $p['isi'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="sj-name sj-rv">
                        <small>Makna nama</small>
                        <p><strong>Babul Hasanah</strong> {{ $maknaNama }}</p>
                    </div>

                    <div class="sj-now">
                        <h3>Hingga kini</h3>
                        <p>{{ $hinggaKini }}</p>
                        <div class="sj-chips">
                            @foreach ($jenjang as $j)<span>{{ $j }}</span>@endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ============ 2. BUDAYA PESANTREN ============ --}}
    @if ($visible['budaya'] ?? true)
        <section class="sj-sec alt" id="budaya" style="order: {{ $order['budaya'] ?? 2 }}">
        <div class="sj-wrap">
            <div class="sj-head">
                <div class="sj-eyebrow">Budaya Pesantren</div>
                <h2>{{ $budayaHeading }}</h2>
                <p>{{ $budayaDesc }}</p>
            </div>

            <div class="sj-culture">
                @foreach ($budaya as $b)
                    <article class="sj-cc sj-rv">
                        <span class="no">{{ sprintf('%02d', $loop->iteration) }}</span>
                        <span class="ico"><i class="fa-solid {{ $b['ikon'] }}"></i></span>
                        <h3>{{ $b['judul'] }}</h3>
                        <p>{{ $b['isi'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ============ 3. KAJIAN ============ --}}
    @if ($visible['kajian'] ?? true)
        <section class="sj-sec" id="kajian" style="order: {{ $order['kajian'] ?? 3 }}">
        <div class="sj-wrap">
            <div class="sj-head">
                <div class="sj-eyebrow">Kajian</div>
                <h2>{{ $kajianHeading }}</h2>
                <p>{{ $kajianDesc }}</p>
            </div>

            <div id="sjKajian">
                <div class="sj-tabs" role="tablist" aria-label="Jadwal kajian">
                    @foreach ($kajian as $key => $k)
                        <button type="button" class="sj-tab" role="tab" id="tab-{{ $key }}" aria-controls="panel-{{ $key }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}" tabindex="{{ $loop->first ? '0' : '-1' }}">{{ $k['judul'] }}</button>
                    @endforeach
                </div>

                @foreach ($kajian as $key => $k)
                    <div class="sj-panel" role="tabpanel" id="panel-{{ $key }}" aria-labelledby="tab-{{ $key }}" @unless($loop->first) hidden @endunless>
                        <p class="sj-panel-note">{{ $k['catatan'] }}</p>
                        <div class="sj-kitab">
                            @foreach ($k['items'] as $it)
                                <div><em>{{ $loop->iteration }}</em><b>{{ $it[0] }}</b><span>{{ $it[1] }}</span></div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ============ 4. KALIMAT MOTIVASI ============ --}}
    @if ($visible['motivasi'] ?? true)
        <section class="sj-motivasi" id="motivasi" style="order: {{ $order['motivasi'] ?? 4 }}">
        <div class="sj-wrap">
            <div class="sj-head">
                <div class="sj-eyebrow light">Kalimat Motivasi</div>
                <h2>{{ $motivasiHeading }}</h2>
            </div>
            <div class="sj-quotes">
                @foreach ($motivasi as $m)
                    <figure class="sj-quote sj-rv" style="margin:0">
                        <i class="fa-solid fa-quote-left"></i>
                        <p>{{ $m[0] }}</p>
                        <span>— {{ $m[1] }}</span>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>
    @endif
</div>

<script>
    (function () {
        // Tab kajian
        var root = document.getElementById('sjKajian');
        if (root) {
            var tabs = Array.prototype.slice.call(root.querySelectorAll('.sj-tab'));
            var activate = function (tab) {
                tabs.forEach(function (t) {
                    var on = t === tab;
                    t.setAttribute('aria-selected', on ? 'true' : 'false');
                    t.tabIndex = on ? 0 : -1;
                    document.getElementById(t.getAttribute('aria-controls')).hidden = !on;
                });
                tab.focus();
            };
            tabs.forEach(function (tab, i) {
                tab.addEventListener('click', function () { activate(tab); });
                tab.addEventListener('keydown', function (e) {
                    if (e.key === 'ArrowRight') activate(tabs[(i + 1) % tabs.length]);
                    if (e.key === 'ArrowLeft') activate(tabs[(i - 1 + tabs.length) % tabs.length]);
                });
            });
        }

        // Animasi muncul saat scroll
        var els = document.querySelectorAll('.sj-rv');
        if (!('IntersectionObserver' in window)) {
            els.forEach(function (el) { el.classList.add('in'); });
            return;
        }
        var obs = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (en.isIntersecting) { en.target.classList.add('in'); obs.unobserve(en.target); }
            });
        }, { threshold: 0.12 });
        els.forEach(function (el, i) {
            el.style.transitionDelay = ((i % 4) * 70) + 'ms';
            obs.observe(el);
        });
    })();
</script>
@endsection