@extends('layouts.app')

@section('title', 'Sejarah - Pondok Pesantren Babul Hasanah')

@section('content')

@php
    /*
     * FOTO: taruh file di public/images/sejarah/ lalu sesuaikan nama file.
     * Jika file belum ada, otomatis tampil blok hijau + ikon.
     */
    $foto = fn ($f) => asset('images/sejarah/' . $f);

    /* ==== TONGGAK SEJARAH — GANTI "19XX/20XX" DAN ISI DENGAN DATA ASLI PESANTREN ==== */
    $tonggak = [
        [
            'waktu' => 'Tahun 19XX',
            'judul' => 'Majelis pengajian di mushalla',
            'isi'   => [
                'Babul Hasanah bermula dari pengajian kitab yang dirintis para pendiri bersama masyarakat. Kegiatan berlangsung di mushalla dan rumah warga, didorong kepedulian terhadap pendidikan agama anak-anak sekitar.',
            ],
        ],
        [
            'waktu' => 'Tahun 19XX',
            'judul' => 'Berdirinya pondok dan asrama santri',
            'isi'   => [
                'Jumlah santri dari luar daerah terus bertambah. Pesantren kemudian membangun asrama dan ruang belajar permanen sehingga santri dapat menetap dan belajar sepanjang hari.',
            ],
        ],
        [
            'waktu' => 'Tahun 20XX',
            'judul' => 'Penataan jenjang kitab kuning',
            'isi'   => [
                'Pembelajaran disusun bertahap: nahwu-shorof sebagai dasar, dilanjutkan fiqih, tafsir, dan hadits. Jenjang yang jelas membuat perkembangan setiap santri lebih mudah dipantau.',
                'Pada masa ini pesantren juga memperkuat pembinaan akhlak dan kegiatan kemandirian santri.',
            ],
        ],
        [
            'waktu' => 'Tahun 20XX',
            'judul' => 'Pesantren terbuka dan melek teknologi',
            'isi'   => [
                'Dengan tetap memegang tradisi salaf, pesantren memanfaatkan teknologi untuk pendaftaran online, penyebaran informasi, dan dakwah digital. Hingga kini Babul Hasanah terus bertumbuh bersama santri, alumni, dan masyarakat.',
            ],
        ],
    ];

    /* ==== SEJARAH KEPENGASUHAN — GANTI DENGAN NAMA ASLI ==== */
    $pengasuh = [
        ['19XX – 19XX', 'Nama pendiri / pengasuh pertama'],
        ['19XX – 20XX', 'Nama pengasuh kedua'],
        ['20XX – sekarang', 'Nama pengasuh saat ini'],
    ];

    $budaya = [
        ['ikon' => 'fa-people-group',     'judul' => 'Santri dari berbagai daerah', 'isi' => 'Santri datang dari banyak kota dan suku. Bahasa daerah, makanan, dan kebiasaan yang berbeda justru mempererat persaudaraan.'],
        ['ikon' => 'fa-drum',             'judul' => 'Hadrah dan rebana',           'isi' => 'Dilatih rutin dan tampil di Maulid Nabi, haflah, serta undangan masyarakat sekitar.'],
        ['ikon' => 'fa-microphone-lines', 'judul' => 'Muhadharah',                  'isi' => 'Latihan pidato mingguan dalam bahasa Indonesia, Arab, dan Inggris agar santri berani tampil.'],
        ['ikon' => 'fa-hands-holding',    'judul' => 'Gotong royong dan piket',     'isi' => 'Membersihkan asrama, masjid, dan dapur dikerjakan bersama. Kemandirian dilatih dari hal kecil.'],
        ['ikon' => 'fa-person-running',   'judul' => 'Pencak silat',                'isi' => 'Seni bela diri tradisional untuk melatih disiplin, keberanian, dan sopan santun.'],
    ];

    $kajian = [
        'harian' => [
            'judul' => 'Setiap hari',
            'catatan' => 'Dasar-dasar yang dipelajari santri setelah shalat Subuh, Ashar, dan Maghrib.',
            'items' => [
                ['Al-Qur’an', 'Tahsin, tajwid, dan setoran hafalan'],
                ['Nahwu & Shorof', 'Jurumiyah, Imrithi, hingga Alfiyah Ibnu Malik'],
                ['Fiqih', 'Safinatun Najah, Fathul Qarib'],
                ['Akhlak', 'Ta’lim Muta’allim, Washaya al-Aba’ lil Abna’'],
            ],
        ],
        'mingguan' => [
            'judul' => 'Setiap pekan',
            'catatan' => 'Kajian yang lebih luas, sebagian terbuka untuk masyarakat umum.',
            'items' => [
                ['Tafsir', 'Tafsir Jalalain'],
                ['Hadits', 'Riyadhus Shalihin, Bulughul Maram'],
                ['Tasawuf', 'Kajian kitab Ihya’ Ulumuddin'],
                ['Pengajian umum', 'Malam Jumat bersama pengasuh dan tamu undangan'],
            ],
        ],
        'tahunan' => [
            'judul' => 'Sepanjang tahun',
            'catatan' => 'Kegiatan khusus pada waktu-waktu tertentu dalam kalender hijriah.',
            'items' => [
                ['Pesantren Kilat Ramadhan', 'Kajian intensif dan khataman kitab'],
                ['Maulid Nabi', 'Pembacaan maulid, hadrah, dan ceramah'],
                ['Haflah Akhirussanah', 'Wisuda santri dan pentas seni'],
                ['Isra’ Mi’raj', 'Pengajian akbar bersama masyarakat'],
            ],
        ],
    ];

    $jadwal = [
        ['03.45', 'Qiyamul lail dan shalat Subuh berjamaah'],
        ['05.30', 'Setoran Al-Qur’an dan kajian pagi'],
        ['07.30', 'Sarapan, lalu sekolah / madrasah diniyah'],
        ['12.00', 'Shalat Dzuhur, makan siang, istirahat'],
        ['15.30', 'Shalat Ashar dan kajian kitab'],
        ['18.00', 'Shalat Maghrib, tadarus, dan kajian'],
        ['19.30', 'Shalat Isya, belajar mandiri, muhadharah'],
        ['21.30', 'Absensi kamar dan istirahat'],
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
    .sj-top h1 { margin: 0; font-size: clamp(2rem, 4.4vw, 3rem); line-height: 1.15; color: var(--g800); }

    /* ====== Banner + pembuka ====== */
    .sj-banner { margin-top: 36px; aspect-ratio: 21 / 9; border-radius: 24px; box-shadow: 0 22px 50px rgba(6,78,59,.18); }
    .sj-intro { padding: 44px 0 20px; }
    .sj-intro h2 { margin: 0 0 14px; font-size: clamp(1.5rem, 3vw, 2rem); line-height: 1.3; color: var(--g800); }
    .sj-intro p { margin: 0 0 14px; font-size: 1.08rem; line-height: 1.85; color: #334155; }

    /* ====== Tonggak sejarah ====== */
    .sj-ms-list { padding-bottom: 20px; }
    .sj-ms { display: grid; grid-template-columns: 210px 1fr; gap: 40px; padding: 34px 0; border-top: 1px solid rgba(6,78,59,.16); }
    .sj-ms time { font-size: 1.25rem; font-weight: 700; color: var(--gold-text); line-height: 1.4; }
    .sj-ms h3 { margin: 0 0 12px; font-size: 1.35rem; line-height: 1.35; color: var(--g800); }
    .sj-ms p { margin: 0 0 12px; line-height: 1.85; color: #334155; font-size: 1.04rem; }
    .sj-ms p:last-child { margin-bottom: 0; }

    /* ====== Tabel kepengasuhan ====== */
    .sj-table-box { margin-top: 20px; padding-bottom: 72px; }
    .sj-table-box h3 { margin: 0 0 18px; font-size: 1.35rem; color: var(--g800); }
    .sj-scroll { overflow-x: auto; border-radius: 16px; border: 1px solid rgba(6,78,59,.16); }
    .sj-table { width: 100%; border-collapse: collapse; min-width: 440px; }
    .sj-table th { background: var(--g800); color: #fff; text-align: left; padding: 14px 20px; font-weight: 600; }
    .sj-table td { padding: 14px 20px; border-top: 1px solid rgba(6,78,59,.1); }
    .sj-table tbody tr:nth-child(even) td { background: var(--g50); }
    .sj-table td:first-child { width: 220px; white-space: nowrap; color: var(--gold-text); font-weight: 600; }

    /* ====== Section umum ====== */
    .sj-sec { padding: 84px 0; }
    .sj-sec.alt { background: var(--g50); }
    .sj-head { max-width: 640px; margin: 0 0 44px; }
    .sj-head h2 { font-size: clamp(1.7rem, 3.2vw, 2.4rem); line-height: 1.25; margin: 0 0 12px; color: var(--g800); }
    .sj-head p { margin: 0; color: #475569; line-height: 1.75; font-size: 1.05rem; }

    /* ====== Budaya ====== */
    .sj-culture { display: grid; grid-template-columns: .85fr 1.15fr; gap: 64px; align-items: start; }
    .sj-culture-intro .sj-photo { aspect-ratio: 4 / 3; border-radius: 24px; margin-top: 26px; }
    .sj-list { list-style: none; margin: 0; padding: 0; }
    .sj-list li { display: grid; grid-template-columns: 54px 1fr; gap: 18px; padding: 24px 0; border-bottom: 1px solid rgba(6,78,59,.14); }
    .sj-list li:first-child { padding-top: 0; }
    .sj-list li:last-child { border-bottom: 0; }
    .sj-list .ico { width: 54px; height: 54px; border-radius: 16px; display: flex; align-items: center; justify-content: center; background: var(--gold-soft); color: var(--gold-text); font-size: 20px; }
    .sj-list h3 { margin: 2px 0 6px; font-size: 1.12rem; color: var(--g800); }
    .sj-list p { margin: 0; color: #475569; line-height: 1.7; }

    /* ====== Kajian (tab) ====== */
    .sj-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 28px; }
    .sj-tab { border: 1.5px solid var(--g600); background: transparent; color: var(--g800); font: inherit; font-weight: 600; padding: 10px 22px; border-radius: 999px; cursor: pointer; }
    .sj-tab:hover { background: rgba(16,185,129,.1); }
    .sj-tab[aria-selected="true"] { background: var(--g700); color: #fff; border-color: transparent; }
    .sj-tab:focus-visible { outline: 3px solid var(--gold); outline-offset: 2px; }
    .sj-panel[hidden] { display: none; }
    .sj-panel-note { color: #475569; margin: 0 0 22px; max-width: 60ch; line-height: 1.7; }
    .sj-kitab { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
    .sj-kitab div { background: #fff; border-radius: 18px; padding: 22px 24px; border-left: 5px solid var(--gold); box-shadow: 0 6px 20px rgba(6,78,59,.07); }
    .sj-kitab b { display: block; font-size: 1.1rem; color: var(--g800); margin-bottom: 4px; }
    .sj-kitab span { color: #475569; line-height: 1.6; font-size: .98rem; }

    /* ====== Sehari di Babul Hasanah ====== */
    .sj-day { display: grid; grid-template-columns: 1fr 1.1fr; gap: 64px; align-items: center; }
    .sj-day .sj-photo { aspect-ratio: 1 / 1; border-radius: 28px; }
    .sj-clock { list-style: none; padding: 0; margin: 0; position: relative; }
    .sj-clock::before { content: ""; position: absolute; left: 70px; top: 14px; bottom: 14px; width: 2px; background: rgba(6,78,59,.18); }
    .sj-clock li { display: grid; grid-template-columns: 54px 36px 1fr; align-items: center; padding: 11px 0; }
    .sj-clock time { font-weight: 700; color: var(--gold-text); }
    .sj-clock i { width: 14px; height: 14px; border-radius: 50%; background: #fff; border: 4px solid #10b981; position: relative; z-index: 1; margin-left: 9px; }
    .sj-clock span { color: #1e293b; line-height: 1.5; }

    /* ====== Hadits & CTA ====== */
    .quote-box { background: #0f5132; background: linear-gradient(135deg, #0f5132, #15803d); color: #fff; border-radius: 26px; padding: 50px 44px; position: relative; overflow: hidden; }
    .quote-box i { position: absolute; font-size: 130px; opacity: .1; top: 6px; left: 22px; }
    .quote-box p { position: relative; font-size: 1.3rem; line-height: 1.8; margin: 0 0 16px; font-weight: 500; color: #fff; }
    .quote-box span { position: relative; color: #fde68a; font-weight: 700; }
    .sj-cta { background: #0f5132; background: linear-gradient(135deg, #0f5132, #15803d); color: #fff; border-radius: 28px; padding: 56px 40px; text-align: center; }
    .sj-cta h2 { margin: 0 0 12px; color: #fff; font-size: clamp(1.6rem, 3vw, 2.2rem); }
    .sj-cta p { margin: 0 auto 26px; max-width: 54ch; color: rgba(255,255,255,.9); line-height: 1.7; }
    .sj-btn { display: inline-flex; align-items: center; gap: 10px; background: #fff; color: var(--g800); font-weight: 700; padding: 14px 30px; border-radius: 14px; text-decoration: none; }
    .sj-btn:hover { background: #fde68a; }
    .sj-btn:focus-visible { outline: 3px solid #fde68a; outline-offset: 3px; }

    /* ====== Responsif ====== */
    @media (max-width: 960px) {
        .sj-culture, .sj-day { grid-template-columns: 1fr; gap: 40px; }
        .sj-day .sj-photo { aspect-ratio: 16 / 10; }
    }
    @media (max-width: 768px) {
        .sj-sec { padding: 60px 0; }
        .sj-banner { aspect-ratio: 16 / 10; border-radius: 18px; }
        .sj-ms { grid-template-columns: 1fr; gap: 10px; padding: 28px 0; }
        .sj-kitab { grid-template-columns: 1fr; }
        .quote-box { padding: 40px 26px; }
        .sj-cta { padding: 44px 24px; }
    }
</style>

<div class="sj-page">

    {{-- ============ JUDUL HALAMAN ============ --}}
    <section class="sj-top">
        <div class="sj-wrap">
            <div class="sj-crumb">
                <a href="{{ url('/') }}">Beranda</a> <i class="fa-solid fa-chevron-right" style="font-size:10px"></i>
                Profil <i class="fa-solid fa-chevron-right" style="font-size:10px"></i> Sejarah
            </div>
            <h1>Sejarah</h1>
        </div>
    </section>

    {{-- ============ BANNER + PEMBUKA ============ --}}
    <section>
        <div class="sj-wrap">
            <div class="sj-photo sj-banner">
                <img src="{{ $foto('pesantren.jpg') }}" alt="Pondok Pesantren Babul Hasanah" onerror="this.remove()">
                <i class="fa-solid fa-mosque"></i>
            </div>

            <div class="sj-narrow sj-intro">
                <h2>Awal berdirinya Pondok Pesantren Babul Hasanah</h2>
                <p>Babul Hasanah berdiri dari kepedulian para pendirinya terhadap pendidikan agama masyarakat sekitar. Dari majelis pengajian sederhana, pesantren ini tumbuh menjadi lembaga pendidikan Islam yang menekankan pendalaman kitab kuning, pembinaan akhlak, dan pengabdian kepada umat.</p>
                <p>Nama <i>Babul Hasanah</i> berarti “pintu kebaikan”: harapan agar setiap santri yang masuk lewat pintu ini keluar sebagai pembawa kebaikan bagi keluarga, masyarakat, dan bangsa.</p>
            </div>
        </div>
    </section>

    {{-- ============ TONGGAK SEJARAH ============ --}}
    <section>
        <div class="sj-wrap">
            <div class="sj-narrow">
                <div class="sj-ms-list">
                    @foreach ($tonggak as $t)
                        <article class="sj-ms">
                            <time>{{ $t['waktu'] }}</time>
                            <div>
                                <h3>{{ $t['judul'] }}</h3>
                                @foreach ($t['isi'] as $para)
                                    <p>{{ $para }}</p>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="sj-table-box">
                    <h3>Sejarah kepengasuhan</h3>
                    <div class="sj-scroll">
                        <table class="sj-table">
                            <thead><tr><th>Periode</th><th>Pengasuh</th></tr></thead>
                            <tbody>
                                @foreach ($pengasuh as $p)
                                    <tr><td>{{ $p[0] }}</td><td>{{ $p[1] }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ SATU PESANTREN, BANYAK BUDAYA ============ --}}
    <section class="sj-sec alt">
        <div class="sj-wrap">
            <div class="sj-culture">
                <div class="sj-culture-intro">
                    <div class="sj-head" style="margin-bottom:0">
                        <h2>Satu pesantren, banyak budaya</h2>
                        <p>Di sini santri belajar bahwa perbedaan asal daerah bukan jarak. Tradisi pesantren dan seni Islam hidup berdampingan dalam keseharian.</p>
                    </div>
                    <div class="sj-photo">
                        <img src="{{ $foto('budaya.jpg') }}" alt="Kegiatan budaya santri" loading="lazy" onerror="this.remove()">
                        <i class="fa-solid fa-people-group"></i>
                    </div>
                </div>

                <ul class="sj-list">
                    @foreach ($budaya as $b)
                        <li>
                            <span class="ico"><i class="fa-solid {{ $b['ikon'] }}"></i></span>
                            <div>
                                <h3>{{ $b['judul'] }}</h3>
                                <p>{{ $b['isi'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- ============ KAJIAN DAN KITAB ============ --}}
    <section class="sj-sec">
        <div class="sj-wrap">
            <div class="sj-head">
                <h2>Kajian dan kitab yang dipelajari</h2>
                <p>Santri belajar bertahap, dari dasar bahasa Arab sampai tafsir dan tasawuf.</p>
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
                                <div><b>{{ $it[0] }}</b><span>{{ $it[1] }}</span></div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ SEHARI DI BABUL HASANAH ============ --}}
    <section class="sj-sec alt">
        <div class="sj-wrap">
            <div class="sj-day">
                <div class="sj-photo">
                    <img src="{{ $foto('kegiatan.jpg') }}" alt="Santri mengaji bersama" loading="lazy" onerror="this.remove()">
                    <i class="fa-solid fa-book-quran"></i>
                </div>
                <div>
                    <div class="sj-head" style="margin-bottom:22px">
                        <h2>Sehari di Babul Hasanah</h2>
                        <p>Hari santri tertata dari sebelum Subuh sampai waktu istirahat malam.</p>
                    </div>
                    <ol class="sj-clock">
                        @foreach ($jadwal as $j)
                            <li><time>{{ $j[0] }}</time><i></i><span>{{ $j[1] }}</span></li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ HADITS ============ --}}
    <section class="sj-sec" style="padding-bottom:0">
        <div class="sj-wrap">
            <div class="quote-box">
                <i class="fa-solid fa-quote-left"></i>
                <p>“Barang siapa menempuh jalan untuk menuntut ilmu, Allah mudahkan baginya jalan menuju surga.”</p>
                <span>— HR. Muslim</span>
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

<script>
    (function () {
        var root = document.getElementById('sjKajian');
        if (!root) return;
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
    })();
</script>
@endsection