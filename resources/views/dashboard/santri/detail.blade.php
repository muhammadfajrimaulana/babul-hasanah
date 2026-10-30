@extends('layouts.admin')

@section('title', 'Detail Data Santri - ' . $santri->nama_lengkap)

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:py-8 px-4 sm:px-6 space-y-6">

    <!-- HEADER HALAMAN & TOMBOL KEMBALI -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-extrabold uppercase tracking-wider border border-emerald-500/20 mb-1">
                Modul Santri
            </span>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Detail Informasi Santri</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Informasi lengkap data akademik dan pendaftaran online santri.</p>
        </div>
        <a href="javascript:history.back()" class="self-start sm:self-center bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- GRID LAYOUT UTAMA (2 Kolom di Desktop) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- KARTU 1: DATA SANTRI (AKADEMIK) -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Data Santri</h2>
                        <p class="text-[11px] text-slate-400">Informasi utama status kesantrian</p>
                    </div>
                </div>

                <dl class="grid grid-cols-1 gap-4 text-xs">
                    <div class="bg-slate-50 dark:bg-slate-800/50 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800/80">
                        <dt class="font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider text-[10px] mb-1">Nomor Induk</dt>
                        <dd class="font-bold text-slate-900 dark:text-white text-sm font-mono">{{ $santri->nomor_induk }}</dd>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/50 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800/80">
                        <dt class="font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider text-[10px] mb-1">Nama Lengkap</dt>
                        <dd class="font-bold text-slate-900 dark:text-white text-sm">{{ $santri->nama_lengkap }}</dd>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-slate-50 dark:bg-slate-800/50 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800/80">
                            <dt class="font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider text-[10px] mb-1">Jenjang Pendidikan</dt>
                            <dd class="font-bold text-slate-900 dark:text-white">{{ $santri->jenjang_pendidikan }}</dd>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800/80">
                            <dt class="font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider text-[10px] mb-1">Kelas</dt>
                            <dd class="font-bold text-emerald-600 dark:text-emerald-400">{{ $santri->kelas }}</dd>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/50 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800/80">
                        <dt class="font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider text-[10px] mb-1">Alamat</dt>
                        <dd class="font-medium text-slate-900 dark:text-white leading-relaxed">{{ $santri->alamat }}</dd>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/50 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800/80">
                        <dt class="font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider text-[10px] mb-1">Nama Wali</dt>
                        <dd class="font-bold text-slate-900 dark:text-white">{{ $santri->nama_wali }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- KARTU 2: DATA PENDAFTARAN ONLINE -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Data Pendaftaran Online</h2>
                        <p class="text-[11px] text-slate-400">Arsip data saat proses pendaftaran</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 text-xs">
                    <div class="bg-slate-50 dark:bg-slate-800/50 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                        <div>
                            <span class="font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider text-[10px] block mb-0.5">No Pendaftaran</span>
                            <strong class="font-mono text-emerald-700 dark:text-emerald-400 text-sm">{{ $pendaftaranOnline->no_pendaftaran }}</strong>
                        </div>
                        <span class="bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold px-2.5 py-1 rounded-lg border border-emerald-500/20">Online Track</span>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/50 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800/80">
                        <span class="font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider text-[10px] block mb-0.5">Nama Lengkap & NIK</span>
                        <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $pendaftaranOnline->nama_lengkap }}</div>
                        <div class="text-[11px] font-mono text-slate-400 mt-0.5">NIK: {{ $pendaftaranOnline->nik_santri ?? '-' }}</div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-slate-50 dark:bg-slate-800/50 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800/80">
                            <span class="font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider text-[10px] block mb-0.5">Tempat, Tanggal Lahir</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $pendaftaranOnline->tempat_tanggal_lahir }}</span>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800/80">
                            <span class="font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider text-[10px] block mb-0.5">Jenjang Tujuan</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $pendaftaranOnline->jenjang_tujuan }}</span>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/50 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800/80">
                        <span class="font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider text-[10px] block mb-0.5">Asal Sekolah</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $pendaftaranOnline->asal_sekolah }}</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="bg-slate-50 dark:bg-slate-800/50 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800/80">
                            <span class="font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider text-[10px] block mb-0.5">Email</span>
                            <span class="font-medium text-slate-700 dark:text-slate-300 truncate block">{{ $pendaftaranOnline->email }}</span>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800/80">
                            <span class="font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider text-[10px] block mb-0.5">No HP Orang Tua</span>
                            <span class="font-bold text-slate-900 dark:text-white font-mono">{{ $pendaftaranOnline->no_hp_ortu }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection