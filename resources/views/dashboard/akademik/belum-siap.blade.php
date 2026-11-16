@extends('layouts.admin')

@section('title', 'Modul Akademik Belum Siap')

@section('content')
    <div class="max-w-2xl mx-auto bg-white dark:bg-slate-900 rounded-3xl border border-amber-200 dark:border-amber-500/30 shadow-sm p-8 space-y-4">
        <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-300 flex items-center justify-center text-xl">
            <i class="fa-solid fa-database"></i>
        </div>
        <div class="space-y-1">
            <h2 class="text-lg font-black text-slate-900 dark:text-white">Tabel akademik belum tersedia</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                Halaman ini membutuhkan tabel akademik. Untuk skema baru: <code class="font-mono">classes</code>,
                <code class="font-mono">subjects</code>, <code class="font-mono">students</code>, dan
                <code class="font-mono">grades</code>. Untuk data lama: <code class="font-mono">mata_pelajaran</code>
                dan <code class="font-mono">nilai_santri</code>. Migrasi sudah disiapkan di folder
                <code class="font-mono">database/migrations</code>, tetapi belum dijalankan.
            </p>
        </div>
        <div class="bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl p-4 text-xs text-slate-600 dark:text-slate-300 space-y-1">
            <p class="font-bold text-slate-800 dark:text-white">Dijalankan oleh manusia (bukan AI):</p>
            <p class="font-mono">php artisan migrate</p>
            <p class="text-[11px] text-slate-500 dark:text-slate-400">Setelah migrasi selesai, muat ulang halaman ini.</p>
        </div>
        <a href="{{ route('admin.santri.index') }}"
            class="inline-flex items-center gap-2 text-xs font-bold text-emerald-700 dark:text-emerald-400 hover:underline">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Data Santri
        </a>
    </div>
@endsection
