@extends('layouts.alumni')

@section('title', 'Legalisir Online')

@section('content')
    @php
        $in = 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500';
        $lb = 'mb-1.5 block text-[11px] font-bold text-slate-600';
    @endphp

    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-black text-slate-900">Legalisir Online</h2>
            <p class="text-xs text-slate-500">
                Ajukan legalisir tanpa datang ke pondok. Setelah diproses admin, berkas hasil dapat diunduh di halaman ini.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm lg:col-span-2">
                <h3 class="mb-4 text-sm font-black text-slate-900">Form Pengajuan</h3>
                <form method="POST" action="{{ route('alumni.legalisir.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="{{ $lb }}">Dokumen yang Dilegalisir</label>
                        <select name="jenis_dokumen" required class="{{ $in }}">
                            <option value="">— Pilih dokumen —</option>
                            @foreach ($jenisDokumen as $kunci => $label)
                                <option value="{{ $label }}" @selected(old('jenis_dokumen') === $label)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('jenis_dokumen') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $lb }}">Jumlah Salinan</label>
                        <input type="number" name="jumlah" min="1" max="10" value="{{ old('jumlah', 1) }}" required class="{{ $in }}">
                        @error('jumlah') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-black text-white hover:bg-indigo-700">
                        <i class="fa-solid fa-paper-plane mr-1"></i> Ajukan Legalisir
                    </button>
                </form>
                <p class="mt-3 rounded-xl bg-slate-50 px-3 py-2 text-[10px] leading-relaxed text-slate-500">
                    Maksimal 10 salinan per pengajuan. Pastikan dokumen asli sudah diunggah Admin Jenjang.
                </p>
            </div>

            <div class="space-y-3 lg:col-span-3">
                <h3 class="text-sm font-black text-slate-900">Riwayat Pengajuan</h3>

                @forelse ($legalisir as $item)
                    @php
                        $badge = match ($item->status) {
                            'selesai' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            'ditolak' => 'bg-rose-100 text-rose-700 border-rose-200',
                            'diproses' => 'bg-sky-100 text-sky-700 border-sky-200',
                            default => 'bg-amber-100 text-amber-700 border-amber-200',
                        };
                    @endphp
                    <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-sm">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-lg border px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider {{ $badge }}">{{ $item->labelStatus() }}</span>
                            <span class="text-[10px] font-semibold text-slate-400">{{ $item->created_at?->translatedFormat('d M Y H:i') }}</span>
                        </div>
                        <p class="mt-2 text-sm font-black text-slate-900">{{ $item->jenis_dokumen }}</p>
                        <p class="text-[11px] text-slate-500">{{ $item->jumlah }} salinan</p>
                        @if ($item->catatan)
                            <p class="mt-2 rounded-xl bg-slate-50 px-3 py-2 text-[11px] text-slate-600">
                                <b>Catatan Admin:</b> {{ $item->catatan }}
                            </p>
                        @endif
                        @if ($item->status === 'selesai' && $item->file_hasil)
                            <a href="{{ route('alumni.legalisir.unduh', $item) }}"
                                class="mt-3 inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 px-4 py-2.5 text-[11px] font-black text-white hover:bg-emerald-800">
                                <i class="fa-solid fa-download"></i> Unduh Hasil Legalisir
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-200 bg-white px-6 py-12 text-center text-sm text-slate-400">
                        <i class="fa-solid fa-stamp mb-2 block text-2xl text-slate-300"></i>
                        Belum ada pengajuan legalisir.
                    </div>
                @endforelse

                <div>{{ $legalisir->links() }}</div>
            </div>
        </div>
    </div>
@endsection
