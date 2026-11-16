@extends('layouts.admin')

@section('title', 'Data Wali Santri - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Data Wali Santri</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Akun wali, anak yang tertaut, dan riwayat pendaftaran PPDB.
                    @if (\App\Support\Akses::dibatasiJenjang(auth()->user()))
                        Data dibatasi ke jenjang <b>{{ auth()->user()->namaJenjang() }}</b>.
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-2 rounded-xl border border-emerald-100 bg-emerald-50 px-3.5 py-2 text-[11px] font-bold text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-400">
                    <i class="fa-solid fa-user-check"></i> {{ number_format($jumlahAktif, 0, ',', '.') }} akun aktif
                </span>
                @if (auth()->user()->bisa('pengguna'))
                    <a href="{{ route('super.pengguna.index', ['kategori' => 'wali_santri']) }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                        <i class="fa-solid fa-users-gear"></i> Kelola Akun
                    </a>
                @endif
            </div>
        </div>

        <form method="GET" class="flex flex-wrap gap-2">
            <input type="text" name="q" value="{{ $filter['q'] ?? '' }}" placeholder="Cari nama, email, atau HP..."
                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 sm:w-80 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
            <select name="status"
                class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                <option value="">Semua status</option>
                <option value="active" @selected(($filter['status'] ?? '') === 'active')>Aktif</option>
                <option value="pending" @selected(($filter['status'] ?? '') === 'pending')>Pending</option>
            </select>
            <button class="rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white">Cari</button>
            @if (request()->filled('q') || request()->filled('status'))
                <a href="{{ route('admin.wali-santri.index') }}"
                    class="rounded-xl bg-slate-100 px-4 py-2.5 text-xs font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">Reset</a>
            @endif
        </form>

        <div class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full border-collapse text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-[10px] uppercase tracking-wider text-slate-400 dark:border-slate-800 dark:bg-slate-800/50">
                            <th class="px-6 py-3 font-extrabold">Wali Santri</th>
                            <th class="px-4 py-3 font-extrabold">Kontak</th>
                            <th class="px-4 py-3 font-extrabold">Status</th>
                            <th class="px-4 py-3 text-center font-extrabold">Anak Tertaut</th>
                            <th class="px-6 py-3 text-right font-extrabold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700 dark:divide-slate-800 dark:text-slate-300">
                        @forelse ($wali as $w)
                            <tr class="transition hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-50 text-[11px] font-black text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400">
                                            {{ mb_strtoupper(mb_substr($w->name, 0, 2)) }}
                                        </span>
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.wali-santri.show', $w) }}"
                                                class="block truncate font-black text-slate-900 hover:text-emerald-600 dark:text-white">
                                                {{ $w->name }}
                                            </a>
                                            <div class="truncate text-[10px] text-slate-400">{{ $w->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div>{{ $w->phone ?: '—' }}</div>
                                    <div class="text-[10px] text-slate-400">Terdaftar {{ $w->created_at?->translatedFormat('d M Y') ?? '—' }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <span @class([
                                        'rounded-full px-2.5 py-1 text-[10px] font-bold',
                                        'bg-emerald-50 text-emerald-700' => $w->sudahAktif(),
                                        'bg-amber-50 text-amber-700' => ! $w->sudahAktif(),
                                    ])>
                                        {{ $w->labelStatus() }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <div class="font-black text-slate-900 dark:text-white">{{ $w->students_count + $w->santri_count }}</div>
                                    <div class="text-[10px] text-slate-400">santri</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.wali-santri.show', $w) }}"
                                            class="rounded-lg bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-600 transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300">
                                            Detail
                                        </a>
                                        @if (auth()->user()->bisa('pengguna'))
                                            <a href="{{ route('super.pengguna.edit', $w) }}"
                                                class="rounded-lg bg-amber-50 px-2.5 py-1 text-[10px] font-bold text-amber-600 transition hover:bg-amber-100 dark:bg-amber-950/50">
                                                Edit
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-xs text-slate-400">
                                    Belum ada akun wali santri yang cocok dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($wali->hasPages())
                <div class="border-t border-slate-100 p-4 dark:border-slate-800">{{ $wali->links() }}</div>
            @endif
        </div>
    </div>
@endsection
