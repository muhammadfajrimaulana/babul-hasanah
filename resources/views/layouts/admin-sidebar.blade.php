@php
    $u = auth()->user();
    $dashRoute = \App\Support\Akses::dashboardRoute($u);
    $roleLabel = \App\Support\Akses::label($u?->role);
    $avatarUrl = $u?->avatar ? asset('storage/' . $u->avatar) : asset('assets/img/logo/logo.png');
    $isGlobal = $u && ! \App\Support\Akses::dibatasiJenjang($u);

    $dashboardAktif =
        request()->routeIs('admin.dashboard') ||
        request()->routeIs('admin.dashboard.pengasuh') ||
        request()->routeIs('admin.dashboard.pembina') ||
        (request()->routeIs('admin.dashboard.jenjang') && !$isGlobal);

    $dashboardJenjangAktif = $isGlobal && request()->routeIs('admin.dashboard.jenjang');

    $akademikAktif = request()->routeIs('admin.akademik.*');
    $prestasiAktif = request()->routeIs('admin.prestasi.*') || request()->routeIs('admin.dashboard.jenjang.prestasi.form');
    $jenjangAktif = request()->routeIs('admin.jenjang.*');

    $itemClass = fn(bool $active) => 'sb-item' . ($active ? ' sb-on' : '');
    $subClass = fn(bool $active) => 'sb-sub' . ($active ? ' sb-on' : '');

    $grupData = $u?->bisa('pendaftaran.lihat') || $u?->bisa('santri.lihat') || $u?->bisa('alumni.lihat');
    $grupAkademik = $u?->bisa('akademik.lihat') || $u?->bisa('prestasi.lihat');
    $grupKonten =
        $u?->bisa('jenjang.lihat') ||
        $u?->bisa('berita') ||
        $u?->bisa('galeri') ||
        $u?->bisa('info') ||
        $u?->bisa('struktur');
    $grupSistem = $u?->bisa('konten') || $u?->bisa('pengguna');
@endphp

<style>
    /* ===== SIDEBAR ADMIN ===== */
    .sb-scroll {
        scrollbar-width: thin;
        scrollbar-color: rgba(52, 211, 153, .35) transparent;
    }

    .sb-scroll::-webkit-scrollbar {
        width: 5px;
    }

    .sb-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    .sb-scroll::-webkit-scrollbar-thumb {
        background: rgba(52, 211, 153, .25);
        border-radius: 999px;
    }

    .sb-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(52, 211, 153, .5);
    }

    .sb-label {
        padding: 0 .7rem;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .16em;
        text-transform: uppercase;
        color: rgba(110, 231, 183, .48);
    }

    .sb-item {
        position: relative;
        display: flex;
        align-items: center;
        gap: .7rem;
        padding: .58rem .7rem;
        border-radius: .8rem;
        font-size: 12px;
        font-weight: 600;
        color: rgba(209, 250, 229, .74);
        transition: background-color .18s ease, color .18s ease, box-shadow .18s ease;
    }

    .sb-item:hover {
        background: rgba(255, 255, 255, .07);
        color: #fff;
    }

    .sb-item>i:first-child,
    .sb-item>a>i {
        width: 1.05rem;
        flex-shrink: 0;
        text-align: center;
        font-size: .8rem;
        color: rgba(110, 231, 183, .82);
        transition: color .18s ease;
    }

    .sb-item:hover>i:first-child,
    .sb-item:hover>a>i {
        color: #a7f3d0;
    }

    .sb-item.sb-on {
        background: #fff;
        color: #064e3b;
        box-shadow: 0 10px 22px -12px rgba(0, 0, 0, .7);
    }

    .sb-item.sb-on>i:first-child,
    .sb-item.sb-on i,
    .sb-item.sb-on .sb-chevron {
        color: #059669;
    }

    .sb-item.sb-on .sb-dot {
        opacity: 1;
    }

    .sb-item:not(.sb-on) .sb-chevron:hover {
        background: rgba(255, 255, 255, .12);
        color: #fff;
    }

    .sb-item.sb-on .sb-chevron:hover {
        background: rgba(5, 150, 105, .12);
        color: #047857;
    }

    .sb-item.sb-parent {
        padding-right: .3rem;
    }

    .sb-dot {
        position: absolute;
        top: 50%;
        right: .6rem;
        height: .28rem;
        width: .28rem;
        transform: translateY(-50%);
        border-radius: 999px;
        background: #059669;
        opacity: 0;
        transition: opacity .18s ease;
    }

    .sb-sub {
        display: flex;
        align-items: center;
        gap: .55rem;
        padding: .42rem .6rem;
        border-radius: .6rem;
        font-size: 10.5px;
        font-weight: 600;
        color: rgba(209, 250, 229, .6);
        transition: background-color .18s ease, color .18s ease;
    }

    .sb-sub:hover {
        background: rgba(255, 255, 255, .07);
        color: #fff;
    }

    .sb-sub>i {
        width: .85rem;
        flex-shrink: 0;
        text-align: center;
        font-size: .62rem;
        opacity: .8;
    }

    .sb-sub.sb-on {
        background: rgba(255, 255, 255, .15);
        color: #fff;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .06);
    }

    .sb-sub.sb-on>i {
        color: #6ee7b7;
        opacity: 1;
    }

    .sb-hr {
        margin: .55rem .55rem;
        border-top: 1px solid rgba(255, 255, 255, .09);
    }

    /* Cegah kedipan layout: sidebar desktop tetap tampil sebelum Alpine siap */
    @media (min-width: 768px) {
        .sb-root[x-cloak] {
            display: flex !important;
        }
    }

    @media (max-width: 767px) {
        .sb-item {
            padding-top: .7rem;
            padding-bottom: .7rem;
            font-size: 12.5px;
        }

        .sb-sub {
            padding-top: .55rem;
            padding-bottom: .55rem;
            font-size: 11.5px;
        }
    }
