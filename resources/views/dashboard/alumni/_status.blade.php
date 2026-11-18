{{--
    Badge status Data Alumni.

    - Alumni : tertaut santri yang sudah lulus (atau data lama tanpa status).
    - Santri : tertaut santri yang masih aktif.
    - Manual : tidak tertaut ke Data Santri.

    Parameter: $alumni (App\Models\Alumni), opsional $tautanSiap (bool).
--}}
@php
    $alumniStatus = $alumni ?? null;
    $tautanStatus = $tautanSiap ?? \App\Support\Kelulusan::siapTautan();
    $santriStatus = $alumniStatus && $tautanStatus ? $alumniStatus->santri : null;
@endphp

@if ($santriStatus)
    @if ($santriStatus->status_kelulusan === 'aktif')
        <span
            class="inline-flex items-center gap-1 rounded-full bg-indigo-100 px-2.5 py-0.5 text-[10px] font-bold text-indigo-800 dark:bg-indigo-500/20 dark:text-indigo-300"
            title="Tertaut ke Data Santri yang masih aktif">
            <i class="fa-solid fa-link text-[8px]"></i> Santri
        </span>
    @else
        <span
            class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300"
            title="Alumni resmi dari kelulusan Data Santri (No. Induk {{ $santriStatus->nomor_induk ?: '-' }})">
            <i class="fa-solid fa-user-graduate text-[8px]"></i> Alumni
        </span>
    @endif
@else
    <span
        class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300"
        title="Ditambahkan manual, tidak tertaut Data Santri">
        Manual
    </span>
@endif
