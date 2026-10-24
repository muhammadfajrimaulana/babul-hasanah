@extends('layouts.app')

@section('title', 'Pembelajaran - Pondok Pesantren Babul Hasanah')

@section('content')

@php
    /* FOTO: taruh di public/images/pembelajaran/ (jika belum ada, tampil blok hijau + ikon). */
    $foto = fn ($f) => asset('images/pembelajaran/' . $f);

    /* ==== TIGA JALUR BELAJAR ==== */
    $jalur = [
        ['fa-book-open',    'Kajian kitab kuning', 'Santri mempelajari kitab-kitab klasik secara bertahap sesuai kemampuan.'],
        ['fa-school',       'Pendidikan formal',   'Jenjang madrasah dan diniyah formal berjalan beriringan dengan pengajian pesantren.'],
        ['fa-hand-holding-heart', 'Pembinaan karakter', 'Adab, bahasa, dan keterampilan dilatih lewat kegiatan harian dan ekstrakurikuler.'],
    ];

    /* ==== BIDANG KAJIAN & KITAB (dari data lama; silakan tambah/ubah) ==== */
    $kajian = [
        ['ikon' => 'fa-spell-check',   'judul' => 'Nahwu & Shorof',  'isi' => 'Fondasi membaca kitab gundul: tata bahasa dan perubahan kata dalam bahasa Arab.',          'kitab' => ['Jurumiyah', 'Imrithi', 'Alfiyah', 'Amtsilati Tasrifiyah']],
        ['ikon' => 'fa-scale-balanced','judul' => 'Fiqih',           'isi' => 'Hukum ibadah dan muamalah menurut mazhab Syafi’i secara mendalam dan praktis.',            'kitab' => ['Safinatun Najah', 'Fathul Qorib', 'Fathul Mu’in']],
        ['ikon' => 'fa-star-and-crescent','judul' => 'Tauhid / Aqidah','isi' => 'Memperkokoh keyakinan sesuai ajaran Ahlussunnah wal Jama’ah.',                          'kitab' => ['Aqidatul Awam', 'Kifayatul Awam', 'Tijan Ad-Darari']],
        ['ikon' => 'fa-heart-pulse',   'judul' => 'Akhlak & Tasawuf','isi' => 'Menata adab, hati, dan perilaku sebagai penuntut ilmu.',                                    'kitab' => ['Ta’lim Muta’allim', 'Akhlaq lil Banin', 'Bidayatul Hidayah']],
        ['ikon' => 'fa-scroll',        'judul' => 'Hadits & Tafsir', 'isi' => 'Memahami sabda Nabi dan kandungan Al-Qur’an melalui kajian para ulama.',                   'kitab' => ['Arba’in Nawawi', 'Riyadhus Shalihin', 'Tafsir Jalalain']],
        ['ikon' => 'fa-book-quran',    'judul' => 'Al-Qur’an',       'isi' => 'Tahsin, tajwid, dan program tahfidz bagi santri yang berminat.',                           'kitab' => ['Tahsin', 'Tajwid', 'Tahfidz']],
    ];

    /* ==== JENJANG & PROGRAM (PPBH) — SESUAIKAN DESKRIPSI DENGAN KURIKULUM ASLI ==== */
    $jalurDiniyah = [
        ['MDTA',        'Madrasah Diniyah Takmiliyah',      'Pengenalan dasar membaca Al-Qur’an, ibadah, akhlak, dan pengantar bahasa Arab.'],
        ['PPS Wustha',  'Program pesantren jenjang Wustha', 'Pendalaman nahwu-shorof, fiqih, dan tauhid dengan kitab tingkat menengah.'],
        ['PDF Wustha',  'Pendidikan Diniyah Formal Wustha', 'Pendidikan diniyah formal jenjang Wustha dengan kurikulum kitab terstruktur.'],
        ['PDF Ulya',    'Pendidikan Diniyah Formal Ulya',   'Kajian lanjutan fiqih, tafsir, hadits, serta pelatihan bahtsul masail.'],
        ['Ma’had ‘Aly', 'Pendidikan tinggi pesantren',      'Pendalaman keilmuan tingkat lanjut bagi santri yang ingin menjadi ahli di bidang kitab.'],
    ];
    $jalurMadrasah = [
        ['MTs', 'Madrasah Tsanawiyah', 'Pendidikan menengah pertama yang memadukan kurikulum madrasah dengan kegiatan pesantren.'],
        ['MA',  'Madrasah Aliyah',     'Pendidikan menengah atas yang menyiapkan santri melanjutkan studi dan berkhidmat di masyarakat.'],
    ];

    /* ==== METODE ==== */
    $metode = [
        ['fa-user-group',      'Sorogan',        'Santri membaca kitab satu per satu di hadapan ustadz untuk dikoreksi langsung.'],
        ['fa-chalkboard-user', 'Bandongan',      'Ustadz membacakan dan menjelaskan kitab, santri menyimak dan memberi makna.'],
        ['fa-brain',           'Muhafazhah',     'Menghafal nazham dan matan kitab untuk memperkuat penguasaan ilmu.'],
        ['fa-comments',        'Bahtsul masail', 'Diskusi kajian hukum untuk melatih nalar dan kemampuan merujuk kitab.'],
    ];

    /* ==== JADWAL HARIAN ==== */
    $jadwal = [
        ['04.00 – 05.30', 'Qiyamul lail & shalat Subuh',           'Dilanjutkan wirid dan mengaji Al-Qur’an'],
        ['06.00 – 07.00', 'Pengajian kitab pagi',                  'Bandongan bersama ustadz'],
        ['07.30 – 12.00', 'Kegiatan belajar formal / madrasah',    'Sesuai jenjang dan kelas masing-masing'],
        ['13.00 – 15.00', 'Shalat Dzuhur & istirahat',             'Makan siang dan muraja’ah'],
        ['16.00 – 17.30', 'Ekstrakurikuler & pembinaan bahasa',    'Muhadharah, bahasa Arab/Inggris, olahraga'],
        ['18.30 – 21.00', 'Pengajian kitab malam',                 'Sorogan, hafalan, dan diskusi'],
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

    .sj-top { background: var(--g50); padding: 48px 0 40px; border-bottom: 1px solid rgba(6,78,59,.1); }
    .sj-crumb { font-size: 14px; color: #475569; margin-bottom: 14px; }
    .sj-crumb a { color: var(--g700); text-decoration: none; }
    .sj-crumb a:hover { text-decoration: underline; }
    .sj-top h1 { margin: 0 0 10px; font-size: clamp(2rem, 4.4vw, 3rem); line-height: 1.15; color: var(--g800); }
    .sj-top p { margin: 0; max-width: 64ch; color: #475569; line-height: 1.75; font-size: 1.05rem; }

    .sj-sec { padding: 84px 0; }
    .sj-sec.alt { background: var(--g50); }
    .sj-head { max-width: 640px; margin: 0 0 44px; }
    .sj-head h2 { font-size: clamp(1.7rem, 3.2vw, 2.4rem); line-height: 1.25; margin: 0 0 12px; color: var(--g800); }
    .sj-head p { margin: 0; color: #475569; line-height: 1.75; font-size: 1.05rem; }

    /* ====== Pembuka: banner + tiga jalur ====== */
    .sj-banner { margin-top: 36px; aspect-ratio: 21 / 9; border-radius: 24px; box-shadow: 0 22px 50px rgba(6,78,59,.18); }
    .sj-intro { padding: 40px 0 8px; }
    .sj-intro p { margin: 0; font-size: 1.1rem; line-height: 1.85; color: #334155; max-width: 70ch; }
    .jalur { display: grid; grid-template-columns: repeat(3, 1fr); margin-top: 44px; }
    .jalur-item { padding: 6px 28px 6px 0; }
    .jalur-item + .jalur-item { padding-left: 28px; border-left: 1px solid rgba(6,78,59,.16); }
    .jalur-ico { width: 52px; height: 52px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 20px; background: #dcfce7; color: var(--g700); margin-bottom: 14px; }
    .jalur-item:nth-child(2) .jalur-ico { background: var(--gold-soft); color: var(--gold-text); }
    .jalur-item h3 { margin: 0 0 6px; font-size: 1.15rem; color: var(--g800); }
    .jalur-item p { margin: 0; color: #475569; line-height: 1.7; }

    /* ====== Kajian: daftar ilmu + panel ====== */
    .kj { display: grid; grid-template-columns: 290px 1fr; gap: 40px; align-items: start; }
    .kj-tabs { display: flex; flex-direction: column; gap: 6px; }
    .kj-tab { display: flex; align-items: center; gap: 14px; text-align: left; border: 0; background: transparent; font: inherit; font-weight: 600; color: var(--g800);
        padding: 14px 16px; border-radius: 14px; cursor: pointer; border-left: 4px solid transparent; }
    .kj-tab i { width: 22px; text-align: center; color: var(--gold-text); }
    .kj-tab:hover { background: rgba(16,185,129,.1); }
    .kj-tab[aria-selected="true"] { background: #fff; border-left-color: var(--gold); box-shadow: 0 8px 22px -12px rgba(6,78,59,.35); }
    .kj-tab:focus-visible { outline: 3px solid var(--gold); outline-offset: 2px; }
    .kj-panel { background: #fff; border-radius: 24px; padding: 40px 42px; box-shadow: 0 18px 44px -24px rgba(6,78,59,.35); min-height: 280px; }
    .kj-panel[hidden] { display: none; }
    .kj-panel .big { width: 64px; height: 64px; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 26px; background: var(--gold-soft); color: var(--gold-text); margin-bottom: 18px; }
    .kj-panel h3 { margin: 0 0 10px; font-size: 1.6rem; color: var(--g800); }
    .kj-panel p { margin: 0 0 24px; color: #475569; line-height: 1.8; font-size: 1.05rem; max-width: 56ch; }
    .kj-panel h4 { margin: 0 0 12px; font-size: .95rem; color: var(--gold-text); }
    .kitab { display: flex; flex-wrap: wrap; gap: 10px; }
    .kitab span { font-weight: 600; font-size: .98rem; color: var(--g800); background: #dcfce7; padding: 9px 18px; border-radius: 999px; }

    /* ====== Jenjang ====== */
    .lane-title { display: flex; align-items: center; gap: 12px; margin: 0 0 6px; font-size: 1.3rem; color: var(--g800); }
    .lane-title i { color: var(--gold-text); }
    .lane-note { margin: 0 0 18px; color: #475569; }
    .lane { margin-bottom: 52px; }
    .lane:last-child { margin-bottom: 0; }
    .jj-row { display: grid; grid-template-columns: 150px 1fr; gap: 28px; padding: 22px 0; border-top: 1px solid rgba(6,78,59,.16); align-items: start; }
    .jj-row:last-child { border-bottom: 1px solid rgba(6,78,59,.16); }
    .jj-code { display: inline-block; font-weight: 800; font-size: 1.15rem; color: #fff; background: var(--g700); padding: 8px 16px; border-radius: 12px; text-align: center; }
    .lane.madrasah .jj-code { background: #955f0f; }
    .jj-row h4 { margin: 2px 0 6px; font-size: 1.1rem; color: var(--g800); }
    .jj-row p { margin: 0; color: #475569; line-height: 1.75; max-width: 62ch; }

    /* ====== Metode ====== */
    .metode { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0 32px; }
    .metode-item { border-top: 3px solid var(--gold); padding-top: 22px; }
    .metode-item i { font-size: 24px; color: var(--g700); margin-bottom: 14px; display: block; }
    .metode-item h3 { margin: 0 0 8px; font-size: 1.2rem; color: var(--g800); }
    .metode-item p { margin: 0; color: #475569; line-height: 1.7; }

    /* ====== Jadwal ====== */
    .jadwal { max-width: 860px; margin: 0 auto; background: #fff; border-radius: 24px; overflow: hidden; border: 1px solid rgba(6,78,59,.14); box-shadow: 0 20px 44px -26px rgba(20,83,45,.35); }
    .jd-row { display: grid; grid-template-columns: 150px 26px 1fr; align-items: center; gap: 14px; padding: 18px 28px; border-bottom: 1px solid rgba(6,78,59,.08); }
    .jd-row:last-child { border-bottom: 0; }
    .jd-time { font-weight: 800; color: var(--gold-text); font-variant-numeric: tabular-nums; }
    .jd-dot { width: 14px; height: 14px; border-radius: 50%; background: #fff; border: 4px solid var(--g500); }
    .jd-act b { display: block; color: #0f172a; }
    .jd-act small { color: #475569; font-size: .92rem; }

    /* ====== CTA ====== */
    .sj-cta { background: #0f5132; background: linear-gradient(135deg, #0f5132, #15803d); color: #fff; border-radius: 28px; padding: 56px 40px; text-align: center; }
    .sj-cta h2 { margin: 0 0 12px; color: #fff; font-size: clamp(1.6rem, 3vw, 2.2rem); }
    .sj-cta p { margin: 0 auto 26px; max-width: 54ch; color: rgba(255,255,255,.9); line-height: 1.7; }
    .sj-btn { display: inline-flex; align-items: center; gap: 10px; background: #fff; color: var(--g800); font-weight: 700; padding: 14px 30px; border-radius: 14px; text-decoration: none; }
    .sj-btn:hover { background: #fde68a; }
    .sj-btn:focus-visible { outline: 3px solid #fde68a; outline-offset: 3px; }

    /* ====== Responsif ====== */
    @media (max-width: 960px) {
        .kj { grid-template-columns: 1fr; gap: 24px; }
        .kj-tabs { flex-direction: row; flex-wrap: wrap; }
        .kj-tab { border-left: 0; border-bottom: 3px solid transparent; padding: 10px 14px; }
        .kj-tab[aria-selected="true"] { border-bottom-color: var(--gold); }
        .metode { grid-template-columns: repeat(2, 1fr); row-gap: 36px; }
    }
    @media (max-width: 768px) {
        .sj-sec { padding: 60px 0; }
        .sj-banner { aspect-ratio: 16 / 10; border-radius: 18px; }
        .jalur { grid-template-columns: 1fr; row-gap: 28px; }
        .jalur-item, .jalur-item + .jalur-item { padding: 0; border-left: 0; }
        .kj-panel { padding: 28px 22px; }
        .jj-row { grid-template-columns: 1fr; gap: 10px; }
        .jj-code { justify-self: start; }
        .jd-row { grid-template-columns: 112px 22px 1fr; padding: 16px; gap: 10px; }
    }
    @media (max-width: 520px) { .metode { grid-template-columns: 1fr; } .sj-cta { padding: 44px 24px; } }
</style>

<div class="sj-page">

    {{-- ============ JUDUL HALAMAN ============ --}}
    <section class="sj-top">
        <div class="sj-wrap">
            <div class="sj-crumb">
                <a href="{{ url('/') }}">Beranda</a> <i class="fa-solid fa-chevron-right" style="font-size:10px"></i>
                Profil <i class="fa-solid fa-chevron-right" style="font-size:10px"></i> Pembelajaran
            </div>
            <h1>Pembelajaran dan kajian kitab kuning</h1>
            <p>Kurikulum terpadu yang memadukan tradisi keilmuan salaf, pembinaan akhlak, dan kegiatan penunjang untuk membentuk santri berilmu dan beradab.</p>
        </div>
    </section>

    {{-- ============ BANNER + TIGA JALUR ============ --}}
    <section>
        <div class="sj-wrap">
            <div class="sj-photo sj-banner">
                <img src="{{ $foto('pembelajaran.jpg') }}" alt="Santri mengaji kitab kuning" onerror="this.remove()">
                <i class="fa-solid fa-book-quran"></i>
            </div>

            <div class="sj-intro">
                <p>Seluruh pelajaran di Babul Hasanah disampaikan melalui kitab-kitab klasik yang diajarkan bertahap sesuai kemampuan santri. Pengajian pesantren berjalan bersama pendidikan formal, sehingga santri tumbuh kuat dalam ilmu agama sekaligus siap menghadapi zamannya.</p>

                <div class="jalur">
                    @foreach ($jalur as $j)
                        <div class="jalur-item">
                            <div class="jalur-ico"><i class="fa-solid {{ $j[0] }}"></i></div>
                            <h3>{{ $j[1] }}</h3>
                            <p>{{ $j[2] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ============ BIDANG KAJIAN ============ --}}
    <section class="sj-sec">
        <div class="sj-wrap">
            <div class="sj-head">
                <h2>Ilmu yang dipelajari santri</h2>
                <p>Pilih bidang ilmu untuk melihat penjelasan dan kitab yang dikaji.</p>
            </div>

            <div class="kj" id="kajian">
                <div class="kj-tabs" role="tablist" aria-label="Bidang kajian">
                    @foreach ($kajian as $i => $k)
                        <button type="button" class="kj-tab" role="tab" id="kt-{{ $i }}" aria-controls="kp-{{ $i }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}" tabindex="{{ $loop->first ? '0' : '-1' }}">
                            <i class="fa-solid {{ $k['ikon'] }}"></i>{{ $k['judul'] }}
                        </button>
                    @endforeach
                </div>

                <div>
                    @foreach ($kajian as $i => $k)
                        <div class="kj-panel" role="tabpanel" id="kp-{{ $i }}" aria-labelledby="kt-{{ $i }}">
                            <div class="big"><i class="fa-solid {{ $k['ikon'] }}"></i></div>
                            <h3>{{ $k['judul'] }}</h3>
                            <p>{{ $k['isi'] }}</p>
                            <h4>Kitab yang dikaji</h4>
                            <div class="kitab">
                                @foreach ($k['kitab'] as $kt)
                                    <span>{{ $kt }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ============ JENJANG PENDIDIKAN ============ --}}
    <section class="sj-sec alt">
        <div class="sj-wrap">
            <div class="sj-narrow">
                <div class="sj-head">
                    <h2>Jenjang dan program pendidikan</h2>
                    <p>Tujuh jenjang di bawah naungan Yayasan Pondok Pesantren Babul Hasanah, terbagi dalam dua jalur.</p>
                </div>

                <div class="lane">
                    <h3 class="lane-title"><i class="fa-solid fa-kaaba"></i> Jalur diniyah</h3>
                    <p class="lane-note">Pendidikan berbasis kitab, dari tingkat dasar hingga Ma’had ‘Aly.</p>
                    @foreach ($jalurDiniyah as $j)
                        <article class="jj-row">
                            <div><span class="jj-code">{{ $j[0] }}</span></div>
                            <div>
                                <h4>{{ $j[1] }}</h4>
                                <p>{{ $j[2] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="lane madrasah">
                    <h3 class="lane-title"><i class="fa-solid fa-school"></i> Jalur madrasah</h3>
                    <p class="lane-note">Pendidikan formal yang berjalan bersama kegiatan pesantren.</p>
                    @foreach ($jalurMadrasah as $j)
                        <article class="jj-row">
                            <div><span class="jj-code">{{ $j[0] }}</span></div>
                            <div>
                                <h4>{{ $j[1] }}</h4>
                                <p>{{ $j[2] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ============ METODE ============ --}}
    <section class="sj-sec">
        <div class="sj-wrap">
            <div class="sj-head">
                <h2>Cara santri belajar</h2>
                <p>Empat metode khas pesantren yang dipakai bergantian sesuai jenis kitab dan tingkat santri.</p>
            </div>
            <div class="metode">
                @foreach ($metode as $m)
                    <div class="metode-item">
                        <i class="fa-solid {{ $m[0] }}"></i>
                        <h3>{{ $m[1] }}</h3>
                        <p>{{ $m[2] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ JADWAL HARIAN ============ --}}
    <section class="sj-sec alt">
        <div class="sj-wrap">
            <div class="sj-head" style="margin-left:auto; margin-right:auto; text-align:center">
                <h2>Jadwal harian santri</h2>
                <p>Rutinitas yang menata hari santri dari sepertiga malam hingga malam hari.</p>
            </div>
            <div class="jadwal">
                @foreach ($jadwal as $j)
                    <div class="jd-row">
                        <div class="jd-time">{{ $j[0] }}</div>
                        <span class="jd-dot"></span>
                        <div class="jd-act"><b>{{ $j[1] }}</b><small>{{ $j[2] }}</small></div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="sj-sec">
        <div class="sj-wrap">
            <div class="sj-cta">
                <h2>Siap belajar bersama kami?</h2>
                <p>Mulai langkah pertama menuju ilmu yang bermanfaat dan akhlak yang mulia.</p>
                <a href="{{ url('/pendaftaran') }}" class="sj-btn">Daftar online <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </section>
</div>

<script>
    (function () {
        var root = document.getElementById('kajian');
        if (!root) return;
        var tabs = Array.prototype.slice.call(root.querySelectorAll('.kj-tab'));
        var panels = tabs.map(function (t) { return document.getElementById(t.getAttribute('aria-controls')); });

        function show(i, focus) {
            tabs.forEach(function (t, n) {
                var on = n === i;
                t.setAttribute('aria-selected', on ? 'true' : 'false');
                t.tabIndex = on ? 0 : -1;
                panels[n].hidden = !on;
            });
            if (focus) tabs[i].focus();
        }

        tabs.forEach(function (t, i) {
            t.addEventListener('click', function () { show(i, false); });
            t.addEventListener('keydown', function (e) {
                var next = null;
                if (e.key === 'ArrowDown' || e.key === 'ArrowRight') next = (i + 1) % tabs.length;
                if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') next = (i - 1 + tabs.length) % tabs.length;
                if (next !== null) { e.preventDefault(); show(next, true); }
            });
        });

        show(0, false); // tanpa JS, semua panel tetap tampil
    })();
</script>
@endsection