@extends('layouts.app')

@section('title', 'Pondok Pesantren Babul Hasanah')

@section('content')

    {{-- Font & Swiper (fungsi slider program unggulan tetap dipakai) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .dh {
            --green: #17402f;
            --green-2: #1d4d3a;
            --cream: #f1eee6;
            --paper: #fbfaf6;
            --gold: #b7791f;
            --ink: #1f2a24;
            --muted: #6b756f;
            --line: #e4e0d4;
            --emerald: #12b886;
            --emerald-d: #0e9f73;
            --emerald-l: #2fe0a0;
            --radius: 22px;
            --sec: 44px; /* jarak vertikal antar-section */
            --shadow: 0 10px 30px rgba(23, 64, 47, 0.07);
            --shadow-lg: 0 22px 50px rgba(23, 64, 47, 0.14);
            font-family: 'Poppins', system-ui, -apple-system, sans-serif;
            color: var(--ink);
            font-size: 15px;
            line-height: 1.6;
            overflow-x: hidden;
            background:
                radial-gradient(900px 500px at 100% -5%, rgba(183, 121, 31, 0.07), transparent 60%),
                radial-gradient(800px 500px at -10% 30%, rgba(23, 64, 47, 0.05), transparent 60%),
                var(--paper);
        }
        .dh *, .dh *::before, .dh *::after { box-sizing: border-box; }
        .dh a { color: inherit; text-decoration: none; }
        .dh h1, .dh h2, .dh h3, .dh h4, .dh h5 {
            font-weight: 600; line-height: 1.25; color: var(--green); letter-spacing: -0.01em; margin: 0;
        }
        .dh p { margin: 0; }
        .dh .container { width: min(1180px, 92%); margin-inline: auto; }

        .dh .eyebrow {
            display: flex; align-items: center; gap: 12px; font-size: 11px; letter-spacing: .12em;
            text-transform: uppercase; color: var(--gold); font-weight: 600; margin-bottom: 16px;
        }
        .dh .eyebrow::before { content: ""; width: 28px; height: 1px; background: var(--gold); }
        .dh .eyebrow.light { color: #e6d3a3; }
        .dh .eyebrow.light::before { background: #e6d3a3; }

        /* Placeholder gambar */
        .dh .ph {
            background: linear-gradient(135deg, #8a9a7b, #4f6b52 60%, #2f4a3a);
            position: relative; overflow: hidden; background-size: cover; background-position: center;
            border-radius: var(--radius);
        }
        .dh .ph.b { background: linear-gradient(135deg, #c9b98f, #8f7a4e 60%, #5b4a2c); }
        .dh .ph.c { background: linear-gradient(135deg, #9db39a, #5d7f66 60%, #34503f); }
        .dh .ph.d { background: linear-gradient(135deg, #d7c7a3, #a98e5b 60%, #6e5731); }

        /* Tombol */
        .dh .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 10px;
            padding: 13px 24px; border-radius: 999px; font-size: 13.5px; font-weight: 500;
            border: 1px solid transparent; cursor: pointer; transition: all .25s ease;
            font-family: inherit; white-space: nowrap;
        }
        .dh .btn:hover { transform: translateY(-2px); box-shadow: 0 10px 22px rgba(23, 64, 47, .15); }
        .dh .btn i { transition: transform .25s ease; font-size: 12px; }
        .dh .btn:hover i { transform: translate(2px, -2px); }
        .dh .btn-primary { background: var(--green); color: #fff; }
        .dh .btn-primary:hover { background: #0f2f22; }
        .dh .btn-outline { background: transparent; border-color: var(--line); color: var(--green); }
        .dh .btn-outline:hover { border-color: var(--green); background: #fff; }
        .dh .btn-light { background: var(--cream); color: var(--green); }
        .dh .btn-light:hover { background: #fff; }
        .dh .btn-emerald {
            background: var(--emerald); color: #fff; border-radius: 12px; font-weight: 600;
            box-shadow: 0 8px 20px rgba(18, 184, 134, .28);
        }
        .dh .btn-emerald:hover { background: var(--emerald-d); }
        .dh .btn-glass {
            background: rgba(18, 26, 42, .72); color: #fff; border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 12px; font-weight: 600;
        }
        .dh .btn-glass:hover { background: rgba(18, 26, 42, .9); }
        .dh .btn-lg { padding: 16px 32px; font-size: 15px; }

        /* ---------- HERO ---------- */
        .dh .hero2 {
            position: relative; display: flex; align-items: center; min-height: min(720px, calc(100vh - 120px));
            color: #fff; overflow: hidden; background: #12261c; z-index: 1;
        }
        .dh .hero2-slides { position: absolute; inset: 0; z-index: 0; overflow: hidden; }
        .dh .hero2-slide {
            position: absolute; inset: 0; opacity: 0; transform: scale(1.02);
            background-image: var(--hero-img, none),
                radial-gradient(900px 500px at 78% 30%, rgba(255, 255, 255, .10), transparent 60%),
                linear-gradient(135deg, #557a5b 0%, #2f4f3c 50%, #183024 100%);
            background-size: cover; background-position: center;
            transition: opacity 1.1s ease, transform 7s ease-out;
            will-change: opacity, transform;
        }
        .dh .hero2-slide.is-active { opacity: 1; transform: scale(1.12); }
        .dh .hero2-nav {
            position: absolute; z-index: 3; right: max(4%, 20px); bottom: 24px;
            display: flex; align-items: center; gap: 10px;
            background: rgba(9, 24, 17, .45); border: 1px solid rgba(255, 255, 255, .16);
            border-radius: 999px; padding: 7px 9px; backdrop-filter: blur(8px);
        }
        .dh .hero2-arrow {
            width: 30px; height: 30px; border-radius: 50%; border: 0; padding: 0; cursor: pointer;
            display: grid; place-items: center; flex: none; font-size: 10px;
            background: rgba(255, 255, 255, .14); color: #fff; transition: background .25s ease, transform .25s ease;
        }
        .dh .hero2-arrow:hover { background: rgba(255, 255, 255, .3); transform: scale(1.06); }
        .dh .hero2-dots { display: flex; align-items: center; gap: 6px; }
        .dh .hero2-dot {
            width: 20px; height: 4px; border-radius: 999px; border: 0; padding: 0; cursor: pointer;
            background: rgba(255, 255, 255, .32); transition: background .3s ease, width .3s ease;
        }
        .dh .hero2-dot:hover { background: rgba(255, 255, 255, .6); }
        .dh .hero2-dot.is-active { background: #19d68f; width: 36px; }
        .dh .hero2::before {
            content: ""; position: absolute; inset: 0; z-index: 1;
            background:
                linear-gradient(90deg, rgba(24, 12, 26, .75) 0%, rgba(14, 28, 22, .55) 55%, rgba(10, 22, 16, .35) 100%),
                linear-gradient(0deg, rgba(6, 18, 12, .6), transparent 50%);
        }
        .dh .hero2 .container { position: relative; z-index: 2; padding: 80px 0; }
        .dh .hero2-badge {
            display: inline-flex; padding: 10px 20px; margin-bottom: 24px; border-radius: 999px;
            border: 1px solid rgba(18, 184, 134, .5); background: rgba(18, 184, 134, .2);
            color: var(--emerald-l); font-size: 12px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase;
        }
        .dh .hero2 h1 {
            color: #fff; font-size: clamp(32px, 5vw, 62px); font-weight: 700; line-height: 1.18;
            max-width: 800px; margin-bottom: 24px;
        }
        .dh .hero2 h1 .accent { color: #19d68f; }
        .dh .hero2 p.lead {
            max-width: 620px; font-size: clamp(14px, 1.4vw, 17px); line-height: 1.7;
            color: rgba(255, 255, 255, .88); margin-bottom: 36px;
        }
        .dh .hero2-cta { display: flex; gap: 16px; flex-wrap: wrap; }

        /* ---------- INFO / MARQUEE ---------- */
        .dh .info-bar { background: var(--green-2); padding: 14px 0; }
        .dh .info-inner {
            display: flex; align-items: center; gap: 12px; background: rgba(255, 255, 255, .07);
            border: 1px solid rgba(255, 255, 255, .14); border-radius: 999px; padding: 8px 10px 8px 8px; overflow: hidden;
        }
        .dh .info-tag {
            display: inline-flex; align-items: center; gap: 8px; background: var(--emerald); color: #fff;
            font-size: 11px; font-weight: 700; padding: 6px 14px; border-radius: 999px; flex: none; letter-spacing: .04em;
        }
        .dh .info-inner marquee { color: #d8e8df; font-size: 13px; }

        /* ---------- STATS ---------- */
        .dh .stats { background: linear-gradient(120deg, var(--green) 0%, var(--green-2) 100%); color: #fff; padding: 36px 0; }
        .dh .stats .container { display: grid; grid-template-columns: 1.2fr repeat(4, 1fr); gap: 24px; align-items: center; }
        .dh .stats small { font-size: 10px; letter-spacing: .1em; text-transform: uppercase; color: #a9c4b6; display: block; }
        .dh .stats .since { font-size: 20px; font-weight: 600; }
        .dh .stat { border-left: 1px solid rgba(255, 255, 255, .18); padding-left: 20px; transition: transform .3s ease; display: block; }
        .dh .stat:hover { transform: translateY(-3px); }
        .dh .stat b { font-size: 28px; font-weight: 600; display: block; line-height: 1.1; }
        .dh .stat span { font-size: 11px; color: #a9c4b6; }

        /* ---------- PROFIL + SAMBUTAN PIMPINAN ---------- */
        .dh .profile { padding: 56px 0 var(--sec); }
        .dh .profile-grid { display: grid; grid-template-columns: .9fr 1.1fr; gap: 70px; align-items: center; }

        /* Foto pimpinan (portrait 4:5, wajah tetap terlihat) */
        .dh .profile-img { position: relative; aspect-ratio: 4 / 5; }
        .dh .profile-img .ph { position: absolute; inset: 0; box-shadow: var(--shadow-lg); padding: 0; }
        .dh .profile-img .ph img {
            width: 100%; height: 100%; display: block;
            object-fit: cover; object-position: center 15%;
        }

        .dh .badge-year {
            position: absolute; right: -20px; bottom: -20px; background: #fff; padding: 16px 20px;
            border-radius: 16px; max-width: 190px; box-shadow: var(--shadow-lg); z-index: 2;
        }
        .dh .badge-year b { font-size: 26px; color: var(--green); display: block; line-height: 1; }
        .dh .badge-year span { font-size: 10.5px; color: var(--muted); line-height: 1.3; display: block; margin-top: 4px; }
        .dh .profile h2 { font-size: clamp(26px, 3.2vw, 38px); margin-bottom: 18px; }

        /* Sambutan pimpinan */
        .dh .sambutan h2 { margin-bottom: 20px; }
        .dh .sambutan-quote { font-size: 26px; color: var(--gold); opacity: .35; margin-bottom: 8px; display: block; }
        .dh .sambutan-text { font-size: 13.5px; color: var(--muted); line-height: 1.8; margin-bottom: 14px; }
        .dh .sambutan-text em { color: var(--green); font-style: italic; }
        .dh .sambutan-sign {
            margin-top: 22px; padding-top: 16px; border-top: 1px solid var(--line);
            display: flex; flex-direction: column; gap: 2px;
        }
        .dh .sambutan-sign strong { font-size: 15px; color: var(--green); font-weight: 600; }
        .dh .sambutan-sign span { font-size: 11.5px; color: var(--gold); font-weight: 600; }

        .dh .features { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-top: 48px; }
        .dh .feature {
            display: flex; gap: 16px; align-items: flex-start; background: #fff; border: 1px solid var(--line);
            border-radius: var(--radius); padding: 20px; transition: transform .3s ease, box-shadow .3s ease;
        }
        .dh .feature:hover { transform: translateY(-4px); box-shadow: var(--shadow); }
        .dh .feature .box {
            width: 40px; height: 40px; border-radius: 12px; background: var(--cream);
            display: grid; place-items: center; color: var(--green); flex: none;
        }
        .dh .feature h3 { font-size: 14px; margin-bottom: 4px; }
        .dh .feature p { font-size: 11.5px; color: var(--muted); }

        /* ---------- JENJANG / LEVELS ---------- */
        .dh .levels { background: var(--cream); padding: var(--sec) 0; border-radius: 36px; margin: 0 1.5%; }
        .dh .section-title { font-size: clamp(26px, 3.4vw, 38px); margin-bottom: 12px; }
        .dh .section-sub { color: var(--muted); font-size: 13.5px; margin-bottom: 36px; max-width: 600px; }
        .dh .level-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; }
        .dh .level {
            background: #fff; border: 1px solid var(--line); border-radius: var(--radius); padding: 22px;
            display: flex; flex-direction: column; gap: 8px; transition: all .3s ease;
        }
        .dh .level:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); border-color: var(--gold); }
        .dh .level .top {
            display: flex; justify-content: space-between; align-items: center; font-size: 9px;
            letter-spacing: .1em; text-transform: uppercase; color: var(--gold); font-weight: 600;
        }
        .dh .level h3 { font-size: 20px; margin-top: 6px; }
        .dh .level .sub { font-size: 11px; color: var(--ink); font-weight: 500; }
        .dh .level p { font-size: 11.5px; color: var(--muted); flex: 1; margin-bottom: 12px; }
        .dh .level .dur {
            font-size: 11px; color: var(--muted); display: flex; align-items: center; gap: 6px;
            border-top: 1px solid var(--line); padding-top: 12px;
        }
        .dh .level-cta {
            background: linear-gradient(150deg, var(--green-2), var(--green)); color: #fff; border-radius: var(--radius);
            padding: 24px; display: flex; flex-direction: column; gap: 12px; justify-content: center; box-shadow: var(--shadow);
        }
        .dh .level-cta h3 { color: #fff; font-size: 18px; }
        .dh .level-cta p { font-size: 11.5px; color: #b9d0c4; }

        /* ---------- STRUKTUR ORGANISASI (BERANDA) ---------- */
        .dh .org { padding: 0 0 var(--sec); }
        .dh .org-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; }
        .dh .org-card {
            background: #fff; border: 1px solid var(--line); border-radius: var(--radius); padding: 26px 20px;
            display: flex; flex-direction: column; align-items: center; text-align: center; gap: 6px;
            transition: all .3s ease;
        }
        .dh .org-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); border-color: var(--gold); }
        .dh .org-card .avatar {
            width: 96px; height: 96px; border-radius: 50%; margin-bottom: 10px; border: 3px solid var(--cream);
            display: grid; place-items: center; color: rgba(255, 255, 255, .85); font-size: 30px;
        }
        .dh .org-card h3 { font-size: 15px; }
        .dh .org-card .role { font-size: 11.5px; color: var(--gold); font-weight: 600; }

        /* ---------- PROGRAM UNGGULAN (SWIPER) ---------- */
        .dh .program-sec { padding: 80px 0 40px; }
        .dh .program-sec .swiper { padding: 10px 4px 6px !important; }
        .dh .program-card {
            background: #fff; border: 1px solid var(--line); border-radius: var(--radius); overflow: hidden;
            display: flex; flex-direction: column; height: 100%; transition: all .3s ease;
        }
        .dh .program-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); border-color: var(--gold); }
        .dh .program-cover {
            position: relative; height: 150px; background-size: cover; background-position: center;
            display: flex; align-items: flex-end; padding: 18px 22px; color: #fff;
        }
        .dh .program-cover::before {
            content: ""; position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(10, 30, 20, .78), rgba(10, 30, 20, .25));
        }
        .dh .program-cover > * { position: relative; z-index: 1; }
        .dh .program-cover .pc-ico {
            position: absolute; top: 16px; right: 18px; width: 42px; height: 42px; border-radius: 12px;
            background: rgba(255, 255, 255, .18); border: 1px solid rgba(255, 255, 255, .3);
            display: grid; place-items: center; font-size: 18px;
        }
        .dh .program-cover .pc-tag { font-size: 10px; letter-spacing: .1em; text-transform: uppercase; font-weight: 600; color: #e6d3a3; }
        .dh .program-body { padding: 22px; display: flex; flex-direction: column; gap: 8px; flex: 1; }
        .dh .program-body h5 { font-size: 19px; }
        .dh .program-body p { font-size: 12px; color: var(--muted); flex: 1; margin-bottom: 12px; }
        .dh .program-body .btn { width: 100%; }
        .dh .custom-swiper-pagination {
            position: relative !important; bottom: 0 !important; margin-top: 2rem !important;
            display: flex !important; justify-content: center !important; align-items: center !important; gap: 4px;
        }
        .dh .custom-swiper-pagination .swiper-pagination-bullet { background: var(--green); opacity: .25; width: 8px; height: 8px; }
        .dh .custom-swiper-pagination .swiper-pagination-bullet-active { background: var(--emerald); opacity: 1; width: 22px; border-radius: 99px; }

        /* ---------- INFO HARIAN ---------- */
        .dh .ih-sec { padding: 0 0 var(--sec); }
        .dh .ih-shell {
            position: relative; overflow: hidden; border-radius: 32px; padding: 42px 40px 40px;
            background: linear-gradient(150deg, var(--green) 0%, var(--green-2) 60%, #2a6a50 100%);
            box-shadow: var(--shadow-lg); color: #fff;
        }
        .dh .ih-shell::before {
            content: ""; position: absolute; inset: 0; opacity: .10; pointer-events: none;
            background-image: radial-gradient(#d1fae5 1px, transparent 1px); background-size: 20px 20px;
        }
        .dh .ih-shell::after {
            content: "\f017"; font-family: "Font Awesome 6 Free"; font-weight: 900; position: absolute;
            right: 2%; bottom: -44px; font-size: 190px; line-height: 1; color: rgba(255, 255, 255, .05);
            pointer-events: none;
        }
        .dh .ih-shell > * { position: relative; z-index: 1; }
        .dh .ih-shell .eyebrow { color: #e6d3a3; }
        .dh .ih-shell .eyebrow::before { background: #e6d3a3; }
        .dh .ih-shell .section-title { color: #fff; }
        .dh .ih-date {
            display: inline-flex; align-items: center; gap: 8px; background: rgba(255, 255, 255, .1);
            border: 1px solid rgba(255, 255, 255, .16); color: #eaf5ef;
            font-size: 12px; font-weight: 600; padding: 9px 16px; border-radius: 999px;
        }
        .dh .ih-date i { color: #19d68f; }
        .dh .ih-shell .btn-outline { color: #fff; border-color: rgba(255, 255, 255, .34); background: rgba(255, 255, 255, .06); }
        .dh .ih-shell .btn-outline:hover { background: #fff; color: var(--green); border-color: #fff; }
        .dh .ih-grid { display: grid; grid-template-columns: .9fr 1.1fr; gap: 22px; align-items: stretch; }

        /* Kotak "Jadwal Hari Ini" — putih di atas latar hijau */
        .dh .ih-today {
            background: #fff; color: var(--ink); border-radius: 22px; padding: 24px 24px 18px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, .16);
        }
        .dh .ih-today-head { display: flex; align-items: center; gap: 14px; margin-bottom: 12px; }
        .dh .ih-today-head > i {
            width: 44px; height: 44px; border-radius: 14px; background: rgba(18, 184, 134, .12);
            display: grid; place-items: center; color: var(--emerald-d); font-size: 18px;
        }
        .dh .ih-today-head small { font-size: 10px; letter-spacing: .12em; text-transform: uppercase; color: var(--gold); display: block; }
        .dh .ih-today-head h3 { color: var(--green); font-size: 20px; }
        .dh .ih-row {
            display: flex; gap: 16px; align-items: baseline; padding: 12px 0;
            border-top: 1px solid var(--line); transition: transform .25s ease;
        }
        .dh .ih-row:hover { transform: translateX(6px); }
        .dh .ih-row b { font-size: 15px; font-weight: 600; min-width: 52px; color: var(--emerald-d); }
        .dh .ih-row span { font-size: 12.5px; color: #4b5b52; }

        .dh .ih-cards { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .dh .ih-card {
            background: #fff; border: 1px solid rgba(255, 255, 255, .4); border-radius: var(--radius); padding: 20px;
            display: flex; flex-direction: column; gap: 8px; transition: all .3s ease;
        }
        .dh .ih-card:hover { transform: translateY(-5px); box-shadow: 0 16px 32px rgba(0, 0, 0, .18); border-color: var(--emerald); }
        .dh .ih-top { display: flex; justify-content: space-between; align-items: center; gap: 8px; }
        .dh .ih-chip {
            display: inline-flex; align-items: center; gap: 7px; font-size: 10px; letter-spacing: .08em;
            text-transform: uppercase; font-weight: 600; color: var(--gold);
        }
        .dh .ih-chip i {
            width: 28px; height: 28px; border-radius: 9px; background: rgba(183, 121, 31, .1);
            display: grid; place-items: center; font-size: 12px;
        }
        .dh .ih-pin {
            font-size: 9.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
            background: rgba(18, 184, 134, .12); color: var(--emerald-d); padding: 3px 9px; border-radius: 999px;
        }
        .dh .ih-card h3 { font-size: 15px; line-height: 1.4; }
        .dh .ih-card p { font-size: 12px; color: var(--muted); flex: 1; }
        .dh .ih-time { font-size: 11px; color: var(--muted); display: flex; gap: 6px; align-items: center; }
        @media (max-width: 1024px) {
            .dh .ih-grid { grid-template-columns: 1fr; }
            .dh .ih-shell { padding: 34px 30px 30px; }
        }
        @media (max-width: 640px) {
            .dh .ih-cards { grid-template-columns: 1fr; }
            .dh .ih-shell { padding: 26px 20px 24px; border-radius: 22px; }
            .dh .ih-shell::after { display: none; }
            .dh .ih-today { padding: 20px 18px 14px; }
        }

        /* ---------- BERITA ---------- */
        .dh .news { padding: var(--sec) 0; }
        .dh .head-row { display: flex; justify-content: space-between; align-items: flex-end; gap: 20px; margin-bottom: 34px; }
        .dh .news-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .dh .news-card { transition: transform .3s ease; }
        .dh .news-card:hover { transform: translateY(-6px); }
        .dh .news-card .ph {
            aspect-ratio: 1.55 / 1; margin-bottom: 16px; transition: box-shadow .3s ease;
            display: flex; align-items: center; justify-content: center; color: rgba(255, 255, 255, .8); font-size: 13px; font-weight: 500;
        }
        .dh .news-card:hover .ph { box-shadow: var(--shadow-lg); }
        .dh .news-meta { display: flex; justify-content: space-between; font-size: 10px; margin-bottom: 8px; }
        .dh .news-meta .cat { letter-spacing: .08em; text-transform: uppercase; color: var(--gold); font-weight: 600; }
        .dh .news-meta .date { color: var(--muted); }
        .dh .news-card h3 { font-size: 18px; margin-bottom: 8px; }
        .dh .news-card p { font-size: 12px; color: var(--muted); margin-bottom: 14px; }
        .dh .link-more { font-size: 12px; font-weight: 600; color: var(--green); display: inline-flex; gap: 6px; align-items: center; }
        .dh .link-more:hover { color: var(--emerald-d); }

        /* ---------- GALERI ---------- */
        .dh .gallery { padding: 0 0 var(--sec); }
        .dh .gallery-grid { display: grid; grid-template-columns: 1.8fr 1fr 1fr; gap: 20px; }
        .dh .g-item { margin: 0; }
        .dh .g-item .ph { height: 300px; position: relative; transition: transform .4s ease, box-shadow .3s ease; }
        .dh .g-item:hover .ph { transform: scale(1.01); box-shadow: var(--shadow-lg); }
        .dh .g-item figcaption { font-size: 11px; color: var(--muted); margin-top: 8px; }
        .dh .g-video .ph { display: flex; align-items: flex-end; padding: 24px; }
        .dh .g-video .ph::before {
            content: ""; position: absolute; inset: 0; background: linear-gradient(to top, rgba(10, 30, 20, .7), transparent 60%);
        }
        .dh .g-video .play {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 58px; height: 58px;
            border-radius: 50%; background: rgba(255, 255, 255, .35); display: grid; place-items: center;
            color: #fff; border: 0; cursor: pointer; z-index: 2; transition: all .3s ease; font-size: 18px;
        }
        .dh .g-video .play:hover { transform: translate(-50%, -50%) scale(1.1); background: rgba(255, 255, 255, .6); }
        .dh .g-video .cap { position: relative; z-index: 2; }
        .dh .g-video .cap strong { display: block; font-size: 20px; color: #fff; }
        .dh .g-video .cap span { font-size: 10px; letter-spacing: .08em; color: #e8efe9; text-transform: uppercase; }
        .dh .g-item > a { display: block; }
        .dh .g-video .play { pointer-events: none; }

        /* ---------- PRESTASI ---------- */
        .dh .achv { padding: 0 0 var(--sec); }
        .dh .achv .container { display: grid; grid-template-columns: .8fr 2.2fr; gap: 40px; align-items: center; }
        .dh .achv h2 { font-size: clamp(26px, 3vw, 36px); margin-bottom: 12px; }
        .dh .achv .intro { font-size: 13px; color: var(--muted); margin-bottom: 16px; }
        .dh .achv-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .dh .achv-card {
            background: #fff; border-radius: var(--radius); padding: 22px; border: 1px solid var(--line); transition: all .3s ease;
        }
        .dh .achv-card:hover { transform: translateY(-5px); box-shadow: var(--shadow); border-color: var(--gold); }
        .dh .achv-card .ico-box {
            background: rgba(183, 121, 31, .1); color: var(--gold); width: 42px; height: 42px;
            border-radius: 12px; display: grid; place-items: center; margin-bottom: 12px;
        }
        .dh .achv-card .tag { font-size: 10px; color: var(--gold); font-weight: 600; display: block; margin-bottom: 4px; }
        .dh .achv-card h3 { font-size: 16px; margin-bottom: 6px; }
        .dh .achv-card .lvl { font-size: 11px; color: var(--muted); margin-bottom: 12px; }
        .dh .achv-card .who { font-size: 11px; color: var(--green); font-weight: 600; border-top: 1px solid var(--line); padding-top: 10px; }

        /* ---------- PENERIMAAN SANTRI BARU ---------- */
        .dh .psb { padding: 0 0 var(--sec); }
        .dh .psb-box {
            background: linear-gradient(135deg, var(--green), var(--green-2)); border-radius: 32px; padding: 50px;
            display: grid; grid-template-columns: 1.1fr 1fr; gap: 50px; color: #fff; box-shadow: var(--shadow-lg);
            position: relative; overflow: hidden;
        }
        .dh .psb-box > * { position: relative; z-index: 1; }
        .dh .psb h2 { color: #fff; font-size: clamp(26px, 3.4vw, 38px); margin-bottom: 16px; }
        .dh .psb .txt { font-size: 13px; color: #b9d0c4; margin-bottom: 24px; }
        .dh .psb .btns { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px; }
        .dh .psb .period { font-size: 11.5px; color: #cfe0d6; display: flex; gap: 8px; align-items: center; }
        .dh .steps-title { font-size: 10px; letter-spacing: .12em; text-transform: uppercase; color: #cfe0d6; margin-bottom: 16px; font-weight: 600; }
        .dh .step { display: flex; gap: 16px; padding: 14px 0; border-bottom: 1px solid rgba(255, 255, 255, .12); transition: transform .25s ease; }
        .dh .step:last-child { border: 0; }
        .dh .step:hover { transform: translateX(6px); }
        .dh .step .num {
            width: 32px; height: 32px; background: rgba(255, 255, 255, .1); border: 1px solid rgba(255, 255, 255, .25);
            border-radius: 50%; display: grid; place-items: center; font-size: 11px; flex: none; color: #cfe0d6;
        }
        .dh .step h4 { color: #fff; font-size: 13px; }
        .dh .step p { font-size: 11px; color: #b9d0c4; }

        /* ---------- KONTAK & PETA ---------- */
        .dh .contact { padding: 0 0 var(--sec); }
        .dh .contact .container { display: grid; grid-template-columns: .9fr 1.1fr; gap: 60px; align-items: start; }
        .dh .contact h2 { font-size: clamp(26px, 3.4vw, 38px); margin-bottom: 16px; }
        .dh .contact .desc { font-size: 13px; color: var(--muted); margin-bottom: 24px; }
        .dh .contact-info .row { display: flex; gap: 12px; margin-bottom: 20px; }
        .dh .contact-info .row > i { color: var(--gold); margin-top: 4px; font-size: 16px; }
        .dh .contact-info strong { font-size: 12px; display: block; color: var(--green); margin-bottom: 4px; }
        .dh .contact-info p { font-size: 12.5px; color: var(--muted); }
        .dh .contact-two { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .dh .contact-two small { font-size: 10px; color: var(--muted); display: block; }
        .dh .contact-two b { font-weight: 600; font-size: 14px; color: var(--green); }
        .dh .hours { background: var(--cream); border-radius: 999px; padding: 12px 22px; font-size: 12px; display: flex; gap: 10px; align-items: center; }
        .dh .hours i { color: var(--gold); }
        .dh .map-section { padding: 0 0 var(--sec); }
        .dh .map-wrap {
            position: relative; border-radius: 32px; overflow: hidden; box-shadow: var(--shadow-lg);
            border: 1px solid var(--line); background: var(--cream); height: 400px;
        }
        .dh .map-wrap iframe { width: 100%; height: 100%; border: 0; display: block; filter: saturate(.85); }
        .dh .map-card {
            position: absolute; left: 24px; top: 24px; background: #fff; border-radius: 20px;
            padding: 20px 22px; max-width: 320px; box-shadow: var(--shadow-lg);
        }
        .dh .map-card h3 { font-size: 18px; margin-bottom: 6px; }
        .dh .map-card p { font-size: 11.5px; color: var(--muted); margin-bottom: 14px; }

        /* ---------- TESTIMONI ALUMNI ---------- */
        .dh .alum { padding: 0 0 var(--sec); }
        .dh .alum-box {
            position: relative; overflow: hidden; border-radius: 32px; padding: 48px 40px 38px;
            background: linear-gradient(140deg, #fff 0%, var(--cream) 100%);
            border: 1px solid var(--line); box-shadow: var(--shadow);
        }
        .dh .alum-box::after {
            content: "\f10e"; font-family: "Font Awesome 6 Free"; font-weight: 900; position: absolute;
            right: 26px; top: 0; font-size: 150px; line-height: 1; color: rgba(23, 64, 47, .05);
            pointer-events: none;
        }
        .dh .alum-head {
            position: relative; z-index: 1; display: flex; align-items: flex-end; justify-content: space-between;
            gap: 24px; flex-wrap: wrap; margin-bottom: 30px;
        }
        .dh .alum-head h2 { font-size: clamp(24px, 3vw, 34px); }
        .dh .alum-head .sub { font-size: 13px; color: var(--muted); margin-top: 10px; max-width: 460px; line-height: 1.7; }
        .dh .alum-nav { display: flex; gap: 8px; }
        .dh .alum-nav button {
            width: 42px; height: 42px; border-radius: 50%; background: #fff; border: 1px solid var(--line);
            color: var(--green); display: grid; place-items: center; cursor: pointer; font-size: 13px;
            transition: background .2s ease, color .2s ease, border-color .2s ease, transform .2s ease;
        }
        .dh .alum-nav button:hover { background: var(--green); border-color: var(--green); color: #fff; transform: translateY(-2px); }

        .dh .alum-viewport { position: relative; z-index: 1; overflow: hidden; padding: 10px 4px; margin: -10px -4px; }
        .dh .alum-track {
            display: flex; gap: 20px; transition: transform .65s cubic-bezier(.22, .7, .2, 1);
            will-change: transform;
        }
        .dh .alum-card {
            flex: 0 0 calc((100% - 40px) / 3); display: flex; flex-direction: column; gap: 14px;
            background: #fff; border: 1px solid var(--line); border-radius: 22px; padding: 26px 24px 22px;
            transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease;
        }
        .dh .alum-card:hover {
            border-color: #cfe3d8; box-shadow: 0 16px 34px rgba(23, 64, 47, .1); transform: translateY(-3px);
        }
        .dh .alum-badge {
            display: inline-flex; align-items: center; gap: 6px; align-self: flex-start;
            font-size: 9.5px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
            color: var(--gold); background: rgba(183, 121, 31, .1); border-radius: 999px; padding: 4px 11px;
        }
        .dh .alum-quote { color: var(--emerald); font-size: 18px; opacity: .55; }
        .dh .alum-text {
            flex: 1; font-size: 13px; line-height: 1.85; color: #33413a; margin: 0;
            display: -webkit-box; -webkit-line-clamp: 6; -webkit-box-orient: vertical; overflow: hidden;
        }
        .dh .alum-meta { display: flex; gap: 12px; align-items: center; border-top: 1px dashed var(--line); padding-top: 16px; }
        .dh .alum-ava {
            width: 48px; height: 48px; border-radius: 50%; flex: none; object-fit: cover;
            border: 2px solid #fff; box-shadow: 0 3px 12px rgba(23, 64, 47, .18);
        }
        .dh .alum-ava.is-initial {
            display: grid; place-items: center; background: linear-gradient(140deg, var(--green), var(--green-2));
            color: #fff; font-size: 15px; font-weight: 600; letter-spacing: .02em;
        }
        .dh .alum-meta strong { display: block; font-size: 13px; color: var(--green); }
        .dh .alum-meta small { display: block; font-size: 10.5px; color: var(--muted); margin-top: 2px; }
        .dh .alum-dots { position: relative; z-index: 1; display: flex; gap: 7px; justify-content: center; margin-top: 24px; }
        .dh .alum-dot {
            width: 8px; height: 8px; padding: 0; border: 0; border-radius: 999px; background: var(--line);
            cursor: pointer; transition: width .25s ease, background .25s ease;
        }
        .dh .alum-dot.is-active { width: 26px; background: var(--emerald); }
        .dh .alum-foot { position: relative; z-index: 1; display: flex; justify-content: center; margin-top: 20px; }
        .dh .alum-box.is-static .alum-nav, .dh .alum-box.is-static .alum-dots { display: none; }

        /* ---------- ANIMASI SCROLL ---------- */
        .dh .reveal { opacity: 0; transform: translateY(24px); transition: opacity .8s ease, transform .8s cubic-bezier(.2, .7, .2, 1); }
        .dh .reveal.in { opacity: 1; transform: none; }

        /* ---------- RESPONSIVE ---------- */
        @media (max-width: 1024px) {
            .dh .profile-grid { grid-template-columns: 1fr; gap: 50px; }
            .dh .profile-img { max-width: 460px; width: 100%; margin-inline: auto; }
            .dh .stats .container { grid-template-columns: repeat(2, 1fr); gap: 20px; }
            .dh .stats .container > div:first-child { grid-column: 1 / -1; }
            .dh .stat { border-left: 0; padding-left: 0; }
            .dh .stat:nth-child(3), .dh .stat:nth-child(5) {
                border-left: 1px solid rgba(255, 255, 255, .15); padding-left: 16px;
            }
            .dh .stat:nth-child(4), .dh .stat:nth-child(5) {
                border-top: 1px solid rgba(255, 255, 255, .15); padding-top: 16px;
            }
            .dh .features, .dh .news-grid { grid-template-columns: 1fr; }
            .dh .badge-year { right: 10px; bottom: -10px; }
            .dh .levels { margin: 0; border-radius: 0; }
            .dh .head-row { flex-direction: column; align-items: flex-start; }
            .dh .level-grid, .dh .org-grid { grid-template-columns: repeat(2, 1fr); }
            .dh .achv .container, .dh .psb-box, .dh .contact .container { grid-template-columns: 1fr; gap: 40px; }
            .dh .achv-cards, .dh .gallery-grid { grid-template-columns: 1fr; }
            .dh .alum-card { flex-basis: calc((100% - 20px) / 2); }
            .dh .alum-box { padding: 40px 30px 32px; }
        }
        @media (max-width: 640px) {
            .dh { --sec: 36px; }
            .dh .hero2-cta { flex-direction: column; width: 100%; }
            .dh .hero2-cta .btn { width: 100%; }
            .dh .hero2 .container { padding: 56px 0 96px; }
            .dh .hero2-nav { left: max(4%, 16px); right: max(4%, 16px); bottom: 20px; justify-content: center; }
            .dh .hero2-dots { flex: 1; justify-content: center; }
            .dh .hero2-dot { width: 16px; }
            .dh .stats .container { gap: 18px; }
            .dh .info-inner { border-radius: 16px; }
            .dh .level-grid, .dh .org-grid, .dh .contact-two { grid-template-columns: 1fr; }
            .dh .psb-box { padding: 28px 20px; border-radius: 20px; }
            .dh .psb .btns { flex-direction: column; }
            .dh .psb .btn { width: 100%; }
            .dh .hours { border-radius: 16px; }
            .dh .map-wrap { height: auto; display: flex; flex-direction: column; }
            .dh .map-wrap iframe { height: 280px; order: 2; }
            .dh .map-card { position: static; max-width: none; border-radius: 0; box-shadow: none; order: 1; }
            .dh .alum-box { padding: 30px 20px 26px; border-radius: 22px; }
            .dh .alum-box::after { font-size: 96px; right: 12px; }
            .dh .alum-head { gap: 16px; margin-bottom: 22px; }
            .dh .alum-card { flex-basis: 100%; }
        }
        @media (prefers-reduced-motion: reduce) {
            .dh .reveal { transition: none !important; opacity: 1 !important; transform: none !important; }
            .dh .hero2-slide { transition: none !important; }
            .dh .hero2-slide.is-active { transform: none !important; }
            .dh .alum-track { transition: none !important; }
        }
    </style>

    @php
        // ----- Data dinamis dari database -----
        $beritaTerbaru = \App\Models\Berita::published()->terbaru()->take(3)->get();
        $warna = ['', 'c', 'd'];

        // Kolom diasumsikan: nama, jabatan, foto. Sesuaikan jika berbeda di model StrukturOrganisasi.
        $pengurus = \App\Models\StrukturOrganisasi::where('aktif', 1)
            ->where('kategori', 'bagan')
            ->orderBy('urutan')->orderBy('id')
            ->take(4)->get();

        // INFO HARIAN: konten section "Kabar & jadwal pesantren hari ini", dikelola lewat
        // dashboard super admin (menu Pengelolaan > Info Harian).
        // Guard hasTable membuat beranda tetap tampil selama migrasi info_harian belum dijalankan.
        $infoHarian = collect();
        $jadwalHariIni = collect();

        if (\Illuminate\Support\Facades\Schema::hasTable('info_harian')) {
            $infoHarian = \App\Models\InfoHarian::info()->aktif()->berlaku()->sudahTerbit()
                ->orderByDesc('penting')->orderByDesc('tanggal')->orderByDesc('id')
                ->take(4)->get();

            $jadwalHariIni = \App\Models\InfoHarian::jadwal()->aktif()->berlaku()
                ->orderBy('jam')->orderBy('urutan')->orderBy('id')->get();
        }

        $teksInfo = $infoHarian->where('penting', true)->pluck('judul')->implode('   •   ');

        // ----- Angka statistik section "Bersama Babul Hasanah" (dihitung langsung dari data admin) -----
        $jumlahSantri   = \Illuminate\Support\Facades\Schema::hasTable('santri')
            ? \App\Models\Santri::count() : 0;
        $jumlahPrestasi = \Illuminate\Support\Facades\Schema::hasTable('prestasi_santri')
            ? \App\Models\PrestasiSantri::count() : 0;
        $jumlahBerita   = \Illuminate\Support\Facades\Schema::hasTable('berita')
            ? \App\Models\Berita::published()->count() : 0;
        $jumlahAlumni   = \Illuminate\Support\Facades\Schema::hasTable('alumni')
            ? \App\Models\Alumni::count() : 0;

        // Prestasi: urutan sama dengan halaman /prestasi (tahun terbaru, juara 1-3 dulu, penghargaan di akhir)
        $prestasiTerbaru = \App\Models\PrestasiSantri::query()
            ->orderByDesc('tahun')
            ->orderByRaw('peringkat = 0, peringkat')
            ->orderByDesc('id')
            ->take(3)->get();

        // Galeri: ambil album terbaru (relasi `media` sama seperti di Public\GaleriController).
        // Slot besar diprioritaskan untuk album yang memuat video, sisanya 2 album terbaru.
        $albumTerbaru = \App\Models\Album::with('media')
            ->orderByDesc('tanggal')->orderByDesc('id')->take(6)->get();
        $albumUtama = $albumTerbaru->first(fn ($a) => $a->media->where('tipe', '!=', 'foto')->isNotEmpty())
            ?? $albumTerbaru->first();
        $albumLain = $albumTerbaru->reject(fn ($a) => $albumUtama && $a->id === $albumUtama->id)->take(2);
        $galeriBeranda = $albumUtama ? collect([$albumUtama])->merge($albumLain) : collect();
        $gambarDefault = asset('assets/img/banner/banner.png');

        $sampulAlbum = function ($a) use ($gambarDefault) {
            if ($a->sampul) {
                return \Illuminate\Support\Facades\Storage::url($a->sampul);
            }
            $foto = $a->media->first(fn ($m) => $m->tipe === 'foto' && $m->path);
            if ($foto) {
                return \Illuminate\Support\Facades\Storage::url($foto->path);
            }
            $poster = $a->media->first(fn ($m) => ! empty($m->poster));
            return $poster ? \Illuminate\Support\Facades\Storage::url($poster->poster) : $gambarDefault;
        };

        // Testimoni alumni: hanya data yang kolom testimoni-nya terisi (dikelola lewat
        // dashboard admin Data Alumni). Section disembunyikan bila belum ada testimoni.
        $testimoniAlumni = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('alumni')) {
            $testimoniAlumni = \App\Models\Alumni::query()
                ->whereNotNull('testimoni')
                ->where('testimoni', '<>', '')
                ->orderByDesc('tahun_lulus')
                ->orderBy('nama')
                ->take(12)
                ->get();
        }
    @endphp

    <div class="dh">

        {{-- HERO SECTION (slider 3 gambar, bergeser otomatis) --}}
        @php
            $heroSlides = [
                ['file' => 'slide-1.png', 'alt' => 'Suasana Pondok Pesantren Babul Hasanah'],
                ['file' => 'slide-2.jpg', 'alt' => 'Kegiatan belajar santri di kelas'],
                ['file' => 'slide-3.jpg', 'alt' => 'Para ustaz dan pengurus pesantren'],
            ];
        @endphp
        <section class="hero2">
            <div class="hero2-slides">
                @foreach ($heroSlides as $i => $slide)
                    <div class="hero2-slide {{ $i === 0 ? 'is-active' : '' }}"
                        style="--hero-img: url('{{ asset('assets/img/banner/' . $slide['file']) }}');"
                        role="img" aria-label="{{ $slide['alt'] }}"
                        @if ($i > 0) aria-hidden="true" @endif></div>
                @endforeach
            </div>
            <div class="container">
                <span class="hero2-badge">Selamat datang di official website</span>
                <h1>Membangun Generasi Unggul Dalam Penguasaan <span class="accent">Kitab Kuning</span></h1>
                <p class="lead">
                    Pondok Pesantren Babul Hasanah berkomitmen mendidik santri yang mendalami ilmu agama, unggul dalam
                    kitab kuning, berakhlak mulia serta berani menghadapi tantangan zaman.
                </p>
                <div class="hero2-cta">
                    <a href="{{ route('pendaftaran.index') }}" class="btn btn-emerald btn-lg">Daftar Santri Baru</a>
                    <a href="{{ route('profil.sejarah') }}" class="btn btn-glass btn-lg">Pelajari Selengkapnya</a>
                </div>
            </div>

            <div class="hero2-nav">
                <button type="button" class="hero2-arrow" data-slide="prev" aria-label="Gambar sebelumnya">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <div class="hero2-dots" role="tablist" aria-label="Pilih gambar hero">
                    @foreach ($heroSlides as $i => $slide)
                        <button type="button" class="hero2-dot {{ $i === 0 ? 'is-active' : '' }}"
                            data-slide="{{ $i }}" role="tab"
                            aria-selected="{{ $i === 0 ? 'true' : 'false' }}"
                            aria-label="Gambar {{ $i + 1 }}"></button>
                    @endforeach
                </div>
                <button type="button" class="hero2-arrow" data-slide="next" aria-label="Gambar berikutnya">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        </section>

        {{-- RUNNING TEXT / INFO BANNER --}}
        <div class="info-bar">
            <div class="container">
                <div class="info-inner">
                    <span class="info-tag"><i class="fa-solid fa-bullhorn"></i> INFO</span>
                    <marquee scrollamount="5">
                        {{ $teksInfo ?: 'Selamat datang di portal resmi Pondok Pesantren Babul Hasanah.' }}
                    </marquee>
                </div>
            </div>
        </div>

        {{-- STATISTIK --}}
        <section class="stats">
            <div class="container">
                <div>
                    <small>Bersama Babul Hasanah</small>
                    <div class="since">Mengabdi sejak 1998.</div>
                </div>
                <a href="{{ route('pendaftaran.index') }}" class="stat">
                    <b data-count="{{ $jumlahSantri }}">{{ number_format($jumlahSantri, 0, ',', '.') }}</b><span>Santri Aktif</span>
                </a>
                <a href="{{ route('prestasi.index') }}" class="stat">
                    <b data-count="{{ $jumlahPrestasi }}">{{ number_format($jumlahPrestasi, 0, ',', '.') }}</b><span>Prestasi Santri</span>
                </a>
                <a href="{{ route('berita.index') }}" class="stat">
                    <b data-count="{{ $jumlahBerita }}">{{ number_format($jumlahBerita, 0, ',', '.') }}</b><span>Berita</span>
                </a>
                <a href="{{ route('alumni.index') }}" class="stat">
                    <b data-count="{{ $jumlahAlumni }}">{{ number_format($jumlahAlumni, 0, ',', '.') }}</b><span>Alumni</span>
                </a>
            </div>
        </section>

        {{-- PROFIL PESANTREN + SAMBUTAN PIMPINAN --}}
        <section class="profile" id="profil">
            <div class="container">
                <div class="profile-grid">
                    <div class="profile-img">
                        <div class="ph c">
                            <img src="{{ asset('assets/img/banner/pimpinan.jpg') }}"
                                 alt="Pimpinan Pondok Pesantren Babul Hasanah"
                                 loading="lazy">
                        </div>
                        <div class="badge-year">
                            <b>1998</b>
                            <span>Awal sebuah ikhtiar, berlanjut lintas generasi.</span>
                        </div>
                    </div>

                    <div class="sambutan">
                        <div class="eyebrow">Sambutan Pimpinan</div>
                        <h2>Berakar pada adab.<br>Bertumbuh dengan ilmu.</h2>

                        <i class="fa-solid fa-quote-left sambutan-quote"></i>
                        <p class="sambutan-text">
                            <em>Assalamu'alaikum warahmatullahi wabarakatuh.</em> Puji syukur kita panjatkan ke hadirat Allah SWT
                            atas segala nikmat-Nya. Selamat datang di website resmi Pondok Pesantren Babul Hasanah.
                        </p>
                        <p class="sambutan-text">
                            Sejak berdiri pada 1998 di Padang Lawas, kami berikhtiar menjaga tradisi keilmuan pesantren,
                            khususnya penguasaan kitab kuning dan Al-Qur'an, sambil membekali santri dengan pendidikan yang
                            relevan bagi masa depan. Bagi kami, ilmu tanpa adab tidak sempurna, dan adab tanpa ilmu tidak
                            cukup untuk berkhidmat kepada umat.
                        </p>
                        <p class="sambutan-text">
                            Kepada para orang tua, kami mengucapkan terima kasih atas amanah yang dipercayakan. Semoga
                            Babul Hasanah terus menjadi tempat tumbuhnya generasi berilmu, berakhlak mulia, dan berdaya guna.
                            <em>Wassalamu'alaikum warahmatullahi wabarakatuh.</em>
                        </p>

                        <div class="sambutan-sign">
                            <strong>[ Nama Pimpinan ]</strong>
                            <span>Pimpinan Pondok Pesantren Babul Hasanah</span>
                        </div>
                    </div>
                </div>

                <div class="features">
                    <div class="feature">
                        <span class="box"><i class="fa-solid fa-book-quran"></i></span>
                        <div><h3>Al-Qur'an &amp; Kitab Kuning</h3><p>Tahsin, tahfiz, dan kajian turats dengan bimbingan asatidz.</p></div>
                    </div>
                    <div class="feature">
                        <span class="box"><i class="fa-solid fa-graduation-cap"></i></span>
                        <div><h3>Ilmu &amp; Kecakapan Hidup</h3><p>Pendidikan formal, bahasa, dan ruang untuk berkarya.</p></div>
                    </div>
                    <div class="feature">
                        <span class="box"><i class="fa-solid fa-hand-holding-heart"></i></span>
                        <div><h3>Adab Keseharian</h3><p>Pembiasaan ibadah, kemandirian, dan kepedulian sosial.</p></div>
                    </div>
                </div>
            </div>
        </section>

        {{-- INFO HARIAN --}}
        <section class="ih-sec" id="info">
            <div class="container">
                <div class="ih-shell">
                    <div class="eyebrow">Info Harian</div>
                    <div class="head-row">
                        <h2 class="section-title" style="margin-bottom:0">Kabar &amp; jadwal pesantren hari ini.</h2>
                        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                            <span class="ih-date"><i class="fa-regular fa-calendar"></i> {{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
                            <a href="{{ route('info-harian.index') }}" class="btn btn-outline">Semua Info <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                        </div>
                    </div>

                    <div class="ih-grid">
                        {{-- Kotak putih "Jadwal Hari Ini" di dalam kotak hijau --}}
                        <div class="ih-today">
                            <div class="ih-today-head">
                                <i class="fa-regular fa-clock"></i>
                                <div><small>Jadwal</small><h3>Hari Ini</h3></div>
                            </div>
                            @forelse ($jadwalHariIni as $j)
                                <div class="ih-row"><b>{{ $j->jam }}</b><span>{{ $j->judul }}</span></div>
                            @empty
                                <div class="ih-row"><span>Belum ada jadwal hari ini.</span></div>
                            @endforelse
                        </div>

                        <div class="ih-cards">
                            @forelse ($infoHarian as $info)
                                <article class="ih-card">
                                    <div class="ih-top">
                                        <span class="ih-chip"><i class="fa-solid {{ $info->ikon }}"></i>{{ $info->kategori }}</span>
                                        @if ($info->penting)<span class="ih-pin">Penting</span>@endif
                                    </div>
                                    <h3>{{ $info->judul }}</h3>
                                    @if ($info->isi)<p>{{ $info->isi }}</p>@endif
                                    <div class="ih-time"><i class="fa-regular fa-clock"></i>{{ $info->waktu_label }}</div>
                                </article>
                            @empty
                                <article class="ih-card">
                                    <div class="ih-top">
                                        <span class="ih-chip"><i class="fa-solid fa-circle-info"></i>Info</span>
                                    </div>
                                    <h3>Belum ada info hari ini</h3>
                                    <p>Pengumuman dan agenda pesantren akan tampil di sini.</p>
                                </article>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- STRUKTUR ORGANISASI (tampil jika sudah ada data aktif) --}}
        @if ($pengurus->count())
            <section class="org" id="struktur">
                <div class="container">
                    <div class="eyebrow">Struktur Organisasi</div>
                    <div class="head-row">
                        <h2 class="section-title" style="margin-bottom:0">Pengurus &amp; pengasuh pesantren.</h2>
                        <a href="{{ route('profil.struktur-organisasi') }}" class="btn btn-outline">Lihat Struktur Lengkap <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                    </div>
                    <div class="org-grid">
                        @foreach ($pengurus as $i => $p)
                            @php $foto = $p->foto ?? null; @endphp
                            <div class="org-card">
                                <div class="avatar ph {{ $warna[$i % 3] }}"
                                    @if ($foto) style="background-image:url('{{ asset('storage/' . $foto) }}')" @endif>
                                    @unless ($foto)<i class="fa-solid fa-user"></i>@endunless
                                </div>
                                <h3>{{ $p->nama ?? '-' }}</h3>
                                <div class="role">{{ $p->jabatan ?? '' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- JENJANG PENDIDIKAN --}}
        <section class="levels" id="jenjang">
            <div class="container">
                <div class="eyebrow">Jenjang Pendidikan</div>
                <h2 class="section-title">Setiap tahap, jalan menuju kebermanfaatan.</h2>
                <p class="section-sub">Tujuh pilihan pendidikan yang saling melengkapi. Temukan jalur yang sesuai dengan perjalanan putra-putri Anda.</p>
                <div class="level-grid">
                    <a href="{{ route('jenjang.show', 'mdta') }}" class="level"><div class="top"><span>Diniyah Nonformal</span><i class="fa-solid fa-arrow-up-right-from-square"></i></div><h3>MDTA</h3><div class="sub">Madrasah Diniyah Takmiliyah</div><p>Fondasi mengaji, ibadah, dan akhlak untuk usia sekolah dasar.</p><div class="dur"><i class="fa-regular fa-clock"></i>Program 4 tahun</div></a>
                    <a href="{{ route('jenjang.show', 'pps-wustha') }}" class="level"><div class="top"><span>Ketarbiyahan</span><i class="fa-solid fa-arrow-up-right-from-square"></i></div><h3>PPS WUSTHA</h3><div class="sub">Pendidikan Pesantren Salafiyah</div><p>Kajian kepesantrenan dengan pendidikan kesetaraan tingkat SMP.</p><div class="dur"><i class="fa-regular fa-clock"></i>Program 3 tahun</div></a>
                    <a href="{{ route('jenjang.show', 'mts') }}" class="level"><div class="top"><span>Pendidikan Formal</span><i class="fa-solid fa-arrow-up-right-from-square"></i></div><h3>MTS</h3><div class="sub">Madrasah Tsanawiyah</div><p>Ilmu agama dan pengetahuan umum dalam budaya belajar santri.</p><div class="dur"><i class="fa-regular fa-clock"></i>Program 3 tahun</div></a>
                    <a href="{{ route('jenjang.show', 'ma') }}" class="level"><div class="top"><span>Pendidikan Formal</span><i class="fa-solid fa-arrow-up-right-from-square"></i></div><h3>MA</h3><div class="sub">Madrasah Aliyah</div><p>Penguatan akademik, karakter, dan persiapan studi lanjutan.</p><div class="dur"><i class="fa-regular fa-clock"></i>Program 3 tahun</div></a>
                    <a href="{{ route('jenjang.show', 'pdf-wustha') }}" class="level"><div class="top"><span>Diniyah Formal</span><i class="fa-solid fa-arrow-up-right-from-square"></i></div><h3>PDF Wustha</h3><div class="sub">Pendidikan Diniyah Formal</div><p>Pendalaman kitab turats pada jenjang menengah pertama.</p><div class="dur"><i class="fa-regular fa-clock"></i>Program 3 tahun</div></a>
                    <a href="{{ route('jenjang.show', 'pdf-ulya') }}" class="level"><div class="top"><span>Diniyah Formal</span><i class="fa-solid fa-arrow-up-right-from-square"></i></div><h3>PDF ULYA</h3><div class="sub">Pendidikan Diniyah Formal</div><p>Penguasaan ilmu syariah dan tradisi keilmuan tingkat lanjutan.</p><div class="dur"><i class="fa-regular fa-clock"></i>Program 3 tahun</div></a>
                    <a href="{{ route('jenjang.show', 'mahad-aly') }}" class="level"><div class="top"><span>Pendidikan Tinggi</span><i class="fa-solid fa-arrow-up-right-from-square"></i></div><h3>MA'HAD 'ALY</h3><div class="sub">Pendidikan Tinggi Pesantren</div><p>Mengkaji fikih dan usul fikih untuk calon kader ulama.</p><div class="dur"><i class="fa-regular fa-clock"></i>Program 4 tahun</div></a>
                    <div class="level-cta">
                        <i class="fa-regular fa-comments" style="font-size:22px"></i>
                        <h3>Masih mencari jenjang yang tepat?</h3>
                        <p>Tim kami siap mendampingi Anda memilih program.</p>
                        <a href="#kontak" class="btn btn-light" style="align-self:flex-start">Konsultasi <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                    </div>
                </div>
            </div>
        </section>

        {{-- BERITA --}}
        <section class="news" id="berita">
            <div class="container">
                <div class="eyebrow">Berita &amp; Pengumuman</div>
                <div class="head-row">
                    <h2 class="section-title" style="margin-bottom:0">Kabar terbaru dari pesantren.</h2>
                    <a href="{{ route('berita.index') }}" class="btn btn-outline">Semua Berita <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                </div>

                <div class="news-grid">
                    @forelse ($beritaTerbaru as $i => $b)
                        <article class="news-card">
                            <a href="{{ route('berita.show', $b) }}">
                                <div class="ph {{ $warna[$i % 3] }}" style="background-image:url('{{ $b->gambar_url }}')"></div>
                            </a>
                            <div class="news-meta">
                                <span class="cat">{{ $b->kategori }}</span>
                                <span class="date">{{ $b->tanggal_terbit->translatedFormat('d F Y') }}</span>
                            </div>
                            <h3>{{ $b->judul }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit($b->ringkasan_otomatis, 110) }}</p>
                            <a href="{{ route('berita.show', $b) }}" class="link-more">Baca Selengkapnya <i class="fa-solid fa-arrow-right"></i></a>
                        </article>
                    @empty
                        <p style="grid-column:1/-1;color:var(--muted)">Belum ada berita.</p>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- GALERI --}}
        <section class="gallery" id="galeri">
            <div class="container">
                <div class="eyebrow">Galeri Foto &amp; Video Santri</div>
                <div class="head-row">
                    <h2 class="section-title" style="margin-bottom:0">Momen sederhana, kenangan berharga.</h2>
                    <a href="{{ route('galeri') }}" class="btn btn-outline">Jelajahi Galeri <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                </div>
                @if ($galeriBeranda->isNotEmpty())
                    <div class="gallery-grid">
                        @foreach ($galeriBeranda as $i => $a)
                            @php
                                $jmlVideo = $a->media->where('tipe', '!=', 'foto')->count();
                                $jmlFoto = $a->media->where('tipe', 'foto')->count();
                                $ringkas = collect([
                                    $jmlFoto ? $jmlFoto . ' foto' : null,
                                    $jmlVideo ? $jmlVideo . ' video' : null,
                                ])->filter()->implode(' • ');
                            @endphp

                            @if ($i === 0)
                                <figure class="g-item g-video">
                                    <a href="{{ route('galeri') }}" aria-label="Buka album {{ $a->judul }}">
                                        <div class="ph c" style="background-image:url('{{ $sampulAlbum($a) }}')">
                                            @if ($jmlVideo)
                                                <span class="play"><i class="fa-solid fa-play"></i></span>
                                            @endif
                                            <div class="cap">
                                                <strong>{{ $a->judul }}</strong>
                                                <span>{{ $a->kategori }}@if ($ringkas) • {{ $ringkas }}@endif</span>
                                            </div>
                                        </div>
                                    </a>
                                </figure>
                            @else
                                <figure class="g-item">
                                    <a href="{{ route('galeri') }}" aria-label="Buka album {{ $a->judul }}">
                                        <div class="ph {{ $i === 1 ? 'd' : 'b' }}" style="background-image:url('{{ $sampulAlbum($a) }}')"></div>
                                    </a>
                                    <figcaption>{{ $a->judul }}</figcaption>
                                </figure>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p style="color:var(--muted)">Belum ada album galeri.</p>
                @endif
            </div>
        </section>

        {{-- PRESTASI --}}
        <section class="achv" id="prestasi">
            <div class="container">
                <div>
                    <div class="eyebrow">Prestasi Santri</div>
                    <h2>Ikhtiar terbaik, buah yang berarti.</h2>
                    <p class="intro">Merayakan proses, menguatkan semangat untuk terus bertumbuh.</p>
                    <a href="{{ route('prestasi.index') }}" class="link-more">Lihat semua prestasi <i class="fa-solid fa-arrow-right"></i></a>
                </div>
                <div class="achv-cards">
                    @forelse ($prestasiTerbaru as $p)
                        @php
                            $lokasi = collect([$p->tingkat, $p->wilayah, $p->tahun])->filter()->implode(' • ');
                            $pemilik = collect([$p->nama, $p->kelas])->filter()->implode(' · ');
                        @endphp
                        <div class="achv-card">
                            <div class="ico-box"><i class="fa-solid fa-trophy"></i></div>
                            @if ($p->label)<span class="tag">{{ $p->label }}</span>@endif
                            <h3>{{ $p->judul }}</h3>
                            <div class="lvl">{{ $lokasi }}</div>
                            <div class="who">{{ $pemilik }}</div>
                        </div>
                    @empty
                        <p style="grid-column:1/-1;color:var(--muted)">Belum ada data prestasi.</p>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- TESTIMONI ALUMNI (slider otomatis) --}}
        @if ($testimoniAlumni->isNotEmpty())
            <section class="alum" id="alumni">
                <div class="container">
                    <div class="alum-box" data-testi>
                        <div class="alum-head">
                            <div>
                                <div class="eyebrow">Kata Alumni</div>
                                <h2>Jejak mereka, cerita yang tinggal.</h2>
                                <p class="sub">Kesan dan pesan para alumni tentang pengalaman menimba ilmu di Pondok Pesantren Babul Hasanah.</p>
                            </div>
                            <div class="alum-nav">
                                <button type="button" data-testi-prev aria-label="Testimoni sebelumnya">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>
                                <button type="button" data-testi-next aria-label="Testimoni berikutnya">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>

                        <div class="alum-viewport">
                            <div class="alum-track" data-testi-track>
                                @foreach ($testimoniAlumni as $al)
                                    @php
                                        $inisial = collect(preg_split('/\s+/', trim((string) $al->nama)))
                                            ->filter()->take(2)
                                            ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                                            ->implode('');
                                        $metaAlumni = collect([
                                            $al->jenjang,
                                            $al->tahun_lulus ? 'Angkatan ' . $al->tahun_lulus : null,
                                        ])->filter()->implode(' • ');
                                        $kegiatanAlumni = collect([$al->kegiatan, $al->instansi])->filter()->implode(' • ');
                                    @endphp
                                    <article class="alum-card">
                                        @if ($al->tahun_lulus)
                                            <span class="alum-badge">
                                                <i class="fa-solid fa-graduation-cap"></i> Alumni {{ $al->tahun_lulus }}
                                            </span>
                                        @endif
                                        <i class="fa-solid fa-quote-left alum-quote" aria-hidden="true"></i>
                                        <p class="alum-text">&ldquo;{!! nl2br(e($al->testimoni)) !!}&rdquo;</p>
                                        <div class="alum-meta">
                                            @if ($al->foto_url)
                                                <img src="{{ $al->foto_url }}" alt="Foto {{ $al->nama }}"
                                                    class="alum-ava" loading="lazy">
                                            @else
                                                <span class="alum-ava is-initial" aria-hidden="true">{{ $inisial ?: 'A' }}</span>
                                            @endif
                                            <div>
                                                <strong>{{ $al->nama }}</strong>
                                                <small>{{ $metaAlumni ?: 'Alumni Babul Hasanah' }}</small>
                                                @if ($kegiatanAlumni)
                                                    <small>{{ $kegiatanAlumni }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>

                        <div class="alum-dots" data-testi-dots aria-label="Navigasi testimoni"></div>

                        <div class="alum-foot">
                            <a href="{{ route('alumni.index') }}" class="link-more">
                                Lihat semua alumni <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- PENERIMAAN SANTRI BARU --}}
        <section class="psb" id="psb">
            <div class="container">
                <div class="psb-box">
                    <div>
                        <div class="eyebrow light">Penerimaan Santri Baru</div>
                        <h2>Langkah kecil hari ini, bekal besar untuk esok.</h2>
                        <p class="txt">Mari bertumbuh bersama keluarga Babul Hasanah. Pendaftaran terbuka untuk santri putra dan putri melalui seleksi sesuai jenjang pendidikan.</p>
                        <div class="btns">
                            <a href="{{ route('pendaftaran.index') }}" class="btn btn-light">Daftar Online Sekarang <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                            <a href="https://wa.me/" target="_blank" rel="noopener" class="btn btn-outline" style="color:#fff;border-color:rgba(255,255,255,.3)"><i class="fa-brands fa-whatsapp"></i> Hubungi Panitia</a>
                        </div>
                        <div class="period"><i class="fa-regular fa-calendar"></i> Isi periode pendaftaran di sini</div>
                    </div>
                    <div>
                        <div class="steps-title">Tiga langkah menjadi santri</div>
                        <div class="step"><span class="num">01</span><div><h4>Buat akun &amp; pilih jenjang</h4><p>Isi identitas calon santri dan pilih program pendidikan.</p></div></div>
                        <div class="step"><span class="num">02</span><div><h4>Lengkapi berkas pendaftaran</h4><p>Siapkan KK, akta kelahiran, pasfoto, dan rapor terakhir.</p></div></div>
                        <div class="step"><span class="num">03</span><div><h4>Ikuti seleksi &amp; daftar ulang</h4><p>Jadwal seleksi dan hasil dapat dilihat melalui akun Anda.</p></div></div>
                    </div>
                </div>
            </div>
        </section>

        {{-- KONTAK --}}
        <section class="contact" id="kontak">
            <div class="container">
                <div>
                    <div class="eyebrow">Kontak</div>
                    <h2>Sapa kami.<br>Mari bersilaturahmi.</h2>
                    <p class="desc">Punya pertanyaan tentang pendidikan atau kehidupan di pesantren? Kami senang membantu Anda.</p>
                    <a href="https://wa.me/" target="_blank" rel="noopener" class="btn btn-primary">Hubungi via WhatsApp <i class="fa-brands fa-whatsapp"></i></a>
                </div>
                <div class="contact-info">
                    <div class="row">
                        <i class="fa-solid fa-location-dot"></i>
                        <div><strong>Kunjungi pesantren</strong><p>Ganti dengan alamat lengkap pesantren,<br>Kabupaten Padang Lawas, Sumatera Utara</p></div>
                    </div>
                    <div class="contact-two">
                        <div><small>Telepon / WhatsApp</small><b>+62 xxx-xxxx-xxxx</b></div>
                        <div><small>Email sekretariat</small><b>info@babulhasanah.example</b></div>
                    </div>
                    <div class="hours"><i class="fa-regular fa-clock"></i>Layanan sekretariat: Senin–Sabtu, 08.00–15.00 WIB</div>
                </div>
            </div>
        </section>

        {{-- PETA LOKASI --}}
        <section class="map-section" id="lokasi">
            <div class="container">
                <div class="map-wrap">
                    <iframe title="Lokasi Pondok Pesantren Babul Hasanah"
                        src="https://www.google.com/maps?q=Padang+Lawas,+Sumatera+Utara&output=embed"
                        loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <div class="map-card">
                        <div class="eyebrow">Lokasi Kami</div>
                        <h3>Pondok Pesantren Babul Hasanah</h3>
                        <p>Kabupaten Padang Lawas, Sumatera Utara</p>
                        <a href="https://www.google.com/maps/search/?api=1&query=Padang+Lawas,+Sumatera+Utara" target="_blank" rel="noopener" class="btn btn-primary">Buka di Google Maps <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            // Animasi muncul saat scroll
            const revealEls = document.querySelectorAll(
                '.dh .hero2 .container > *, .dh .stats .container > *, .dh .profile-grid > *, .dh .feature, ' +
                '.dh .program-card, .dh .news-card, .dh .level, .dh .org-card, .dh .ih-card, .dh .ih-today, .dh .g-item, .dh .achv-card, ' +
                '.dh .psb-box > *, .dh .contact .container > *'
            );
            const revealObs = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('in');
                        revealObs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            revealEls.forEach((el, i) => {
                // kartu di dalam Swiper tidak diberi animasi supaya tidak mengganggu slider
                if (el.closest('.swiper')) return;
                el.classList.add('reveal');
                el.style.transitionDelay = ((i % 4) * 70) + 'ms';
                revealObs.observe(el);
            });

            // Angka statistik berjalan
            const fmt = new Intl.NumberFormat('id-ID');
            const countObs = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) return;
                    const el = entry.target;
                    const end = parseInt(el.dataset.count, 10);
                    const suffix = el.dataset.suffix || '';
                    const start = performance.now();
                    const tick = (now) => {
                        const p = Math.min((now - start) / 1200, 1);
                        el.textContent = fmt.format(Math.round(end * p)) + suffix;
                        if (p < 1) requestAnimationFrame(tick);
                    };
                    requestAnimationFrame(tick);
                    countObs.unobserve(el);
                });
            }, { threshold: 0.4 });

            document.querySelectorAll('.dh [data-count]').forEach(el => countObs.observe(el));

            // ---------- Hero slider: 3 gambar bergeser otomatis ----------
            const hero = document.querySelector('.dh .hero2');
            if (hero) {
                const heroSlides = Array.from(hero.querySelectorAll('.hero2-slide'));
                const heroDots = Array.from(hero.querySelectorAll('.hero2-dot'));
                const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                const DURASI = 6000;   // jeda antar gambar (ms)
                let heroIndex = 0;
                let heroTimer = null;

                const showSlide = (i) => {
                    heroIndex = (i + heroSlides.length) % heroSlides.length;
                    heroSlides.forEach((s, n) => {
                        s.classList.toggle('is-active', n === heroIndex);
                        s.setAttribute('aria-hidden', n === heroIndex ? 'false' : 'true');
                    });
                    heroDots.forEach((d, n) => {
                        const on = n === heroIndex;
                        d.classList.toggle('is-active', on);
                        d.setAttribute('aria-selected', on ? 'true' : 'false');
                    });
                };

                const stopAuto = () => {
                    if (heroTimer) {
                        clearInterval(heroTimer);
                        heroTimer = null;
                    }
                };

                const startAuto = () => {
                    stopAuto();
                    if (reduceMotion || heroSlides.length < 2) return;
                    heroTimer = setInterval(() => showSlide(heroIndex + 1), DURASI);
                };

                heroDots.forEach((dot, i) => dot.addEventListener('click', () => {
                    showSlide(i);
                    startAuto();
                }));

                hero.querySelector('[data-slide="prev"]')
                    ?.addEventListener('click', () => { showSlide(heroIndex - 1); startAuto(); });
                hero.querySelector('[data-slide="next"]')
                    ?.addEventListener('click', () => { showSlide(heroIndex + 1); startAuto(); });

                // Berhenti sejenak saat kursor/fokus di area hero, lanjut lagi setelahnya
                hero.addEventListener('mouseenter', stopAuto);
                hero.addEventListener('mouseleave', startAuto);
                hero.addEventListener('focusin', stopAuto);
                hero.addEventListener('focusout', (e) => {
                    if (!hero.contains(e.relatedTarget)) startAuto();
                });
                document.addEventListener('visibilitychange', () => {
                    document.hidden ? stopAuto() : startAuto();
                });

                startAuto();
            }

            // ---------- Slider testimoni alumni ----------
            const testi = document.querySelector('.dh [data-testi]');
            if (testi) {
                const track = testi.querySelector('[data-testi-track]');
                const cards = track ? Array.from(track.children) : [];
                const dotsBox = testi.querySelector('[data-testi-dots]');
                const btnPrev = testi.querySelector('[data-testi-prev]');
                const btnNext = testi.querySelector('[data-testi-next]');
                const viewport = testi.querySelector('.alum-viewport');
                const reduceTesti = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                const DURASI_TESTI = 5500;   // jeda antar kartu (ms)
                let testiIndex = 0;
                let testiTimer = null;
                let maxIndex = 0;

                const perView = () => {
                    const w = window.innerWidth;
                    if (w <= 640) return 1;
                    if (w <= 1024) return 2;
                    return 3;
                };

                const langkah = () => {
                    if (!cards.length) return 0;
                    const gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap) || 20;
                    return cards[0].getBoundingClientRect().width + gap;
                };

                const buatDots = () => {
                    if (!dotsBox) return;
                    maxIndex = Math.max(0, cards.length - perView());
                    dotsBox.innerHTML = '';
                    for (let i = 0; i <= maxIndex; i++) {
                        const b = document.createElement('button');
                        b.type = 'button';
                        b.className = 'alum-dot';
                        b.setAttribute('aria-label', 'Testimoni ' + (i + 1));
                        b.addEventListener('click', () => { tampil(i); mulai(); });
                        dotsBox.appendChild(b);
                    }
                    testi.classList.toggle('is-static', maxIndex === 0);
                };

                const tampil = (i) => {
                    if (!cards.length) return;
                    maxIndex = Math.max(0, cards.length - perView());
                    testiIndex = i > maxIndex ? 0 : (i < 0 ? maxIndex : i);
                    track.style.transform = 'translateX(' + (-testiIndex * langkah()) + 'px)';
                    Array.from(dotsBox ? dotsBox.children : []).forEach((d, n) => {
                        const on = n === testiIndex;
                        d.classList.toggle('is-active', on);
                        d.setAttribute('aria-current', on ? 'true' : 'false');
                    });
                };

                const stopTesti = () => {
                    if (testiTimer) {
                        clearInterval(testiTimer);
                        testiTimer = null;
                    }
                };

                const mulai = () => {
                    stopTesti();
                    if (reduceTesti || maxIndex < 1) return;
                    testiTimer = setInterval(() => tampil(testiIndex + 1), DURASI_TESTI);
                };

                btnPrev?.addEventListener('click', () => { tampil(testiIndex - 1); mulai(); });
                btnNext?.addEventListener('click', () => { tampil(testiIndex + 1); mulai(); });

                // Geser dengan usapan (mobile)
                if (viewport) {
                    let startX = null;
                    viewport.addEventListener('pointerdown', (e) => { startX = e.clientX; });
                    viewport.addEventListener('pointerup', (e) => {
                        if (startX === null) return;
                        const delta = e.clientX - startX;
                        startX = null;
                        if (Math.abs(delta) < 40) return;
                        tampil(testiIndex + (delta < 0 ? 1 : -1));
                        mulai();
                    });
                    viewport.addEventListener('pointercancel', () => { startX = null; });
                }

                // Berhenti sejenak saat kursor/fokus di area testimoni
                testi.addEventListener('mouseenter', stopTesti);
                testi.addEventListener('mouseleave', mulai);
                testi.addEventListener('focusin', stopTesti);
                testi.addEventListener('focusout', (e) => {
                    if (!testi.contains(e.relatedTarget)) mulai();
                });
                document.addEventListener('visibilitychange', () => {
                    document.hidden ? stopTesti() : mulai();
                });

                buatDots();
                tampil(0);

                let resizeTimer = null;
                window.addEventListener('resize', () => {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(() => {
                        buatDots();
                        tampil(testiIndex);
                    }, 150);
                });

                mulai();
            }
        });
    </script>
@endsection