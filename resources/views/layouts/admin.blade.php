<!DOCTYPE html>
<html lang="id" x-data="{
    darkMode: window.bacaDarkMode(),
    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        try {
            localStorage.setItem('darkMode', String(this.darkMode));
        } catch (e) {
            /* Penyimpanan browser bisa diblokir; tema tetap berganti untuk sesi ini. */
        }
        window.dispatchEvent(new CustomEvent('tema-diubah', { detail: this.darkMode }));
    }
}" :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <script>
        // Pasang kelas dark sebelum CSS/Alpine dimuat supaya tidak berkedip (FOUC).
        window.bacaDarkMode = function () {
            try {
                return localStorage.getItem('darkMode') === 'true';
            } catch (e) {
                return false;
            }
        };
        if (window.bacaDarkMode()) document.documentElement.classList.add('dark');
    </script>
    <title>@yield('title', 'Pondok Pesantren Babul Hasanah')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
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
    x-data="{ sidebarOpen: false, sidebarMinimize: localStorage.getItem('adminSidebarMinimize') === 'true', logoutModalOpen: false, resetPasswordModal: false }"
    x-init="
        if (window.innerWidth < 768) sidebarMinimize = false;
        $watch('sidebarMinimize', value => {
            if (window.innerWidth >= 768) localStorage.setItem('adminSidebarMinimize', value);
        });
        window.addEventListener('resize', () => {
            if (window.innerWidth < 768 && sidebarMinimize) sidebarMinimize = false;
        });
    "
    :class="{ 'overflow-hidden': sidebarOpen }">

    <div class="flex h-screen overflow-hidden">

        @include('layouts.admin-sidebar')

        <!-- AREA KONTEN UTAMA -->
        <div class="flex-1 flex flex-col overflow-hidden">

            @include('layouts.admin-navbar')

            <!-- MODAL POP-UP KONFIRMASI LOGOUT -->
            <div x-show="logoutModalOpen" x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @keydown.escape.window="logoutModalOpen = false"
                @click.self="logoutModalOpen = false"
                x-effect="if (logoutModalOpen) $nextTick(() => $refs.logoutConfirm?.focus())"
                role="dialog" aria-modal="true" aria-labelledby="logout-title">

                <div class="relative w-full max-w-md overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl shadow-slate-950/20 dark:border-slate-800 dark:bg-slate-900"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100">

                    <!-- Header ringkas, mengikuti gaya modal Reset Password -->
                    <div class="flex items-center gap-3 border-b border-slate-100 px-6 py-5 dark:border-slate-800 sm:px-8">
                        <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                            <i class="fa-solid fa-right-from-bracket text-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-black uppercase tracking-[0.16em] text-emerald-700 dark:text-emerald-400">Panel Admin Pesantren</p>
                            <h3 id="logout-title" class="mt-0.5 text-lg font-black text-slate-900 dark:text-white">Keluar dari akun Anda?</h3>
                        </div>
                    </div>

                    <div class="space-y-5 px-6 pb-6 pt-5 sm:px-8">
                        <p class="text-sm leading-relaxed text-slate-500 dark:text-slate-400">
                            Sesi Anda akan diakhiri. Anda perlu masuk kembali untuk mengakses panel admin.
                        </p>

                        <!-- Ringkasan akun yang sedang login -->
                        <div class="flex items-center gap-3 rounded-2xl border border-emerald-100 bg-emerald-50/60 p-3 text-left dark:border-emerald-500/20 dark:bg-emerald-500/5">
                            @php
                                $logoutAvatar = auth()->user()->avatar
                                    ? asset('storage/' . auth()->user()->avatar)
                                    : asset('assets/img/logo/logo.png');
                            @endphp
                            <img src="{{ $logoutAvatar }}" alt="Foto profil {{ auth()->user()->name }}"
                                class="h-10 w-10 shrink-0 rounded-full object-cover ring-2 ring-white dark:ring-slate-700"
                                onerror="this.src='{{ asset('assets/img/logo/logo.png') }}'">
                            <div class="min-w-0">
                                <p class="truncate text-xs font-bold text-slate-900 dark:text-white">{{ auth()->user()->name }}</p>
                                <p class="truncate text-[11px] text-slate-500 dark:text-slate-400">{{ \App\Support\Akses::label(auth()->user()->role) }}</p>
                            </div>
                            <i class="fa-solid fa-circle-check ml-auto text-emerald-500" aria-hidden="true"></i>
                        </div>

                        <div class="space-y-2">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" x-ref="logoutConfirm"
                                    class="group flex w-full items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 py-3 text-sm font-black text-white shadow-lg shadow-rose-600/25 transition hover:bg-rose-700 active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-400">
                                    <i class="fa-solid fa-right-from-bracket transition-transform group-hover:translate-x-0.5"></i>
                                    Ya, keluar sekarang
                                </button>
                            </form>

                            <button type="button" @click="logoutModalOpen = false"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                                Batal, tetap di sini
                            </button>
                        </div>
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
