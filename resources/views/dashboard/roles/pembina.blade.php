@extends('layouts.admin')

@section('title', 'Dashboard Pembina Asrama - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-6">

        @include('dashboard.roles._hero', [
            'badge' => 'Pembina Asrama',
            'ikon' => 'fa-house-chimney-user',
            'judul' => 'Dashboard Pembina Asrama',
            'deskripsi' => 'Pantau kondisi santri dan kehadiran harian. Halaman ini bersifat monitoring (hanya baca) agar pembina dapat fokus mendampingi santri tanpa mengubah data induk.',
            'gradien' => 'from-cyan-800 via-emerald-900 to-emerald-950',
        ])

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @include('dashboard.roles._stat', [
                'label' => 'Total Santri',
                'nilai' => number_format($totalSantri, 0, ',', '.'),
                'sub' => 'Seluruh jenjang',
                'ikon' => 'fa-people-group',
                'href' => route('admin.santri.index'),
            ])
            @include('dashboard.roles._stat', [
                'label' => 'Kehadiran Hari Ini',
                'nilai' => number_format($absensiHariIni, 0, ',', '.'),
                'sub' => 'Baris absensi tercatat',
                'ikon' => 'fa-clipboard-check',
                'ikonBg' => 'bg-cyan-50 text-cyan-600 dark:bg-cyan-950/50 dark:text-cyan-400',
            ])
            @include('dashboard.roles._stat', [
                'label' => 'Prestasi Santri',
                'nilai' => number_format($totalPrestasi, 0, ',', '.'),
                'sub' => 'Seluruh jenjang',
                'ikon' => 'fa-trophy',
                'ikonBg' => 'bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400',
                'href' => route('admin.prestasi.index'),
            ])
            @include('dashboard.roles._stat', [
                'label' => 'Alumni',
                'nilai' => number_format($totalAlumni, 0, ',', '.'),
                'sub' => 'Lulusan tercatat',
                'ikon' => 'fa-user-graduate',
                'ikonBg' => 'bg-violet-50 text-violet-600 dark:bg-violet-950/50 dark:text-violet-400',
                'href' => route('admin.alumni.index'),
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
                    @php $persen = $maks > 0 ? max(2, round($j['santri'] / $maks * 100)) : 2; @endphp
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

            {{-- Prestasi terbaru --}}
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-3 flex items-center justify-between gap-2">
                    <h2 class="text-sm font-black text-slate-900 dark:text-white">Prestasi Terbaru</h2>
                    <a href="{{ route('admin.prestasi.index') }}" class="text-[10px] font-bold text-emerald-600 hover:underline">Semua</a>
                </div>
                <div class="space-y-2">
                    @forelse ($prestasiTerbaru as $pr)
                        <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2.5 dark:border-slate-800 dark:bg-slate-800/40">
                            <p class="text-[11px] font-bold text-slate-800 dark:text-slate-100">{{ $pr->judul ?? '-' }}</p>
                            <p class="text-[10px] text-slate-400">{{ $pr->nama ?? '-' }} · {{ $pr->tingkat ?? $pr->kategori ?? '-' }}</p>
                        </div>
                    @empty
                        <p class="py-6 text-center text-[11px] text-slate-400">Belum ada prestasi tercatat.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-[11px] leading-relaxed text-slate-500 dark:text-slate-400">
                <i class="fa-solid fa-circle-info mr-1 text-emerald-600"></i>
                <b>Batas akses Pembina Asrama:</b> hanya membaca data santri, pendaftaran, alumni, prestasi, dan jenjang.
                Pembina tidak dapat menambah, mengubah, atau menghapus data.
            </p>
        </div>
    </div>
@endsection
