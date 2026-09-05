@extends('layouts.admin')

@section('title', 'Manajemen Berita - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Manajemen Berita</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Publikasikan informasi dan kegiatan terbaru Pondok
                    Pesantren Babul Hasanah.</p>
            </div>
            <div>
                <a href="#"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-plus"></i> Tambah Berita Baru
                </a>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-300">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr
                            class="bg-slate-50 dark:bg-slate-800/50 text-slate-400 dark:text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-100 dark:border-slate-800">
                            <th class="py-3 px-6 font-extrabold">Judul Berita</th>
                            <th class="py-3 px-6 font-extrabold">Status</th>
                            <th class="py-3 px-6 font-extrabold">Tanggal Publikasi</th>
                            <th class="py-3 px-6 font-extrabold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse($beritas as $berita)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                <td class="py-4 px-6">
                                    <div class="font-black text-slate-900 dark:text-white">{{ $berita->judul }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $berita->slug }}</div>
                                </td>
                                <td class="py-4 px-6">
                                    <span
                                        class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 rounded-lg text-[10px] font-bold">
                                        {{ $berita->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-slate-500 dark:text-slate-400">
                                    {{ $berita->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="py-4 px-6 text-right space-x-1">
                                    <button
                                        class="px-2.5 py-1 bg-amber-50 dark:bg-amber-950/50 text-amber-600 hover:bg-amber-100 rounded-lg text-[10px] font-bold transition">Edit</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400 text-xs">Belum ada berita yang
                                    dipublikasikan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($beritas, 'links'))
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $beritas->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
