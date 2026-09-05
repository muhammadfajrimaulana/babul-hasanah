<!DOCTYPE html>
<html lang="id" x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true',
    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
    }
}" :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pondok Pesantren Babul Hasanah')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            /* Palette Colors */
            --color-white: #fefefe;
            --color-pharmaceutical-green: #1b7b32;
            --color-kashmir: #698d6e;
            --color-misty-marsh: #cdd9cf;
            --color-copper-patina: #9cb49d;
            --color-woodgrain: #916438;
            --color-queen-of-the-night: #44467e;
            --color-bourbon-peach: #ee8a3e;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        /* --- CUSTOM SCROLLBAR --- */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(203, 213, 225, 0.6);
            border-radius: 9999px;
            transition: background-color 0.2s ease;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: rgba(148, 163, 184, 0.9);
        }

        /* Dark Mode Scrollbar */
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(51, 65, 85, 0.7);
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: rgba(71, 85, 105, 1);
        }
    </style>
</head>

<body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans antialiased"
    x-data="{ sidebarOpen: false, sidebarMinimize: false, logoutModalOpen: false }">

    <div class="flex h-screen overflow-hidden">

        @include('layouts.admin-sidebar')

        <!-- AREA KONTEN UTAMA -->
        <div class="flex-1 flex flex-col overflow-hidden">

            @include('layouts.admin-navbar')

            <!-- MODAL POP-UP KONFIRMASI LOGOUT -->
            <div x-show="logoutModalOpen" x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-fade-in"
                @keydown.escape.window="logoutModalOpen = false">

                <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-800 max-w-sm w-full p-6 sm:p-8 space-y-5 text-center relative"
                    @click.away="logoutModalOpen = false">

                    <div
                        class="mx-auto w-14 h-14 bg-rose-100 dark:bg-rose-900/30 text-rose-600 rounded-2xl flex items-center justify-center text-xl shadow-sm">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>

                    <div class="space-y-1.5">
                        <h3 class="text-base font-black text-slate-900 dark:text-white">Konfirmasi Keluar</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            Apakah Anda yakin ingin keluar dari Panel Admin Pesantren? Sesi Anda akan diakhiri.
                        </p>
                    </div>

                    <div class="space-y-2 pt-1">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-black px-4 py-3 rounded-xl text-xs transition shadow-lg shadow-rose-600/20 flex items-center justify-center gap-2">
                                Ya, Keluar Sekarang
                            </button>
                        </form>

                        <button type="button" @click="logoutModalOpen = false"
                            class="w-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold px-4 py-2.5 rounded-xl text-xs transition">
                            Batal
                        </button>
                    </div>

                </div>
            </div>

            <!-- MAIN CONTENT (Ditambahkan kelas custom-scrollbar) -->
            <main
                class="flex-1 overflow-x-hidden overflow-y-auto custom-scrollbar bg-slate-100 dark:bg-slate-950 p-4 sm:p-6 lg:p-8 pb-24 md:pb-8 transition-colors duration-300">
                @yield('content')
            </main>

        </div>
    </div>

    @include('layouts.admin-footer')

</body>

</html>
