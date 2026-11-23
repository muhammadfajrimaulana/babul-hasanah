@extends('layouts.alumni')

@section('title', 'Nilai & Riwayat Akademik')

@use('App\Support\Akademik')

@section('content')
    @php
        $card = 'rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm';
        $label = 'text-[10px] font-black uppercase tracking-wider text-slate-400';
        $riwayatTerakhir = $santri->riwayatKelas()->latest()->first();
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-2xl font-black text-slate-900">Nilai &amp; Riwayat Akademik</h2>
                <p class="text-xs text-slate-500">
                    Seluruh nilai yang sudah diterbitkan saat Anda masih menjadi santri. Data ini terkunci dan hanya dapat dibaca.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-3.5 py-2 text-[11px] font-bold text-indigo-700">
                    <i class="fa-solid fa-lock"></i> History terkunci
                </span>
                <a href="{{ route('alumni.dokumen') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-3.5 py-2 text-[11px] font-black text-white hover:bg-indigo-700">
                    <i class="fa-solid fa-folder-open"></i> Dokumen &amp; Transkrip
                </a>
            </div>
        </div>

        {{-- IDENTITAS --}}
        <div class="{{ $card }}">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 text-lg">
                        <i class="fa-solid fa-user-graduate"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-black text-slate-900">{{ $alumni->nama ?: $santri->nama_lengkap }}</h3>
                        <p class="text-[11px] text-slate-500">
                            {{ \App\Support\Jenjang::label($riwayatTerakhir?->jenjang ?: $alumni->jenjang) ?: '-' }}
                            · Kelas terakhir {{ $riwayatTerakhir?->kelas ?: ($santri->kelas ?: '-') }}
                            · Lulus {{ $alumni->tahun_lulus ?: '-' }}
                        </p>
                        <p class="font-mono text-[10px] text-slate-400">
                            No. Ijazah: {{ $alumni->nomor_ijazah ?: 'Belum diinput Admin Jenjang' }}
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div class="rounded-xl bg-slate-50 px-3.5 py-2.5 text-center">
                        <p class="{{ $label }}">Nilai</p>
                        <p class="mt-0.5 text-lg font-black text-slate-800">{{ $totalNilai }}</p>
                    </div>
                    <div class="rounded-xl bg-emerald-50 px-3.5 py-2.5 text-center">
                        <p class="{{ $label }}">Rata-rata</p>
                        <p class="mt-0.5 text-lg font-black text-emerald-700">{{ Akademik::formatNilai($rataKeseluruhan) }}</p>
                    </div>
                </div>
            </div>
        </div>

        @forelse ($periode as $p)
            <div class="{{ $card }}">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-black text-slate-900">
                            Tahun Ajaran {{ $p['tahun'] }} · Semester {{ Akademik::labelSemester($p['semester']) }}
                        </h3>
                        <p class="text-[10px] text-slate-400">{{ $p['mapel']->count() }} mata pelajaran</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 px-3 py-1.5 text-[10px] font-black text-emerald-700">
                        <i class="fa-solid fa-chart-simple"></i> Rata-rata {{ Akademik::formatNilai($p['rata']) }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[720px] text-left text-xs">
                        <thead class="bg-slate-50">
                            <tr class="text-[10px] uppercase tracking-wider text-slate-400">
                                <th class="px-3 py-2.5 font-extrabold">Mata Pelajaran</th>
                                <th class="px-3 py-2.5 text-center font-extrabold">Tugas</th>
                                <th class="px-3 py-2.5 text-center font-extrabold">UTS</th>
                                <th class="px-3 py-2.5 text-center font-extrabold">UAS</th>
                                <th class="px-3 py-2.5 text-center font-extrabold">Hafalan</th>
                                <th class="px-3 py-2.5 text-center font-extrabold">Akhir</th>
                                <th class="px-3 py-2.5 text-center font-extrabold">Huruf</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($p['mapel'] as $row)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-3 py-2.5">
                                        <p class="font-bold text-slate-700">{{ $row['mapel']?->nama ?? 'Mata pelajaran terhapus' }}</p>
                                        @if ($row['mapel']?->guru)
                                            <p class="text-[10px] text-slate-400">Guru: {{ $row['mapel']->guru->nama }}</p>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2.5 text-center text-slate-600">{{ Akademik::formatNilai($row['tugas']) }}</td>
                                    <td class="px-3 py-2.5 text-center text-slate-600">{{ Akademik::formatNilai($row['uts']) }}</td>
                                    <td class="px-3 py-2.5 text-center text-slate-600">{{ Akademik::formatNilai($row['uas']) }}</td>
                                    <td class="px-3 py-2.5 text-center text-slate-600">{{ Akademik::formatNilai($row['hafalan']) }}</td>
                                    <td class="px-3 py-2.5 text-center font-black text-slate-900">{{ Akademik::formatNilai($row['akhir']) }}</td>
                                    <td class="px-3 py-2.5 text-center">
                                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-100 text-[11px] font-black text-emerald-700">
                                            {{ $row['predikat'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
                <i class="fa-solid fa-book-open text-3xl text-slate-300"></i>
                <p class="mt-3 text-sm font-bold text-slate-500">Belum ada nilai yang diterbitkan.</p>
                <p class="mt-1 text-[11px] text-slate-400">Hubungi Admin Jenjang bila nilai Anda belum muncul.</p>
            </div>
        @endforelse

        <p class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-[11px] leading-relaxed text-amber-700">
            <i class="fa-solid fa-circle-info mr-1"></i>
            History nilai bersifat terkunci. Jika ada kekeliruan data, hubungi Admin Jenjang — alumni tidak dapat mengubah nilai.
        </p>
    </div>
@endsection
