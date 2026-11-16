@extends('layouts.portal')

@section('title', 'Profil - Portal Wali Santri')

@section('content')
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-black text-slate-900">Profil Saya</h2>
            <p class="text-xs text-slate-500">Kelola data akun dan kata sandi portal wali santri.</p>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-xs font-bold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Kartu identitas --}}
            <div class="space-y-6 lg:col-span-1">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-4">
                        <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-100 text-lg font-black text-emerald-700">
                            {{ mb_strtoupper(mb_substr($user->name, 0, 2)) }}
                        </span>
                        <div class="min-w-0">
                            <div class="truncate text-sm font-black text-slate-900">{{ $user->name }}</div>
                            <div class="truncate text-[11px] text-slate-400">{{ $user->email }}</div>
                            <span class="mt-1 inline-block rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">
                                {{ $user->labelPeran() }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-5 space-y-3 border-t border-slate-100 pt-5 text-xs">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-slate-400">Nomor HP</span>
                            <span class="font-bold text-slate-700">{{ $user->phone ?: 'Belum diisi' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-slate-400">Anak terhubung</span>
                            <span class="font-bold text-slate-700">{{ $anakList->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-slate-400">Status akun</span>
                            <span class="font-bold text-emerald-600">{{ ucfirst((string) ($user->status ?: 'aktif')) }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Anak Terhubung</h3>
                    <div class="mt-4 space-y-3">
                        @forelse ($anakList as $anak)
                            <a href="{{ route('wali.anak') }}"
                                class="flex items-center justify-between gap-3 rounded-2xl bg-slate-50 px-3 py-2.5 transition hover:bg-slate-100">
                                <div class="min-w-0">
                                    <div class="truncate text-xs font-bold text-slate-800">{{ $anak->nama }}</div>
                                    <div class="text-[10px] text-slate-400">
                                        {{ $anak->jenjang ?: 'Jenjang belum diatur' }}
                                        &middot; {{ $anak->kelas ? 'Kelas '.$anak->kelas : 'Kelas belum diatur' }}
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
                            </a>
                        @empty
                            <p class="text-[11px] text-slate-400">
                                Belum ada anak terhubung. Anak muncul setelah PPDB diverifikasi atau admin
                                menambahkan data dengan email ini.
                            </p>
                        @endforelse
                    </div>
                    <a href="{{ route('wali.anak') }}"
                        class="mt-4 block rounded-xl border border-slate-200 px-4 py-2.5 text-center text-[11px] font-bold text-slate-600 transition hover:bg-slate-50">
                        Kelola Data Anak
                    </a>
                </div>
            </div>

            {{-- Form --}}
            <div class="space-y-6 lg:col-span-2">
                <form method="POST" action="{{ route('wali.profil.update') }}"
                    class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    @csrf
                    @method('PUT')

                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <i class="fa-solid fa-user-pen"></i>
                        </span>
                        <div>
                            <h3 class="text-sm font-black text-slate-900">Data Akun</h3>
                            <p class="text-[11px] text-slate-400">Perbarui nama dan nomor HP yang dapat dihubungi.</p>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-bold text-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            @error('name')
                                <p class="mt-1 text-[10px] font-bold text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Nomor HP</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-bold text-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            @error('phone')
                                <p class="mt-1 text-[10px] font-bold text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Email (tidak dapat diubah)</label>
                            <input type="email" value="{{ $user->email }}" disabled
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-bold text-slate-400">
                            <p class="mt-1 text-[10px] text-slate-400">
                                Email adalah kunci penautan data anak. Hubungi admin bila perlu menggantinya.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end">
                        <button class="rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>

                <form method="POST" action="{{ route('wali.profil.password') }}"
                    class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    @csrf
                    @method('PUT')

                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                            <i class="fa-solid fa-key"></i>
                        </span>
                        <div>
                            <h3 class="text-sm font-black text-slate-900">Ganti Kata Sandi</h3>
                            <p class="text-[11px] text-slate-400">Gunakan minimal 8 karakter yang mudah Anda ingat.</p>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Kata Sandi Saat Ini</label>
                            <input type="password" name="password_lama" required autocomplete="current-password"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-bold text-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            @error('password_lama')
                                <p class="mt-1 text-[10px] font-bold text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Kata Sandi Baru</label>
                            <input type="password" name="password" required autocomplete="new-password"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-bold text-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            @error('password')
                                <p class="mt-1 text-[10px] font-bold text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="password_confirmation" required autocomplete="new-password"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-bold text-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end">
                        <button class="rounded-xl bg-slate-900 px-5 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800">
                            Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
