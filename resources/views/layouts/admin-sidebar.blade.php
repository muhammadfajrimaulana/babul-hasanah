<!-- OVERLAY SIDEBAR MOBILE -->
<div x-show="sidebarOpen" @click="sidebarOpen = false"
    class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm md:hidden" x-transition.opacity style="display: none;">
</div>

<!-- SIDEBAR -->
<aside
    :class="{
        'translate-x-0': sidebarOpen,
        '-translate-x-full': !sidebarOpen,
        'md:w-20': sidebarMinimize,
        'md:w-64': !sidebarMinimize
    }"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-300 flex flex-col justify-between border-r border-slate-800 transition-all duration-300 ease-in-out md:translate-x-0 md:static shadow-2xl md:shadow-none overflow-hidden">

    <!-- Bagian Atas: Logo & Tombol Toggle/Close -->
    <div>
        <div class="h-20 flex items-center justify-between px-6 border-b border-slate-800">
            <!-- Logo & Title -->
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center font-black">
                    <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Logo Babul Hasanah"
                        class="w-full h-full object-contain">
                </div>
                <div x-show="!sidebarMinimize" x-transition.opacity class="whitespace-nowrap">
                    <span class="block text-white font-black text-sm tracking-wide">Babul Hasanah</span>
                    <span class="block text-[10px] text-emerald-400 font-semibold uppercase tracking-wider">Panel
                        Dashboard</span>
                </div>
            </div>

            <!-- Tombol Close Mobile -->
            <button @click="sidebarOpen = false"
                class="md:hidden text-slate-400 hover:text-white p-2 rounded-xl hover:bg-slate-800 transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            <!-- Tombol Minimize Desktop -->
            <button @click="sidebarMinimize = !sidebarMinimize"
                class="hidden md:flex text-slate-400 hover:text-white p-2 rounded-xl hover:bg-slate-800 transition items-center justify-center">
                <i class="fa-solid" :class="sidebarMinimize ? 'fa-angles-right' : 'fa-angles-left'"></i>
            </button>
        </div>

        <!-- Daftar Menu Navigasi -->
        <div
            class="p-4 space-y-1.5 overflow-y-auto max-h-[calc(100vh-13rem)] [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-slate-800 [&::-webkit-scrollbar-thumb]:rounded-full hover:[&::-webkit-scrollbar-thumb]:bg-slate-700">

            <p x-show="!sidebarMinimize"
                class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Ringkasan</p>

            <!-- Dashboard Menu -->
            <a href="{{ route('admin.dashboard') }}" @click="sidebarOpen = false"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/50' : 'hover:bg-slate-800 text-slate-400' }} font-semibold text-xs transition relative group">
                <i class="fa-solid fa-chart-pie w-5 text-sm flex-shrink-0"></i>
                <span x-show="!sidebarMinimize" class="whitespace-nowrap">DASHBOARD</span>

                <!-- Tooltip saat minimized -->
                <div x-show="sidebarMinimize"
                    class="absolute left-full ml-3 px-2.5 py-1 bg-slate-800 text-white text-xs rounded-md shadow-md opacity-0 group-hover:opacity-100 transition pointer-events-none z-50 whitespace-nowrap">
                    Dashboard
                </div>
            </a>

            <!-- MENU UTAMA -->
            <div class="pt-3" x-data="{ open: {{ request()->routeIs('admin.pendaftaran.*') || request()->routeIs('admin.santri.*') || request()->routeIs('admin.alumni.*') || request()->routeIs('super.pengguna.*') ? 'true' : 'false' }} }">
                <button @click="if(!sidebarMinimize) { open = !open } else { sidebarMinimize = false; open = true; }"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-xs font-bold uppercase tracking-wider text-slate-400 hover:bg-slate-800 transition relative group">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-address-card w-5 text-sm flex-shrink-0"></i>
                        <span x-show="!sidebarMinimize" class="whitespace-nowrap">Menu Utama</span>
                    </div>
                    <i x-show="!sidebarMinimize"
                        class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200"
                        :class="open ? 'rotate-180 text-emerald-400' : ''"></i>

                    <!-- Tooltip saat minimized -->
                    <div x-show="sidebarMinimize"
                        class="absolute left-full ml-3 px-2.5 py-1 bg-slate-800 text-white text-xs rounded-md shadow-md opacity-0 group-hover:opacity-100 transition pointer-events-none z-50 whitespace-nowrap">
                        Menu Utama
                    </div>
                </button>

                <!-- Sub-menu Dropdown -->
                <div x-show="open && !sidebarMinimize" x-collapse
                    class="pl-4 pt-1.5 space-y-1 border-l border-slate-800 ml-4">
                    <a href="{{ route('admin.pendaftaran.index') }}" @click="sidebarOpen = false"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.pendaftaran.*') ? 'bg-emerald-600/20 text-emerald-400 font-bold' : 'hover:bg-slate-800 text-slate-400' }} text-xs transition">
                        <i class="fas fa-address-book w-4 text-xs flex-shrink-0"></i>
                        <span>Pendaftaran</span>
                    </a>
                    <a href="{{ route('admin.santri.index') }}" @click="sidebarOpen = false"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.santri.*') ? 'bg-emerald-600/20 text-emerald-400 font-bold' : 'hover:bg-slate-800 text-slate-400' }} text-xs transition">
                        <i class="fa-solid fa-people-group w-4 text-xs flex-shrink-0"></i>
                        <span>Data Santri</span>
                    </a>
                    <a href="{{ route('admin.alumni.index') }}" @click="sidebarOpen = false"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.alumni.*') ? 'bg-emerald-600/20 text-emerald-400 font-bold' : 'hover:bg-slate-800 text-slate-400' }} text-xs transition">
                        <i class="fa-solid fa-user-graduate w-4 text-xs flex-shrink-0"></i>
                        <span>Data Alumni</span>
                    </a>
                    @if (auth()->check() && auth()->user()->role == 'super_admin')
                        <a href="{{ route('super.pengguna.index') }}" @click="sidebarOpen = false"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('super.pengguna.*') ? 'bg-emerald-600/20 text-emerald-400 font-bold' : 'hover:bg-slate-800 text-slate-400' }} text-xs transition">
                            <i class="fa-solid fa-users-gear w-4 text-xs flex-shrink-0"></i>
                            <span>Pengguna Dashboard</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- MENU PENGELOLAAN -->
            <div class="pt-2" x-data="{ open: {{ request()->routeIs('admin.konten.*') || request()->routeIs('admin.program.*') || request()->routeIs('admin.berita.*') || request()->routeIs('admin.info.*') || request()->routeIs('admin.kegiatan.*') || request()->routeIs('admin.galeri.*') || request()->routeIs('admin.prestasi.*') ? 'true' : 'false' }} }">
                <button @click="if(!sidebarMinimize) { open = !open } else { sidebarMinimize = false; open = true; }"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-xs font-bold uppercase tracking-wider text-slate-400 hover:bg-slate-800 transition relative group">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-layer-group w-5 text-sm flex-shrink-0"></i>
                        <span x-show="!sidebarMinimize" class="whitespace-nowrap">Pengelolaan</span>
                    </div>
                    <i x-show="!sidebarMinimize"
                        class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200"
                        :class="open ? 'rotate-180 text-emerald-400' : ''"></i>

                    <!-- Tooltip saat minimized -->
                    <div x-show="sidebarMinimize"
                        class="absolute left-full ml-3 px-2.5 py-1 bg-slate-800 text-white text-xs rounded-md shadow-md opacity-0 group-hover:opacity-100 transition pointer-events-none z-50 whitespace-nowrap">
                        Pengelolaan
                    </div>
                </button>

                <!-- Sub-menu Dropdown -->
                <div x-show="open && !sidebarMinimize" x-collapse
                    class="pl-4 pt-1.5 space-y-1 border-l border-slate-800 ml-4">
                    <a href="{{ route('admin.konten.index') }}" @click="sidebarOpen = false"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.konten.*') ? 'bg-emerald-600/20 text-emerald-400 font-bold' : 'hover:bg-slate-800 text-slate-400' }} text-xs transition">
                        <i class="fa-solid fa-blog w-4 text-xs flex-shrink-0"></i>
                        <span>Konten</span>
                    </a>
                    <a href="{{ route('admin.program.index') }}" @click="sidebarOpen = false"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.program.*') ? 'bg-emerald-600/20 text-emerald-400 font-bold' : 'hover:bg-slate-800 text-slate-400' }} text-xs transition">
                        <i class="fa-solid fa-folder-tree w-4 text-xs flex-shrink-0"></i>
                        <span>Program</span>
                    </a>
                    <a href="{{ route('admin.berita.index') }}" @click="sidebarOpen = false"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.berita.*') ? 'bg-emerald-600/20 text-emerald-400 font-bold' : 'hover:bg-slate-800 text-slate-400' }} text-xs transition">
                        <i class="fa-solid fa-newspaper w-4 text-xs flex-shrink-0"></i>
                        <span>Berita</span>
                    </a>
                    @if (auth()->check() && auth()->user()->role == 'super_admin')
                        <a href="{{ route('admin.info.index') }}" @click="sidebarOpen = false"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.info.*') ? 'bg-emerald-600/20 text-emerald-400 font-bold' : 'hover:bg-slate-800 text-slate-400' }} text-xs transition">
                            <i class="fa-solid fa-bullhorn w-4 text-xs flex-shrink-0"></i>
                            <span>Info Harian</span>
                        </a>
                    @endif
                    <a href="{{ route('admin.struktur.index') }}" @click="sidebarOpen = false"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.struktur.*') ? 'bg-emerald-600/20 text-emerald-400 font-bold' : 'hover:bg-slate-800 text-slate-400' }} text-xs transition">
                        <i class="fa-solid fa-calendar-plus w-4 text-xs flex-shrink-0"></i>
                        <span>Struktur Organisasi</span>
                    </a>
                    
                    <a href="{{ route('admin.galeri.index') }}" @click="sidebarOpen = false"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.galeri.*') ? 'bg-emerald-600/20 text-emerald-400 font-bold' : 'hover:bg-slate-800 text-slate-400' }} text-xs transition">
                        <i class="fa-solid fa-photo-film w-4 text-xs flex-shrink-0"></i>
                        <span>Galeri</span>
                    </a>
                    <a href="{{ route('admin.prestasi.index') }}" @click="sidebarOpen = false"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.prestasi.*') ? 'bg-emerald-600/20 text-emerald-400 font-bold' : 'hover:bg-slate-800 text-slate-400' }} text-xs transition">
                        <i class="fa-solid fa-trophy w-4 text-xs flex-shrink-0"></i>
                        <span>Prestasi</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Bagian Bawah: Tombol Logout -->
    <div class="p-4 border-t border-slate-800 flex items-center justify-between gap-2">
        <button @click="logoutModalOpen = true; sidebarOpen = false;"
            class="flex w-full items-center justify-center md:justify-start gap-3 px-4 py-3 rounded-xl bg-slate-800/50 hover:bg-red-600/20 hover:text-red-400 font-semibold text-xs transition text-slate-400 border border-slate-800 relative group">
            <i class="fa-solid fa-right-from-bracket w-5 text-sm flex-shrink-0"></i>
            <span x-show="!sidebarMinimize" class="whitespace-nowrap">Logout</span>

            <!-- Tooltip saat minimized -->
            <div x-show="sidebarMinimize"
                class="absolute left-full ml-3 px-2.5 py-1 bg-slate-800 text-white text-xs rounded-md shadow-md opacity-0 group-hover:opacity-100 transition pointer-events-none z-50 whitespace-nowrap">
                Logout
            </div>
        </button>
    </div>
</aside>
