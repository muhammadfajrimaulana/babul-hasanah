<header
    class="h-20 bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between px-4 sm:px-6 z-10 shadow-sm relative transition-colors duration-300">

    <!-- BAGIAN KIRI & TENGAH (MOBILE) -->
    <div class="flex items-center gap-3">
        <!-- Hamburger Menu -->
        <button @click="sidebarOpen = true"
            class="md:hidden text-[#1b7b32] dark:text-emerald-400 hover:text-[#ee8a2a] py-1.5 px-2.5 rounded-xl flex items-center justify-center transition active:scale-95 shadow-sm">
            <i class="fa-solid fa-bars-staggered text-lg"></i>
        </button>

        <!-- Judul Panel untuk Desktop (Tersembunyi di Mobile) -->
        <h1 class="hidden md:block text-lg font-black text-slate-900 dark:text-white">Panel Admin Pesantren
        </h1>
    </div>

    <!-- LOGO TEKS DI TENGAH (Mobile) -->
    <div class="absolute left-1/2 -translate-x-1/2 md:hidden flex items-center justify-center">
        <img src="{{ asset('/assets/img/logo/logo-teks.png') }}" alt="Logo Pesantren" class="h-10 w-auto object-contain">
    </div>

    <!-- BAGIAN KANAN: TOMBOL TOGGLE DARK MODE & USER INFO -->
    <div class="flex items-center gap-3">
        <!-- Tombol Toggle Dark Mode -->
        <button @click="toggleDarkMode()"
            class="w-10 h-10 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-amber-400 rounded-xl flex items-center justify-center transition active:scale-95 shadow-sm"
            :title="darkMode ? 'Ubah ke Light Mode' : 'Ubah ke Dark Mode'">
            <i class="fa-solid text-sm" :class="darkMode ? 'fa-sun' : 'fa-moon'"></i>
        </button>

        <!-- Badge Nama User (Desktop) -->
        <div
            class="hidden sm:flex items-center text-xs font-bold text-slate-900 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 px-3.5 py-1.5 rounded-full border border-slate-200 dark:border-slate-700">
            <i class="fa-solid fa-circle text-[8px] text-emerald-500 mr-1.5"></i> {{ Auth::user()->name }}
        </div>
    </div>
</header>
