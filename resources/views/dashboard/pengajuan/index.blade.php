@extends('layouts.admin')

@section('title', 'Pengajuan Perubahan Data - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white">Pengajuan Perubahan Data</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Wali hanya bisa mengajukan; Admin Jenjang yang menyetujui. Setiap keputusan tercatat di log aktivitas.
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

        <form method="GET" action="{{ route('admin.pengajuan.index') }}" class="flex flex-col gap-2 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:flex-row sm:items-center">
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
            @forelse ($pengajuan as $item)
                @php
                    $badge = match ($item->status) {
                        'disetujui' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                        'ditolak' => 'bg-rose-100 text-rose-700 border-rose-200',
                        default => 'bg-amber-100 text-amber-700 border-amber-200',
                    };
                @endphp
                <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900" x-data="{ tolak: false }">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-lg border px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider {{ $badge }}">{{ $item->labelStatus() }}</span>
                                <span class="text-[10px] font-semibold text-slate-400">{{ $item->created_at?->translatedFormat('d M Y H:i') }}</span>
                            </div>
                            <h3 class="mt-1.5 text-sm font-black text-slate-900 dark:text-white">
                                {{ $item->santri?->nama_lengkap ?? 'Santri' }}
                                <span class="text-[11px] font-semibold text-slate-400">
                                    · {{ \App\Support\Jenjang::label($item->santri?->jenjang_pendidikan) }}
                                    {{ $item->santri?->kelas ? '· '.$item->santri->kelas : '' }}
                                </span>
                            </h3>
                            <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                                <i class="fa-solid fa-user mr-1 text-slate-400"></i> Diajukan oleh {{ $item->wali?->name ?? '-' }}
                            </p>
                        </div>

                        @if ($bolehProses && $item->status === 'menunggu')
                            <div class="flex shrink-0 flex-wrap items-center gap-2">
                                <button type="button" @click="tolak = !tolak"
                                    class="rounded-xl bg-rose-50 px-3.5 py-2 text-[11px] font-black text-rose-700 hover:bg-rose-100 dark:bg-rose-500/10 dark:text-rose-300">
                                    <i class="fa-solid fa-xmark mr-1"></i> Tolak
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3 dark:border-slate-800 dark:bg-slate-800/40">
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Bagian Data</p>
                            <p class="mt-0.5 text-[12px] font-bold text-slate-700 dark:text-slate-200">{{ $bidang[$item->bidang] ?? $item->bidang }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3 dark:border-slate-800 dark:bg-slate-800/40">
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Nilai Lama</p>
                            <p class="mt-0.5 break-words text-[12px] font-semibold text-slate-500">{{ $item->nilai_lama ?: '-' }}</p>
                        </div>
                        <div class="rounded-xl border border-emerald-100 bg-emerald-50/60 p-3 dark:border-emerald-900/40 dark:bg-emerald-950/20">
                            <p class="text-[10px] font-black uppercase tracking-wider text-emerald-600">Diajukan Menjadi</p>
                            <p class="mt-0.5 break-words text-[12px] font-black text-emerald-800 dark:text-emerald-300">{{ $item->nilai_baru }}</p>
                        </div>
                    </div>

                    @if ($item->alasan)
                        <p class="mt-2 rounded-xl bg-slate-50 px-3 py-2 text-[11px] leading-relaxed text-slate-600 dark:bg-slate-800/60 dark:text-slate-300">
                            <b>Alasan wali:</b> {{ $item->alasan }}
                        </p>
                    @endif
                    @if ($item->catatan_admin)
                        <p class="mt-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[11px] leading-relaxed text-slate-600 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-300">
                            <b>Catatan admin:</b> {{ $item->catatan_admin }}
                            @if ($item->pemroses) <span class="text-slate-400">· {{ $item->pemroses->name }}</span> @endif
                        </p>
                    @endif

                    @if ($bolehProses && $item->status === 'menunggu')
                        <div class="mt-4">
                            <form method="POST" action="{{ route('admin.pengajuan.setujui', $item) }}" class="flex flex-col gap-2 sm:flex-row sm:items-end">
                                @csrf
                                @method('PATCH')
                                <div class="flex-1">
                                    <label class="mb-1 block text-[10px] font-bold text-slate-500">Catatan admin (opsional)</label>
                                    <input type="text" name="catatan_admin" maxlength="1000"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                                        placeholder="Mis. data sudah diverifikasi">
                                </div>
                                <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2.5 text-[11px] font-black text-white hover:bg-emerald-700">
                                    <i class="fa-solid fa-check mr-1"></i> Setujui &amp; Terapkan
                                </button>
                            </form>

                            <form x-show="tolak" x-transition method="POST" action="{{ route('admin.pengajuan.tolak', $item) }}"
                                class="mt-2 flex flex-col gap-2 rounded-2xl border border-rose-100 bg-rose-50/60 p-3 dark:border-rose-900/40 dark:bg-rose-950/20 sm:flex-row sm:items-end">
                                @csrf
                                @method('PATCH')
                                <div class="flex-1">
                                    <label class="mb-1 block text-[10px] font-bold text-rose-700">Alasan penolakan (wajib)</label>
                                    <input type="text" name="catatan_admin" required maxlength="1000"
                                        class="w-full rounded-xl border border-rose-200 px-3 py-2 text-xs dark:border-rose-900/50 dark:bg-slate-900 dark:text-white"
                                        placeholder="Jelaskan agar wali memahami">
                                </div>
                                <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2.5 text-[11px] font-black text-white hover:bg-rose-700">
                                    <i class="fa-solid fa-paper-plane mr-1"></i> Kirim Penolakan
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-6 py-12 text-center text-xs text-slate-400 dark:border-slate-700 dark:bg-slate-900">
                    <i class="fa-solid fa-inbox mb-2 block text-2xl text-slate-300"></i>
                    Belum ada pengajuan perubahan data dari wali.
                </div>
            @endforelse
        </div>

        <div>{{ $pengajuan->links() }}</div>
    </div>
@endsection
