@extends('layouts.admin')

@section('title', 'Jadwal Pelajaran - Akademik')

@use('App\Support\Akademik')
@use('App\Support\Jenjang')

@section('content')
    @php
        $slugAdmin = auth()->user()->jenjangSlug();
        $hariSekarang = null;
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Jadwal Pelajaran</h2>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                    Atur jadwal per jenjang, kelas, tahun ajaran, dan semester. Bentrok jam kelas dan guru dicegah otomatis.
                </p>
            </div>
            @if ($bisaUbah)
                <a href="{{ route('admin.akademik.jadwal.create', ['jenjang' => $filter['jenjang']]) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-xs font-black text-white shadow-sm transition hover:bg-emerald-800">
                    <i class="fa-solid fa-plus"></i> Tambah Jadwal
                </a>
            @endif
        </div>

        @include('dashboard.akademik._tabs')
        @include('dashboard.akademik._peringatan-jenjang')

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10 px-4 py-3 text-xs font-bold text-emerald-800 dark:text-emerald-300">
                <i class="fa-solid fa-circle-check mr-1.5"></i>{{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 dark:border-rose-500/30 bg-rose-50 dark:bg-rose-500/10 px-4 py-3 text-xs font-bold text-rose-800 dark:text-rose-300">
                <i class="fa-solid fa-triangle-exclamation mr-1.5"></i>{{ session('error') }}
            </div>
        @endif

        <form method="GET" action="{{ route('admin.akademik.jadwal.index') }}"
            class="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                @if ($slugAdmin)
                    <input type="hidden" name="jenjang" value="{{ $slugAdmin }}">
                @else
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Jenjang</label>
                        <select name="jenjang"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            <option value="">Semua jenjang</option>
                            @foreach ($daftarJenjang as $j)
                                <option value="{{ $j['slug'] }}" @selected($filter['jenjang'] === $j['slug'])>
                                    {{ $j['kode'] }} — {{ $j['nama'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Kelas</label>
                    <input type="text" name="kelas" maxlength="50" value="{{ $filter['kelas'] }}" placeholder="Contoh: 7A"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Tahun Ajaran</label>
                    <select name="tahun_ajaran"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                        @foreach (Akademik::tahunAjaranPilihan() as $tahun)
                            <option value="{{ $tahun }}" @selected($filter['tahun'] === $tahun)>{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Semester</label>
                    <select name="semester"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                        @foreach (Akademik::SEMESTER as $nilai => $label)
                            <option value="{{ $nilai }}" @selected($filter['semester'] === $nilai)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-slate-800 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-slate-900 dark:bg-slate-700">
                    <i class="fa-solid fa-filter"></i> Tampilkan Jadwal
                </button>
            </div>
        </form>

        <div class="overflow-hidden rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
            @if ($jadwal->isEmpty())
                <div class="px-6 py-12 text-center space-y-2">
                    <i class="fa-solid fa-calendar-xmark text-2xl text-slate-300 dark:text-slate-600"></i>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400">Belum ada jadwal untuk konteks ini.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[820px] text-left">
                        <thead class="bg-slate-50 dark:bg-slate-800/60">
                            <tr>
                                <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Hari</th>
                                <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Jam</th>
                                <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Kelas</th>
                                <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Mata Pelajaran</th>
                                <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Guru</th>
                                <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Ruang</th>
                                @if ($bisaUbah)
                                    <th class="px-4 py-3 text-right text-[10px] font-black uppercase tracking-wider text-slate-400">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($jadwal as $row)
                                @if ($hariSekarang !== $row->hari)
                                    @php $hariSekarang = $row->hari; @endphp
                                    <tr class="bg-emerald-50/60 dark:bg-emerald-500/10">
                                        <td colspan="{{ $bisaUbah ? 7 : 6 }}"
                                            class="px-4 py-2 text-[11px] font-black uppercase tracking-wider text-emerald-800 dark:text-emerald-300">
                                            <i class="fa-solid fa-calendar-day mr-1.5"></i>{{ $row->hari }}
                                        </td>
                                    </tr>
                                @endif
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                                    <td class="px-4 py-3 text-xs text-slate-400">—</td>
                                    <td class="px-4 py-3 text-xs font-black font-mono text-slate-800 dark:text-slate-100">{{ $row->labelWaktu() }}</td>
                                    <td class="px-4 py-3 text-xs font-bold text-slate-700 dark:text-slate-200">{{ $row->kelas }}</td>
                                    <td class="px-4 py-3">
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ $row->mataPelajaran?->nama ?? '—' }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $row->jenjang }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-600 dark:text-slate-300">{{ $row->guru?->nama ?? 'Belum ditentukan' }}</td>
                                    <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">{{ $row->ruang ?? '—' }}</td>
                                    @if ($bisaUbah)
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('admin.akademik.jadwal.edit', $row) }}"
                                                    class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-[10px] font-bold text-slate-600 transition hover:border-emerald-400 hover:text-emerald-700 dark:border-slate-700 dark:text-slate-300">
                                                    <i class="fa-solid fa-pen"></i> Edit
                                                </a>
                                                <form method="POST" action="{{ route('admin.akademik.jadwal.destroy', $row) }}"
                                                    onsubmit="return confirm('Hapus jadwal ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="rounded-lg border border-rose-200 px-2.5 py-1.5 text-[10px] font-bold text-rose-600 transition hover:bg-rose-50 dark:border-rose-500/30 dark:text-rose-300">
                                                        <i class="fa-solid fa-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
