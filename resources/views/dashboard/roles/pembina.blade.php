@extends('layouts.admin')

@section('title', 'Dashboard Pembina Asrama - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-6">

        @include('dashboard.roles._hero', [
            'badge' => 'Pembina',
            'ikon' => 'fa-house-chimney-user',
            'judul' => 'Dashboard Pembinaan',
            'deskripsi' => 'Catat hafalan, presensi kegiatan, pelanggaran, dan verifikasi izin pulang. Pembina tidak mengubah data induk santri maupun nilai akademik.',
            'gradien' => 'from-cyan-800 via-emerald-900 to-emerald-950',
        ])

        @if ($izinTerlambat > 0 || $izinMenunggu > 0)
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                @if ($izinTerlambat > 0)
                    <a href="{{ route('admin.izin.index', ['status' => 'terlambat']) }}"
                        class="flex items-center gap-3 rounded-2xl border border-red-300 bg-red-50 p-4 transition hover:border-red-400">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-600 text-white"><i class="fa-solid fa-triangle-exclamation"></i></span>
                        <div>
                            <p class="text-xs font-black text-red-700">{{ $izinTerlambat }} santri TERLAMBAT kembali</p>
                            <p class="text-[10px] text-red-600">Segera periksa dan hubungi wali.</p>
                        </div>
                    </a>
                @endif
                @if ($izinMenunggu > 0)
                    <a href="{{ route('admin.izin.index', ['status' => 'menunggu']) }}"
                        class="flex items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 transition hover:border-amber-400">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500 text-white"><i class="fa-solid fa-hourglass-half"></i></span>
                        <div>
                            <p class="text-xs font-black text-amber-700">{{ $izinMenunggu }} izin menunggu verifikasi</p>
                            <p class="text-[10px] text-amber-600">Buka antrean izin pulang.</p>
                        </div>
                    </a>
                @endif
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @include('dashboard.roles._stat', [
                'label' => 'Total Santri',
                'nilai' => number_format($totalSantri, 0, ',', '.'),
                'sub' => 'Seluruh jenjang',
                'ikon' => 'fa-people-group',
                'href' => route('admin.santri.index'),
            ])
            @include('dashboard.roles._stat', [
                'label' => 'Presensi Hari Ini',
                'nilai' => number_format($absensiHariIni, 0, ',', '.'),
                'sub' => 'Baris presensi tercatat',
                'ikon' => 'fa-clipboard-check',
                'ikonBg' => 'bg-cyan-50 text-cyan-600 dark:bg-cyan-950/50 dark:text-cyan-400',
                'href' => route('admin.pembinaan.index'),
            ])
            @include('dashboard.roles._stat', [
                'label' => 'Setoran Hafalan Hari Ini',
                'nilai' => number_format($hafalanHariIni, 0, ',', '.'),
                'sub' => 'Setoran / ziyadah / murojaah',
                'ikon' => 'fa-book-quran',
                'ikonBg' => 'bg-teal-50 text-teal-600 dark:bg-teal-950/50 dark:text-teal-400',
                'href' => route('admin.pembinaan.index'),
            ])
            @include('dashboard.roles._stat', [
                'label' => 'Pelanggaran Bulan Ini',
                'nilai' => number_format($pelanggaranBulanIni, 0, ',', '.'),
                'sub' => 'Catatan pembinaan',
                'ikon' => 'fa-triangle-exclamation',
                'ikonBg' => 'bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400',
                'href' => route('admin.pembinaan.index'),
            ])
        </div>

        {{-- Komposisi santri per jenjang --}}
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm font-black text-slate-900 dark:text-white">Komposisi Santri per Jenjang</h2>
                    <p class="text-[10px] text-slate-400">Gambaran cepat jumlah santri yang didampingi.</p>
                </div>
                <a href="{{ route('admin.santri.index') }}" class="text-[10px] font-bold text-emerald-600 hover:underline">Buka data santri</a>
            </div>

            @php
                $maks = collect($jenjangStats)->max('santri') ?: 1;
            @endphp

            <div class="space-y-3">
                @foreach ($jenjangStats as $j)
                    @php $persen = $j['santri'] > 0 ? max(2, round($j['santri'] / $maks * 100)) : 0; @endphp
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full" style="background: {{ $j['warna'] }}"></span>
                                <span class="text-[11px] font-bold text-slate-700 dark:text-slate-200">{{ $j['kode'] }}</span>
                            </div>
                            <span class="text-[11px] font-black text-slate-900 dark:text-white">{{ number_format($j['santri'], 0, ',', '.') }} santri</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                            <div class="h-full rounded-full" style="width: {{ $persen }}%; background: {{ $j['warna'] }}"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Komponen akses cepat per jenjang --}}
        @include('dashboard.roles._jenjang-akses', [
            'judul' => 'Akses Cepat per Jenjang',
            'deskripsi' => 'Lihat nilai, prestasi, data santri, dan alumni pada tiap jenjang (hanya baca).',
        ])

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            {{-- Santri terbaru --}}
            <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 xl:col-span-2">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                    <div>
                        <h2 class="text-sm font-black text-slate-900 dark:text-white">Santri Terbaru</h2>
                        <p class="text-[10px] text-slate-400">6 data terakhir</p>
                    </div>
                    <a href="{{ route('admin.santri.index') }}" class="text-[10px] font-bold text-emerald-600 hover:underline">Lihat semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-400 dark:bg-slate-800/50">
                                <th class="px-5 py-3 font-extrabold">Nama</th>
                                <th class="px-5 py-3 font-extrabold">Jenjang</th>
                                <th class="px-5 py-3 font-extrabold">Kelas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse ($santriTerbaru as $s)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                    <td class="px-5 py-3 font-bold text-slate-700 dark:text-slate-200">
                                        <a href="{{ route('admin.santri.detail', $s->id) }}" class="hover:text-emerald-600">{{ $s->nama_lengkap ?? '-' }}</a>
                                    </td>
                                    <td class="px-5 py-3 text-slate-500">{{ \App\Support\Jenjang::label($s->jenjang_pendidikan ?? null) ?: '-' }}</td>
                                    <td class="px-5 py-3 text-slate-500">{{ $s->kelas ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-5 py-8 text-center text-[11px] text-slate-400">Belum ada data santri.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Izin terbaru --}}
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-3 flex items-center justify-between gap-2">
                    <h2 class="text-sm font-black text-slate-900 dark:text-white">Izin Terbaru</h2>
                    <a href="{{ route('admin.izin.index') }}" class="text-[10px] font-bold text-emerald-600 hover:underline">Semua</a>
                </div>
                <div class="space-y-2">
                    @forelse ($izinTerbaru as $iz)
                        <a href="{{ route('admin.izin.index') }}" class="block rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2.5 transition hover:border-emerald-200 dark:border-slate-800 dark:bg-slate-800/40">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-[11px] font-bold text-slate-800 dark:text-slate-100">{{ $iz->santri?->nama_lengkap ?? 'Santri' }}</p>
                                <span class="rounded-lg border px-2 py-0.5 text-[9px] font-black uppercase {{ $iz->kelasStatus() }}">{{ $iz->labelStatus() }}</span>
                            </div>
                            <p class="mt-0.5 text-[10px] text-slate-400">
                                {{ $iz->tanggal_pulang?->translatedFormat('d M') }} – {{ $iz->tanggal_kembali?->translatedFormat('d M Y') }}
                                · {{ $iz->penjemput }}
                            </p>
                        </a>
                    @empty
                        <p class="py-6 text-center text-[11px] text-slate-400">Belum ada pengajuan izin.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-[11px] leading-relaxed text-slate-500 dark:text-slate-400">
                <i class="fa-solid fa-circle-info mr-1 text-emerald-600"></i>
                <b>Batas akses Pembina:</b> menginput hafalan, presensi kegiatan, pelanggaran, dan memverifikasi izin.
                Pembina tidak dapat mengubah data induk santri, nilai akademik, atau mengelola pengguna.
            </p>
        </div>
    </div>
@endsection
