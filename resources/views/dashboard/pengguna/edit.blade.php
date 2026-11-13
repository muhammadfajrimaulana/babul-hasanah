@extends('layouts.admin')

@php
    $multiPeran = count($roles ?? []) > 1;
    $kembali = route('super.pengguna.index', $multiPeran ? [] : ['kategori' => 'wali_santri']);
@endphp

@section('title', ($multiPeran ? 'Edit Pengguna' : 'Edit Wali Santri').' - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="max-w-2xl space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ $kembali }}"
                    class="inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-400 hover:text-emerald-600 transition mb-1">
                    <i class="fa-solid fa-arrow-left"></i>
                    {{ $multiPeran ? 'Kembali ke daftar pengguna' : 'Kembali ke daftar wali santri' }}
                </a>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">
                    {{ $multiPeran ? 'Edit Pengguna' : 'Edit Wali Santri' }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Perbarui data akun <span class="font-bold text-slate-700 dark:text-slate-200">{{ $pengguna->name }}</span>.
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm p-6">
            <div class="flex items-center gap-3 pb-5 mb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 flex items-center justify-center font-black">
                    <i class="fa-solid fa-user-pen"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $pengguna->name }}</p>
                    <p class="text-[11px] text-slate-400 truncate">{{ $pengguna->email }}</p>
                </div>
                <span class="ml-auto shrink-0 text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-300">
                    {{ $pengguna->labelPeran() }}
                </span>
            </div>

            <form action="{{ route('super.pengguna.update', $pengguna) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')
                @include('dashboard.pengguna._form', ['pengguna' => $pengguna])
                <div class="flex gap-2 pt-2">
                    <button class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl transition">Simpan Perubahan</button>
                    <a href="{{ $kembali }}" class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold px-5 py-2.5 rounded-xl">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
