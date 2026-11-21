@extends('layouts.portal')

@section('content')
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-black text-slate-900">Dokumen Anak</h2>
            <p class="text-xs text-slate-500">
                Ijazah, surat keterangan lulus, transkrip, dan sertifikat diunggah oleh Admin Jenjang. Wali tetap bisa mengunduh meskipun anak sudah lulus.
            </p>
        </div>

        @forelse ($anak as $a)
            @php
                $dokumen = $a->dokumen->keyBy('jenis');
                $lulus = $a->alumni !== null || ($a->status_kelulusan ?? null) === 'lulus';
            @endphp
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm">
                <div class="mb-5 flex flex-col gap-2 border-b border-slate-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-black text-slate-900">{{ $a->nama_lengkap }}</h3>
                        <p class="text-[11px] text-slate-500">
                            {{ \App\Support\Jenjang::label($a->jenjang_pendidikan) }}
                            {{ $a->kelas ? '· Kelas '.$a->kelas : '' }}
                            @if ($a->nomor_induk) · NI {{ $a->nomor_induk }} @endif
                        </p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-wider {{ $lulus ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700' }}">
                        {{ $lulus ? 'Alumni / Lulus' : 'Santri Aktif' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($jenisDokumen as $kunci => $label)
                        @php $d = $dokumen->get($kunci); @endphp
                        <div class="rounded-2xl border {{ $d ? 'border-emerald-200 bg-emerald-50/50' : 'border-dashed border-slate-200 bg-slate-50/50' }} p-4">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-[11px] font-black text-slate-800">{{ $label }}</p>
                                    @if ($d)
                                        <p class="mt-0.5 truncate text-[10px] text-slate-500">{{ $d->file_name ?: 'Berkas tersedia' }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $d->ukuranManusia() }}{{ $d->created_at ? ' · '.$d->created_at->translatedFormat('d M Y') : '' }}</p>
                                    @else
                                        <p class="mt-0.5 text-[10px] text-slate-400">Belum diunggah admin.</p>
                                    @endif
                                </div>
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ $d ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-400' }}">
                                    <i class="fa-solid fa-file-pdf text-[11px]"></i>
                                </span>
                            </div>

                            @if ($d && $d->status === 'published')
                                <a href="{{ route('portal.dokumen.unduh', $d) }}"
                                    class="mt-3 inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-emerald-700 px-3 py-2 text-[11px] font-black text-white transition hover:bg-emerald-800">
                                    <i class="fa-solid fa-download"></i> Unduh
                                </a>
                            @else
                                <span class="mt-3 inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-slate-200 px-3 py-2 text-[11px] font-bold text-slate-500">
                                    <i class="fa-solid fa-clock"></i> Belum tersedia
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="rounded-3xl border border-amber-200 bg-amber-50 px-6 py-10 text-center text-sm text-amber-800">
                Akun Anda belum tertaut ke data santri. Hubungi Admin Jenjang untuk menautkan anak Anda.
            </div>
        @endforelse
    </div>
@endsection
