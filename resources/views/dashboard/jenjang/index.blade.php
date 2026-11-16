@extends('layouts.admin')

@section('title', 'Jenjang Pendidikan - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-6">

        {{-- ===================== HEADER ===================== --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Jenjang Pendidikan</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    @if (\App\Support\Akses::dibatasiJenjang(auth()->user()))
                        Data jenjang {{ auth()->user()->namaJenjang() }} beserta ringkasannya.
                    @else
                        Tujuh jenjang di Pondok Pesantren Babul Hasanah beserta ringkasan datanya.
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center gap-2 rounded-xl border border-emerald-100 dark:border-emerald-900/50 bg-emerald-50 dark:bg-emerald-950/40 px-3.5 py-2 text-[11px] font-bold text-emerald-700 dark:text-emerald-400">
                    <i class="fa-solid fa-users"></i>
                    {{ number_format($totalSantri, 0, ',', '.') }} santri aktif
                </span>
            </div>
        </div>

        {{-- ===================== KARTU JENJANG ===================== --}}
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($ringkasan as $j)
                @php
                    $persen = $totalSantri > 0 ? round(($j['santri'] / $totalSantri) * 100) : 0;
                @endphp
                <a href="{{ route('admin.jenjang.show', $j['slug']) }}"
                    class="group relative overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <span class="absolute inset-x-0 top-0 h-1.5" style="background: {{ $j['warna'] }}"></span>

                    <div class="flex items-start justify-between gap-3">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl text-white"
                                style="background: {{ $j['warna'] }}">
                                <i class="fa-solid {{ $j['ikon'] }}"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                                    {{ $j['kode'] }}</p>
                                <h3 class="truncate text-sm font-black leading-tight text-slate-900 dark:text-white"
                                    title="{{ $j['nama'] }}">{{ $j['nama'] }}</h3>
                            </div>
                        </div>
                        <i
                            class="fa-solid fa-arrow-up-right-from-square mt-1 text-xs text-slate-300 transition group-hover:text-emerald-500"></i>
                    </div>

                    <div class="mt-4 grid grid-cols-3 gap-2">
                        <div class="rounded-xl bg-slate-50 dark:bg-slate-800/60 px-2.5 py-2 text-center">
                            <p class="text-base font-black leading-none text-slate-900 dark:text-white">
                                {{ number_format($j['santri'], 0, ',', '.') }}</p>
                            <p class="mt-1 text-[9px] font-bold uppercase tracking-wide text-slate-400">Santri</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 dark:bg-slate-800/60 px-2.5 py-2 text-center">
                            <p class="text-base font-black leading-none text-slate-900 dark:text-white">
                                {{ number_format($j['pendaftar'], 0, ',', '.') }}</p>
                            <p class="mt-1 text-[9px] font-bold uppercase tracking-wide text-slate-400">Pendaftar</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 dark:bg-slate-800/60 px-2.5 py-2 text-center">
                            <p class="text-base font-black leading-none text-slate-900 dark:text-white">
                                {{ number_format($j['alumni'], 0, ',', '.') }}</p>
                            <p class="mt-1 text-[9px] font-bold uppercase tracking-wide text-slate-400">Alumni</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="h-1.5 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                            <div class="h-full rounded-full transition-all"
                                style="width: {{ $persen }}%; background: {{ $j['warna'] }}"></div>
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
            @empty
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-8 text-center dark:border-amber-900/50 dark:bg-amber-950/30 sm:col-span-2 xl:col-span-3">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-lg text-amber-600 dark:bg-amber-900/50 dark:text-amber-300">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <h3 class="mt-3 text-base font-black text-amber-900 dark:text-amber-200">Jenjang Belum Tersedia</h3>
                    <p class="mx-auto mt-1 max-w-md text-xs leading-relaxed text-amber-700 dark:text-amber-300">
                        Akun Anda belum memiliki jenjang yang ditangani. Hubungi Super Admin untuk
                        mengatur jenjang pada akun ini.
                    </p>
                </div>
            @endforelse
        </div>

        {{-- ===================== CHART PERBANDINGAN ===================== --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 px-5 py-4">
                <div class="flex items-center gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-blue-600 text-white">
                        <i class="fa-solid fa-chart-column text-sm"></i>
                    </span>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white">Data per Jenjang</h3>
                        <p class="text-[11px] text-slate-400">Perbandingan jumlah santri, pendaftar, dan alumni.</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ count($ringkasan) }} Jenjang</span>
            </div>
            <div class="p-4 sm:p-5">
                <div class="h-72 w-full sm:h-80">
                    <canvas id="chartJenjang"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const data = @json($ringkasan);
            const canvas = document.getElementById('chartJenjang');
            if (!canvas) return;

            new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: data.map(d => d.kode),
                    datasets: [{
                            label: 'Santri',
                            data: data.map(d => d.santri),
                            backgroundColor: '#12a67a',
                            borderRadius: 5,
                            maxBarThickness: 22,
                        },
                        {
                            label: 'Pendaftar',
                            data: data.map(d => d.pendaftar),
                            backgroundColor: '#2563eb',
                            borderRadius: 5,
                            maxBarThickness: 22,
                        },
                        {
                            label: 'Alumni',
                            data: data.map(d => d.alumni),
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
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, .92)',
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: true,
                            callbacks: {
                                title: items => {
                                    const i = items[0].dataIndex;
                                    return data[i].kode + ' — ' + data[i].nama;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
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
                        },
                        y: {
                            ticks: {
                                font: {
                                    size: 9
                                },
                                color: '#64748b',
                                autoSkip: false
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
