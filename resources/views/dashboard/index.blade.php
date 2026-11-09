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

        /* ===================== RESPONSIVE MOBILE ===================== */
        @media (max-width: 1100px) {
            .db-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .db-main {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .db-right {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .db-stats {
                grid-template-columns: 1fr;
            }

            .db-charts-grid {
                grid-template-columns: 1fr;
            }

            .db-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .db-meta {
                width: 100%;
                justify-content: space-between;
            }

            .db-greeting {
                min-width: 100%;
            }

            .db-panel-body {
                padding: 16px 18px;
            }

            .db-panel-header {
                padding: 14px 18px;
            }

            .db-stat {
                padding: 16px 18px;
            }
        }

        @media (max-width: 560px) {
            .db-page {
                padding: 0 8px;
            }

            .db-stat {
                padding: 14px 16px;
            }

            .db-stat-num {
                font-size: 24px;
            }

            .db-panel-title {
                font-size: 14px;
            }

            .db-panel-sub {
                font-size: 11px;
            }

            .db-chart-box {
                height: 220px;
            }

            .db-chart-bar {
                height: 140px;
            }

            .db-cal-btn {
                width: 26px;
                height: 26px;
                font-size: 11px;
            }

            .db-cal-day {
                font-size: 10px;
            }

            .db-info-pill {
                font-size: 9px;
                padding: 2px 8px;
            }
        }

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
        }

        .db-right {
            display: flex;
            flex-direction: column;
            gap: 20px;
            position: sticky;
            top: 20px;
            align-self: start;
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
                        <div style="max-height: 170px; overflow: visible;">
                            <canvas id="chartJenjang" class="db-chart-box db-chart-bar"></canvas>
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
                            <canvas id="chartSantri" style="max-height: 220px"></canvas>
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
                            <canvas id="chartAlumni" style="max-height: 220px"></canvas>
                        </div>
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
            <div class="db-right">
                <div class="db-cal-panel" x-data="kalenderApp()">
                    <div class="db-cal-header">
                        <div class="db-cal-title"><i class="fa-solid fa-calendar-days"></i> Kalender Kegiatan</div>
                        <div class="db-cal-nav">
                            <button class="db-cal-btn" @click="prevMonth()">&lt;</button>
                            <span class="db-cal-month" x-text="bulanTahun"></span>
                            <button class="db-cal-btn" @click="nextMonth()">&gt;</button>
                        </div>
                    </div>
                    <div class="db-cal-body">
                        <div class="db-cal-week">
                            <div class="db-cal-day-name">Min</div>
                            <div class="db-cal-day-name">Sen</div>
                            <div class="db-cal-day-name">Sel</div>
                            <div class="db-cal-day-name">Rab</div>
                            <div class="db-cal-day-name">Kam</div>
                            <div class="db-cal-day-name">Jum</div>
                            <div class="db-cal-day-name">Sab</div>
                        </div>
                        <div class="db-cal-days">
                            <template x-for="i in hariSebelum" :key="i">
                                <div class="db-cal-day other"></div>
                            </template>
                            <template x-for="i in jumlahHari" :key="i">
                                <div class="db-cal-day"
                                    :class="{ 'today': isToday(i), 'has-event': eventsForDay(i).length > 0 }"
                                    @click="pilihHari(i)">
                                    <span class="day-num" x-text="i"></span>
                                    <template x-for="ev in eventsForDay(i)" :key="ev.id">
                                        <span class="day-events" :class="ev.colorClass" x-text="ev.short"></span>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="db-cal-add">
                        <button class="db-cal-add-btn" @click="tambahNote = !tambahNote">
                            <i class="fa-solid fa-plus"></i> Tambah Note
                        </button>
                    </div>

                    <!-- Form Tambah Note -->
                    <div x-show="tambahNote" x-cloak
                        class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
                        <div class="space-y-3">
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Judul
                                    Kegiatan</label>
                                <input type="text" x-model="noteJudul"
                                    class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:border-emerald-500"
                                    placeholder="Contoh: Kegiatan Tahfidz">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Kategori</label>
                                <select x-model="noteKategori"
                                    class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:border-emerald-500">
                                    <option value="Jadwal">Jadwal</option>
                                    <option value="Agenda">Agenda</option>
                                    <option value="Pengumuman">Pengumuman</option>
                                    <option value="Libur">Libur</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Waktu</label>
                                <input type="time" x-model="noteWaktu"
                                    class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:border-emerald-500">
                            </div>
                            <div class="flex gap-2 pt-2">
                                <button @click="simpanNote()"
                                    class="flex-1 px-3 py-2 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition">Simpan</button>
                                <button @click="tambahNote = false"
                                    class="px-3 py-2 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 rounded-lg transition">Batal</button>
                            </div>
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
                                <p class="db-panel-sub">Daftar kegiatan yang akan datang.</p>
                            </div>
                        </div>
                    </div>
                    <div class="db-panel-body" style="padding-top: 0;">
                        <div class="db-event-list">
                            @if (count($kalenderEvents))
                                @foreach ($kalenderEvents as $ev)
                                    @php
                                        if ($ev['category'] === 'Pengumuman') {
                                            $evColor = '#2563eb';
                                        } elseif ($ev['category'] === 'Jadwal') {
                                            $evColor = '#12a67a';
                                        } elseif ($ev['category'] === 'Agenda') {
                                            $evColor = '#b45309';
                                        } elseif ($ev['category'] === 'Libur') {
                                            $evColor = '#e11d48';
                                        } else {
                                            $evColor = '#8b5cc6';
                                        }
                                    @endphp
                                    <div class="db-event-item">
                                        <div class="db-event-ico"
                                            style="background: {{ $evColor }}20; color: {{ $evColor }}">
                                            <i class="fa-solid fa-calendar-day"></i>
                                        </div>
                                        <div class="db-event-content">
                                            <div class="db-event-title">{{ $ev['title'] }}</div>
                                            <div class="db-event-meta">
                                                <span><i class="fa-regular fa-clock"></i>{{ $ev['time'] }}</span>
                                                <span><i class="fa-solid fa-tag"></i>{{ $ev['category'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-6 text-slate-500 dark:text-slate-400 text-sm">
                                    <i class="fa-regular fa-calendar-check"></i>
                                    <p class="mt-2">Belum ada kegiatan mendatang.</p>
                                </div>
                            @endif
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
                return {
                    tanggal: new Date(),
                    tambahNote: false,
                    noteJudul: '',
                    noteKategori: 'Jadwal',
                    noteWaktu: '',
                    events: @json($kalenderEvents),
                    userNotes: [],

                    get bulanTahun() {
                        const opts = {
                            month: 'long',
                            year: 'numeric'
                        };
                        return this.tanggal.toLocaleDateString('id-ID', opts);
                    },

                    get hariSebelum() {
                        const awal = new Date(this.tanggal.getFullYear(), this.tanggal.getMonth(), 1);
                        return awal.getDay();
                    },

                    get jumlahHari() {
                        return new Date(this.tanggal.getFullYear(), this.tanggal.getMonth() + 1, 0).getDate();
                    },

                    isToday(d) {
                        const today = new Date();
                        return d === today.getDate() &&
                            this.tanggal.getMonth() === today.getMonth() &&
                            this.tanggal.getFullYear() === today.getFullYear();
                    },

                    eventsForDay(d) {
                        const tgl = new Date(this.tanggal.getFullYear(), this.tanggal.getMonth(), d).toISOString().split('T')[
                            0];
                        return [...this.events, ...this.userNotes].filter(ev => ev.date === tgl);
                    },

                    prevMonth() {
                        this.tanggal = new Date(this.tanggal.getFullYear(), this.tanggal.getMonth() - 1, 1);
                    },
                    nextMonth() {
                        this.tanggal = new Date(this.tanggal.getFullYear(), this.tanggal.getMonth() + 1, 1);
                    },

                    pilihHari(d) {
                        // placeholder untuk interaksi lebih lanjut
                    },

                    warnaKategori(k) {
                        if (k === 'Pengumuman') return '#2563eb';
                        if (k === 'Jadwal') return '#12a67a';
                        if (k === 'Agenda') return '#b45309';
                        if (k === 'Libur') return '#e11d48';
                        return '#8b5cc6';
                    },

                    warnaClass(k) {
                        if (k === 'Pengumuman') return 'day-event-1';
                        if (k === 'Jadwal') return 'day-event-2';
                        if (k === 'Agenda') return 'day-event-1';
                        if (k === 'Libur') return 'day-event-2';
                        return 'day-event-1';
                    },

                    simpanNote() {
                        if (!this.noteJudul || !this.noteWaktu) {
                            this.showDbToast('Judul dan waktu wajib diisi.', 'bad');
                            return;
                        }
                        const tgl = new Date(this.tanggal.getFullYear(), this.tanggal.getMonth(), 1).toISOString().split('T')[
                            0];
                        const note = {
                            id: Date.now(),
                            title: this.noteJudul,
                            date: tgl,
                            time: this.noteWaktu,
                            category: this.noteKategori,
                            color: this.warnaKategori(this.noteKategori),
                            colorClass: this.warnaClass(this.noteKategori),
                            short: this.noteJudul.length > 12 ? this.noteJudul.slice(0, 12) + '…' : this.noteJudul,
                        };
                        this.userNotes.push(note);
                        this.tambahNote = false;
                        this.noteJudul = '';
                        this.noteWaktu = '';
                        this.showDbToast('Note kegiatan ditambahkan ke kalender.');
                    },

                    showDbToast(msg, type = 'ok') {
                        const t = document.getElementById('db-toast');
                        document.getElementById('db-toast-msg').textContent = msg;
                        t.className = 'db-toast ' + (type === 'bad' ? 'bad' : 'ok') + ' show';
                        setTimeout(() => t.classList.remove('show'), 3000);
                    },
                };
            }

            /* ========== CHART: PENDAFTAR PER JENJANG ========== */
            const ctxJenjang = document.getElementById('chartJenjang');
            if (ctxJenjang) {
                const dataJenjang = @json($pendaftarPerJenjang);
                const warnaJenjang = ['#17402f', '#b08d3e', '#2563eb', '#12a67a', '#e11d48', '#0e9f8e', '#9cb49d'];
                new Chart(ctxJenjang, {
                    type: 'bar',
                    data: {
                        labels: dataJenjang.map(d => d.jenjang),
                        datasets: [{
                            label: 'Jumlah Pendaftar',
                            data: dataJenjang.map(d => d.jml),
                            backgroundColor: warnaJenjang,
                            borderRadius: 6,
                            barThickness: 26,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                titleFont: {
                                    size: 12,
                                    weight: '600'
                                },
                                bodyFont: {
                                    size: 12
                                },
                                padding: 10,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    label: ctx => ctx.parsed + ' pendaftar'
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                suggestedMax: Math.max(...dataJenjang.map(d => d.jml)) + 2,
                                ticks: {
                                    stepSize: 1,
                                    font: {
                                        size: 11
                                    },
                                    color: '#64748b'
                                },
                                grid: {
                                    color: '#f1f5f9',
                                    drawBorder: false
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    color: '#64748b'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        },
                        layout: {
                            padding: {
                                top: 10,
                                bottom: 10,
                                left: 10,
                                right: 10
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
                        labels: dataSantri.map(d => d.jenjang),
                        datasets: [{
                            data: dataSantri.map(d => d.jml),
                            backgroundColor: warnaSantri,
                            borderWidth: 0,
                            hoverOffset: 24,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    font: {
                                        size: 10
                                    },
                                    padding: 16,
                                    boxWidth: 10
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                titleFont: {
                                    size: 12,
                                    weight: '600'
                                },
                                bodyFont: {
                                    size: 12
                                },
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
                        labels: dataAlumni.map(d => d.jenjang),
                        datasets: [{
                            data: dataAlumni.map(d => d.jml),
                            backgroundColor: warnaAlumni,
                            borderWidth: 0,
                            hoverOffset: 24,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    font: {
                                        size: 10
                                    },
                                    padding: 16,
                                    boxWidth: 10
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                titleFont: {
                                    size: 12,
                                    weight: '600'
                                },
                                bodyFont: {
                                    size: 12
                                },
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

            function hideDbToast() {
                document.getElementById('db-toast').classList.remove('show');
            }
        </script>

    </div>

@endsection
