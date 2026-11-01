@extends('layouts.admin')

@section('title', 'Edit Data Santri - ' . $santri->nama_lengkap)

@section('content')
@php
    $labelCls = 'block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5';
    $inputCls = 'w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/60 text-slate-800 dark:text-slate-100 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none';

    $jenjangList = [
        'MTs', 'MA', 'Madrasah Diniyah Takmiliyah', 'PPS Wustha',
        'Diniyah Formal Wustha', 'Diniyah Formal Ulya', "Ma'had 'Aly",
    ];
@endphp

<div class="max-w-3xl mx-auto py-6 sm:py-8 px-4 sm:px-6 space-y-6">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <span class="inline-block px-2.5 py-0.5 rounded-full bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 text-[10px] font-extrabold uppercase tracking-wider border border-amber-500/20 mb-1">
                Modul Santri
            </span>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Edit Data Santri</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Perbarui data kesantrian {{ $santri->nama_lengkap }}.</p>
        </div>
        <a href="{{ route('admin.santri.index') }}"
            class="self-start sm:self-center bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- ERROR VALIDASI -->
    @if ($errors->any())
        <div class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-300 p-4 rounded-2xl text-xs">
            <p class="font-black mb-1">Data belum bisa disimpan:</p>
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- FORM -->
    <form action="{{ route('admin.santri.update', $santri->id) }}" method="POST"
        class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="{{ $labelCls }}" for="nama_lengkap">Nama Lengkap</label>
                <input id="nama_lengkap" type="text" name="nama_lengkap" required
                    value="{{ old('nama_lengkap', $santri->nama_lengkap) }}" class="{{ $inputCls }}">
            </div>

            <div>
                <label class="{{ $labelCls }}" for="nomor_induk">Nomor Induk</label>
                <input id="nomor_induk" type="text" name="nomor_induk"
                    value="{{ old('nomor_induk', $santri->nomor_induk) }}" class="{{ $inputCls }} font-mono">
            </div>

            <div>
                <label class="{{ $labelCls }}" for="kelas">Kelas</label>
                <input id="kelas" type="text" name="kelas"
                    value="{{ old('kelas', $santri->kelas) }}" class="{{ $inputCls }}">
            </div>

            <div class="sm:col-span-2">
                <label class="{{ $labelCls }}" for="jenjang_pendidikan">Jenjang Pendidikan</label>
                <select id="jenjang_pendidikan" name="jenjang_pendidikan" required class="{{ $inputCls }}">
                    @foreach ($jenjangList as $jenjang)
                        <option value="{{ $jenjang }}"
                            {{ old('jenjang_pendidikan', $santri->jenjang_pendidikan) === $jenjang ? 'selected' : '' }}>
                            {{ $jenjang }}
                        </option>
                    @endforeach
                    {{-- Pertahankan nilai lama jika tidak ada dalam daftar --}}
                    @if ($santri->jenjang_pendidikan && ! in_array($santri->jenjang_pendidikan, $jenjangList))
                        <option value="{{ $santri->jenjang_pendidikan }}" selected>{{ $santri->jenjang_pendidikan }}</option>
                    @endif
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="{{ $labelCls }}" for="nama_wali">Nama Wali</label>
                <input id="nama_wali" type="text" name="nama_wali"
                    value="{{ old('nama_wali', $santri->nama_wali) }}" class="{{ $inputCls }}">
            </div>

            <div class="sm:col-span-2">
                <label class="{{ $labelCls }}" for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" rows="3" class="{{ $inputCls }}">{{ old('alamat', $santri->alamat) }}</textarea>
            </div>
        </div>

        <div class="pt-2 flex flex-col-reverse sm:flex-row gap-2 sm:justify-end">
            <a href="{{ route('admin.santri.index') }}"
                class="text-center bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold px-6 py-3 rounded-xl text-xs transition">
                Batal
            </a>
            <button type="submit"
                class="bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold px-6 py-3 rounded-xl text-xs transition shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
            </button>
        </div>
    </form>

</div>
@endsection
