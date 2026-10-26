@extends('layouts.admin')

@section('title', 'Tambah Alumni - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="max-w-3xl space-y-6">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white">Tambah Alumni</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Isi data lulusan pesantren.</p>
        </div>

        <form action="{{ route('admin.alumni.store') }}" method="POST" enctype="multipart/form-data"
            class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm p-6 space-y-5">
            @csrf
            @include('dashboard.alumni._form', ['alumni' => null])
            <div class="flex gap-2 pt-2">
                <button class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl transition">Simpan</button>
                <a href="{{ route('admin.alumni.index') }}" class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold px-5 py-2.5 rounded-xl">Batal</a>
            </div>
        </form>
    </div>
@endsection
