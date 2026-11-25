@extends('layouts.portal')

@section('title', 'Nilai Santri - Portal Wali')

@use('App\Support\Akademik')

@section('content')
    @php
        $labelAnak = $anak ? $anak->nama_lengkap . ' — ' . ($anak->kelas ?? 'Kelas belum diatur') : null;
        $queryCetak = $anak ? [
            'anak' => $anak->id,
            'tahun_ajaran' => $filter['tahun'] ?? null,
            'semester' => $filter['semester'] ?? null,
        ] : [];
    @endphp

    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="bg-gradient-to-r from-emerald-900 to-slate-900 text-white p-6 sm:p-8 rounded-3xl shadow-lg space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="space-y-2">
                    <span class="bg-emerald-500/20 text-emerald-400 text-[11px] font-bold px-3 py-1 rounded-full border border-emerald-500/30 inline-block">
                        Rapor / Nilai Santri
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black">Nilai Akademik Ananda</h2>
                    <p class="text-slate-300 text-xs sm:text-sm max-w-2xl leading-relaxed">
                        Lihat nilai yang sudah diterbitkan pengajar, lengkap dengan rata-rata, predikat, dan catatan per mata pelajaran.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <a href="{{ route('portal.prestasi') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-400/20 hover:bg-amber-400/30 border border-amber-300/30 px-4 py-2.5 text-xs font-bold text-amber-100 transition">
                        <i class="fa-solid fa-trophy"></i> Prestasi
                    </a>
                    <a href="{{ route('portal.dashboard') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 px-4 py-2.5 text-xs font-bold text-white transition">
                        <i class="fa-solid fa-house"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>

        @if (! $siap)
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-amber-200 shadow-sm text-center space-y-2">
                <i class="fa-solid fa-hourglass-half text-2xl text-amber-500"></i>
                <h3 class="text-sm font-black text-slate-900">Modul nilai belum tersedia</h3>
                <p class="text-xs text-slate-500">Pengelola pesantren belum mengaktifkan modul nilai. Silakan coba lagi nanti.</p>
            </div>
        @elseif ($anakList->isEmpty())
            <div class="bg-amber-50 border border-amber-200 p-6 sm:p-8 rounded-3xl text-center space-y-3">
                <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto text-lg">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h3 class="text-lg font-bold text-amber-900">Belum Ada Data Anak Terhubung</h3>
                <p class="text-xs sm:text-sm text-amber-700 max-w-md mx-auto leading-relaxed">
                    Akun Anda belum dikaitkan dengan data santri manapun. Silakan informasikan Nomor Induk atau Nama Santri Anda ke pihak Admin Pesantren.
                </p>
            </div>
        @else
            {{-- PEMILIH ANAK & PERIODE --}}
            <form method="GET" action="{{ route('portal.nilai') }}"
                class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-bold text-slate-600">Pilih Anak</label>
                        <select name="anak"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            @foreach ($anakList as $pilihan)
                                <option value="{{ $pilihan->id }}" @selected($anak?->id === $pilihan->id)>
                                    {{ $pilihan->nama_lengkap }} — {{ $pilihan->kelas ?? 'Kelas belum diatur' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-bold text-slate-600">Tahun Ajaran</label>
                        <select name="tahun_ajaran"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            @php
                                $tahunOpsi = $opsi->pluck('tahun_ajaran')->unique()->values();
                                if ($tahunOpsi->isEmpty()) {
                                    $tahunOpsi = collect(Akademik::tahunAjaranPilihan());
                                }
                            @endphp
                            @foreach ($tahunOpsi as $tahun)
                                <option value="{{ $tahun }}" @selected(($filter['tahun'] ?? null) === $tahun)>{{ $tahun }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-bold text-slate-600">Semester</label>
                        <select name="semester"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            @foreach (Akademik::SEMESTER as $nilai => $label)
                                <option value="{{ $nilai }}" @selected(($filter['semester'] ?? null) === $nilai)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <p class="text-[10px] text-slate-400">Hanya nilai berstatus terbit yang ditampilkan di portal.</p>
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="submit"
                            class="rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2.5 text-xs font-bold transition inline-flex items-center gap-2">
                            <i class="fa-solid fa-filter"></i> Tampilkan Nilai
                        </button>
                        @if ($anak)
                            <a href="{{ route('portal.nilai.cetak', array_filter($queryCetak)) }}" target="_blank"
                                class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-600 hover:border-emerald-400 hover:text-emerald-700 transition inline-flex items-center gap-2">
                                <i class="fa-solid fa-print"></i> Cetak / PDF
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            @if (! $anak)
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm text-center">
                    <p class="text-xs font-bold text-slate-500">Pilih anak terlebih dahulu.</p>
                </div>
            @else
                {{-- IDENTITAS ANAK --}}
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Santri</p>
                        <h3 class="text-lg font-black text-slate-900">{{ $anak->nama_lengkap }}</h3>
                        <p class="text-[11px] text-slate-500 font-mono">NIS: {{ $anak->nomor_induk }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 border border-emerald-200 px-3 py-2 text-[11px] font-bold text-emerald-700">
                            <i class="fa-solid fa-building-columns"></i> {{ $anak->jenjang_pendidikan }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-xl bg-slate-50 border border-slate-200 px-3 py-2 text-[11px] font-bold text-slate-600">
                            <i class="fa-solid fa-users"></i> Kelas {{ $anak->kelas ?? '—' }}
                        </span>
                    </div>
                </div>

                {{-- NOTIFIKASI NILAI / RAPOR BARU --}}
                @php
                    $adaDokumenBaru = $dokumen->contains(
                        fn ($d) => $d->created_at && $d->created_at->gte(now()->subDays(14))
                    );
                @endphp
                @if (($data && $data['baru']) || $adaDokumenBaru)
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-xs font-bold text-emerald-800 flex items-start gap-2.5">
                        <i class="fa-solid fa-bell mt-0.5"></i>
                        <span>
                            Nilai atau rapor baru telah diterbitkan untuk ananda {{ $anak->nama_lengkap }}.
                            Silakan periksa rincian di bawah atau buka rapor PDF.
                        </span>
                    </div>
                @endif

                {{-- RAPOR PDF YANG DIUNGGAH PENGELOLA --}}
                @if ($dokumen->isNotEmpty())
                    @foreach ($dokumen as $dok)
                        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-[10px] font-black uppercase tracking-wider text-rose-600">
                                        <i class="fa-solid fa-file-pdf mr-1"></i> Rapor PDF
                                    </p>
                                    <h3 class="text-sm font-black text-slate-900 truncate">{{ $dok->judul }}</h3>
                                    <p class="text-[11px] text-slate-500">
                                        {{ $dok->tahun_ajaran }} · Semester {{ Akademik::labelSemester($dok->semester) }}
                                        · {{ $dok->ukuranManusia() }}
                                    </p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 shrink-0">
                                    <a href="{{ route('portal.nilai.dokumen', $dok) }}" target="_blank" rel="noopener"
                                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3.5 py-2 text-[11px] font-bold text-slate-600 transition hover:border-emerald-400 hover:text-emerald-700">
                                        <i class="fa-solid fa-up-right-from-square"></i> Buka
                                    </a>
                                    <a href="{{ route('portal.nilai.dokumen.unduh', $dok) }}"
                                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 px-3.5 py-2 text-[11px] font-bold text-white transition">
                                        <i class="fa-solid fa-download"></i> Unduh PDF
                                    </a>
                                </div>
                            </div>

                            <div class="p-4 sm:p-5 space-y-2">
                                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                                    <iframe src="{{ route('portal.nilai.dokumen', $dok) }}#toolbar=1&navpanes=0&view=FitH"
                                        title="Rapor PDF {{ $dok->judul }}"
                                        class="h-[420px] w-full sm:h-[600px]"></iframe>
                                </div>
                                <p class="text-[10px] text-slate-400">
                                    <i class="fa-solid fa-circle-info mr-1"></i>
                                    Jika pratinjau tidak muncul di perangkat Anda, gunakan tombol
                                    <b>Buka</b> atau <b>Unduh PDF</b>.
                                    Diunggah {{ $dok->created_at?->translatedFormat('d F Y H:i') }} WIB
                                    @if ($dok->pengunggah) · oleh {{ $dok->pengunggah->name }} @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if (! $data || $data['mapel']->isEmpty())
                    @if ($dokumen->isEmpty())
                        <div class="bg-white p-6 sm:p-10 rounded-3xl border border-slate-200 shadow-sm text-center space-y-2">
                            <i class="fa-solid fa-folder-open text-3xl text-slate-300"></i>
                            <h3 class="text-sm font-black text-slate-900">Belum ada nilai terbit</h3>
                            <p class="text-xs text-slate-500 max-w-md mx-auto">
                                Pengajar belum menerbitkan nilai untuk tahun ajaran dan semester ini.
                                Silakan pilih periode lain atau cek kembali nanti.
                            </p>
                        </div>
                    @else
                        <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-xs text-slate-500">
                            <i class="fa-solid fa-circle-info mr-1 text-slate-400"></i>
                            Rincian nilai angka belum diterbitkan untuk periode ini. Rapor PDF di atas tetap
                            dapat dibuka dan diunduh.
                        </div>
                    @endif
                @else
                    {{-- KARTU RINGKASAN --}}
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                        @php
                            $kartu = [
                                ['label' => 'Rata-rata', 'nilai' => Akademik::formatNilai($data['rata']), 'ikon' => 'fa-chart-simple', 'warna' => 'text-emerald-700'],
                                ['label' => 'Predikat', 'nilai' => $data['predikat'], 'ikon' => 'fa-award', 'warna' => 'text-violet-700'],
                                ['label' => 'Mata Pelajaran', 'nilai' => $data['jumlah'], 'ikon' => 'fa-book', 'warna' => 'text-sky-700'],
                                ['label' => 'Tuntas KKM', 'nilai' => $data['tuntas'] . '/' . $data['jumlah'], 'ikon' => 'fa-circle-check', 'warna' => 'text-amber-700'],
                            ];
                        @endphp
                        @foreach ($kartu as $k)
                            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $k['label'] }}</span>
                                    <i class="fa-solid {{ $k['ikon'] }} text-xs text-slate-300"></i>
                                </div>
                                <p class="mt-2 text-xl font-black {{ $k['warna'] }}">{{ $k['nilai'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- CATATAN WALI KELAS --}}
                    @if ($data['catatan'])
                        <div class="rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3.5 flex items-start gap-2.5">
                            <i class="fa-solid fa-comment-dots text-sky-600 mt-0.5"></i>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-wider text-sky-700">Catatan Pengajar</p>
                                <p class="text-xs text-sky-900 mt-0.5">{{ $data['catatan'] }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- TABEL NILAI --}}
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <h3 class="text-sm font-black text-slate-900">Rincian Nilai per Mata Pelajaran</h3>
                                <p class="text-[11px] text-slate-500">
                                    {{ $filter['tahun'] }} · Semester {{ Akademik::labelSemester($filter['semester']) }}
                                </p>
                            </div>
                            <a href="{{ route('portal.nilai.cetak', array_filter($queryCetak)) }}" target="_blank"
                                class="inline-flex items-center gap-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white px-3.5 py-2 text-[11px] font-bold transition">
                                <i class="fa-solid fa-print"></i> Cetak Rapor
                            </a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[760px] text-left">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Mata Pelajaran</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">Tugas</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">UTS</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">UAS</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">Hafalan</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">Nilai Akhir</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">Huruf</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">Ket.</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($data['mapel'] as $row)
                                        <tr class="hover:bg-slate-50/70">
                                            <td class="px-4 py-3">
                                                <p class="text-xs font-bold text-slate-800">{{ $row['mapel']?->nama ?? 'Mata pelajaran terhapus' }}</p>
                                                <p class="text-[10px] text-slate-400">
                                                    KKM {{ $row['mapel']?->kkm ?? 70 }}
                                                    @if ($row['mapel']?->guru)
                                                        · Guru: {{ $row['mapel']->guru->nama }}
                                                    @endif
                                                </p>
                                            </td>
                                            <td class="px-4 py-3 text-center text-xs font-semibold text-slate-700">{{ Akademik::formatNilai($row['tugas']) }}</td>
                                            <td class="px-4 py-3 text-center text-xs font-semibold text-slate-700">{{ Akademik::formatNilai($row['uts']) }}</td>
                                            <td class="px-4 py-3 text-center text-xs font-semibold text-slate-700">{{ Akademik::formatNilai($row['uas']) }}</td>
                                            <td class="px-4 py-3 text-center text-xs font-semibold text-slate-700">{{ Akademik::formatNilai($row['hafalan']) }}</td>
                                            <td class="px-4 py-3 text-center text-xs font-black text-slate-900">{{ Akademik::formatNilai($row['akhir']) }}</td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-100 text-[11px] font-black text-emerald-700">
                                                    {{ $row['predikat'] }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if ($row['tuntas'])
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-black text-emerald-700">
                                                        <i class="fa-solid fa-check"></i> Tuntas
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2.5 py-1 text-[10px] font-black text-rose-700">
                                                        <i class="fa-solid fa-arrow-trend-down"></i> Belum
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if ($data['terakhir'])
                        <p class="text-[10px] text-slate-400 text-center">
                            Terakhir diperbarui {{ $data['terakhir']->translatedFormat('d F Y H:i') }} WIB
                        </p>
                    @endif
                @endif
            @endif
        @endif
    </div>
@endsection
