@extends('layouts.admin')

@section('title', 'Pengelolaan Prestasi')

@section('content')
<div class="space-y-5">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Prestasi Santri</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Kelola capaian dan penghargaan yang tampil di website.</p>
        </div>
        <a href="{{ route('admin.prestasi.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition">
            <i class="fa-solid fa-plus"></i> Tambah Prestasi
        </a>
    </div>

    @if (session('success'))
        <div class="px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
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