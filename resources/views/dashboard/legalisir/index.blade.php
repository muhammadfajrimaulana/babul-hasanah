@extends('layouts.admin')

@section('title', 'Legalisir Alumni - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white">Legalisir Alumni</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Proses pengajuan legalisir online alumni. Unggah berkas hasil agar dapat diunduh alumni setelah status selesai.
            </p>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-xs font-bold text-emerald-700">
                <i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-xs font-bold text-rose-700">
                <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ session('error') }}
            </div>
        @endif

        <form method="GET" action="{{ route('admin.legalisir.index') }}" class="flex flex-col gap-2 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:flex-row sm:items-center">
            <label class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Status:</label>
            <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 sm:w-56">
                <option value="">Semua status</option>
                @foreach ($status as $kunci => $label)
                    <option value="{{ $kunci }}" @selected($filterStatus === $kunci)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white">Terapkan</button>
        </form>

        <div class="space-y-4">
            @forelse ($legalisir as $item)
                @php
                    $badge = match ($item->status) {
                        'selesai' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                        'ditolak' => 'bg-rose-100 text-rose-700 border-rose-200',
                        'diproses' => 'bg-sky-100 text-sky-700 border-sky-200',
                        default => 'bg-amber-100 text-amber-700 border-amber-200',
                    };
                    $nama = $item->alumni?->nama ?? $item->alumni?->santri?->nama_lengkap ?? 'Alumni';
                @endphp
                <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-lg border px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider {{ $badge }}">{{ $item->labelStatus() }}</span>
                                <span class="text-[10px] font-semibold text-slate-400">{{ $item->created_at?->translatedFormat('d M Y H:i') }}</span>
                            </div>
                            <h3 class="mt-1.5 text-sm font-black text-slate-900 dark:text-white">{{ $nama }}</h3>
                            <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                                <i class="fa-solid fa-file-lines mr-1 text-slate-400"></i>
                                <b>{{ $item->jenis_dokumen }}</b> · {{ $item->jumlah }} salinan
                                @if ($item->alumni?->nomor_ijazah)
                                    · Ijazah {{ $item->alumni->nomor_ijazah }}
                                @endif
                            </p>
                            @if ($item->catatan)
                                <p class="mt-2 rounded-xl bg-slate-50 px-3 py-2 text-[11px] text-slate-600 dark:bg-slate-800/60 dark:text-slate-300"><b>Catatan:</b> {{ $item->catatan }}</p>
                            @endif
                            @if ($item->file_hasil)
                                <a href="{{ asset('storage/'.$item->file_hasil) }}" target="_blank" rel="noopener"
                                    class="mt-2 inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 hover:underline">
                                    <i class="fa-solid fa-download"></i> Berkas hasil tersedia
                                </a>
                            @endif
                            @if ($item->pemroses)
                                <p class="mt-1 text-[10px] text-slate-400">Diproses oleh {{ $item->pemroses->name }}</p>
                            @endif
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.legalisir.proses', $item) }}" enctype="multipart/form-data"
                        class="mt-4 grid grid-cols-1 gap-3 border-t border-slate-100 pt-4 dark:border-slate-800 sm:grid-cols-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label class="mb-1.5 block text-[11px] font-bold text-slate-600 dark:text-slate-300">Status</label>
                            <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                @foreach ($status as $kunci => $label)
                                    <option value="{{ $kunci }}" @selected($item->status === $kunci)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-[11px] font-bold text-slate-600 dark:text-slate-300">Catatan</label>
                            <input type="text" name="catatan" value="{{ $item->catatan }}" maxlength="1000"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-[11px] font-bold text-slate-600 dark:text-slate-300">Berkas hasil (PDF)</label>
                            <input type="file" name="file_hasil" accept="application/pdf"
                                class="w-full rounded-xl border border-slate-200 px-3 py-1.5 text-[11px] dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                        </div>
                        <div class="sm:col-span-4">
                            <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2.5 text-[11px] font-black text-white hover:bg-emerald-700">
                                <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Proses
                            </button>
                        </div>
                    </form>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-6 py-12 text-center text-xs text-slate-400 dark:border-slate-700 dark:bg-slate-900">
                    <i class="fa-solid fa-stamp mb-2 block text-2xl text-slate-300"></i>
                    Belum ada pengajuan legalisir.
                </div>
            @endforelse
        </div>

        <div>{{ $legalisir->links() }}</div>
    </div>
@endsection
