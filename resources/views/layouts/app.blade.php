<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pondok Pesantren Babul Hasanah')</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo/logo.png') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
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
            --shadow-lg: 0 22px 50px rgba(23, 64, 47, 0.14);
        }

        body {
            font-family: 'Poppins', system-ui, -apple-system, sans-serif;
            background: var(--paper);
            color: var(--ink);
        }

        [x-cloak] { display: none !important; }

        /* =====================================================
           TOPBAR
           ===================================================== */
        .site-topbar { background: var(--green); color: #cfe0d6; font-size: 11.5px; padding: 8px 0; }
        .site-container { width: min(1180px, 92%); margin-inline: auto; }
        .site-topbar .site-container { display: flex; justify-content: space-between; align-items: center; gap: 16px; }
        .site-topbar a { white-space: nowrap; display: inline-flex; gap: 6px; align-items: center; color: #fff; font-weight: 500; transition: color .2s; }
        .site-topbar a:hover { color: var(--emerald-l); }
        .tb-left, .tb-right { display: flex; align-items: center; gap: 14px; }
        .tb-left i { color: var(--emerald-l); font-size: 10px; }
        .tb-sep { color: rgba(255, 255, 255, .25); }
        .tb-social { display: flex; gap: 6px; }
        .tb-social a {
            width: 24px; height: 24px; border-radius: 50%; justify-content: center;
            background: rgba(255, 255, 255, .1); font-size: 11px; transition: all .2s;
        }
        .tb-social a:hover { background: var(--emerald); color: #fff; }

        /* =====================================================
           HEADER & NAVBAR
           ===================================================== */
        .site-header {
            background: rgba(255, 255, 255, .98); position: sticky; top: 0;
            z-index: 1000; /* PERBAIKAN: dinaikkan agar tidak tertutup elemen halaman (hero/slider) */
            transition: box-shadow .3s ease;
        }
        .site-header.scrolled { box-shadow: 0 8px 28px rgba(15, 40, 30, .08); }
        .site-nav { display: flex; align-items: center; justify-content: space-between; padding: 14px 0; gap: 20px; }

        .site-brand { display: flex; align-items: center; gap: 12px; }
        .site-brand-logo {
            width: 44px; height: 44px; background: #e8f6ef; border-radius: 12px;
            display: grid; place-items: center; overflow: hidden; flex: none;
        }
        .site-brand-logo img { width: 100%; height: 100%; object-fit: contain; padding: 4px; }
        .site-brand small { display: block; font-size: 11px; color: var(--emerald-d); font-weight: 500; line-height: 1.1; }
        .site-brand strong { font-size: 18px; color: var(--green); font-weight: 600; display: block; line-height: 1.25; }

        .site-menu { display: flex; align-items: center; font-size: 14px; }
        .menu-links { display: flex; align-items: center; gap: 28px; }
        .menu-links > a, .dd-btn {
            color: #4b5563; padding: 6px 2px; position: relative; display: inline-flex; align-items: center; gap: 6px;
            background: none; border: 0; cursor: pointer; font-family: inherit; font-size: 14px; transition: color .2s ease;
        }
        .menu-links > a::after, .dd-btn::after {
            content: ""; position: absolute; left: 0; right: 0; bottom: 0; height: 2px; border-radius: 2px;
            background: var(--emerald); transform: scaleX(0); transition: transform .25s ease;
        }
        .menu-links > a.active, .dd-btn.active { color: var(--emerald-d); font-weight: 500; }
        .menu-links > a.active::after, .dd-btn.active::after { transform: scaleX(1); }
        .mi { display: none; }
        .dd-btn .chev { font-size: 10px; transition: transform .2s ease; }
        .dd-btn .chev.open { transform: rotate(180deg); }

        /* Hover hanya di desktop, supaya di layar sentuh tidak "menempel" */
        @media (min-width: 1025px) and (hover: hover) {
            .menu-links > a:hover, .dd-btn:hover { color: var(--emerald-d); font-weight: 500; }
            .menu-links > a:hover::after, .dd-btn:hover::after { transform: scaleX(1); }
        }

        /* Dropdown */
        .dd { position: relative; }
        .dd-panel {
            position: absolute; left: 0; top: 100%; margin-top: 10px; width: 260px; background: #fff;
            border: 1px solid var(--line); border-radius: 18px; box-shadow: var(--shadow-lg); padding: 8px; z-index: 60;
        }
        .dd-title {
            padding: 8px 12px; font-size: 10px; font-weight: 600; letter-spacing: .1em; text-transform: uppercase;
            color: var(--gold); border-bottom: 1px solid var(--line); margin-bottom: 6px;
        }
        .dd-item {
            display: flex; align-items: center; gap: 12px; padding: 9px 12px; border-radius: 12px;
            font-size: 13px; font-weight: 500; color: var(--ink); transition: all .2s ease;
        }
        .dd-item .ic {
            width: 32px; height: 32px; border-radius: 10px; background: var(--cream); color: var(--green);
            display: grid; place-items: center; font-size: 12px; flex: none; transition: all .2s ease;
        }
        .dd-item.active { background: #f1f8f4; color: var(--emerald-d); }
        .dd-item.active .ic { background: var(--emerald); color: #fff; }
        @media (hover: hover) {
            .dd-item:hover { background: #f1f8f4; color: var(--emerald-d); }
            .dd-item:hover .ic { background: var(--emerald); color: #fff; }
        }

        /* PERBAIKAN: area aksi selalu di atas dan bisa diklik */
        .site-actions {
            display: flex; align-items: center; gap: 12px; font-size: 14px;
            position: relative; z-index: 2; pointer-events: auto; flex: none;
        }
        .site-login {
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 500; color: var(--ink); padding: 10px 14px; border-radius: 10px;
            cursor: pointer; pointer-events: auto; position: relative; z-index: 2;
            transition: color .2s, background .2s;
        }
        .site-login:hover { color: var(--emerald-d); background: #f1f8f4; }
        .site-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 22px;
            border-radius: 12px; font-size: 13.5px; font-weight: 600; background: var(--emerald); color: #fff;
            box-shadow: 0 8px 20px rgba(18, 184, 134, .28); transition: all .25s ease; white-space: nowrap;
        }
        .site-btn:hover { background: var(--emerald-d); transform: translateY(-2px); }

        .site-burger {
            display: none; background: var(--cream); border: 1px solid var(--line); border-radius: 10px;
            padding: 10px 12px; cursor: pointer; color: var(--green); transition: all .2s ease; font-size: 16px; line-height: 1;
        }
        .site-burger:hover { background: #e2ddd0; }
        .menu-header, .site-backdrop, .menu-extra { display: none; }

        /* =====================================================
           FOOTER
           ===================================================== */
        .site-footer {
            background: var(--green); color: #cfe0d6; padding: 60px 0 20px; border-radius: 36px 36px 0 0;
        }
        .foot-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr 1fr; gap: 40px; margin-bottom: 40px; }
        .site-footer .site-brand-logo { background: rgba(255, 255, 255, .1); }
        .site-footer .site-brand strong { color: #fff; }
        .site-footer .site-brand small { color: #a9c4b6; }
        .foot-about { font-size: 12.5px; margin: 18px 0; max-width: 300px; line-height: 1.7; }
        .foot-social { display: flex; gap: 10px; }
        .foot-social a {
            width: 36px; height: 36px; border: 1px solid rgba(255, 255, 255, .2); border-radius: 50%;
            display: grid; place-items: center; transition: all .25s ease; color: #fff; font-size: 14px;
        }
        .foot-social a:hover { background: var(--emerald); border-color: var(--emerald); transform: translateY(-2px); }
        .site-footer h5 { color: #fff; font-size: 13px; font-weight: 600; margin-bottom: 16px; }
        .site-footer p.txt { font-size: 12px; line-height: 1.8; }
        .site-footer ul { list-style: none; margin: 0; padding: 0; }
        .site-footer li { font-size: 12px; margin-bottom: 10px; }
        .site-footer li a { transition: color .2s ease; }
        .site-footer li a:hover { color: #fff; }
        .foot-contact a { transition: color .2s ease; }
        .foot-contact a:hover { color: #fff; }
        .foot-bottom {
            border-top: 1px solid rgba(255, 255, 255, .12); padding-top: 20px; display: flex;
            justify-content: space-between; font-size: 11px; gap: 16px; flex-wrap: wrap;
        }

        /* =====================================================
           RESPONSIVE (drawer mobile)
           ===================================================== */
        @media (max-width: 1024px) {
            .site-burger { display: inline-flex; align-items: center; justify-content: center; }

            /* PERBAIKAN: Masuk TIDAK lagi disembunyikan di mobile, tampil sebagai tombol outline */
            .site-login {
                display: inline-flex; padding: 8px 14px; font-size: 13px; font-weight: 600;
                border: 1px solid var(--line); color: var(--green); background: #fff;
            }
            .site-actions { gap: 8px; }
            .site-actions .site-btn-desktop { display: none; }

            /* Panel menu layar penuh, turun dari atas dan menutupi halaman di belakangnya */
            .site-menu {
                position: fixed; top: 0; right: 0; bottom: 0; left: 0; width: 100%; background: #fff; z-index: 9999;
                flex-direction: column; align-items: stretch;
                transform: translateY(-100%); visibility: hidden; opacity: 0;
                pointer-events: none; /* PERBAIKAN: saat tertutup tidak menangkap klik */
                transition: transform .4s cubic-bezier(.16, 1, .3, 1), opacity .25s ease, visibility .4s;
            }
            .site-menu.open { transform: translateY(0); visibility: visible; opacity: 1; pointer-events: auto; }

            .menu-header {
                display: flex; align-items: center; justify-content: space-between; gap: 12px; flex: none;
                padding: 14px max(18px, calc((100% - 560px) / 2)); border-bottom: 1px solid var(--line);
            }
            .menu-header .site-brand-logo { width: 38px; height: 38px; }
            .menu-header .site-brand small { font-size: 10px; }
            .menu-header .site-brand strong { font-size: 16px; }
            .close-btn {
                background: var(--cream); border: 0; border-radius: 50%; width: 36px; height: 36px; flex: none;
                display: grid; place-items: center; cursor: pointer; color: var(--ink); font-size: 14px;
            }

            /* Daftar menu (bisa di-scroll) */
            .menu-links {
                flex: 1; min-height: 0; overflow-y: auto; -webkit-overflow-scrolling: touch;
                flex-direction: column; align-items: stretch; gap: 2px; padding: 12px max(14px, calc((100% - 560px) / 2));
            }
            .menu-links > a, .dd-btn {
                width: 100%; padding: 8px 12px; border-radius: 14px; font-size: 14.5px; font-weight: 500;
                color: var(--ink); gap: 12px; justify-content: flex-start; text-align: left;
            }
            .menu-links > a::after, .dd-btn::after { display: none; }
            .menu-links > a:active, .dd-btn:active { background: #f6f4ee; }
            .mi {
                display: grid; place-items: center; width: 34px; height: 34px; border-radius: 10px; flex: none;
                background: var(--cream); color: var(--green); font-size: 13px; transition: all .2s ease;
            }
            .dd-btn .label { flex: 1; }
            .dd-btn .chev { margin-left: auto; color: var(--muted); }
            .menu-links > a.active, .dd-btn.active { background: #eaf7f1; color: var(--emerald-d); font-weight: 600; }
            .menu-links > a.active .mi, .dd-btn.active .mi { background: var(--emerald); color: #fff; }

            /* Submenu Profil / Jenjang */
            .dd { width: 100%; }
            .dd-panel {
                position: static; width: auto; margin: 2px 0 6px 29px; padding: 2px 0 2px 12px;
                border: 0; border-left: 2px solid var(--line); border-radius: 0; box-shadow: none; background: transparent;
            }
            .dd-title { display: none; }
            .dd-item { padding: 9px 8px; font-size: 13.5px; border-radius: 10px; gap: 10px; }
            .dd-item .ic { width: 28px; height: 28px; font-size: 11px; }
            .dd-item:active { background: #f1f8f4; }

            /* Tombol Masuk & Daftar selalu terlihat di bawah */
            .menu-extra {
                display: flex; flex-direction: column; gap: 10px; flex: none; background: #fff;
                padding: 14px max(16px, calc((100% - 560px) / 2)) calc(14px + env(safe-area-inset-bottom, 0px)); border-top: 1px solid var(--line);
            }
            .menu-extra .login-m {
                text-align: center; border: 1px solid var(--line); color: var(--green); font-weight: 600;
                padding: 12px; border-radius: 12px; font-size: 14px;
            }
            .menu-extra .site-btn { width: 100%; padding: 13px; }

            .foot-grid { grid-template-columns: 1fr 1fr; }
        }
        /* Topbar di smartphone: semua ikon tetap tampil, ukuran disesuaikan */
        @media (max-width: 768px) {
            .site-topbar { padding: 6px 0; font-size: 11px; }
            .site-topbar .site-container { gap: 8px; }
            .tb-left { gap: 10px; }
            .tb-sep { display: none; }
            .tb-social { gap: 5px; }
        }
        @media (max-width: 480px) {
            /* Email jadi ikon bulat (seperti ikon sosmed) supaya muat satu baris */
            .tb-email-text { display: none; }
            .site-topbar a.tb-email {
                width: 24px; height: 24px; border-radius: 50%; justify-content: center;
                background: rgba(255, 255, 255, .1);
            }
            .tb-email i { color: #fff; font-size: 11px; }
        }
        @media (max-width: 640px) {
            .site-nav { padding: 10px 0; }
            .site-brand-logo { width: 38px; height: 38px; }
            .site-brand strong { font-size: 16px; }
            .site-login { padding: 7px 12px; font-size: 12px; border-radius: 8px; }
            .site-footer { border-radius: 28px 28px 0 0; }
            .foot-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body class="antialiased flex flex-col min-h-screen">

    {{-- ===================== TOPBAR ===================== --}}
    <div class="site-topbar">
        <div class="site-container">
            <div class="tb-left">
                <a href="tel:081234567890"><i class="fa-solid fa-phone"></i> 0812-3456-7890</a>
                <span class="tb-sep">|</span>
                <a href="mailto:info@babulhasanah.sch.id" class="tb-email" aria-label="Email"><i class="fa-solid fa-envelope"></i><span class="tb-email-text">info@babulhasanah.sch.id</span></a>
            </div>
            <div class="tb-right">
                <div class="tb-social">
                    <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    <a href="https://whatsapp.com" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== HEADER ===================== --}}
    <header class="site-header" id="siteHeader">

        <div class="site-container site-nav">

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="site-brand">
                <span class="site-brand-logo">
                    <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Logo Babul Hasanah">
                </span>
                <span><small>Pondok Pesantren</small><strong>Babul Hasanah</strong></span>
            </a>

            {{-- Menu (desktop) / Drawer (mobile) --}}
            <nav class="site-menu" id="siteMenu" aria-label="Menu utama">
                <div class="menu-header">
                    <a href="{{ url('/') }}" class="site-brand">
                        <span class="site-brand-logo"><img src="{{ asset('assets/img/logo/logo.png') }}" alt="Logo Babul Hasanah"></span>
                        <span><small>Pondok Pesantren</small><strong>Babul Hasanah</strong></span>
                    </a>
                    <button type="button" class="close-btn" id="closeMenu" aria-label="Tutup Menu"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <div class="menu-links">
                    <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">
                        <span class="mi"><i class="fa-solid fa-house"></i></span>Beranda
                    </a>

                    {{-- Dropdown Profil --}}
                    <div class="dd" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" class="dd-btn {{ request()->is('profil*') ? 'active' : '' }}" @click="open = !open">
                            <span class="mi"><i class="fa-solid fa-landmark"></i></span>
                            <span class="label">Profil</span>
                            <i class="fa-solid fa-chevron-down chev" :class="{ 'open': open }"></i>
                        </button>
                        <div class="dd-panel" x-show="open" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2"
                            style="display: none;">
                            <div class="dd-title">Tentang Pesantren</div>
                            <a href="{{ url('/profil/sejarah') }}" class="dd-item"><span class="ic"><i class="fa-solid fa-landmark"></i></span>Sejarah</a>
                            <a href="{{ url('/profil/visi-misi') }}" class="dd-item"><span class="ic"><i class="fa-solid fa-bullseye"></i></span>Visi &amp; Misi</a>
                            <a href="{{ url('/profil/struktur-organisasi') }}" class="dd-item"><span class="ic"><i class="fa-solid fa-sitemap"></i></span>Struktur Organisasi</a>
                            <a href="{{ url('/profil/pembelajaran') }}" class="dd-item"><span class="ic"><i class="fa-solid fa-graduation-cap"></i></span>Pembelajaran</a>
                        </div>
                    </div>

                    {{-- Dropdown Jenjang --}}
                    <div class="dd" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" class="dd-btn {{ request()->is('jenjang*') ? 'active' : '' }}" @click="open = !open">
                            <span class="mi"><i class="fa-solid fa-book-open-reader"></i></span>
                            <span class="label">Jenjang</span>
                            <i class="fa-solid fa-chevron-down chev" :class="{ 'open': open }"></i>
                        </button>
                        <div class="dd-panel" x-show="open" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2"
                            style="display: none;">
                            <div class="dd-title">Jenjang Pendidikan</div>
                            <a href="{{ route('jenjang.show', 'mdta') }}" class="dd-item {{ request()->is('jenjang/mdta') ? 'active' : '' }}"><span class="ic"><i class="fa-solid fa-book-quran"></i></span>Madrasah Diniyah Takmiliyah</a>
                            <a href="{{ route('jenjang.show', 'pps-wustha') }}" class="dd-item {{ request()->is('jenjang/pps-wustha') ? 'active' : '' }}"><span class="ic"><i class="fa-solid fa-award"></i></span>PPS Wustha</a>
                            <a href="{{ route('jenjang.show', 'mts') }}" class="dd-item {{ request()->is('jenjang/mts') ? 'active' : '' }}"><span class="ic"><i class="fa-solid fa-school"></i></span>MTs</a>
                            <a href="{{ route('jenjang.show', 'ma') }}" class="dd-item {{ request()->is('jenjang/ma') ? 'active' : '' }}"><span class="ic"><i class="fa-solid fa-school-flag"></i></span>MA</a>
                            <a href="{{ route('jenjang.show', 'pdf-wustha') }}" class="dd-item {{ request()->is('jenjang/pdf-wustha') ? 'active' : '' }}"><span class="ic"><i class="fa-solid fa-book"></i></span>Diniyah Formal Wustha</a>
                            <a href="{{ route('jenjang.show', 'pdf-ulya') }}" class="dd-item {{ request()->is('jenjang/pdf-ulya') ? 'active' : '' }}"><span class="ic"><i class="fa-solid fa-book-open"></i></span>Diniyah Formal Ulya</a>
                            <a href="{{ route('jenjang.show', 'mahad-aly') }}" class="dd-item {{ request()->is('jenjang/mahad-aly') ? 'active' : '' }}"><span class="ic"><i class="fa-solid fa-graduation-cap"></i></span>Ma'had 'Aly</a>
                        </div>
                    </div>

                    <a href="{{ url('/berita') }}" class="{{ request()->is('berita*') ? 'active' : '' }}">
                        <span class="mi"><i class="fa-solid fa-newspaper"></i></span>Berita
                    </a>
                    <a href="{{ url('/galeri') }}" class="{{ request()->is('galeri*') ? 'active' : '' }}">
                        <span class="mi"><i class="fa-solid fa-images"></i></span>Galeri
                    </a>
                    <a href="{{ url('/prestasi') }}" class="{{ request()->is('prestasi*') ? 'active' : '' }}">
                        <span class="mi"><i class="fa-solid fa-trophy"></i></span>Prestasi
                    </a>
                    <a href="{{ route('kontak') }}" class="{{ request()->is('kontak*') ? 'active' : '' }}">
                        <span class="mi"><i class="fa-solid fa-address-book"></i></span>Kontak
                    </a>
                </div>

                {{-- Tombol Masuk & Daftar (tampil di bagian bawah laci mobile) --}}
                <div class="menu-extra">
                    <a href="{{ route('login') }}" class="login-m">Masuk Sistem</a>
                    <a href="{{ route('pendaftaran.index') }}" class="site-btn">Daftar Online</a>
                </div>
            </nav>

            {{-- Aksi kanan --}}
            <div class="site-actions">
                <a href="{{ route('login') }}" class="site-login">Masuk</a>
                <a href="{{ route('pendaftaran.index') }}" class="site-btn site-btn-desktop">Daftar Online</a>
                <button type="button" class="site-burger" id="openMenu" aria-label="Menu Mobile">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>

        </div>
    </header>

    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- ===================== FOOTER ===================== --}}
    <footer class="site-footer">
        <div class="site-container">
            <div class="foot-grid">

                <div>
                    <a href="{{ url('/') }}" class="site-brand">
                        <span class="site-brand-logo">
                            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Logo Babul Hasanah">
                        </span>
                        <span><small>Pondok Pesantren</small><strong>Babul Hasanah</strong></span>
                    </a>
                    <p class="foot-about">
                        Pusat pendidikan islam terpadu yang berdedikasi membangun generasi santri unggul, cerdas,
                        berakhlak mulia, dan siap menghadapi tantangan zaman.
                    </p>
                    <div class="foot-social">
                        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    </div>
                </div>

                <div>
                    <h5>Lokasi Pesantren</h5>
                    <p class="txt">
                        Jl. Lintas Pinarik-Papaso Km. 14<br>
                        Desa Manggis, Kec. Batang Lubu Sutam<br>
                        Kabupaten Padang Lawas, Sumatera Utara, 22763
                    </p>
                </div>

                <div>
                    <h5>Tautan Cepat</h5>
                    <ul>
                        <li><a href="{{ url('/profil/sejarah') }}">Tentang Pesantren</a></li>
                        <li><a href="{{ route('jenjang.show', 'mdta') }}">Jenjang Pendidikan</a></li>
                        <li><a href="{{ route('pendaftaran.index') }}">Pendaftaran Santri Baru</a></li>
                        <li><a href="{{ route('login') }}">Portal Wali Santri</a></li>
                    </ul>
                </div>

                <div>
                    <h5>Layanan &amp; Informasi</h5>
                    <p class="txt foot-contact">
                        Email: <a href="mailto:info@babulhasanah.sch.id">info@babulhasanah.sch.id</a><br>
                        Telepon/WA: <a href="tel:081234567890">0812-3456-7890</a>
                    </p>
                </div>

            </div>

            <div class="foot-bottom">
                <span>&copy; {{ date('Y') }} Pondok Pesantren Babul Hasanah. All rights reserved.</span>
                <span>Developed with Laravel &amp; Tailwind CSS</span>
            </div>
        </div>
    </footer>

    <script>
        // Bayangan header saat scroll
        const siteHeader = document.getElementById('siteHeader');
        window.addEventListener('scroll', () => {
            siteHeader.classList.toggle('scrolled', window.scrollY > 20);
        });

        // Panel menu mobile (turun dari atas)
        const siteMenu = document.getElementById('siteMenu');

        function openMenu() {
            siteMenu.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeMenu() {
            siteMenu.classList.remove('open');
            document.body.style.overflow = '';
        }

        document.getElementById('openMenu').addEventListener('click', openMenu);
        document.getElementById('closeMenu').addEventListener('click', closeMenu);

        // Tutup drawer saat salah satu link diklik (tombol dropdown tidak menutup)
        document.querySelectorAll('#siteMenu a').forEach(link => link.addEventListener('click', closeMenu));

        // Tutup dengan tombol Esc, dan reset saat layar dilebarkan ke desktop
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMenu(); });
        window.addEventListener('resize', () => { if (window.innerWidth > 1024) closeMenu(); });
    </script>
</body>

</html>