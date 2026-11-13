@php
    $user = auth()->user();
    $slugAdmin = $user->jenjangSlug();
    $aksi = $action ?? route('admin.akademik.nilai.index');
@endphp

<form method="GET" action="{{ $aksi }}"
    class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <h3 class="text-sm font-black text-slate-900 dark:text-white">
            <i class="fa-solid fa-filter mr-1.5 text-emerald-600"></i> Pilih Kelas, Mata Pelajaran & Semester
        </h3>
        <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 px-2.5 py-1 text-[10px] font-bold text-emerald-700 dark:text-emerald-300">
            <i class="fa-solid fa-building-columns"></i>
            {{ $slugAdmin ? $user->namaJenjang() : 'Semua jenjang' }}
        </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
        <div class="space-y-1.5">
            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Kelas</label>
            <select name="class_id"
                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                <option value="">Pilih kelas</option>
                @foreach ($daftarKelas as $kelas)
                    <option value="{{ $kelas->id }}" @selected((int) ($filter['class_id'] ?? 0) === $kelas->id)>
                        {{ $kelas->label() }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="space-y-1.5">
            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Mata Pelajaran</label>
            <select name="subject_id"
                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                <option value="">Pilih mata pelajaran</option>
                @foreach ($daftarMapel as $mapel)
                    <option value="{{ $mapel->id }}" @selected((int) ($filter['subject_id'] ?? 0) === $mapel->id)>
                        {{ $mapel->kode }} — {{ $mapel->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="space-y-1.5">
            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Tahun Ajaran</label>
            <select name="tahun_ajaran"
                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                @foreach (\App\Support\Akademik::tahunAjaranPilihan() as $tahun)
                    <option value="{{ $tahun }}" @selected(($filter['tahun'] ?? '') === $tahun)>{{ $tahun }}</option>
                @endforeach
            </select>
        </div>

        <div class="space-y-1.5">
            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Semester</label>
            <select name="semester"
                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                @foreach (\App\Support\Akademik::SEMESTER as $nilai => $label)
                    <option value="{{ $nilai }}" @selected(($filter['semester'] ?? '') === $nilai)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if ($daftarKelas->isEmpty() || $daftarMapel->isEmpty())
        <p class="rounded-xl border border-amber-200 dark:border-amber-500/30 bg-amber-50 dark:bg-amber-500/10 px-3 py-2 text-[11px] font-semibold text-amber-800 dark:text-amber-300">
            <i class="fa-solid fa-triangle-exclamation mr-1"></i>
            Data kelas/mata pelajaran belum tersedia. Pastikan kelas sudah dibuat dan mapel sudah
            ditambahkan untuk jenjang Anda.
        </p>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
        <p class="text-[10px] text-slate-400">
            Hanya nilai berstatus <span class="font-bold">Terbit</span> yang tampil di Portal Wali Santri.
        </p>
        <div class="flex items-center gap-2">
            <a href="{{ $aksi }}"
                class="rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                Reset
            </a>
            <button type="submit"
                class="rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2.5 text-xs font-bold transition inline-flex items-center gap-2">
                <i class="fa-solid fa-magnifying-glass"></i> Tampilkan
            </button>
        </div>
    </div>
</form>
