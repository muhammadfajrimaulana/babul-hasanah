@php
    $ikonBg = $ikonBg ?? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400';
    $href = $href ?? null;
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif
    class="group rounded-2xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">{{ $label }}</p>
            <p class="mt-1 text-2xl font-black text-slate-900 dark:text-white">{{ $nilai }}</p>
            @isset($sub)
                <p class="mt-1 text-[10px] font-semibold text-slate-400">{{ $sub }}</p>
            @endisset
        </div>
        <span class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl {{ $ikonBg }}">
            <i class="fa-solid {{ $ikon ?? 'fa-chart-simple' }}"></i>
        </span>
    </div>
</{{ $tag }}>
