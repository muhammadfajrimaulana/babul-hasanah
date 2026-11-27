@php
    $tabNilai = request()->routeIs('admin.akademik.nilai.*');
    $tabTranskrip = request()->routeIs('admin.akademik.transkrip.*');
    $tabJadwal = request()->routeIs('admin.akademik.jadwal.*');
    $tabKurikulum = request()->routeIs('admin.akademik.kurikulum.*');
    $tabRapor = request()->routeIs('admin.akademik.rapor.*');
    $tabMapel = request()->routeIs('admin.akademik.mapel.*');
    $tabClass = fn (bool $aktif) => 'inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold transition '
        . ($aktif
            ? 'bg-emerald-700 text-white shadow-sm'
            : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:border-emerald-400');
@endphp

<div class="flex flex-wrap items-center gap-2">
    <a href="{{ route('admin.akademik.nilai.index') }}" class="{{ $tabClass($tabNilai) }}">
        <i class="fa-solid fa-pen-to-square"></i> Nilai Santri
    </a>
    <a href="{{ route('admin.akademik.transkrip.index') }}" class="{{ $tabClass($tabTranskrip) }}">
        <i class="fa-solid fa-file-lines"></i> Transkrip Nilai
    </a>
    <a href="{{ route('admin.akademik.jadwal.index') }}" class="{{ $tabClass($tabJadwal) }}">
        <i class="fa-solid fa-calendar-week"></i> Jadwal Pelajaran
    </a>
    <a href="{{ route('admin.akademik.kurikulum.index') }}" class="{{ $tabClass($tabKurikulum) }}">
        <i class="fa-solid fa-book-open"></i> Kurikulum
    </a>
    <a href="{{ route('admin.akademik.rapor.index') }}" class="{{ $tabClass($tabRapor) }}">
        <i class="fa-solid fa-file-lines"></i> Rapor Santri
    </a>
    @if (auth()->user()->bisa('akademik.mapel'))
        <a href="{{ route('admin.akademik.mapel.index') }}" class="{{ $tabClass($tabMapel) }}">
            <i class="fa-solid fa-list-check"></i> Mata Pelajaran
        </a>
    @endif
</div>
