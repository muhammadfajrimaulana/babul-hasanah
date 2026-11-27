@extends('layouts.admin')

@section('title', ($item ? 'Edit' : 'Tambah') . ' Kurikulum - Akademik')

@use('App\Support\Akademik')

@section('content')
    @php
        $sedangEdit = (bool) $item;
        $aksi = $sedangEdit
            ? route('admin.akademik.kurikulum.update', $item)
            : route('admin.akademik.kurikulum.store');
        $slugAdmin = auth()->user()->jenjangSlug();
        $inputCls = 'w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20';
        $labelCls = 'block text-[11px] font-bold text-slate-600 dark:text-slate-300';
    @endphp

    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <a href="{{ $sedangEdit ? route('admin.akademik.kurikulum.show', $item) : route('admin.akademik.kurikulum.index') }}"
                class="inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-500 hover:text-emerald-700 transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <h2 class="mt-1 text-xl font-black text-slate-900 dark:text-white">
                {{ $sedangEdit ? 'Edit Kurikulum' : 'Tambah Kurikulum' }}
            </h2>
        </div>

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-xs text-rose-800 space-y-1 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
                @foreach ($errors->all() as $error)
                    <p class="flex items-start gap-2"><i class="fa-solid fa-triangle-exclamation mt-0.5"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ $aksi }}"
            class="space-y-5 rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm">
            @csrf
            @if ($sedangEdit)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="space-y-1.5 sm:col-span-2">
                    <label class="{{ $labelCls }}">Jenjang</label>
                    @if ($slugAdmin)
                        <input type="hidden" name="jenjang" value="{{ $slugAdmin }}">
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-xs font-bold text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
                            {{ auth()->user()->namaJenjang() }}
                        </div>
                    @else
                        <select name="jenjang" required class="{{ $inputCls }}">
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
                    <label class="{{ $labelCls }}">Tahun Ajaran</label>
                    <select name="tahun_ajaran" required class="{{ $inputCls }}">
                        @foreach (Akademik::tahunAjaranPilihan() as $tahun)
                            <option value="{{ $tahun }}" @selected(old('tahun_ajaran', $item?->tahun_ajaran ?? Akademik::tahunAjaranSekarang()) === $tahun)>{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="{{ $labelCls }}">Semester</label>
                    <select name="semester" required class="{{ $inputCls }}">
                        @foreach (Akademik::SEMESTER as $nilai => $label)
                            <option value="{{ $nilai }}" @selected(old('semester', $item?->semester ?? Akademik::semesterSekarang()) === $nilai)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5 sm:col-span-2">
                    <label class="{{ $labelCls }}">Nama Kurikulum</label>
                    <input type="text" name="nama" maxlength="150" required
                        value="{{ old('nama', $item?->nama) }}" placeholder="Contoh: Kurikulum Pesantren 2026/2027"
                        class="{{ $inputCls }}">
                    <p class="text-[10px] text-slate-400">Harus unik untuk jenjang, tahun ajaran, dan semester yang sama.</p>
                </div>

                <div class="space-y-1.5 sm:col-span-2">
                    <label class="{{ $labelCls }}">Deskripsi <span class="font-normal text-slate-400">(opsional)</span></label>
                    <textarea name="deskripsi" rows="4" maxlength="2000" placeholder="Ringkasan fokus kurikulum..."
                        class="{{ $inputCls }}">{{ old('deskripsi', $item?->deskripsi) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end border-t border-slate-100 pt-4 dark:border-slate-800">
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-6 py-3 text-xs font-black text-white transition hover:bg-emerald-800">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Kurikulum
                </button>
            </div>
        </form>
    </div>
@endsection
