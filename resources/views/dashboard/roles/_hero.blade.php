@php
    $gradien = $gradien ?? 'from-emerald-800 via-emerald-900 to-emerald-950';
@endphp

<div class="relative overflow-hidden rounded-3xl bg-gradient-to-br {{ $gradien }} text-white p-6 sm:p-8 shadow-xl shadow-emerald-950/20">
    <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full bg-white/5"></div>
    <div class="absolute -bottom-20 right-16 h-40 w-40 rounded-full bg-amber-300/10"></div>

    <div class="relative space-y-3">
        <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-amber-200">
            <i class="fa-solid {{ $ikon ?? 'fa-shield-halved' }}"></i> {{ $badge }}
        </span>

        <h1 class="text-2xl sm:text-3xl font-black leading-tight">{{ $judul }}</h1>
        <p class="max-w-2xl text-xs sm:text-sm leading-relaxed text-emerald-50/80">{{ $deskripsi }}</p>

        <div class="flex flex-wrap items-center gap-2 pt-1">
            @isset($identitas)
                <span class="inline-flex items-center gap-2 rounded-xl bg-white/10 border border-white/15 px-3 py-1.5 text-[11px] font-bold">
                    <i class="fa-solid fa-layer-group text-amber-300"></i> {{ $identitas }}
                </span>
            @endisset
            <span class="inline-flex items-center gap-2 rounded-xl bg-white/10 border border-white/15 px-3 py-1.5 text-[11px] font-bold">
                <i class="fa-solid fa-user-shield text-amber-300"></i> {{ auth()->user()->name }}
            </span>
            <span class="inline-flex items-center gap-2 rounded-xl bg-white/10 border border-white/15 px-3 py-1.5 text-[11px] font-bold">
                <i class="fa-regular fa-calendar text-amber-300"></i> {{ now()->locale('id')->translatedFormat('d F Y') }}
            </span>
        </div>
    </div>
</div>
