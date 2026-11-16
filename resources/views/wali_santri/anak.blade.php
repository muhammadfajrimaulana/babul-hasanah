@extends('layouts.portal')

@section('title', 'Data Anak - Portal Wali Santri')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-black text-slate-900">Data Anak</h2>
                <p class="text-xs text-slate-500">
                    Santri yang terhubung dengan akun <span class="font-semibold">{{ $user->email }}</span>.
                </p>
            </div>
            <a href="{{ route('wali.pendaftaran') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-50">
                <i class="fa-solid fa-file-lines"></i> Riwayat Pendaftaran
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-xs font-bold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @forelse ($anakList as $anak)
            @php
                $idDaftar = $anak->student?->pendaftaran_online_id ?? $anak->santri?->pendaftaran_online_id;
                $daftar = $idDaftar ? $pendaftaran->firstWhere('id', $idDaftar) : null;
                $status = strtolower((string) $daftar?->status_pendaftaran);
            @endphp
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-4 border-b border-slate-100 p-6 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-start gap-4">
                        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-base font-black text-emerald-700">
                            {{ $anak->inisial() }}
                        </span>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-lg font-black text-slate-900">{{ $anak->nama }}</h3>
                                @if ($anak->key === $anakAktif?->key)
                                    <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Anak Aktif</span>
                                @endif
                            </div>
                            <p class="mt-0.5 font-mono text-[11px] text-slate-400">
                                NISN / NIAT: {{ $anak->nis ?: '—' }}
                            </p>
                            <div class="mt-2 flex flex-wrap gap-2 text-[11px] font-bold">
                                <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-slate-600">{{ $anak->jenjang ?: 'Jenjang belum diatur' }}</span>
                                <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-slate-600">{{ $anak->kelas ? 'Kelas '.$anak->kelas : 'Kelas belum diatur' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('wali.nilai', ['anak' => $anak->key]) }}"
                            class="rounded-xl bg-slate-900 px-3.5 py-2 text-[11px] font-bold text-white transition hover:bg-slate-800">
                            <i class="fa-solid fa-chart-line mr-1"></i> Nilai
                        </a>
                        <a href="{{ route('portal.nilai', ['anak' => $anak->key]) }}"
                            class="rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2 text-[11px] font-bold text-emerald-700 transition hover:bg-emerald-100">
                            <i class="fa-solid fa-book-open mr-1"></i> Rapor Digital
                        </a>
                    </div>
                </div>

                <div class="grid gap-4 p-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nama Wali</div>
                        <div class="mt-1 text-xs font-bold text-slate-700">{{ $anak->namaWali ?: $user->name }}</div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Alamat</div>
                        <div class="mt-1 text-xs font-bold text-slate-700">{{ $anak->alamat ?: 'Belum diisi' }}</div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Sumber Data</div>
                        <div class="mt-1 text-xs font-bold text-slate-700">
                            {{ $anak->student ? 'Data santri (admin/PPDB)' : 'Data santri lama' }}
                        </div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status PPDB</div>
                        <div class="mt-1">
                            @if ($daftar)
                                <span @class([
                                    'rounded-full px-2 py-0.5 text-[10px] font-bold',
                                    'bg-emerald-50 text-emerald-700' => $status === 'diterima',
                                    'bg-rose-50 text-rose-700' => $status === 'ditolak',
                                    'bg-amber-50 text-amber-700' => ! in_array($status, ['diterima', 'ditolak'], true),
                                ])>
                                    {{ $status === '' ? 'Menunggu' : ucfirst($status) }}
                                </span>
                            @else
                                <span class="text-xs font-bold text-slate-400">Tidak melalui PPDB</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($daftar)
                    <div class="border-t border-slate-100 bg-slate-50/60 px-6 py-4 text-[11px] text-slate-500">
                        <i class="fa-solid fa-circle-info mr-1 text-slate-400"></i>
                        No. pendaftaran <span class="font-mono font-bold">{{ $daftar->no_pendaftaran }}</span>
                        &middot; jenjang {{ $daftar->jenjang_tujuan ?: '—' }}
                        &middot; didaftarkan {{ $daftar->created_at?->translatedFormat('d M Y') ?? '—' }}
                    </div>
                @endif
            </div>
        @empty
            <div class="rounded-3xl border border-amber-200 bg-amber-50 p-8 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-lg text-amber-600">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h3 class="mt-3 text-lg font-bold text-amber-900">Belum Ada Data Anak</h3>
                <p class="mx-auto mt-2 max-w-md text-xs leading-relaxed text-amber-700 sm:text-sm">
                    Anak hanya dapat dilihat bila terdaftar melalui PPDB yang diverifikasi admin, atau
                    ditambahkan admin dengan email wali <span class="font-mono">{{ $user->email }}</span>.
                </p>
                <div class="mt-4 flex flex-wrap justify-center gap-2">
                    <a href="{{ route('pendaftaran.index') }}"
                        class="rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                        <i class="fa-solid fa-user-plus mr-1"></i> Daftarkan Anak
                    </a>
                    <a href="{{ route('wali.pendaftaran') }}"
                        class="rounded-xl border border-amber-300 bg-white px-4 py-2.5 text-xs font-bold text-amber-700 transition hover:bg-amber-50">
                        Cek Riwayat Pendaftaran
                    </a>
                </div>
            </div>
        @endforelse
    </div>
@endsection
