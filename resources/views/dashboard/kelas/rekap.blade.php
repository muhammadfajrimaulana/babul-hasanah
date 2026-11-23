@extends('layouts.admin')

@section('title', 'Rekap Kelas ' . $kelas->nama)

@use('App\Support\Akademik')

@section('content')
    @php
        $in = 'rounded-xl border border-slate-200 px-3 py-2 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500';
        $label = 'text-[10px] font-black uppercase tracking-wider text-slate-400';
    @endphp

    <style>
        @media print {
            aside, header, nav, .no-print {
                display: none !important;
            }

            body {
                background: #fff !important;
            }

            main {
                padding: 0 !important;
            }

            .print-area {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
            }
        }
    </style>

    <div class="space-y-5">
        <div class="no-print flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('admin.kelas.index') }}"
                    class="inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-500 hover:text-emerald-700 dark:text-slate-400">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Kelas &amp; Guru
                </a>
                <h2 class="mt-1 text-xl font-black text-slate-900 dark:text-white">Rekap Kelas {{ $kelas->nama }}</h2>
                <p class="max-w-2xl text-xs text-slate-500 dark:text-slate-400">
                    Rekap ini disiapkan Admin Jenjang untuk diperiksa <b>Wali Kelas</b>.
                    Wali Kelas tidak memiliki akun login; perubahan tetap diinput Admin Jenjang dan tercatat di Log Aktivitas.
                </p>
            </div>
            <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-xs font-bold text-white hover:bg-emerald-800">
                <i class="fa-solid fa-print"></i> Cetak Rekap
            </button>
        </div>

        <form method="GET" action="{{ route('admin.kelas.rekap', $kelas) }}"
            class="no-print flex flex-col gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:flex-row sm:items-end">
            <div>
                <label class="mb-1.5 block text-[11px] font-bold text-slate-600 dark:text-slate-300">Tahun Ajaran</label>
                <select name="tahun_ajaran" class="{{ $in }}">
                    @foreach ($tahunPilihan as $t)
                        <option value="{{ $t }}" @selected($tahun === $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-[11px] font-bold text-slate-600 dark:text-slate-300">Semester</label>
                <select name="semester" class="{{ $in }}">
                    @foreach (Akademik::SEMESTER as $kode => $teks)
                        <option value="{{ $kode }}" @selected($semester === $kode)>{{ $teks }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-800 px-4 py-2 text-xs font-bold text-white hover:bg-slate-900">
                <i class="fa-solid fa-filter"></i> Terapkan
            </button>
        </form>

        <div class="print-area rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-4 flex flex-col gap-2 border-b border-slate-100 pb-4 dark:border-slate-800 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white">
                        Rekap Kelas {{ $kelas->nama }} — {{ \App\Support\Jenjang::label($kelas->jenjang) }}
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        Tahun Ajaran {{ $tahun }} · Semester {{ Akademik::labelSemester($semester) }}
                        · Wali Kelas: <b>{{ $kelas->waliKelas?->nama ?? 'Belum ditentukan' }}</b>
                    </p>
                </div>
                <div class="text-left sm:text-right">
                    <p class="{{ $label }}">Jumlah Santri</p>
                    <p class="text-lg font-black text-slate-900 dark:text-white">{{ $santris->count() }}</p>
                </div>
            </div>

            @if ($santris->isEmpty())
                <p class="py-10 text-center text-xs text-slate-400">Belum ada santri pada kelas ini.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[11px]" style="min-width: {{ 380 + ($mapel->count() * 78) }}px;">
                        <thead class="bg-slate-50 dark:bg-slate-800/60">
                            <tr>
                                <th class="px-2 py-2.5 font-black uppercase tracking-wider text-slate-400">No</th>
                                <th class="px-2 py-2.5 font-black uppercase tracking-wider text-slate-400">NIS</th>
                                <th class="px-2 py-2.5 font-black uppercase tracking-wider text-slate-400">Nama Santri</th>
                                @foreach ($mapel as $m)
                                    <th class="px-2 py-2.5 text-center font-black uppercase tracking-wider text-slate-400" title="{{ $m->nama }}{{ $m->guru ? ' · '.$m->guru->nama : '' }}">
                                        {{ $m->kode ?: $m->nama }}
                                    </th>
                                @endforeach
                                <th class="px-2 py-2.5 text-center font-black uppercase tracking-wider text-slate-400">Hafalan</th>
                                <th class="px-2 py-2.5 text-center font-black uppercase tracking-wider text-slate-400">Pelanggaran</th>
                                <th class="px-2 py-2.5 text-center font-black uppercase tracking-wider text-slate-400">Hadir</th>
                                <th class="px-2 py-2.5 font-black uppercase tracking-wider text-slate-400">Catatan Wali Kelas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($santris as $santri)
                                <tr>
                                    <td class="px-2 py-2.5 text-slate-400">{{ $loop->iteration }}</td>
                                    <td class="px-2 py-2.5 font-mono text-slate-500">{{ $santri->nomor_induk ?: '-' }}</td>
                                    <td class="px-2 py-2.5 font-bold text-slate-800 dark:text-slate-100">{{ $santri->nama_lengkap }}</td>
                                    @foreach ($mapel as $m)
                                        <td class="px-2 py-2.5 text-center font-semibold text-slate-700 dark:text-slate-200">
                                            {{ Akademik::formatNilai($nilai->get($santri->id.'|'.$m->id)) }}
                                        </td>
                                    @endforeach
                                    <td class="px-2 py-2.5 text-center text-slate-600">{{ (int) ($hafalan[$santri->id] ?? 0) }}</td>
                                    <td class="px-2 py-2.5 text-center text-slate-600">
                                        {{ (int) ($pelanggaran[$santri->id]->jumlah ?? 0) }}
                                        @if ((int) ($pelanggaran[$santri->id]->poin ?? 0) > 0)
                                            <span class="text-rose-600">({{ (int) $pelanggaran[$santri->id]->poin }} poin)</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-2.5 text-center text-slate-600">{{ (int) ($presensi[$santri->id] ?? 0) }}</td>
                                    <td class="px-2 py-2.5 text-slate-300">........................................</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-3 text-[10px] text-slate-500 dark:text-slate-400 sm:grid-cols-2">
                    <div class="rounded-xl bg-slate-50 px-3 py-2 dark:bg-slate-800/50">
                        <b>Keterangan kolom nilai:</b> rata-rata nilai terbit per mata pelajaran pada periode ini.
                    </div>
                    <div class="rounded-xl bg-slate-50 px-3 py-2 dark:bg-slate-800/50">
                        <b>Alur:</b> Wali Kelas memeriksa rekap → menyerahkan catatan ke Admin Jenjang → Admin menginput ke sistem.
                    </div>
                </div>
            @endif
        </div>

        <div class="no-print flex flex-wrap items-center justify-between gap-2 text-[10px] text-slate-400">
            <p>Dicetak dari sistem Pondok Pesantren Babul Hasanah.</p>
            <p>{{ now()->translatedFormat('d F Y H:i') }} WIB</p>
        </div>
    </div>
@endsection
