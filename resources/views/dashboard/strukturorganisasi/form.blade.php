@extends('layouts.admin')

@section('title', ($item ? 'Ubah' : 'Tambah') . ' Struktur Organisasi - Panel Admin')

@section('content')
@php
    $label = \App\Models\StrukturOrganisasi::KATEGORI[$kategori];
    $punyaFoto = in_array($kategori, ['bagan', 'jenjang'], true);
    $namaLabel = match ($kategori) {
        'bagan'   => 'Nama lengkap',
        'jenjang' => 'Nama ketua',
        default   => 'Nama bidang',
    };
    $input = 'w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-900 dark:text-slate-100 px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500';
    $lbl = 'block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5';
    $ikonAwal = old('ikon', $item->ikon ?? '');
@endphp

<div class="max-w-3xl mx-auto space-y-6">

    <div>
        <a href="{{ route('admin.struktur.index', ['kategori' => $kategori]) }}" class="text-xs font-bold text-emerald-600 hover:underline">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white mt-2">{{ $item ? 'Ubah data' : 'Tambah data' }}</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Kategori: <b>{{ $label }}</b></p>
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

    <form action="{{ $item ? route('admin.struktur.update', $item) : route('admin.struktur.store') }}"
        method="POST" enctype="multipart/form-data"
        class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 sm:p-7 space-y-5">
        @csrf
        @if ($item)
            @method('PUT')
        @endif
        <input type="hidden" name="kategori" value="{{ $kategori }}">

        {{-- Foto --}}
        @if ($punyaFoto)
            <div x-data="{ preview: @js($item?->foto_url), hapus: false }" class="flex items-center gap-5">
                <div class="relative w-24 h-24 shrink-0 rounded-full overflow-hidden bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 grid place-items-center text-3xl">
                    <i class="fa-solid" :class="'{{ $ikonAwal ?: 'fa-user' }}'"></i>
                    <img x-show="preview && !hapus" :src="preview" alt="Pratinjau foto" class="absolute inset-0 w-full h-full object-cover" onerror="this.style.display='none'">
                </div>
                <div class="flex-1 space-y-2">
                    <label class="{{ $lbl }}">Foto (opsional)</label>
                    <input type="file" name="foto" accept="image/png,image/jpeg,image/webp"
                        @change="const f = $event.target.files[0]; if (f) { preview = URL.createObjectURL(f); hapus = false }"
                        class="block w-full text-xs text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-600 file:px-3.5 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-emerald-700">
                    <p class="text-[11px] text-slate-400">JPG, PNG, atau WEBP. Maksimal 2 MB. Foto persegi (rasio 1:1) hasilnya paling rapi.</p>
                    @if ($item && $item->foto)
                        <label class="inline-flex items-center gap-2 text-xs text-slate-600 dark:text-slate-300">
                            <input type="checkbox" name="hapus_foto" value="1" x-model="hapus" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                            Hapus foto saat ini
                        </label>
                    @endif
                </div>
            </div>
        @endif

        {{-- Nama --}}
        <div>
            <label class="{{ $lbl }}" for="nama">{{ $namaLabel }} <span class="text-rose-500">*</span></label>
            <input id="nama" type="text" name="nama" value="{{ old('nama', $item->nama ?? '') }}" required maxlength="150" class="{{ $input }}">
        </div>

        {{-- Bagan: jabatan & atasan --}}
        @if ($kategori === 'bagan')
            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="{{ $lbl }}" for="jabatan">Jabatan <span class="text-rose-500">*</span></label>
                    <input id="jabatan" type="text" name="jabatan" value="{{ old('jabatan', $item->jabatan ?? '') }}" required maxlength="150" class="{{ $input }}" placeholder="Contoh: Sekretaris">
                </div>
                <div>
                    <label class="{{ $lbl }}" for="parent_id">Atasan</label>
                    <select id="parent_id" name="parent_id" class="{{ $input }}">
                        <option value="">- Puncak bagan (tanpa atasan) -</option>
                        @php
                            $dilarang = $item
                                ? array_merge([$item->id], \App\Models\StrukturOrganisasi::idKeturunan($item->id))
                                : [];
                        @endphp
                        @foreach ($indukOpsi as $opsi)
                            @continue(in_array($opsi->id, $dilarang, true))
                            <option value="{{ $opsi->id }}" @selected((string) old('parent_id', $item->parent_id ?? '') === (string) $opsi->id)>
                                {{ str_repeat('— ', $opsi->level) }}{{ $opsi->nama }} ({{ $opsi->jabatan }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif

        {{-- Jenjang: kode --}}
        @if ($kategori === 'jenjang')
            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="{{ $lbl }}" for="kode">Kode jenjang <span class="text-rose-500">*</span></label>
                    <input id="kode" type="text" name="kode" value="{{ old('kode', $item->kode ?? '') }}" required maxlength="30" class="{{ $input }}" placeholder="Contoh: MTs">
                </div>
                <div>
                    <label class="{{ $lbl }}" for="keterangan">Keterangan</label>
                    <input id="keterangan" type="text" name="keterangan" value="{{ old('keterangan', $item->keterangan ?? '') }}" maxlength="255" class="{{ $input }}" placeholder="Contoh: Madrasah Tsanawiyah">
                </div>
            </div>
        @endif

        {{-- Bidang: keterangan --}}
        @if ($kategori === 'bidang')
            <div>
                <label class="{{ $lbl }}" for="keterangan">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="3" maxlength="255" class="{{ $input }}" placeholder="Penjelasan singkat tugas bidang ini">{{ old('keterangan', $item->keterangan ?? '') }}</textarea>
            </div>
        @endif

        {{-- Ikon & urutan --}}
        <div class="grid sm:grid-cols-2 gap-5">
            <div x-data="{ ikon: @js($ikonAwal) }">
                <label class="{{ $lbl }}" for="ikon">Ikon</label>
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 shrink-0 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 grid place-items-center">
                        <i class="fa-solid" :class="ikon || 'fa-user'"></i>
                    </span>
                    <select id="ikon" name="ikon" x-model="ikon" class="{{ $input }}">
                        <option value="">- Bawaan -</option>
                        @foreach (\App\Models\StrukturOrganisasi::IKON as $kelas => $nama)
                            <option value="{{ $kelas }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                @if ($punyaFoto)
                    <p class="text-[11px] text-slate-400 mt-1.5">Ikon tampil bila foto belum diunggah.</p>
                @endif
            </div>
            <div>
                <label class="{{ $lbl }}" for="urutan">Urutan tampil</label>
                <input id="urutan" type="number" name="urutan" min="0" max="9999" value="{{ old('urutan', $item->urutan ?? '') }}" class="{{ $input }}" placeholder="Kosongkan untuk otomatis di urutan terakhir">
                <p class="text-[11px] text-slate-400 mt-1.5">Angka kecil tampil lebih dulu.</p>
            </div>
        </div>

        {{-- Status --}}
        <div>
            <input type="hidden" name="aktif" value="0">
            <label class="inline-flex items-center gap-2.5 text-sm text-slate-700 dark:text-slate-200">
                <input type="checkbox" name="aktif" value="1" @checked(old('aktif', $item->aktif ?? true)) class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                Tampilkan di halaman publik
            </label>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
            <a href="{{ route('admin.struktur.index', ['kategori' => $kategori]) }}"
                class="px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition">Batal</a>
            <button type="submit"
                class="px-5 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg shadow-emerald-600/20 transition">
                <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan
            </button>
        </div>
    </form>
</div>
@endsection
