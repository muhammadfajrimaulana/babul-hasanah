@extends('layouts.app')

@section('title', 'Prestasi Santri - Pondok Pesantren Babul Hasanah')

@section('content')

    <style>
        .pr {
            --green: #17402f; --green-2: #1d4d3a; --cream: #f1eee6; --paper: #fbfaf6; --gold: #b7791f;
            --ink: #1f2a24; --muted: #6b756f; --line: #e4e0d4; --emerald: #12b886; --emerald-d: #0e9f73;
            --radius: 22px; --shadow: 0 10px 30px rgba(23, 64, 47, .07); --shadow-lg: 0 22px 50px rgba(23, 64, 47, .14);
            font-family: 'Poppins', system-ui, sans-serif; color: var(--ink); font-size: 15px; line-height: 1.6;
            background:
                radial-gradient(900px 500px at 100% -5%, rgba(183, 121, 31, .07), transparent 60%),
                radial-gradient(800px 500px at -10% 30%, rgba(23, 64, 47, .05), transparent 60%),
                var(--paper);
        }
        .pr *, .pr *::before, .pr *::after { box-sizing: border-box; }
        .pr a { color: inherit; text-decoration: none; }
        .pr h1, .pr h2, .pr h3, .pr h4 { margin: 0; font-weight: 600; line-height: 1.25; color: var(--green); letter-spacing: -.01em; }
        .pr p { margin: 0; }
        .pr .container { width: min(1180px, 92%); margin-inline: auto; }
        .pr .eyebrow {
            display: flex; align-items: center; gap: 12px; font-size: 11px; letter-spacing: .12em; text-transform: uppercase;
            color: var(--gold); font-weight: 600; margin-bottom: 14px;
        }
        .pr .eyebrow::before { content: ""; width: 28px; height: 1px; background: var(--gold); }
        .pr .eyebrow.light { color: #e6d3a3; }
        .pr .eyebrow.light::before { background: #e6d3a3; }

        /* ---------- HEADER HALAMAN ---------- */
        .pr-hero {
            position: relative; overflow: hidden; color: #fff; padding: 70px 0 90px;
            background: linear-gradient(130deg, var(--green), var(--green-2));
        }
        .pr-hero::before {
            content: ""; position: absolute; inset: 0; opacity: .08;
            background-image: radial-gradient(#d1fae5 1px, transparent 1px); background-size: 18px 18px;
        }
        .pr-hero::after {
            content: "\f091"; font-family: "Font Awesome 6 Free"; font-weight: 900; position: absolute;
            right: 6%; top: 50%; transform: translateY(-50%) rotate(-12deg); font-size: 220px; color: rgba(255, 255, 255, .06);
        }
        .pr-hero .container { position: relative; z-index: 1; }
        .pr-crumb { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #b9d0c4; margin-bottom: 22px; }
        .pr-crumb a:hover { color: #fff; }
        .pr-hero h1 { color: #fff; font-size: clamp(30px, 4.6vw, 52px); font-weight: 700; margin-bottom: 14px; }
        .pr-hero h1 span { color: #19d68f; }
        .pr-hero p.lead { max-width: 620px; color: rgba(255, 255, 255, .85); font-size: clamp(13.5px, 1.4vw, 16px); }

        /* ---------- RINGKASAN ---------- */
        .pr-stats { margin-top: -48px; position: relative; z-index: 2; }
        .pr-stats-grid {
            display: grid; grid-template-columns: repeat(4, 1fr); background: #fff; border: 1px solid var(--line);
            border-radius: var(--radius); box-shadow: var(--shadow-lg); overflow: hidden;
        }
        .pr-stat { padding: 24px 26px; display: flex; align-items: center; gap: 16px; border-left: 1px solid var(--line); }
        .pr-stat:first-child { border-left: 0; }
        .pr-stat .ic {
            width: 46px; height: 46px; border-radius: 14px; background: var(--cream); color: var(--green);
            display: grid; place-items: center; font-size: 18px; flex: none;
        }
        .pr-stat:nth-child(2) .ic { background: rgba(183, 121, 31, .12); color: var(--gold); }
        .pr-stat b { display: block; font-size: 26px; font-weight: 600; color: var(--green); line-height: 1.1; }
        .pr-stat span { font-size: 11.5px; color: var(--muted); }

        /* ---------- TOOLBAR FILTER ---------- */
        .pr-main { padding: 56px 0 70px; }
        .pr-toolbar { display: flex; flex-direction: column; gap: 18px; margin-bottom: 32px; }
        .pr-toolbar-top { display: flex; justify-content: space-between; align-items: flex-end; gap: 20px; flex-wrap: wrap; }
        .pr-toolbar h2 { font-size: clamp(24px, 3vw, 34px); }
        .pr-controls { display: flex; gap: 12px; flex-wrap: wrap; }
        .pr-field { position: relative; }
        .pr-field i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 13px; pointer-events: none; }
        .pr-input, .pr-select {
            font-family: inherit; font-size: 13px; color: var(--ink); background: #fff; border: 1px solid var(--line);
            border-radius: 999px; padding: 11px 18px; outline: none; transition: all .2s ease;
        }
        .pr-input { padding-left: 40px; width: 260px; }
        .pr-select { padding-right: 36px; cursor: pointer; appearance: none; -webkit-appearance: none;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b756f' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'><path d='m6 9 6 6 6-6'/></svg>");
            background-repeat: no-repeat; background-position: right 16px center; }
        .pr-input:focus, .pr-select:focus { border-color: var(--emerald); box-shadow: 0 0 0 3px rgba(18, 184, 134, .15); }

        .pr-chips { display: flex; gap: 8px; flex-wrap: wrap; }
        .pr-chip {
            font-family: inherit; font-size: 12px; font-weight: 500; color: var(--muted); background: #fff;
            border: 1px solid var(--line); border-radius: 999px; padding: 8px 18px; cursor: pointer; transition: all .2s ease;
        }
        .pr-chip:hover { border-color: var(--green); color: var(--green); }
        .pr-chip.active { background: var(--green); border-color: var(--green); color: #fff; }
        .pr-count { font-size: 12px; color: var(--muted); }
        .pr-count b { color: var(--green); }

        /* ---------- KARTU PRESTASI ---------- */
        .pr-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; }
        .pr-card {
            background: #fff; border: 1px solid var(--line); border-radius: var(--radius); overflow: hidden;
            display: flex; flex-direction: column; transition: all .3s ease;
        }
        .pr-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); border-color: var(--gold); }
        .pr-card.hide { display: none; }
        .pr-card-top {
            position: relative; padding: 22px 22px 40px; color: #fff; display: flex; justify-content: space-between; align-items: flex-start;
            background: linear-gradient(135deg, #5d7f66, #2f4a3a);
        }
        .pr-card.r1 .pr-card-top { background: linear-gradient(135deg, #d9a441, #9a6416); }
        .pr-card.r2 .pr-card-top { background: linear-gradient(135deg, #aab4ba, #6f7c85); }
        .pr-card.r3 .pr-card-top { background: linear-gradient(135deg, #c98a54, #8a5128); }
        .pr-card-top::after {
            content: ""; position: absolute; inset: 0; opacity: .12;
            background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 14px 14px;
        }
        .pr-medal {
            position: relative; z-index: 1; width: 52px; height: 52px; border-radius: 16px; background: rgba(255, 255, 255, .22);
            border: 1px solid rgba(255, 255, 255, .4); display: grid; place-items: center; font-size: 22px;
        }
        .pr-rank { position: relative; z-index: 1; text-align: right; }
        .pr-rank b { display: block; font-size: 20px; font-weight: 700; line-height: 1.1; }
        .pr-rank span { font-size: 10px; letter-spacing: .1em; text-transform: uppercase; opacity: .9; }
        .pr-body { padding: 22px; margin-top: -22px; position: relative; background: #fff; border-radius: 22px 22px 0 0; display: flex; flex-direction: column; flex: 1; gap: 10px; }
        .pr-tags { display: flex; gap: 6px; flex-wrap: wrap; }
        .pr-tag { font-size: 10px; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; padding: 4px 10px; border-radius: 999px; background: var(--cream); color: var(--green); }
        .pr-tag.gold { background: rgba(183, 121, 31, .12); color: var(--gold); }
        .pr-body h3 { font-size: 17px; }
        .pr-meta { font-size: 11.5px; color: var(--muted); display: flex; gap: 14px; flex-wrap: wrap; }
        .pr-meta i { color: var(--gold); margin-right: 5px; }
        .pr-body p.desc { font-size: 12px; color: var(--muted); flex: 1; }
        .pr-who { display: flex; align-items: center; gap: 12px; border-top: 1px solid var(--line); padding-top: 14px; margin-top: 4px; }
        .pr-avatar {
            width: 38px; height: 38px; border-radius: 50%; background: var(--green); color: #fff; font-weight: 600; font-size: 14px;
            display: grid; place-items: center; flex: none;
        }
        .pr-who b { display: block; font-size: 12.5px; color: var(--green); font-weight: 600; line-height: 1.3; }
        .pr-who span { font-size: 11px; color: var(--muted); }

        .pr-empty { display: none; text-align: center; padding: 60px 20px; background: #fff; border: 1px dashed var(--line); border-radius: var(--radius); }
        .pr-empty.show { display: block; }
        .pr-empty i { font-size: 34px; color: var(--gold); margin-bottom: 12px; }
        .pr-empty h3 { font-size: 18px; margin-bottom: 6px; }
        .pr-empty p { font-size: 13px; color: var(--muted); }

        /* ---------- AJAKAN ---------- */
        .pr-cta { padding: 0 0 80px; }
        .pr-cta-box {
            background: linear-gradient(135deg, var(--green), var(--green-2)); border-radius: 32px; padding: 50px; color: #fff;
            display: flex; justify-content: space-between; align-items: center; gap: 30px; flex-wrap: wrap; box-shadow: var(--shadow-lg);
        }
        .pr-cta-box h2 { color: #fff; font-size: clamp(22px, 3vw, 32px); margin-bottom: 8px; }
        .pr-cta-box p { font-size: 13px; color: #b9d0c4; max-width: 520px; }
        .pr-btn {
            display: inline-flex; align-items: center; gap: 10px; padding: 14px 28px; border-radius: 999px; font-size: 13.5px;
            font-weight: 600; background: var(--cream); color: var(--green); transition: all .25s ease; white-space: nowrap;
        }
        .pr-btn:hover { background: #fff; transform: translateY(-2px); box-shadow: 0 10px 22px rgba(0, 0, 0, .18); }

        /* animasi muncul */
        .pr .reveal { opacity: 0; transform: translateY(22px); transition: opacity .7s ease, transform .7s cubic-bezier(.2, .7, .2, 1); }
        .pr .reveal.in { opacity: 1; transform: none; }

        @media (max-width: 1024px) {
            .pr-grid { grid-template-columns: repeat(2, 1fr); }
            .pr-stats-grid { grid-template-columns: repeat(2, 1fr); }
            .pr-stat:nth-child(3) { border-left: 0; }
            .pr-stat:nth-child(n+3) { border-top: 1px solid var(--line); }
        }
        @media (max-width: 640px) {
            .pr-hero { padding: 48px 0 80px; }
            .pr-hero::after { font-size: 130px; right: -10px; }
            .pr-grid { grid-template-columns: 1fr; }
            .pr-stat { padding: 18px 16px; gap: 12px; }
            .pr-stat .ic { width: 40px; height: 40px; font-size: 16px; }
            .pr-stat b { font-size: 22px; }
            .pr-controls { width: 100%; }
            .pr-field, .pr-select { flex: 1; }
            .pr-input { width: 100%; }
            .pr-select { width: 100%; }
            .pr-chips { flex-wrap: nowrap; overflow-x: auto; padding-bottom: 6px; margin-inline: -4%; padding-inline: 4%; scrollbar-width: none; }
            .pr-chips::-webkit-scrollbar { display: none; }
            .pr-chip { white-space: nowrap; flex: none; }
            .pr-cta-box { padding: 30px 22px; border-radius: 22px; }
            .pr-btn { width: 100%; justify-content: center; }
        }
        @media (prefers-reduced-motion: reduce) { .pr .reveal { transition: none !important; opacity: 1 !important; transform: none !important; } }
    </style>

    @php
        $kategoriList = $prestasi->pluck('kategori')->unique()->values();
        $tahunList    = $prestasi->pluck('tahun')->unique()->sortDesc()->values();
        $totalJuara1  = $prestasi->where('peringkat', 1)->count();
        $totalNasional = $prestasi->where('tingkat', 'Nasional')->count();
    @endphp

    <div class="pr">

        {{-- HEADER HALAMAN --}}
        <section class="pr-hero">
            <div class="container">
                <div class="pr-crumb">
                    <a href="{{ url('/') }}">Beranda</a>
                    <i class="fa-solid fa-chevron-right" style="font-size:9px"></i>
                    <span>Prestasi</span>
                </div>
                <div class="eyebrow light">Prestasi Santri</div>
                <h1>Ikhtiar terbaik, <span>buah yang berarti.</span></h1>
                <p class="lead">Merayakan proses dan menguatkan semangat santri Babul Hasanah untuk terus bertumbuh, dari tingkat kabupaten hingga nasional.</p>
            </div>
        </section>

        {{-- RINGKASAN --}}
        <section class="pr-stats">
            <div class="container">
                <div class="pr-stats-grid">
                    <div class="pr-stat reveal"><span class="ic"><i class="fa-solid fa-award"></i></span><div><b data-count="{{ $prestasi->count() }}">{{ $prestasi->count() }}</b><span>Total prestasi</span></div></div>
                    <div class="pr-stat reveal"><span class="ic"><i class="fa-solid fa-trophy"></i></span><div><b data-count="{{ $totalJuara1 }}">{{ $totalJuara1 }}</b><span>Juara pertama</span></div></div>
                    <div class="pr-stat reveal"><span class="ic"><i class="fa-solid fa-earth-asia"></i></span><div><b data-count="{{ $totalNasional }}">{{ $totalNasional }}</b><span>Tingkat nasional</span></div></div>
                    <div class="pr-stat reveal"><span class="ic"><i class="fa-solid fa-layer-group"></i></span><div><b data-count="{{ $kategoriList->count() }}">{{ $kategoriList->count() }}</b><span>Bidang lomba</span></div></div>
                </div>
            </div>
        </section>

        {{-- DAFTAR PRESTASI --}}
        <section class="pr-main">
            <div class="container">

                <div class="pr-toolbar">
                    <div class="pr-toolbar-top">
                        <div>
                            <div class="eyebrow">Galeri Prestasi</div>
                            <h2>Semua capaian santri</h2>
                        </div>
                        <div class="pr-controls">
                            <div class="pr-field">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="search" id="prSearch" class="pr-input" placeholder="Cari lomba atau nama santri...">
                            </div>
                            <select id="prYear" class="pr-select" aria-label="Filter tahun">
                                <option value="all">Semua tahun</option>
                                @foreach ($tahunList as $tahun)
                                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="pr-chips" id="prChips">
                        <button type="button" class="pr-chip active" data-cat="all">Semua</button>
                        @foreach ($kategoriList as $kategori)
                            <button type="button" class="pr-chip" data-cat="{{ $kategori }}">{{ $kategori }}</button>
                        @endforeach
                    </div>

                    <div class="pr-count">Menampilkan <b id="prShown">{{ $prestasi->count() }}</b> dari {{ $prestasi->count() }} prestasi</div>
                </div>

                <div class="pr-grid" id="prGrid">
                    @foreach ($prestasi as $item)
                        @php
                            $rankClass = in_array($item['peringkat'], [1, 2, 3]) ? 'r' . $item['peringkat'] : '';
                            $icon = $item['peringkat'] === 1 ? 'fa-trophy' : ($item['peringkat'] > 0 ? 'fa-medal' : 'fa-star');
                            $inisial = strtoupper(mb_substr($item['nama'], 0, 1));
                        @endphp
                        <article class="pr-card {{ $rankClass }}"
                            data-cat="{{ $item['kategori'] }}"
                            data-year="{{ $item['tahun'] }}"
                            data-text="{{ strtolower($item['judul'] . ' ' . $item['nama'] . ' ' . $item['kelas']) }}">
                            <div class="pr-card-top">
                                <span class="pr-medal"><i class="fa-solid {{ $icon }}"></i></span>
                                <div class="pr-rank"><b>{{ $item['label'] }}</b><span>{{ $item['tahun'] }}</span></div>
                            </div>
                            <div class="pr-body">
                                <div class="pr-tags">
                                    <span class="pr-tag">{{ $item['kategori'] }}</span>
                                    <span class="pr-tag gold">Tingkat {{ $item['tingkat'] }}</span>
                                </div>
                                <h3>{{ $item['judul'] }}</h3>
                                <div class="pr-meta">
                                    <span><i class="fa-solid fa-location-dot"></i>{{ $item['wilayah'] }}</span>
                                    <span><i class="fa-regular fa-calendar"></i>{{ $item['tahun'] }}</span>
                                </div>
                                <p class="desc">{{ $item['deskripsi'] }}</p>
                                <div class="pr-who">
                                    <span class="pr-avatar">{{ $inisial }}</span>
                                    <div><b>{{ $item['nama'] }}</b><span>Kelas {{ $item['kelas'] }}</span></div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pr-empty" id="prEmpty">
                    <i class="fa-regular fa-face-frown-open"></i>
                    <h3>Prestasi tidak ditemukan</h3>
                    <p>Coba ubah kata kunci, bidang lomba, atau tahun yang dipilih.</p>
                </div>

            </div>
        </section>

        {{-- AJAKAN --}}
        <section class="pr-cta">
            <div class="container">
                <div class="pr-cta-box">
                    <div>
                        <h2>Jadilah bagian dari santri berprestasi.</h2>
                        <p>Pendaftaran santri baru terbuka. Daftarkan putra-putri Anda dan biarkan mereka tumbuh bersama keluarga besar Babul Hasanah.</p>
                    </div>
                    <a href="{{ route('pendaftaran.index') }}" class="pr-btn">Daftar Santri Baru <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:12px"></i></a>
                </div>
            </div>
        </section>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cards = document.querySelectorAll('#prGrid .pr-card');
            const chips = document.querySelectorAll('#prChips .pr-chip');
            const search = document.getElementById('prSearch');
            const year = document.getElementById('prYear');
            const shown = document.getElementById('prShown');
            const empty = document.getElementById('prEmpty');
            let activeCat = 'all';

            function applyFilter() {
                const q = search.value.trim().toLowerCase();
                const y = year.value;
                let visible = 0;

                cards.forEach(card => {
                    const okCat = activeCat === 'all' || card.dataset.cat === activeCat;
                    const okYear = y === 'all' || card.dataset.year === y;
                    const okText = q === '' || card.dataset.text.includes(q);
                    const show = okCat && okYear && okText;
                    card.classList.toggle('hide', !show);
                    if (show) visible++;
                });

                shown.textContent = visible;
                empty.classList.toggle('show', visible === 0);
            }

            chips.forEach(chip => {
                chip.addEventListener('click', () => {
                    chips.forEach(c => c.classList.remove('active'));
                    chip.classList.add('active');
                    activeCat = chip.dataset.cat;
                    applyFilter();
                });
            });
            search.addEventListener('input', applyFilter);
            year.addEventListener('change', applyFilter);

            // Animasi muncul saat scroll
            const obs = new IntersectionObserver((entries) => {
                entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); obs.unobserve(e.target); } });
            }, { threshold: 0.1 });
            document.querySelectorAll('.pr .reveal').forEach((el, i) => {
                el.style.transitionDelay = ((i % 4) * 70) + 'ms';
                obs.observe(el);
            });
            cards.forEach((el, i) => {
                el.classList.add('reveal');
                el.style.transitionDelay = ((i % 3) * 70) + 'ms';
                obs.observe(el);
            });
        });
    </script>
@endsection
