@extends('layouts.admin')

@section('title', 'Dashboard Jenjang - Pondok Pesantren Babul Hasanah')

@section('content')
    @php
        $pengguna = auth()->user();
        $peranPengguna = \App\Support\Akses::label($pengguna->role);
        $judulHero = match ($peranPengguna) {
            'Super Admin' => 'Dashboard Jenjang Pesantren',
            'Pengasuh / Pengurus' => 'Dashboard Pengasuh',
            'Pembina Asrama' => 'Dashboard Pembina',
            default => 'Dashboard '.$peranPengguna,
        };
        $deskripsiHero = $pengguna->bisa('akademik.ubah')
            ? 'Kelola pendaftaran, data santri, alumni, serta input nilai dan prestasi. Pilih jenjang untuk mempersempit data; tanpa pilihan jenjang data ditampilkan gabungan dari seluruh jenjang.'
            : 'Pantau pendaftaran, data santri, alumni, nilai, dan prestasi per jenjang. Halaman ini bersifat monitoring (hanya baca).';
    @endphp

    <div class="space-y-6">

        @include('dashboard.roles._hero', [
            'badge' => $peranPengguna,
            'ikon' => 'fa-layer-group',
            'judul' => $judulHero,
            'deskripsi' => $deskripsiHero,
            'identitas' => $jenjang ? $jenjang['kode'].' — '.$jenjang['nama'] : ($tanpaJenjang ? 'Belum ditautkan' : 'Semua jenjang'),
        ])

        @include('dashboard.roles._pilih-jenjang')

        @if ($tanpaJenjang)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-xs text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-200">
                <p class="font-black"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Akun belum ditautkan ke jenjang.</p>
                <p class="mt-1 leading-relaxed">Hubungi Super Admin agar akun ini diberi jenjang melalui menu <b>Pengguna</b>. Selama belum diatur, dashboard tidak menampilkan data agar tidak tercampur dengan jenjang lain.</p>
            </div>
        @elseif ($modeAgregat)
            <div class="rounded-2xl border border-sky-200 bg-sky-50 px-5 py-4 text-xs text-sky-800 dark:border-sky-900/50 dark:bg-sky-950/30 dark:text-sky-200">
                <p class="font-black"><i class="fa-solid fa-circle-info mr-1"></i> Mode gabungan seluruh jenjang.</p>
                <p class="mt-1 leading-relaxed">Angka di bawah adalah total 7 jenjang. Pilih salah satu jenjang pada menu di atas untuk melihat data dan mengisi nilai/prestasi jenjang tersebut.</p>
            </div>
        @endif

        {{-- Ringkasan jenjang: menggantikan halaman Pengelolaan Jenjang --}}
        @if ($jenjang)
            <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="h-1.5" style="background: {{ $jenjang['warna'] }}"></div>
                <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex min-w-0 items-start gap-4">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl text-white"
                            style="background: {{ $jenjang['warna'] }}">
                            <i class="fa-solid {{ $jenjang['ikon'] }}"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">{{ $jenjang['kode'] }}</p>
                            <h2 class="text-base font-black text-slate-900 dark:text-white">{{ $jenjang['nama'] }}</h2>
                            <p class="mt-0.5 max-w-2xl text-[11px] leading-relaxed text-slate-500 dark:text-slate-400">
                                {{ $jenjang['deskripsi'] }}</p>
                        </div>
                    </div>
                    <a href="{{ route('jenjang.show', $jenjang['slug']) }}" target="_blank" rel="noopener"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-slate-200 px-3.5 py-2 text-[11px] font-bold text-slate-600 transition hover:border-emerald-500 hover:text-emerald-600 dark:border-slate-700 dark:text-slate-300">
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Halaman publik
                    </a>
                </div>
            </div>
        @elseif ($modeAgregat)
            @php
                $totalSeluruhSantri = array_sum(array_column($rekapJenjang, 'santri'));
            @endphp
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($rekapJenjang as $j)
                    @php
                        $persen = $totalSeluruhSantri > 0 ? round(($j['santri'] / $totalSeluruhSantri) * 100) : 0;
                    @endphp
                    <a href="{{ route('admin.dashboard.jenjang', ['jenjang' => $j['slug']]) }}"
                        class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900">
                        <span class="absolute inset-x-0 top-0 h-1.5" style="background: {{ $j['warna'] }}"></span>
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl text-white"
                                    style="background: {{ $j['warna'] }}">
                                    <i class="fa-solid {{ $j['ikon'] }}"></i>
                                </span>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">{{ $j['kode'] }}</p>
                                    <h3 class="truncate text-sm font-black leading-tight text-slate-900 dark:text-white"
                                        title="{{ $j['nama'] }}">{{ $j['nama'] }}</h3>
                                </div>
                            </div>
                            <i class="fa-solid fa-arrow-up-right-from-square mt-1 text-xs text-slate-300 transition group-hover:text-emerald-500"></i>
                        </div>
                        <div class="mt-4 grid grid-cols-3 gap-2">
                            <div class="rounded-xl bg-slate-50 px-2.5 py-2 text-center dark:bg-slate-800/60">
                                <p class="text-base font-black leading-none text-slate-900 dark:text-white">{{ number_format($j['santri'], 0, ',', '.') }}</p>
                                <p class="mt-1 text-[9px] font-bold uppercase tracking-wide text-slate-400">Santri</p>
                            </div>
                            <div class="rounded-xl bg-slate-50 px-2.5 py-2 text-center dark:bg-slate-800/60">
                                <p class="text-base font-black leading-none text-slate-900 dark:text-white">{{ number_format($j['pendaftar'], 0, ',', '.') }}</p>
                                <p class="mt-1 text-[9px] font-bold uppercase tracking-wide text-slate-400">Pendaftar</p>
                            </div>
                            <div class="rounded-xl bg-slate-50 px-2.5 py-2 text-center dark:bg-slate-800/60">
                                <p class="text-base font-black leading-none text-slate-900 dark:text-white">{{ number_format($j['alumni'], 0, ',', '.') }}</p>
                                <p class="mt-1 text-[9px] font-bold uppercase tracking-wide text-slate-400">Alumni</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="h-1.5 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                                <div class="h-full rounded-full transition-all" style="width: {{ $persen }}%; background: {{ $j['warna'] }}"></div>
                            </div>
                            <div class="mt-1.5 flex items-center justify-between text-[10px] text-slate-400">
                                <span>{{ $persen }}% dari total santri</span>
                                @if ($j['menunggu'] > 0)
                                    <span class="font-bold text-amber-600">{{ $j['menunggu'] }} pendaftar menunggu</span>
                                @else
                                    <span class="font-semibold text-emerald-600">Tidak ada antrean</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white">Data per Jenjang</h3>
                        <p class="text-[11px] text-slate-400">Perbandingan jumlah santri, pendaftar, dan alumni.</p>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">7 Jenjang</span>
                </div>
                <div class="p-4 sm:p-5">
                    <div class="h-72 w-full sm:h-80"><canvas id="chartPerJenjang"></canvas></div>
                </div>
            </div>
        @endif

        @if (! $tanpaJenjang && (count($kelasChart) || count($statusChart)))
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div class="rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                        <h3 class="text-sm font-black text-slate-900 dark:text-white">Santri per Kelas</h3>
                        <p class="text-[11px] text-slate-400">{{ $jenjang ? 'Sebaran kelas jenjang '.$jenjang['kode'] : 'Sebaran seluruh jenjang' }}.</p>
                    </div>
                    <div class="p-4">
                        @if (count($kelasChart))
                            <div class="h-60 w-full"><canvas id="chartKelas"></canvas></div>
                        @else
                            <div class="grid h-60 place-items-center text-center text-xs text-slate-400">
                                Belum ada data kelas.
                            </div>
                        @endif
                    </div>
                </div>
                <div class="rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                        <h3 class="text-sm font-black text-slate-900 dark:text-white">Status Pendaftar</h3>
                        <p class="text-[11px] text-slate-400">Rekap verifikasi pendaftaran.</p>
                    </div>
                    <div class="p-4">
                        @if (count($statusChart))
                            <div class="h-60 w-full"><canvas id="chartStatus"></canvas></div>
                        @else
                            <div class="grid h-60 place-items-center text-center text-xs text-slate-400">
                                Belum ada data pendaftar.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
            @include('dashboard.roles._stat', [
                'label' => 'Santri',
                'nilai' => number_format($totalSantri, 0, ',', '.'),
                'sub' => $jenjang['nama'] ?? 'Seluruh jenjang',
                'ikon' => 'fa-people-group',
                'href' => route('admin.santri.index', $jenjangPilihan ? ['jenjang' => $jenjangPilihan] : []),
            ])
            @include('dashboard.roles._stat', [
                'label' => 'Pendaftar',
                'nilai' => number_format($totalPendaftar, 0, ',', '.'),
                'sub' => $jenjang ? 'Total PPDB jenjang ini' : 'Total PPDB semua jenjang',
                'ikon' => 'fa-address-book',
                'ikonBg' => 'bg-sky-50 text-sky-600 dark:bg-sky-950/50 dark:text-sky-400',
                'href' => route('admin.pendaftaran.index'),
            ])
            @include('dashboard.roles._stat', [
                'label' => 'Menunggu Verifikasi',
                'nilai' => number_format($pendaftarMenunggu, 0, ',', '.'),
                'sub' => 'Perlu ditindaklanjuti',
                'ikon' => 'fa-clock',
                'ikonBg' => 'bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400',
                'href' => route('admin.pendaftaran.index'),
            ])
            @include('dashboard.roles._stat', [
                'label' => 'Alumni',
                'nilai' => number_format($totalAlumni, 0, ',', '.'),
                'sub' => $totalAlumni > 0 ? $alumniPunyaNilai.' punya nilai/transkrip' : 'Riwayat lulusan',
                'ikon' => 'fa-user-graduate',
                'ikonBg' => 'bg-violet-50 text-violet-600 dark:bg-violet-950/50 dark:text-violet-400',
                'href' => route('admin.alumni.index', $jenjangPilihan ? ['jenjang' => $jenjangPilihan] : []),
            ])
            @include('dashboard.roles._stat', [
                'label' => 'Prestasi',
                'nilai' => number_format($totalPrestasi, 0, ',', '.'),
                'sub' => $jenjang ? 'Capaian jenjang ini' : 'Capaian semua jenjang',
                'ikon' => 'fa-trophy',
                'ikonBg' => 'bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400',
                'href' => route('admin.prestasi.index'),
            ])
        </div>

        {{-- Aksi cepat --}}
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-3 flex items-center justify-between gap-3">
                <h2 class="text-sm font-black text-slate-900 dark:text-white">Aksi Cepat</h2>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Sesuai hak akses</span>
            </div>
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
                @if ($bolehNilai)
                    <a href="{{ route('admin.dashboard.jenjang.nilai.form', $jenjangPilihan ? ['jenjang' => $jenjangPilihan] : []) }}"
                        class="flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-3 text-[11px] font-black text-emerald-800 transition hover:bg-emerald-100 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                        <i class="fa-solid fa-pen-to-square"></i> Input Nilai
                    </a>
                @else
                    <a href="{{ route('admin.dashboard.jenjang.nilai.form', $jenjangPilihan ? ['jenjang' => $jenjangPilihan] : []) }}"
                        class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-3 py-3 text-[11px] font-bold text-slate-700 transition hover:border-emerald-200 hover:text-emerald-700 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-200">
                        <i class="fa-solid fa-book-open-reader text-emerald-600"></i> Lihat Nilai
                    </a>
                @endif
                @if ($bolehPrestasi)
                    <a href="{{ route('admin.dashboard.jenjang.prestasi.form', $jenjangPilihan ? ['jenjang' => $jenjangPilihan] : []) }}"
                        class="flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-3 text-[11px] font-black text-amber-800 transition hover:bg-amber-100 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-200">
                        <i class="fa-solid fa-trophy"></i> Input Prestasi
                    </a>
                @else
                    <a href="{{ route('admin.prestasi.index') }}"
                        class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-3 py-3 text-[11px] font-bold text-slate-700 transition hover:border-amber-200 hover:text-amber-700 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-200">
                        <i class="fa-solid fa-medal text-amber-500"></i> Lihat Prestasi
                    </a>
                @endif
                <a href="{{ route('admin.pendaftaran.index') }}"
                    class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-3 py-3 text-[11px] font-bold text-slate-700 transition hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-200">
                    <i class="fa-solid fa-address-book text-emerald-600"></i> Data PPDB
                </a>
                <a href="{{ route('admin.santri.index', $jenjangPilihan ? ['jenjang' => $jenjangPilihan] : []) }}"
                    class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-3 py-3 text-[11px] font-bold text-slate-700 transition hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-200">
                    <i class="fa-solid fa-people-group text-emerald-600"></i> Data Santri
                </a>
                <a href="{{ route('admin.alumni.index', $jenjangPilihan ? ['jenjang' => $jenjangPilihan] : []) }}"
                    class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-3 py-3 text-[11px] font-bold text-slate-700 transition hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-200">
                    <i class="fa-solid fa-user-graduate text-emerald-600"></i> Data Alumni
                </a>
                @if ($jenjang)
                    <a href="{{ route('jenjang.show', $jenjang['slug']) }}" target="_blank" rel="noopener"
                        class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-3 py-3 text-[11px] font-bold text-slate-700 transition hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-200">
                        <i class="fa-solid fa-arrow-up-right-from-square text-emerald-600"></i> Halaman Publik
                    </a>
                @endif
            </div>
        </div>

        {{-- ================= INPUT NILAI & PRESTASI ================= --}}
        @php
            $inputCls = 'w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500';
            $labelCls = 'block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1.5';
            $cardCls = 'rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-4';
        @endphp

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-bold text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300 flex items-start gap-2">
                <i class="fa-solid fa-circle-check mt-0.5"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-bold text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300 flex items-start gap-2">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300 space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="flex items-start gap-2"><i class="fa-solid fa-triangle-exclamation mt-0.5"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Pintasan ke halaman khusus input / daftar nilai --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <a href="{{ route('admin.dashboard.jenjang.nilai.form', $jenjangPilihan ? ['jenjang' => $jenjangPilihan] : []) }}"
                class="group relative overflow-hidden rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50 via-white to-white p-5 shadow-sm transition hover:border-emerald-300 hover:shadow-md dark:border-emerald-500/20 dark:from-emerald-500/10 dark:via-slate-900 dark:to-slate-900">
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-1">
                        <p class="text-[10px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                            {{ $bolehNilai ? 'Halaman Khusus' : 'Daftar Nilai' }}
                        </p>
                        <h2 class="text-sm font-black text-slate-900 dark:text-white">
                            {{ $bolehNilai ? 'Input Nilai Santri' : 'Nilai Santri' }}
                        </h2>
                        <p class="max-w-xs text-[11px] leading-relaxed text-slate-500 dark:text-slate-400">
                            @if ($bolehNilai)
                                Form lebih lengkap dengan unggah rapor PDF massal, ringkasan, panduan, dan daftar nilai terbaru.
                            @else
                                Lihat rekap nilai, daftar nilai terbaru, dan rapor PDF per jenjang.
                            @endif
                        </p>
                    </div>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white transition group-hover:scale-105">
                        <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </div>
            </a>
            <a href="{{ $bolehPrestasi
                ? route('admin.dashboard.jenjang.prestasi.form', $jenjangPilihan ? ['jenjang' => $jenjangPilihan] : [])
                : route('admin.prestasi.index') }}"
                class="group relative overflow-hidden rounded-2xl border border-amber-100 bg-gradient-to-br from-amber-50 via-white to-white p-5 shadow-sm transition hover:border-amber-300 hover:shadow-md dark:border-amber-500/20 dark:from-amber-500/10 dark:via-slate-900 dark:to-slate-900">
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-1">
                        <p class="text-[10px] font-black uppercase tracking-wider text-amber-600 dark:text-amber-400">
                            {{ $bolehPrestasi ? 'Halaman Khusus' : 'Daftar Prestasi' }}
                        </p>
                        <h2 class="text-sm font-black text-slate-900 dark:text-white">
                            {{ $bolehPrestasi ? 'Input Prestasi Santri' : 'Prestasi Santri' }}
                        </h2>
                        <p class="max-w-xs text-[11px] leading-relaxed text-slate-500 dark:text-slate-400">
                            {{ $bolehPrestasi
                                ? 'Catat capaian santri, tampil di /prestasi, portal wali, dan email wali.'
                                : 'Lihat capaian santri yang tampil di halaman publik /prestasi.' }}
                        </p>
                    </div>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-white transition group-hover:scale-105">
                        <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </div>
            </a>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2" id="input-nilai">

            {{-- FORM NILAI --}}
            <div class="{{ $cardCls }}">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-black text-slate-900 dark:text-white">
                            <i class="fa-solid fa-book-open-reader mr-1 text-emerald-600"></i> Input Nilai Santri
                        </h2>
                        <p class="text-[10px] text-slate-400 mt-0.5">
                            Nilai langsung terbit di portal wali santri.
                        </p>
                    </div>
                    <div class="flex shrink-0 flex-col items-end gap-1">
                        <a href="{{ route('admin.dashboard.jenjang.nilai.form', $jenjangPilihan ? ['jenjang' => $jenjangPilihan] : []) }}"
                            class="text-[10px] font-black text-emerald-600 hover:underline whitespace-nowrap">
                            {{ $bolehNilai ? 'Halaman khusus' : 'Daftar nilai' }}
                        </a>
                        @if (auth()->user()->bisa('akademik.lihat'))
                            <a href="{{ route('admin.akademik.nilai.index') }}"
                                class="text-[10px] font-bold text-slate-400 hover:underline whitespace-nowrap">Nilai lengkap</a>
                        @endif
                    </div>
                </div>

                @if (! $jenjang)
                    <div class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-[11px] text-sky-800">
                        Pilih jenjang di menu atas untuk mengisi nilai. Data di bawah menampilkan nilai terbaru
                        @if ($modeAgregat) dari seluruh jenjang @endif.
                    </div>
                @elseif (! $bolehNilai)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[11px] text-slate-600 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-300">
                        <i class="fa-solid fa-eye mr-1"></i> Mode hanya baca. Form input nilai hanya untuk Super Admin dan Admin Jenjang.
                    </div>
                @elseif (! $akademikSiap)
                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-[11px] text-amber-800">
                        Modul akademik belum siap. Jalankan migrasi akademik terlebih dahulu.
                    </div>
                @elseif ($santriList->isEmpty())
                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-[11px] text-amber-800">
                        Belum ada data santri pada jenjang ini. Tambahkan atau verifikasi data santri terlebih dahulu.
                    </div>
                @elseif ($mapelList->isEmpty())
                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-[11px] text-amber-800">
                        Belum ada mata pelajaran aktif untuk jenjang ini.
                        @if ($bolehNilai)
                            <a href="{{ route('admin.akademik.mapel.index') }}" class="underline font-bold">Kelola mata pelajaran</a>.
                        @endif
                    </div>
                @else
                    <form method="POST" action="{{ route('admin.dashboard.jenjang.nilai') }}" class="space-y-3">
                        @csrf
                        <input type="hidden" name="jenjang" value="{{ $jenjangPilihan }}">

                        <div>
                            <label for="nilai_santri_id" class="{{ $labelCls }}">Santri</label>
                            <select id="nilai_santri_id" name="santri_id" required class="{{ $inputCls }}">
                                <option value="" disabled @selected(old('santri_id') === null)>Pilih santri</option>
                                @foreach ($santriList as $s)
                                    <option value="{{ $s->id }}" @selected((string) old('santri_id') === (string) $s->id)>
                                        {{ $s->nama_lengkap }} — Kelas {{ $s->kelas ?: '-' }} ({{ $s->nomor_induk ?: 'tanpa induk' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="nilai_mapel" class="{{ $labelCls }}">Mata Pelajaran</label>
                            <select id="nilai_mapel" name="mata_pelajaran_id" required class="{{ $inputCls }}">
                                <option value="" disabled @selected(old('mata_pelajaran_id') === null)>Pilih mata pelajaran</option>
                                @foreach ($mapelList as $mapel)
                                    <option value="{{ $mapel->id }}" @selected((string) old('mata_pelajaran_id') === (string) $mapel->id)>
                                        {{ $mapel->kode ? $mapel->kode.' — ' : '' }}{{ $mapel->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="nilai_tahun" class="{{ $labelCls }}">Tahun Ajaran</label>
                                <select id="nilai_tahun" name="tahun_ajaran" required class="{{ $inputCls }}">
                                    @foreach ($tahunPilihan as $tahun)
                                        <option value="{{ $tahun }}" @selected(old('tahun_ajaran', $tahunSekarang) === $tahun)>{{ $tahun }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="nilai_semester" class="{{ $labelCls }}">Semester</label>
                                <select id="nilai_semester" name="semester" required class="{{ $inputCls }}">
                                    @foreach ($semesterList as $kunci => $label)
                                        <option value="{{ $kunci }}" @selected(old('semester', $semesterSekarang) === $kunci)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="nilai_kategori" class="{{ $labelCls }}">Kategori</label>
                                <select id="nilai_kategori" name="kategori" required class="{{ $inputCls }}">
                                    @foreach ($kategoriNilai as $kunci => $label)
                                        <option value="{{ $kunci }}" @selected(old('kategori', 'tugas') === $kunci)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="nilai_angka" class="{{ $labelCls }}">Nilai (0–100)</label>
                                <input id="nilai_angka" type="number" name="nilai" min="0" max="100" step="0.01"
                                    value="{{ old('nilai') }}" required class="{{ $inputCls }}" placeholder="Contoh: 85">
                            </div>
                        </div>

                        <div>
                            <label for="nilai_catatan" class="{{ $labelCls }}">Catatan (opsional)</label>
                            <input id="nilai_catatan" type="text" name="catatan" maxlength="255"
                                value="{{ old('catatan') }}" class="{{ $inputCls }}" placeholder="Catatan untuk wali santri">
                        </div>

                        <button type="submit"
                            class="w-full rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 text-xs font-black text-white transition inline-flex items-center justify-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan & Terbitkan Nilai
                        </button>
                    </form>
                @endif

                @if ($nilaiTerbaru->isNotEmpty())
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-3">
                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-2">Nilai terbaru</p>
                        <div class="space-y-2">
                            @foreach ($nilaiTerbaru as $n)
                                <div class="flex items-center justify-between gap-3 text-[11px]">
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-700 dark:text-slate-200 truncate">{{ $n->santri?->nama_lengkap ?? '-' }}</p>
                                        <p class="text-slate-400 truncate">{{ $n->mataPelajaran?->nama ?? '-' }} · {{ ucfirst($n->kategori) }}</p>
                                    </div>
                                    <span class="shrink-0 rounded-lg bg-emerald-50 px-2 py-1 font-black text-emerald-700">{{ $n->nilai }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- FORM PRESTASI --}}
            <div class="{{ $cardCls }}">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-black text-slate-900 dark:text-white">
                            <i class="fa-solid fa-trophy mr-1 text-amber-500"></i> Input Prestasi Santri
                        </h2>
                        <p class="text-[10px] text-slate-400 mt-0.5">
                            Tampil di halaman /prestasi dan portal wali santri.
                        </p>
                    </div>
                    <div class="flex shrink-0 flex-col items-end gap-1">
                        <a href="{{ route('admin.dashboard.jenjang.prestasi.form', $jenjangPilihan ? ['jenjang' => $jenjangPilihan] : []) }}"
                            class="text-[10px] font-black text-amber-600 hover:underline whitespace-nowrap">
                            {{ $bolehPrestasi ? 'Halaman khusus' : 'Daftar prestasi' }}
                        </a>
                        <a href="{{ route('admin.prestasi.index') }}"
                            class="text-[10px] font-bold text-slate-400 hover:underline whitespace-nowrap">Kelola prestasi</a>
                    </div>
                </div>

                @if (! $jenjang)
                    <div class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-[11px] text-sky-800">
                        Pilih jenjang di menu atas untuk mencatat prestasi. Daftar di bawah menampilkan prestasi terbaru
                        @if ($modeAgregat) dari seluruh jenjang @endif.
                    </div>
                @elseif (! $bolehPrestasi)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[11px] text-slate-600 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-300">
                        <i class="fa-solid fa-eye mr-1"></i> Mode hanya baca. Form input prestasi hanya untuk Super Admin dan Admin Jenjang.
                    </div>
                @elseif (! $prestasiSiap)
                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-[11px] text-amber-800">
                        Kolom penghubung prestasi belum tersedia. Jalankan <b>php artisan migrate</b> terlebih dahulu.
                    </div>
                @elseif ($santriList->isEmpty())
                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-[11px] text-amber-800">
                        Belum ada data santri pada jenjang ini. Tambahkan atau verifikasi data santri terlebih dahulu.
                    </div>
                @else
                    <form method="POST" action="{{ route('admin.dashboard.jenjang.prestasi') }}" class="space-y-3">
                        @csrf

                        <div>
                            <label for="prestasi_santri_id" class="{{ $labelCls }}">Santri</label>
                            <select id="prestasi_santri_id" name="santri_id" required class="{{ $inputCls }}">
                                <option value="" disabled @selected(old('santri_id') === null)>Pilih santri</option>
                                @foreach ($santriList as $s)
                                    <option value="{{ $s->id }}" @selected((string) old('santri_id') === (string) $s->id)>
                                        {{ $s->nama_lengkap }} — Kelas {{ $s->kelas ?: '-' }} ({{ $s->nomor_induk ?: 'tanpa induk' }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-slate-400 mt-1">
                                Nama & kelas diambil otomatis dari data santri. Notifikasi email dikirim ke akun wali yang tertaut.
                            </p>
                        </div>

                        <div>
                            <label for="prestasi_judul" class="{{ $labelCls }}">Nama Lomba / Capaian</label>
                            <input id="prestasi_judul" type="text" name="judul" maxlength="150" required
                                value="{{ old('judul') }}" class="{{ $inputCls }}" placeholder="Contoh: Musabaqah Hifzhil Qur'an 30 Juz">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="prestasi_kategori" class="{{ $labelCls }}">Bidang Lomba</label>
                                <input id="prestasi_kategori" type="text" name="kategori" list="kategori-prestasi"
                                    maxlength="60" required value="{{ old('kategori') }}" class="{{ $inputCls }}" placeholder="Contoh: Tahfidz">
                                <datalist id="kategori-prestasi">
                                    @foreach ($kategoriPrestasi as $k)
                                        <option value="{{ $k }}"></option>
                                    @endforeach
                                    <option value="Tahfidz"></option>
                                    <option value="Tilawah"></option>
                                    <option value="Pidato"></option>
                                    <option value="Kaligrafi"></option>
                                    <option value="Olimpiade"></option>
                                </datalist>
                            </div>
                            <div>
                                <label for="prestasi_tingkat" class="{{ $labelCls }}">Tingkat</label>
                                <select id="prestasi_tingkat" name="tingkat" required class="{{ $inputCls }}">
                                    <option value="" disabled @selected(old('tingkat') === null)>Pilih tingkat</option>
                                    @foreach ($tingkatPrestasi as $t)
                                        <option value="{{ $t }}" @selected(old('tingkat') === $t)>{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="prestasi_wilayah" class="{{ $labelCls }}">Lokasi Lomba</label>
                                <input id="prestasi_wilayah" type="text" name="wilayah" maxlength="100" required
                                    value="{{ old('wilayah') }}" class="{{ $inputCls }}" placeholder="Contoh: Medan">
                            </div>
                            <div>
                                <label for="prestasi_tahun" class="{{ $labelCls }}">Tahun</label>
                                <input id="prestasi_tahun" type="number" name="tahun" min="2000" max="{{ date('Y') + 1 }}"
                                    value="{{ old('tahun', date('Y')) }}" required class="{{ $inputCls }}">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="prestasi_peringkat" class="{{ $labelCls }}">Peringkat</label>
                                <input id="prestasi_peringkat" type="number" name="peringkat" min="0" max="99"
                                    value="{{ old('peringkat', 1) }}" required class="{{ $inputCls }}">
                                <p class="text-[10px] text-slate-400 mt-1">1–3 untuk juara, 0 untuk penghargaan.</p>
                            </div>
                            <div>
                                <label for="prestasi_label" class="{{ $labelCls }}">Label Kartu (opsional)</label>
                                <input id="prestasi_label" type="text" name="label" maxlength="40"
                                    value="{{ old('label') }}" class="{{ $inputCls }}" placeholder="Contoh: Juara 1">
                            </div>
                        </div>

                        <div>
                            <label for="prestasi_deskripsi" class="{{ $labelCls }}">Deskripsi (opsional)</label>
                            <textarea id="prestasi_deskripsi" name="deskripsi" rows="2" maxlength="1000"
                                class="{{ $inputCls }}" placeholder="Keterangan singkat prestasi">{{ old('deskripsi') }}</textarea>
                        </div>

                        <button type="submit"
                            class="w-full rounded-xl bg-amber-500 hover:bg-amber-600 px-4 py-2.5 text-xs font-black text-white transition inline-flex items-center justify-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan & Kirim ke Wali
                        </button>
                    </form>
                @endif

                @if ($prestasiTerbaru->isNotEmpty())
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-3">
                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-2">Prestasi terbaru</p>
                        <div class="space-y-2">
                            @foreach ($prestasiTerbaru as $p)
                                <div class="flex items-center justify-between gap-3 text-[11px]">
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-700 dark:text-slate-200 truncate">{{ $p->judul }}</p>
                                        <p class="text-slate-400 truncate">{{ $p->nama }} · {{ $p->tingkat }}</p>
                                    </div>
                                    <span class="shrink-0 rounded-lg bg-amber-50 px-2 py-1 font-black text-amber-700">{{ $p->label }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            {{-- Pendaftar terbaru --}}
            <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                    <div>
                        <h2 class="text-sm font-black text-slate-900 dark:text-white">Pendaftar Terbaru</h2>
                        <p class="text-[10px] text-slate-400">{{ $jenjang ? 'Jenjang '.$jenjang['nama'] : 'Seluruh jenjang' }}</p>
                    </div>
                    <a href="{{ route('admin.pendaftaran.index') }}" class="text-[10px] font-bold text-emerald-600 hover:underline">Lihat semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-400 dark:bg-slate-800/50">
                                <th class="px-5 py-3 font-extrabold">Nama</th>
                                <th class="px-5 py-3 font-extrabold">Status</th>
                                <th class="px-5 py-3 font-extrabold">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse ($pendaftarTerbaru as $p)
                                @php
                                    $status = strtolower((string) ($p->status_pendaftaran ?? 'menunggu'));
                                    $statusClass = match (true) {
                                        in_array($status, ['diterima', 'lulus'], true) => 'bg-emerald-50 text-emerald-700',
                                        in_array($status, ['ditolak', 'gagal'], true) => 'bg-red-50 text-red-600',
                                        default => 'bg-amber-50 text-amber-700',
                                    };
                                @endphp
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                    <td class="px-5 py-3 font-bold text-slate-700 dark:text-slate-200">{{ $p->nama_lengkap ?? '-' }}</td>
                                    <td class="px-5 py-3">
                                        <span class="rounded-lg px-2 py-1 text-[10px] font-bold {{ $statusClass }}">{{ ucfirst($status ?: 'menunggu') }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-slate-500">{{ $p->created_at?->translatedFormat('d M Y') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-5 py-8 text-center text-[11px] text-slate-400">Belum ada pendaftar untuk jenjang ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Santri terbaru --}}
            <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                    <div>
                        <h2 class="text-sm font-black text-slate-900 dark:text-white">Santri Terbaru</h2>
                        <p class="text-[10px] text-slate-400">{{ $jenjang ? '5 data terakhir jenjang ini' : '5 data terakhir seluruh jenjang' }}</p>
                    </div>
                    <a href="{{ route('admin.santri.index', $jenjangPilihan ? ['jenjang' => $jenjangPilihan] : []) }}" class="text-[10px] font-bold text-emerald-600 hover:underline">Lihat semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-400 dark:bg-slate-800/50">
                                <th class="px-5 py-3 font-extrabold">Nama</th>
                                <th class="px-5 py-3 font-extrabold">Kelas</th>
                                <th class="px-5 py-3 font-extrabold">Induk</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse ($santriTerbaru as $s)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                    <td class="px-5 py-3 font-bold text-slate-700 dark:text-slate-200">
                                        <a href="{{ route('admin.santri.detail', $s->id) }}" class="hover:text-emerald-600">{{ $s->nama_lengkap ?? '-' }}</a>
                                    </td>
                                    <td class="px-5 py-3 text-slate-500">{{ $s->kelas ?? '-' }}</td>
                                    <td class="px-5 py-3 text-slate-500">{{ $s->nomor_induk ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-5 py-8 text-center text-[11px] text-slate-400">Belum ada data santri.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= DATA ALUMNI JENJANG ================= --}}
        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                <div>
                    <h2 class="text-sm font-black text-slate-900 dark:text-white">Data Alumni</h2>
                    <p class="text-[10px] text-slate-400">
                        {{ $jenjang ? 'Lulusan jenjang '.$jenjang['kode'] : 'Lulusan seluruh jenjang' }} ·
                        {{ number_format($totalAlumni, 0, ',', '.') }} alumni
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('alumni.index') }}" target="_blank" rel="noopener"
                        class="text-[10px] font-bold text-slate-400 hover:underline">Lihat publik</a>
                    <a href="{{ route('admin.alumni.index', $jenjangPilihan ? ['jenjang' => $jenjangPilihan] : []) }}"
                        class="text-[10px] font-bold text-emerald-600 hover:underline">Kelola alumni</a>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2 px-5 pt-4">
                <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2.5 text-center dark:border-slate-800 dark:bg-slate-800/40">
                    <p class="text-base font-black text-slate-900 dark:text-white">{{ number_format($totalAlumni, 0, ',', '.') }}</p>
                    <p class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Total alumni</p>
                </div>
                <div class="rounded-xl border border-emerald-100 bg-emerald-50/60 px-3 py-2.5 text-center dark:border-emerald-500/20 dark:bg-emerald-500/10">
                    <p class="text-base font-black text-emerald-700 dark:text-emerald-300">{{ number_format($alumniPunyaNilai, 0, ',', '.') }}</p>
                    <p class="text-[9px] font-bold uppercase tracking-wide text-emerald-600/80 dark:text-emerald-400/80">Punya nilai</p>
                </div>
                <div class="rounded-xl border border-amber-100 bg-amber-50/60 px-3 py-2.5 text-center dark:border-amber-500/20 dark:bg-amber-500/10">
                    <p class="text-base font-black text-amber-700 dark:text-amber-300">{{ number_format($alumniBelumNilai, 0, ',', '.') }}</p>
                    <p class="text-[9px] font-bold uppercase tracking-wide text-amber-600/80 dark:text-amber-400/80">Belum ada nilai</p>
                </div>
            </div>

            <div class="overflow-x-auto px-1 pb-1 pt-3">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-400 dark:bg-slate-800/50">
                            <th class="px-4 py-3 font-extrabold">Nama</th>
                            <th class="px-4 py-3 font-extrabold">Jenjang</th>
                            <th class="px-4 py-3 font-extrabold">Lulus</th>
                            <th class="px-4 py-3 font-extrabold">Kegiatan</th>
                            <th class="px-4 py-3 font-extrabold text-center">Status</th>
                            <th class="px-4 py-3 font-extrabold text-center">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($alumniTerbaru as $a)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.alumni.show', $a) }}"
                                        class="font-bold text-slate-700 hover:text-emerald-600 dark:text-slate-200">
                                        {{ $a->nama }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-slate-500">{{ \App\Support\Jenjang::label($a->jenjang) ?: '-' }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $a->tahun_lulus ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    @if (filled($a->kegiatan))
                                        <span class="rounded-lg bg-slate-100 px-2 py-1 text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                            {{ $a->kegiatan }}
                                        </span>
                                    @else
                                        <span class="text-[10px] text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @include('dashboard.alumni._status', ['alumni' => $a])
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($nilaiSiap && $a->punyaNilai())
                                        <a href="{{ route('admin.alumni.show', $a) }}"
                                            class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2 py-1 text-[10px] font-bold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                                            <i class="fa-solid fa-file-pdf"></i> Ada
                                        </a>
                                    @elseif ($nilaiSiap && $bolehAlumni)
                                        <a href="{{ route('admin.alumni.show', $a) }}"
                                            class="inline-flex items-center gap-1 rounded-lg bg-amber-50 px-2 py-1 text-[10px] font-bold text-amber-700 hover:bg-amber-100 dark:bg-amber-500/10 dark:text-amber-300">
                                            <i class="fa-solid fa-upload"></i> Unggah
                                        </a>
                                    @else
                                        <span class="text-[10px] text-slate-400">Belum</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-7 text-center text-[11px] text-slate-400">
                                    Belum ada data alumni{{ $jenjang ? ' pada jenjang ini' : '' }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Komponen akses cepat untuk role global (Super Admin/Pengasuh/Pembina) --}}
        @unless (\App\Support\Akses::dibatasiJenjang($pengguna))
            @include('dashboard.roles._jenjang-akses', ['jenjangStats' => $rekapJenjang])
        @endunless

        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-[11px] leading-relaxed text-slate-500 dark:text-slate-400">
                <i class="fa-solid fa-circle-info mr-1 text-emerald-600"></i>
                <b>Batas akses {{ $peranPengguna }}:</b>
                @if ($pengguna->bisa('akademik.ubah'))
                    Anda dapat mengelola pendaftaran, santri, alumni, prestasi, nilai, berita, dan galeri.
                    Menu <b>Pengguna</b>, <b>Info Harian</b>, <b>Konten</b>, dan <b>Struktur Organisasi</b> hanya untuk Super Admin.
                @else
                    Anda hanya dapat membaca data pendaftaran, santri, alumni, prestasi, dan nilai.
                    Form input nilai/prestasi serta tombol tambah/ubah/hapus tidak tersedia.
                @endif
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Chart === 'undefined') return;

            let gelap = document.documentElement.classList.contains('dark');
            if (!gelap) {
                try {
                    gelap = localStorage.getItem('darkMode') === 'true';
                } catch (e) {
                    gelap = false;
                }
            }
            const tema = {
                tick: gelap ? '#94a3b8' : '#64748b',
                grid: gelap ? '#334155' : '#f1f5f9',
                legend: gelap ? '#cbd5e1' : '#334155',
            };
            const legendBawah = {
                position: 'bottom',
                labels: { boxWidth: 10, padding: 14, font: { size: 10 }, color: tema.legend },
            };

            const rekap = @json($rekapJenjang);
            const kanvasRekap = document.getElementById('chartPerJenjang');
            if (kanvasRekap) {
                new Chart(kanvasRekap, {
                    type: 'bar',
                    data: {
                        labels: rekap.map(d => d.kode),
                        datasets: [{
                                label: 'Santri',
                                data: rekap.map(d => d.santri),
                                backgroundColor: '#12a67a',
                                borderRadius: 5,
                                maxBarThickness: 22,
                            },
                            {
                                label: 'Pendaftar',
                                data: rekap.map(d => d.pendaftar),
                                backgroundColor: '#2563eb',
                                borderRadius: 5,
                                maxBarThickness: 22,
                            },
                            {
                                label: 'Alumni',
                                data: rekap.map(d => d.alumni),
                                backgroundColor: '#b45309',
                                borderRadius: 5,
                                maxBarThickness: 22,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: { legend: legendBawah },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: { precision: 0, font: { size: 10 }, color: tema.tick },
                                grid: { color: tema.grid },
                            },
                            y: {
                                ticks: { font: { size: 9 }, color: tema.tick, autoSkip: false },
                                grid: { display: false },
                            },
                        },
                    },
                });
            }

            const kelas = @json($kelasChart);
            const kanvasKelas = document.getElementById('chartKelas');
            if (kanvasKelas && kelas.length) {
                new Chart(kanvasKelas, {
                    type: 'bar',
                    data: {
                        labels: kelas.map(d => 'Kelas ' + d.label),
                        datasets: [{
                            label: 'Santri',
                            data: kelas.map(d => d.jml),
                            backgroundColor: @json($jenjang ? $jenjang['warna'] : '#12a67a'),
                            borderRadius: 6,
                            maxBarThickness: 36,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: {
                                ticks: { font: { size: 9 }, color: tema.tick },
                                grid: { display: false },
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0, font: { size: 10 }, color: tema.tick },
                                grid: { color: tema.grid },
                            },
                        },
                    },
                });
            }

            const status = @json($statusChart);
            const kanvasStatus = document.getElementById('chartStatus');
            if (kanvasStatus && status.length) {
                new Chart(kanvasStatus, {
                    type: 'doughnut',
                    data: {
                        labels: status.map(d => d.label),
                        datasets: [{
                            data: status.map(d => d.jml),
                            backgroundColor: status.map(d => d.warna),
                            borderWidth: 0,
                            hoverOffset: 14,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        plugins: { legend: legendBawah },
                    },
                });
            }
        });
    </script>
@endsection
