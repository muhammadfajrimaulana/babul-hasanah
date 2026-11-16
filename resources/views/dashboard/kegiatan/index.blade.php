@extends('layouts.admin')

@section('title', 'Manajemen Kegiatan Santri - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Kegiatan & Aktivitas Santri</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Modul pengelolaan kegiatan sedang disiapkan.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}"
                class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 text-xs font-bold px-4 py-2.5 rounded-xl transition inline-flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>

        <div
            class="bg-white dark:bg-slate-900 rounded-3xl border border-amber-200 dark:border-amber-500/30 shadow-sm p-8 text-center space-y-3">
            <div
                class="mx-auto w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-300 flex items-center justify-center text-xl">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <h3 class="text-base font-black text-slate-900 dark:text-white">Modul kegiatan belum siap</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-lg mx-auto">
                Halaman ini belum memiliki data untuk ditampilkan. Silakan gunakan menu lain sementara waktu.
            </p>
        </div>
    </div>
@endsection
