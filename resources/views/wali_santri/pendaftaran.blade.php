@extends('layouts.portal')

@section('title', 'Riwayat Pendaftaran - Portal Wali Santri')

@section('content')
    @php
        $statusBadge = function ($status) {
            $s = strtolower(trim((string) $status));

            return match (true) {
                $s === 'diterima' => ['label' => 'Diterima', 'class' => 'bg-emerald-50 text-emerald-700'],
                $s === 'ditolak' => ['label' => 'Ditolak', 'class' => 'bg-rose-50 text-rose-700'],
                default => ['label' => $s === '' ? 'Menunggu' : ucfirst($s), 'class' => 'bg-amber-50 text-amber-700'],
            };
        };

        $jumlahPending = $pendaftaran->filter(fn ($p) => in_array(strtolower((string) $p->status_pendaftaran), ['', 'pending', 'menunggu'], true))->count();
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-black text-slate-900">Riwayat Pendaftaran</h2>
                <p class="text-xs text-slate-500">
                    Pendaftaran PPDB dengan email wali <span class="font-semibold">{{ $user->email }}</span>.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('pendaftaran.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                    <i class="fa-solid fa-user-plus"></i> Daftarkan Anak
                </a>
                <a href="{{ route('wali.anak') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-50">
                    <i class="fa-solid fa-children"></i> Data Anak
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-xs font-bold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($jumlahPending > 0)
            <div class="flex items-start gap-3 rounded-3xl border border-amber-200 bg-amber-50 p-5">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-sm text-white">
                    <i class="fa-solid fa-hourglass-half"></i>
                </span>
                <div>
                    <p class="text-xs font-black text-amber-900">{{ $jumlahPending }} pendaftaran menunggu verifikasi</p>
                    <p class="text-[11px] leading-relaxed text-amber-700">
                        Admin akan memverifikasi pendaftaran. Setelah diterima, data anak otomatis muncul
                        di halaman Data Anak dan dapat diakses di portal ini.
                    </p>
                </div>
            </div>
        @endif

        @if ($pendaftaran->isEmpty())
            <div class="rounded-3xl border border-amber-200 bg-amber-50 p-8 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-lg text-amber-600">
                    <i class="fa-solid fa-file-circle-plus"></i>
                </div>
                <h3 class="mt-3 text-lg font-bold text-amber-900">Belum Ada Pendaftaran</h3>
                <p class="mx-auto mt-2 max-w-md text-xs leading-relaxed text-amber-700">
                    Belum ada pendaftaran PPDB yang menggunakan email akun ini. Jika anak sudah terdaftar
                    melalui admin, data anak tetap dapat dilihat pada halaman Data Anak.
                </p>
                <div class="mt-4 flex flex-wrap justify-center gap-2">
                    <a href="{{ route('pendaftaran.index') }}"
                        class="rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                        Mulai Pendaftaran
                    </a>
                    <a href="{{ route('wali.anak') }}"
                        class="rounded-xl border border-amber-300 bg-white px-4 py-2.5 text-xs font-bold text-amber-700 transition hover:bg-amber-50">
                        Lihat Data Anak
                    </a>
                </div>
            </div>
        @else
            {{-- Ringkasan status --}}
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Pendaftaran</div>
                    <div class="mt-2 text-2xl font-black text-slate-900">{{ $pendaftaran->count() }}</div>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Diterima</div>
                    <div class="mt-2 text-2xl font-black text-emerald-600">
                        {{ $pendaftaran->filter(fn ($p) => strtolower((string) $p->status_pendaftaran) === 'diterima')->count() }}
                    </div>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Menunggu / Diproses</div>
                    <div class="mt-2 text-2xl font-black text-amber-600">{{ $jumlahPending }}</div>
                </div>
            </div>

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full border-collapse text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50 text-[10px] uppercase tracking-wider text-slate-400">
                                <th class="px-6 py-3 font-extrabold">Pendaftar</th>
                                <th class="px-4 py-3 font-extrabold">No. Pendaftaran</th>
                                <th class="px-4 py-3 font-extrabold">Jenjang</th>
                                <th class="px-4 py-3 font-extrabold">Tanggal</th>
                                <th class="px-4 py-3 font-extrabold">Status</th>
                                <th class="px-6 py-3 font-extrabold">Anak Terhubung</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @foreach ($pendaftaran as $daftar)
                                @php
                                    $badge = $statusBadge($daftar->status_pendaftaran);
                                    $anakTertaut = $anakList->first(function ($anak) use ($daftar) {
                                        $idDaftar = $anak->student?->pendaftaran_online_id
                                            ?? $anak->santri?->pendaftaran_online_id;

                                        return (int) $idDaftar === (int) $daftar->id;
                                    });
                                @endphp
                                <tr class="hover:bg-slate-50/60">
                                    <td class="px-6 py-4">
                                        <div class="font-black text-slate-900">{{ $daftar->nama_lengkap }}</div>
                                        <div class="text-[10px] text-slate-400">
                                            {{ $daftar->asal_sekolah ?: 'Asal sekolah belum diisi' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 font-mono text-[11px]">{{ $daftar->no_pendaftaran ?: '—' }}</td>
                                    <td class="px-4 py-4">{{ $daftar->jenjang_tujuan ?: '—' }}</td>
                                    <td class="px-4 py-4">
                                        {{ $daftar->created_at?->translatedFormat('d M Y') ?? '—' }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="rounded-full px-2.5 py-1 text-[10px] font-bold {{ $badge['class'] }}">
                                            {{ $badge['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($anakTertaut)
                                            <a href="{{ route('wali.anak') }}" class="font-bold text-emerald-600 hover:underline">
                                                {{ $anakTertaut->nama }}
                                            </a>
                                        @elseif (strtolower((string) $daftar->status_pendaftaran) === 'diterima')
                                            <span class="text-[10px] text-slate-400">Menunggu penautan</span>
                                        @else
                                            <span class="text-[10px] text-slate-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
