@extends('layouts.app')

@section('title', 'Info Harian - Kabar & Jadwal Pesantren Hari Ini | Babul Hasanah')

@section('content')

    <style>
        .ihp {
            --green: #17402f; --green-2: #1d4d3a; --cream: #f1eee6; --paper: #fbfaf6; --gold: #b7791f;
            --ink: #1f2a24; --muted: #6b756f; --line: #e4e0d4; --emerald: #12b886; --emerald-d: #0e9f73;
            --radius: 22px; --shadow: 0 10px 30px rgba(23, 64, 47, .07); --shadow-lg: 0 22px 50px rgba(23, 64, 47, .14);
            font-family: 'Poppins', system-ui, sans-serif; color: var(--ink); font-size: 15px; line-height: 1.6;
            background:
                radial-gradient(900px 500px at 100% -5%, rgba(183, 121, 31, .07), transparent 60%),
                radial-gradient(800px 500px at -10% 30%, rgba(23, 64, 47, .05), transparent 60%),
                var(--paper);
        }
        .ihp *, .ihp *::before, .ihp *::after { box-sizing: border-box; }
        .ihp a { color: inherit; text-decoration: none; }
        .ihp h1, .ihp h2, .ihp h3, .ihp h4 { margin: 0; font-weight: 600; line-height: 1.25; color: var(--green); letter-spacing: -.01em; }
        .ihp p { margin: 0; }
        .ihp ul { list-style: none; margin: 0; padding: 0; }
        .ihp .container { width: min(1180px, 92%); margin-inline: auto; }
        .ihp .eyebrow {
            display: flex; align-items: center; gap: 12px; font-size: 11px; letter-spacing: .12em;
            text-transform: uppercase; color: var(--gold); font-weight: 600; margin-bottom: 14px;
        }
        .ihp .eyebrow::before { content: ""; width: 28px; height: 1px; background: var(--gold); }

        /* ================= HERO ================= */
        .ihp-hero {
            position: relative; overflow: hidden; color: #fff; padding: 64px 0 104px;
            background: linear-gradient(130deg, var(--green) 0%, var(--green-2) 60%, #245c45 100%);
        }
        .ihp-hero::before {
            content: ""; position: absolute; inset: 0; opacity: .09;
            background-image: radial-gradient(#d1fae5 1px, transparent 1px); background-size: 18px 18px;
        }
        .ihp-hero::after {
            content: "\f0a1"; font-family: "Font Awesome 6 Free"; font-weight: 900; position: absolute;
            right: 3%; top: 50%; transform: translateY(-50%) rotate(-12deg);
            font-size: clamp(120px, 18vw, 240px); color: rgba(255, 255, 255, .07); pointer-events: none;
        }
        .ihp-hero .container { position: relative; z-index: 1; }
        .ihp-crumb { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #b9d0c4; margin-bottom: 22px; flex-wrap: wrap; }
        .ihp-crumb a:hover { color: #fff; }
        .ihp-crumb i { font-size: 8px; }
        .ihp-badge {
            display: inline-flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 600;
            letter-spacing: .1em; text-transform: uppercase; color: #e6d3a3;
            background: rgba(255, 255, 255, .1); border: 1px solid rgba(255, 255, 255, .18);
            padding: 7px 14px; border-radius: 999px; margin-bottom: 18px;
        }
        .ihp-badge .live {
            width: 7px; height: 7px; border-radius: 50%; background: #19d68f;
            box-shadow: 0 0 0 0 rgba(25, 214, 143, .7); animation: ihpPulse 2s infinite;
        }
        @keyframes ihpPulse {
            0% { box-shadow: 0 0 0 0 rgba(25, 214, 143, .6); }
            70% { box-shadow: 0 0 0 9px rgba(25, 214, 143, 0); }
            100% { box-shadow: 0 0 0 0 rgba(25, 214, 143, 0); }
        }
        .ihp-hero h1 { color: #fff; font-size: clamp(28px, 4.4vw, 50px); font-weight: 700; margin-bottom: 16px; max-width: 760px; }
        .ihp-hero h1 span { color: #19d68f; }
        .ihp-hero p.lead { max-width: 640px; color: rgba(255, 255, 255, .85); font-size: clamp(13.5px, 1.4vw, 16px); }
        .ihp-hero-meta { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 26px; }
        .ihp-meta {
            display: inline-flex; align-items: center; gap: 9px; font-size: 12px; font-weight: 500;
            color: #dcece3; background: rgba(255, 255, 255, .08); border: 1px solid rgba(255, 255, 255, .16);
            padding: 9px 15px; border-radius: 999px;
        }
        .ihp-meta i { color: #19d68f; font-size: 12px; }
        .ihp-meta b { color: #fff; font-weight: 600; }

        /* ================= STATISTIK ================= */
        .ihp-stats { margin-top: -56px; position: relative; z-index: 2; }
        .ihp-stats-grid {
            display: grid; grid-template-columns: repeat(4, 1fr); background: #fff; border: 1px solid var(--line);
            border-radius: var(--radius); box-shadow: var(--shadow-lg); overflow: hidden;
        }
        .ihp-stat { padding: 22px 24px; display: flex; align-items: center; gap: 15px; border-left: 1px solid var(--line); }
        .ihp-stat:first-child { border-left: 0; }
        .ihp-stat .ic {
            width: 44px; height: 44px; border-radius: 14px; background: var(--cream); color: var(--green);
            display: grid; place-items: center; font-size: 17px; flex: none;
        }
        .ihp-stat:nth-child(2) .ic { background: rgba(18, 184, 134, .12); color: var(--emerald-d); }
        .ihp-stat:nth-child(3) .ic { background: rgba(183, 121, 31, .12); color: var(--gold); }
        .ihp-stat:nth-child(4) .ic { background: rgba(56, 132, 255, .1); color: #3884ff; }
        .ihp-stat b { display: block; font-size: 25px; font-weight: 600; color: var(--green); line-height: 1.1; }
        .ihp-stat span { font-size: 11.5px; color: var(--muted); }

        /* ================= KONTEN ================= */
        .ihp-main { padding: 54px 0 20px; }
        .ihp-layout { display: grid; grid-template-columns: .78fr 1.22fr; gap: 26px; align-items: start; }

        /* ----- Panel jadwal ----- */
        .ihp-panel {
            background: linear-gradient(165deg, var(--green), var(--green-2)); color: #fff;
            border-radius: 26px; padding: 26px; box-shadow: var(--shadow-lg); position: sticky; top: 92px;
        }
        .ihp-panel-top { display: flex; align-items: center; gap: 13px; margin-bottom: 6px; }
        .ihp-panel-top > i {
            width: 44px; height: 44px; border-radius: 14px; background: rgba(255, 255, 255, .12);
            display: grid; place-items: center; color: #19d68f; font-size: 18px; flex: none;
        }
        .ihp-panel-top small { font-size: 10px; letter-spacing: .12em; text-transform: uppercase; color: #a9c4b6; display: block; }
        .ihp-panel-top h2 { color: #fff; font-size: 19px; }
        .ihp-panel-date {
            font-size: 11.5px; color: #cfe0d6; padding: 8px 0 18px; border-bottom: 1px solid rgba(255, 255, 255, .14);
            margin-bottom: 18px; display: flex; align-items: center; gap: 8px;
        }
        .ihp-panel-date i { color: #e6d3a3; }

        .ihp-tl li {
            display: flex; gap: 13px; position: relative; padding-bottom: 20px;
        }
        .ihp-tl li:last-child { padding-bottom: 0; }
        .ihp-tl li:not(:last-child)::after {
            content: ""; position: absolute; left: 6px; top: 19px; bottom: 0; width: 2px;
            background: linear-gradient(rgba(255, 255, 255, .25), rgba(255, 255, 255, .06));
        }
        .ihp-tl-dot {
            width: 14px; height: 14px; border-radius: 50%; border: 3px solid rgba(255, 255, 255, .3);
            background: var(--green-2); flex: none; margin-top: 3px; position: relative; z-index: 1;
        }
        .ihp-tl li.past { opacity: .48; }
        .ihp-tl li.next .ihp-tl-dot {
            border-color: #19d68f; background: #19d68f; box-shadow: 0 0 0 5px rgba(25, 214, 143, .18);
        }
        .ihp-tl-body b { display: block; font-size: 14.5px; font-weight: 600; color: #e6d3a3; line-height: 1.3; }
        .ihp-tl li.next .ihp-tl-body b { color: #19d68f; }
        .ihp-tl-body span { font-size: 12.5px; color: #d8e8df; }
        .ihp-tl-tag {
            display: inline-block; margin-top: 5px; font-size: 9.5px; font-weight: 700; letter-spacing: .07em;
            text-transform: uppercase; background: rgba(25, 214, 143, .18); color: #19d68f;
            border-radius: 999px; padding: 2px 8px;
        }
        .ihp-panel-foot {
            margin-top: 22px; padding-top: 18px; border-top: 1px solid rgba(255, 255, 255, .14);
            font-size: 11.5px; color: #a9c4b6; display: flex; align-items: center; gap: 8px;
        }
        .ihp-panel-foot i { color: #e6d3a3; }

        /* ----- Toolbar ----- */
        .ihp-toolbar {
            background: #fff; border: 1px solid var(--line); border-radius: var(--radius);
            padding: 18px; display: flex; flex-direction: column; gap: 14px; box-shadow: var(--shadow); margin-bottom: 20px;
        }
        .ihp-search { position: relative; }
        .ihp-search i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 13px; pointer-events: none; }
        .ihp-input {
            width: 100%; font-family: inherit; font-size: 13px; color: var(--ink); background: #fff;
            border: 1px solid var(--line); border-radius: 999px; padding: 12px 18px 12px 40px;
            outline: none; transition: all .2s ease;
        }
        .ihp-input:focus { border-color: var(--emerald); box-shadow: 0 0 0 3px rgba(18, 184, 134, .15); }
        .ihp-chips { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
        .ihp-chip {
            font-family: inherit; font-size: 12px; font-weight: 500; color: var(--muted); background: #fff;
            border: 1px solid var(--line); border-radius: 999px; padding: 8px 17px; cursor: pointer; transition: all .2s ease;
        }
        .ihp-chip:hover { border-color: var(--green); color: var(--green); }
        .ihp-chip.active { background: var(--green); border-color: var(--green); color: #fff; }
        .ihp-count { margin-left: auto; font-size: 12px; color: var(--muted); }
        .ihp-count b { color: var(--green); }

        /* ----- Info penting ----- */
        .ihp-ticker {
            display: flex; align-items: center; gap: 14px; background: #fff; border: 1px solid var(--line);
            border-left: 4px solid var(--emerald); border-radius: 16px; padding: 13px 18px;
            margin-bottom: 20px; box-shadow: var(--shadow); overflow: hidden;
        }
        .ihp-ticker-label {
            display: inline-flex; align-items: center; gap: 7px; font-size: 10px; font-weight: 700;
            letter-spacing: .08em; text-transform: uppercase; color: var(--emerald-d); flex: none;
        }
        .ihp-ticker marquee { font-size: 12.5px; color: var(--ink); }

        /* ----- Kartu info ----- */
        .ihp-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        .ihp-card {
            background: #fff; border: 1px solid var(--line); border-radius: var(--radius); padding: 20px;
            display: flex; flex-direction: column; gap: 9px; position: relative; overflow: hidden;
            transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;
        }
        .ihp-card::before {
            content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--line);
            transition: background .3s ease;
        }
        .ihp-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); border-color: var(--gold); }
        .ihp-card.hide { display: none; }
        .ihp-card[data-cat="Pengumuman"]::before { background: var(--gold); }
        .ihp-card[data-cat="Jadwal"]::before { background: var(--emerald); }
        .ihp-card[data-cat="Agenda"]::before { background: #3884ff; }
        .ihp-card[data-cat="Libur"]::before { background: #e8597b; }
        .ihp-card-top { display: flex; justify-content: space-between; align-items: center; gap: 10px; }
        .ihp-chip-cat {
            display: inline-flex; align-items: center; gap: 8px; font-size: 10px; letter-spacing: .08em;
            text-transform: uppercase; font-weight: 600; color: var(--gold);
        }
        .ihp-chip-cat i {
            width: 28px; height: 28px; border-radius: 9px; background: rgba(183, 121, 31, .1);
            display: grid; place-items: center; font-size: 12px;
        }
        .ihp-card[data-cat="Jadwal"] .ihp-chip-cat { color: var(--emerald-d); }
        .ihp-card[data-cat="Jadwal"] .ihp-chip-cat i { background: rgba(18, 184, 134, .12); }
        .ihp-card[data-cat="Agenda"] .ihp-chip-cat { color: #2f6fd0; }
        .ihp-card[data-cat="Agenda"] .ihp-chip-cat i { background: rgba(56, 132, 255, .12); }
        .ihp-card[data-cat="Libur"] .ihp-chip-cat { color: #c73b60; }
        .ihp-card[data-cat="Libur"] .ihp-chip-cat i { background: rgba(232, 89, 123, .12); }
        .ihp-pin {
            font-size: 9.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
            background: rgba(18, 184, 134, .12); color: var(--emerald-d); padding: 3px 9px; border-radius: 999px;
            white-space: nowrap;
        }
        .ihp-card h3 { font-size: 15px; line-height: 1.45; }
        .ihp-card p { font-size: 12.5px; color: var(--muted); flex: 1; }
        .ihp-time {
            font-size: 11px; color: var(--muted); display: flex; align-items: center; gap: 6px;
            padding-top: 10px; border-top: 1px dashed var(--line);
        }
        .ihp-time i { color: var(--gold); }
        .ihp-empty {
            grid-column: 1 / -1; text-align: center; padding: 54px 22px; background: #fff;
            border: 1px dashed var(--line); border-radius: var(--radius);
        }
        .ihp-empty i { font-size: 34px; color: var(--line); margin-bottom: 12px; display: block; }
        .ihp-empty h3 { font-size: 16px; margin-bottom: 6px; }
        .ihp-empty p { font-size: 12.5px; color: var(--muted); }
        .ihp-hide { display: none !important; }

        /* ================= HISTORI INFO ================= */
        .ihp-history { padding: 10px 0 34px; }
        .ihp-history-head {
            display: flex; justify-content: space-between; align-items: flex-end; gap: 20px;
            flex-wrap: wrap; margin-bottom: 22px;
        }
        .ihp-history-head h2 { font-size: clamp(20px, 2.7vw, 30px); margin-bottom: 7px; }
        .ihp-history-head p { font-size: 12.5px; color: var(--muted); max-width: 580px; }
        .ihp-history-count {
            display: inline-flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 600;
            color: var(--muted); background: #fff; border: 1px solid var(--line);
            border-radius: 999px; padding: 9px 16px; flex: none;
        }
        .ihp-history-count i { color: var(--gold); }
        .ihp-card.is-old { background: #fcfbf7; border-style: dashed; }
        .ihp-card.is-old::before { opacity: .38; }
        .ihp-card.is-old:hover { transform: none; box-shadow: var(--shadow); border-color: var(--line); }
        .ihp-card.is-old h3 { color: #5f6b64; }
        .ihp-old {
            font-size: 9.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
            background: #efece2; color: #8a8477; padding: 3px 9px; border-radius: 999px; white-space: nowrap;
        }

        /* ================= CTA ================= */
        .ihp-cta { padding: 50px 0 70px; }
        .ihp-cta-box {
            position: relative; overflow: hidden; border-radius: 30px; padding: 44px 46px; color: #fff;
            background: linear-gradient(120deg, var(--green), #2a6a50);
            display: flex; align-items: center; justify-content: space-between; gap: 30px; flex-wrap: wrap;
        }
        .ihp-cta-box::after {
            content: "\f19d"; font-family: "Font Awesome 6 Free"; font-weight: 900; position: absolute;
            right: 4%; bottom: -30px; font-size: 160px; color: rgba(255, 255, 255, .07); pointer-events: none;
        }
        .ihp-cta-box h2 { color: #fff; font-size: clamp(21px, 2.6vw, 30px); margin-bottom: 8px; max-width: 620px; }
        .ihp-cta-box p { color: rgba(255, 255, 255, .8); font-size: 13px; max-width: 560px; }
        .ihp-cta-actions { display: flex; gap: 10px; flex-wrap: wrap; position: relative; z-index: 1; }
        .ihp-btn {
            display: inline-flex; align-items: center; gap: 9px; font-family: inherit; font-size: 13px;
            font-weight: 600; padding: 13px 22px; border-radius: 13px; transition: all .25s ease; white-space: nowrap;
        }
        /* Spesifisitas dinaikkan (.ihp .ihp-btn-*) agar tidak tertimpa rule `.ihp a { color: inherit; }` */
        .ihp .ihp-btn-light { background: #fff; color: #111; box-shadow: 0 10px 24px rgba(0, 0, 0, .16); }
        .ihp .ihp-btn-light:hover { transform: translateY(-2px); color: #111; }
        .ihp .ihp-btn-ghost { border: 1px solid rgba(255, 255, 255, .35); color: #fff; }
        .ihp .ihp-btn-ghost:hover { background: rgba(255, 255, 255, .12); color: #fff; }

        /* ================= REVEAL ================= */
        .ihp .reveal { opacity: 0; transform: translateY(16px); transition: opacity .6s ease, transform .6s ease; }
        .ihp .reveal.in { opacity: 1; transform: none; }
        @media (prefers-reduced-motion: reduce) {
            .ihp .reveal { opacity: 1 !important; transform: none !important; transition: none !important; }
            .ihp-badge .live { animation: none; }
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 1024px) {
            .ihp-layout { grid-template-columns: 1fr; }
            .ihp-panel { position: static; }
            .ihp-stats-grid { grid-template-columns: 1fr 1fr; }
            .ihp-stat:nth-child(3) { border-left: 0; }
            .ihp-stat:nth-child(-n+2) { border-bottom: 1px solid var(--line); }
        }
        @media (max-width: 700px) {
            .ihp-hero { padding: 48px 0 92px; }
            .ihp-grid { grid-template-columns: 1fr; }
            .ihp-cta-box { padding: 32px 26px; }
            .ihp-count { margin-left: 0; width: 100%; }
        }
        @media (max-width: 520px) {
            .ihp-stats-grid { grid-template-columns: 1fr; }
            .ihp-stat { border-left: 0; border-bottom: 1px solid var(--line); }
            .ihp-stat:last-child { border-bottom: 0; }
            .ihp-panel { padding: 22px; }
        }
    </style>

    <div class="ihp">

        {{-- ================= HERO ================= --}}
        <section class="ihp-hero">
            <div class="container">
                <nav class="ihp-crumb" aria-label="Breadcrumb">
                    <a href="{{ url('/') }}">Beranda</a>
                    <i class="fa-solid fa-chevron-right"></i>
                    <span>Info Harian</span>
                </nav>

                <span class="ihp-badge"><span class="live"></span> Diperbarui setiap hari</span>

                <h1>Kabar &amp; jadwal pesantren <span>hari ini.</span></h1>
                <p class="lead">
                    Semua pengumuman, agenda, dan jadwal kegiatan Pondok Pesantren Babul Hasanah dalam satu halaman.
                    Pastikan Anda tidak melewatkan informasi terbaru dari pesantren.
                </p>

                <div class="ihp-hero-meta">
                    <span class="ihp-meta"><i class="fa-regular fa-calendar"></i> {{ $tanggalHariIni }}</span>
                    <span class="ihp-meta"><i class="fa-regular fa-clock"></i> Sekarang <b id="ihpJam">--:--:--</b> WIB</span>
                    <span class="ihp-meta"><i class="fa-solid fa-bullhorn"></i> <b>{{ $info->count() }}</b> kabar aktif</span>
                    <span class="ihp-meta"><i class="fa-solid fa-list-check"></i> <b>{{ $jadwal->count() }}</b> jadwal kegiatan</span>
                </div>
            </div>
        </section>

        {{-- ================= STATISTIK ================= --}}
        <section class="ihp-stats">
            <div class="container">
                <div class="ihp-stats-grid">
                    <div class="ihp-stat">
                        <span class="ic"><i class="fa-solid fa-bullhorn"></i></span>
                        <div><b>{{ $info->count() }}</b><span>Total Kabar</span></div>
                    </div>
                    <div class="ihp-stat">
                        <span class="ic"><i class="fa-solid fa-thumbtack"></i></span>
                        <div><b>{{ $infoPenting->count() }}</b><span>Info Penting</span></div>
                    </div>
                    <div class="ihp-stat">
                        <span class="ic"><i class="fa-regular fa-clock"></i></span>
                        <div><b>{{ $jadwal->count() }}</b><span>Jadwal Kegiatan</span></div>
                    </div>
                    <div class="ihp-stat">
                        <span class="ic"><i class="fa-solid fa-tags"></i></span>
                        <div><b>{{ $kategori->count() ?: '-' }}</b><span>Kategori Info</span></div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================= KONTEN ================= --}}
        <section class="ihp-main">
            <div class="container">
                <div class="ihp-layout">

                    {{-- ----- Jadwal hari ini ----- --}}
                    <aside class="ihp-panel">
                        <div class="ihp-panel-top">
                            <i class="fa-regular fa-clock"></i>
                            <div>
                                <small>Jadwal</small>
                                <h2>Hari Ini</h2>
                            </div>
                        </div>
                        <div class="ihp-panel-date">
                            <i class="fa-regular fa-calendar"></i> {{ $tanggalHariIni }}
                        </div>

                        <ul class="ihp-tl">
                            @forelse ($jadwal as $j)
                                @php
                                    $jamItem = str_replace(':', '.', (string) $j->jam);
                                @endphp
                                <li class="{{ $jamItem < $jamSekarang ? 'past' : '' }} {{ $jadwalBerikutnya && $j->id === $jadwalBerikutnya->id ? 'next' : '' }}">
                                    <span class="ihp-tl-dot"></span>
                                    <div class="ihp-tl-body">
                                        <b>{{ $j->jam }}</b>
                                        <span>{{ $j->judul }}</span>
                                        @if ($jadwalBerikutnya && $j->id === $jadwalBerikutnya->id)
                                            <em class="ihp-tl-tag">Berikutnya</em>
                                        @endif
                                    </div>
                                </li>
                            @empty
                                <li>
                                    <span class="ihp-tl-dot"></span>
                                    <div class="ihp-tl-body">
                                        <b>--.--</b>
                                        <span>Belum ada jadwal yang dipublikasikan.</span>
                                    </div>
                                </li>
                            @endforelse
                        </ul>

                        <div class="ihp-panel-foot">
                            <i class="fa-solid fa-circle-info"></i>
                            <span>Jadwal dapat berubah sewaktu-waktu. Ikuti arahan pengurus pesantren.</span>
                        </div>
                    </aside>

                    {{-- ----- Daftar info ----- --}}
                    <div>
                        @if ($infoPenting->isNotEmpty())
                            <div class="ihp-ticker">
                                <span class="ihp-ticker-label"><i class="fa-solid fa-thumbtack"></i> Penting</span>
                                <marquee scrollamount="4">
                                    {{ $infoPenting->pluck('judul')->implode('   •   ') }}
                                </marquee>
                            </div>
                        @endif

                        <div class="ihp-toolbar">
                            <div class="ihp-search">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" id="ihpSearch" class="ihp-input"
                                    placeholder="Cari pengumuman, agenda, atau kegiatan..." autocomplete="off"
                                    aria-label="Cari info harian">
                            </div>

                            <div class="ihp-chips" id="ihpChips">
                                <button type="button" class="ihp-chip active" data-cat="all">Semua</button>
                                @foreach ($kategori as $kat)
                                    <button type="button" class="ihp-chip" data-cat="{{ $kat }}">{{ $kat }}</button>
                                @endforeach
                                <span class="ihp-count"><b id="ihpShown">{{ $info->count() }}</b> info</span>
                            </div>
                        </div>

                        <div class="ihp-grid" id="ihpGrid">
                            @forelse ($info as $item)
                                <article class="ihp-card reveal"
                                    data-cat="{{ $item->kategori }}"
                                    data-text="{{ \Illuminate\Support\Str::lower($item->judul . ' ' . $item->isi . ' ' . $item->kategori) }}"
                                    style="transition-delay: {{ ($loop->index % 4) * 70 }}ms">
                                    <div class="ihp-card-top">
                                        <span class="ihp-chip-cat">
                                            <i class="fa-solid {{ $item->ikon }}"></i>{{ $item->kategori ?: 'Info' }}
                                        </span>
                                        @if ($item->penting)
                                            <span class="ihp-pin">Penting</span>
                                        @endif
                                    </div>

                                    <h3>{{ $item->judul }}</h3>

                                    @if ($item->isi)
                                        <p>{{ $item->isi }}</p>
                                    @endif

                                    <div class="ihp-time">
                                        <i class="fa-regular fa-clock"></i>
                                        {{ $item->waktu_label ?: 'Tanpa batas waktu' }}
                                        @if ($item->berlaku_sampai && ! $item->kedaluwarsa)
                                            <span>&middot; berlaku s.d. {{ $item->berlaku_sampai->translatedFormat('d M Y') }}</span>
                                        @endif
                                    </div>
                                </article>
                            @empty
                                <div class="ihp-empty">
                                    <i class="fa-regular fa-bell"></i>
                                    <h3>Belum ada info hari ini</h3>
                                    <p>Pengumuman dan agenda pesantren akan tampil di sini begitu dipublikasikan.</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- Pesan saat filter tidak menemukan hasil --}}
                        @if ($info->isNotEmpty())
                            <div class="ihp-empty ihp-hide" id="ihpEmpty" style="margin-top:18px">
                                <i class="fa-regular fa-face-frown-open"></i>
                                <h3>Info tidak ditemukan</h3>
                                <p>Coba gunakan kata kunci lain atau pilih kategori yang berbeda.</p>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </section>

        {{-- ================= HISTORI INFO KEDALUWARSA ================= --}}
        @if ($infoHistori->isNotEmpty())
            <section class="ihp-history">
                <div class="container">
                    <div class="ihp-history-head">
                        <div>
                            <div class="eyebrow">Arsip</div>
                            <h2>Histori info yang sudah berakhir.</h2>
                            <p>
                                Info yang masa berlakunya sudah lewat tetap bisa dibaca selama {{ $historiHari }} hari.
                                Setelah itu, info otomatis tidak ditampilkan lagi di halaman ini.
                            </p>
                        </div>
                        <span class="ihp-history-count">
                            <i class="fa-solid fa-clock-rotate-left"></i> {{ $infoHistori->count() }} info
                        </span>
                    </div>

                    <div class="ihp-grid">
                        @foreach ($infoHistori as $item)
                            <article class="ihp-card is-old" data-cat="{{ $item->kategori }}">
                                <div class="ihp-card-top">
                                    <span class="ihp-chip-cat">
                                        <i class="fa-solid {{ $item->ikon }}"></i>{{ $item->kategori ?: 'Info' }}
                                    </span>
                                    <span class="ihp-old">Kedaluwarsa</span>
                                </div>

                                <h3>{{ $item->judul }}</h3>

                                @if ($item->isi)
                                    <p>{{ $item->isi }}</p>
                                @endif

                                <div class="ihp-time">
                                    <i class="fa-regular fa-calendar"></i>
                                    Berakhir {{ $item->berlaku_sampai->translatedFormat('d M Y') }}
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- ================= CTA ================= --}}
        <section class="ihp-cta">
            <div class="container">
                <div class="ihp-cta-box">
                    <div>
                        <h2>Ingin putra-putri Anda tumbuh bersama Babul Hasanah?</h2>
                        <p>Pendaftaran santri baru sudah dibuka. Bergabunglah dengan keluarga besar pesantren dan
                            ikuti seluruh kegiatan yang telah kami jadwalkan.</p>
                    </div>
                    <div class="ihp-cta-actions">
                        <a href="{{ route('pendaftaran.index') }}" class="ihp-btn ihp-btn-light">
                            Daftar Santri Baru <i class="fa-solid fa-arrow-right" style="font-size:11px"></i>
                        </a>
                        <a href="{{ route('berita.index') }}" class="ihp-btn ihp-btn-ghost">
                            Baca Berita <i class="fa-solid fa-newspaper" style="font-size:11px"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            /* ---------- Jam berjalan ---------- */
            const jamEl = document.getElementById('ihpJam');
            if (jamEl) {
                const tick = () => {
                    jamEl.textContent = new Intl.DateTimeFormat('id-ID', {
                        hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
                    }).format(new Date());
                };
                tick();
                setInterval(tick, 1000);
            }

            /* ---------- Filter kategori & pencarian ---------- */
            const cards = Array.from(document.querySelectorAll('#ihpGrid .ihp-card'));
            const chips = document.querySelectorAll('#ihpChips .ihp-chip');
            const search = document.getElementById('ihpSearch');
            const shown = document.getElementById('ihpShown');
            const empty = document.getElementById('ihpEmpty');
            let activeCat = 'all';

            function applyFilter() {
                const q = (search?.value || '').trim().toLowerCase();
                let visible = 0;

                cards.forEach(card => {
                    const okCat = activeCat === 'all' || card.dataset.cat === activeCat;
                    const okText = q === '' || (card.dataset.text || '').includes(q);
                    const show = okCat && okText;
                    card.classList.toggle('hide', !show);
                    if (show) visible++;
                });

                if (shown) shown.textContent = visible;
                if (empty) empty.classList.toggle('ihp-hide', visible !== 0);
            }

            chips.forEach(chip => {
                chip.addEventListener('click', () => {
                    chips.forEach(c => c.classList.remove('active'));
                    chip.classList.add('active');
                    activeCat = chip.dataset.cat;
                    applyFilter();
                });
            });

            search?.addEventListener('input', applyFilter);

            /* ---------- Animasi muncul saat scroll ---------- */
            const obs = new IntersectionObserver((entries) => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        e.target.classList.add('in');
                        obs.unobserve(e.target);
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.ihp .reveal').forEach(el => obs.observe(el));
        });
    </script>
@endsection
