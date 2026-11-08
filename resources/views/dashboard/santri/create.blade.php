@extends('layouts.admin')

@section('title', 'Tambah Santri Baru - Pondok Pesantren Babul Hasanah')

@section('content')
@php
    $labelCls = 'block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5';
    $inputCls = 'w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/60 text-slate-800 dark:text-slate-100 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none';
    $hintCls = 'text-[10px] text-slate-400 mt-1';
    $errCls = 'text-[11px] text-rose-600 dark:text-rose-400 mt-1';
@endphp

<div class="max-w-3xl mx-auto py-6 sm:py-8 px-4 sm:px-6 space-y-6">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-extrabold uppercase tracking-wider border border-emerald-500/20 mb-1">
                Modul Santri
            </span>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Tambah Santri Baru</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Input data santri secara manual. Untuk banyak santri sekaligus, gunakan Import Excel.</p>
        </div>
        <a href="{{ route('admin.santri.index') }}"
            class="self-start sm:self-center bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

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

    <form action="{{ route('admin.santri.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- DATA SANTRI -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-5">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-900 dark:text-white">Data Santri</h2>
                    <p class="text-[11px] text-slate-400">Informasi kesantrian</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="{{ $labelCls }}" for="nama_lengkap">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input id="nama_lengkap" type="text" name="nama_lengkap" required value="{{ old('nama_lengkap') }}"
                        class="{{ $inputCls }}" placeholder="Contoh: Ahmad Zaky Al-Fatih">
                    @error('nama_lengkap') <p class="{{ $errCls }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="{{ $labelCls }}" for="nomor_induk">Nomor Induk</label>
                    <input id="nomor_induk" type="text" name="nomor_induk" value="{{ old('nomor_induk') }}"
                        class="{{ $inputCls }} font-mono" placeholder="Kosongkan untuk otomatis">
                    <p class="{{ $hintCls }}">Jika kosong, sistem membuat nomor induk otomatis.</p>
                    @error('nomor_induk') <p class="{{ $errCls }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="{{ $labelCls }}" for="kelas">Kelas</label>
                    <input id="kelas" type="text" name="kelas" value="{{ old('kelas') }}"
                        class="{{ $inputCls }}" placeholder="Contoh: 1">
                </div>

                <div class="sm:col-span-2">
                    <label class="{{ $labelCls }}" for="jenjang_pendidikan">Jenjang Pendidikan <span class="text-rose-500">*</span></label>
                    <select id="jenjang_pendidikan" name="jenjang_pendidikan" required class="{{ $inputCls }}">
                        <option value="">-- Pilih Jenjang --</option>
                        @foreach ($jenjangList as $jenjang)
                            <option value="{{ $jenjang }}" {{ old('jenjang_pendidikan') === $jenjang ? 'selected' : '' }}>{{ $jenjang }}</option>
                        @endforeach
                    </select>
                    @error('jenjang_pendidikan') <p class="{{ $errCls }}">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="{{ $labelCls }}" for="alamat">Alamat</label>
                    <textarea id="alamat" name="alamat" rows="3" class="{{ $inputCls }}"
                        placeholder="Alamat lengkap santri">{{ old('alamat') }}</textarea>
                </div>
            </div>
        </div>

        <!-- DATA WALI & AKUN PORTAL -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-5">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <i class="fa-solid fa-people-roof"></i>
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-900 dark:text-white">Wali Santri & Akun Portal</h2>
                    <p class="text-[11px] text-slate-400">Email milik orang tua/wali dipakai untuk login Portal Wali Santri</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="{{ $labelCls }}" for="nama_wali">Nama Wali</label>
                    <input id="nama_wali" type="text" name="nama_wali" value="{{ old('nama_wali') }}"
                        class="{{ $inputCls }}" placeholder="Contoh: Budi Santoso">
                </div>

                <div>
                    <label class="{{ $labelCls }}" for="email_wali">Email Orang Tua / Wali (Login Portal)</label>
                    <input id="email_wali" type="email" name="email_wali" value="{{ old('email_wali') }}"
                        class="{{ $inputCls }}" placeholder="budi@gmail.com">
                    <p class="{{ $hintCls }}">Jika diisi, akun portal wali dibuat otomatis. Jika email sudah terdaftar sebagai wali, santri ditautkan ke akun itu.</p>
                    @error('email_wali') <p class="{{ $errCls }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="{{ $labelCls }}" for="no_hp_wali">Nomor WhatsApp Wali</label>
                    <input id="no_hp_wali" type="text" name="no_hp_wali" value="{{ old('no_hp_wali') }}"
                        class="{{ $inputCls }}" placeholder="081234567890">
                    <p class="{{ $hintCls }}">Dipakai untuk mengirim email dan password login.</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row gap-2 sm:justify-end">
            <a href="{{ route('admin.santri.index') }}"
                class="text-center bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold px-6 py-3 rounded-xl text-xs transition">
                Batal
            </a>
            <button type="submit"
                class="bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold px-6 py-3 rounded-xl text-xs transition shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Santri
            </button>
        </div>
    </form>

</div>
@endsection
