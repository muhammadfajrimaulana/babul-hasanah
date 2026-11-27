@extends('layouts.admin')

@section('title', 'Buka Kembali Nilai - Akademik')

@use('App\Support\Akademik')

@section('content')
    @php
        $konteks = [
            'jenjang' => $nilai->jenjang,
            'kelas' => $nilai->kelas,
            'mata_pelajaran_id' => $nilai->mata_pelajaran_id,
            'tahun_ajaran' => $nilai->tahun_ajaran,
            'semester' => $nilai->semester,
            'kategori' => $nilai->kategori,
        ];
    @endphp

    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <a href="{{ route('admin.akademik.nilai.index', $konteks) }}"
                class="inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-500 hover:text-emerald-700 transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar nilai
            </a>
            <h2 class="mt-1 text-xl font-black text-slate-900 dark:text-white">Buka Kembali Nilai Terbit</h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                Nilai akan kembali menjadi draft dan tidak tampil di portal wali sampai diterbitkan ulang. Alasan wajib dicatat di log aktivitas.
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-xs text-rose-800 space-y-1 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
                @foreach ($errors->all() as $error)
                    <p class="flex items-start gap-2"><i class="fa-solid fa-triangle-exclamation mt-0.5"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm">
            <p class="text-sm font-black text-slate-900 dark:text-white">{{ $nilai->santri?->nama_lengkap ?? '—' }}</p>
            <p class="mt-0.5 text-[11px] text-slate-500 dark:text-slate-400">
                NIS {{ $nilai->santri?->nomor_induk }} · Kelas {{ $nilai->kelas }} · {{ $nilai->jenjang }}
            </p>
            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Mata Pelajaran</p>
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ $nilai->mataPelajaran?->nama ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Kategori</p>
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ Akademik::labelKategori($nilai->kategori) }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nilai</p>
                    <p class="text-sm font-black text-emerald-700 dark:text-emerald-400">{{ Akademik::formatNilai($nilai->nilai) }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Periode</p>
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ $nilai->tahun_ajaran }} · {{ Akademik::labelSemester($nilai->semester) }}</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.akademik.nilai.buka-kembali', $nilai) }}"
            class="space-y-4 rounded-3xl border border-amber-200 dark:border-amber-500/30 bg-white dark:bg-slate-900 p-6 shadow-sm">
            @csrf
            <div class="space-y-1.5">
                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Alasan Pembukaan <span class="text-rose-600">*</span></label>
                <textarea name="alasan" rows="3" minlength="5" maxlength="255" required
                    placeholder="Contoh: Koreksi nilai UTS setelah verifikasi panitia"
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs text-slate-700 dark:text-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20">{{ old('alasan') }}</textarea>
                <p class="text-[10px] text-slate-400">Wajib minimal 5 karakter dan akan tersimpan di log aktivitas.</p>
            </div>

            <div class="flex flex-col gap-2 border-t border-slate-100 pt-4 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-[10px] text-slate-400">
                    <i class="fa-solid fa-triangle-exclamation mr-1 text-amber-600"></i>
                    Tindakan ini mengubah data sensitif dan dapat dilihat Superadmin di Log Aktivitas.
                </p>
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-6 py-3 text-xs font-black text-slate-900 transition hover:bg-amber-600">
                    <i class="fa-solid fa-lock-open"></i> Buka Kembali Nilai
                </button>
            </div>
        </form>
    </div>
@endsection
