<header
    class="h-16 sm:h-20 bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between px-3 sm:px-6 z-10 shadow-sm relative transition-colors duration-300">

    <!-- BAGIAN KIRI & TENGAH (MOBILE) -->
    <div class="flex items-center gap-2 sm:gap-3">
        <!-- Hamburger Menu -->
        <button @click="sidebarOpen = true"
            class="md:hidden text-[#1b7b32] dark:text-emerald-400 hover:text-[#ee8a2a] py-1.5 px-2 rounded-xl flex items-center justify-center transition active:scale-95">
            <i class="fa-solid fa-bars-staggered text-lg"></i>
        </button>

        <!-- Judul Panel untuk Desktop (Tersembunyi di Mobile) -->
        <h1 class="hidden md:flex items-center gap-2.5 text-lg font-black text-slate-900 dark:text-white">
            <span class="grid h-8 w-8 place-items-center rounded-xl bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                <i class="fa-solid fa-gauge-high text-sm"></i>
            </span>
            Panel Admin <span class="hidden font-bold text-slate-400 lg:inline dark:text-slate-500">Pesantren</span>
        </h1>
    </div>

    <!-- LOGO TEKS DI TENGAH (Mobile) -->
    <div class="absolute left-1/2 -translate-x-1/2 md:hidden flex items-center justify-center pointer-events-none">
        <img src="{{ asset('/assets/img/logo/logo-teks.png') }}" alt="Logo Pondok Pesantren Babul Hasanah" class="h-8 sm:h-10 w-auto object-contain">
    </div>

    <!-- BAGIAN KANAN: TOMBOL TOGGLE DARK MODE & USER PROFILE -->
    <div class="flex items-center gap-2 sm:gap-3">
        <!-- Tombol Toggle Dark Mode -->
        <button @click="toggleDarkMode()"
            class="w-9 h-9 sm:w-10 sm:h-10 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-amber-400 rounded-xl flex items-center justify-center transition active:scale-95 shadow-sm"
            :title="darkMode ? 'Ubah ke Light Mode' : 'Ubah ke Dark Mode'">
            <i class="fa-solid text-sm" :class="darkMode ? 'fa-sun' : 'fa-moon'"></i>
        </button>

        <!-- Dropdown Profil Pengguna -->
        @php
            $avatarUrl = auth()->user()->avatar
                ? asset('storage/' . auth()->user()->avatar)
                : asset('assets/img/logo/logo.png');
        @endphp
        <div x-data="{ profileOpen: false }" @click.away="profileOpen = false" class="relative">
            <button type="button" @click="profileOpen = !profileOpen"
                class="group flex h-10 items-center gap-2 rounded-full border border-slate-200 bg-white py-1 pl-1 pr-2.5 text-slate-600 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:border-emerald-500/50 dark:hover:bg-slate-800 sm:h-11 sm:pr-3"
                :class="{ 'ring-2 ring-emerald-400/60': profileOpen }"
                :aria-expanded="profileOpen" aria-haspopup="true"
                aria-label="Menu profil {{ auth()->user()->name }}">
                <img src="{{ $avatarUrl }}" alt="Foto profil {{ auth()->user()->name }}"
                    class="h-8 w-8 shrink-0 rounded-full object-cover ring-2 ring-white dark:ring-slate-700 sm:h-9 sm:w-9"
                    onerror="this.src='{{ asset('assets/img/logo/logo.png') }}'">
                <span class="hidden max-w-[7rem] truncate text-xs font-bold sm:inline">{{ auth()->user()->name }}</span>
                <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400"
                    :class="profileOpen ? 'rotate-180' : ''"></i>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="profileOpen" x-cloak x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
                class="absolute right-0 z-50 mt-2 w-64 origin-top-right rounded-2xl border border-slate-200 bg-white p-2 shadow-xl shadow-slate-900/10 dark:border-slate-800 dark:bg-slate-900 dark:shadow-black/30">

                <!-- Header Profil -->
                <div class="rounded-xl bg-gradient-to-br from-emerald-50 to-emerald-100/50 p-4 dark:from-emerald-950/40 dark:to-slate-800/50">
                    <div class="flex items-center gap-3">
                        <img src="{{ $avatarUrl }}" alt="Foto profil {{ auth()->user()->name }}"
                            class="h-11 w-11 shrink-0 rounded-full object-cover ring-2 ring-white shadow-sm dark:ring-slate-700"
                            onerror="this.src='{{ asset('assets/img/logo/logo.png') }}'">
                        <div class="min-w-0">
                            <h3 class="truncate text-sm font-black text-slate-900 dark:text-white">{{ auth()->user()->name }}</h3>
                            <p class="truncate text-[11px] text-slate-500 dark:text-slate-400">{{ auth()->user()->email }}</p>
                            <span class="mt-1 inline-block rounded-md bg-white/80 px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wider text-emerald-700 shadow-sm dark:bg-emerald-900/40 dark:text-emerald-300">
                                {{ \App\Support\Akses::label(auth()->user()->role) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="px-3 pb-1 pt-3 text-[10px] font-black uppercase tracking-[0.14em] text-slate-400 dark:text-slate-500">Akun</div>
                <a href="{{ route('admin.profil') }}" @click="profileOpen = false"
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-800 dark:text-slate-200 dark:hover:bg-slate-800 dark:hover:text-white">
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                        <i class="fa-solid fa-user-pen text-xs"></i>
                    </span>
                    <span>Setting Akun</span>
                </a>

                <div class="mx-3 my-1 h-px bg-slate-200 dark:bg-slate-800"></div>

                <div class="px-3 pb-1 pt-1 text-[10px] font-black uppercase tracking-[0.14em] text-slate-400 dark:text-slate-500">Keamanan</div>
                <a href="#" @click.prevent="profileOpen = false; resetPasswordModal = true"
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-800 dark:text-slate-200 dark:hover:bg-slate-800 dark:hover:text-white">
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                        <i class="fa-solid fa-key text-xs"></i>
                    </span>
                    <span>Reset Password</span>
                </a>

                <div class="mx-3 my-1 h-px bg-slate-200 dark:bg-slate-800"></div>

                <!-- Menu Item: Logout -->
                <button type="button" @click="logoutModalOpen = true; profileOpen = false;"
                    class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold text-rose-600 transition hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-900/20">
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-rose-50 dark:bg-rose-900/20">
                        <i class="fa-solid fa-right-from-bracket text-xs"></i>
                    </span>
                    <span>Keluar</span>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- MODAL RESET PASSWORD -->
<div x-show="resetPasswordModal" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" @click.away="resetPasswordModal = false"
    @keydown.escape.window="resetPasswordModal = false">

    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-800 max-w-md w-full p-6 sm:p-8">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-rose-100 dark:bg-rose-900/30 text-rose-600 rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-key text-lg"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Reset Password</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Masukkan password baru untuk akun Anda.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.profil.password') }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Password Baru</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-3 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition">
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="resetPasswordModal = false"
                    class="px-4 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-6 py-2.5 text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-lg transition shadow-lg shadow-rose-600/20">
                    Simpan Password
                </button>
            </div>
        </form>
    </div>
</div>