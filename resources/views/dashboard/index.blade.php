@extends('layouts.admin')

@section('title', 'Dashboard Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-5 pb-6">

        <div
            class="flex items-center justify-between bg-white dark:bg-slate-900 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-300">
            <div class="space-y-0.5">
                <span
                    class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 text-[10px] font-extrabold uppercase tracking-wider border border-emerald-500/20 dark:border-emerald-500/30 mb-1">
                    Tahun Ajaran 2026/2027
                </span>
                <h2 class="text-base sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Dashboard Utama
                </h2>
            </div>
            <div
                class="w-10 h-10 rounded-2xl bg-slate-900 dark:bg-slate-800 text-emerald-400 flex items-center justify-center font-black shadow-md shadow-slate-900/20 border border-slate-800">
                <i class="fa-solid fa-shield-halved text-sm"></i>
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">

            <div
                class="bg-white dark:bg-slate-900 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between group transition-colors duration-300">
                <div class="flex items-center justify-between mb-3">
                    <span
                        class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm sm:text-lg">
                        <i class="fa-solid fa-user-graduate"></i>
                    </span>
                    <span
                        class="text-[9px] sm:text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-full border border-emerald-500/20">
                        +12%
                    </span>
                </div>
                <div>
                    <span
                        class="text-[10px] sm:text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Santri
                        Aktif</span>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-0.5">485</h3>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 font-medium truncate">Putra: 250 | Putri:
                        235</p>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-900 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between group transition-colors duration-300">
                <div class="flex items-center justify-between mb-3">
                    <span
                        class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 flex items-center justify-center text-sm sm:text-lg">
                        <i class="fa-solid fa-file-invoice"></i>
                    </span>
                    <span
                        class="text-[9px] sm:text-[10px] font-bold text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-950/60 px-2 py-0.5 rounded-full border border-sky-500/20">
                        Gel. 1
                    </span>
                </div>
                <div>
                    <span
                        class="text-[10px] sm:text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Pendaftar</span>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-0.5">124</h3>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 font-medium truncate">Menunggu verifikasi
                    </p>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-900 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between group transition-colors duration-300">
                <div class="flex items-center justify-between mb-3">
                    <span
                        class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm sm:text-lg">
                        <i class="fa-solid fa-newspaper"></i>
                    </span>
                    <span
                        class="text-[9px] sm:text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-2 py-0.5 rounded-full border border-amber-500/20">
                        Publik
                    </span>
                </div>
                <div>
                    <span
                        class="text-[10px] sm:text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Berita</span>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-0.5">38</h3>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 font-medium truncate">Artikel aktif bulan
                        ini</p>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-900 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between group transition-colors duration-300">
                <div class="flex items-center justify-between mb-3">
                    <span
                        class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 flex items-center justify-center text-sm sm:text-lg">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </span>
                    <span
                        class="text-[9px] sm:text-[10px] font-bold text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-950/60 px-2 py-0.5 rounded-full border border-purple-500/20">
                        Staf
                    </span>
                </div>
                <div>
                    <span
                        class="text-[10px] sm:text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Asatidz</span>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-0.5">32</h3>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 font-medium truncate">Pengajar & Pembina
                    </p>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div
                class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm lg:col-span-2 flex flex-col justify-between transition-colors duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white">Statistik Tren Pendaftar Santri</h3>
                        <p class="text-[11px] text-slate-400 dark:text-slate-400">Grafik jumlah calon santri baru per bulan
                            (Tahun Ajaran)</p>
                    </div>
                    <div
                        class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-[11px] font-bold px-3 py-1.5 rounded-xl">
                        <span>Periode 2025/2026</span>
                    </div>
                </div>

                <div class="py-4 relative">
                    <div
                        class="absolute inset-0 flex flex-col justify-between pointer-events-none opacity-20 dark:opacity-10">
                        <div class="border-b border-dashed border-slate-400 dark:border-slate-600 w-full"></div>
                        <div class="border-b border-dashed border-slate-400 dark:border-slate-600 w-full"></div>
                        <div class="border-b border-dashed border-slate-400 dark:border-slate-600 w-full"></div>
                        <div class="border-b border-dashed border-slate-400 dark:border-slate-600 w-full"></div>
                    </div>

                    <div class="h-56 w-full flex items-end relative z-10 px-2">
                        <svg class="w-full h-full overflow-visible" viewBox="0 0 600 200" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="emeraldGradient" x1="0" y1="0" x2="0"
                                    y2="1">
                                    <stop offset="0%" stop-color="#10b981" stop-opacity="0.3" />
                                    <stop offset="100%" stop-color="#10b981" stop-opacity="0.0" />
                                </linearGradient>
                            </defs>
                            <path d="M 0,150 Q 100,120 200,90 T 400,60 T 600,30 L 600,200 L 0,200 Z"
                                fill="url(#emeraldGradient)" />
                            <path d="M 0,150 Q 100,120 200,90 T 400,60 T 600,30" fill="none" stroke="#10b981"
                                stroke-width="3" stroke-linecap="round" />
                            <circle cx="0" cy="150" r="4" fill="#10b981" />
                            <circle cx="120" cy="125" r="4" fill="#10b981" />
                            <circle cx="240" cy="95" r="4" fill="#10b981" />
                            <circle cx="360" cy="70" r="4" fill="#10b981" />
                            <circle cx="480" cy="45" r="4" fill="#10b981" />
                            <circle cx="600" cy="30" r="4" fill="#10b981" />
                        </svg>
                    </div>

                    <div
                        class="grid grid-cols-6 text-center text-[10px] font-bold text-slate-400 dark:text-slate-500 mt-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span>Nov</span>
                        <span>Des</span>
                        <span>Jan</span>
                        <span>Feb</span>
                        <span>Mar</span>
                        <span>Apr</span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                        <span class="text-slate-600 dark:text-slate-300 font-semibold text-[11px]">Jumlah Pendaftar
                            Masuk</span>
                    </div>
                    <span class="text-slate-900 dark:text-white font-black text-xs">Total Puncak: 42 Orang/Bulan</span>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between transition-colors duration-300">
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Komposisi Santri Aktif</h3>
                    <p class="text-[11px] text-slate-400 dark:text-slate-400">Distribusi berdasarkan tingkat jenjang
                        pendidikan</p>
                </div>

                <div class="my-6 flex flex-col items-center justify-center">
                    <div class="relative w-36 h-36 rounded-full flex items-center justify-center shadow-inner"
                        style="background: conic-gradient(#10b981 0% 50%, #3b82f6 50% 80%, #f59e0b 80% 100%);">
                        <div
                            class="w-24 h-24 bg-white dark:bg-slate-900 rounded-full flex flex-col items-center justify-center shadow-sm transition-colors duration-300">
                            <span
                                class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider">Total</span>
                            <span class="text-base font-black text-slate-900 dark:text-white">324</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-slate-600 dark:text-slate-400 font-medium text-[11px]">Tingkat Ula /
                                TPQ</span>
                        </div>
                        <span class="font-bold text-slate-900 dark:text-slate-200 text-[11px]">50% (162)</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            <span class="text-slate-600 dark:text-slate-400 font-medium text-[11px]">Tingkat Wustha</span>
                        </div>
                        <span class="font-bold text-slate-900 dark:text-slate-200 text-[11px]">30% (97)</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span class="text-slate-600 dark:text-slate-400 font-medium text-[11px]">Tingkat Ulya</span>
                        </div>
                        <span class="font-bold text-slate-900 dark:text-slate-200 text-[11px]">20% (65)</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            <div
                class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5 sm:p-8 space-y-4 transition-colors duration-300">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-sm sm:text-base font-black text-slate-900 dark:text-white">Pendaftar Terbaru</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Calon santri yang baru mengisi formulir
                        </p>
                    </div>
                    <a href="{{ route('admin.pendaftaran.index') }}"
                        class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 bg-emerald-50 dark:bg-emerald-950/50 px-3 py-1.5 rounded-xl transition">
                        <span>Semua</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                <!-- Mobile View -->
                <div class="space-y-3 sm:hidden">
                    @forelse($pendaftarTerbaru as $pendaftar)
                        <div
                            class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700 flex items-center justify-between">
                            <div class="space-y-0.5">
                                <span class="text-[9px] font-bold text-slate-400">{{ $pendaftar->no_pendaftaran }}</span>
                                <h4 class="text-xs font-black text-slate-900 dark:text-white">
                                    {{ $pendaftar->nama_lengkap }}</h4>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400">{{ $pendaftar->jenjang_tujuan }}
                                    • {{ $pendaftar->asal_sekolah }}</p>
                            </div>

                            @php
                                $statusClass = match (strtolower($pendaftar->status_pendaftaran)) {
                                    'diterima'
                                        => 'bg-emerald-100 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-400',
                                    'ditolak' => 'bg-rose-100 dark:bg-rose-950/50 text-rose-800 dark:text-rose-400',
                                    default => 'bg-amber-100 dark:bg-amber-950/50 text-amber-800 dark:text-amber-400',
                                };
                            @endphp
                            <span class="{{ $statusClass }} px-2.5 py-1 rounded-full text-[9px] font-bold">
                                {{ ucfirst($pendaftar->status_pendaftaran) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-xs text-slate-400">Belum ada data pendaftar.</div>
                    @endforelse
                </div>

                <!-- Desktop Table View -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                        <thead
                            class="bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4 rounded-l-xl">Nama Calon Santri</th>
                                <th class="py-3 px-4">Jenjang</th>
                                <th class="py-3 px-4">Asal Sekolah</th>
                                <th class="py-3 px-4 rounded-r-xl">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                            @forelse($pendaftarTerbaru as $pendaftar)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900 dark:text-white">
                                            {{ $pendaftar->nama_lengkap }}</div>
                                        <div class="text-[10px] text-slate-400">ID: {{ $pendaftar->no_pendaftaran }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300">
                                        {{ $pendaftar->jenjang_tujuan }}</td>
                                    <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400">
                                        {{ $pendaftar->asal_sekolah }}</td>
                                    <td class="py-3.5 px-4">
                                        @php
                                            $badgeClass = match (strtolower($pendaftar->status_pendaftaran)) {
                                                'diterima'
                                                    => 'bg-emerald-100 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-400',
                                                'ditolak'
                                                    => 'bg-rose-100 dark:bg-rose-950/50 text-rose-800 dark:text-rose-400',
                                                default
                                                    => 'bg-amber-100 dark:bg-amber-950/50 text-amber-800 dark:text-amber-400',
                                            };
                                        @endphp
                                        <span
                                            class="{{ $badgeClass }} px-3 py-1 rounded-full text-[10px] font-bold inline-block">
                                            {{ ucfirst($pendaftar->status_pendaftaran) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-6 text-slate-400 text-xs">Belum ada data
                                        pendaftar terbaru.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5 sm:p-8 flex flex-col justify-between space-y-4 transition-colors duration-300">
                <div>
                    <h3 class="text-sm sm:text-base font-black text-slate-900 dark:text-white mb-0.5">Aksi Cepat</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-4">Pintasan menu operasional sistem</p>

                    <div class="space-y-2.5">
                        <a href="#"
                            class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-emerald-700 dark:hover:text-emerald-400 border border-slate-200/60 dark:border-slate-700 transition group">
                            <div class="flex items-center gap-3">
                                <span
                                    class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 group-hover:bg-emerald-600 group-hover:text-white flex items-center justify-center shadow-sm transition">
                                    <i class="fa-solid fa-user-plus text-xs"></i>
                                </span>
                                <div>
                                    <h4
                                        class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-emerald-700 dark:group-hover:text-emerald-400">
                                        Tambah Data Santri</h4>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Input manual data baru</p>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-slate-400 group-hover:text-emerald-600"></i>
                        </a>

                        <a href="#"
                            class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-emerald-700 dark:hover:text-emerald-400 border border-slate-200/60 dark:border-slate-700 transition group">
                            <div class="flex items-center gap-3">
                                <span
                                    class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 group-hover:bg-emerald-600 group-hover:text-white flex items-center justify-center shadow-sm transition">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </span>
                                <div>
                                    <h4
                                        class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-emerald-700 dark:group-hover:text-emerald-400">
                                        Tulis Berita Baru</h4>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Publikasi info kegiatan</p>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-slate-400 group-hover:text-emerald-600"></i>
                        </a>

                        <a href="#"
                            class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-emerald-700 dark:hover:text-emerald-400 border border-slate-200/60 dark:border-slate-700 transition group">
                            <div class="flex items-center gap-3">
                                <span
                                    class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 group-hover:bg-emerald-600 group-hover:text-white flex items-center justify-center shadow-sm transition">
                                    <i class="fa-solid fa-cloud-arrow-down text-xs"></i>
                                </span>
                                <div>
                                    <h4
                                        class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-emerald-700 dark:group-hover:text-emerald-400">
                                        Rekapitulasi Data</h4>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Unduh laporan Excel/PDF</p>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-slate-400 group-hover:text-emerald-600"></i>
                        </a>
                    </div>
                </div>

                <div
                    class="p-3.5 rounded-2xl bg-gradient-to-br from-emerald-900 to-slate-900 text-white space-y-1.5 shadow-sm">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-emerald-400 text-xs"></i>
                        <h5 class="text-[11px] font-bold tracking-wide">Pusat Bantuan</h5>
                    </div>
                    <p class="text-[10px] text-slate-300 leading-relaxed">
                        Kendala server atau database? Hubungi tim pengembang internal.
                    </p>
                </div>
            </div>

        </div>

    </div>
@endsection
