@extends('layouts.admin')

@section('title', ($jenjang['nama'] ?? 'Jenjang') . ' - Pondok Pesantren Babul Hasanah')

@section('content')
    @php
        $statusBadge = function (?string $status) {
            $s = strtolower(trim((string) $status));

            return match (true) {
                in_array($s, ['diterima', 'accepted'], true) => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400',
                in_array($s, ['ditolak', 'rejected'], true) => 'bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-400',
                default => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400',
            };
        };
        $statusLabel = function (?string $status) {
            $s = strtolower(trim((string) $status));

            return match (true) {
                in_array($s, ['diterima', 'accepted'], true) => 'Diterima',
                in_array($s, ['ditolak', 'rejected'], true) => 'Ditolak',
                $s === '' => 'Menunggu',
                default => ucfirst($s),
            };
        };
    @endphp

    <div class="space-y-5">

        {{-- ===================== HEADER ===================== --}}
        <div>
            <a href="{{ route('admin.jenjang.index') }}"
                class="inline-flex items-center gap-2 text-[11px] font-bold text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar jenjang
            </a>

            <div class="mt-3 overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
                <div class="h-1.5" style="background: {{ $jenjang['warna'] }}"></div>
                <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex min-w-0 items-start gap-4">
                        <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl text-white"
                            style="background: {{ $jenjang['warna'] }}">
                            <i class="fa-solid {{ $jenjang['ikon'] }} text-xl"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                                {{ $jenjang['kode'] }}</p>
                            <h2 class="text-lg font-black leading-tight text-slate-900 dark:text-white sm:text-xl">
                                {{ $jenjang['nama'] }}</h2>
                            <p class="mt-1 max-w-2xl text-[11px] leading-relaxed text-slate-500 dark:text-slate-400">
                                {{ $jenjang['deskripsi'] }}</p>
                        </div>
                    </div>
                    <a href="{{ route('jenjang.show', $jenjang['slug']) }}" target="_blank"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 px-3.5 py-2 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition hover:border-emerald-500 hover:text-emerald-600">
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Lihat halaman publik
                    </a>
                </div>
            </div>
        </div>

        {{-- ===================== STATISTIK ===================== --}}
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            @foreach ([
            ['Santri Aktif', $ringkasan['santri'] ?? 0, 'fa-users', 'bg-emerald-600'],
            ['Pendaftar', $ringkasan['pendaftar'] ?? 0, 'fa-file-invoice', 'bg-blue-600'],
            ['Alumni', $ringkasan['alumni'] ?? 0, 'fa-user-graduate', 'bg-amber-600'],
            ['Menunggu Verifikasi', $ringkasan['menunggu'] ?? 0, 'fa-hourglass-half', 'bg-rose-600'],
        ] as $s)
                <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-[9px] font-black uppercase tracking-wider text-slate-400">{{ $s[0] }}</p>
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg {{ $s[3] }} text-white">
                            <i class="fa-solid {{ $s[2] }} text-xs"></i>
                        </span>
                    </div>
                    <p class="mt-2 text-2xl font-black leading-none text-slate-900 dark:text-white">
                        {{ number_format($s[1], 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>

        {{-- ===================== CHART ===================== --}}
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
                <div class="border-b border-slate-100 dark:border-slate-800 px-5 py-4">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Santri per Kelas</h3>
                    <p class="text-[11px] text-slate-400">Sebaran santri aktif berdasarkan kelas.</p>
                </div>
                <div class="p-4">
                    @if (count($kelasChart))
                        <div class="h-60 w-full">
                            <canvas id="chartKelas"></canvas>
                        </div>
                    @else
                        <div class="grid h-60 place-items-center text-center text-xs text-slate-400">
                            <div>
                                <i class="fa-regular fa-chart-bar mb-2 block text-3xl text-slate-300"></i>
                                Belum ada data kelas untuk jenjang ini.
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
                <div class="border-b border-slate-100 dark:border-slate-800 px-5 py-4">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Status Pendaftar</h3>
                    <p class="text-[11px] text-slate-400">Rekap verifikasi pendaftaran jenjang ini.</p>
                </div>
                <div class="p-4">
                    @if (count($statusChart))
                        <div class="h-60 w-full">
                            <canvas id="chartStatus"></canvas>
                        </div>
                    @else
                        <div class="grid h-60 place-items-center text-center text-xs text-slate-400">
                            <div>
                                <i class="fa-regular fa-circle-check mb-2 block text-3xl text-slate-300"></i>
                                Belum ada data pendaftar untuk jenjang ini.
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===================== DATA SANTRI ===================== --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 px-5 py-4">
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Data Santri</h3>
                    <p class="text-[11px] text-slate-400">Santri aktif pada jenjang {{ $jenjang['kode'] }}.</p>
                </div>
                <a href="{{ route('admin.santri.index') }}"
                    class="text-[11px] font-bold text-emerald-600 hover:text-emerald-700">Kelola santri <i
                        class="fa-solid fa-arrow-right text-[9px]"></i></a>
            </div>

            @if ($santri && $santri->count())
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-xs">
                        <thead>
                            <tr
                                class="border-b border-slate-100 bg-slate-50 text-[9px] uppercase tracking-wider text-slate-400 dark:border-slate-800 dark:bg-slate-800/50">
                                <th class="px-4 py-2.5 font-extrabold sm:px-5">Nama</th>
                                <th class="hidden px-4 py-2.5 font-extrabold sm:table-cell sm:px-5">NIS</th>
                                <th class="px-4 py-2.5 font-extrabold sm:px-5">Kelas</th>
                                <th class="hidden px-4 py-2.5 font-extrabold md:table-cell md:px-5">Wali</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700 dark:divide-slate-800 dark:text-slate-300">
                            @foreach ($santri as $s)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                    <td class="px-4 py-3 font-bold text-slate-900 dark:text-white sm:px-5">
                                        {{ $s->nama_lengkap }}</td>
                                    <td class="hidden px-4 py-3 font-mono text-[10px] text-slate-400 sm:table-cell sm:px-5">
                                        {{ $s->nomor_induk ?: '-' }}</td>
                                    <td class="px-4 py-3 sm:px-5">{{ $s->kelas ?: '-' }}</td>
                                    <td class="hidden px-4 py-3 md:table-cell md:px-5">{{ $s->nama_wali ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-100 px-4 py-3 dark:border-slate-800 sm:px-5">{{ $santri->links() }}</div>
            @else
                <div class="px-5 py-10 text-center text-xs text-slate-400">Belum ada santri pada jenjang ini.</div>
            @endif
        </div>

        {{-- ===================== DATA PENDAFTAR ===================== --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 px-5 py-4">
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Data Pendaftar</h3>
                    <p class="text-[11px] text-slate-400">Calon santri yang memilih jenjang {{ $jenjang['kode'] }}.</p>
                </div>
                <a href="{{ route('admin.pendaftaran.index') }}"
                    class="text-[11px] font-bold text-emerald-600 hover:text-emerald-700">Kelola pendaftaran <i
                        class="fa-solid fa-arrow-right text-[9px]"></i></a>
            </div>

            @if ($pendaftar && $pendaftar->count())
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-xs">
                        <thead>
                            <tr
                                class="border-b border-slate-100 bg-slate-50 text-[9px] uppercase tracking-wider text-slate-400 dark:border-slate-800 dark:bg-slate-800/50">
                                <th class="px-4 py-2.5 font-extrabold sm:px-5">Nama</th>
                                <th class="hidden px-4 py-2.5 font-extrabold sm:table-cell sm:px-5">Asal Sekolah</th>
                                <th class="px-4 py-2.5 font-extrabold sm:px-5">Status</th>
                                <th class="hidden px-4 py-2.5 font-extrabold md:table-cell md:px-5">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700 dark:divide-slate-800 dark:text-slate-300">
                            @foreach ($pendaftar as $p)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                    <td class="px-4 py-3 font-bold text-slate-900 dark:text-white sm:px-5">
                                        {{ $p->nama_lengkap }}</td>
                                    <td class="hidden px-4 py-3 sm:table-cell sm:px-5">{{ $p->asal_sekolah ?: '-' }}</td>
                                    <td class="px-4 py-3 sm:px-5">
                                        <span
                                            class="rounded-lg px-2 py-0.5 text-[9px] font-bold {{ $statusBadge($p->status_pendaftaran) }}">
                                            {{ $statusLabel($p->status_pendaftaran) }}
                                        </span>
                                    </td>
                                    <td class="hidden px-4 py-3 text-slate-400 md:table-cell md:px-5">
                                        {{ optional($p->created_at)->format('d M Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-100 px-4 py-3 dark:border-slate-800 sm:px-5">{{ $pendaftar->links() }}
                </div>
            @else
                <div class="px-5 py-10 text-center text-xs text-slate-400">Belum ada pendaftar pada jenjang ini.</div>
            @endif
        </div>

        {{-- ===================== DATA ALUMNI ===================== --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 px-5 py-4">
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Data Alumni</h3>
                    <p class="text-[11px] text-slate-400">Lulusan dari jenjang {{ $jenjang['kode'] }}.</p>
                </div>
                <a href="{{ route('admin.alumni.index') }}"
                    class="text-[11px] font-bold text-emerald-600 hover:text-emerald-700">Kelola alumni <i
                        class="fa-solid fa-arrow-right text-[9px]"></i></a>
            </div>

            @if ($alumni && $alumni->count())
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-xs">
                        <thead>
                            <tr
                                class="border-b border-slate-100 bg-slate-50 text-[9px] uppercase tracking-wider text-slate-400 dark:border-slate-800 dark:bg-slate-800/50">
                                <th class="px-4 py-2.5 font-extrabold sm:px-5">Nama</th>
                                <th class="px-4 py-2.5 font-extrabold sm:px-5">Tahun Lulus</th>
                                <th class="hidden px-4 py-2.5 font-extrabold sm:table-cell sm:px-5">Kegiatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700 dark:divide-slate-800 dark:text-slate-300">
                            @foreach ($alumni as $a)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                    <td class="px-4 py-3 font-bold text-slate-900 dark:text-white sm:px-5">{{ $a->nama }}
                                    </td>
                                    <td class="px-4 py-3 sm:px-5">{{ $a->tahun_lulus ?: '-' }}</td>
                                    <td class="hidden px-4 py-3 sm:table-cell sm:px-5">{{ $a->kegiatan ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-100 px-4 py-3 dark:border-slate-800 sm:px-5">{{ $alumni->links() }}</div>
            @else
                <div class="px-5 py-10 text-center text-xs text-slate-400">Belum ada alumni pada jenjang ini.</div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const kelas = @json($kelasChart);
            const status = @json($statusChart);

            const kelasCanvas = document.getElementById('chartKelas');
            if (kelasCanvas && kelas.length) {
                new Chart(kelasCanvas, {
                    type: 'bar',
                    data: {
                        labels: kelas.map(d => 'Kelas ' + d.label),
                        datasets: [{
                            label: 'Santri',
                            data: kelas.map(d => d.jml),
                            backgroundColor: @json($jenjang['warna']),
                            borderRadius: 6,
                            maxBarThickness: 36,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    font: {
                                        size: 9
                                    },
                                    color: '#64748b'
                                },
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    font: {
                                        size: 10
                                    },
                                    color: '#64748b'
                                },
                                grid: {
                                    color: '#f1f5f9'
                                }
                            }
                        }
                    }
                });
            }

            const statusCanvas = document.getElementById('chartStatus');
            if (statusCanvas && status.length) {
                new Chart(statusCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: status.map(d => d.label),
                        datasets: [{
                            data: status.map(d => d.jml),
                            backgroundColor: status.map(d => d.warna),
                            borderWidth: 0,
                            hoverOffset: 14,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 10,
                                    padding: 14,
                                    font: {
                                        size: 10
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
