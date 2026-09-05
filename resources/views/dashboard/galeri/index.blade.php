@extends('layouts.admin')

@section('title', 'Manajemen Galeri - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Manajemen Galeri</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Kelola dokumentasi foto dan tautan video kegiatan
                    Pondok Pesantren Babul Hasanah.</p>
            </div>
            <div>
                <a href="#"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-plus"></i> Tambah Media Baru
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
                            <th class="py-3 px-6 font-extrabold">Pratinjau</th>
                            <th class="py-3 px-6 font-extrabold">Judul & Tipe</th>
                            <th class="py-3 px-6 font-extrabold">Keterangan / Tautan</th>
                            <th class="py-3 px-6 font-extrabold">Tanggal</th>
                            <th class="py-3 px-6 font-extrabold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse($galeris as $galeri)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                <td class="py-4 px-6">
                                    <div
                                        class="w-16 h-12 bg-slate-100 dark:bg-slate-800 rounded-xl overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 relative">
                                        @if ($galeri->tipe == 'Video')
                                            <div
                                                class="absolute inset-0 bg-slate-900/40 flex items-center justify-center text-rose-500 text-sm">
                                                <i class="fa-brands fa-youtube"></i>
                                            </div>
                                        @endif

                                        @if ($galeri->gambar)
                                            <img src="{{ asset('storage/' . $galeri->gambar) }}" alt="{{ $galeri->judul }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <i
                                                class="fa-solid {{ $galeri->tipe == 'Video' ? 'fa-video text-rose-500' : 'fa-image text-slate-400' }} text-xs"></i>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-black text-slate-900 dark:text-white">{{ $galeri->judul }}</div>
                                    <div class="mt-1">
                                        <span
                                            class="px-2 py-0.5 {{ $galeri->tipe == 'Video' ? 'bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400' : 'bg-sky-50 text-sky-600 dark:bg-sky-950/50 dark:text-sky-400' }} rounded text-[9px] font-bold">
                                            {{ $galeri->tipe }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-slate-500 dark:text-slate-400 max-w-xs truncate">
                                    @if ($galeri->tipe == 'Video' && $galeri->youtube_url)
                                        <a href="{{ $galeri->youtube_url }}" target="_blank"
                                            class="text-sky-600 hover:underline flex items-center gap-1 font-mono text-[10px]">
                                            <i class="fa-solid fa-link"></i> {{ $galeri->youtube_url }}
                                        </a>
                                    @else
                                        {{ $galeri->keterangan ?? '-' }}
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-slate-500 dark:text-slate-400">
                                    {{ $galeri->created_at->format('d M Y') }}
                                </td>
                                <td class="py-4 px-6 text-right space-x-1">
                                    <button
                                        class="px-2.5 py-1 bg-amber-50 dark:bg-amber-950/50 text-amber-600 hover:bg-amber-100 rounded-lg text-[10px] font-bold transition">Edit</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400 text-xs">Belum ada dokumentasi
                                    foto atau video di galeri.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($galeris, 'links'))
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $galeris->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
