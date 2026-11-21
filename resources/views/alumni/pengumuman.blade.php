@extends('layouts.alumni')

@section('title', 'Pengumuman Alumni')

@section('content')
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-black text-slate-900">Pengumuman Alumni</h2>
            <p class="text-xs text-slate-500">
                Pengumuman untuk jenjang alumni Anda dan pengumuman yayasan. Alumni hanya dapat membaca.
            </p>
        </div>

        <div class="space-y-4">
            @forelse ($pengumuman as $item)
                <article class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm">
                    <div class="mb-2 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1 rounded-full border border-indigo-200 bg-indigo-50 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-indigo-700">
                            <i class="fa-solid fa-layer-group"></i> {{ $item->labelTarget() }}
                        </span>
                        <span class="text-[10px] font-semibold text-slate-400">{{ $item->tanggal?->translatedFormat('d F Y') }}</span>
                        @if ($item->pembuat)
                            <span class="text-[10px] text-slate-400">· {{ $item->pembuat->name }}</span>
                        @endif
                    </div>
                    <h3 class="text-lg font-black text-slate-900">{{ $item->judul }}</h3>
                    <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-slate-600">{{ $item->isi }}</p>
                </article>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-200 bg-white px-6 py-14 text-center text-sm text-slate-400">
                    <i class="fa-solid fa-bullhorn mb-3 block text-3xl text-slate-300"></i>
                    Belum ada pengumuman.
                </div>
            @endforelse
        </div>

        <div>{{ $pengumuman->links() }}</div>
    </div>
@endsection
