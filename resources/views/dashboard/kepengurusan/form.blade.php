@extends('layouts.admin')

@section('title', ($item ? 'Ubah' : 'Tambah') . ' Kepengurusan - Panel Admin')

@section('content')
@php
    $input = 'w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-900 dark:text-slate-100 px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500';
    $lbl = 'block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5';
@endphp

<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('admin.kepengurusan.index') }}" class="text-xs font-bold text-emerald-600 hover:underline">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white mt-2">{{ $item ? 'Ubah data' : 'Tambah data' }}</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Kartu ini tampil pada bagian Kepengurusan halaman Profil.</p>
    </div>

    @if ($errors->any())
        <div class="rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 px-4 py-3 text-sm">
            <b>Periksa kembali isian Anda:</b>
            <ul class="list-disc pl-5 mt-1">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $item ? route('admin.kepengurusan.update', $item) : route('admin.kepengurusan.store') }}"
        method="POST" enctype="multipart/form-data"
        class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 sm:p-7 space-y-5">
        @csrf
        @if ($item)
            @method('PUT')
        @endif

        <div x-data="{ preview: @js($item?->foto_url), hapus: false }" class="flex items-center gap-5">
            <div class="relative w-24 h-24 shrink-0 rounded-full overflow-hidden bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 grid place-items-center text-3xl font-bold">
                <i class="fa-solid fa-user" x-show="!preview || hapus"></i>
                <img x-show="preview && !hapus" :src="preview" alt="Pratinjau foto" class="absolute inset-0 w-full h-full object-cover" onerror="this.style.display='none'">
            </div>
            <div class="flex-1 space-y-2">
                <label class="{{ $lbl }}">Foto (opsional)</label>
                <input type="file" name="foto" accept="image/png,image/jpeg,image/webp"
                    @change="const f = $event.target.files[0]; if (f) { preview = URL.createObjectURL(f); hapus = false }"
                    class="block w-full text-xs text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-600 file:px-3.5 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-emerald-700">
                <p class="text-[11px] text-slate-400">JPG, PNG, atau WEBP. Maksimal 2 MB. Foto persegi paling rapi untuk kartu profil.</p>
                @if ($item && $item->foto)
                    <label class="inline-flex items-center gap-2 text-xs text-slate-600 dark:text-slate-300">
                        <input type="checkbox" name="hapus_foto" value="1" x-model="hapus" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                        Hapus foto saat ini
                    </label>
                @endif
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="{{ $lbl }}" for="nama">Nama lengkap <span class="text-rose-500">*</span></label>
                <input id="nama" type="text" name="nama" value="{{ old('nama', $item->nama ?? '') }}" required maxlength="150" class="{{ $input }}" placeholder="Contoh: Ustadz Ahmad Zaki">
            </div>
            <div>
                <label class="{{ $lbl }}" for="jabatan">Jabatan <span class="text-rose-500">*</span></label>
                <input id="jabatan" type="text" name="jabatan" value="{{ old('jabatan', $item->jabatan ?? '') }}" required maxlength="150" class="{{ $input }}" placeholder="Contoh: Pimpinan Pesantren">
            </div>
        </div>

        <div>
            <label class="{{ $lbl }}" for="urutan">Urutan tampil</label>
            <input id="urutan" type="number" name="urutan" value="{{ old('urutan', $item->urutan ?? '') }}" min="0" max="9999" class="{{ $input }}">
            <p class="mt-1 text-[11px] text-slate-400">Kosongkan untuk menaruh di urutan terakhir.</p>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-lg shadow-emerald-600/20 transition">
                <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan
            </button>
            <a href="{{ route('admin.kepengurusan.index') }}"
                class="px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
