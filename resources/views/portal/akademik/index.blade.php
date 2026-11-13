@extends('layouts.portal')

@section('title', 'Rapor Digital - Portal Wali')

@use('App\Support\Akademik')
@use('Illuminate\Support\Carbon')

@section('content')
    @php
        $queryPeriode = $anak ? array_filter([
            'anak' => $anak->key,
            'tahun_ajaran' => $filter['tahun'],
            'semester' => $filter['semester'],
        ]) : [];
        $tabKelas = 'inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold transition';
        $tahunOpsi = $opsi->pluck('tahun_ajaran')->unique()->values();
        if (! $tahunOpsi->contains($filter['tahun'])) {
            $tahunOpsi->prepend($filter['tahun']);
        }
    @endphp

    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="bg-gradient-to-r from-emerald-900 to-slate-900 text-white p-6 sm:p-8 rounded-3xl shadow-lg space-y-3">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                <div class="space-y-2">
                    <span class="bg-emerald-500/20 text-emerald-400 text-[11px] font-bold px-3 py-1 rounded-full border border-emerald-500/30 inline-block">
                        Rapor Digital Wali Santri
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black">
                        {{ $anak?->nama ?? 'Portal Wali Santri' }}
                    </h2>
                    <p class="text-slate-300 text-xs sm:text-sm max-w-2xl leading-relaxed">
                        Rapor, progres, transkrip, tahfidz, absensi, dan administrasi dalam satu halaman.
                        Hanya nilai berstatus <span class="font-bold text-emerald-300">Terbit</span> yang ditampilkan.
                    </p>
                    @if ($anak)
                        <div class="flex flex-wrap items-center gap-2 pt-1 text-[11px] font-bold">
                            <span class="rounded-xl bg-white/10 px-3 py-1.5 border border-white/15">
                                <i class="fa-solid fa-id-card mr-1 text-emerald-300"></i> {{ $anak->nis }}
                            </span>
                            <span class="rounded-xl bg-white/10 px-3 py-1.5 border border-white/15">
                                <i class="fa-solid fa-users mr-1 text-emerald-300"></i> Kelas {{ $anak->kelas ?? '—' }}
                            </span>
                            <span class="rounded-xl bg-white/10 px-3 py-1.5 border border-white/15">
                                <i class="fa-solid fa-building-columns mr-1 text-emerald-300"></i> {{ $anak->jenjang ?? '—' }}
                            </span>
                        </div>
                    @endif
                </div>

                @if ($anak)
                    <a href="{{ route('portal.nilai.pdf', $queryPeriode) }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-900 px-4 py-3 text-xs font-black transition shrink-0">
                        <i class="fa-solid fa-file-pdf"></i> Unduh Rapor PDF
                    </a>
                @endif
            </div>
        </div>

        @if (! $anak)
            <div class="bg-white p-10 rounded-3xl border border-slate-200 shadow-sm text-center space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto text-xl">
                    <i class="fa-solid fa-user-group"></i>
                </div>
                <h3 class="text-base font-black text-slate-900">Belum ada anak terhubung</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
                    Akun Anda belum dikaitkan dengan data santri manapun. Pastikan email akun ini sama
                    dengan <span class="font-mono">parent_email</span> santri, atau hubungi Admin Pesantren.
                </p>
            </div>
        @else
            {{-- FILTER PERIODE --}}
            <form method="GET" action="{{ route('portal.nilai') }}"
                class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                    <div class="space-y-1.5 sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-600">Tahun Ajaran</label>
                        <select name="tahun_ajaran"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            @foreach ($tahunOpsi as $tahun)
                                <option value="{{ $tahun }}" @selected($filter['tahun'] === $tahun)>{{ $tahun }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-bold text-slate-600">Semester</label>
                        <select name="semester"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            @foreach (Akademik::SEMESTER as $nilai => $label)
                                <option value="{{ $nilai }}" @selected($filter['semester'] === $nilai)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <p class="text-[10px] text-slate-400">
                        Periode {{ $filter['tahun'] }} · Semester {{ Akademik::labelSemester($filter['semester']) }}.
                        Ganti anak melalui dropdown di header.
                    </p>
                    <button type="submit"
                        class="rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2.5 text-xs font-bold transition inline-flex items-center gap-2">
                        <i class="fa-solid fa-filter"></i> Tampilkan
                    </button>
                </div>
            </form>

            {{-- TAB INTERAKTIF --}}
            <div class="flex flex-wrap items-center gap-2" role="tablist" aria-label="Menu portal wali">
                @foreach ([
                    'rapor' => ['label' => 'Rapor & Nilai', 'ikon' => 'fa-book-open'],
                    'transkrip' => ['label' => 'Progres & Transkrip', 'ikon' => 'fa-chart-line'],
                    'tahfidz' => ['label' => 'Tahfidz', 'ikon' => 'fa-book-quran'],
                    'absensi' => ['label' => 'Absensi', 'ikon' => 'fa-calendar-check'],
                    'keuangan' => ['label' => 'Keuangan', 'ikon' => 'fa-wallet'],
                ] as $kunci => $tab)
                    <button type="button" data-tab-target="{{ $kunci }}" role="tab"
                        aria-controls="panel-{{ $kunci }}"
                        class="{{ $tabKelas }} bg-white text-slate-600 border border-slate-200 hover:border-emerald-400">
                        <i class="fa-solid {{ $tab['ikon'] }}"></i> {{ $tab['label'] }}
                    </button>
                @endforeach
            </div>

            {{-- PANEL RAPOR --}}
            <div data-tab-panel="rapor" id="panel-rapor" role="tabpanel" class="space-y-4">
                @if ($rapor['baru'])
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-xs font-bold text-emerald-800 flex items-start gap-2.5">
                        <i class="fa-solid fa-bell mt-0.5"></i>
                        <span>Nilai baru telah diterbitkan untuk ananda {{ $anak->nama }}. Silakan periksa rincian di bawah atau unduh rapor PDF.</span>
                    </div>
                @endif

                @if ($rapor['mapel']->isEmpty())
                    <div class="bg-white p-10 rounded-3xl border border-slate-200 shadow-sm text-center space-y-2">
                        <i class="fa-solid fa-folder-open text-3xl text-slate-300"></i>
                        <h3 class="text-sm font-black text-slate-900">Belum ada nilai terbit</h3>
                        <p class="text-xs text-slate-500 max-w-md mx-auto">
                            Pengajar belum menerbitkan nilai untuk tahun ajaran dan semester ini.
                            Pilih periode lain atau cek kembali nanti.
                        </p>
                    </div>
                @else
                    @php
                        $kartu = [
                            ['label' => 'Rata-rata', 'nilai' => Akademik::formatNilai($rapor['rata']), 'ikon' => 'fa-chart-simple', 'warna' => 'text-emerald-700'],
                            ['label' => 'Predikat', 'nilai' => $rapor['predikat'], 'ikon' => 'fa-award', 'warna' => 'text-violet-700'],
                            ['label' => 'Mata Pelajaran', 'nilai' => $rapor['jumlah'], 'ikon' => 'fa-book', 'warna' => 'text-sky-700'],
                            ['label' => 'Tuntas KKM', 'nilai' => $rapor['tuntas'] . '/' . $rapor['jumlah'], 'ikon' => 'fa-circle-check', 'warna' => 'text-amber-700'],
                        ];
                    @endphp
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
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

                    @if ($rapor['catatan'])
                        <div class="rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3.5 flex items-start gap-2.5">
                            <i class="fa-solid fa-comment-dots text-sky-600 mt-0.5"></i>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-wider text-sky-700">Catatan Pengajar</p>
                                <p class="text-xs text-sky-900 mt-0.5">{{ $rapor['catatan'] }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <h3 class="text-sm font-black text-slate-900">Nilai Akhir per Mata Pelajaran</h3>
                                <p class="text-[11px] text-slate-500">
                                    {{ $filter['tahun'] }} · Semester {{ Akademik::labelSemester($filter['semester']) }}
                                    @if ($rapor['sumber'] === 'lama')
                                        · arsip nilai lama
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('portal.nilai.pdf', $queryPeriode) }}"
                                class="inline-flex items-center gap-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white px-3.5 py-2 text-[11px] font-bold transition">
                                <i class="fa-solid fa-file-pdf"></i> Unduh Rapor PDF
                            </a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[820px] text-left">
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
                                    @foreach ($rapor['mapel'] as $row)
                                        <tr class="hover:bg-slate-50/70">
                                            <td class="px-4 py-3">
                                                <p class="text-xs font-bold text-slate-800">{{ $row['nama'] }}</p>
                                                <p class="text-[10px] text-slate-400">KKM {{ Akademik::formatNilai($row['kkm']) }}</p>
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

                    @if ($rapor['terakhir'])
                        <p class="text-[10px] text-slate-400 text-center">
                            Terakhir diperbarui {{ Carbon::parse($rapor['terakhir'])->translatedFormat('d F Y H:i') }} WIB
                        </p>
                    @endif
                @endif
            </div>

            {{-- PANEL PROGRES & TRANSKRIP --}}
            <div data-tab-panel="transkrip" id="panel-transkrip" role="tabpanel" class="hidden space-y-4">
                @if ($transkrip->isEmpty())
                    <div class="bg-white p-10 rounded-3xl border border-slate-200 shadow-sm text-center space-y-2">
                        <i class="fa-solid fa-chart-line text-3xl text-slate-300"></i>
                        <h3 class="text-sm font-black text-slate-900">Belum ada nilai terbit</h3>
                        <p class="text-xs text-slate-500 max-w-md mx-auto">
                            Transkrip akan tampil setelah Admin Jenjang menerbitkan nilai untuk ananda
                            {{ $anak->nama }}. Hanya nilai berstatus <span class="font-bold">Terbit</span>
                            yang ditampilkan.
                        </p>
                    </div>
                @else
                    @php
                        $totalPeriode = $transkrip->count();
                        $totalMapel = $transkrip->sum('jumlah');
                        $rataList = $transkrip->pluck('rata')->filter(fn ($r) => $r !== null);
                        $rataSemua = $rataList->isNotEmpty() ? round((float) $rataList->avg(), 2) : null;
                        $kartuTranskrip = [
                            ['label' => 'Periode Tersedia', 'nilai' => $totalPeriode, 'ikon' => 'fa-calendar-days', 'warna' => 'text-sky-700'],
                            ['label' => 'Total Mata Pelajaran', 'nilai' => $totalMapel, 'ikon' => 'fa-book', 'warna' => 'text-violet-700'],
                            ['label' => 'Rata-rata Keseluruhan', 'nilai' => Akademik::formatNilai($rataSemua), 'ikon' => 'fa-chart-simple', 'warna' => 'text-emerald-700'],
                            ['label' => 'Predikat', 'nilai' => Akademik::predikat($rataSemua), 'ikon' => 'fa-award', 'warna' => 'text-amber-700'],
                        ];
                    @endphp

                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 flex items-start gap-2.5">
                        <i class="fa-solid fa-shield-halved text-emerald-600 mt-0.5"></i>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-wider text-emerald-700">Data Sesuai Jenjang</p>
                            <p class="text-xs text-emerald-900 mt-0.5">
                                Transkrip {{ $anak->nama }} hanya memuat nilai terbit dari jenjang
                                <span class="font-bold">{{ $anak->jenjang ?? 'yang terdaftar' }}</span>
                                {{ $anak->kelas ? '· Kelas '.$anak->kelas : '' }}.
                                Nilai dari jenjang lain tidak ditampilkan.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                        @foreach ($kartuTranskrip as $k)
                            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $k['label'] }}</span>
                                    <i class="fa-solid {{ $k['ikon'] }} text-xs text-slate-300"></i>
                                </div>
                                <p class="mt-2 text-xl font-black {{ $k['warna'] }}">{{ $k['nilai'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    @foreach ($transkrip as $trx)
                        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <h3 class="text-sm font-black text-slate-900">
                                        Tahun Ajaran {{ $trx['tahun_ajaran'] }} · Semester {{ $trx['label_semester'] }}
                                    </h3>
                                    <p class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $trx['jumlah'] }} mata pelajaran · {{ $trx['tuntas'] }} tuntas KKM
                                        @if ($trx['sumber'] === 'lama')
                                            · arsip nilai lama
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="rounded-xl bg-slate-900 px-3 py-1.5 text-[11px] font-black text-white">
                                        Rata-rata {{ Akademik::formatNilai($trx['rata']) }}
                                    </span>
                                    <span class="rounded-xl bg-emerald-100 px-3 py-1.5 text-[11px] font-black text-emerald-700">
                                        Predikat {{ $trx['predikat'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full min-w-[620px] text-left">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Mata Pelajaran</th>
                                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">KKM</th>
                                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">Nilai</th>
                                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">Huruf</th>
                                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">Ket.</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach ($trx['mapel'] as $row)
                                            <tr class="hover:bg-slate-50/70">
                                                <td class="px-4 py-3 text-xs font-bold text-slate-800">{{ $row['nama'] }}</td>
                                                <td class="px-4 py-3 text-center text-xs font-semibold text-slate-500">{{ Akademik::formatNilai($row['kkm']) }}</td>
                                                <td class="px-4 py-3 text-center text-xs font-black text-slate-900">{{ Akademik::formatNilai($row['nilai']) }}</td>
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
                    @endforeach

                    <p class="text-[10px] text-slate-400 text-center">
                        Transkrip ini hanya memuat nilai yang sudah diterbitkan Admin Jenjang.
                    </p>
                @endif
            </div>

            {{-- PANEL TAHFIDZ --}}
            <div data-tab-panel="tahfidz" id="panel-tahfidz" role="tabpanel" class="hidden space-y-4">
                @if ($tahfidz->isEmpty())
                    <div class="bg-white p-10 rounded-3xl border border-slate-200 shadow-sm text-center space-y-2">
                        <i class="fa-solid fa-book-quran text-3xl text-slate-300"></i>
                        <h3 class="text-sm font-black text-slate-900">Belum ada setoran tahfidz terbit</h3>
                        <p class="text-xs text-slate-500 max-w-md mx-auto">
                            Setoran hafalan yang sudah diterbitkan pengajar akan tampil di sini.
                        </p>
                    </div>
                @else
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100">
                            <h3 class="text-sm font-black text-slate-900">Setoran Tahfidz</h3>
                            <p class="text-[11px] text-slate-500">
                                {{ $filter['tahun'] }} · Semester {{ Akademik::labelSemester($filter['semester']) }}
                            </p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[680px] text-left">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Materi / Mapel</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">Nilai</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">Predikat</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Catatan</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($tahfidz as $row)
                                        <tr class="hover:bg-slate-50/70">
                                            <td class="px-4 py-3 text-xs font-bold text-slate-800">{{ $row['mapel'] }}</td>
                                            <td class="px-4 py-3 text-center text-xs font-black text-slate-900">{{ Akademik::formatNilai($row['nilai']) }}</td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-100 text-[11px] font-black text-emerald-700">
                                                    {{ $row['predikat'] }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-xs text-slate-600">{{ $row['catatan'] ?: '—' }}</td>
                                            <td class="px-4 py-3 text-center text-[11px] text-slate-500">
                                                {{ $row['tanggal'] ? Carbon::parse($row['tanggal'])->translatedFormat('d M Y') : '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            {{-- PANEL ABSENSI --}}
            <div data-tab-panel="absensi" id="panel-absensi" role="tabpanel" class="hidden space-y-4">
                @if ($absensi['total'] === 0)
                    <div class="bg-white p-10 rounded-3xl border border-slate-200 shadow-sm text-center space-y-2">
                        <i class="fa-solid fa-calendar-xmark text-3xl text-slate-300"></i>
                        <h3 class="text-sm font-black text-slate-900">Belum ada catatan absensi</h3>
                        <p class="text-xs text-slate-500 max-w-md mx-auto">
                            Absensi semester ini belum diinput, atau data santri belum tertaut ke absensi pesantren.
                        </p>
                    </div>
                @else
                    @php
                        $rekapAbsen = [
                            ['label' => 'Hadir', 'nilai' => $absensi['rekap']->get('hadir', 0), 'warna' => 'text-emerald-700', 'bg' => 'bg-emerald-100'],
                            ['label' => 'Izin', 'nilai' => $absensi['rekap']->get('izin', 0), 'warna' => 'text-sky-700', 'bg' => 'bg-sky-100'],
                            ['label' => 'Sakit', 'nilai' => $absensi['rekap']->get('sakit', 0), 'warna' => 'text-amber-700', 'bg' => 'bg-amber-100'],
                            ['label' => 'Alpa', 'nilai' => $absensi['rekap']->get('alpa', 0), 'warna' => 'text-rose-700', 'bg' => 'bg-rose-100'],
                        ];
                    @endphp
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                        @foreach ($rekapAbsen as $k)
                            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $k['label'] }}</span>
                                <p class="mt-2 text-xl font-black {{ $k['warna'] }}">{{ $k['nilai'] }} <span class="text-[10px] font-semibold text-slate-400">hari</span></p>
                            </div>
                        @endforeach
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100">
                            <h3 class="text-sm font-black text-slate-900">Riwayat Absensi</h3>
                            <p class="text-[11px] text-slate-500">Menampilkan {{ $absensi['total'] }} catatan terbaru pada semester ini.</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[640px] text-left">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Tanggal</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Kegiatan</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">Status</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($absensi['rows'] as $row)
                                        @php
                                            $status = strtolower((string) $row->status_kehadiran);
                                            $badge = match ($status) {
                                                'hadir' => 'bg-emerald-100 text-emerald-700',
                                                'izin' => 'bg-sky-100 text-sky-700',
                                                'sakit' => 'bg-amber-100 text-amber-700',
                                                default => 'bg-rose-100 text-rose-700',
                                            };
                                        @endphp
                                        <tr class="hover:bg-slate-50/70">
                                            <td class="px-4 py-3 text-xs font-semibold text-slate-700">
                                                {{ Carbon::parse($row->tanggal)->translatedFormat('d M Y') }}
                                            </td>
                                            <td class="px-4 py-3 text-xs text-slate-700">{{ $row->jenis_kegiatan }}</td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-black uppercase {{ $badge }}">
                                                    {{ $row->status_kehadiran }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-xs text-slate-500">{{ $row->keterangan ?: '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            {{-- PANEL KEUANGAN --}}
            <div data-tab-panel="keuangan" id="panel-keuangan" role="tabpanel" class="hidden space-y-4">
                @if ($keuangan['rows']->isEmpty())
                    <div class="bg-white p-10 rounded-3xl border border-slate-200 shadow-sm text-center space-y-2">
                        <i class="fa-solid fa-wallet text-3xl text-slate-300"></i>
                        <h3 class="text-sm font-black text-slate-900">Belum ada tagihan</h3>
                        <p class="text-xs text-slate-500 max-w-md mx-auto">
                            Tagihan administrasi (SPP, daftar ulang, kitab, dan lain-lain) akan tampil di sini
                            setelah dicatat oleh bagian keuangan.
                        </p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Tagihan</span>
                            <p class="mt-2 text-lg font-black text-slate-900">Rp {{ number_format($keuangan['total'], 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Sudah Dibayar</span>
                            <p class="mt-2 text-lg font-black text-emerald-700">Rp {{ number_format($keuangan['dibayar'], 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Sisa Tagihan</span>
                            <p class="mt-2 text-lg font-black {{ $keuangan['sisa'] > 0 ? 'text-rose-700' : 'text-emerald-700' }}">
                                Rp {{ number_format($keuangan['sisa'], 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100">
                            <h3 class="text-sm font-black text-slate-900">Rincian Tagihan</h3>
                            <p class="text-[11px] text-slate-500">Status pembayaran diperbarui oleh bagian keuangan.</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[720px] text-left">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Jenis</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Periode</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-right">Nominal</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-right">Dibayar</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-right">Sisa</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">Status</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">Jatuh Tempo</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($keuangan['rows'] as $row)
                                        @php
                                            $statusBayar = match ($row->status) {
                                                'lunas' => 'bg-emerald-100 text-emerald-700',
                                                'sebagian' => 'bg-amber-100 text-amber-700',
                                                default => 'bg-rose-100 text-rose-700',
                                            };
                                        @endphp
                                        <tr class="hover:bg-slate-50/70">
                                            <td class="px-4 py-3">
                                                <p class="text-xs font-bold text-slate-800">{{ $row->jenis }}</p>
                                                @if ($row->keterangan)
                                                    <p class="text-[10px] text-slate-400">{{ $row->keterangan }}</p>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-xs text-slate-600">{{ $row->periode ?: '—' }}</td>
                                            <td class="px-4 py-3 text-xs text-right text-slate-700">Rp {{ number_format((float) $row->nominal, 0, ',', '.') }}</td>
                                            <td class="px-4 py-3 text-xs text-right text-emerald-700">Rp {{ number_format((float) $row->dibayar, 0, ',', '.') }}</td>
                                            <td class="px-4 py-3 text-xs text-right font-bold {{ $row->sisa() > 0 ? 'text-rose-700' : 'text-emerald-700' }}">
                                                Rp {{ number_format($row->sisa(), 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-black {{ $statusBayar }}">
                                                    {{ $row->labelStatus() }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center text-[11px] text-slate-500">
                                                {{ $row->jatuh_tempo ? $row->jatuh_tempo->translatedFormat('d M Y') : '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        @endif

    </div>

    @if ($anak)
        <script>
            (function () {
                const tombol = Array.from(document.querySelectorAll('[data-tab-target]'));
                const panel = Array.from(document.querySelectorAll('[data-tab-panel]'));
                const aktif = 'bg-emerald-700 text-white shadow-sm border-emerald-700';
                const pasif = 'bg-white text-slate-600 border border-slate-200 hover:border-emerald-400';

                function tampilkan(nama) {
                    tombol.forEach(function (b) {
                        const dipilih = b.dataset.tabTarget === nama;
                        b.classList.remove('bg-emerald-700', 'text-white', 'shadow-sm', 'border-emerald-700');
                        b.classList.remove('bg-white', 'text-slate-600', 'border', 'border-slate-200', 'hover:border-emerald-400');
                        b.className += ' ' + (dipilih ? aktif : pasif);
                        b.setAttribute('aria-selected', dipilih ? 'true' : 'false');
                    });

                    panel.forEach(function (p) {
                        p.classList.toggle('hidden', p.dataset.tabPanel !== nama);
                    });

                    const url = new URL(window.location.href);
                    url.searchParams.set('tab', nama);
                    window.history.replaceState({}, '', url);
                }

                tombol.forEach(function (b) {
                    b.addEventListener('click', function () {
                        tampilkan(b.dataset.tabTarget);
                    });
                });

                tampilkan(@json($tabAktif));
            })();
        </script>
    @endif
@endsection
