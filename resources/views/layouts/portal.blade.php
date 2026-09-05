<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Wali Santri - Pondok Pesantren Babul Hasanah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-slate-100 text-slate-900 font-sans antialiased">

    <div class="min-h-screen flex flex-col justify-between">
        <!-- Top Navbar Portal -->
        <header
            class="bg-white border-b border-slate-200 py-4 px-6 sm:px-10 flex items-center justify-between shadow-sm z-10">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-emerald-500 text-slate-900 flex items-center justify-center font-black">
                    <i class="fa-solid fa-house-user"></i>
                </div>
                <div>
                    <h1 class="font-black text-slate-900 text-sm sm:text-base">Portal Wali Santri</h1>
                    <p class="text-[10px] text-emerald-600 font-semibold uppercase tracking-wider">Pondok Pesantren
                        Babul Hasanah</p>
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

        <!-- Main Content (Tempat yield masuk) -->
        <main class="flex-1 max-w-7xl w-full mx-auto p-6 sm:p-8 lg:px-10">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-400">
            &copy; 2026 Pondok Pesantren Babul Hasanah. All rights reserved.
        </footer>
    </div>

</body>

</html>
