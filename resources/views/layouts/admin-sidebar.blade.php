<!-- OVERLAY SIDEBAR MOBILE -->
<div x-show="sidebarOpen" @click="sidebarOpen = false"
    class="fixed inset-0 z-40 bg-emerald-950/70 backdrop-blur-sm md:hidden" x-transition.opacity style="display: none;"></div>

<!-- SIDEBAR -->
<aside
    :class="{
        'translate-x-0': sidebarOpen,
        '-translate-x-full': !sidebarOpen,
        'md:w-20': sidebarMinimize,
        'md:w-64': !sidebarMinimize
    }"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-emerald-800 via-emerald-900 to-emerald-950 text-emerald-50/80 flex flex-col justify-between border-r border-emerald-700/50 transition-all duration-300 ease-in-out md:translate-x-0 md:static shadow-2xl md:shadow-none overflow-hidden">

    <!-- Bagian Atas: Logo & Tombol Toggle/Close -->
    <div>
        <div class="h-20 flex items-center justify-between px-6 border-b border-white/10">
            <!-- Logo & Title -->
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center font-black">
                    <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Logo Babul Hasanah"
                        class="w-full h-full object-contain">
                </div>
                <div x-show="!sidebarMinimize" x-transition.opacity class="whitespace-nowrap">
                    <span class="block text-white font-black text-sm tracking-wide">Babul Hasanah</span>
                    <span class="block text-[10px] text-amber-300 font-semibold uppercase tracking-wider">Panel
                        Dashboard</span>
                </div>
            </div>

            <!-- Tombol Close Mobile -->
            <button @click="sidebarOpen = false"
                class="md:hidden text-emerald-100/70 hover:text-white p-2 rounded-xl hover:bg-white/10 transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            <!-- Tombol Minimize Desktop -->
            <button @click="sidebarMinimize = !sidebarMinimize"
                class="hidden md:flex text-emerald-100/70 hover:text-white p-2 rounded-xl hover:bg-white/10 transition items-center justify-center">
                <i class="fa-solid" :class="sidebarMinimize ? 'fa-angles-right' : 'fa-angles-left'"></i>
            </button>
        </div>

        <!-- Daftar Menu Navigasi -->
        <div
            class="p-4 pt-5 space-y-1 overflow-y-auto max-h-[calc(100vh-13rem)] [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-emerald-700/60 [&::-webkit-scrollbar-thumb]:rounded-full hover:[&::-webkit-scrollbar-thumb]:bg-emerald-500/70">

            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" @click="sidebarOpen = false"
                class="flex items-center gap-3 px-4 py-3.5 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-white text-emerald-900 shadow-lg shadow-emerald-950/40' : 'hover:bg-white/10 text-emerald-50/80 hover:text-white' }} font-semibold text-xs transition relative group">
                <i class="fa-solid fa-chart-pie w-5 text-sm flex-shrink-0"></i>
                <span x-show="!sidebarMinimize" class="whitespace-nowrap">Dashboard</span>
                <div x-show="sidebarMinimize"
                    class="absolute left-full ml-3 px-2.5 py-1 bg-emerald-950 text-white text-xs rounded-md shadow-md opacity-0 group-hover:opacity-100 transition pointer-events-none z-50 whitespace-nowrap">
                    Dashboard
                </div>
            </a>

            <!-- Pendaftaran -->
            <a href="{{ route('admin.pendaftaran.index') }}" @click="sidebarOpen = false"
                class="flex items-center gap-3 px-4 py-3.5 rounded-xl {{ request()->routeIs('admin.pendaftaran.*') ? 'bg-white text-emerald-900 shadow-lg shadow-emerald-950/40' : 'hover:bg-white/10 text-emerald-50/80 hover:text-white' }} font-semibold text-xs transition relative group">
                <i class="fa-solid fa-address-book w-5 text-sm flex-shrink-0"></i>
                <span x-show="!sidebarMinimize" class="whitespace-nowrap">Pendaftaran</span>
                <div x-show="sidebarMinimize"
                    class="absolute left-full ml-3 px-2.5 py-1 bg-emerald-950 text-white text-xs rounded-md shadow-md opacity-0 group-hover:opacity-100 transition pointer-events-none z-50 whitespace-nowrap">
                    Pendaftaran
                </div>
            </a>

            <!-- Data Santri -->
            <a href="{{ route('admin.santri.index') }}" @click="sidebarOpen = false"
                class="flex items-center gap-3 px-4 py-3.5 rounded-xl {{ request()->routeIs('admin.santri.*') ? 'bg-white text-emerald-900 shadow-lg shadow-emerald-950/40' : 'hover:bg-white/10 text-emerald-50/80 hover:text-white' }} font-semibold text-xs transition relative group">
                <i class="fa-solid fa-people-group w-5 text-sm flex-shrink-0"></i>
                <span x-show="!sidebarMinimize" class="whitespace-nowrap">Data Santri</span>
                <div x-show="sidebarMinimize"
                    class="absolute left-full ml-3 px-2.5 py-1 bg-emerald-950 text-white text-xs rounded-md shadow-md opacity-0 group-hover:opacity-100 transition pointer-events-none z-50 whitespace-nowrap">
                    Data Santri
                </div>
            </a>

            <!-- Data Alumni -->
            <a href="{{ route('admin.alumni.index') }}" @click="sidebarOpen = false"
                class="flex items-center gap-3 px-4 py-3.5 rounded-xl {{ request()->routeIs('admin.alumni.*') ? 'bg-white text-emerald-900 shadow-lg shadow-emerald-950/40' : 'hover:bg-white/10 text-emerald-50/80 hover:text-white' }} font-semibold text-xs transition relative group">
                <i class="fa-solid fa-user-graduate w-5 text-sm flex-shrink-0"></i>
                <span x-show="!sidebarMinimize" class="whitespace-nowrap">Data Alumni</span>
                <div x-show="sidebarMinimize"
                    class="absolute left-full ml-3 px-2.5 py-1 bg-emerald-950 text-white text-xs rounded-md shadow-md opacity-0 group-hover:opacity-100 transition pointer-events-none z-50 whitespace-nowrap">
                    Data Alumni
                </div>
            </a>

            <!-- Pengguna Dashboard (hanya super admin) -->
            @if (auth()->check() && auth()->user()->role == 'super_admin')
                <a href="{{ route('super.pengguna.index') }}" @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-4 py-3.5 rounded-xl {{ request()->routeIs('super.pengguna.*') ? 'bg-white text-emerald-900 shadow-lg shadow-emerald-950/40' : 'hover:bg-white/10 text-emerald-50/80 hover:text-white' }} font-semibold text-xs transition relative group">
                    <i class="fa-solid fa-users-gear w-5 text-sm flex-shrink-0"></i>
                    <span x-show="!sidebarMinimize" class="whitespace-nowrap">Pengguna</span>
                    <div x-show="sidebarMinimize"
                        class="absolute left-full ml-3 px-2.5 py-1 bg-emerald-950 text-white text-xs rounded-md shadow-md opacity-0 group-hover:opacity-100 transition pointer-events-none z-50 whitespace-nowrap">
                        Pengguna
                    </div>
                </a>
            @endif

            <div class="h-3" aria-hidden="true"></div>

            <!-- Konten -->
            <a href="{{ route('admin.konten.index') }}" @click="sidebarOpen = false"
                class="flex items-center gap-3 px-4 py-3.5 rounded-xl {{ request()->routeIs('admin.konten.*') ? 'bg-white text-emerald-900 shadow-lg shadow-emerald-950/40' : 'hover:bg-white/10 text-emerald-50/80 hover:text-white' }} font-semibold text-xs transition relative group">
                <i class="fa-solid fa-blog w-5 text-sm flex-shrink-0"></i>
                <span x-show="!sidebarMinimize" class="whitespace-nowrap">Konten</span>
                <div x-show="sidebarMinimize"
                    class="absolute left-full ml-3 px-2.5 py-1 bg-emerald-950 text-white text-xs rounded-md shadow-md opacity-0 group-hover:opacity-100 transition pointer-events-none z-50 whitespace-nowrap">
                    Konten
                </div>
            </a>

            <!-- Jenjang (dengan submenu 7 jenjang) -->
            <div x-data="{ jenjangOpen: {{ request()->routeIs('admin.jenjang.*') ? 'true' : 'false' }} }">
                <div
                    class="flex items-center rounded-xl {{ request()->routeIs('admin.jenjang.index') ? 'bg-white text-emerald-900 shadow-lg shadow-emerald-950/40' : 'text-emerald-50/80 hover:bg-white/10 hover:text-white' }} font-semibold text-xs transition relative group">
                    <a href="{{ route('admin.jenjang.index') }}" @click="sidebarOpen = false"
                        class="flex flex-1 items-center gap-3 px-4 py-3.5 min-w-0">
                        <i class="fa-solid fa-layer-group w-5 text-sm flex-shrink-0"></i>
                        <span x-show="!sidebarMinimize" class="whitespace-nowrap">Jenjang</span>
                    </a>
                    <button type="button" x-show="!sidebarMinimize" @click="jenjangOpen = !jenjangOpen"
                        :aria-expanded="jenjangOpen" aria-label="Buka daftar jenjang"
                        class="px-3 py-3.5 text-emerald-100/70 hover:text-white transition">
                        <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200"
                            :class="jenjangOpen && 'rotate-180'"></i>
                    </button>
                    <div x-show="sidebarMinimize"
                        class="absolute left-full ml-3 px-2.5 py-1 bg-emerald-950 text-white text-xs rounded-md shadow-md opacity-0 group-hover:opacity-100 transition pointer-events-none z-50 whitespace-nowrap">
                        Jenjang
                    </div>
                </div>

                <div x-show="jenjangOpen && !sidebarMinimize" x-transition
                    class="mt-1 ml-4 space-y-0.5 border-l border-white/15 pl-2">
                    @foreach (\App\Support\Jenjang::semua() as $j)
                        @php
                            $aktifJenjang = request()->routeIs('admin.jenjang.show') && request()->route('jenjang') === $j['slug'];
                        @endphp
                        <a href="{{ route('admin.jenjang.show', $j['slug']) }}" @click="sidebarOpen = false"
                            class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-[11px] font-semibold transition {{ $aktifJenjang ? 'bg-white/15 text-white' : 'text-emerald-100/70 hover:bg-white/10 hover:text-white' }}">
                            <span class="h-1.5 w-1.5 flex-shrink-0 rounded-full"
                                style="background: {{ $j['warna'] }}"></span>
                            <span class="truncate">{{ $j['kode'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Berita -->
            <a href="{{ route('admin.berita.index') }}" @click="sidebarOpen = false"
                class="flex items-center gap-3 px-4 py-3.5 rounded-xl {{ request()->routeIs('admin.berita.*') ? 'bg-white text-emerald-900 shadow-lg shadow-emerald-950/40' : 'hover:bg-white/10 text-emerald-50/80 hover:text-white' }} font-semibold text-xs transition relative group">
                <i class="fa-solid fa-newspaper w-5 text-sm flex-shrink-0"></i>
                <span x-show="!sidebarMinimize" class="whitespace-nowrap">Berita</span>
                <div x-show="sidebarMinimize"
                    class="absolute left-full ml-3 px-2.5 py-1 bg-emerald-950 text-white text-xs rounded-md shadow-md opacity-0 group-hover:opacity-100 transition pointer-events-none z-50 whitespace-nowrap">
                    Berita
                </div>
            </a>

            <!-- Info Harian (hanya super admin) -->
            @if (auth()->check() && auth()->user()->role == 'super_admin')
                <a href="{{ route('admin.info.index') }}" @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-4 py-3.5 rounded-xl {{ request()->routeIs('admin.info.*') ? 'bg-white text-emerald-900 shadow-lg shadow-emerald-950/40' : 'hover:bg-white/10 text-emerald-50/80 hover:text-white' }} font-semibold text-xs transition relative group">
                    <i class="fa-solid fa-bullhorn w-5 text-sm flex-shrink-0"></i>
                    <span x-show="!sidebarMinimize" class="whitespace-nowrap">Info Harian</span>
                    <div x-show="sidebarMinimize"
                        class="absolute left-full ml-3 px-2.5 py-1 bg-emerald-950 text-white text-xs rounded-md shadow-md opacity-0 group-hover:opacity-100 transition pointer-events-none z-50 whitespace-nowrap">
                        Info Harian
                    </div>
                </a>
            @endif

            <!-- Struktur Organisasi -->
            <a href="{{ route('admin.struktur.index') }}" @click="sidebarOpen = false"
                class="flex items-center gap-3 px-4 py-3.5 rounded-xl {{ request()->routeIs('admin.struktur.*') ? 'bg-white text-emerald-900 shadow-lg shadow-emerald-950/40' : 'hover:bg-white/10 text-emerald-50/80 hover:text-white' }} font-semibold text-xs transition relative group">
                <i class="fa-solid fa-sitemap w-5 text-sm flex-shrink-0"></i>
                <span x-show="!sidebarMinimize" class="whitespace-nowrap">Struktur</span>
                <div x-show="sidebarMinimize"
                    class="absolute left-full ml-3 px-2.5 py-1 bg-emerald-950 text-white text-xs rounded-md shadow-md opacity-0 group-hover:opacity-100 transition pointer-events-none z-50 whitespace-nowrap">
                    Struktur
                </div>
            </a>

            <!-- Galeri -->
            <a href="{{ route('admin.galeri.index') }}" @click="sidebarOpen = false"
                class="flex items-center gap-3 px-4 py-3.5 rounded-xl {{ request()->routeIs('admin.galeri.*') ? 'bg-white text-emerald-900 shadow-lg shadow-emerald-950/40' : 'hover:bg-white/10 text-emerald-50/80 hover:text-white' }} font-semibold text-xs transition relative group">
                <i class="fa-solid fa-photo-film w-5 text-sm flex-shrink-0"></i>
                <span x-show="!sidebarMinimize" class="whitespace-nowrap">Galeri</span>
                <div x-show="sidebarMinimize"
                    class="absolute left-full ml-3 px-2.5 py-1 bg-emerald-950 text-white text-xs rounded-md shadow-md opacity-0 group-hover:opacity-100 transition pointer-events-none z-50 whitespace-nowrap">
                    Galeri
                </div>
            </a>

            <!-- Prestasi -->
            <a href="{{ route('admin.prestasi.index') }}" @click="sidebarOpen = false"
                class="flex items-center gap-3 px-4 py-3.5 rounded-xl {{ request()->routeIs('admin.prestasi.*') ? 'bg-white text-emerald-900 shadow-lg shadow-emerald-950/40' : 'hover:bg-white/10 text-emerald-50/80 hover:text-white' }} font-semibold text-xs transition relative group">
                <i class="fa-solid fa-trophy w-5 text-sm flex-shrink-0"></i>
                <span x-show="!sidebarMinimize" class="whitespace-nowrap">Prestasi</span>
                <div x-show="sidebarMinimize"
                    class="absolute left-full ml-3 px-2.5 py-1 bg-emerald-950 text-white text-xs rounded-md shadow-md opacity-0 group-hover:opacity-100 transition pointer-events-none z-50 whitespace-nowrap">
                    Prestasi
                </div>
            </a>

        </div>
    </div>

    <!-- Bagian Bawah: Tombol Logout -->
    <div class="p-4 border-t border-white/10">
        <button @click="logoutModalOpen = true; sidebarOpen = false;"
            class="flex w-full items-center justify-center md:justify-start gap-3 px-4 py-3.5 rounded-xl bg-white/10 hover:bg-red-500/25 hover:text-red-100 font-semibold text-xs transition text-emerald-50/80 border border-white/10 relative group">
            <i class="fa-solid fa-right-from-bracket w-5 text-sm flex-shrink-0"></i>
            <span x-show="!sidebarMinimize" class="whitespace-nowrap">Keluar</span>
            <div x-show="sidebarMinimize"
                class="absolute left-full ml-3 px-2.5 py-1 bg-emerald-950 text-white text-xs rounded-md shadow-md opacity-0 group-hover:opacity-100 transition pointer-events-none z-50 whitespace-nowrap">
                Keluar
            </div>
        </button>
    </div>
</aside>