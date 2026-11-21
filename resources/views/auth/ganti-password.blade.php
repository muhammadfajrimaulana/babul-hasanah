@extends('layouts.auth')

@section('title', 'Ganti Password - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="min-h-screen flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 space-y-6">
            <div class="text-center space-y-2">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h1 class="text-xl font-black text-slate-900">Wajib Ganti Password</h1>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Akun Anda masih memakai password sementara. Password sementara hanya berlaku 24 jam.
                    Ganti sekarang sebelum melanjutkan.
                </p>
            </div>

            @if (session('warning'))
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs font-semibold text-amber-800">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ session('warning') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs text-rose-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <p><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('password.change.update') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Password Saat Ini</label>
                    <input type="password" name="password_lama" required autofocus
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Password Baru</label>
                    <input type="password" name="password" required minlength="8"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <p class="mt-1 text-[10px] text-slate-400">Minimal 8 karakter, jangan bagikan ke siapa pun.</p>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Ulangi Password Baru</label>
                    <input type="password" name="password_confirmation" required minlength="8"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <button type="submit"
                    class="w-full rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white py-3 text-sm font-black transition">
                    Simpan Password Baru
                </button>
            </form>

            <form action="{{ route('logout') }}" method="POST" class="text-center">
                @csrf
                <button type="submit" class="text-[11px] font-bold text-slate-400 hover:text-rose-600 transition">
                    Keluar dari akun
                </button>
            </form>
        </div>
    </div>
@endsection
