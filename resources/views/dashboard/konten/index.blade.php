@extends('layouts.admin')

@section('title', 'Manajemen Konten Profil Pesantren')

@section('content')
    <div class="space-y-8 max-w-7xl mx-auto pb-12">
        <!-- Header Section -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm transition-colors">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center text-xl shrink-0 shadow-inner">
                    <i class="fa-solid fa-address-card"></i>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Manajemen Konten Profil
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola informasi teks sejarah, visi misi,
                        serta struktur dewan pengasuh pesantren secara dinamis.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Live Preview Active
                </span>
            </div>
        </div>

        @if (session('success'))
            <div
                class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 rounded-2xl text-xs font-semibold flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Form Edit Teks Profil -->
        <form action="{{ route('admin.konten.update') }}" method="POST" enctype="multipart/form-data"
            class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm overflow-hidden transition-colors">
            @csrf
            @method('PUT')

            <div
                class="p-6 sm:p-8 border-b border-slate-100 dark:border-slate-700/60 bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-xs font-bold shadow-sm">
                        1</div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white">Konten Teks &
                        Informasi Utama</h2>
                </div>
                <button type="submit"
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-lg shadow-emerald-900/10 flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan Teks
                </button>
            </div>

            <div class="p-6 sm:p-8 space-y-6">
                <!-- Hero Section Group -->
                <div class="space-y-4">
                    <h3
                        class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 flex items-center gap-2">
                        <i class="fa-solid fa-panorama"></i> Hero Section Profil
                    </h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Judul Hero Section</label>
                            <input type="text" name="profil_hero_title" value="{{ $kontens['profil_hero_title'] ?? '' }}"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Deskripsi Hero
                                Section</label>
                            <textarea name="profil_hero_desc" rows="2"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-sm">{{ $kontens['profil_hero_desc'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100 dark:border-slate-700/60">

                <!-- Sejarah Singkat Group -->
                <div class="space-y-4">
                    <h3
                        class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 flex items-center gap-2">
                        <i class="fa-solid fa-book-open"></i> Sejarah Berdirinya Pesantren
                    </h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Judul Sejarah
                                Singkat</label>
                            <input type="text" name="sejarah_judul" value="{{ $kontens['sejarah_judul'] ?? '' }}"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-sm">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Sejarah Paragraf
                                    1</label>
                                <textarea name="sejarah_p1" rows="4"
                                    class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-sm">{{ $kontens['sejarah_p1'] ?? '' }}</textarea>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Sejarah Paragraf 2
                                    (Lokasi & Alamat)</label>
                                <textarea name="sejarah_p2" rows="4"
                                    class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-sm">{{ $kontens['sejarah_p2'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100 dark:border-slate-700/60">

                <!-- Visi & Misi Group -->
                <div class="space-y-4">
                    <h3
                        class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 flex items-center gap-2">
                        <i class="fa-solid fa-bullseye"></i> Visi & Misi Pesantren
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Visi Pesantren</label>
                            <textarea name="visi" rows="3"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-sm">{{ $kontens['visi'] ?? '' }}</textarea>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Misi Pesantren <span
                                    class="text-slate-400 dark:text-slate-500 font-normal">(Gunakan baris baru untuk setiap
                                    poin)</span></label>
                            <textarea name="misi" rows="3"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-sm">{{ $kontens['misi'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="p-6 bg-slate-50/50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-700/60 flex justify-end">
                <button type="submit"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-lg shadow-emerald-900/10 flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan Teks
                </button>
            </div>
        </form>

        <!-- Manajemen Pimpinan & Pengurus -->
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm overflow-hidden transition-colors">
            <div
                class="p-6 sm:p-8 border-b border-slate-100 dark:border-slate-700/60 bg-slate-50/50 dark:bg-slate-800/50 flex items-center gap-3">
                <div
                    class="w-8 h-8 rounded-lg bg-slate-900 dark:bg-slate-700 text-white flex items-center justify-center text-xs font-bold shadow-sm">
                    2</div>
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white">Pimpinan & Dewan
                    Pengasuh</h2>
            </div>

            <div class="p-6 sm:p-8 space-y-8">
                <!-- Form Tambah Pengurus -->
                <form action="{{ route('admin.pengurus.store') }}" method="POST" enctype="multipart/form-data"
                    class="bg-slate-50/80 dark:bg-slate-900/50 p-5 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 grid grid-cols-1 sm:grid-cols-4 gap-4 items-end shadow-inner">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wide">Nama
                            Lengkap & Gelar</label>
                        <input type="text" name="nama" required placeholder="Contoh: KH. Ahmad, Lc."
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-sm">
                    </div>
                    <div class="space-y-1.5">
                        <label
                            class="text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wide">Jabatan</label>
                        <input type="text" name="jabatan" required placeholder="Contoh: Pimpinan Pondok"
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-sm">
                    </div>
                    <div class="space-y-1.5">
                        <label
                            class="text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wide">Foto
                            (Opsional)</label>
                        <input type="file" name="foto"
                            class="w-full text-[10px] text-slate-500 dark:text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 dark:file:bg-emerald-950/60 file:text-emerald-700 dark:file:text-emerald-300 hover:file:bg-emerald-100 cursor-pointer">
                    </div>
                    <div>
                        <button type="submit"
                            class="w-full py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-slate-800 dark:hover:bg-slate-600 text-white text-xs font-bold rounded-xl transition shadow-md flex items-center justify-center gap-2">
                            <i class="fa-solid fa-plus text-[10px]"></i> Tambah Pengurus
                        </button>
                    </div>
                </form>

                <!-- Tabel List Pengurus -->
                <div class="overflow-x-auto rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                        <thead
                            class="bg-slate-100/70 dark:bg-slate-900/80 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-200/60 dark:border-slate-700/60">
                            <tr>
                                <th class="p-3.5 font-bold">Foto</th>
                                <th class="p-3.5 font-bold">Nama</th>
                                <th class="p-3.5 font-bold">Jabatan</th>
                                <th class="p-3.5 font-bold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                            @forelse($kepengurusans as $p)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition">
                                    <td class="p-3.5">
                                        <div
                                            class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden flex items-center justify-center font-bold text-slate-600 dark:text-slate-300 text-xs shadow-sm ring-2 ring-slate-100 dark:ring-slate-700">
                                            @if ($p->foto)
                                                <img src="{{ asset($p->foto) }}" alt="{{ $p->nama }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                {{ substr($p->nama, 0, 1) }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="p-3.5 font-bold text-slate-900 dark:text-white">{{ $p->nama }}</td>
                                    <td class="p-3.5">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800">
                                            {{ $p->jabatan }}
                                        </span>
                                    </td>
                                    <td class="p-3.5 text-center">
                                        <form action="{{ route('admin.pengurus.destroy', $p->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus pengurus ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus Data"
                                                class="w-8 h-8 bg-red-50 dark:bg-red-950/50 hover:bg-red-100 dark:hover:bg-red-900/50 text-red-600 dark:text-red-400 rounded-lg transition inline-flex items-center justify-center shadow-sm">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-slate-400 dark:text-slate-500">
                                        <div class="flex flex-col items-center justify-center space-y-2">
                                            <i
                                                class="fa-solid fa-folder-open text-2xl text-slate-300 dark:text-slate-600"></i>
                                            <span>Belum ada data struktur pengurus yang ditambahkan.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
