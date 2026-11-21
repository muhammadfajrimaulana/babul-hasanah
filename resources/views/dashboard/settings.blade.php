@extends('layouts.admin')

@section('title', 'Pengaturan Sistem - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white">Pengaturan Sistem</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Informasi konfigurasi aplikasi. Hanya Superadmin yang dapat membuka halaman ini.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-4 flex items-center gap-3 border-b border-slate-100 pb-3 dark:border-slate-800">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <i class="fa-solid fa-server"></i>
                </span>
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Informasi Aplikasi</h3>
                    <p class="text-[10px] text-slate-400">Dibaca langsung dari konfigurasi server.</p>
                </div>
            </div>

            <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                @foreach ($infoSistem as $label => $nilai)
                    <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3.5 dark:border-slate-800 dark:bg-slate-800/40">
                        <dt class="text-[10px] font-black uppercase tracking-wider text-slate-400">{{ $label }}</dt>
                        <dd class="mt-0.5 break-all text-xs font-bold text-slate-800 dark:text-slate-100">{{ $nilai ?: '-' }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-900/40 dark:bg-amber-950/20">
            <p class="text-[11px] leading-relaxed text-amber-800 dark:text-amber-300">
                <i class="fa-solid fa-shield-halved mr-1"></i>
                Pengaturan koneksi database dan kredensial sengaja tidak ditampilkan. Perubahan konfigurasi dilakukan
                melalui file <span class="font-mono">.env</span> oleh pengelola server.
            </p>
        </div>
    </div>
@endsection
