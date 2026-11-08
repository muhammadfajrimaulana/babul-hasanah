@extends('layouts.app')

@section('title', 'Alumni - Pondok Pesantren Babul Hasanah')

@section('content')

    <style>
        .al {
            --green: #17402f; --green-2: #1d4d3a; --cream: #f1eee6; --paper: #fbfaf6; --gold: #b7791f;
            --ink: #1f2a24; --muted: #6b756f; --line: #e4e0d4; --emerald: #12b886; --emerald-d: #0e9f73;
            --radius: 22px; --shadow: 0 10px 30px rgba(23, 64, 47, .07); --shadow-lg: 0 22px 50px rgba(23, 64, 47, .14);
            font-family: 'Poppins', system-ui, sans-serif; color: var(--ink); font-size: 15px; line-height: 1.6;
            background:
                radial-gradient(900px 500px at 100% -5%, rgba(183, 121, 31, .07), transparent 60%),
                radial-gradient(800px 500px at -10% 30%, rgba(23, 64, 47, .05), transparent 60%),
                var(--paper);
        }
        .al *, .al *::before, .al *::after { box-sizing: border-box; }
        .al a { color: inherit; text-decoration: none; }
        .al h1, .al h2, .al h3, .al h4 { margin: 0; font-weight: 600; line-height: 1.25; color: var(--green); letter-spacing: -.01em; }
        .al p { margin: 0; }
        .al .container { width: min(1180px, 92%); margin-inline: auto; }
        .al .eyebrow {
            display: flex; align-items: center; gap: 12px; font-size: 11px; letter-spacing: .12em; text-transform: uppercase;
            color: var(--gold); font-weight: 600; margin-bottom: 14px;
        }
        .al .eyebrow::before { content: ""; width: 28px; height: 1px; background: var(--gold); }
        .al .eyebrow.light { color: #e6d3a3; }
        .al .eyebrow.light::before { background: #e6d3a3; }

        /* ---------- HEADER HALAMAN ---------- */
        .al-hero {
            position: relative; overflow: hidden; color: #fff; padding: 70px 0 90px;
            background: linear-gradient(130deg, var(--green), var(--green-2));
        }
        .al-hero::before {
            content: ""; position: absolute; inset: 0; opacity: .08;
            background-image: radial-gradient(#d1fae5 1px, transparent 1px); background-size: 18px 18px;
        }
        .al-hero::after {
            content: "\f501"; font-family: "Font Awesome 6 Free"; font-weight: 900; position: absolute;
            right: 6%; top: 50%; transform: translateY(-50%) rotate(-10deg); font-size: 220px; color: rgba(255, 255, 255, .06);
        }
        .al-hero .container { position: relative; z-index: 1; }
        .al-crumb { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #b9d0c4; margin-bottom: 22px; }
        .al-crumb a:hover { color: #fff; }
        .al-hero h1 { color: #fff; font-size: clamp(30px, 4.6vw, 52px); font-weight: 700; margin-bottom: 14px; }
        .al-hero h1 span { color: #19d68f; }
        .al-hero p.lead { max-width: 640px; color: rgba(255, 255, 255, .85); font-size: clamp(13.5px, 1.4vw, 16px); }

        /* ---------- RINGKASAN ---------- */
        .al-stats { margin-top: -48px; position: relative; z-index: 2; }
        .al-stats-grid {
            display: grid; grid-template-columns: repeat(4, 1fr); background: #fff; border: 1px solid var(--line);
            border-radius: var(--radius); box-shadow: var(--shadow-lg); overflow: hidden;
        }
        .al-stat { padding: 24px 26px; display: flex; align-items: center; gap: 16px; border-left: 1px solid var(--line); }
        .al-stat:first-child { border-left: 0; }
        .al-stat .ic {
            width: 46px; height: 46px; border-radius: 14px; background: var(--cream); color: var(--green);
            display: grid; place-items: center; font-size: 18px; flex: none;
        }
        .al-stat:nth-child(2) .ic { background: rgba(183, 121, 31, .12); color: var(--gold); }
        .al-stat:nth-child(3) .ic { background: rgba(18, 184, 134, .12); color: var(--emerald-d); }
        .al-stat b { display: block; font-size: 26px; font-weight: 600; color: var(--green); line-height: 1.1; }
        .al-stat span { font-size: 11.5px; color: var(--muted); }

        /* ---------- TOOLBAR FILTER ---------- */
        .al-main { padding: 56px 0 70px; }
        .al-toolbar { display: flex; flex-direction: column; gap: 18px; margin-bottom: 32px; }
        .al-toolbar-top { display: flex; justify-content: space-between; align-items: flex-end; gap: 20px; flex-wrap: wrap; }
        .al-toolbar h2 { font-size: clamp(24px, 3vw, 34px); }
        .al-controls { display: flex; gap: 12px; flex-wrap: wrap; }
        .al-field { position: relative; }
        .al-field i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 13px; pointer-events: none; }
        .al-input, .al-select {
            font-family: inherit; font-size: 13px; color: var(--ink); background: #fff; border: 1px solid var(--line);
            border-radius: 999px; padding: 11px 18px; outline: none; transition: all .2s ease;
        }
        .al-input { padding-left: 40px; width: 260px; }
        .al-select { padding-right: 36px; cursor: pointer; appearance: none; -webkit-appearance: none;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b756f' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'><path d='m6 9 6 6 6-6'/></svg>");
            background-repeat: no-repeat; background-position: right 16px center; }
        .al-input:focus, .al-select:focus { border-color: var(--emerald); box-shadow: 0 0 0 3px rgba(18, 184, 134, .15); }

        .al-chips { display: flex; gap: 8px; flex-wrap: wrap; }
        .al-chip {
            font-family: inherit; font-size: 12px; font-weight: 500; color: var(--muted); background: #fff;
            border: 1px solid var(--line); border-radius: 999px; padding: 8px 18px; cursor: pointer; transition: all .2s ease;
        }
        .al-chip:hover { border-color: var(--green); color: var(--green); }
        .al-chip.active { background: var(--green); border-color: var(--green); color: #fff; }
        .al-count { font-size: 12px; color: var(--muted); }
        .al-count b { color: var(--green); }

        /* ---------- KARTU ALUMNI ---------- */
        .al-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; }
        .al-card {
            background: #fff; border: 1px solid var(--line); border-radius: var(--radius); overflow: hidden;
            display: flex; flex-direction: column; transition: all .3s ease;
        }
        .al-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); border-color: var(--gold); }
        .al-card.hide { display: none; }
        .al-media {
            position: relative; height: 200px; display: grid; place-items: center; overflow: hidden;
            background: linear-gradient(135deg, #5d7f66, #2f4a3a);
        }
        .al-media::before {
            content: ""; position: absolute; inset: 0; opacity: .12;
            background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 14px 14px;
        }
        .al-media img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
        .al-initials {
            position: relative; z-index: 1; width: 88px; height: 88px; border-radius: 50%;
            display: grid; place-items: center; font-size: 34px; font-weight: 600; color: #fff;
            background: rgba(255, 255, 255, .14); border: 1px solid rgba(255, 255, 255, .38);
        }
        .al-badge {
            position: absolute; z-index: 2; top: 15px; left: 15px; display: inline-flex; align-items: center; gap: 7px;
            font-size: 10.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--green);
            background: rgba(255, 255, 255, .94); border-radius: 999px; padding: 6px 12px; box-shadow: 0 6px 16px rgba(0, 0, 0, .12);
        }
        .al-badge i { color: var(--gold); font-size: 10px; }
        .al-year {
            position: absolute; z-index: 2; right: 15px; bottom: 15px; font-size: 11px; font-weight: 600; color: #fff;
            background: rgba(15, 32, 24, .55); border: 1px solid rgba(255, 255, 255, .25); border-radius: 999px;
            padding: 5px 12px; backdrop-filter: blur(4px);
        }

        .al-body { padding: 22px; display: flex; flex-direction: column; gap: 9px; flex: 1; }
        .al-tags { display: flex; gap: 6px; flex-wrap: wrap; }
        .al-tag {
            font-size: 10px; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; padding: 4px 10px;
            border-radius: 999px; background: var(--cream); color: var(--green);
        }
        .al-tag.gold { background: rgba(183, 121, 31, .12); color: var(--gold); }
        .al-body h3 { font-size: 17.5px; }
        .al-instansi { display: flex; align-items: flex-start; gap: 8px; font-size: 12.5px; color: var(--muted); }
        .al-instansi i { color: var(--gold); margin-top: 4px; font-size: 12px; }
        .al-quote {
            margin: 4px 0 0; padding: 12px 15px; background: var(--cream); border-left: 3px solid var(--gold);
            border-radius: 0 12px 12px 0; font-size: 12px; line-height: 1.65; color: var(--muted);
            font-style: italic; flex: 1;
        }

        .al-empty { display: none; text-align: center; padding: 60px 20px; background: #fff; border: 1px dashed var(--line); border-radius: var(--radius); }
        .al-empty.show { display: block; }
        .al-empty i { font-size: 34px; color: var(--gold); margin-bottom: 12px; }
        .al-empty h3 { font-size: 18px; margin-bottom: 6px; }
        .al-empty p { font-size: 13px; color: var(--muted); }

        /* ---------- AJAKAN ---------- */
        .al-cta { padding: 0 0 80px; }
        .al-cta-box {
            background: linear-gradient(135deg, var(--green), var(--green-2)); border-radius: 32px; padding: 50px; color: #fff;
            display: flex; justify-content: space-between; align-items: center; gap: 30px; flex-wrap: wrap; box-shadow: var(--shadow-lg);
        }
        .al-cta-box h2 { color: #fff; font-size: clamp(22px, 3vw, 32px); margin-bottom: 8px; }
        .al-cta-box p { font-size: 13px; color: #b9d0c4; max-width: 520px; }
        .al-btn {
            display: inline-flex; align-items: center; gap: 10px; padding: 14px 28px; border-radius: 999px; font-size: 13.5px;
            font-weight: 600; background: var(--cream); color: var(--green); transition: all .25s ease; white-space: nowrap;
        }
        .al-btn:hover { background: #fff; transform: translateY(-2px); box-shadow: 0 10px 22px rgba(0, 0, 0, .18); }

        /* animasi muncul */
        .al .reveal { opacity: 0; transform: translateY(22px); transition: opacity .7s ease, transform .7s cubic-bezier(.2, .7, .2, 1); }
        .al .reveal.in { opacity: 1; transform: none; }

        @media (max-width: 1024px) {
            .al-grid { grid-template-columns: repeat(2, 1fr); }
            .al-stats-grid { grid-template-columns: repeat(2, 1fr); }
            .al-stat:nth-child(3) { border-left: 0; }
            .al-stat:nth-child(n+3) { border-top: 1px solid var(--line); }
        }
        @media (max-width: 640px) {
            .al-hero { padding: 48px 0 80px; }
            .al-hero::after { font-size: 130px; right: -10px; }
            .al-grid { grid-template-columns: 1fr; }
            .al-stat { padding: 18px 16px; gap: 12px; }
            .al-stat .ic { width: 40px; height: 40px; font-size: 16px; }
            .al-stat b { font-size: 22px; }
            .al-controls { width: 100%; }
            .al-field, .al-select { flex: 1; }
            .al-input { width: 100%; }
            .al-select { width: 100%; }
            .al-chips { flex-wrap: nowrap; overflow-x: auto; padding-bottom: 6px; margin-inline: -4%; padding-inline: 4%; scrollbar-width: none; }
            .al-chips::-webkit-scrollbar { display: none; }
            .al-chip { white-space: nowrap; flex: none; }
            .al-cta-box { padding: 30px 22px; border-radius: 22px; }
            .al-btn { width: 100%; justify-content: center; }
        }
        @media (prefers-reduced-motion: reduce) { .al .reveal { transition: none !important; opacity: 1 !important; transform: none !important; } }
    </style>

    <div class="al">

        {{-- HEADER HALAMAN --}}
        <section class="al-hero">
            <div class="container">
                <div class="al-crumb">
                    <a href="{{ url('/') }}">Beranda</a>
                    <i class="fa-solid fa-chevron-right" style="font-size:9px"></i>
                    <span>Alumni</span>
                </div>
                <div class="eyebrow light">Jejak Alumni</div>
                <h1>Tumbuh di sini, <span>berkarya di mana-mana.</span></h1>
                <p class="lead">Kisah para alumni Babul Hasanah yang melanjutkan langkah — menuntut ilmu, bekerja, berwirausaha, dan mengabdi untuk umat.</p>
            </div>
        </section>

        {{-- RINGKASAN --}}
        <section class="al-stats">
            <div class="container">
                <div class="al-stats-grid">
                    <div class="al-stat reveal"><span class="ic"><i class="fa-solid fa-user-graduate"></i></span><div><b data-count="{{ $statistik['total'] }}">{{ $statistik['total'] }}</b><span>Total alumni</span></div></div>
                    <div class="al-stat reveal"><span class="ic"><i class="fa-solid fa-layer-group"></i></span><div><b data-count="{{ $statistik['angkatan'] }}">{{ $statistik['angkatan'] }}</b><span>Angkatan lulus</span></div></div>
                    <div class="al-stat reveal"><span class="ic"><i class="fa-solid fa-graduation-cap"></i></span><div><b data-count="{{ $statistik['kuliah'] }}">{{ $statistik['kuliah'] }}</b><span>Melanjutkan kuliah</span></div></div>
                    <div class="al-stat reveal"><span class="ic"><i class="fa-solid fa-briefcase"></i></span><div><b data-count="{{ $statistik['bekerja'] }}">{{ $statistik['bekerja'] }}</b><span>Bekerja &amp; wirausaha</span></div></div>
                </div>
            </div>
        </section>

        {{-- DAFTAR ALUMNI --}}
        <section class="al-main">
            <div class="container">

                <div class="al-toolbar">
                    <div class="al-toolbar-top">
                        <div>
                            <div class="eyebrow">Direktori Alumni</div>
                            <h2>Keluarga besar yang terus bertumbuh</h2>
                        </div>
                        <div class="al-controls">
                            <div class="al-field">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="search" id="alSearch" class="al-input" placeholder="Cari nama atau instansi...">
                            </div>
                            <select id="alYear" class="al-select" aria-label="Filter angkatan">
                                <option value="all">Semua angkatan</option>
                                @foreach ($angkatanList as $tahun)
                                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="al-chips" id="alChips">
                        <button type="button" class="al-chip active" data-keg="all">Semua</button>
                        @foreach ($kegiatanList as $kegiatan)
                            <button type="button" class="al-chip" data-keg="{{ $kegiatan }}">{{ $kegiatan }}</button>
                        @endforeach
                    </div>

                    <div class="al-count">Menampilkan <b id="alShown">{{ $alumni->count() }}</b> dari {{ $alumni->count() }} alumni</div>
                </div>

                <div class="al-grid" id="alGrid">
                    @forelse ($alumni as $item)
                        @php
                            $ikonKegiatan = match ($item['kegiatan']) {
                                'Kuliah'    => 'fa-graduation-cap',
                                'Bekerja'   => 'fa-briefcase',
                                'Wirausaha' => 'fa-store',
                                'Mengabdi'  => 'fa-hands-praying',
                                default     => 'fa-compass',
                            };
                        @endphp
                        <article class="al-card"
                            data-keg="{{ $item['kegiatan'] ?? '' }}"
                            data-year="{{ $item['tahun_lulus'] }}"
                            data-text="{{ strtolower($item['nama'] . ' ' . ($item['instansi'] ?? '') . ' ' . ($item['jenjang'] ?? '')) }}">
                            <div class="al-media">
                                <span class="al-initials" aria-hidden="true">{{ $item['inisial'] }}</span>

                                @if ($item['foto'])
                                    <img src="{{ $item['foto'] }}" alt="Foto {{ $item['nama'] }}" loading="lazy"
                                        onerror="this.remove()">
                                @endif

                                @if ($item['kegiatan'])
                                    <span class="al-badge"><i class="fa-solid {{ $ikonKegiatan }}"></i>{{ $item['kegiatan'] }}</span>
                                @endif

                                <span class="al-year">Lulus {{ $item['tahun_lulus'] }}</span>
                            </div>

                            <div class="al-body">
                                <div class="al-tags">
                                    @if ($item['jenjang'])
                                        <span class="al-tag">{{ $item['jenjang'] }}</span>
                                    @endif
                                    <span class="al-tag gold">Angkatan {{ $item['tahun_lulus'] }}</span>
                                </div>

                                <h3>{{ $item['nama'] }}</h3>

                                @if ($item['instansi'])
                                    <p class="al-instansi">
                                        <i class="fa-solid {{ $item['kegiatan'] === 'Kuliah' ? 'fa-graduation-cap' : 'fa-location-dot' }}"></i>
                                        <span>{{ $item['instansi'] }}</span>
                                    </p>
                                @endif

                                @if ($item['testimoni'])
                                    <blockquote class="al-quote">&ldquo;{{ $item['testimoni'] }}&rdquo;</blockquote>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="al-empty show" style="grid-column: 1 / -1;">
                            <i class="fa-regular fa-folder-open"></i>
                            <h3>Belum ada data alumni</h3>
                            <p>Data alumni akan tampil di sini setelah ditambahkan melalui dashboard admin.</p>
                        </div>
                    @endforelse
                </div>

                @if ($alumni->isNotEmpty())
                    <div class="al-empty" id="alEmpty">
                        <i class="fa-regular fa-face-frown-open"></i>
                        <h3>Alumni tidak ditemukan</h3>
                        <p>Coba ubah kata kunci, angkatan, atau kegiatan yang dipilih.</p>
                    </div>
                @endif

            </div>
        </section>

        {{-- AJAKAN --}}
        <section class="al-cta">
            <div class="container">
                <div class="al-cta-box">
                    <div>
                        <h2>Jadilah bagian dari jejak berikutnya.</h2>
                        <p>Pendaftaran santri baru terbuka. Daftarkan putra-putri Anda dan biarkan mereka tumbuh bersama keluarga besar Babul Hasanah.</p>
                    </div>
                    <a href="{{ route('pendaftaran.index') }}" class="al-btn">Daftar Santri Baru <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:12px"></i></a>
                </div>
            </div>
        </section>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cards = document.querySelectorAll('#alGrid .al-card');
            const chips = document.querySelectorAll('#alChips .al-chip');
            const search = document.getElementById('alSearch');
            const year = document.getElementById('alYear');
            const shown = document.getElementById('alShown');
            const empty = document.getElementById('alEmpty');
            let activeKeg = 'all';

            function applyFilter() {
                const q = search.value.trim().toLowerCase();
                const y = year.value;
                let visible = 0;

                cards.forEach(card => {
                    const okKeg = activeKeg === 'all' || card.dataset.keg === activeKeg;
                    const okYear = y === 'all' || card.dataset.year === y;
                    const okText = q === '' || card.dataset.text.includes(q);
                    const show = okKeg && okYear && okText;
                    card.classList.toggle('hide', !show);
                    if (show) visible++;
                });

                shown.textContent = visible;
                if (empty) empty.classList.toggle('show', visible === 0);
            }

            chips.forEach(chip => {
                chip.addEventListener('click', () => {
                    chips.forEach(c => c.classList.remove('active'));
                    chip.classList.add('active');
                    activeKeg = chip.dataset.keg;
                    applyFilter();
                });
            });
            search.addEventListener('input', applyFilter);
            year.addEventListener('change', applyFilter);

            // Animasi muncul saat scroll
            const obs = new IntersectionObserver((entries) => {
                entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); obs.unobserve(e.target); } });
            }, { threshold: 0.1 });
            document.querySelectorAll('.al .reveal').forEach((el, i) => {
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
