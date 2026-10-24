@extends('layouts.app')

@section('title', 'Pondok Pesantren Babul Hasanah')

@section('content')

    {{-- Font & Swiper (fungsi slider program unggulan tetap dipakai) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

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
        .dh .hero2-bg {
            position: absolute; inset: 0; z-index: 0;
            background-image: var(--hero-img, none),
                radial-gradient(900px 500px at 78% 30%, rgba(255, 255, 255, .10), transparent 60%),
                linear-gradient(135deg, #557a5b 0%, #2f4f3c 50%, #183024 100%);
            background-size: cover; background-position: center; transform: scale(1.04);
        }
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

        /* ---------- PROFIL ---------- */
        .dh .profile { padding: 90px 0 70px; }
        .dh .profile-grid { display: grid; grid-template-columns: .9fr 1.1fr; gap: 70px; align-items: center; }
        .dh .profile-img { position: relative; aspect-ratio: 1 / 1.05; }
        .dh .profile-img .ph { position: absolute; inset: 0; box-shadow: var(--shadow-lg); padding: 22px; display: flex; }
        .dh .photo-slot {
            flex: 1; border: 2px dashed rgba(255, 255, 255, .4); border-radius: 18px; background: rgba(10, 30, 20, .35);
            display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;
            color: rgba(255, 255, 255, .85); padding: 20px; gap: 6px;
        }
        .dh .photo-slot i { font-size: 34px; color: rgba(255, 255, 255, .6); }
        .dh .photo-slot strong { font-size: 12px; letter-spacing: .08em; text-transform: uppercase; }
        .dh .photo-slot span { font-size: 11px; color: rgba(255, 255, 255, .65); }
        .dh .badge-year {
            position: absolute; right: -20px; bottom: -20px; background: #fff; padding: 16px 20px;
            border-radius: 16px; max-width: 190px; box-shadow: var(--shadow-lg); z-index: 2;
        }
        .dh .badge-year b { font-size: 26px; color: var(--green); display: block; line-height: 1; }
        .dh .badge-year span { font-size: 10.5px; color: var(--muted); line-height: 1.3; display: block; margin-top: 4px; }
        .dh .profile h2 { font-size: clamp(26px, 3.2vw, 38px); margin-bottom: 18px; }
        .dh .profile .desc { color: var(--muted); font-size: 13.5px; margin-bottom: 24px; }
        .dh .tabs {
            display: flex; gap: 6px; background: var(--cream); padding: 5px; border-radius: 999px;
            width: fit-content; margin-bottom: 20px; flex-wrap: wrap;
        }
        .dh .tab { padding: 8px 18px; font-size: 12px; color: var(--muted); border-radius: 999px; transition: all .25s ease; }
        .dh .tab:hover { color: var(--green); }
        .dh .tab.active { background: #fff; color: var(--green); font-weight: 600; box-shadow: 0 4px 12px rgba(23, 64, 47, .1); }
        .dh .tab-note { font-size: 13px; color: var(--muted); }

        .dh .features { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-top: 60px; }
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

        /* ---------- PROGRAM UNGGULAN (SWIPER) ---------- */
        .dh .levels { background: var(--cream); padding: 80px 0; border-radius: 36px; margin: 0 1.5%; }
        .dh .section-title { font-size: clamp(26px, 3.4vw, 38px); margin-bottom: 12px; }
        .dh .section-sub { color: var(--muted); font-size: 13.5px; margin-bottom: 36px; max-width: 600px; }
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

        /* ---------- BERITA ---------- */
        .dh .news { padding: 80px 0; }
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

        /* ---------- JENJANG PENDIDIKAN ---------- */
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

        /* ---------- KEHIDUPAN SANTRI ---------- */
        .dh .life {
            background: linear-gradient(130deg, var(--green), var(--green-2)); color: #fff; padding: 70px 0;
            border-radius: 36px; margin: 0 1.5%; position: relative; overflow: hidden;
        }
        .dh .life .container { display: grid; grid-template-columns: 1fr 2fr; gap: 60px; align-items: start; }
        .dh .life h2 { color: #fff; font-size: clamp(26px, 3vw, 36px); margin-bottom: 14px; }
        .dh .life .intro { font-size: 13px; color: #b9d0c4; }
        .dh .times { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .dh .time {
            background: rgba(255, 255, 255, .06); border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 20px; padding: 22px; transition: all .3s ease;
        }
        .dh .time:hover { background: rgba(255, 255, 255, .12); transform: translateY(-4px); }
        .dh .time b { font-size: 26px; font-weight: 600; display: block; margin-bottom: 8px; }
        .dh .time h4 { color: #fff; font-size: 13px; margin-bottom: 6px; }
        .dh .time p { font-size: 11.5px; color: #b9d0c4; }

        /* ---------- GALERI ---------- */
        .dh .gallery { padding: 80px 0; }
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

        /* ---------- PRESTASI ---------- */
        .dh .achv { padding: 60px 0; }
        .dh .achv .container { display: grid; grid-template-columns: .8fr 2.2fr; gap: 40px; align-items: center; }
        .dh .achv h2 { font-size: clamp(26px, 3vw, 36px); margin-bottom: 12px; }
        .dh .achv .intro { font-size: 13px; color: var(--muted); }
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
        .dh .psb { padding: 60px 0 80px; }
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
        .dh .contact { padding: 30px 0 60px; }
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
        .dh .map-section { padding: 0 0 80px; }
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

        /* ---------- ANIMASI SCROLL ---------- */
        .dh .reveal { opacity: 0; transform: translateY(24px); transition: opacity .8s ease, transform .8s cubic-bezier(.2, .7, .2, 1); }
        .dh .reveal.in { opacity: 1; transform: none; }

        /* ---------- RESPONSIVE ---------- */
        @media (max-width: 1024px) {
            .dh .profile-grid { grid-template-columns: 1fr; gap: 50px; }
            .dh .stats .container { grid-template-columns: 1fr 1fr; gap: 20px; }
            .dh .features, .dh .news-grid { grid-template-columns: 1fr; }
            .dh .badge-year { right: 10px; bottom: -10px; }
            .dh .levels { margin: 0; border-radius: 0; }
            .dh .head-row { flex-direction: column; align-items: flex-start; }
        }
        @media (max-width: 640px) {
            .dh .hero2-cta { flex-direction: column; width: 100%; }
            .dh .hero2-cta .btn { width: 100%; }
            .dh .stats .container { grid-template-columns: 1fr; }
            .dh .stat { border-left: 0; border-bottom: 1px solid rgba(255, 255, 255, .15); padding-left: 0; padding-bottom: 12px; }
            .dh .psb-box { padding: 36px 20px; border-radius: 20px; }
            .dh .psb .btns { flex-direction: column; }
            .dh .psb .btn { width: 100%; }
            .dh .info-inner { border-radius: 16px; }
        }
        @media (max-width: 1024px) {
            .dh .level-grid { grid-template-columns: repeat(2, 1fr); }
            .dh .life .container, .dh .achv .container, .dh .psb-box, .dh .contact .container { grid-template-columns: 1fr; gap: 40px; }
            .dh .times, .dh .achv-cards, .dh .gallery-grid { grid-template-columns: 1fr; }
            .dh .life { margin: 0; border-radius: 0; }
        }
        @media (max-width: 640px) {
            .dh .level-grid, .dh .contact-two { grid-template-columns: 1fr; }
            .dh .psb-box { padding: 28px 20px; border-radius: 20px; }
            .dh .psb .btns { flex-direction: column; }
            .dh .psb .btn { width: 100%; }
            .dh .hours { border-radius: 16px; }
            .dh .map-wrap { height: auto; display: flex; flex-direction: column; }
            .dh .map-wrap iframe { height: 280px; order: 2; }
            .dh .map-card { position: static; max-width: none; border-radius: 0; box-shadow: none; order: 1; }
        }
        @media (prefers-reduced-motion: reduce) {
            .dh .reveal, .dh .hero2-bg { transition: none !important; opacity: 1 !important; transform: none !important; }
        }
    </style>

    <div class="dh">

        {{-- HERO SECTION --}}
        <section class="hero2">
            <div class="hero2-bg" style="--hero-img: url('{{ asset('assets/img/banner/banner.png') }}');" role="img"
                aria-label="Suasana Pondok Pesantren Babul Hasanah"></div>
            <div class="container">
                <span class="hero2-badge">Selamat datang di official website</span>
                <h1>Membangun Generasi Unggul Dalam Penguasaan <span class="accent">Kitab Kuning</span></h1>
                <p class="lead">
                    Pondok Pesantren Babul Hasanah berkomitmen mendidik santri yang mendalami ilmu agama, unggul dalam
                    kitab kuning, berakhlak mulia serta berani menghadapi tantangan zaman.
                </p>
                <div class="hero2-cta">
                    <a href="{{ route('pendaftaran.index') }}" class="btn btn-emerald btn-lg">Daftar Santri Baru</a>
                    <a href="#profil" class="btn btn-glass btn-lg">Pelajari Selengkapnya</a>
                </div>
            </div>
        </section>

        {{-- RUNNING TEXT / INFO BANNER --}}
        <div class="info-bar">
            <div class="container">
                <div class="info-inner">
                    <span class="info-tag"><i class="fa-solid fa-bullhorn"></i> INFO</span>
                    <marquee scrollamount="5">
                        Selamat datang di portal resmi Pondok Pesantren Babul Hasanah. Pendaftaran Santri Baru Tahun Ajaran Baru telah dibuka, mari bergabung bersama kami untuk mencetak generasi unggul!
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
                <a href="{{ url('/santri') }}" class="stat">
                    <b data-count="500" data-suffix="+">500+</b><span>Santri Aktif</span>
                </a>
                <a href="{{ url('/profil/struktur-organisasi') }}" class="stat">
                    <b data-count="30" data-suffix="+">30+</b><span>Ustadz / Ustadzah</span>
                </a>
                <a href="{{ url('/program/kategori-1') }}" class="stat">
                    <b data-count="2">2</b><span>Program Unggulan</span>
                </a>
                <a href="{{ url('/ekstrakurikuler') }}" class="stat">
                    <b data-count="15" data-suffix="+">15+</b><span>Ekstrakurikuler</span>
                </a>
            </div>
        </section>

        {{-- PROFIL PESANTREN --}}
        <section class="profile" id="profil">
            <div class="container">
                <div class="profile-grid">
                    <div class="profile-img">
                        <div class="ph c">
                            {{-- KOTAK DUMMY FOTO PIMPINAN (ganti dengan <img> jika sudah ada fotonya) --}}
                            <div class="photo-slot">
                                <i class="fa-solid fa-image"></i>
                                <strong>[ Kotak Foto Pimpinan ]</strong>
                                <span>Nanti bisa disesuaikan dengan tag &lt;img&gt;</span>
                            </div>
                        </div>
                        <div class="badge-year">
                            <b>1998</b>
                            <span>Awal sebuah ikhtiar, berlanjut lintas generasi.</span>
                        </div>
                    </div>

                    <div>
                        <div class="eyebrow">Profil Pesantren</div>
                        <h2>Berakar pada adab.<br>Bertumbuh dengan ilmu.</h2>
                        <p class="desc">
                            Berawal dari majelis kecil di Padang Lawas pada 1998, Babul Hasanah tumbuh menjadi lingkungan belajar yang memadukan tradisi keilmuan pesantren dengan pendidikan yang relevan bagi masa depan.
                        </p>

                        <p class="tab-note">
                            Visi kami: melahirkan insan berilmu, berakhlak mulia, dan berdaya guna. Dikelola oleh dewan pengasuh bersama tim pendidikan, setiap langkah terpusat pada tumbuh kembang santri.
                        </p>
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

        {{-- JENJANG PENDIDIKAN --}}
        <section class="levels" id="jenjang">
            <div class="container">
                <div class="eyebrow">Jenjang Pendidikan</div>
                <h2 class="section-title">Setiap tahap, jalan menuju kebermanfaatan.</h2>
                <p class="section-sub">Tujuh pilihan pendidikan yang saling melengkapi. Temukan jalur yang sesuai dengan perjalanan putra-putri Anda.</p>
                <div class="level-grid">
                    <a href="#" class="level"><div class="top"><span>Diniyah Nonformal</span><i class="fa-solid fa-arrow-up-right-from-square"></i></div><h3>MDTA</h3><div class="sub">Madrasah Diniyah Takmiliyah</div><p>Fondasi mengaji, ibadah, dan akhlak untuk usia sekolah dasar.</p><div class="dur"><i class="fa-regular fa-clock"></i>Program 4 tahun</div></a>
                    <a href="#" class="level"><div class="top"><span>Ketarbiyahan</span><i class="fa-solid fa-arrow-up-right-from-square"></i></div><h3>PPS WUSTHA</h3><div class="sub">Pendidikan Pesantren Salafiyah</div><p>Kajian kepesantrenan dengan pendidikan kesetaraan tingkat SMP.</p><div class="dur"><i class="fa-regular fa-clock"></i>Program 3 tahun</div></a>
                    <a href="#" class="level"><div class="top"><span>Pendidikan Formal</span><i class="fa-solid fa-arrow-up-right-from-square"></i></div><h3>MTS</h3><div class="sub">Madrasah Tsanawiyah</div><p>Ilmu agama dan pengetahuan umum dalam budaya belajar santri.</p><div class="dur"><i class="fa-regular fa-clock"></i>Program 3 tahun</div></a>
                    <a href="#" class="level"><div class="top"><span>Pendidikan Formal</span><i class="fa-solid fa-arrow-up-right-from-square"></i></div><h3>MA</h3><div class="sub">Madrasah Aliyah</div><p>Penguatan akademik, karakter, dan persiapan studi lanjutan.</p><div class="dur"><i class="fa-regular fa-clock"></i>Program 3 tahun</div></a>
                    <a href="#" class="level"><div class="top"><span>Diniyah Formal</span><i class="fa-solid fa-arrow-up-right-from-square"></i></div><h3>PDF Wustha</h3><div class="sub">Pendidikan Diniyah Formal</div><p>Pendalaman kitab turats pada jenjang menengah pertama.</p><div class="dur"><i class="fa-regular fa-clock"></i>Program 3 tahun</div></a>
                    <a href="#" class="level"><div class="top"><span>Diniyah Formal</span><i class="fa-solid fa-arrow-up-right-from-square"></i></div><h3>PDF ULYA</h3><div class="sub">Pendidikan Diniyah Formal</div><p>Penguasaan ilmu syariah dan tradisi keilmuan tingkat lanjutan.</p><div class="dur"><i class="fa-regular fa-clock"></i>Program 3 tahun</div></a>
                    <a href="#" class="level"><div class="top"><span>Pendidikan Tinggi</span><i class="fa-solid fa-arrow-up-right-from-square"></i></div><h3>MA'HAD 'ALY</h3><div class="sub">Pendidikan Tinggi Pesantren</div><p>Mengkaji fikih dan usul fikih untuk calon kader ulama.</p><div class="dur"><i class="fa-regular fa-clock"></i>Program 4 tahun</div></a>
                    <div class="level-cta">
                        <i class="fa-regular fa-comments" style="font-size:22px"></i>
                        <h3>Masih mencari jenjang yang tepat?</h3>
                        <p>Tim kami siap mendampingi Anda memilih program.</p>
                        <a href="#kontak" class="btn btn-light" style="align-self:flex-start">Konsultasi <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                    </div>
                </div>
            </div>
        </section>

        {{-- PROGRAM UNGGULAN (SWIPER) --}}
        <section class="program-sec" id="program">
            <div class="container">
                <div class="eyebrow">Program Unggulan</div>
                <h2 class="section-title">Pusat Pendidikan Islam Berwawasan Global</h2>
                <p class="section-sub">
                    Pondok Pesantren Babul Hasanah hadir di Kabupaten Padang Lawas untuk memberikan fasilitas pendidikan
                    agama yang kokoh serta penguasaan sains dan teknologi.
                </p>

                <div class="swiper mySwiper">
                    <div class="swiper-wrapper">

                        <div class="swiper-slide !h-auto">
                            <div class="program-card">
                                <div class="program-cover" style="background-image: url('https://images.unsplash.com/photo-1609599006353-e629aaabfeae?q=80&w=500&auto=format&fit=crop');">
                                    <span class="pc-ico"><i class="fa-solid fa-book-quran"></i></span>
                                    <span class="pc-tag">Tahfidz</span>
                                </div>
                                <div class="program-body">
                                    <h5>Tahfidzul Qur'an</h5>
                                    <p>Program khusus hafalan Al-Qur'an dengan bimbingan intensif dan metode talaqqi yang teruji menghasilkan hafizh dan hafizhah handal.</p>
                                    <a href="#" class="btn btn-primary">DAFTAR <i class="fa-solid fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="swiper-slide !h-auto">
                            <div class="program-card">
                                <div class="program-cover" style="background-image: url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=500&auto=format&fit=crop');">
                                    <span class="pc-ico"><i class="fa-solid fa-school"></i></span>
                                    <span class="pc-tag">Formal</span>
                                </div>
                                <div class="program-body">
                                    <h5>Kulliyatul Mu'allimin</h5>
                                    <p>Pendidikan formal tingkat MTs dan MA dengan kurikulum memadukan DEPAG, pesantren salafiyah, serta penguasaan bahasa asing.</p>
                                    <a href="#" class="btn btn-primary">DAFTAR <i class="fa-solid fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="swiper-slide !h-auto">
                            <div class="program-card">
                                <div class="program-cover" style="background-image: url('https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=500&auto=format&fit=crop');">
                                    <span class="pc-ico"><i class="fa-solid fa-laptop-code"></i></span>
                                    <span class="pc-tag">Teknologi</span>
                                </div>
                                <div class="program-body">
                                    <h5>Kemandirian &amp; Teknologi</h5>
                                    <p>Membekali santri dengan kecakapan hidup (life skills), kewirausahaan, serta literasi digital guna menghadapi tantangan zaman modern.</p>
                                    <a href="#" class="btn btn-primary">DAFTAR <i class="fa-solid fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="swiper-slide !h-auto">
                            <div class="program-card">
                                <div class="program-cover" style="background-image: url('https://images.unsplash.com/photo-1532012197267-da84d127e765?q=80&w=500&auto=format&fit=crop');">
                                    <span class="pc-ico"><i class="fa-solid fa-book-open"></i></span>
                                    <span class="pc-tag">Salafiyah</span>
                                </div>
                                <div class="program-body">
                                    <h5>Kajian Kitab Kuning</h5>
                                    <p>Pendalaman ilmu agama klasik bermetode muhafazhah dan pendalaman kaidah Bahasa Arab Nahwu &amp; Shorof secara terstruktur.</p>
                                    <a href="#" class="btn btn-primary">DAFTAR <i class="fa-solid fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="swiper-slide !h-auto">
                            <div class="program-card">
                                <div class="program-cover" style="background-image: url('https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?q=80&w=500&auto=format&fit=crop');">
                                    <span class="pc-ico"><i class="fa-solid fa-language"></i></span>
                                    <span class="pc-tag">Bahasa</span>
                                </div>
                                <div class="program-body">
                                    <h5>Bahasa Asing Intensif</h5>
                                    <p>Pembiasaan percakapan harian (Arabic &amp; English Club) guna membentuk santri yang komunikatif dan berwawasan global.</p>
                                    <a href="#" class="btn btn-primary">DAFTAR <i class="fa-solid fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="swiper-slide !h-auto">
                            <div class="program-card">
                                <div class="program-cover" style="background-image: url('https://images.unsplash.com/photo-1511632765486-a01980e01a18?q=80&w=500&auto=format&fit=crop');">
                                    <span class="pc-ico"><i class="fa-solid fa-award"></i></span>
                                    <span class="pc-tag">Ekstra</span>
                                </div>
                                <div class="program-body">
                                    <h5>Minat &amp; Bakat Santri</h5>
                                    <p>Wadah pengembangan kepemimpinan melalui pramuka, seni bela diri, kaligrafi, pidato 3 bahasa, dan keolahragaan.</p>
                                    <a href="#" class="btn btn-primary">DAFTAR <i class="fa-solid fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="custom-swiper-pagination"></div>
            </div>
        </section>

        {{-- BERITA TERBARU --}}
        <section class="news">
            <div class="container">
                <div class="eyebrow">Informasi &amp; Kegiatan</div>
                <div class="head-row">
                    <h2 class="section-title">Berita Terbaru Pesantren</h2>
                    <a href="{{ route('berita.index') }}" class="btn btn-outline">Lihat Semua Berita <i class="fa-solid fa-arrow-right"></i></a>
                </div>

                <div class="news-grid">
                    <article class="news-card">
                        <div class="ph">[ Gambar Kegiatan ]</div>
                        <div class="news-meta"><span class="cat">Pengumuman</span><span class="date">24 Agustus 2026</span></div>
                        <h3>Pelaksanaan Ujian Semester Ganjil Santri Babul Hasanah</h3>
                        <p>Ujian akhir semester ganjil tahun ajaran ini resmi dibuka oleh pimpinan pondok dengan sistem terpadu...</p>
                        <a href="#" class="link-more">Baca Selengkapnya <i class="fa-solid fa-arrow-right"></i></a>
                    </article>

                    <article class="news-card">
                        <div class="ph c">[ Gambar Kegiatan ]</div>
                        <div class="news-meta"><span class="cat">Prestasi</span><span class="date">20 Agustus 2026</span></div>
                        <h3>Santri Babul Hasanah Juara 1 Musabaqah Tilawatil Qur'an</h3>
                        <p>Kebanggaan kembali hadir setelah perwakilan santri berhasil menyabet juara pertama tingkat Kabupaten Padang Lawas...</p>
                        <a href="#" class="link-more">Baca Selengkapnya <i class="fa-solid fa-arrow-right"></i></a>
                    </article>

                    <article class="news-card">
                        <div class="ph d">[ Gambar Kegiatan ]</div>
                        <div class="news-meta"><span class="cat">Kegiatan Pondok</span><span class="date">15 Agustus 2026</span></div>
                        <h3>Kajian Kitab Kuning Bulanan Bersama Ulama Tamu</h3>
                        <p>Kegiatan rutin pengajian kitab kuning penunjang pemahaman fiqih santri dilaksanakan di aula utama pesantren...</p>
                        <a href="#" class="link-more">Baca Selengkapnya <i class="fa-solid fa-arrow-right"></i></a>
                    </article>
                </div>
            </div>
        </section>

        {{-- KEHIDUPAN SANTRI --}}
        <section class="life">
            <div class="container">
                <div>
                    <div class="eyebrow light">Kehidupan Santri</div>
                    <h2>Belajar bukan hanya di dalam kelas.</h2>
                    <p class="intro">Dari subuh hingga malam, setiap kebiasaan menjadi bekal untuk hidup yang lebih bermakna.</p>
                </div>
                <div class="times">
                    <div class="time"><b>04.00</b><h4>Mengawali dengan Doa</h4><p>Qiyamul lail, subuh berjamaah, dan halaqah Al-Qur'an.</p></div>
                    <div class="time"><b>07.00</b><h4>Menjelajah Ilmu</h4><p>Pembelajaran madrasah, diskusi, dan pengembangan diri.</p></div>
                    <div class="time"><b>18.00</b><h4>Menjaga Tradisi</h4><p>Kajian kitab, murajaah, dan belajar bersama.</p></div>
                </div>
            </div>
        </section>

        {{-- GALERI --}}
        <section class="gallery" id="galeri">
            <div class="container">
                <div class="eyebrow">Galeri Foto &amp; Video Santri</div>
                <div class="head-row">
                    <h2 class="section-title">Momen sederhana, kenangan berharga.</h2>
                    <a href="#" class="btn btn-outline">Jelajahi Galeri <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                </div>
                <div class="gallery-grid">
                    <figure class="g-item g-video">
                        <div class="ph c">
                            <button class="play" aria-label="Putar video"><i class="fa-solid fa-play"></i></button>
                            <div class="cap"><strong>Sehari menjadi santri Babul Hasanah</strong><span>Video Profil • 04:32 Menit</span></div>
                        </div>
                    </figure>
                    <figure class="g-item"><div class="ph d"></div><figcaption>Ruang karya &amp; kreativitas</figcaption></figure>
                    <figure class="g-item"><div class="ph b"></div><figcaption>Hadrah &amp; kebersamaan</figcaption></figure>
                </div>
            </div>
        </section>

        {{-- PRESTASI --}}
        <section class="achv" id="prestasi">
            <div class="container">
                <div>
                    <div class="eyebrow">Prestasi Santri</div>
                    <h2>Ikhtiar terbaik, buah yang berarti.</h2>
                    <p class="intro">Merayakan proses, menguatkan semangat untuk terus bertumbuh.</p>
                </div>
                <div class="achv-cards">
                    <div class="achv-card"><div class="ico-box"><i class="fa-solid fa-trophy"></i></div><span class="tag">Juara I</span><h3>Musabaqah Tilawatil Qur'an</h3><div class="lvl">Tingkat Kabupaten Padang Lawas • 2026</div><div class="who">Nama Santri · MA</div></div>
                    <div class="achv-card"><div class="ico-box"><i class="fa-solid fa-trophy"></i></div><span class="tag">Juara II</span><h3>Musabaqah Qira'atil Kutub</h3><div class="lvl">Tingkat Provinsi • 2026</div><div class="who">Nama Santri · PDF Ulya</div></div>
                    <div class="achv-card"><div class="ico-box"><i class="fa-solid fa-trophy"></i></div><span class="tag">Medali Emas</span><h3>Olimpiade Bahasa Arab</h3><div class="lvl">Tingkat Provinsi • 2026</div><div class="who">Nama Santri · MTS</div></div>
                </div>
            </div>
        </section>

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
                            <a href="https://wa.me/" target="_blank" class="btn btn-outline" style="color:#fff;border-color:rgba(255,255,255,.3)"><i class="fa-brands fa-whatsapp"></i> Hubungi Panitia</a>
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
                    <a href="https://wa.me/" target="_blank" class="btn btn-primary">Hubungi via WhatsApp <i class="fa-brands fa-whatsapp"></i></a>
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

            // Swiper program unggulan (konfigurasi tetap seperti semula)
            new Swiper(".mySwiper", {
                slidesPerView: 1,
                spaceBetween: 24,
                loop: true,
                autoplay: { delay: 2000, disableOnInteraction: false },
                pagination: { el: ".custom-swiper-pagination", clickable: true },
                breakpoints: {
                    640: { slidesPerView: 2, spaceBetween: 24 },
                    1024: { slidesPerView: 3, spaceBetween: 32 },
                },
            });

            // Animasi muncul saat scroll
            const revealEls = document.querySelectorAll(
                '.dh .hero2 .container > *, .dh .stats .container > *, .dh .profile-grid > *, .dh .feature, ' +
                '.dh .program-card, .dh .news-card, .dh .level, .dh .time, .dh .g-item, .dh .achv-card, .dh .psb-box > *, .dh .contact .container > *'
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
        });
    </script>
@endsection
