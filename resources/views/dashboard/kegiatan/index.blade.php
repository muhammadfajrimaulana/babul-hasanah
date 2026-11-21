@extends('layouts.admin')

@section('title', 'Kegiatan Pesantren - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white">Kegiatan Pesantren</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Daftar kegiatan harian dan agenda pesantren. Halaman ini bersifat monitoring (hanya baca).
            </p>
        </div>

        <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-6 py-14 text-center dark:border-slate-700 dark:bg-slate-900">
            <i class="fa-solid fa-calendar-days mb-3 block text-3xl text-slate-300"></i>
            <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum ada data kegiatan yang ditampilkan.</p>
            <p class="mt-1 text-[11px] text-slate-400">
                Kegiatan harian dapat dicatat melalui menu Pembinaan. Data presensi dan setoran tampil pada detail santri.
            </p>
        </div>
    </div>
@endsection
