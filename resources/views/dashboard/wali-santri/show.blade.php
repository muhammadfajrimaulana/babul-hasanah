@extends('layouts.admin')

@section('title', 'Detail Wali Santri - Pondok Pesantren Babul Hasanah')

@section('content')
    @php
        $statusBadge = function ($status) {
            $s = strtolower(trim((string) $status));

            return match (true) {
                $s === 'diterima' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400',
                $s === 'ditolak' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-400',
                default => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400',
            };
        };
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2 text-[11px] font-bold text-slate-400">
                    <a href="{{ route('admin.wali-santri.index') }}" class="hover:text-emerald-600">Data Wali Santri</a>
                    <i class="fa-solid fa-chevron-right text-[8px]"></i>
                    <span>Detail</span>
                </div>
                <h2 class="mt-1 text-xl font-black text-slate-900 dark:text-white">{{ $wali->name }}</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $wali->email }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.wali-santri.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
                @if (auth()->user()->bisa('pengguna'))
                    <a href="{{ route('super.pengguna.edit', $wali) }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                        <i class="fa-solid fa-user-pen"></i> Edit Akun
                    </a>
                @endif
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status Akun</div>
                <div class="mt-2 text-lg font-black {{ $wali->sudahAktif() ? 'text-emerald-600' : 'text-amber-600' }}">
                    {{ $wali->labelStatus() }}
                </div>
            </div>
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Anak Tertaut</div>
                <div class="mt-2 text-2xl font-black text-slate-900 dark:text-white">{{ $anak->count() }}</div>
            </div>
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Pendaftaran PPDB</div>
                <div class="mt-2 text-2xl font-black text-slate-900 dark:text-white">{{ $pendaftaran->count() }}</div>
            </div>
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nomor HP</div>
                <div class="mt-2 text-sm font-black text-slate-900 dark:text-white">{{ $wali->phone ?: 'Belum diisi' }}</div>
            </div>
        </div>

        {{-- Data anak --}}
        <div class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-100 px-6 py-4 dark:border-slate-800">
                <h3 class="text-sm font-black text-slate-900 dark:text-white">Data Anak</h3>
                <p class="text-[11px] text-slate-400">Santri yang terhubung dengan akun wali ini.</p>
            </div>

            @if ($anak->isEmpty())
                <div class="px-6 py-10 text-center text-xs text-slate-400">
                    Belum ada anak yang tertaut. Tautkan melalui verifikasi PPDB atau tambah data santri
                    dengan email wali ini.
                </div>
            @else
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full border-collapse text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50 text-[10px] uppercase tracking-wider text-slate-400 dark:border-slate-800 dark:bg-slate-800/50">
                                <th class="px-6 py-3 font-extrabold">Nama</th>
                                <th class="px-4 py-3 font-extrabold">NISN / NIAT</th>
                                <th class="px-4 py-3 font-extrabold">Jenjang</th>
                                <th class="px-4 py-3 font-extrabold">Kelas</th>
                                <th class="px-6 py-3 font-extrabold">Sumber</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700 dark:divide-slate-800 dark:text-slate-300">
                            @foreach ($anak as $a)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                    <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">{{ $a->nama }}</td>
                                    <td class="px-4 py-4 font-mono text-[11px]">{{ $a->nis ?: '—' }}</td>
                                    <td class="px-4 py-4">{{ $a->jenjang ?: '—' }}</td>
                                    <td class="px-4 py-4">{{ $a->kelas ?: '—' }}</td>
                                    <td class="px-6 py-4 text-[11px] text-slate-500">
                                        {{ $a->student ? 'Data santri / PPDB' : 'Data santri lama' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Riwayat PPDB --}}
        <div class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-100 px-6 py-4 dark:border-slate-800">
                <h3 class="text-sm font-black text-slate-900 dark:text-white">Riwayat Pendaftaran</h3>
                <p class="text-[11px] text-slate-400">Semua pendaftaran dengan email wali ini, termasuk yang belum diverifikasi.</p>
            </div>

            @if ($pendaftaran->isEmpty())
                <div class="px-6 py-10 text-center text-xs text-slate-400">Belum ada pendaftaran PPDB.</div>
            @else
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full border-collapse text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50 text-[10px] uppercase tracking-wider text-slate-400 dark:border-slate-800 dark:bg-slate-800/50">
                                <th class="px-6 py-3 font-extrabold">Pendaftar</th>
                                <th class="px-4 py-3 font-extrabold">No. Pendaftaran</th>
                                <th class="px-4 py-3 font-extrabold">Jenjang</th>
                                <th class="px-4 py-3 font-extrabold">Tahun</th>
                                <th class="px-6 py-3 font-extrabold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700 dark:divide-slate-800 dark:text-slate-300">
                            @foreach ($pendaftaran as $p)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900 dark:text-white">{{ $p->nama_lengkap }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $p->asal_sekolah ?: 'Asal sekolah belum diisi' }}</div>
                                    </td>
                                    <td class="px-4 py-4 font-mono text-[11px]">{{ $p->no_pendaftaran ?: '—' }}</td>
                                    <td class="px-4 py-4">{{ $p->jenjang_tujuan ?: '—' }}</td>
                                    <td class="px-4 py-4">{{ $p->tahun_ajaran ?: $p->created_at?->format('Y') }}</td>
                                    <td class="px-6 py-4">
                                        @php($status = strtolower((string) $p->status_pendaftaran))
                                        <span class="rounded-full px-2.5 py-1 text-[10px] font-bold {{ $statusBadge($status) }}">
                                            {{ $status === '' ? 'Menunggu' : ucfirst($status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