</style>

<!-- OVERLAY SIDEBAR MOBILE -->
<div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
    class="fixed inset-0 z-40 bg-emerald-950/70 backdrop-blur-sm md:hidden" x-transition.opacity></div>

<!-- SIDEBAR -->
<aside x-cloak @keydown.escape.window="sidebarOpen = false"
    :class="{
        'translate-x-0': sidebarOpen,
        '-translate-x-full': !sidebarOpen,
        'md:w-20': sidebarMinimize,
        'md:w-64': !sidebarMinimize
    }"
    class="sb-root fixed inset-y-0 left-0 z-50 flex w-72 max-w-[85vw] flex-col overflow-hidden border-r border-emerald-700/40 bg-gradient-to-b from-emerald-900 via-emerald-950 to-slate-950 text-emerald-50/80 shadow-2xl transition-all duration-300 ease-in-out md:static md:translate-x-0 md:shadow-none">

    <!-- Aksen dekoratif -->
    <div class="pointer-events-none absolute inset-0"
        style="background:
            radial-gradient(38rem 18rem at 15% -5%, rgba(16, 185, 129, .28), transparent 60%),
            radial-gradient(28rem 16rem at 110% 105%, rgba(5, 150, 105, .22), transparent 65%);">
    </div>

    <!-- HEADER: LOGO + TOGGLE -->
    <div class="relative z-10 flex h-20 shrink-0 items-center gap-2 border-b border-white/10 px-3 md:px-4">
        <a href="{{ route($dashRoute) }}"
            class="flex min-w-0 flex-1 items-center gap-3 rounded-xl outline-none transition focus-visible:ring-2 focus-visible:ring-emerald-400/60"
            title="Babul Hasanah — {{ $roleLabel }}">
            <span
                class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-white/95 p-1.5 shadow-lg shadow-emerald-950/50 ring-1 ring-white/40">
                <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Logo Babul Hasanah"
                    class="h-full w-full object-contain">
            </span>
            <span x-show="!sidebarMinimize" x-transition.opacity.duration.200ms class="min-w-0 whitespace-nowrap">
                <span class="block truncate text-[13px] font-black leading-tight tracking-tight text-white">
                    Babul Hasanah
                </span>
                <span class="mt-1 block truncate text-[9px] font-bold uppercase tracking-[0.14em] text-amber-300/90">
                    {{ $roleLabel }}
                </span>
            </span>
        </a>

        <!-- Tutup (mobile) -->
        <button type="button" @click="sidebarOpen = false" aria-label="Tutup menu"
            class="grid h-9 w-9 shrink-0 place-items-center rounded-xl text-emerald-100/70 transition hover:bg-white/10 hover:text-white md:hidden">
            <i class="fa-solid fa-xmark text-base"></i>
        </button>

        <!-- Ringkas (desktop) -->
        <button type="button" x-show="!sidebarMinimize" @click="sidebarMinimize = true" aria-label="Ringkas menu"
            class="hidden h-9 w-9 shrink-0 place-items-center rounded-xl text-emerald-100/70 transition hover:bg-white/10 hover:text-white md:grid">
            <i class="fa-solid fa-angles-left text-[11px]"></i>
        </button>
    </div>

    <!-- NAVIGASI -->
    <nav class="sb-scroll relative z-10 flex-1 overflow-y-auto overscroll-contain px-3 py-3"
        aria-label="Navigasi admin">

        {{-- ============ UTAMA ============ --}}
        <div class="mb-1" x-show="!sidebarMinimize">
            <p class="sb-label">Utama</p>
        </div>
        <div class="mb-2" x-show="sidebarMinimize">
            <div class="sb-hr"></div>
        </div>

        <a href="{{ route($dashRoute) }}" @click="sidebarOpen = false"
            class="{{ $itemClass($dashboardAktif) }}" @if ($dashboardAktif) aria-current="page" @endif
            title="Dashboard">
            <i class="fa-solid fa-chart-pie"></i>
            <span x-show="!sidebarMinimize" class="truncate whitespace-nowrap">Dashboard</span>
            <span class="sb-dot" x-show="!sidebarMinimize" aria-hidden="true"></span>
        </a>

        @if ($isGlobal && $u?->bisa('jenjang.lihat'))
            <a href="{{ route('admin.dashboard.jenjang') }}" @click="sidebarOpen = false"
                class="{{ $itemClass($dashboardJenjangAktif) }}"
                @if ($dashboardJenjangAktif) aria-current="page" @endif title="Dashboard Jenjang">
                <i class="fa-solid fa-layer-group"></i>
                <span x-show="!sidebarMinimize" class="truncate whitespace-nowrap">Dashboard Jenjang</span>
                <span class="sb-dot" x-show="!sidebarMinimize" aria-hidden="true"></span>
            </a>
        @endif

        {{-- ============ PENDAFTARAN & DATA ============ --}}
        @if ($grupData)
            <div class="mb-1 mt-4" x-show="!sidebarMinimize">
                <p class="sb-label">Pendaftaran &amp; Data</p>
            </div>
            <div class="mb-2" x-show="sidebarMinimize">
                <div class="sb-hr"></div>
            </div>

            @if ($u?->bisa('pendaftaran.lihat'))
                <a href="{{ route('admin.pendaftaran.index') }}" @click="sidebarOpen = false"
                    class="{{ $itemClass(request()->routeIs('admin.pendaftaran.*')) }}"
                    @if (request()->routeIs('admin.pendaftaran.*')) aria-current="page" @endif title="Pendaftaran">
                    <i class="fa-solid fa-address-book"></i>
                    <span x-show="!sidebarMinimize" class="truncate whitespace-nowrap">Pendaftaran</span>
                    <span class="sb-dot" x-show="!sidebarMinimize" aria-hidden="true"></span>
                </a>
            @endif

            @if ($u?->bisa('santri.lihat'))
                <a href="{{ route('admin.santri.index') }}" @click="sidebarOpen = false"
                    class="{{ $itemClass(request()->routeIs('admin.santri.*')) }}"
                    @if (request()->routeIs('admin.santri.*')) aria-current="page" @endif title="Data Santri">
                    <i class="fa-solid fa-people-group"></i>
                    <span x-show="!sidebarMinimize" class="truncate whitespace-nowrap">Data Santri</span>
                    <span class="sb-dot" x-show="!sidebarMinimize" aria-hidden="true"></span>
                </a>
            @endif

            @if ($u?->bisa('alumni.lihat'))
                <a href="{{ route('admin.alumni.index') }}" @click="sidebarOpen = false"
                    class="{{ $itemClass(request()->routeIs('admin.alumni.*')) }}"
                    @if (request()->routeIs('admin.alumni.*')) aria-current="page" @endif title="Data Alumni">
                    <i class="fa-solid fa-user-graduate"></i>
                    <span x-show="!sidebarMinimize" class="truncate whitespace-nowrap">Data Alumni</span>
                    <span class="sb-dot" x-show="!sidebarMinimize" aria-hidden="true"></span>
                </a>
            @endif
        @endif

        {{-- ============ AKADEMIK & PRESTASI ============ --}}
        @if ($grupAkademik)
            <div class="mb-1 mt-4" x-show="!sidebarMinimize">
                <p class="sb-label">Akademik &amp; Prestasi</p>
            </div>
            <div class="mb-2" x-show="sidebarMinimize">
                <div class="sb-hr"></div>
            </div>

            @if ($u?->bisa('akademik.lihat'))
                <div x-data="{ akOpen: {{ $akademikAktif ? 'true' : 'false' }} }">
                    <div class="{{ $itemClass($akademikAktif) }} sb-parent">
                        <a href="{{ route('admin.akademik.nilai.index') }}" @click="sidebarOpen = false"
                            class="flex min-w-0 flex-1 items-center gap-3 outline-none" title="Akademik">
                            <i class="fa-solid fa-book-open-reader"></i>
                            <span x-show="!sidebarMinimize" class="truncate whitespace-nowrap">Akademik</span>
                        </a>
                        <button type="button" x-show="!sidebarMinimize" @click="akOpen = !akOpen"
                            :aria-expanded="akOpen" aria-label="Buka menu akademik"
                            class="sb-chevron grid h-7 w-7 shrink-0 place-items-center rounded-lg text-emerald-100/60 transition">
                            <i class="fa-solid fa-chevron-down text-[9px] transition-transform duration-200"
                                :class="akOpen && 'rotate-180'"></i>
                        </button>
                    </div>

                    <div x-show="akOpen && !sidebarMinimize" x-transition.opacity.duration.150ms
                        class="mb-1 ml-4 mt-1 space-y-0.5 border-l border-white/10 pl-2">
                        <a href="{{ route('admin.akademik.nilai.index') }}" @click="sidebarOpen = false"
                            class="{{ $subClass(request()->routeIs('admin.akademik.nilai.*')) }}">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span class="truncate">Nilai Santri</span>
                        </a>

                        <a href="{{ route('admin.akademik.transkrip.index') }}" @click="sidebarOpen = false"
                            class="{{ $subClass(request()->routeIs('admin.akademik.transkrip.*')) }}">
                            <i class="fa-solid fa-file-lines"></i>
                            <span class="truncate">Transkrip Nilai</span>
                        </a>

                        @if ($u?->bisa('akademik.mapel'))
                            <a href="{{ route('admin.akademik.mapel.index') }}" @click="sidebarOpen = false"
                                class="{{ $subClass(request()->routeIs('admin.akademik.mapel.*')) }}">
                                <i class="fa-solid fa-list-check"></i>
                                <span class="truncate">Mata Pelajaran</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            @if ($u?->bisa('prestasi.lihat'))
                <a href="{{ route('admin.prestasi.index') }}" @click="sidebarOpen = false"
                    class="{{ $itemClass($prestasiAktif) }}" @if ($prestasiAktif) aria-current="page" @endif
                    title="Prestasi">
                    <i class="fa-solid fa-trophy"></i>
                    <span x-show="!sidebarMinimize" class="truncate whitespace-nowrap">Prestasi</span>
                    <span class="sb-dot" x-show="!sidebarMinimize" aria-hidden="true"></span>
                </a>

                @if (\App\Support\Akses::jenjangInfo($u) || $u?->bisa('prestasi.ubah'))
                    <div x-show="!sidebarMinimize" class="ml-4 mt-0.5 border-l border-white/10 pl-2">
                        <a href="{{ route('admin.dashboard.jenjang.prestasi.form') }}" @click="sidebarOpen = false"
                            class="{{ $subClass(request()->routeIs('admin.dashboard.jenjang.prestasi.form')) }}">
                            <i class="fa-solid fa-plus"></i>
                            <span class="truncate">Input Prestasi</span>
                        </a>
                    </div>
                @endif
            @endif
        @endif

        {{-- ============ KONTEN & INFORMASI ============ --}}
        @if ($grupKonten)
            <div class="mb-1 mt-4" x-show="!sidebarMinimize">
                <p class="sb-label">Konten &amp; Informasi</p>
            </div>
            <div class="mb-2" x-show="sidebarMinimize">
                <div class="sb-hr"></div>
            </div>

            @if ($u?->bisa('jenjang.lihat'))
                @php
                    $jenjangSendiri = \App\Support\Akses::jenjangInfo($u);
                @endphp

                @if ($jenjangSendiri)
                    {{-- Admin Jenjang: satu jenjang saja, tanpa daftar 7 jenjang. --}}
                    <a href="{{ route('admin.jenjang.show', $jenjangSendiri['slug']) }}" @click="sidebarOpen = false"
                        class="{{ $itemClass($jenjangAktif) }}"
                        @if ($jenjangAktif) aria-current="page" @endif
                        title="Jenjang {{ $jenjangSendiri['nama'] }}">
                        <i class="fa-solid fa-layer-group"></i>
                        <span x-show="!sidebarMinimize" class="truncate whitespace-nowrap">Jenjang
                            {{ $jenjangSendiri['kode'] }}</span>
                        <span class="sb-dot" x-show="!sidebarMinimize" aria-hidden="true"></span>
                    </a>
                @else
                    <div x-data="{ jenjangOpen: {{ $jenjangAktif ? 'true' : 'false' }} }">
                        <div class="{{ $itemClass($jenjangAktif) }} sb-parent">
                            <a href="{{ route('admin.jenjang.index') }}" @click="sidebarOpen = false"
                                class="flex min-w-0 flex-1 items-center gap-3 outline-none" title="Jenjang">
                                <i class="fa-solid fa-layer-group"></i>
                                <span x-show="!sidebarMinimize" class="truncate whitespace-nowrap">Jenjang</span>
                            </a>
                            <button type="button" x-show="!sidebarMinimize" @click="jenjangOpen = !jenjangOpen"
                                :aria-expanded="jenjangOpen" aria-label="Buka daftar jenjang"
                                class="sb-chevron grid h-7 w-7 shrink-0 place-items-center rounded-lg text-emerald-100/60 transition">
                                <i class="fa-solid fa-chevron-down text-[9px] transition-transform duration-200"
                                    :class="jenjangOpen && 'rotate-180'"></i>
                            </button>
                        </div>

                        <div x-show="jenjangOpen && !sidebarMinimize" x-transition.opacity.duration.150ms
                            class="mb-1 ml-4 mt-1 space-y-0.5 border-l border-white/10 pl-2">
                            @foreach (\App\Support\Jenjang::semua() as $j)
                                @php
                                    $aktifJenjang =
                                        request()->routeIs('admin.jenjang.show') &&
                                        request()->route('jenjang') === $j['slug'];
                                @endphp
                                <a href="{{ route('admin.jenjang.show', $j['slug']) }}" @click="sidebarOpen = false"
                                    class="{{ $subClass($aktifJenjang) }}">
                                    <span class="h-1.5 w-1.5 shrink-0 rounded-full" style="background: {{ $j['warna'] }}"></span>
                                    <span class="truncate">{{ $j['kode'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            @if ($u?->bisa('berita'))
                <a href="{{ route('admin.berita.index') }}" @click="sidebarOpen = false"
                    class="{{ $itemClass(request()->routeIs('admin.berita.*')) }}"
                    @if (request()->routeIs('admin.berita.*')) aria-current="page" @endif title="Berita">
                    <i class="fa-solid fa-newspaper"></i>
                    <span x-show="!sidebarMinimize" class="truncate whitespace-nowrap">Berita</span>
                    <span class="sb-dot" x-show="!sidebarMinimize" aria-hidden="true"></span>
                </a>
            @endif

            @if ($u?->bisa('galeri'))
                <a href="{{ route('admin.galeri.index') }}" @click="sidebarOpen = false"
                    class="{{ $itemClass(request()->routeIs('admin.galeri.*')) }}"
                    @if (request()->routeIs('admin.galeri.*')) aria-current="page" @endif title="Galeri">
                    <i class="fa-solid fa-photo-film"></i>
                    <span x-show="!sidebarMinimize" class="truncate whitespace-nowrap">Galeri</span>
                    <span class="sb-dot" x-show="!sidebarMinimize" aria-hidden="true"></span>
                </a>
            @endif

            @if ($u?->bisa('info'))
                <a href="{{ route('admin.info.index') }}" @click="sidebarOpen = false"
                    class="{{ $itemClass(request()->routeIs('admin.info.*')) }}"
                    @if (request()->routeIs('admin.info.*')) aria-current="page" @endif title="Info Harian">
                    <i class="fa-solid fa-bullhorn"></i>
                    <span x-show="!sidebarMinimize" class="truncate whitespace-nowrap">Info Harian</span>
                    <span class="sb-dot" x-show="!sidebarMinimize" aria-hidden="true"></span>
                </a>
            @endif

            @if ($u?->bisa('struktur'))
                <a href="{{ route('admin.struktur.index') }}" @click="sidebarOpen = false"
                    class="{{ $itemClass(request()->routeIs('admin.struktur.*')) }}"
                    @if (request()->routeIs('admin.struktur.*')) aria-current="page" @endif title="Struktur">
                    <i class="fa-solid fa-sitemap"></i>
                    <span x-show="!sidebarMinimize" class="truncate whitespace-nowrap">Struktur</span>
                    <span class="sb-dot" x-show="!sidebarMinimize" aria-hidden="true"></span>
                </a>
            @endif
        @endif

        {{-- ============ SISTEM ============ --}}
        @if ($grupSistem)
            <div class="mb-1 mt-4" x-show="!sidebarMinimize">
                <p class="sb-label">Sistem</p>
            </div>
            <div class="mb-2" x-show="sidebarMinimize">
                <div class="sb-hr"></div>
            </div>

            @if ($u?->bisa('konten'))
                <a href="{{ route('admin.konten.index') }}" @click="sidebarOpen = false"
                    class="{{ $itemClass(request()->routeIs('admin.konten.*')) }}"
                    @if (request()->routeIs('admin.konten.*')) aria-current="page" @endif title="Konten">
                    <i class="fa-solid fa-blog"></i>
                    <span x-show="!sidebarMinimize" class="truncate whitespace-nowrap">Konten</span>
                    <span class="sb-dot" x-show="!sidebarMinimize" aria-hidden="true"></span>
                </a>
            @endif

            @if ($u?->bisa('pengguna'))
                @php $menuPengguna = $u?->isSuperAdmin() ? 'Pengguna' : 'Wali Santri'; @endphp
                <a href="{{ route('super.pengguna.index', $u?->isSuperAdmin() ? [] : ['kategori' => 'wali_santri']) }}"
                    @click="sidebarOpen = false" class="{{ $itemClass(request()->routeIs('super.pengguna.*')) }}"
                    @if (request()->routeIs('super.pengguna.*')) aria-current="page" @endif
                    title="{{ $menuPengguna }}">
                    <i class="fa-solid {{ $u?->isSuperAdmin() ? 'fa-users-gear' : 'fa-people-roof' }}"></i>
                    <span x-show="!sidebarMinimize" class="truncate whitespace-nowrap">{{ $menuPengguna }}</span>
                    <span class="sb-dot" x-show="!sidebarMinimize" aria-hidden="true"></span>
                </a>
            @endif
        @endif

        <div class="h-2" aria-hidden="true"></div>
    </nav>

    <!-- FOOTER: PROFIL + KELUAR -->
    <div class="relative z-10 shrink-0 border-t border-white/10 bg-emerald-950/40 p-3 backdrop-blur-sm">
        <a href="{{ route('admin.profil') }}" @click="sidebarOpen = false" title="Pengaturan akun"
            class="mb-2 flex min-w-0 items-center gap-3 rounded-xl p-1.5 transition hover:bg-white/5"
            :class="sidebarMinimize && 'md:justify-center'">
            <img src="{{ $avatarUrl }}" alt="Foto profil {{ $u?->name }}"
                class="h-9 w-9 shrink-0 rounded-full object-cover ring-2 ring-emerald-400/40"
                onerror="this.src='{{ asset('assets/img/logo/logo.png') }}'">
            <span x-show="!sidebarMinimize" class="min-w-0 whitespace-nowrap">
                <span class="block truncate text-[11.5px] font-bold text-white">{{ $u?->name }}</span>
                <span
                    class="mt-0.5 block truncate text-[9px] font-semibold uppercase tracking-[0.12em] text-emerald-300/70">{{ $roleLabel }}</span>
            </span>
        </a>

        <button type="button" x-show="sidebarMinimize" @click="sidebarMinimize = false" title="Perluas sidebar"
            class="mb-2 hidden h-8 w-full place-items-center rounded-xl bg-white/5 text-emerald-200/70 transition hover:bg-white/10 hover:text-white md:grid">
            <i class="fa-solid fa-angles-right text-[10px]"></i>
        </button>

        <button type="button" @click="logoutModalOpen = true; sidebarOpen = false;"
            class="flex w-full items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-[11.5px] font-semibold text-emerald-50/80 transition hover:border-rose-400/30 hover:bg-rose-500/20 hover:text-rose-50"
            :class="sidebarMinimize && 'md:justify-center md:px-0'">
            <i class="fa-solid fa-right-from-bracket w-4 shrink-0 text-center text-[11px]"></i>
            <span x-show="!sidebarMinimize" class="whitespace-nowrap">Keluar</span>
        </button>
    </div>
</aside>
