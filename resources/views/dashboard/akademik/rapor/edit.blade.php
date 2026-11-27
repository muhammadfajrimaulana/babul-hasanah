@extends('layouts.admin')

@section('title', 'Catatan Wali Kelas - Rapor')

@use('App\Support\Akademik')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <a href="{{ route('admin.akademik.rapor.index', [
                'jenjang' => $rapor->jenjang,
                'kelas' => $rapor->kelas,
                'tahun_ajaran' => $rapor->tahun_ajaran,
                'semester' => $rapor->semester,
            ]) }}"
                class="inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-500 hover:text-emerald-700 transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar rapor
            </a>
            <h2 class="mt-1 text-xl font-black text-slate-900 dark:text-white">Catatan Wali Kelas</h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                Catatan ini ditampilkan di portal wali setelah rapor diterbitkan.
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
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Santri</p>
                    <p class="text-xs font-black text-slate-800 dark:text-slate-100">{{ $rapor->santri?->nama_lengkap ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Kelas</p>
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ $rapor->kelas }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rata-rata</p>
                    <p class="text-xs font-black text-emerald-700 dark:text-emerald-400">{{ Akademik::formatNilai($rapor->rata_rata) }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Peringkat</p>
                    <p class="text-xs font-black text-slate-800 dark:text-slate-100">{{ $rapor->peringkat ?? '—' }} / {{ $rapor->jumlah_santri }}</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.akademik.rapor.update', $rapor) }}"
            class="space-y-4 rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm">
            @csrf
            @method('PUT')

            <div class="space-y-1.5">
                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Catatan Wali Kelas</label>
                <textarea name="catatan_wali_kelas" rows="6" maxlength="2000"
                    placeholder="Tulis ringkasan perkembangan akademik, kehadiran, dan saran untuk orang tua..."
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs leading-relaxed text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">{{ old('catatan_wali_kelas', $rapor->catatan_wali_kelas) }}</textarea>
                <p class="text-[10px] text-slate-400">Maksimal 2000 karakter. Kosongkan bila tidak ada catatan.</p>
            </div>

            <div class="flex justify-end border-t border-slate-100 pt-4 dark:border-slate-800">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-6 py-3 text-xs font-black text-white transition hover:bg-emerald-800">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Catatan
                </button>
            </div>
        </form>
    </div>
@endsection
