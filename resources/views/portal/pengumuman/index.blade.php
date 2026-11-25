@extends('layouts.portal')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-2xl font-black text-slate-900">Pengumuman</h2>
                <p class="text-xs text-slate-500">
                    Hanya pengumuman untuk jenjang anak Anda dan pengumuman yayasan yang ditampilkan di sini.
                </p>
            </div>

            @if ($slugs->isNotEmpty())
                <form method="GET" action="{{ route('portal.pengumuman.index') }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <select name="jenjang" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 sm:w-auto">
                        <option value="">Semua jenjang anak</option>
                        @foreach ($daftarJenjang as $j)
                            @if ($slugs->contains($j['slug']))
                                <option value="{{ $j['slug'] }}" @selected(\App\Support\Jenjang::normalisasi($filterJenjang) === $j['slug'])>{{ $j['kode'] }}</option>
                            @endif
                        @endforeach
                    </select>
                    <button class="rounded-xl bg-emerald-700 px-4 py-2 text-xs font-bold text-white">Filter</button>
                </form>
            @endif
        </div>

        <div class="space-y-4">
            @forelse ($pengumuman as $item)
                <article class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm">
                    <div class="mb-2 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-emerald-700">
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
                    Belum ada pengumuman untuk jenjang anak Anda.
                </div>
            @endforelse
        </div>

        <div>{{ $pengumuman->links() }}</div>
    </div>
@endsection
