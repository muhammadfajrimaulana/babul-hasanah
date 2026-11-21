@extends('layouts.admin')

@section('title', 'Izin Pulang - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Verifikasi Izin Pulang</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Setujui (terbit kode izin) atau tolak dengan alasan. Tandai “Sudah Kembali” agar status menjadi selesai.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-xl border border-amber-200 bg-amber-50 px-3.5 py-2 text-[11px] font-black text-amber-700">
                    <i class="fa-solid fa-hourglass-half"></i> Menunggu: {{ $antrean }}
                </span>
                <a href="{{ route('admin.izin.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-[11px] font-bold text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                    <i class="fa-solid fa-rotate"></i> Reset
                </a>
            </div>
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

        <form method="GET" action="{{ route('admin.izin.index') }}" class="flex flex-col gap-2 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:flex-row sm:items-center">
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
            @forelse ($izin as $item)
                @php
                    $statusKunci = $item->status;
                @endphp
                <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900" x-data="{ tolak: false }">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-lg border px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider {{ $item->kelasStatus() }}">
                                    {{ $item->labelStatus() }}
                                </span>
                                <span class="text-[10px] font-semibold text-slate-400">
                                    Diajukan {{ $item->created_at?->translatedFormat('d M Y H:i') }}
                                </span>
                                @if ($item->kode_izin)
                                    <span class="rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 font-mono text-[10px] font-black text-emerald-700">
                                        {{ $item->kode_izin }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="mt-1.5 text-sm font-black text-slate-900 dark:text-white">
                                {{ $item->santri?->nama_lengkap ?? 'Santri' }}
                                <span class="text-[11px] font-semibold text-slate-400">
                                    · {{ \App\Support\Jenjang::label($item->santri?->jenjang_pendidikan) }} {{ $item->santri?->kelas ? '· '.$item->santri->kelas : '' }}
                                </span>
                            </h3>
                            <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                                <i class="fa-solid fa-user mr-1 text-slate-400"></i> Wali: {{ $item->wali?->name ?? '-' }}
                                &nbsp;·&nbsp;
                                <i class="fa-solid fa-phone mr-1 text-slate-400"></i> Penjemput: {{ $item->penjemput }}
                            </p>
                            <p class="mt-2 rounded-xl bg-slate-50 px-3 py-2 text-[11px] leading-relaxed text-slate-600 dark:bg-slate-800/60 dark:text-slate-300">
                                <b>Alasan:</b> {{ $item->alasan }}
                            </p>
                            <div class="mt-2 flex flex-wrap gap-3 text-[11px] font-semibold text-slate-500 dark:text-slate-400">
                                <span><i class="fa-solid fa-plane-departure mr-1 text-emerald-500"></i> Pulang: {{ $item->tanggal_pulang?->translatedFormat('d M Y') ?? '-' }}</span>
                                <span><i class="fa-solid fa-plane-arrival mr-1 text-sky-500"></i> Kembali: {{ $item->tanggal_kembali?->translatedFormat('d M Y') ?? '-' }}</span>
                                @if ($item->kembali_pada)
                                    <span><i class="fa-solid fa-check mr-1 text-emerald-500"></i> Kembali tercatat: {{ $item->kembali_pada->translatedFormat('d M Y H:i') }}</span>
                                @endif
                            </div>
                            @if ($item->alasan_penolakan)
                                <p class="mt-2 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-[11px] leading-relaxed text-rose-700 dark:border-rose-900/50 dark:bg-rose-950/30 dark:text-rose-300">
                                    <b>Alasan penolakan:</b> {{ $item->alasan_penolakan }}
                                </p>
                            @endif
                            @if ($item->penyetuju)
                                <p class="mt-1 text-[10px] text-slate-400">Diproses oleh {{ $item->penyetuju->name }}{{ $item->disetujui_pada ? ' · '.$item->disetujui_pada->translatedFormat('d M Y H:i') : '' }}</p>
                            @endif
                        </div>

                        @if ($bolehVerifikasi)
                            <div class="flex shrink-0 flex-wrap items-center gap-2 sm:flex-col sm:items-stretch">
                                @if ($statusKunci === \App\Models\Izin::MENUNGGU)
                                    <form method="POST" action="{{ route('admin.izin.setujui', $item) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="w-full rounded-xl bg-emerald-600 px-3.5 py-2 text-[11px] font-black text-white transition hover:bg-emerald-700">
                                            <i class="fa-solid fa-check mr-1"></i> Setujui
                                        </button>
                                    </form>
                                    <button type="button" @click="tolak = !tolak"
                                        class="rounded-xl bg-rose-50 px-3.5 py-2 text-[11px] font-black text-rose-700 transition hover:bg-rose-100 dark:bg-rose-500/10 dark:text-rose-300">
                                        <i class="fa-solid fa-xmark mr-1"></i> Tolak
                                    </button>
                                @elseif (in_array($statusKunci, [\App\Models\Izin::DISETUJUI, \App\Models\Izin::TERLAMBAT], true))
                                    <form method="POST" action="{{ route('admin.izin.kembali', $item) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="w-full rounded-xl bg-sky-600 px-3.5 py-2 text-[11px] font-black text-white transition hover:bg-sky-700">
                                            <i class="fa-solid fa-arrow-rotate-left mr-1"></i> Sudah Kembali
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>

                    @if ($bolehVerifikasi && $statusKunci === \App\Models\Izin::MENUNGGU)
                        <form x-show="tolak" x-transition method="POST" action="{{ route('admin.izin.tolak', $item) }}"
                            class="mt-4 space-y-2 rounded-2xl border border-rose-100 bg-rose-50/60 p-4 dark:border-rose-900/40 dark:bg-rose-950/20">
                            @csrf
                            @method('PATCH')
                            <label class="block text-[11px] font-bold text-rose-700 dark:text-rose-300">Alasan Penolakan (wajib, terlihat oleh wali)</label>
                            <textarea name="alasan_penolakan" rows="2" required maxlength="1000"
                                class="w-full rounded-xl border border-rose-200 px-4 py-2.5 text-xs dark:border-rose-900/50 dark:bg-slate-900 dark:text-white"></textarea>
                            <button type="submit"
                                class="rounded-xl bg-rose-600 px-4 py-2.5 text-[11px] font-black text-white hover:bg-rose-700">
                                <i class="fa-solid fa-paper-plane mr-1"></i> Kirim Penolakan
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-6 py-12 text-center text-xs text-slate-400 dark:border-slate-700 dark:bg-slate-900">
                    <i class="fa-solid fa-calendar-check mb-2 block text-2xl text-slate-300"></i>
                    Belum ada pengajuan izin pulang.
                </div>
            @endforelse
        </div>

        <div>{{ $izin->links() }}</div>
    </div>
@endsection
