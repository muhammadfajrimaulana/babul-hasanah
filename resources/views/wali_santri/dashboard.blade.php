@extends('layouts.portal')

@section('title', 'Dashboard Wali Santri')

@section('content')
    @php
        $jumlahAnak = $anakList->count();
        $jumlahNilai = $infoNilai->sum(fn ($baris) => (int) $baris->jumlah);
        $pending = $pendaftaran->filter(fn ($p) => in_array(strtolower((string) $p->status_pendaftaran), ['', 'pending', 'menunggu'], true))->count();
        $diterima = $pendaftaran->filter(fn ($p) => strtolower((string) $p->status_pendaftaran) === 'diterima')->count();
    @endphp

    <div class="space-y-6">
        {{-- Banner --}}
        <div class="rounded-3xl bg-gradient-to-r from-emerald-900 to-slate-900 p-6 sm:p-8 text-white shadow-lg space-y-3">
            <span class="inline-block rounded-full border border-emerald-500/30 bg-emerald-500/20 px-3 py-1 text-[11px] font-bold text-emerald-300">
                Portal Wali Santri
            </span>
            <h2 class="text-2xl font-black sm:text-3xl">Assalamu'alaikum, {{ $user->name }}</h2>
            <p class="max-w-2xl text-xs leading-relaxed text-slate-300 sm:text-sm">
                Pantau data anak, nilai yang sudah diterbitkan, dan status pendaftaran PPDB
                dari satu tempat.
            </p>
            <div class="flex flex-wrap items-center gap-2 pt-1">
                <a href="{{ route('wali.anak') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-4 py-2.5 text-xs font-black text-slate-900 transition hover:bg-emerald-400">
                    <i class="fa-solid fa-children"></i> Data Anak
                </a>
                <a href="{{ route('wali.pendaftaran') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-white/20 px-4 py-2.5 text-xs font-black text-white transition hover:bg-white/10">
                    <i class="fa-solid fa-file-lines"></i> Riwayat Pendaftaran
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-xs font-bold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- Ringkasan --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Anak Terhubung</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><i class="fa-solid fa-children"></i></span>
                </div>
                <div class="mt-3 text-2xl font-black text-slate-900">{{ $jumlahAnak }}</div>
                <div class="text-[11px] text-slate-400">santri terdaftar pada akun ini</div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nilai Terbit</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-sky-50 text-sky-600"><i class="fa-solid fa-chart-line"></i></span>
                </div>
                <div class="mt-3 text-2xl font-black text-slate-900">{{ $jumlahNilai }}</div>
                <div class="text-[11px] text-slate-400">nilai yang boleh dilihat wali</div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">PPDB Diterima</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-amber-50 text-amber-600"><i class="fa-solid fa-circle-check"></i></span>
                </div>
                <div class="mt-3 text-2xl font-black text-slate-900">{{ $diterima }}</div>
                <div class="text-[11px] text-slate-400">pendaftaran yang sudah diterima</div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Menunggu Verifikasi</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-rose-50 text-rose-600"><i class="fa-solid fa-hourglass-half"></i></span>
                </div>
                <div class="mt-3 text-2xl font-black text-slate-900">{{ $pending }}</div>
                <div class="text-[11px] text-slate-400">pendaftaran yang belum diverifikasi</div>
            </div>
        </div>

        @if ($adaNilaiBaru)
            <a href="{{ route('wali.nilai') }}"
                class="flex items-start gap-3 rounded-3xl border border-emerald-200 bg-emerald-50 p-5 transition hover:border-emerald-400">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-sm text-white">
                    <i class="fa-solid fa-bell"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-xs font-black text-emerald-900">Nilai baru telah diterbitkan</p>
                    <p class="text-[11px] leading-relaxed text-emerald-700">
                        Ada nilai yang baru diterbitkan dalam 14 hari terakhir. Klik untuk melihat rinciannya.
                    </p>
                </div>
                <i class="fa-solid fa-chevron-right ml-auto mt-3 shrink-0 text-xs text-emerald-500"></i>
            </a>
        @endif

        {{-- Anak aktif --}}
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-4 lg:col-span-2">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-slate-900">Anak Anda</h3>
                    <a href="{{ route('wali.anak') }}" class="text-[11px] font-bold text-emerald-600 hover:underline">Lihat semua</a>
                </div>

                @forelse ($anakList as $anak)
                    @php($ringkas = $infoNilai->get($anak->key))
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start gap-4">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-sm font-black text-emerald-700">
                                {{ $anak->inisial() }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="text-sm font-black text-slate-900">{{ $anak->nama }}</h4>
                                    @if ($anak->key === $anakAktif?->key)
                                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Aktif</span>
                                    @endif
                                </div>
                                <p class="mt-0.5 font-mono text-[11px] text-slate-400">NISN / NIAT: {{ $anak->nis ?: '—' }}</p>
                                <div class="mt-3 flex flex-wrap gap-2 text-[11px] font-bold">
                                    <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-slate-600">{{ $anak->jenjang ?: 'Jenjang belum diatur' }}</span>
                                    <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-slate-600">{{ $anak->kelas ? 'Kelas '.$anak->kelas : 'Kelas belum diatur' }}</span>
                                    @if ($ringkas && $ringkas->jumlah > 0)
                                        <span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-emerald-700">{{ $ringkas->jumlah }} nilai terbit</span>
                                    @else
                                        <span class="rounded-lg bg-amber-50 px-2.5 py-1 text-amber-700">Belum ada nilai terbit</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex shrink-0 flex-col gap-2">
                                <a href="{{ route('wali.nilai', ['anak' => $anak->key]) }}"
                                    class="rounded-xl bg-slate-900 px-3 py-1.5 text-[11px] font-bold text-white transition hover:bg-slate-800">Nilai</a>
                                <a href="{{ route('portal.nilai', ['anak' => $anak->key]) }}"
                                    class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-center text-[11px] font-bold text-emerald-700 transition hover:bg-emerald-100">Rapor</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-3xl border border-amber-200 bg-amber-50 p-8 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-lg text-amber-600">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <h3 class="mt-3 text-lg font-bold text-amber-900">Belum Ada Data Anak Terhubung</h3>
                        <p class="mx-auto mt-2 max-w-md text-xs leading-relaxed text-amber-700 sm:text-sm">
                            Akun ini belum dikaitkan dengan data santri. Anak akan muncul setelah pendaftaran
                            PPDB diverifikasi admin atau admin menambahkan data anak dengan email akun ini
                            (<span class="font-mono">{{ $user->email }}</span>).
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

            {{-- Pendaftaran terbaru --}}
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-slate-900">Pendaftaran Terbaru</h3>
                    <a href="{{ route('wali.pendaftaran') }}" class="text-[11px] font-bold text-emerald-600 hover:underline">Semua</a>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    @forelse ($pendaftaran->take(5) as $daftar)
                        @php($status = strtolower((string) $daftar->status_pendaftaran))
                        <a href="{{ route('wali.pendaftaran') }}"
                            class="flex items-start justify-between gap-3 border-b border-slate-100 py-3 last:border-0 last:pb-0 first:pt-0">
                            <div class="min-w-0">
                                <div class="truncate text-xs font-bold text-slate-800">{{ $daftar->nama_lengkap }}</div>
                                <div class="text-[10px] text-slate-400">
                                    {{ $daftar->jenjang_tujuan ?: 'Jenjang belum diisi' }}
                                    &middot; {{ $daftar->tahun_ajaran ?? $daftar->created_at?->format('Y') }}
                                </div>
                            </div>
                            <span @class([
                                'shrink-0 rounded-full px-2 py-0.5 text-[10px] font-bold',
                                'bg-emerald-50 text-emerald-700' => $status === 'diterima',
                                'bg-rose-50 text-rose-700' => $status === 'ditolak',
                                'bg-amber-50 text-amber-700' => ! in_array($status, ['diterima', 'ditolak'], true),
                            ])>
                                {{ $status === '' ? 'Menunggu' : ucfirst($status) }}
                            </span>
                        </a>
                    @empty
                        <p class="py-6 text-center text-xs text-slate-400">Belum ada riwayat pendaftaran.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
