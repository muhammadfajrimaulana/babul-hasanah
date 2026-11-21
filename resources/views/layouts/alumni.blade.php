<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Alumni') - Pondok Pesantren Babul Hasanah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-slate-100 text-slate-900 font-sans antialiased">
    <div class="min-h-screen flex flex-col justify-between">
        <!-- Top Navbar -->
        <header class="bg-white border-b border-slate-200 py-4 px-6 sm:px-10 flex items-center justify-between shadow-sm z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
                <div>
                    <h1 class="font-black text-slate-900 text-sm sm:text-base">Portal Alumni</h1>
                    <p class="text-[10px] text-indigo-600 font-semibold uppercase tracking-wider">Pondok Pesantren Babul Hasanah</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <span class="text-xs font-bold text-slate-700 hidden sm:inline">Halo, {{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="bg-red-50 text-red-600 hover:bg-red-600 hover:text-white px-4 py-2 rounded-xl text-xs font-bold transition duration-300">
                        <i class="fa-solid fa-right-from-bracket mr-1.5"></i> Keluar
                    </button>
                </form>
            </div>
        </header>

        <!-- Menu -->
        <nav class="bg-white border-b border-slate-200 px-6 sm:px-10">
            <div class="max-w-7xl mx-auto flex items-center gap-1.5 overflow-x-auto py-2">
                @php
                    $navClass = fn (bool $aktif) => 'inline-flex shrink-0 items-center gap-2 rounded-xl px-3.5 py-2 text-[11px] font-bold transition '
                        .($aktif
                            ? 'bg-indigo-700 text-white shadow-sm'
                            : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-700');
                @endphp

                <a href="{{ route('alumni.dashboard') }}" class="{{ $navClass(request()->routeIs('alumni.dashboard')) }}">
                    <i class="fa-solid fa-house"></i> Beranda
                </a>
                <a href="{{ route('alumni.profil') }}" class="{{ $navClass(request()->routeIs('alumni.profil*')) }}">
                    <i class="fa-solid fa-id-card"></i> Profil
                </a>
                <a href="{{ route('alumni.dokumen') }}" class="{{ $navClass(request()->routeIs('alumni.dokumen*')) }}">
                    <i class="fa-solid fa-folder-open"></i> Dokumen
                </a>
                <a href="{{ route('alumni.tracer') }}" class="{{ $navClass(request()->routeIs('alumni.tracer*')) }}">
                    <i class="fa-solid fa-briefcase"></i> Tracer Study
                </a>
                <a href="{{ route('alumni.legalisir') }}" class="{{ $navClass(request()->routeIs('alumni.legalisir*')) }}">
                    <i class="fa-solid fa-stamp"></i> Legalisir
                </a>
                <a href="{{ route('alumni.pengumuman') }}" class="{{ $navClass(request()->routeIs('alumni.pengumuman*')) }}">
                    <i class="fa-solid fa-bullhorn"></i> Pengumuman
                </a>
            </div>
        </nav>

        <!-- Content -->
        <main class="flex-1 max-w-7xl w-full mx-auto p-6 sm:p-8 lg:px-10">
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

        <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-400">
            &copy; 2026 Pondok Pesantren Babul Hasanah. All rights reserved.
        </footer>
    </div>
</body>

</html>
