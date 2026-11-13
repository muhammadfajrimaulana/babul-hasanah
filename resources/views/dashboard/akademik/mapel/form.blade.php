@extends('layouts.admin')

@section('title', ($item ? 'Edit' : 'Tambah') . ' Mata Pelajaran - Akademik')

@section('content')
    @php
        $user = auth()->user();
        $slugAdmin = $user->jenjangSlug();
        $sedangEdit = (bool) $item;
        $aksi = $sedangEdit
            ? route('admin.akademik.mapel.update', $item)
            : route('admin.akademik.mapel.store');
        $aktifLama = old('aktif', $item?->aktif ?? true);
    @endphp

    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <a href="{{ route('admin.akademik.mapel.index', $jenjang ? ['jenjang' => $jenjang] : []) }}"
                class="inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-500 dark:text-slate-400 hover:text-emerald-700 dark:hover:text-emerald-400 transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar mata pelajaran
            </a>
            <h2 class="mt-1 text-xl font-black text-slate-900 dark:text-white">
                {{ $sedangEdit ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran' }}
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Jenjang terkunci sesuai akun admin. Kode harus unik di dalam satu jenjang.
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 dark:border-rose-500/30 bg-rose-50 dark:bg-rose-500/10 px-4 py-3.5 text-xs text-rose-800 dark:text-rose-300 space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="flex items-start gap-2"><i class="fa-solid fa-triangle-exclamation mt-0.5"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ $aksi }}"
            class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-5">
            @csrf
            @if ($sedangEdit)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5 sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Jenjang</label>
                    @if ($slugAdmin)
                        <input type="hidden" name="jenjang" value="{{ $slugAdmin }}">
                        <div class="rounded-xl border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10 px-3 py-2.5 text-xs font-bold text-emerald-800 dark:text-emerald-300">
                            {{ $user->namaJenjang() }}
                        </div>
                    @else
                        <select name="jenjang" required
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            <option value="">Pilih jenjang</option>
                            @foreach ($daftarJenjang as $j)
                                <option value="{{ $j['slug'] }}" @selected(old('jenjang', $jenjang) === $j['slug'])>
                                    {{ $j['kode'] }} — {{ $j['nama'] }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Kode</label>
                    <input type="text" name="kode" maxlength="30" required
                        value="{{ old('kode', $item?->kode) }}" placeholder="Contoh: FQH"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-bold uppercase text-slate-800 dark:text-slate-100 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                    <p class="text-[10px] text-slate-400">Singkatan unik, otomatis huruf besar.</p>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">KKM</label>
                    <input type="number" name="kkm" min="0" max="100" required
                        value="{{ old('kkm', $item?->kkm ?? 70) }}"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-bold text-slate-800 dark:text-slate-100 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                    <p class="text-[10px] text-slate-400">Nilai minimum ketuntasan (0–100).</p>
                </div>

                <div class="space-y-1.5 sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Nama Mata Pelajaran</label>
                    <input type="text" name="nama" maxlength="150" required
                        value="{{ old('nama', $item?->nama) }}" placeholder="Contoh: Fiqih"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-800 dark:text-slate-100 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                </div>

                <div class="space-y-1.5 sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Kelompok <span class="font-normal text-slate-400">(opsional)</span></label>
                    <input type="text" name="kelompok" maxlength="50"
                        value="{{ old('kelompok', $item?->kelompok) }}" placeholder="Contoh: Diniyah / Umum / Tahfidz"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-800 dark:text-slate-100 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                </div>

                <div class="sm:col-span-2">
                    <label class="inline-flex items-center gap-2.5 cursor-pointer">
                        <input type="hidden" name="aktif" value="0">
                        <input type="checkbox" name="aktif" value="1" @checked((bool) $aktifLama)
                            class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200">Aktifkan mata pelajaran ini</span>
                    </label>
                    <p class="mt-1 text-[10px] text-slate-400">Mata pelajaran nonaktif tidak muncul di pilihan input nilai, tetapi riwayat tetap tersimpan.</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                <p class="text-[10px] text-slate-400">Perubahan akan langsung memengaruhi pilihan pada halaman input nilai.</p>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.akademik.mapel.index', $jenjang ? ['jenjang' => $jenjang] : []) }}"
                        class="rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2.5 text-xs font-bold transition inline-flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> {{ $sedangEdit ? 'Simpan Perubahan' : 'Simpan Mata Pelajaran' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
