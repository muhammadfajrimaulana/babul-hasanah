@extends('layouts.admin')

@section('title', 'Struktur Organisasi - Panel Admin')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Judul --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Struktur Organisasi</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Kelola bagan pengurus, ketua jenjang, dan bidang yang tampil di halaman publik.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('profil.struktur-organisasi') }}" target="_blank"
                class="px-4 py-2.5 rounded-xl text-xs font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                <i class="fa-solid fa-arrow-up-right-from-square mr-1"></i> Lihat halaman
            </a>
            <a href="{{ route('admin.struktur.create', ['kategori' => $kategori]) }}"
                class="px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg shadow-emerald-600/20 transition">
                <i class="fa-solid fa-plus mr-1"></i> Tambah
            </a>
        </div>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm">
            <i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 px-4 py-3 text-sm">
            <i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Tab kategori --}}
    <div class="flex flex-wrap gap-2">
        @foreach (\App\Models\StrukturOrganisasi::KATEGORI as $key => $label)
            <a href="{{ route('admin.struktur.index', ['kategori' => $key]) }}"
                class="px-4 py-2 rounded-xl text-xs font-bold border transition
                {{ $kategori === $key
                    ? 'bg-emerald-600 text-white border-emerald-600'
                    : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-emerald-400' }}">
                {{ $label }}
                <span class="ml-1 opacity-70">({{ $jumlah[$key] ?? 0 }})</span>
            </a>
        @endforeach
    </div>

    {{-- Daftar --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        @forelse ($items as $item)
            <div class="flex items-center gap-4 px-4 sm:px-5 py-3.5 border-b last:border-b-0 border-slate-100 dark:border-slate-800">

                {{-- Indentasi untuk bagan --}}
                @if ($kategori === 'bagan' && ($item->level ?? 0) > 0)
                    <div class="shrink-0 flex items-center text-slate-300 dark:text-slate-600" style="width: {{ $item->level * 22 }}px; justify-content: flex-end;">
                        <i class="fa-solid fa-turn-up fa-rotate-90 text-xs"></i>
                    </div>
                @endif

                {{-- Avatar --}}
                <div class="relative w-11 h-11 shrink-0 rounded-full overflow-hidden bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 flex items-center justify-center">
                    <i class="fa-solid {{ $item->ikon ?: 'fa-user' }}"></i>
                    @if ($item->foto)
                        <img src="{{ $item->foto_url }}" alt="{{ $item->nama }}" class="absolute inset-0 w-full h-full object-cover" onerror="this.remove()">
                    @endif
                </div>

                {{-- Teks --}}
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-sm text-slate-900 dark:text-white truncate">{{ $item->nama }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                        @if ($kategori === 'bagan')
                            {{ $item->jabatan }}
                        @elseif ($kategori === 'jenjang')
                            Ketua {{ $item->kode }}@if ($item->keterangan) &middot; {{ $item->keterangan }}@endif
                        @else
                            {{ $item->keterangan ?: '-' }}
                        @endif
                    </p>
                </div>

                {{-- Status & urutan --}}
                <div class="hidden sm:flex items-center gap-3 shrink-0">
                    <span class="text-[11px] text-slate-400">Urutan {{ $item->urutan }}</span>
                    @if ($item->aktif)
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">Tampil</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">Disembunyikan</span>
                    @endif
                </div>

                {{-- Aksi --}}
                <div class="flex items-center gap-1.5 shrink-0">
                    <a href="{{ route('admin.struktur.edit', $item) }}" title="Ubah"
                        class="w-9 h-9 grid place-items-center rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-600 hover:text-white transition">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </a>
                    <form action="{{ route('admin.struktur.destroy', $item) }}" method="POST"
                        onsubmit="return confirm(@js('Hapus ' . $item->nama . '?'))">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="Hapus"
                            class="w-9 h-9 grid place-items-center rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-rose-600 hover:text-white transition">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-14 text-slate-500 dark:text-slate-400">
                <i class="fa-solid fa-sitemap text-3xl text-slate-300 dark:text-slate-600 mb-3 block"></i>
                <p class="text-sm">Belum ada data di kategori ini.</p>
                <a href="{{ route('admin.struktur.create', ['kategori' => $kategori]) }}" class="inline-block mt-3 text-xs font-bold text-emerald-600 hover:underline">Tambah sekarang</a>
            </div>
        @endforelse
    </div>

    @if ($kategori === 'bagan')
        <p class="text-xs text-slate-400">
            Susunan bagan mengikuti pilihan <b>Atasan</b>. Data tanpa atasan menjadi puncak bagan. Bawahan dari data yang disembunyikan ikut tidak tampil di halaman publik.
        </p>
    @endif
</div>
@endsection
