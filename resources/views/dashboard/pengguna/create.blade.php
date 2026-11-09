@extends('layouts.admin')

@php
    $bolehSemua = \App\Support\Akses::superAdmin(auth()->user());
@endphp

@section('title', ($bolehSemua ? 'Tambah Pengguna' : 'Tambah Wali Santri').' - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="max-w-2xl space-y-6">
        <div>
            <a href="{{ route('super.pengguna.index', $bolehSemua ? [] : ['kategori' => 'wali_santri']) }}"
                class="inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-400 hover:text-emerald-600 transition mb-1">
                <i class="fa-solid fa-arrow-left"></i>
                {{ $bolehSemua ? 'Kembali ke daftar pengguna' : 'Kembali ke daftar wali santri' }}
            </a>
            <h2 class="text-xl font-black text-slate-900 dark:text-white">
                {{ $bolehSemua ? 'Tambah Pengguna' : 'Tambah Wali Santri' }}
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $bolehSemua
                    ? 'Buat akun baru untuk masuk ke panel sesuai perannya.'
                    : 'Buat akun wali santri dan tautkan ke santri yang akan dipantau.' }}
            </p>
        </div>

        <form action="{{ route('super.pengguna.store') }}" method="POST"
            class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm p-6 space-y-5">
            @csrf
            @include('dashboard.pengguna._form', ['pengguna' => null])
            <div class="flex gap-2 pt-2">
                <button class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl transition">Simpan</button>
                <a href="{{ route('super.pengguna.index', $bolehSemua ? [] : ['kategori' => 'wali_santri']) }}"
                    class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold px-5 py-2.5 rounded-xl">Batal</a>
            </div>
        </form>
    </div>
@endsection
