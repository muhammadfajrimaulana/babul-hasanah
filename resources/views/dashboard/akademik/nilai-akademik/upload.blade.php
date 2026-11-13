@extends('layouts.admin')

@section('title', 'Unggah Nilai Excel - Akademik')

@use('App\Support\Akademik')

@section('content')
    @php
        $user = auth()->user();
        $bisaUbah = $user->bisa('akademik.ubah');
        $queryKonteks = array_filter([
            'class_id' => $filter['class_id'],
            'subject_id' => $filter['subject_id'],
            'tahun_ajaran' => $filter['tahun'],
            'semester' => $filter['semester'],
        ], fn ($nilai) => $nilai !== null && $nilai !== '');
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-sky-700 dark:text-sky-400">
                    <i class="fa-solid fa-file-arrow-up"></i> Akademik / Unggah Massal
                </div>
                <h2 class="mt-1 text-xl font-black text-slate-900 dark:text-white">Unggah Nilai dari Excel</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-2xl">
                    Unduh template, isi kolom nilai, lalu unggah kembali. Sistem memvalidasi setiap baris
                    dan menyimpan nilai sebagai draft.
                </p>
            </div>
            <a href="{{ route('admin.akademik.nilai.index', $queryKonteks) }}"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-300 hover:border-emerald-400 transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Input Nilai
            </a>
        </div>

        @include('dashboard.akademik._tabs')
        @include('dashboard.akademik._peringatan-jenjang')

        @foreach (['success' => 'emerald', 'error' => 'rose', 'info' => 'sky'] as $jenis => $warna)
            @if (session($jenis))
                <div class="rounded-2xl border border-{{ $warna }}-200 dark:border-{{ $warna }}-500/30 bg-{{ $warna }}-50 dark:bg-{{ $warna }}-500/10 px-4 py-3.5 text-xs font-bold text-{{ $warna }}-800 dark:text-{{ $warna }}-300 flex items-start gap-2.5">
                    <i class="fa-solid {{ $jenis === 'success' ? 'fa-circle-check' : ($jenis === 'error' ? 'fa-triangle-exclamation' : 'fa-circle-info') }} mt-0.5"></i>
                    <span>{{ session($jenis) }}</span>
                </div>
            @endif
        @endforeach

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 dark:border-rose-500/30 bg-rose-50 dark:bg-rose-500/10 px-4 py-3.5 text-xs text-rose-800 dark:text-rose-300 space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="flex items-start gap-2"><i class="fa-solid fa-triangle-exclamation mt-0.5"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (! $bisaUbah)
            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 px-4 py-3.5 text-xs font-bold text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-eye mr-1"></i> Mode monitoring: unggah nilai hanya dapat dilakukan Super Admin dan Admin Jenjang.
            </div>
        @endif

        @include('dashboard.akademik.nilai-akademik._filter', [
            'action' => route('admin.akademik.nilai.upload'),
        ])

        @if (! $filter['lengkap'])
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-8 text-center space-y-2">
                <i class="fa-solid fa-sliders text-2xl text-slate-300"></i>
                <h3 class="text-sm font-black text-slate-900 dark:text-white">Tentukan konteks nilai</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-lg mx-auto">
                    Pilih kelas dan mata pelajaran pada filter di atas. Template dan unggahan Excel
                    selalu terikat pada satu kelas, satu mapel, satu tahun ajaran, dan satu semester.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-600 text-white text-sm shrink-0"><i class="fa-solid fa-download"></i></span>
                        <div>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white">1. Unduh template</h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                Template berisi daftar santri kelas {{ $filter['kelas']->name }} untuk
                                mapel {{ $filter['mapel']->nama }}.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.akademik.nilai.template', $queryKonteks) }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 px-4 py-2.5 text-xs font-bold text-white transition">
                        <i class="fa-solid fa-file-excel"></i> Unduh Template Excel
                    </a>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-sky-600 text-white text-sm shrink-0"><i class="fa-solid fa-upload"></i></span>
                        <div>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white">2. Unggah file nilai</h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                Format .xlsx, .xls, atau .csv. Kolom: nisn, nama, tugas, uts, uas, hafalan, catatan.
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.akademik.nilai.import') }}" enctype="multipart/form-data"
                        class="space-y-3">
                        @csrf
                        @foreach ($queryKonteks as $nama => $nilai)
                            <input type="hidden" name="{{ $nama }}" value="{{ $nilai }}">
                        @endforeach

                        <input type="file" name="file_nilai" accept=".xlsx,.xls,.csv" required
                            @disabled(! $bisaUbah)
                            class="block w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs text-slate-700 dark:text-slate-200 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 dark:file:bg-slate-800 file:px-3 file:py-1.5 file:text-[11px] file:font-bold file:text-slate-700 dark:file:text-slate-200 disabled:opacity-60">

                        <button type="submit" @disabled(! $bisaUbah)
                            class="inline-flex items-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 disabled:opacity-60 px-4 py-2.5 text-xs font-bold text-white transition">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Proses Unggahan
                        </button>
                    </form>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 px-4 py-3.5 text-[11px] text-slate-600 dark:text-slate-300 space-y-1">
                <p class="font-black text-slate-800 dark:text-white">Catatan penting</p>
                <p>• Jangan mengubah kolom <span class="font-mono">nisn</span>; NISN adalah kunci pencocokan santri.</p>
                <p>• Nilai kosong tidak mengubah nilai yang sudah ada. Nilai yang diubah kembali menjadi draft.</p>
                <p>• Baris dengan NISN di luar kelas ini atau nilai bukan angka akan dilewati dan dilaporkan.</p>
            </div>
        @endif
    </div>
@endsection
