<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'Portal Alumni') - Pondok Pesantren Babul Hasanah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* Token global yang sama dengan halaman publik, dipakai seluruh portal alumni. */
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
        }

        body {
            font-family: 'Poppins', system-ui, -apple-system, sans-serif;
            font-size: 15px;
            line-height: 1.6;
            background: var(--paper);
            color: var(--ink);
        }

        .alumni-wrap { width: min(1200px, 90%); margin-inline: auto; }
    </style>
</head>

<body class="antialiased">
    <div class="min-h-screen flex flex-col justify-between">
        <!-- Top Navbar -->
        <div class="sticky top-0 z-20">
        <header class="bg-white border-b border-[#e4e0d4] py-3 px-4 sm:py-4 sm:px-6 lg:px-10 flex items-center justify-between gap-3 shadow-sm">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 sm:w-10 sm:h-10 shrink-0 rounded-xl bg-[#17402f] text-[#f1eee6] flex items-center justify-center font-extrabold">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="font-extrabold text-[#17402f] text-sm sm:text-base truncate">Portal Alumni</h1>
                    <p class="text-[10px] text-[#b7791f] font-bold uppercase tracking-wider truncate">Pondok Pesantren Babul Hasanah</p>
                </div>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <span class="text-xs font-bold text-[#1f2a24] hidden sm:inline">Halo, {{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="bg-red-50 text-red-600 hover:bg-red-600 hover:text-white px-3 sm:px-4 py-2 rounded-full text-xs font-bold transition duration-300 whitespace-nowrap">
                        <i class="fa-solid fa-right-from-bracket sm:mr-1.5"></i><span class="hidden sm:inline">Keluar</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Menu -->
        <nav class="bg-white border-b border-[#e4e0d4]">
            <div class="alumni-wrap">
                <div class="nav-scroll flex items-center gap-1.5 overflow-x-auto py-2">
                @php
                    $navClass = fn (bool $aktif) => 'inline-flex shrink-0 items-center gap-2 rounded-xl px-3.5 py-2 text-[11px] font-bold transition '
                        .($aktif
                            ? 'bg-[#17402f] text-white shadow-sm'
                            : 'text-[#6b756f] hover:bg-[#f1eee6] hover:text-[#17402f]');
                @endphp

                <a href="{{ route('alumni.dashboard') }}" class="{{ $navClass(request()->routeIs('alumni.dashboard')) }}">
                    <i class="fa-solid fa-house"></i> Beranda
                </a>
                <a href="{{ route('alumni.profil') }}" class="{{ $navClass(request()->routeIs('alumni.profil*')) }}">
                    <i class="fa-solid fa-id-card"></i> Profil &amp; Tracer
                </a>
                <a href="{{ route('alumni.nilai') }}" class="{{ $navClass(request()->routeIs('alumni.nilai*')) }}">
                    <i class="fa-solid fa-book-open"></i> Nilai &amp; Riwayat
                </a>
                <a href="{{ route('alumni.dokumen') }}" class="{{ $navClass(request()->routeIs('alumni.dokumen*')) }}">
                    <i class="fa-solid fa-folder-open"></i> Dokumen
                </a>
                <a href="{{ route('alumni.legalisir') }}" class="{{ $navClass(request()->routeIs('alumni.legalisir*')) }}">
                    <i class="fa-solid fa-stamp"></i> Legalisir
                </a>
                <a href="{{ route('alumni.pengumuman') }}" class="{{ $navClass(request()->routeIs('alumni.pengumuman*')) }}">
                    <i class="fa-solid fa-bullhorn"></i> Pengumuman
                </a>
            </div>
        </nav>
        </div>

        <!-- Content -->
        <main class="alumni-wrap w-full flex-1 py-6 sm:py-8">
            @if (session('success'))
                <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-bold text-emerald-700">
                    <i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-bold text-rose-700">
                    <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-bold text-rose-700">
                    <i class="fa-solid fa-circle-exclamation mr-1"></i> Periksa kembali isian yang ditandai merah.
                </div>
            @endif
            @yield('content')
        </main>

        <footer class="bg-white border-t border-[#e4e0d4] py-4 px-4 text-center text-[11px] sm:text-xs text-[#6b756f]">
            &copy; 2026 Pondok Pesantren Babul Hasanah. All rights reserved.
        </footer>
    </div>
</body>

</html>
