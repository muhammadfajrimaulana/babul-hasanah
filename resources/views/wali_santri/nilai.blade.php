@extends('layouts.portal')

@section('title', 'Nilai - Portal Wali Santri')

@section('content')
    @php
        $tahunOpsi = $opsi->pluck('tahun_ajaran')->filter()->unique()->values();
        if (! $tahunOpsi->contains($tahun)) {
            $tahunOpsi->prepend($tahun);
        }
        $labelSemester = \App\Support\Akademik::labelSemester($semester);
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-black text-slate-900">Nilai &amp; Rapor</h2>
                <p class="text-xs text-slate-500">
                    Hanya nilai yang sudah diterbitkan pengajar yang ditampilkan.
                </p>
            </div>
            @if ($anak)
                <a href="{{ route('portal.nilai', ['anak' => $anak->key]) }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                    <i class="fa-solid fa-book-open"></i> Rapor Digital Lengkap
                </a>
            @endif
        </div>

        @if (! $anak)
            <div class="rounded-3xl border border-amber-200 bg-amber-50 p-8 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-lg text-amber-600">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h3 class="mt-3 text-lg font-bold text-amber-900">Belum Ada Anak Terhubung</h3>
                <p class="mx-auto mt-2 max-w-md text-xs leading-relaxed text-amber-700">
                    Nilai akan tampil setelah data anak terhubung dengan akun ini dan pengajar menerbitkan nilai.
                </p>
                <a href="{{ route('wali.anak') }}"
                    class="mt-4 inline-flex rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                    Lihat Data Anak
                </a>
            </div>
        @else
            {{-- Pemilih anak & periode --}}
            <form method="GET" action="{{ route('wali.nilai') }}"
                class="flex flex-col gap-3 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-end">
                @if ($anakList->count() > 1)
                    <div class="flex-1">
                        <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Anak</label>
                        <select name="anak"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-bold text-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            @foreach ($anakList as $pilihan)
                                <option value="{{ $pilihan->key }}" @selected($pilihan->key === $anak->key)>
                                    {{ $pilihan->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <input type="hidden" name="anak" value="{{ $anak->key }}">
                @endif

                <div class="flex-1">
                    <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Tahun Ajaran</label>
                    <select name="tahun_ajaran"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-bold text-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                        @foreach ($tahunOpsi as $th)
                            <option value="{{ $th }}" @selected($th === $tahun)>{{ $th }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex-1">
                    <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Semester</label>
                    <select name="semester"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-bold text-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                        @foreach (\App\Support\Akademik::SEMESTER as $kunci => $label)
                            <option value="{{ $kunci }}" @selected($kunci === $semester)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <button
                    class="rounded-xl bg-slate-900 px-5 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800">
                    Tampilkan
                </button>
            </form>

            {{-- Ringkasan --}}
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rata-rata</div>
                    <div class="mt-2 text-2xl font-black text-slate-900">{{ $rapor['rata'] !== null ? number_format($rapor['rata'], 2) : '—' }}</div>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Predikat</div>
                    <div class="mt-2 text-2xl font-black text-emerald-600">{{ $rapor['predikat'] ?: '—' }}</div>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Mata Pelajaran</div>
                    <div class="mt-2 text-2xl font-black text-slate-900">{{ $rapor['jumlah'] }}</div>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tuntas KKM</div>
                    <div class="mt-2 text-2xl font-black text-slate-900">{{ $rapor['tuntas'] }}<span class="text-sm font-bold text-slate-400">/{{ $rapor['jumlah'] }}</span></div>
                </div>
            </div>

            {{-- Tabel nilai --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-6 py-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-900">{{ $anak->nama }}</h3>
                        <p class="text-[11px] text-slate-400">
                            {{ $anak->jenjang ?: 'Jenjang belum diatur' }} &middot;
                            {{ $anak->kelas ? 'Kelas '.$anak->kelas : 'Kelas belum diatur' }} &middot;
                            {{ $tahun }} &middot; {{ $labelSemester }}
                        </p>
                    </div>
                    @if ($rapor['terakhir'])
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-[10px] font-bold text-emerald-700">
                            Terbit {{ \Illuminate\Support\Carbon::parse($rapor['terakhir'])->translatedFormat('d M Y') }}
                        </span>
                    @endif
                </div>

                @if ($rapor['mapel']->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-lg text-slate-400">
                            <i class="fa-solid fa-inbox"></i>
                        </div>
                        <p class="mt-3 text-sm font-bold text-slate-700">Belum ada nilai terbit</p>
                        <p class="mx-auto mt-1 max-w-md text-xs text-slate-400">
                            Nilai untuk {{ $tahun }} semester {{ $labelSemester }} belum diterbitkan.
                            Coba pilih periode lain atau hubungi pengajar.
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full border-collapse text-left text-xs">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50 text-[10px] uppercase tracking-wider text-slate-400">
                                    <th class="px-6 py-3 font-extrabold">Mata Pelajaran</th>
                                    <th class="px-4 py-3 text-center font-extrabold">Tugas</th>
                                    <th class="px-4 py-3 text-center font-extrabold">UTS</th>
                                    <th class="px-4 py-3 text-center font-extrabold">UAS</th>
                                    <th class="px-4 py-3 text-center font-extrabold">Hafalan</th>
                                    <th class="px-4 py-3 text-center font-extrabold">Akhir</th>
                                    <th class="px-4 py-3 text-center font-extrabold">Predikat</th>
                                    <th class="px-6 py-3 text-center font-extrabold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                                @foreach ($rapor['mapel'] as $row)
                                    <tr class="hover:bg-slate-50/60">
                                        <td class="px-6 py-3">
                                            <div class="font-bold text-slate-900">{{ $row['nama'] }}</div>
                                            <div class="text-[10px] text-slate-400">KKM {{ number_format($row['kkm'], 0) }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">{{ $row['tugas'] !== null ? number_format((float) $row['tugas'], 2) : '—' }}</td>
                                        <td class="px-4 py-3 text-center">{{ $row['uts'] !== null ? number_format((float) $row['uts'], 2) : '—' }}</td>
                                        <td class="px-4 py-3 text-center">{{ $row['uas'] !== null ? number_format((float) $row['uas'], 2) : '—' }}</td>
                                        <td class="px-4 py-3 text-center">{{ $row['hafalan'] !== null ? number_format((float) $row['hafalan'], 2) : '—' }}</td>
                                        <td class="px-4 py-3 text-center font-black text-slate-900">{{ number_format((float) $row['akhir'], 2) }}</td>
                                        <td class="px-4 py-3 text-center font-bold text-emerald-600">{{ $row['predikat'] }}</td>
                                        <td class="px-6 py-3 text-center">
                                            <span @class([
                                                'rounded-full px-2 py-0.5 text-[10px] font-bold',
                                                'bg-emerald-50 text-emerald-700' => $row['tuntas'],
                                                'bg-rose-50 text-rose-700' => ! $row['tuntas'],
                                            ])>
                                                {{ $row['tuntas'] ? 'Tuntas' : 'Belum tuntas' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($rapor['catatan'])
                        <div class="border-t border-slate-100 bg-slate-50/60 px-6 py-4">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Catatan Pengajar</div>
                            <p class="mt-1 text-xs leading-relaxed text-slate-600">{{ $rapor['catatan'] }}</p>
                        </div>
                    @endif
                @endif
            </div>

            {{-- Transkrip --}}
            <div class="space-y-3">
                <h3 class="text-sm font-black text-slate-900">Transkrip per Periode</h3>

                @forelse ($transkrip as $periode)
                    <details class="group rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-6 py-4">
                            <div>
                                <div class="text-xs font-black text-slate-900">
                                    {{ $periode['tahun_ajaran'] }} &middot; {{ $periode['label_semester'] }}
                                </div>
                                <div class="text-[10px] text-slate-400">{{ $periode['mapel']->count() }} mata pelajaran</div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-[10px] font-bold text-emerald-700">
                                    Rata {{ $periode['rata'] !== null ? number_format($periode['rata'], 2) : '—' }}
                                </span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition group-open:rotate-180"></i>
                            </div>
                        </summary>
                        <div class="border-t border-slate-100 px-6 py-4">
                            <div class="grid gap-2 sm:grid-cols-2">
                                @foreach ($periode['mapel'] as $row)
                                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2">
                                        <span class="text-[11px] font-bold text-slate-600">{{ $row['nama'] }}</span>
                                        <span class="font-mono text-[11px] font-black text-slate-900">{{ number_format((float) $row['nilai'], 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </details>
                @empty
                    <div class="rounded-3xl border border-slate-200 bg-white px-6 py-8 text-center text-xs text-slate-400">
                        Belum ada transkrip nilai yang diterbitkan.
                    </div>
                @endforelse
            </div>
        @endif
    </div>
@endsection
