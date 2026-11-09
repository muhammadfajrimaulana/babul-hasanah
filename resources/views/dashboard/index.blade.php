@extends('layouts.admin')

@section('title', 'Dashboard | Pondok Pesantren Babul Hasanah')

@section('content')

    @php
        use Illuminate\Support\Facades\Route;
        $user = auth()->user();
        $namaDepan = \Illuminate\Support\Str::before($user->name ?? 'Admin', ' ');
        $tanggal = now()->locale('id')->translatedFormat('l, d F Y');
        $avatarUrl = $user->avatar ? asset('storage/' . $user->avatar) : asset('assets/img/logo/logo.png');
        $ada = function ($nama, $param = []) {
            return Route::has($nama) ? route($nama, $param) : null;
        };
    @endphp

    <!-- Chart.js + Fonts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --surface: #ffffff;
            --tint: #f8fafc;
            --chip: #f1f5f9;
            --accent: #12a67a;
            --accent-soft: #e0f2ee;
            --gold: #b45309;
            --gold-soft: #fff7e6;
            --blue: #2563eb;
            --blue-soft: #eef2ff;
            --rose: #e11d48;
            --rose-soft: #fff1f2;
            --shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.06);
            --shadow-lg: 0 20px 40px -10px rgba(15, 23, 42, 0.10);
        }

        .dark {
            --ink: #f8fafc;
            --muted: #94a3b8;
            --line: #334155;
            --surface: #0f172a;
            --tint: #020617;
            --chip: #1e293b;
            --accent: #34d399;
            --accent-soft: #064e3b;
            --gold: #fbbf24;
            --gold-soft: #451a03;
            --blue: #60a5fa;
            --blue-soft: #1e3a8a;
            --rose: #fb7185;
            --rose-soft: #490d14;
            --shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 20px 40px -10px rgba(0, 0, 0, 0.5);
        }

        * {
            box-sizing: border-box;
        }

        .db-page {
            font-family: 'Poppins', system-ui, -apple-system, sans-serif;
            color: var(--ink);
            background: var(--tint);
            min-height: 100%;
        }

        .db-page *,
        .db-page *::before,
        .db-page *::after {
            box-sizing: border-box;
        }

        .db-page a {
            text-decoration: none;
            color: inherit;
        }

        .db-page h1,
        .db-page h2,
        .db-page h3,
        .db-page h4,
        .db-page p {
            margin: 0;
        }

        /* ===================== HEADER ===================== */
        .db-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 28px;
        }

        .db-greeting {
            flex: 1;
            min-width: 280px;
        }

        .db-title {
            font-size: clamp(20px, 2.6vw, 26px);
            font-weight: 700;
            color: var(--ink);
            line-height: 1.25;
            letter-spacing: -0.02em;
        }

        .db-title span {
            color: var(--accent);
        }

        .db-sub {
            font-size: 13px;
            color: var(--muted);
            margin-top: 6px;
            line-height: 1.5;
        }

        .db-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .db-date {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--muted);
            background: var(--surface);
            border: 1px solid var(--line);
            padding: 8px 14px;
            border-radius: 10px;
            white-space: nowrap;
        }

        .db-avatar-wrap {
            position: relative;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--line);
            flex-shrink: 0;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .db-avatar-wrap:hover {
            border-color: var(--accent);
            transform: scale(1.04);
        }

        .db-avatar-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* ===================== STAT CARDS ===================== */
        .db-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 26px;
        }

        .db-stat {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        .db-stat:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--accent);
        }

        .db-stat::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--accent);
            opacity: 0.9;
        }

        .db-stat-blue::before {
            background: var(--blue);
        }

        .db-stat-gold::before {
            background: var(--gold);
        }

        .db-stat-rose::before {
            background: var(--rose);
        }

        .db-stat-accent::before {
            background: var(--accent);
        }

        /* Catatan: seluruh aturan responsive ada di blok
           "RESPONSIVE FINAL" di akhir stylesheet agar tidak
           tertimpa aturan dasar di bawah ini. */

        .db-stat-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .db-stat-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
        }

        .db-stat-ico {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 14px;
            color: #fff;
            flex-shrink: 0;
            background: var(--accent);
            box-shadow: 0 4px 12px -2px rgba(18, 166, 122, 0.35);
        }

        .db-stat-blue .db-stat-ico {
            background: var(--blue);
            box-shadow: 0 4px 12px -2px rgba(37, 99, 235, 0.35);
        }

        .db-stat-gold .db-stat-ico {
            background: var(--gold);
            box-shadow: 0 4px 12px -2px rgba(180, 83, 9, 0.35);
        }

        .db-stat-rose .db-stat-ico {
            background: var(--rose);
            box-shadow: 0 4px 12px -2px rgba(225, 29, 72, 0.35);
        }

        .db-stat-num {
            font-size: 28px;
            font-weight: 700;
            color: var(--ink);
            line-height: 1;
            margin-top: 2px;
        }

        .db-stat-num span {
            font-size: 12px;
            font-weight: 500;
            color: var(--accent);
            margin-left: 6px;
        }

        .db-stat-blue .db-stat-num span {
            color: var(--blue);
        }

        .db-stat-gold .db-stat-num span {
            color: var(--gold);
        }

        .db-stat-rose .db-stat-num span {
            color: var(--rose);
        }

        .db-stat-desc {
            font-size: 11px;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 4px;
        }

        .db-stat-desc i {
            font-size: 10px;
            opacity: 0.7;
        }

        /* ===================== MAIN LAYOUT ===================== */
        .db-main {
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 24px;
            align-items: start;
        }

        .db-left {
            display: flex;
            flex-direction: column;
            gap: 20px;
            min-width: 0;
        }

        .db-right {
            display: flex;
            flex-direction: column;
            gap: 20px;
            position: sticky;
            top: 20px;
            align-self: start;
            min-width: 0;
        }

        /* ===================== PANEL ===================== */
        .db-panel {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow);
            transition: all 0.25s ease;
            min-width: 0;
        }

        .db-panel:hover {
            box-shadow: var(--shadow-lg);
        }

        .db-panel-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .db-panel-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .db-panel-ico {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 15px;
            color: #fff;
            flex-shrink: 0;
        }

        .db-panel-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--ink);
            display: block;
        }

        .db-panel-sub {
            font-size: 11.5px;
            color: var(--muted);
            margin-top: 4px;
            line-height: 1.4;
            display: block;
        }

        .db-panel-link {
            font-size: 12px;
            font-weight: 600;
            color: var(--accent);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .db-panel-link:hover {
            color: var(--ink);
            gap: 10px;
        }

        .db-panel-body {
            padding: 22px 24px;
        }

        /* ===================== CHART CONTAINER ===================== */
        .db-chart-box {
            position: relative;
            height: 240px;
            width: 100%;
        }

        .db-chart-bar {
            height: 150px;
        }

        .db-chart-caption {
            font-size: 12px;
            color: var(--muted);
            text-align: center;
            margin-top: 8px;
        }

        .db-charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        /* ===================== INFO HARIAN ===================== */
        .db-info-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .db-info-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: var(--surface);
            transition: all 0.2s ease;
        }

        .db-info-item:hover {
            border-color: var(--accent);
            transform: translateX(3px);
        }

        .db-info-ico {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 14px;
            color: #fff;
            flex-shrink: 0;
        }

        .db-info-content {
            flex: 1;
            min-width: 0;
        }

        .db-info-title {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--ink);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .db-info-meta {
            font-size: 10.5px;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 3px;
        }

        .db-info-meta i {
            font-size: 9px;
            color: var(--accent);
        }

        .db-info-pill {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 3px 10px;
            border-radius: 999px;
            flex-shrink: 0;
        }

        .db-info-pill.jadwal {
            background: var(--accent-soft);
            color: var(--accent);
        }

        .db-info-pill.penting {
            background: var(--rose-soft);
            color: var(--rose);
        }

        .db-info-pill.aktif {
            background: var(--chip);
            color: var(--ink);
        }

        .db-empty {
            text-align: center;
            padding: 40px 20px;
            color: var(--muted);
        }

        .db-empty i {
            font-size: 36px;
            color: var(--line);
            display: block;
            margin-bottom: 12px;
            opacity: 0.5;
        }

        /* ===================== KALENDER ===================== */
        .db-cal-panel {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .db-cal-header {
            background: var(--ink);
            color: #fff;
            padding: 20px 22px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .dark .db-cal-header {
            background: var(--ink);
        }

        .db-cal-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .db-cal-title i {
            color: var(--gold);
        }

        .db-cal-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .db-cal-month {
            font-size: 14px;
            font-weight: 600;
            color: var(--gold);
        }

        .db-cal-btn {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .db-cal-btn:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .db-cal-body {
            padding: 16px;
        }

        .db-cal-week {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
            margin-bottom: 6px;
        }

        .db-cal-day-name {
            font-size: 9.5px;
            font-weight: 600;
            text-align: center;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 4px 0;
        }

        .db-cal-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
        }

        .db-cal-day {
            aspect-ratio: 1;
            border-radius: 10px;
            border: 1px solid transparent;
            background: var(--chip);
            color: var(--muted);
            font-size: 11px;
            font-weight: 600;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .db-cal-day:hover {
            background: var(--tint);
            border-color: var(--accent);
            color: var(--ink);
        }

        .db-cal-day.today {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
        }

        .db-cal-day.other {
            color: var(--muted);
            opacity: 0.4;
        }

        .db-cal-day.has-event {
            position: relative;
        }

        .db-cal-day.has-event::after {
            content: '';
            position: absolute;
            bottom: 6px;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--gold);
        }

        .db-cal-day .day-num {
            font-weight: 700;
        }

        .db-cal-day .day-events {
            font-size: 8px;
            font-weight: 600;
            margin-top: 2px;
            text-align: center;
            line-height: 1.1;
            display: -webkit-box;
            overflow: hidden;
        }

        .db-cal-day .day-event-1 {
            color: var(--accent);
        }

        .db-cal-day .day-event-2 {
            color: var(--gold);
        }

        .db-cal-add {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 14px 16px;
            border-top: 1px solid var(--line);
            background: var(--tint);
        }

        .db-cal-add-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            background: var(--ink);
            color: #fff;
            border: none;
            padding: 8px 14px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .db-cal-add-btn:hover {
            background: var(--accent);
        }

        /* ===================== EVENT LIST ===================== */
        .db-event-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .db-event-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid var(--line);
            background: var(--surface);
            transition: all 0.2s ease;
        }

        .db-event-item:hover {
            border-color: var(--accent);
            transform: translateX(2px);
        }

        .db-event-ico {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            font-size: 13px;
            color: #fff;
            flex-shrink: 0;
        }

        .db-event-content {
            flex: 1;
            min-width: 0;
        }

        .db-event-title {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--ink);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .db-event-meta {
            font-size: 10px;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 2px;
        }

        .db-event-meta i {
            font-size: 9px;
        }

        /* ===================== ALERT ===================== */
        .db-alert {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 16px;
        }

        .db-alert.ok {
            background: var(--accent-soft);
            color: var(--accent);
        }

        .db-alert.bad {
            background: var(--rose-soft);
            color: var(--rose);
        }

        /* ===================== TOAST ===================== */
        #db-toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 60;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 14px 18px;
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateY(120px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        #db-toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        #db-toast.ok {
            border-left: 4px solid var(--accent);
        }

        #db-toast i {
            color: var(--accent);
            font-size: 16px;
        }

        #db-toast-msg {
            font-size: 13px;
            font-weight: 500;
            color: var(--ink);
        }

        #db-toast-close {
            font-size: 11px;
            color: var(--muted);
            cursor: pointer;
            margin-left: auto;
        }

        /* ===================== CHART CONTAINERS (FIXED HEIGHT) ===================== */
        .db-chart-box-sm {
            height: 180px;
        }

        .db-chart-box-lg {
            height: 300px;
        }

        /* ===================== STRIP DATA JENJANG ===================== */
        .db-jenjang-strip {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 8px;
            margin-bottom: 16px;
        }

        .db-jenjang-mini {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1px;
            padding: 10px 6px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: var(--tint);
            text-align: center;
            transition: all .2s ease;
            min-width: 0;
        }

        .db-jenjang-mini:hover {
            border-color: var(--accent);
            background: var(--surface);
            transform: translateY(-2px);
        }

        .db-jenjang-mini .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-bottom: 2px;
        }

        .db-jenjang-mini .kode {
            font-size: 9px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .02em;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .db-jenjang-mini b {
            font-size: 16px;
            font-weight: 700;
            color: var(--ink);
            line-height: 1.1;
        }

        .db-jenjang-mini small {
            font-size: 8.5px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        /* ===================== MOBILE POLISH ===================== */
        .db-page {
            overflow-x: clip;
        }

        @media (max-width: 1024px) {
            .db-jenjang-strip {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .db-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            .db-stat {
                padding: 14px 15px;
                border-radius: 16px;
            }

            .db-stat-num {
                font-size: 22px;
            }

            .db-stat-label {
                font-size: 9px;
            }

            .db-stat-desc {
                font-size: 10px;
                line-height: 1.3;
            }

            .db-stat-ico {
                width: 28px;
                height: 28px;
                font-size: 12px;
                border-radius: 9px;
            }

            .db-header {
                margin-bottom: 18px;
            }

            .db-title {
                font-size: 20px;
            }

            .db-sub {
                font-size: 12px;
            }

            .db-date {
                padding: 7px 12px;
                font-size: 11px;
            }

            .db-avatar-wrap {
                width: 40px;
                height: 40px;
            }

            .db-panel {
                border-radius: 16px;
            }

            .db-panel-header {
                padding: 14px 16px;
                align-items: flex-start;
            }

            .db-panel-body {
                padding: 16px;
            }

            .db-panel-title {
                font-size: 14px;
            }

            .db-panel-sub {
                font-size: 11px;
            }

            .db-panel-link {
                font-size: 11px;
            }

            .db-chart-box {
                height: 210px;
            }

            .db-chart-box-sm {
                height: 190px;
            }

            .db-chart-box-lg {
                height: 260px;
            }

            .db-cal-body {
                padding: 12px;
            }

            .db-cal-day {
                border-radius: 8px;
                font-size: 10px;
            }

            .db-cal-day .day-events {
                display: none;
            }

            .db-cal-day.has-event::after {
                bottom: 4px;
                width: 5px;
                height: 5px;
            }

            .db-info-item {
                padding: 12px;
                gap: 10px;
                align-items: flex-start;
            }

            .db-info-title {
                font-size: 12.5px;
                white-space: normal;
                line-height: 1.35;
            }

            .db-info-pill {
                display: none;
            }

            .db-event-item {
                padding: 10px 12px;
                align-items: flex-start;
            }

            .db-event-title {
                white-space: normal;
                line-height: 1.35;
            }
        }

        @media (max-width: 560px) {
            .db-page {
                padding: 0;
            }

            .db-stats {
                gap: 8px;
            }

            .db-stat {
                padding: 12px 13px;
            }

            .db-stat-num {
                font-size: 20px;
            }

            .db-stat-desc {
                font-size: 9.5px;
            }

            .db-meta {
                gap: 8px;
            }

            .db-date {
                flex: 1;
                text-align: center;
            }

            .db-panel-body {
                padding: 14px;
            }

            .db-chart-box {
                height: 190px;
            }

            .db-chart-box-sm {
                height: 180px;
            }

            .db-chart-box-lg {
                height: 240px;
            }

            .db-jenjang-strip {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 6px;
            }

            .db-jenjang-mini {
                padding: 9px 4px;
            }

            .db-jenjang-mini b {
                font-size: 15px;
            }

            .db-cal-body {
                padding: 10px;
            }

            .db-cal-day {
                font-size: 9.5px;
            }

            #db-toast {
                left: 12px;
                right: 12px;
                bottom: 12px;
            }
        }

        /* ===================== KALENDER v2 ===================== */
        [x-cloak] {
            display: none !important;
        }

        .db-cal-panel {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 22px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .db-cal-header {
            position: relative;
            padding: 18px 20px 16px;
            color: #fff;
            background: linear-gradient(135deg, #0b1220 0%, #123b2b 58%, #17634a 100%);
            overflow: hidden;
        }

        .db-cal-header::before,
        .db-cal-header::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .db-cal-header::before {
            width: 190px;
            height: 190px;
            right: -70px;
            top: -110px;
            background: rgba(255, 255, 255, .08);
        }

        .db-cal-header::after {
            width: 130px;
            height: 130px;
            right: 45px;
            bottom: -95px;
            background: rgba(18, 166, 122, .25);
        }

        .db-cal-top {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .db-cal-title {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            font-size: 13.5px;
            font-weight: 700;
            letter-spacing: .01em;
        }

        .db-cal-title i {
            color: var(--gold);
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, .35));
        }

        .db-cal-today-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            border: 1px solid rgba(255, 255, 255, .22);
            border-radius: 999px;
            background: rgba(255, 255, 255, .10);
            color: #fff;
            font-family: inherit;
            font-size: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s ease;
        }

        .db-cal-today-btn:hover {
            background: rgba(255, 255, 255, .22);
        }

        .db-cal-nav {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-top: 14px;
        }

        .db-cal-month {
            text-align: center;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.15;
            text-transform: capitalize;
        }

        .db-cal-month small {
            display: block;
            margin-top: 2px;
            color: rgba(255, 255, 255, .6);
            font-size: 9px;
            font-weight: 500;
            letter-spacing: .08em;
        }

        .db-cal-btn {
            display: grid;
            place-items: center;
            flex-shrink: 0;
            width: 32px;
            height: 32px;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 10px;
            background: rgba(255, 255, 255, .08);
            color: #fff;
            font-size: 12px;
            cursor: pointer;
            transition: all .2s ease;
        }

        .db-cal-btn:hover {
            background: rgba(255, 255, 255, .22);
            transform: translateY(-1px);
        }

        .db-cal-notif {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 8px;
            width: calc(100% - 28px);
            margin: 12px 14px 0;
            padding: 9px 12px;
            border: 1px solid rgba(251, 191, 36, .35);
            border-radius: 12px;
            background: rgba(251, 191, 36, .12);
            color: #fde68a;
            font-family: inherit;
            font-size: 11px;
            font-weight: 600;
            text-align: left;
            cursor: pointer;
            transition: background .2s ease;
        }

        .db-cal-notif:hover {
            background: rgba(251, 191, 36, .2);
        }

        .db-cal-notif .fa-bell {
            color: #fbbf24;
        }

        .db-cal-notif .fa-chevron-right {
            margin-left: auto;
            font-size: 9px;
            opacity: .8;
        }

        .db-cal-body {
            padding: 14px 14px 8px;
        }

        .db-cal-week {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
            margin-bottom: 4px;
        }

        .db-cal-day-name {
            padding: 4px 0;
            color: var(--muted);
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .06em;
            text-align: center;
            text-transform: uppercase;
        }

        .db-cal-day-name.weekend {
            color: var(--rose);
        }

        .db-cal-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
        }

        .db-cal-day {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            width: 100%;
            min-width: 0;
            aspect-ratio: 1 / 1;
            padding: 6px 2px 2px;
            border: 1px solid transparent;
            border-radius: 12px;
            background: transparent;
            color: var(--ink);
            font-family: inherit;
            font-size: 11.5px;
            cursor: pointer;
            -webkit-appearance: none;
            appearance: none;
            transition: background .18s ease, border-color .18s ease, transform .18s ease, color .18s ease;
        }

        .db-cal-day:hover {
            background: var(--chip);
            border-color: var(--line);
            transform: translateY(-1px);
        }

        .db-cal-day.today {
            border-color: var(--accent);
            background: var(--accent-soft);
            color: var(--accent);
        }

        .db-cal-day.today .day-num {
            font-weight: 800;
        }

        .db-cal-day.selected {
            border-color: var(--ink);
            background: var(--ink);
            color: var(--surface);
            box-shadow: 0 8px 18px -6px rgba(15, 23, 42, .45);
        }

        .db-cal-day.selected:hover {
            background: var(--ink);
        }

        .db-cal-day.other {
            opacity: 0;
            pointer-events: none;
        }

        .db-cal-day .day-num {
            font-weight: 600;
            line-height: 1;
        }

        .db-cal-day .day-dots {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2px;
            max-width: 100%;
            margin-top: 4px;
        }

        .db-cal-day .day-dots span {
            flex-shrink: 0;
            width: 4px;
            height: 4px;
            border-radius: 50%;
        }

        .db-cal-day .day-count {
            position: absolute;
            top: 3px;
            right: 3px;
            display: grid;
            place-items: center;
            min-width: 13px;
            height: 13px;
            padding: 0 3px;
            border-radius: 999px;
            background: var(--accent);
            color: #fff;
            font-size: 8px;
            font-weight: 800;
            line-height: 1;
        }

        .db-cal-day.selected .day-count {
            background: #fff;
            color: var(--ink);
        }

        .db-cal-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 12px;
            padding: 4px 16px 12px;
        }

        .db-cal-legend span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: var(--muted);
            font-size: 9.5px;
            font-weight: 600;
        }

        .db-cal-legend i {
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 50%;
        }

        .db-cal-add {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 12px 16px;
            border-top: 1px solid var(--line);
            background: var(--tint);
        }

        .db-cal-add-hint {
            margin: 0;
            color: var(--muted);
            font-size: 10px;
            line-height: 1.35;
        }

        .db-cal-add-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
            padding: 9px 14px;
            border: none;
            border-radius: 11px;
            background: linear-gradient(135deg, #12a67a, #0e8a66);
            color: #fff;
            font-family: inherit;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 6px 14px -6px rgba(18, 166, 122, .7);
            transition: all .2s ease;
        }

        .db-cal-add-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px -8px rgba(18, 166, 122, .8);
        }

        /* ===================== MODAL NOTE ===================== */
        .db-modal {
            position: fixed;
            inset: 0;
            z-index: 80;
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }

        @media (min-width: 640px) {
            .db-modal {
                align-items: center;
                padding: 24px;
            }
        }

        .db-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(2, 6, 23, .62);
            backdrop-filter: blur(4px);
        }

        .db-modal-card {
            position: relative;
            width: 100%;
            max-width: 460px;
            max-height: 92vh;
            overflow-y: auto;
            border-radius: 22px 22px 0 0;
            background: var(--surface);
            box-shadow: 0 30px 70px -20px rgba(2, 6, 23, .65);
            animation: dbModalIn .22s ease;
        }

        @media (min-width: 640px) {
            .db-modal-card {
                border-radius: 22px;
            }
        }

        @keyframes dbModalIn {
            from {
                opacity: 0;
                transform: translateY(16px) scale(.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .db-modal-head {
            position: relative;
            overflow: hidden;
            padding: 16px 18px;
            color: #fff;
            background: linear-gradient(135deg, #0b1220 0%, #123b2b 58%, #17634a 100%);
        }

        .db-modal-head::after {
            content: '';
            position: absolute;
            top: -90px;
            right: -60px;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
            pointer-events: none;
        }

        .db-modal-head-row {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .db-modal-ico {
            display: grid;
            place-items: center;
            flex-shrink: 0;
            width: 38px;
            height: 38px;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 12px;
            background: rgba(255, 255, 255, .14);
        }

        .db-modal-date {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.25;
        }

        .db-modal-sub {
            margin: 2px 0 0;
            color: rgba(255, 255, 255, .65);
            font-size: 10px;
        }

        .db-modal-close {
            display: grid;
            place-items: center;
            flex-shrink: 0;
            width: 30px;
            height: 30px;
            margin-left: auto;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 9px;
            background: rgba(255, 255, 255, .08);
            color: #fff;
            font-size: 12px;
            cursor: pointer;
            transition: background .2s ease;
        }

        .db-modal-close:hover {
            background: rgba(255, 255, 255, .2);
        }

        .db-modal-body {
            padding: 16px 18px 18px;
        }

        .db-note-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .db-note-card {
            display: flex;
            gap: 10px;
            padding: 12px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: var(--tint);
            transition: all .2s ease;
        }

        .db-note-card:hover {
            border-color: var(--accent);
            background: var(--surface);
        }

        .db-note-bar {
            flex-shrink: 0;
            width: 4px;
            border-radius: 999px;
        }

        .db-note-main {
            flex: 1;
            min-width: 0;
        }

        .db-note-title {
            margin: 0;
            color: var(--ink);
            font-size: 12.5px;
            font-weight: 700;
            line-height: 1.3;
        }

        .db-note-desc {
            margin: 2px 0 0;
            color: var(--muted);
            font-size: 10.5px;
            line-height: 1.45;
            white-space: pre-line;
        }

        .db-note-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
            margin-top: 6px;
            color: var(--muted);
            font-size: 9.5px;
        }

        .db-note-meta .tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 7px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 700;
        }

        .db-note-actions {
            display: flex;
            flex-shrink: 0;
            align-items: flex-start;
            gap: 2px;
        }

        .db-note-icon-btn {
            display: grid;
            place-items: center;
            width: 26px;
            height: 26px;
            border: none;
            border-radius: 8px;
            background: transparent;
            color: var(--muted);
            font-size: 11px;
            cursor: pointer;
            transition: all .18s ease;
        }

        .db-note-icon-btn:hover {
            background: var(--chip);
            color: var(--ink);
        }

        .db-note-icon-btn.danger:hover {
            background: var(--rose-soft);
            color: var(--rose);
        }

        .db-note-empty {
            padding: 22px 14px;
            border: 1px dashed var(--line);
            border-radius: 14px;
            background: var(--tint);
            text-align: center;
        }

        .db-note-empty i {
            color: var(--line);
            font-size: 22px;
        }

        .db-note-empty p {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: 11px;
        }

        .db-note-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .db-field label {
            display: block;
            margin-bottom: 4px;
            color: var(--muted);
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .db-field input,
        .db-field select,
        .db-field textarea {
            width: 100%;
            padding: 9px 11px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: var(--surface);
            color: var(--ink);
            font-family: inherit;
            font-size: 12px;
            transition: border-color .18s ease, box-shadow .18s ease;
        }

        .db-field input:focus,
        .db-field select:focus,
        .db-field textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-soft);
        }

        .db-field textarea {
            min-height: 64px;
            resize: vertical;
        }

        .db-field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .db-form-actions {
            display: flex;
            gap: 8px;
            padding-top: 4px;
        }

        .db-btn-primary {
            flex: 1;
            padding: 10px 14px;
            border: none;
            border-radius: 11px;
            background: linear-gradient(135deg, #12a67a, #0e8a66);
            color: #fff;
            font-family: inherit;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s ease;
        }

        .db-btn-primary:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
        }

        .db-btn-ghost {
            padding: 10px 14px;
            border: 1px solid var(--line);
            border-radius: 11px;
            background: transparent;
            color: var(--muted);
            font-family: inherit;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s ease;
        }

        .db-btn-ghost:hover {
            background: var(--chip);
            color: var(--ink);
        }

        .db-note-add {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            margin-top: 10px;
            padding: 9px;
            border: 1px dashed var(--accent);
            border-radius: 11px;
            background: var(--accent-soft);
            color: var(--accent);
            font-family: inherit;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s ease;
        }

        .db-note-add:hover {
            background: var(--accent);
            color: #fff;
        }

        .db-event-item {
            cursor: pointer;
            font-family: inherit;
            text-align: left;
        }

        #db-toast.bad {
            border-left: 4px solid var(--rose);
        }

        #db-toast.bad i {
            color: var(--rose);
        }

        @media (max-width: 560px) {
            .db-cal-body {
                padding: 12px 10px 6px;
            }

            .db-cal-day {
                border-radius: 10px;
                font-size: 10px;
                padding-top: 5px;
            }

            .db-cal-day .day-count {
                top: 2px;
                right: 2px;
                min-width: 12px;
                height: 12px;
                font-size: 7.5px;
            }

            .db-cal-legend {
                padding: 2px 14px 10px;
            }

            .db-cal-add {
                padding: 10px 14px;
            }

            .db-cal-add-hint {
                font-size: 9px;
            }

            .db-field-row {
                grid-template-columns: 1fr;
            }
        }

        /* ===================== IKON KALENDER ===================== */
        .db-cal-day .day-mark,
        .db-cal-day .day-plus {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 3px;
            line-height: 1;
        }

        .db-cal-day .day-mark {
            font-size: 9px;
        }

        .db-cal-day .day-plus {
            color: var(--muted);
            font-size: 8px;
            opacity: 0;
            transition: opacity .18s ease;
        }

        .db-cal-day:hover .day-plus,
        .db-cal-day:focus-visible .day-plus {
            opacity: .75;
        }

        .db-cal-day.selected .day-plus {
            color: var(--surface);
            opacity: .8;
        }

        .db-cal-actions {
            display: flex;
            flex-shrink: 0;
            align-items: center;
            gap: 6px;
        }

        .db-cal-action {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            height: 34px;
            padding: 0 10px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: var(--surface);
            color: var(--muted);
            font-family: inherit;
            font-size: 10.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s ease;
        }

        .db-cal-action:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .db-cal-action.primary {
            border-color: transparent;
            background: linear-gradient(135deg, #12a67a, #0e8a66);
            color: #fff;
            box-shadow: 0 6px 14px -6px rgba(18, 166, 122, .7);
        }

        .db-cal-action.primary:hover {
            color: #fff;
            transform: translateY(-1px);
        }

        .db-cal-action-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            display: grid;
            place-items: center;
            min-width: 14px;
            height: 14px;
            padding: 0 3px;
            border-radius: 999px;
            background: var(--rose);
            color: #fff;
            font-size: 8px;
            font-weight: 800;
            line-height: 1;
        }

        /* ===================== MOBILE: CHART & IKON ===================== */
        @media (max-width: 768px) {
            .db-chart-box {
                height: 230px;
            }

            .db-chart-box-sm {
                height: 210px;
            }

            .db-chart-box-lg {
                height: 280px;
            }

            .db-panel-header {
                flex-wrap: wrap;
                row-gap: 6px;
            }

            .db-panel-ico {
                width: 30px;
                height: 30px;
                border-radius: 9px;
                font-size: 13px;
            }

            .db-panel-link {
                font-size: 10px;
            }

            .db-panel-link i {
                font-size: 9px;
            }

            .db-stat-head {
                align-items: center;
            }

            .db-stat-label {
                min-width: 0;
                line-height: 1.25;
            }

            .db-cal-day .day-plus {
                opacity: .4;
            }

            .db-cal-action-label {
                display: none;
            }

            .db-cal-action {
                width: 34px;
                padding: 0;
            }
        }

        @media (max-width: 560px) {
            .db-chart-box {
                height: 220px;
            }

            .db-chart-box-sm {
                height: 200px;
            }

            .db-chart-box-lg {
                height: 260px;
            }

            .db-stat-ico {
                width: 26px;
                height: 26px;
                border-radius: 8px;
                font-size: 11px;
            }

            .db-stat-label {
                font-size: 8.5px;
                letter-spacing: .04em;
            }

            .db-jenjang-strip .db-jenjang-mini:last-child:nth-child(odd) {
                grid-column: span 2;
            }
        }

        /* =========================================================
           RESPONSIVE FINAL — ditulis paling akhir agar menang
           dari aturan dasar di atas.
           ========================================================= */
        .db-charts-grid>* {
            min-width: 0;
        }

        .db-chart-box canvas {
            max-width: 100% !important;
        }

        /* Tombol cepat kalender (mobile) */
        .db-cal-jump {
            display: none;
            place-items: center;
            width: 38px;
            height: 38px;
            border: 1px solid var(--line);
            border-radius: 11px;
            background: var(--surface);
            color: var(--accent);
            font-size: 14px;
            cursor: pointer;
            flex-shrink: 0;
            transition: all .2s ease;
        }

        .db-cal-jump:active {
            transform: scale(.94);
        }

        #db-kalender {
            scroll-margin-top: 88px;
        }

        .db-cal-fab {
            position: fixed;
            right: 14px;
            bottom: calc(78px + env(safe-area-inset-bottom, 0px));
            z-index: 45;
            display: none;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
        }

        .db-cal-fab-btn {
            position: relative;
            display: grid;
            place-items: center;
            width: 48px;
            height: 48px;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: var(--surface);
            color: var(--ink);
            font-size: 17px;
            cursor: pointer;
            box-shadow: 0 12px 26px -10px rgba(15, 23, 42, .45);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .db-cal-fab-btn:active {
            transform: scale(.94);
        }

        .db-cal-fab-btn.primary {
            border-color: transparent;
            background: linear-gradient(135deg, #12a67a, #0e8a66);
            color: #fff;
        }

        .db-cal-fab-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            display: grid;
            place-items: center;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
            border-radius: 999px;
            background: var(--rose);
            color: #fff;
            font-size: 9px;
            font-weight: 800;
            line-height: 1;
        }

        @media (max-width: 1100px) {
            .db-main {
                grid-template-columns: minmax(0, 1fr);
                gap: 18px;
            }

            .db-right {
                position: static;
            }

            .db-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 900px) {
            .db-charts-grid {
                grid-template-columns: minmax(0, 1fr);
                gap: 14px;
            }

            .db-jenjang-strip {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .db-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .db-greeting {
                min-width: 100%;
            }

            .db-meta {
                width: 100%;
                justify-content: space-between;
            }

            .db-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
                margin-bottom: 18px;
            }

            .db-stat {
                padding: 14px 15px;
            }

            .db-stat-num {
                font-size: 22px;
            }

            .db-stat-desc {
                flex-wrap: wrap;
                row-gap: 2px;
                min-width: 0;
            }

            .db-stat-desc i {
                flex-shrink: 0;
                margin-top: 2px;
            }

            .db-jenjang-strip {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .db-jenjang-strip .db-jenjang-mini:last-child:nth-child(odd) {
                grid-column: 2;
            }

            .db-chart-box {
                height: 240px;
            }

            .db-chart-box-sm {
                height: 205px;
            }

            .db-chart-box-lg {
                height: 300px;
            }

            .db-cal-fab {
                display: flex;
            }

            .db-cal-jump {
                display: inline-grid;
            }

            .db-right {
                padding-bottom: 8px;
            }

            #db-toast {
                left: 12px;
                right: 12px;
                bottom: calc(84px + env(safe-area-inset-bottom, 0px));
            }

            .db-modal-card {
                padding-bottom: env(safe-area-inset-bottom, 0px);
            }
        }

        @media (max-width: 560px) {
            .db-page {
                padding: 0;
            }

            .db-stat {
                padding: 12px 13px;
                border-radius: 15px;
            }

            .db-stat-num {
                font-size: 20px;
            }

            .db-stat-label {
                font-size: 8.5px;
                letter-spacing: .04em;
            }

            .db-stat-desc {
                font-size: 9.5px;
            }

            .db-jenjang-strip {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 6px;
            }

            .db-jenjang-strip .db-jenjang-mini:last-child:nth-child(odd) {
                grid-column: span 2;
            }

            .db-chart-box {
                height: 230px;
            }

            .db-chart-box-sm {
                height: 200px;
            }

            .db-chart-box-lg {
                height: 285px;
            }

            .db-panel-body {
                padding: 14px;
            }

            .db-cal-body {
                padding: 12px 10px 6px;
            }

            .db-cal-day {
                font-size: 10px;
                border-radius: 10px;
            }

            .db-cal-fab-btn {
                width: 44px;
                height: 44px;
                border-radius: 14px;
                font-size: 16px;
            }
        }
    </style>

    <div class="db-page">

        @if (session('success'))
            <div class="db-alert ok"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="db-alert bad"><i class="fa-solid fa-circle-exclamation"></i>{{ session('error') }}</div>
        @endif

        {{-- ===================== HEADER ===================== --}}
        <div class="db-header">
            <div class="db-greeting">
                <div class="flex items-center gap-2 mb-1">
                    {{-- Badge Role Dinamis --}}
                    @if (auth()->user()->role === 'super_admin')
                        <span
                            class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-500 border border-amber-500/20 uppercase tracking-wider">Super
                            Administrator</span>
                    @elseif(auth()->user()->role === 'pengasuh')
                        <span
                            class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 uppercase tracking-wider">Pengasuh
                            Pesantren</span>
                    @elseif(auth()->user()->role === 'pembina')
                        <span
                            class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-cyan-500 border border-cyan-500/20 uppercase tracking-wider">Pembina
                            Asrama</span>
                    @else
                        <span
                            class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-500 border border-blue-500/20 uppercase tracking-wider">Admin
                            Jenjang: {{ $namaJenjang }}</span>
                    @endif
                </div>

                <h1 class="db-title">Assalamu’alaikum, <span>{{ $namaDepan }}</span> <i class="fa-solid fa-hands-praying"
                        style="font-size:0.75em;color:var(--gold)"></i></h1>

                <p class="db-sub">
                    @if (auth()->user()->role === 'super_admin')
                        Selamat datang kembali di Panel Utama Super Admin Pondok Pesantren Babul Hasanah.
                    @elseif(auth()->user()->role === 'pengasuh')
                        Selamat datang kembali, Buya/Kiyai/Ustadz. Berikut rekapitulasi pengawasan menyeluruh aktivitas
                        pondok hari ini.
                    @elseif(auth()->user()->role === 'pembina')
                        Selamat datang di Panel Manajemen Pembina Asrama. Pantau data
                        santri dan progres unit Anda di sini.
                    @else
                        Selamat datang di Panel Kontrol Admin Jenjang <strong>{{ $namaJenjang }}</strong>. Pantau data
                        santri dan progres unit Anda di sini.
                    @endif
                </p>
            </div>

            <div class="db-meta">
                <button type="button" class="db-cal-jump"
                    onclick="document.getElementById('db-kalender')?.scrollIntoView({behavior:'smooth', block:'start'})"
                    title="Buka kalender" aria-label="Buka kalender">
                    <i class="fa-solid fa-calendar-days"></i>
                </button>
                <span class="db-date">{{ $tanggal }}</span>
                <div class="db-avatar-wrap" @click="location.href='{{ route('admin.profil') }}'">
                    <img src="{{ $avatarUrl }}" alt="Profil"
                        onerror="this.src='{{ asset('assets/img/logo/logo.png') }}'">
                </div>
            </div>
        </div>

        {{-- ===================== STAT CARDS ===================== --}}
        <div class="db-stats">
            @php
                $statCards = [
                    [
                        'Santri Aktif',
                        $totalSantri,
                        'fa-users',
                        'accent',
                        "Putra: {$santriPutra} | Putri: {$santriPutri}",
                    ],
                    [
                        'Pendaftar',
                        $totalPendaftar,
                        'fa-file-invoice',
                        'blue',
                        $pendaftarMenunggu . ' menunggu verifikasi',
                    ],
                    ['Berita', $totalBerita, 'fa-newspaper', 'gold', $beritaBulanIni . ' artikel bulan ini'],
                    ['Alumni', $totalAlumni, 'fa-user-graduate', 'rose', 'Lulusan tercatat'],
                ];
            @endphp
            @foreach ($statCards as $index => $s)
                @php
                    if ($s[3] === 'accent') {
                        $badgeClass = 'db-stat-accent';
                        $icoColor = '#12a67a';
                        $icoTextColor = '#fff';
                    } elseif ($s[3] === 'blue') {
                        $badgeClass = 'db-stat-blue';
                        $icoColor = '#ffffff';
                        $icoTextColor = '#2563eb';
                    } elseif ($s[3] === 'gold') {
                        $badgeClass = 'db-stat-gold';
                        $icoColor = '#b45309';
                        $icoTextColor = '#fff';
                    } else {
                        $badgeClass = 'db-stat-rose';
                        $icoColor = '#e11d48';
                        $icoTextColor = '#fff';
                    }
                @endphp
                <div class="db-stat {{ $badgeClass }}">
                    <div class="db-stat-head">
                        <span class="db-stat-label">{{ $s[0] }}</span>
                        <span class="db-stat-ico" style="background: {{ $icoColor }}"><i
                                class="fa-solid {{ $s[2] }}" style="color: {{ $icoTextColor }}"></i></span>
                    </div>
                    <div class="db-stat-num"><b>{{ number_format($s[1], 0, ',', '.') }}</b></div>
                    <small class="db-stat-desc"><i class="fa-solid fa-circle"></i>{{ $s[4] }}</small>
                </div>
            @endforeach
        </div>

        {{-- ===================== MAIN LAYOUT ===================== --}}
        <div class="db-main">

            {{-- KIRI: CHARTS & INFO ===================== --}}
            <div class="db-left">

                {{-- Grafik Pendaftar per Jenjang --}}
                <div class="db-panel">
                    <div class="db-panel-header">
                        <div class="db-panel-left">
                            <span class="db-panel-ico" style="background: #2563eb"><i
                                    class="fa-solid fa-chart-bar"></i></span>
                            <div>
                                <h3 class="db-panel-title">Pendaftar per Jenjang</h3>
                                <p class="db-panel-sub">Distribusi calon santri berdasarkan jenjang tujuan pendaftaran.</p>
                            </div>
                        </div>
                        <a href="{{ $ada('admin.pendaftaran.index') }}" class="db-panel-link">Lihat semua <i
                                class="fa-solid fa-arrow-right"></i></a>
                    </div>
                    <div class="db-panel-body" style="align-items: flex-start;">
                        <div class="db-chart-box db-chart-box-sm">
                            <canvas id="chartJenjang"></canvas>
                        </div>
                        <p class="db-chart-caption">Jumlah pendaftar yang telah dikumpulkan per jenjang pendidikan.</p>
                    </div>
                </div>

                {{-- Grafik Santri & Alumni --}}
                <div class="db-charts-grid">
                    <div class="db-panel">
                        <div class="db-panel-header">
                            <div class="db-panel-left">
                                <span class="db-panel-ico" style="background: #12a67a"><i
                                        class="fa-solid fa-people-group"></i></span>
                                <div>
                                    <h3 class="db-panel-title">Data Santri Aktif</h3>
                                    <p class="db-panel-sub">Komposisi santri berdasarkan jenjang pendidikan.</p>
                                </div>
                            </div>
                        </div>
                        <div class="db-panel-body">
                            <div class="db-chart-box">
                                <canvas id="chartSantri"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="db-panel">
                        <div class="db-panel-header">
                            <div class="db-panel-left">
                                <span class="db-panel-ico" style="background: #b45309"><i
                                        class="fa-solid fa-user-graduate"></i></span>
                                <div>
                                    <h3 class="db-panel-title">Data Alumni</h3>
                                    <p class="db-panel-sub">Distribusi alumni berdasarkan jenjang pendidikan.</p>
                                </div>
                            </div>
                        </div>
                        <div class="db-panel-body">
                            <div class="db-chart-box">
                                <canvas id="chartAlumni"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Grafik Data per Jenjang --}}
                <div class="db-panel">
                    <div class="db-panel-header">
                        <div class="db-panel-left">
                            <span class="db-panel-ico" style="background: #8b5cc6"><i
                                    class="fa-solid fa-layer-group"></i></span>
                            <div>
                                <h3 class="db-panel-title">Data per Jenjang</h3>
                                <p class="db-panel-sub">Ringkasan santri, pendaftar, dan alumni pada 7 jenjang pendidikan.
                                </p>
                            </div>
                        </div>
                        <a href="{{ $ada('admin.jenjang.index') }}" class="db-panel-link">Kelola jenjang <i
                                class="fa-solid fa-arrow-right"></i></a>
                    </div>
                    <div class="db-panel-body">
                        <div class="db-jenjang-strip">
                            @foreach ($jenjangStats as $j)
                                <a href="{{ route('admin.jenjang.show', $j['slug']) }}" class="db-jenjang-mini"
                                    title="{{ $j['nama'] }}">
                                    <span class="dot" style="background: {{ $j['warna'] }}"></span>
                                    <span class="kode">{{ $j['kode'] }}</span>
                                    <b>{{ number_format($j['santri'], 0, ',', '.') }}</b>
                                    <small>santri</small>
                                </a>
                            @endforeach
                        </div>
                        <div class="db-chart-box db-chart-box-lg">
                            <canvas id="chartDataJenjang"></canvas>
                        </div>
                        <p class="db-chart-caption">Klik salah satu jenjang di atas untuk melihat data lengkapnya.</p>
                    </div>
                </div>

                {{-- Info Harian --}}
                <div class="db-panel">
                    <div class="db-panel-header">
                        <div class="db-panel-left">
                            <span class="db-panel-ico" style="background: #e11d48"><i
                                    class="fa-solid fa-bullhorn"></i></span>
                            <div>
                                <h3 class="db-panel-title">Info Harian</h3>
                                <p class="db-panel-sub">Pengumuman dan jadwal terbaru dari pesantren.</p>
                            </div>
                        </div>
                        <a href="{{ $ada('admin.info.index') }}" class="db-panel-link">Kelola Info <i
                                class="fa-solid fa-arrow-right"></i></a>
                    </div>
                    <div class="db-panel-body">
                        <div class="db-info-list">
                            @if (count($infoHarian))
                                @foreach ($infoHarian as $info)
                                    @php
                                        if ($info->jenis === 'jadwal') {
                                            $jenjangClass = 'jadwal';
                                            $icoColor = '#12a67a';
                                        } elseif ($info->penting) {
                                            $jenjangClass = 'penting';
                                            $icoColor = '#e11d48';
                                        } else {
                                            $jenjangClass = 'aktif';
                                            $icoColor = '#12a67a';
                                        }
                                        $ikonInfo = $info->ikon ?? 'fa-circle-info';
                                        $tglLabel = $info->waktu_label ?? '';
                                        $kategoriLabel = $info->kategori ?? '-';
                                    @endphp
                                    <div class="db-info-item">
                                        <span class="db-info-ico" style="background: {{ $icoColor }}"><i
                                                class="fa-solid {{ $ikonInfo }}"></i></span>
                                        <div class="db-info-content">
                                            <div class="db-info-title">{{ $info->judul }}</div>
                                            <div class="db-info-meta">
                                                <span><i class="fa-regular fa-calendar"></i>{{ $tglLabel }}</span>
                                                <span><i class="fa-solid fa-tag"></i>{{ $kategoriLabel }}</span>
                                            </div>
                                        </div>
                                        <span
                                            class="db-info-pill {{ $jenjangClass }}">{{ ucfirst($jenjangClass) }}</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="db-empty">
                                    <i class="fa-regular fa-bell"></i>
                                    <div>Belum ada info harian yang aktif.</div>
                                    @if ($user->role == 'super_admin')
                                        <div class="mt-3"><a href="{{ $ada('admin.info.create') }}"
                                                class="db-panel-link">Tambah Info <i class="fa-solid fa-plus"></i></a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            {{-- KANAN: KALENDER ===================== --}}
            <div class="db-right" x-data="kalenderApp()" @keydown.escape.window="tutupModal()"
                x-effect="document.body.style.overflow = modalOpen ? 'hidden' : ''">
                <div class="db-cal-panel" id="db-kalender">
                    <div class="db-cal-header">
                        <div class="db-cal-top">
                            <div class="db-cal-title"><i class="fa-solid fa-calendar-days"></i> Kalender Kegiatan</div>
                            <button type="button" class="db-cal-today-btn" @click="keHariIni()">
                                <i class="fa-solid fa-location-crosshairs"></i> Hari ini
                            </button>
                        </div>
                        <div class="db-cal-nav">
                            <button type="button" class="db-cal-btn" @click="prevMonth()"
                                aria-label="Bulan sebelumnya"><i class="fa-solid fa-chevron-left"></i></button>
                            <span class="db-cal-month">
                                <span x-text="namaBulan"></span>
                                <small x-text="tanggal.getFullYear()"></small>
                            </span>
                            <button type="button" class="db-cal-btn" @click="nextMonth()"
                                aria-label="Bulan berikutnya"><i class="fa-solid fa-chevron-right"></i></button>
                        </div>
                    </div>

                    {{-- Notifikasi catatan hari ini --}}
                    <button type="button" class="db-cal-notif" x-show="catatanHariIni.length" x-cloak
                        @click="bukaTanggal(todayKey)">
                        <i class="fa-solid fa-bell"></i>
                        <span><b x-text="catatanHariIni.length"></b> catatan untuk hari ini — ketuk untuk melihat</span>
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>

                    <div class="db-cal-body">
                        <div class="db-cal-week">
                            <div class="db-cal-day-name weekend">Min</div>
                            <div class="db-cal-day-name">Sen</div>
                            <div class="db-cal-day-name">Sel</div>
                            <div class="db-cal-day-name">Rab</div>
                            <div class="db-cal-day-name">Kam</div>
                            <div class="db-cal-day-name">Jum</div>
                            <div class="db-cal-day-name weekend">Sab</div>
                        </div>
                        <div class="db-cal-days">
                            <template x-for="i in hariSebelum" :key="'p' + i">
                                <div class="db-cal-day other"></div>
                            </template>
                            <template x-for="i in jumlahHari" :key="'d' + i">
                                <button type="button" class="db-cal-day"
                                    :class="{
                                        'today': isToday(i),
                                        'selected': isSelected(i)
                                    }"
                                    @click="pilihHari(i)" :aria-label="labelHari(i)">
                                    <span class="day-num" x-text="i"></span>
                                    <span class="day-mark" x-show="jumlahCatatan(i) > 0" aria-hidden="true">
                                        <i class="fa-solid fa-note-sticky"
                                            :style="'color:' + (warnaHari(i)[0] || '#12a67a')"></i>
                                    </span>
                                    <span class="day-plus" x-show="jumlahCatatan(i) === 0" aria-hidden="true">
                                        <i class="fa-solid fa-plus"></i>
                                    </span>
                                    <span class="day-count" x-show="jumlahCatatan(i) > 1" x-text="jumlahCatatan(i)"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <div class="db-cal-legend">
                        <span><i style="background:#12a67a"></i> Jadwal</span>
                        <span><i style="background:#b45309"></i> Agenda</span>
                        <span><i style="background:#2563eb"></i> Pengumuman</span>
                        <span><i style="background:#e11d48"></i> Libur</span>
                    </div>

                    <div class="db-cal-add">
                        <p class="db-cal-add-hint">Ketuk tanggal untuk melihat<br>atau menambah catatan.</p>
                        <div class="db-cal-actions">
                            <button type="button" class="db-cal-action" @click="bukaTanggal(dipilih || todayKey)"
                                title="Lihat catatan" aria-label="Lihat catatan">
                                <i class="fa-solid fa-note-sticky"></i>
                                <span class="db-cal-action-label">Lihat</span>
                                <span class="db-cal-action-badge"
                                    x-show="catatanPada(dipilih || todayKey).length > 0"
                                    x-text="catatanPada(dipilih || todayKey).length"></span>
                            </button>
                            <button type="button" class="db-cal-action primary" @click="bukaTambah()"
                                title="Tambah catatan" aria-label="Tambah catatan">
                                <i class="fa-solid fa-plus"></i>
                                <span class="db-cal-action-label">Tambah</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Event List --}}
                <div class="mt-4">
                    <div class="db-panel-header" style="border-bottom: none; padding: 0;">
                        <div class="db-panel-left">
                            <span class="db-panel-ico" style="background: var(--ink)"><i
                                    class="fa-solid fa-list-check"></i></span>
                            <div>
                                <h3 class="db-panel-title">Kegiatan Mendatang</h3>
                                <p class="db-panel-sub">Jadwal pesantren dan catatan Anda.</p>
                            </div>
                        </div>
                    </div>
                    <div class="db-panel-body" style="padding-top: 0;">
                        <div class="db-event-list">
                            <template x-if="agendaMendatang.length === 0">
                                <div class="text-center py-6 text-slate-500 dark:text-slate-400 text-sm">
                                    <i class="fa-regular fa-calendar-check"></i>
                                    <p class="mt-2">Belum ada kegiatan mendatang.</p>
                                </div>
                            </template>
                            <template x-for="ev in agendaMendatang" :key="ev.id">
                                <button type="button" class="db-event-item" @click="bukaTanggal(ev.date)">
                                    <div class="db-event-ico"
                                        :style="'background:' + hexSoft(ev.color) + '; color:' + ev.color">
                                        <i class="fa-solid" :class="ev.editable ? 'fa-note-sticky' : 'fa-calendar-day'"></i>
                                    </div>
                                    <div class="db-event-content">
                                        <div class="db-event-title" x-text="ev.title"></div>
                                        <div class="db-event-meta">
                                            <span><i class="fa-regular fa-calendar"></i><span
                                                    x-text="formatTanggal(ev.date)"></span></span>
                                            <span><i class="fa-regular fa-clock"></i><span x-text="ev.time"></span></span>
                                            <span><i class="fa-solid fa-tag"></i><span x-text="ev.category"></span></span>
                                        </div>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Tombol cepat kalender (tampil di mobile) --}}
                <div class="db-cal-fab">
                    <button type="button" class="db-cal-fab-btn" @click="bukaTanggal(dipilih || todayKey)"
                        title="Lihat catatan" aria-label="Lihat catatan">
                        <i class="fa-solid fa-note-sticky"></i>
                        <span class="db-cal-fab-badge" x-show="catatanPada(dipilih || todayKey).length > 0" x-cloak
                            x-text="catatanPada(dipilih || todayKey).length"></span>
                    </button>
                    <button type="button" class="db-cal-fab-btn primary" @click="bukaTambah()" title="Tambah catatan"
                        aria-label="Tambah catatan">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>

                {{-- MODAL: Detail & Form Catatan --}}
                <div class="db-modal" x-show="modalOpen" x-cloak x-transition.opacity>
                    <div class="db-modal-backdrop" @click="tutupModal()"></div>
                    <div class="db-modal-card">
                        <div class="db-modal-head">
                            <div class="db-modal-head-row">
                                <span class="db-modal-ico"><i class="fa-solid fa-calendar-day"></i></span>
                                <div class="min-w-0">
                                    <p class="db-modal-date" x-text="judulTanggal"></p>
                                    <p class="db-modal-sub" x-text="subjudulTanggal"></p>
                                </div>
                                <button type="button" class="db-modal-close" @click="tutupModal()"
                                    aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                        </div>

                        <div class="db-modal-body">
                            {{-- Daftar catatan --}}
                            <div x-show="!formMode">
                                <div class="db-note-list" x-show="catatanTanggal.length">
                                    <template x-for="note in catatanTanggal" :key="note.id">
                                        <div class="db-note-card">
                                            <span class="db-note-bar" :style="'background:' + note.color"></span>
                                            <div class="db-note-main">
                                                <p class="db-note-title" x-text="note.title"></p>
                                                <p class="db-note-desc" x-show="note.desc" x-text="note.desc"></p>
                                                <div class="db-note-meta">
                                                    <span class="tag"
                                                        :style="'background:' + hexSoft(note.color) + '; color:' + note.color">
                                                        <i class="fa-solid fa-tag"></i><span x-text="note.category"></span>
                                                    </span>
                                                    <span><i class="fa-regular fa-clock"></i> <span
                                                            x-text="note.time"></span></span>
                                                    <span x-show="!note.editable"><i class="fa-solid fa-lock"></i> Info
                                                        Harian</span>
                                                </div>
                                            </div>
                                            <div class="db-note-actions" x-show="note.editable">
                                                <button type="button" class="db-note-icon-btn" @click="mulaiEdit(note)"
                                                    title="Ubah catatan"><i class="fa-solid fa-pen"></i></button>
                                                <button type="button" class="db-note-icon-btn danger"
                                                    @click="hapusCatatan(note)" title="Hapus catatan"><i
                                                        class="fa-solid fa-trash-can"></i></button>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <div class="db-note-empty" x-show="!catatanTanggal.length">
                                    <i class="fa-regular fa-note-sticky"></i>
                                    <p>Belum ada catatan pada tanggal ini.</p>
                                </div>

                                <button type="button" class="db-note-add" @click="mulaiTambah()">
                                    <i class="fa-solid fa-plus"></i> Tambah catatan di tanggal ini
                                </button>

                                <p class="mt-3 text-center text-[9px] text-slate-400">
                                    <i class="fa-solid fa-lock"></i> Catatan tersimpan di perangkat ini.
                                </p>
                            </div>

                            {{-- Form tambah / ubah --}}
                            <div x-show="formMode" x-cloak>
                                <div class="db-note-form">
                                    <div class="db-field">
                                        <label>Judul Catatan</label>
                                        <input type="text" x-model="form.judul" maxlength="120"
                                            placeholder="Contoh: Rapat dewan asatidz">
                                    </div>
                                    <div class="db-field-row">
                                        <div class="db-field">
                                            <label>Tanggal</label>
                                            <input type="date" x-model="form.tanggal">
                                        </div>
                                        <div class="db-field">
                                            <label>Waktu</label>
                                            <input type="time" x-model="form.waktu">
                                        </div>
                                    </div>
                                    <div class="db-field">
                                        <label>Kategori</label>
                                        <select x-model="form.kategori">
                                            <option value="Jadwal">Jadwal</option>
                                            <option value="Agenda">Agenda</option>
                                            <option value="Pengumuman">Pengumuman</option>
                                            <option value="Libur">Libur</option>
                                        </select>
                                    </div>
                                    <div class="db-field">
                                        <label>Catatan (opsional)</label>
                                        <textarea x-model="form.catatan" rows="3"
                                            placeholder="Detail kegiatan, tempat, atau hal penting lainnya..."></textarea>
                                    </div>
                                    <div class="db-form-actions">
                                        <button type="button" class="db-btn-primary" @click="simpanCatatan()">
                                            <i class="fa-solid fa-floppy-disk"></i>
                                            <span x-text="formMode === 'edit' ? 'Simpan Perubahan' : 'Simpan Catatan'"></span>
                                        </button>
                                        <button type="button" class="db-btn-ghost" @click="batalForm()">Batal</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ===================== TOAST ===================== --}}
        <div id="db-toast">
            <i class="fa-solid fa-check-circle"></i>
            <span id="db-toast-msg">Berhasil disimpan.</span>
            <span id="db-toast-close" onclick="hideDbToast()">&times;</span>
        </div>

        <script>
            /* ========== KALENDER ALPINE ========== */
            function kalenderApp() {
                const STORAGE_KEY = 'babulHasanahKalenderNotesV1';
                const serverEvents = @json($kalenderEvents);

                const pad = n => String(n).padStart(2, '0');
                const ymd = d => d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
                const parseYmd = s => {
                    const p = String(s || '').split('-').map(Number);
                    return (p.length === 3 && !isNaN(p[0]) && !isNaN(p[1]) && !isNaN(p[2])) ?
                        new Date(p[0], p[1] - 1, p[2]) : null;
                };

                return {
                    tanggal: new Date(),
                    dipilih: null,
                    modalOpen: false,
                    formMode: null,
                    events: [],
                    userNotes: [],
                    form: {
                        id: null,
                        judul: '',
                        tanggal: '',
                        waktu: '09:00',
                        kategori: 'Jadwal',
                        catatan: '',
                    },

                    init() {
                        this.events = (serverEvents || []).map((e, i) => ({
                            id: 'info-' + i,
                            title: e.title || 'Kegiatan',
                            date: e.date || '',
                            time: e.time || '-',
                            category: e.category || 'Jadwal',
                            desc: e.desc || '',
                            color: e.color || this.warnaKategori(e.category || 'Jadwal'),
                            editable: false,
                        }));

                        this.muatCatatan();

                        if (this.catatanHariIni.length) {
                            const jumlah = this.catatanHariIni.length;
                            setTimeout(() => this.showDbToast('Ada ' + jumlah + ' catatan untuk hari ini.'), 900);
                        }
                    },

                    /* ---------- Data turunan ---------- */
                    get todayKey() {
                        return ymd(new Date());
                    },

                    get namaBulan() {
                        return this.tanggal.toLocaleDateString('id-ID', {
                            month: 'long'
                        });
                    },

                    get hariSebelum() {
                        return new Date(this.tanggal.getFullYear(), this.tanggal.getMonth(), 1).getDay();
                    },

                    get jumlahHari() {
                        return new Date(this.tanggal.getFullYear(), this.tanggal.getMonth() + 1, 0).getDate();
                    },

                    get semuaCatatan() {
                        return [...this.events, ...this.userNotes];
                    },

                    get catatanTanggal() {
                        if (!this.dipilih) return [];

                        return this.semuaCatatan
                            .filter(n => n.date === this.dipilih)
                            .sort((a, b) => (a.time || '').localeCompare(b.time || ''));
                    },

                    get catatanHariIni() {
                        return this.semuaCatatan.filter(n => n.date === this.todayKey);
                    },

                    get agendaMendatang() {
                        return this.semuaCatatan
                            .filter(n => n.date && n.date >= this.todayKey)
                            .sort((a, b) => (a.date + (a.time || '')).localeCompare(b.date + (b.time || '')))
                            .slice(0, 5);
                    },

                    get judulTanggal() {
                        const d = parseYmd(this.formMode ? this.form.tanggal : this.dipilih);
                        if (!d) return 'Pilih tanggal';

                        return d.toLocaleDateString('id-ID', {
                            weekday: 'long',
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        });
                    },

                    get subjudulTanggal() {
                        if (this.formMode === 'edit') return 'Ubah catatan';
                        if (this.formMode === 'add') return 'Catatan baru';

                        const n = this.catatanTanggal.length;
                        return n ? n + ' catatan pada tanggal ini' : 'Belum ada catatan';
                    },

                    /* ---------- Bantuan tanggal ---------- */
                    tanggalKey(d) {
                        return ymd(new Date(this.tanggal.getFullYear(), this.tanggal.getMonth(), d));
                    },

                    catatanPada(key) {
                        return this.semuaCatatan.filter(n => n.date === key);
                    },

                    jumlahCatatan(d) {
                        return this.catatanPada(this.tanggalKey(d)).length;
                    },

                    warnaHari(d) {
                        const unik = [];
                        this.catatanPada(this.tanggalKey(d)).forEach(n => {
                            if (n.color && !unik.includes(n.color)) unik.push(n.color);
                        });
                        return unik.slice(0, 4);
                    },

                    isToday(d) {
                        return this.tanggalKey(d) === this.todayKey;
                    },

                    isSelected(d) {
                        return this.tanggalKey(d) === this.dipilih;
                    },

                    labelHari(d) {
                        const tgl = new Date(this.tanggal.getFullYear(), this.tanggal.getMonth(), d);
                        const teks = tgl.toLocaleDateString('id-ID', {
                            weekday: 'long',
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        });
                        const n = this.jumlahCatatan(d);

                        return teks + (n ? ' — ' + n + ' catatan' : '');
                    },

                    /* ---------- Navigasi ---------- */
                    prevMonth() {
                        this.tanggal = new Date(this.tanggal.getFullYear(), this.tanggal.getMonth() - 1, 1);
                        this.dipilih = null;
                    },

                    nextMonth() {
                        this.tanggal = new Date(this.tanggal.getFullYear(), this.tanggal.getMonth() + 1, 1);
                        this.dipilih = null;
                    },

                    keHariIni() {
                        const now = new Date();
                        this.tanggal = new Date(now.getFullYear(), now.getMonth(), 1);
                        this.dipilih = this.todayKey;
                    },

                    pilihHari(d) {
                        this.dipilih = this.tanggalKey(d);
                        this.formMode = null;
                        this.modalOpen = true;
                    },

                    bukaTanggal(key) {
                        const d = parseYmd(key);
                        if (!d) return;

                        this.tanggal = new Date(d.getFullYear(), d.getMonth(), 1);
                        this.dipilih = key;
                        this.formMode = null;
                        this.modalOpen = true;
                    },

                    tutupModal() {
                        this.modalOpen = false;
                        this.formMode = null;
                    },

                    /* ---------- Tambah / ubah / hapus ---------- */
                    bukaTambah() {
                        if (!this.dipilih) {
                            const now = new Date();
                            const bulanIni = this.tanggal.getFullYear() === now.getFullYear() &&
                                this.tanggal.getMonth() === now.getMonth();

                            this.dipilih = bulanIni ? this.todayKey :
                                ymd(new Date(this.tanggal.getFullYear(), this.tanggal.getMonth(), 1));
                        }

                        const d = parseYmd(this.dipilih);
                        if (d) this.tanggal = new Date(d.getFullYear(), d.getMonth(), 1);

                        this.modalOpen = true;
                        this.mulaiTambah();
                    },

                    mulaiTambah() {
                        this.formMode = 'add';
                        this.form = {
                            id: null,
                            judul: '',
                            tanggal: this.dipilih || this.todayKey,
                            waktu: '09:00',
                            kategori: 'Jadwal',
                            catatan: '',
                        };
                    },

                    mulaiEdit(note) {
                        if (!note.editable) return;

                        this.formMode = 'edit';
                        this.form = {
                            id: note.id,
                            judul: note.title,
                            tanggal: note.date,
                            waktu: note.time && note.time !== '-' ? note.time : '09:00',
                            kategori: note.category || 'Jadwal',
                            catatan: note.desc || '',
                        };
                    },

                    batalForm() {
                        this.formMode = null;
                    },

                    simpanCatatan() {
                        const judul = (this.form.judul || '').trim();

                        if (!judul) {
                            this.showDbToast('Judul catatan wajib diisi.', 'bad');
                            return;
                        }

                        if (!this.form.tanggal) {
                            this.showDbToast('Tanggal catatan wajib dipilih.', 'bad');
                            return;
                        }

                        const wasEdit = this.formMode === 'edit';
                        const data = {
                            id: wasEdit && this.form.id ? this.form.id : 'note-' + Date.now(),
                            title: judul,
                            date: this.form.tanggal,
                            time: this.form.waktu || '00:00',
                            category: this.form.kategori || 'Jadwal',
                            desc: (this.form.catatan || '').trim(),
                            color: this.warnaKategori(this.form.kategori),
                            editable: true,
                            updatedAt: new Date().toISOString(),
                        };

                        if (wasEdit) {
                            const i = this.userNotes.findIndex(n => n.id === data.id);
                            if (i > -1) this.userNotes.splice(i, 1, data);
                            else this.userNotes.push(data);
                        } else {
                            this.userNotes.push(data);
                        }

                        this.simpanKePenyimpanan();

                        this.dipilih = data.date;
                        const d = parseYmd(data.date);
                        if (d) this.tanggal = new Date(d.getFullYear(), d.getMonth(), 1);

                        this.formMode = null;
                        this.showDbToast(wasEdit ? 'Perubahan catatan disimpan.' : 'Catatan berhasil disimpan.');
                    },

                    hapusCatatan(note) {
                        if (!note.editable) return;
                        if (!window.confirm('Hapus catatan "' + note.title + '"?')) return;

                        this.userNotes = this.userNotes.filter(n => n.id !== note.id);
                        this.simpanKePenyimpanan();
                        this.showDbToast('Catatan dihapus.', 'ok');
                    },

                    /* ---------- Penyimpanan lokal ---------- */
                    muatCatatan() {
                        try {
                            const raw = window.localStorage.getItem(STORAGE_KEY);
                            const data = raw ? JSON.parse(raw) : [];
                            this.userNotes = Array.isArray(data) ?
                                data.filter(n => n && n.id && n.date && n.title) : [];
                        } catch (e) {
                            this.userNotes = [];
                        }
                    },

                    simpanKePenyimpanan() {
                        try {
                            window.localStorage.setItem(STORAGE_KEY, JSON.stringify(this.userNotes));
                        } catch (e) {
                            this.showDbToast('Catatan tampil sementara: penyimpanan browser penuh.', 'bad');
                        }
                    },

                    /* ---------- Utilitas tampilan ---------- */
                    formatTanggal(dateStr) {
                        const d = parseYmd(dateStr);
                        return d ? d.toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'short'
                        }) : (dateStr || '-');
                    },

                    hexSoft(color) {
                        return /^#[0-9a-f]{6}$/i.test(String(color || '')) ? color + '20' :
                            'rgba(100,116,139,.15)';
                    },

                    warnaKategori(k) {
                        if (k === 'Pengumuman') return '#2563eb';
                        if (k === 'Jadwal') return '#12a67a';
                        if (k === 'Agenda') return '#b45309';
                        if (k === 'Libur') return '#e11d48';
                        return '#8b5cc6';
                    },

                    showDbToast(msg, type = 'ok') {
                        const t = document.getElementById('db-toast');
                        const m = document.getElementById('db-toast-msg');
                        if (!t || !m) return;

                        m.textContent = msg;
                        t.className = 'db-toast ' + (type === 'bad' ? 'bad' : 'ok') + ' show';
                        clearTimeout(this._toastTimer);
                        this._toastTimer = setTimeout(() => t.classList.remove('show'), 3200);
                    },
                };
            }

            /* ========== HELPER CHART ========== */
            const chartMobile = window.matchMedia('(max-width: 640px)').matches;

            const singkatJenjang = (nama) => {
                const n = String(nama || '').toLowerCase().replace(/[’‘`]/g, "'").replace(/[^a-z0-9]+/g, ' ')
                    .trim();

                if (n.includes('mdta') || n.includes('madrasah diniyah') || n === 'diniyah') return 'MDTA';
                if (n.includes('pps') || n.includes('salafiyah wustha')) return 'PPS Wustha';
                if (n.includes('mts') || n.includes('tsanawiyah')) return 'MTs';
                if (n === 'ma' || n === 'mas' || n.includes('aliyah')) return 'MA';
                if ((n.includes('pdf') && n.includes('wustha')) || n.includes('diniyah formal wustha'))
                    return 'PDF Wustha';
                if ((n.includes('pdf') && n.includes('ulya')) || n.includes('diniyah formal ulya'))
                    return 'PDF Ulya';
                if (n.includes('ma had') || n.includes('mahad')) return "Ma'had 'Aly";

                return nama || '-';
            };

            const legendChart = () => ({
                position: 'bottom',
                labels: {
                    boxWidth: 8,
                    padding: chartMobile ? 6 : 12,
                    usePointStyle: true,
                    pointStyle: 'circle',
                    font: {
                        size: chartMobile ? 8.5 : 10
                    }
                }
            });

            /* ========== CHART: PENDAFTAR PER JENJANG ========== */
            const ctxJenjang = document.getElementById('chartJenjang');
            if (ctxJenjang) {
                const dataJenjang = @json($jenjangStats);
                new Chart(ctxJenjang, {
                    type: 'bar',
                    data: {
                        labels: dataJenjang.map(d => d.kode),
                        datasets: [{
                            label: 'Pendaftar',
                            data: dataJenjang.map(d => d.pendaftar),
                            backgroundColor: dataJenjang.map(d => d.warna),
                            borderRadius: 5,
                            maxBarThickness: chartMobile ? 14 : 18,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                padding: 10,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    title: items => {
                                        const d = dataJenjang[items[0].dataIndex];
                                        return d.kode + ' — ' + d.nama;
                                    },
                                    label: ctx => ctx.parsed.x + ' pendaftar'
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    font: {
                                        size: 9
                                    },
                                    color: '#64748b'
                                },
                                grid: {
                                    color: '#f1f5f9'
                                }
                            },
                            y: {
                                ticks: {
                                    font: {
                                        size: chartMobile ? 8.5 : 9.5
                                    },
                                    color: '#64748b',
                                    autoSkip: false
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            /* ========== CHART: SANTRI (Donut) ========== */
            const ctxSantri = document.getElementById('chartSantri');
            if (ctxSantri) {
                const dataSantri = @json($santriPerJenjang);
                const warnaSantri = dataSantri.map((d, i) => ['#17402f', '#b08d3e', '#2563eb', '#12a67a', '#e11d48', '#0e9f8e',
                    '#9cb49d'
                ][i % 7]);
                new Chart(ctxSantri, {
                    type: 'doughnut',
                    data: {
                        labels: dataSantri.map(d => singkatJenjang(d.jenjang)),
                        datasets: [{
                            data: dataSantri.map(d => d.jml),
                            backgroundColor: warnaSantri,
                            borderWidth: 0,
                            hoverOffset: chartMobile ? 10 : 20,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        plugins: {
                            legend: legendChart(),
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                padding: 10,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    label: ctx => ctx.label + ': ' + ctx.parsed + ' santri'
                                }
                            }
                        }
                    }
                });
            }

            /* ========== CHART: ALUMNI (Donut) ========== */
            const ctxAlumni = document.getElementById('chartAlumni');
            if (ctxAlumni) {
                const dataAlumni = @json($alumniPerJenjang);
                const warnaAlumni = dataAlumni.map((d, i) => ['#b45309', '#e11d48', '#12a67a', '#2563eb', '#8b5cc6', '#0e9f8e',
                    '#9cb49d'
                ][i % 7]);
                new Chart(ctxAlumni, {
                    type: 'doughnut',
                    data: {
                        labels: dataAlumni.map(d => singkatJenjang(d.jenjang)),
                        datasets: [{
                            data: dataAlumni.map(d => d.jml),
                            backgroundColor: warnaAlumni,
                            borderWidth: 0,
                            hoverOffset: chartMobile ? 10 : 20,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        plugins: {
                            legend: legendChart(),
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                padding: 10,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    label: ctx => ctx.label + ': ' + ctx.parsed + ' alumni'
                                }
                            }
                        }
                    }
                });
            }

            /* ========== CHART: DATA PER JENJANG (Batang Kelompok) ========== */
            const ctxDataJenjang = document.getElementById('chartDataJenjang');
            if (ctxDataJenjang) {
                const dataJenjangAll = @json($jenjangStats);
                new Chart(ctxDataJenjang, {
                    type: 'bar',
                    data: {
                        labels: dataJenjangAll.map(d => d.kode),
                        datasets: [{
                                label: 'Santri',
                                data: dataJenjangAll.map(d => d.santri),
                                backgroundColor: '#12a67a',
                                borderRadius: 5,
                                maxBarThickness: chartMobile ? 8 : 14,
                            },
                            {
                                label: 'Pendaftar',
                                data: dataJenjangAll.map(d => d.pendaftar),
                                backgroundColor: '#2563eb',
                                borderRadius: 5,
                                maxBarThickness: chartMobile ? 8 : 14,
                            },
                            {
                                label: 'Alumni',
                                data: dataJenjangAll.map(d => d.alumni),
                                backgroundColor: '#b45309',
                                borderRadius: 5,
                                maxBarThickness: chartMobile ? 8 : 14,
                            },
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: legendChart(),
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    title: items => {
                                        const d = dataJenjangAll[items[0].dataIndex];
                                        return d.kode + ' — ' + d.nama;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    font: {
                                        size: 9
                                    },
                                    color: '#64748b'
                                },
                                grid: {
                                    color: '#f1f5f9'
                                }
                            },
                            y: {
                                ticks: {
                                    font: {
                                        size: chartMobile ? 8.5 : 9.5
                                    },
                                    color: '#64748b',
                                    autoSkip: false
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            function hideDbToast() {
                document.getElementById('db-toast').classList.remove('show');
            }
        </script>

    </div>

@endsection
