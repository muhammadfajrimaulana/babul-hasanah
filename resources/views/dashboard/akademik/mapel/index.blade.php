@extends('layouts.admin')

@section('title', 'Mata Pelajaran - Akademik')

@section('content')
    @php
        $user = auth()->user();
        $slugAdmin = $user->jenjangSlug();
        $namaJenjang = fn (?string $slug) => \App\Support\Jenjang::cari($slug)['nama'] ?? $slug;
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                    <i class="fa-solid fa-book-open-reader"></i> Akademik
                </div>
                <h2 class="mt-1 text-xl font-black text-slate-900 dark:text-white">Master Mata Pelajaran</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-2xl">
                    Daftar mata pelajaran per jenjang beserta KKM. Mata pelajaran dipakai saat input nilai dan transkrip.
                </p>
            </div>
            @if ($bisaUbah)
                <a href="{{ route('admin.akademik.mapel.create', $jenjang ? ['jenjang' => $jenjang] : []) }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2.5 text-xs font-bold transition">
                    <i class="fa-solid fa-plus"></i> Tambah Mata Pelajaran
                </a>
            @endif
        </div>

        @include('dashboard.akademik._tabs')

        @include('dashboard.akademik._peringatan-jenjang')

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10 px-4 py-3.5 text-xs font-bold text-emerald-800 dark:text-emerald-300 flex items-start gap-2.5">
                <i class="fa-solid fa-circle-check mt-0.5"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 dark:border-rose-500/30 bg-rose-50 dark:bg-rose-500/10 px-4 py-3.5 text-xs font-bold text-rose-800 dark:text-rose-300 flex items-start gap-2.5">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- FILTER JENJANG --}}
        <form method="GET" action="{{ route('admin.akademik.mapel.index') }}"
            class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-5">
            <div class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div class="space-y-1.5 flex-1">
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
                                <option value="{{ $j['slug'] }}" @selected($jenjang === $j['slug'])>{{ $j['kode'] }} — {{ $j['nama'] }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.akademik.mapel.index') }}"
                        class="rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        Reset
                    </a>
                    <button type="submit"
                        class="rounded-xl bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white px-4 py-2.5 text-xs font-bold transition inline-flex items-center gap-2">
                        <i class="fa-solid fa-filter"></i> Terapkan
                    </button>
                </div>
            </div>
        </form>

        {{-- DAFTAR MAPEL --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Daftar Mata Pelajaran</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ $mapel->count() }} mata pelajaran</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-left">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">No</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Jenjang</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Kode</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Nama Mata Pelajaran</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Kelompok</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">KKM</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">Status</th>
                            @if ($bisaUbah)
                                <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-right">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($mapel as $item)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                                <td class="px-4 py-3 text-xs text-slate-400">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-lg bg-slate-100 dark:bg-slate-800 px-2.5 py-1 text-[10px] font-bold text-slate-600 dark:text-slate-300">
                                        {{ $namaJenjang($item->jenjang) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-[11px] font-mono font-bold text-slate-600 dark:text-slate-300">{{ $item->kode }}</td>
                                <td class="px-4 py-3 text-xs font-bold text-slate-800 dark:text-slate-100">{{ $item->nama }}</td>
                                <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">{{ $item->kelompok ?: '—' }}</td>
                                <td class="px-4 py-3 text-center text-xs font-black text-slate-900 dark:text-white">{{ $item->kkm }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if ($item->aktif)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 dark:bg-emerald-500/20 px-2.5 py-1 text-[10px] font-black text-emerald-700 dark:text-emerald-300">
                                            <i class="fa-solid fa-circle-check"></i> Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-1 text-[10px] font-black text-slate-500 dark:text-slate-400">
                                            <i class="fa-solid fa-circle-pause"></i> Nonaktif
                                        </span>
                                    @endif
                                </td>
                                @if ($bisaUbah)
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('admin.akademik.mapel.edit', $item) }}"
                                                class="inline-flex items-center gap-1 rounded-lg border border-slate-200 dark:border-slate-700 px-2.5 py-1.5 text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:border-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition">
                                                <i class="fa-solid fa-pen"></i> Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.akademik.mapel.destroy', $item) }}"
                                                onsubmit="return confirm('Hapus mata pelajaran {{ $item->nama }}? Tindakan ini tidak dapat dibatalkan.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1 rounded-lg border border-rose-200 dark:border-rose-500/30 px-2.5 py-1.5 text-[10px] font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition">
                                                    <i class="fa-solid fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $bisaUbah ? 8 : 7 }}" class="px-4 py-12 text-center">
                                    <i class="fa-solid fa-book text-2xl text-slate-300"></i>
                                    <p class="mt-2 text-xs font-bold text-slate-500 dark:text-slate-400">Belum ada mata pelajaran pada jenjang ini.</p>
                                    @if ($bisaUbah)
                                        <a href="{{ route('admin.akademik.mapel.create', $jenjang ? ['jenjang' => $jenjang] : []) }}"
                                            class="mt-3 inline-flex items-center gap-1.5 text-[11px] font-bold text-emerald-700 dark:text-emerald-400 hover:underline">
                                            <i class="fa-solid fa-plus"></i> Tambah mata pelajaran pertama
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
