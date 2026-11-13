@extends('layouts.admin')

@section('title', 'Transkrip Nilai - Akademik')

@use('App\Support\Akademik')

@section('content')
    @php
        $user = auth()->user();
        $slugAdmin = $user->jenjangSlug();
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-violet-700 dark:text-violet-400">
                    <i class="fa-solid fa-file-lines"></i> Akademik
                </div>
                <h2 class="mt-1 text-xl font-black text-slate-900 dark:text-white">Transkrip & Rekap Nilai</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-2xl">
                    Rekap rata-rata nilai yang sudah terbit per kelas. Buka detail santri untuk melihat rincian semua mata pelajaran.
                </p>
            </div>
            <span class="inline-flex items-center gap-2 rounded-xl border border-violet-200 dark:border-violet-500/30 bg-violet-50 dark:bg-violet-500/10 px-3 py-2 text-[11px] font-bold text-violet-800 dark:text-violet-300">
                <i class="fa-solid fa-building-columns"></i> {{ $user->namaJenjang() }}
            </span>
        </div>

        @include('dashboard.akademik._tabs')

        @include('dashboard.akademik._peringatan-jenjang')

        {{-- FILTER --}}
        <form method="GET" action="{{ route('admin.akademik.transkrip.index') }}"
            class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-3 items-end">
                <div class="space-y-1.5 xl:col-span-1">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Jenjang</label>
                    @if ($slugAdmin)
                        <input type="hidden" name="jenjang" value="{{ $slugAdmin }}">
                        <div class="rounded-xl border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10 px-3 py-2.5 text-xs font-bold text-emerald-800 dark:text-emerald-300">
                            {{ $user->namaJenjang() }}
                        </div>
                    @else
                        <select name="jenjang" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                            <option value="">Semua jenjang</option>
                            @foreach ($daftarJenjang as $j)
                                <option value="{{ $j['slug'] }}" @selected($filter['jenjang'] === $j['slug'])>{{ $j['kode'] }} — {{ $j['nama'] }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Kelas</label>
                    <select name="kelas" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                        <option value="">Semua kelas</option>
                        @foreach ($daftarKelas as $kelas)
                            <option value="{{ $kelas }}" @selected($filter['kelas'] === $kelas)>{{ $kelas }}</option>
                        @endforeach
                    </select>
                </div>
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
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.akademik.transkrip.index') }}"
                        class="flex-1 text-center rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        Reset
                    </a>
                    <button type="submit"
                        class="flex-1 rounded-xl bg-violet-700 hover:bg-violet-800 text-white px-4 py-2.5 text-xs font-bold transition inline-flex items-center justify-center gap-2">
                        <i class="fa-solid fa-filter"></i> Terapkan
                    </button>
                </div>
            </div>
            <p class="mt-3 text-[10px] text-slate-400">
                Rata-rata dihitung dari nilai berstatus <span class="font-bold">Terbit</span> saja.
            </p>
        </form>

        {{-- TABEL --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Daftar Santri</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ $santris->count() }} santri · {{ $filter['tahun'] }} · Semester {{ Akademik::labelSemester($filter['semester']) }}</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">No</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">NIS</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Nama Santri</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Kelas</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Mapel</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Rata-rata</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Predikat</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($santris as $santri)
                            @php($agg = $agregat->get($santri->id))
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                                <td class="px-4 py-3 text-xs text-slate-400">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 text-[11px] font-mono text-slate-500 dark:text-slate-400">{{ $santri->nomor_induk }}</td>
                                <td class="px-4 py-3 text-xs font-bold text-slate-800 dark:text-slate-100">{{ $santri->nama_lengkap }}</td>
                                <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">{{ $santri->kelas ?? '—' }}</td>
                                <td class="px-4 py-3 text-xs font-bold text-slate-700 dark:text-slate-200">{{ $agg->jumlah ?? 0 }}</td>
                                <td class="px-4 py-3 text-xs font-black text-slate-900 dark:text-white">{{ $agg ? Akademik::formatNilai($agg->rata) : '—' }}</td>
                                <td class="px-4 py-3">
                                    @if ($agg)
                                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-500/20 text-[11px] font-black text-emerald-700 dark:text-emerald-300">
                                            {{ Akademik::predikat($agg->rata) }}
                                        </span>
                                    @else
                                        <span class="text-[10px] font-bold text-slate-400">Belum ada</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.akademik.transkrip.show', $santri) }}?tahun_ajaran={{ $filter['tahun'] }}&semester={{ $filter['semester'] }}"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 dark:border-slate-700 px-3 py-2 text-[11px] font-bold text-slate-600 dark:text-slate-300 hover:border-violet-400 hover:text-violet-700 dark:hover:text-violet-300 transition">
                                        <i class="fa-solid fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center">
                                    <i class="fa-solid fa-user-slash text-2xl text-slate-300"></i>
                                    <p class="mt-2 text-xs font-bold text-slate-500 dark:text-slate-400">Belum ada santri pada filter ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
