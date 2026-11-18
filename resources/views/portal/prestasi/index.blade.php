@extends('layouts.portal')

@section('title', 'Prestasi Santri - Portal Wali Santri')

@section('content')
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-amber-600">
                    <i class="fa-solid fa-trophy"></i> Prestasi Ananda
                </div>
                <h2 class="mt-1 text-xl sm:text-2xl font-black text-slate-900">Capaian & Penghargaan</h2>
                <p class="text-xs text-slate-500 max-w-2xl leading-relaxed">
                    Seluruh prestasi yang dicatat pengelola pesantren untuk putra/putri Anda. Data ini juga
                    tampil di halaman publik prestasi pesantren.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('portal.dashboard') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                    <i class="fa-solid fa-arrow-left"></i> Beranda Portal
                </a>
                <a href="{{ route('portal.nilai') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 text-xs font-black text-white transition">
                    <i class="fa-solid fa-book-open"></i> Lihat Nilai
                </a>
            </div>
        </div>

        @if (! $siap)
            <div class="rounded-3xl border border-amber-200 bg-amber-50 p-6 text-xs text-amber-800 leading-relaxed">
                <p class="font-black"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Data prestasi belum tersedia.</p>
                <p class="mt-1">
                    Akun Anda belum tertaut dengan data santri, atau pengelola pesantren belum menyiapkan
                    kolom penghubung prestasi. Hubungi admin pesantren untuk menautkan akun Anda.
                </p>
            </div>
        @elseif ($prestasi->isEmpty())
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center space-y-3">
                <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto text-lg">
                    <i class="fa-solid fa-trophy"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Belum ada prestasi tercatat</h3>
                <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto leading-relaxed">
                    Prestasi ananda akan muncul di sini setelah dicatat oleh Admin Jenjang melalui dashboard
                    pesantren.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach ($prestasi as $item)
                    @php
                        $inisial = strtoupper(mb_substr($item->nama ?: '-', 0, 1));
                        $warna = match ((int) $item->peringkat) {
                            1 => 'from-amber-400 to-amber-600',
                            2 => 'from-slate-300 to-slate-500',
                            3 => 'from-orange-300 to-orange-500',
                            default => 'from-emerald-500 to-emerald-700',
                        };
                    @endphp
                    <article class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                        <div class="bg-gradient-to-br {{ $warna }} px-5 py-5 text-white flex items-start justify-between gap-3">
                            <span class="w-11 h-11 rounded-2xl bg-white/20 border border-white/40 flex items-center justify-center text-lg">
                                <i class="fa-solid {{ (int) $item->peringkat === 1 ? 'fa-trophy' : ((int) $item->peringkat > 0 ? 'fa-medal' : 'fa-star') }}"></i>
                            </span>
                            <div class="text-right">
                                <div class="text-sm font-black">{{ $item->label ?: 'Penghargaan' }}</div>
                                <div class="text-[10px] font-bold uppercase tracking-wider opacity-90">{{ $item->tahun }}</div>
                            </div>
                        </div>

                        <div class="p-5 space-y-3 flex-1 flex flex-col">
                            <div class="flex flex-wrap gap-1.5">
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-600">
                                    {{ $item->kategori }}
                                </span>
                                <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-bold text-amber-700">
                                    Tingkat {{ $item->tingkat }}
                                </span>
                            </div>

                            <h3 class="text-sm font-black text-slate-900 leading-snug">{{ $item->judul }}</h3>

                            <div class="text-[11px] text-slate-500 space-y-1">
                                <p><i class="fa-solid fa-location-dot text-amber-500 mr-1.5"></i>{{ $item->wilayah }}</p>
                                <p><i class="fa-regular fa-calendar text-amber-500 mr-1.5"></i>{{ $item->tahun }}</p>
                            </div>

                            @if (filled($item->deskripsi))
                                <p class="text-[11.5px] text-slate-500 leading-relaxed flex-1">{{ $item->deskripsi }}</p>
                            @else
                                <div class="flex-1"></div>
                            @endif

                            <div class="flex items-center gap-3 border-t border-slate-100 pt-3">
                                <span class="w-9 h-9 rounded-full bg-emerald-700 text-white text-xs font-black flex items-center justify-center">
                                    {{ $inisial }}
                                </span>
                                <div>
                                    <p class="text-xs font-bold text-slate-800 leading-tight">{{ $item->nama }}</p>
                                    <p class="text-[10px] text-slate-400">Kelas {{ $item->kelas ?: '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

    </div>
@endsection
