@extends('layouts.admin')

@section('title', 'Detail Transkrip - ' . $santri->nama_lengkap)

@use('App\Support\Akademik')

@section('content')
    @php
        $user = auth()->user();
        $queryCetak = [
            'tahun_ajaran' => $filter['tahun'],
            'semester' => $filter['semester'],
        ];
        $adaDraft = collect($baris)->contains(fn ($r) => $r['ada_draft']);
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <a href="{{ route('admin.akademik.transkrip.index', ['jenjang' => $filter['jenjang'], 'kelas' => $santri->kelas, 'tahun_ajaran' => $filter['tahun'], 'semester' => $filter['semester']]) }}"
                    class="inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-500 dark:text-slate-400 hover:text-emerald-700 dark:hover:text-emerald-400 transition">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar transkrip
                </a>
                <h2 class="mt-1 text-xl font-black text-slate-900 dark:text-white">Detail Transkrip Nilai</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    {{ $santri->nama_lengkap }} · {{ $santri->nomor_induk }} · {{ $santri->jenjang_pendidikan }} — Kelas {{ $santri->kelas }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.akademik.transkrip.cetak', [$santri] + $queryCetak) }}" target="_blank"
                    class="inline-flex items-center gap-2 rounded-xl bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white px-4 py-2.5 text-xs font-bold transition">
                    <i class="fa-solid fa-print"></i> Cetak / PDF
                </a>
            </div>
        </div>

        @include('dashboard.akademik._tabs')

        @if ($adaDraft)
            <div class="rounded-2xl border border-amber-200 dark:border-amber-500/30 bg-amber-50 dark:bg-amber-500/10 px-4 py-3.5 text-xs font-bold text-amber-800 dark:text-amber-300 flex items-start gap-2.5">
                <i class="fa-solid fa-clock mt-0.5"></i>
                <span>Beberapa nilai pada periode ini masih berstatus Draft dan belum terlihat oleh wali santri. Cetak/PDF hanya memuat nilai yang sudah terbit.</span>
            </div>
        @endif

        {{-- FILTER PERIODE --}}
        <form method="GET" action="{{ route('admin.akademik.transkrip.show', $santri) }}"
            class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-5">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Tahun Ajaran</label>
                    <select name="tahun_ajaran" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                        @foreach ($tahunPilihan as $tahun)
                            <option value="{{ $tahun }}" @selected($filter['tahun'] === $tahun)>{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Semester</label>
                    <select name="semester" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                        @foreach (Akademik::SEMESTER as $nilai => $label)
                            <option value="{{ $nilai }}" @selected($filter['semester'] === $nilai)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                    class="rounded-xl bg-violet-700 hover:bg-violet-800 text-white px-4 py-2.5 text-xs font-bold transition inline-flex items-center justify-center gap-2">
                    <i class="fa-solid fa-filter"></i> Tampilkan Periode
                </button>
            </div>
        </form>

        {{-- RINGKASAN --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            @php
                $kartu = [
                    ['label' => 'Rata-rata', 'nilai' => Akademik::formatNilai($ringkasan['rata']), 'ikon' => 'fa-chart-simple', 'warna' => 'text-emerald-700 dark:text-emerald-300'],
                    ['label' => 'Predikat', 'nilai' => $ringkasan['predikat'], 'ikon' => 'fa-award', 'warna' => 'text-violet-700 dark:text-violet-300'],
                    ['label' => 'Mata Pelajaran', 'nilai' => $ringkasan['mapel'], 'ikon' => 'fa-book', 'warna' => 'text-sky-700 dark:text-sky-300'],
                    ['label' => 'Mencapai KKM', 'nilai' => $ringkasan['kkm'], 'ikon' => 'fa-circle-check', 'warna' => 'text-amber-700 dark:text-amber-300'],
                ];
            @endphp
            @foreach ($kartu as $k)
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $k['label'] }}</span>
                        <i class="fa-solid {{ $k['ikon'] }} text-xs text-slate-300"></i>
                    </div>
                    <p class="mt-2 text-xl font-black {{ $k['warna'] }}">{{ $k['nilai'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- RINCIAN MAPEL --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-sm font-black text-slate-900 dark:text-white">
                    {{ $filter['tahun'] }} · Semester {{ Akademik::labelSemester($filter['semester']) }}
                </h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">Rincian nilai per mata pelajaran beserta rata-rata dan predikat.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-left">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Mata Pelajaran</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">Tugas</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">UTS</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">UAS</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">Hafalan</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">Akhir</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">Predikat</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($baris as $row)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                                <td class="px-4 py-3">
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ $row['mapel']?->nama ?? 'Mata pelajaran terhapus' }}</p>
                                    <p class="text-[10px] text-slate-400">
                                        {{ $row['mapel']?->kode ?? '—' }}
                                        @if ($row['ada_draft'])
                                            <span class="ml-1 inline-flex items-center gap-1 rounded-full bg-amber-100 dark:bg-amber-500/20 px-2 py-0.5 text-[9px] font-black text-amber-700 dark:text-amber-300">
                                                <i class="fa-solid fa-clock"></i> ada draft
                                            </span>
                                        @endif
                                    </p>
                                </td>
                                <td class="px-4 py-3 text-center text-xs font-semibold text-slate-700 dark:text-slate-200">{{ Akademik::formatNilai($row['tugas']) }}</td>
                                <td class="px-4 py-3 text-center text-xs font-semibold text-slate-700 dark:text-slate-200">{{ Akademik::formatNilai($row['uts']) }}</td>
                                <td class="px-4 py-3 text-center text-xs font-semibold text-slate-700 dark:text-slate-200">{{ Akademik::formatNilai($row['uas']) }}</td>
                                <td class="px-4 py-3 text-center text-xs font-semibold text-slate-700 dark:text-slate-200">{{ Akademik::formatNilai($row['hafalan']) }}</td>
                                <td class="px-4 py-3 text-center text-xs font-black text-slate-900 dark:text-white">{{ Akademik::formatNilai($row['akhir']) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-500/20 text-[11px] font-black text-emerald-700 dark:text-emerald-300">
                                        {{ $row['predikat'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-[11px] text-slate-500 dark:text-slate-400">{{ $row['catatan'] ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center">
                                    <i class="fa-solid fa-folder-open text-2xl text-slate-300"></i>
                                    <p class="mt-2 text-xs font-bold text-slate-500 dark:text-slate-400">Belum ada nilai pada periode ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
