@extends('layouts.admin')

@section('title', 'Manajemen Galeri - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Manajemen Galeri</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Kelola album foto dan video kegiatan Pondok
                    Pesantren Babul Hasanah.</p>
            </div>
            <div>
                <a href="{{ route('admin.galeri.create') }}"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-plus"></i> Tambah Album Baru
                </a>
            </div>
        </div>

        @if (session('success'))
            <div
                class="bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900 text-emerald-700 dark:text-emerald-400 text-xs font-bold px-4 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <div
            class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-300">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr
                            class="bg-slate-50 dark:bg-slate-800/50 text-slate-400 dark:text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-100 dark:border-slate-800">
                            <th class="py-3 px-6 font-extrabold">Pratinjau</th>
                            <th class="py-3 px-6 font-extrabold">Judul & Kategori</th>
                            <th class="py-3 px-6 font-extrabold">Isi Album</th>
                            <th class="py-3 px-6 font-extrabold">Tanggal</th>
                            <th class="py-3 px-6 font-extrabold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse($albums as $album)
                            @php
                                $cover =
                                    $album->sampul
                                    ?? $album->media->firstWhere('tipe', 'foto')?->path
                                    ?? $album->media->first(fn ($m) => $m->poster)?->poster;
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                <td class="py-4 px-6">
                                    <div
                                        class="w-16 h-12 bg-slate-100 dark:bg-slate-800 rounded-xl overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700">
                                        @if ($cover)
                                            <img src="{{ asset('storage/' . $cover) }}" alt="{{ $album->judul }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <i
                                                class="fa-solid {{ $album->video_count ? 'fa-video text-rose-500' : 'fa-image text-slate-400' }} text-xs"></i>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-black text-slate-900 dark:text-white">{{ $album->judul }}</div>
                                    <div class="mt-1">
                                        <span
                                            class="px-2 py-0.5 bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400 rounded text-[9px] font-bold">
                                            {{ $album->kategori }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex flex-wrap gap-1.5">
                                        <span
                                            class="px-2 py-0.5 bg-sky-50 text-sky-600 dark:bg-sky-950/50 dark:text-sky-400 rounded text-[9px] font-bold">
                                            <i class="fa-regular fa-image"></i> {{ $album->foto_count }} foto
                                        </span>
                                        <span
                                            class="px-2 py-0.5 bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400 rounded text-[9px] font-bold">
                                            <i class="fa-solid fa-circle-play"></i> {{ $album->video_count }} video
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-slate-500 dark:text-slate-400">
                                    {{ ($album->tanggal ?? $album->created_at)->format('d M Y') }}
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <form method="POST" action="{{ route('admin.galeri.destroy', $album) }}"
                                        onsubmit="return confirm('Hapus album ini beserta semua foto dan videonya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-2.5 py-1 bg-rose-50 dark:bg-rose-950/50 text-rose-600 hover:bg-rose-100 rounded-lg text-[10px] font-bold transition">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400 text-xs">Belum ada album.
                                    Klik “Tambah Album Baru” untuk mulai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($albums->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $albums->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
