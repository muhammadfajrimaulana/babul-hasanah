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

    <!-- BAGIAN KANAN: TOMBOL TOGGLE DARK MODE & USER PROFILE -->
    <div class="flex items-center gap-3">
        <!-- Tombol Toggle Dark Mode -->
        <button @click="toggleDarkMode()"
            class="w-10 h-10 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-amber-400 rounded-xl flex items-center justify-center transition active:scale-95 shadow-sm"
            :title="darkMode ? 'Ubah ke Light Mode' : 'Ubah ke Dark Mode'">
            <i class="fa-solid text-sm" :class="darkMode ? 'fa-sun' : 'fa-moon'"></i>
        </button>

        <!-- Dropdown Profil Pengguna -->
        <div x-data="{ profileOpen: false }" @click.away="profileOpen = false" class="relative">
            <button @click="profileOpen = !profileOpen"
                class="w-10 h-10 rounded-full overflow-hidden border-2 border-slate-200 dark:border-slate-700 flex items-center justify-center transition transform hover:scale-105 shadow-sm cursor-pointer"
                :class="{ 'ring-2 ring-emerald-400': profileOpen }">
                @php
                    $avatarUrl = auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('assets/img/logo/logo.png');
                @endphp
                <img src="{{ $avatarUrl }}" alt="Profil" class="w-full h-full object-cover" onerror="this.src='{{ asset('assets/img/logo/logo.png') }}'">
                <i class="fa-solid fa-chevron-down ml-1 text-[10px] text-slate-500 dark:text-slate-400 transition-transform duration-200"
                   :class="profileOpen ? 'rotate-180' : ''"></i>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="profileOpen" x-cloak
                class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 z-50 animate-fade-in py-2">
                
                <!-- Header Profil -->
                <div class="p-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <img src="{{ $avatarUrl }}" alt="Profil" class="w-10 h-10 rounded-full object-cover" onerror="this.src='{{ asset('assets/img/logo/logo.png') }}'">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ Auth::user()->name }}</h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ Auth::user()->email }}</p>
                            <span class="mt-1 inline-block rounded-md bg-emerald-50 px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wider text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400">
                                {{ Auth::user()->labelPeran() }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Menu Item: Setting Akun -->
                <div class="px-4 py-2 text-xs font-bold text-slate-400 uppercase tracking-wider">Akun</div>
                <a href="{{ route('admin.profil') }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    <i class="fa-solid fa-user-pen w-5 text-slate-500 dark:text-slate-400"></i>
                    <span>Setting Akun</span>
                </a>

                <!-- Divider -->
                <div class="px-4 py-1">
                    <div class="h-[1px] bg-slate-200 dark:bg-slate-800 my-1"></div>
                </div>

                <div class="px-4 py-2 text-xs font-bold text-slate-400 uppercase tracking-wider">Keamanan</div>
                <a href="#" @click.prevent="profileOpen = false; resetPasswordModal = true"
                    class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    <i class="fa-solid fa-key w-5 text-slate-500 dark:text-slate-400"></i>
                    <span>Reset Password</span>
                </a>

                <div class="px-4 py-1">
                    <div class="h-[1px] bg-slate-200 dark:bg-slate-800 my-1"></div>
                </div>

                <!-- Menu Item: Logout -->
                <button @click="logoutModalOpen = true; profileOpen = false;"
                    class="flex items-center gap-3 px-4 py-3 text-sm text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition">
                    <i class="fa-solid fa-right-from-bracket w-5"></i>
                    <span>Keluar</span>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- MODAL RESET PASSWORD -->
<div x-data="{ resetPasswordModal: false }" x-show="resetPasswordModal" x-cloak
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