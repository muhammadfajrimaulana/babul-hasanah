@extends('layouts.admin')

@section('title', 'Pengelolaan Prestasi')

@section('content')
<div class="space-y-5">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Prestasi Santri</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Kelola capaian dan penghargaan yang tampil di website.</p>
        </div>
        @if (auth()->user()->bisa('prestasi.ubah'))
            <a href="{{ route('admin.prestasi.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition">
                <i class="fa-solid fa-plus"></i> Tambah Prestasi
            </a>
        @else
            <span class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-[11px] font-bold text-slate-500 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-300">
                <i class="fa-solid fa-eye"></i> Mode monitoring
            </span>
        @endif
    </div>

    {{-- FILTER JENJANG --}}
    @if ($bisaFilterJenjang ?? false)
        <form method="GET" action="{{ route('admin.prestasi.index') }}"
            class="flex flex-col gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-3">
                <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                    <i class="fa-solid fa-layer-group"></i>
                </span>
                <div>
                    <p class="text-[11px] font-black uppercase tracking-wider text-slate-400">Pilih Jenjang</p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        Menampilkan prestasi satu jenjang, atau seluruh jenjang bila tidak dipilih.
                    </p>
                </div>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <select name="jenjang"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 sm:w-64">
                    <option value="">Semua jenjang</option>
                    @foreach ($daftarJenjang as $j)
                        <option value="{{ $j['slug'] }}" @selected(($filterJenjang ?? '') === $j['slug'])>
                            {{ $j['kode'] }} — {{ $j['nama'] }}
                        </option>
                    @endforeach
                </select>
                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-amber-500 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-amber-600">
                        <i class="fa-solid fa-filter"></i> Terapkan
                    </button>
                    @if (filled($filterJenjang ?? null))
                        <a href="{{ route('admin.prestasi.index') }}"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3 py-2.5 text-[11px] font-bold text-slate-500 transition hover:text-slate-700 dark:border-slate-700 dark:text-slate-400">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    @elseif (filled($namaJenjangAdmin ?? null))
        <div class="flex items-center gap-2 rounded-2xl border border-amber-100 bg-amber-50/70 px-4 py-3 text-[11px] font-bold text-amber-800 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-300">
            <i class="fa-solid fa-layer-group"></i>
            Data dibatasi pada jenjang {{ $namaJenjangAdmin }}.
        </div>
    @endif

    @if (session('success'))
        <div class="px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3">Judul</th>
                        <th class="px-5 py-3">Nama / Kelas</th>
                        <th class="px-5 py-3">Kategori</th>
                        <th class="px-5 py-3">Tingkat</th>
                        <th class="px-5 py-3">Tahun</th>
                        <th class="px-5 py-3">Label</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse ($prestasi as $p)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="px-5 py-3 font-semibold text-slate-900 dark:text-white">{{ $p->judul }}</td>
                            <td class="px-5 py-3">{{ $p->nama }}<span class="block text-[11px] text-slate-400">{{ $p->kelas }}</span></td>
                            <td class="px-5 py-3">{{ $p->kategori }}</td>
                            <td class="px-5 py-3">{{ $p->tingkat }}<span class="block text-[11px] text-slate-400">{{ $p->wilayah }}</span></td>
                            <td class="px-5 py-3">{{ $p->tahun }}</td>
                            <td class="px-5 py-3">
                                <span class="px-2.5 py-1 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 font-semibold">{{ $p->label }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex justify-end gap-2">
                                    @if (auth()->user()->bisa('prestasi.ubah'))
                                        <a href="{{ route('admin.prestasi.edit', $p) }}"
                                            class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-600 hover:text-white transition font-semibold">
                                            <i class="fa-regular fa-pen-to-square"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.prestasi.destroy', $p) }}" method="POST"
                                            onsubmit="return confirm('Hapus prestasi ini?')">
                                            @csrf @method('DELETE')
                                            <button class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-red-600 hover:text-white transition font-semibold">
                                                <i class="fa-regular fa-trash-can"></i> Hapus
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[11px] italic text-slate-400">Hanya baca</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-400">Belum ada data prestasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($prestasi->hasPages())
            <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-800">{{ $prestasi->links() }}</div>
        @endif
    </div>
</div>
@endsection