@extends('layouts.alumni')

@section('title', 'Dokumen Alumni')

@section('content')
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-black text-slate-900">Dokumen Kelulusan</h2>
            <p class="text-xs text-slate-500">
                Semua dokumen diunggah oleh Admin Jenjang. Alumni hanya dapat mengunduh, tidak dapat mengunggah sendiri.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($jenisDokumen as $kunci => $label)
                @php $d = $dokumen->get($kunci); @endphp
                <div class="rounded-3xl border {{ $d ? 'border-emerald-200 bg-emerald-50/40' : 'border-dashed border-slate-200 bg-white' }} p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $d ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-400' }}">
                            <i class="fa-solid fa-file-pdf text-lg"></i>
                        </span>
                        @if ($d)
                            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[9px] font-black uppercase tracking-wider text-emerald-700">Tersedia</span>
                        @else
                            <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[9px] font-black uppercase tracking-wider text-slate-400">Belum</span>
                        @endif
                    </div>
                    <h3 class="mt-3 text-sm font-black text-slate-900">{{ $label }}</h3>
                    @if ($d)
                        <p class="mt-0.5 truncate text-[10px] text-slate-500">{{ $d->file_name ?: 'Berkas dokumen' }}</p>
                        <p class="text-[10px] text-slate-400">{{ $d->ukuranManusia() }}{{ $d->created_at ? ' · '.$d->created_at->translatedFormat('d M Y') : '' }}</p>
                        @if ($d->status === 'published')
                            <a href="{{ route('alumni.dokumen.unduh', $d) }}"
                                class="mt-4 inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-emerald-700 px-3 py-2.5 text-[11px] font-black text-white transition hover:bg-emerald-800">
                                <i class="fa-solid fa-download"></i> Unduh Dokumen
                            </a>
                        @else
                            <p class="mt-4 rounded-xl bg-slate-100 px-3 py-2.5 text-center text-[10px] font-bold text-slate-500">Belum diterbitkan</p>
                        @endif
                    @else
                        <p class="mt-1 text-[11px] leading-relaxed text-slate-400">
                            Dokumen belum diunggah Admin Jenjang. Silakan hubungi admin jenjang asal Anda.
                        </p>
                        <span class="mt-4 inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-slate-100 px-3 py-2.5 text-[11px] font-bold text-slate-500">
                            <i class="fa-solid fa-lock"></i> Menunggu Admin
                        </span>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-black text-slate-900">Butuh Legalisir?</h3>
            <p class="mt-1 text-[11px] text-slate-500">
                Anda dapat mengajukan legalisir online. Hasil yang sudah diproses admin akan muncul di menu Legalisir.
            </p>
            <a href="{{ route('alumni.legalisir') }}" class="mt-3 inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2.5 text-[11px] font-black text-white hover:bg-indigo-700">
                <i class="fa-solid fa-stamp"></i> Ajukan Legalisir
            </a>
        </div>
    </div>
@endsection
