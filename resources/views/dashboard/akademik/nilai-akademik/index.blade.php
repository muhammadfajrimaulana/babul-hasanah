@extends('layouts.admin')

@section('title', 'Manajemen Nilai Akademik')

@use('App\Support\Akademik')
@use('App\Support\NilaiAkademik')

@section('content')
    @php
        $user = auth()->user();
        $bisaUbah = $user->bisa('akademik.ubah');
        $bisaTerbit = $user->bisa('akademik.terbit');
        $queryKonteks = array_filter([
            'class_id' => $filter['class_id'],
            'subject_id' => $filter['subject_id'],
            'tahun_ajaran' => $filter['tahun'],
            'semester' => $filter['semester'],
        ], fn ($nilai) => $nilai !== null && $nilai !== '');
        $statusSiswa = fn ($rows) => NilaiAkademik::statusSiswa($rows);
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                    <i class="fa-solid fa-book-open-reader"></i> Akademik
                </div>
                <h2 class="mt-1 text-xl font-black text-slate-900 dark:text-white">Manajemen Nilai Akademik</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-2xl">
                    Input nilai per kelas dan mata pelajaran, unduh template Excel, unggah kembali,
                    lalu terbitkan agar tampil di Portal Wali Santri.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10 px-3 py-2 text-[11px] font-bold text-emerald-800 dark:text-emerald-300">
                    <i class="fa-solid fa-building-columns"></i> {{ $user->namaJenjang() }}
                </span>
                @if (! $bisaUbah)
                    <span class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 px-3 py-2 text-[11px] font-bold text-slate-600 dark:text-slate-300">
                        <i class="fa-solid fa-eye"></i> Mode monitoring (hanya baca)
                    </span>
                @endif
            </div>
        </div>

        @include('dashboard.akademik._tabs')
        @include('dashboard.akademik._peringatan-jenjang')

        @foreach (['success' => 'emerald', 'error' => 'rose', 'info' => 'sky'] as $jenis => $warna)
            @if (session($jenis))
                <div class="rounded-2xl border border-{{ $warna }}-200 dark:border-{{ $warna }}-500/30 bg-{{ $warna }}-50 dark:bg-{{ $warna }}-500/10 px-4 py-3.5 text-xs font-bold text-{{ $warna }}-800 dark:text-{{ $warna }}-300 flex items-start gap-2.5">
                    <i class="fa-solid {{ $jenis === 'success' ? 'fa-circle-check' : ($jenis === 'error' ? 'fa-triangle-exclamation' : 'fa-circle-info') }} mt-0.5"></i>
                    <span>{{ session($jenis) }}</span>
                </div>
            @endif
        @endforeach

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 dark:border-rose-500/30 bg-rose-50 dark:bg-rose-500/10 px-4 py-3.5 text-xs text-rose-800 dark:text-rose-300 space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="flex items-start gap-2"><i class="fa-solid fa-triangle-exclamation mt-0.5"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (session('import_errors'))
            <div class="rounded-2xl border border-amber-200 dark:border-amber-500/30 bg-amber-50 dark:bg-amber-500/10 px-4 py-3.5 text-xs text-amber-900 dark:text-amber-200 space-y-1">
                <p class="font-black flex items-center gap-2"><i class="fa-solid fa-file-circle-exclamation"></i> Sebagian baris tidak diproses:</p>
                @foreach (session('import_errors') as $barisError)
                    <p class="pl-6">{{ $barisError }}</p>
                @endforeach
            </div>
        @endif

        @include('dashboard.akademik.nilai-akademik._filter', [
            'action' => route('admin.akademik.nilai.index'),
        ])

        @if ($filter['mismatch'])
            <div class="rounded-2xl border border-amber-200 dark:border-amber-500/30 bg-amber-50 dark:bg-amber-500/10 px-4 py-3.5 text-xs font-bold text-amber-800 dark:text-amber-300">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                Mata pelajaran yang dipilih bukan untuk jenjang kelas ini. Pilih pasangan kelas dan mapel yang sesuai.
            </div>
        @endif

        @if (! $filter['lengkap'])
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-5">
                <div class="max-w-xl space-y-1">
                    <h3 class="text-base font-black text-slate-900 dark:text-white">Mulai input nilai</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Pilih kelas, mata pelajaran, tahun ajaran, dan semester di atas.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/40 p-4 space-y-2">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-sm font-black">1</span>
                        <h4 class="text-xs font-black text-slate-900 dark:text-white">Pilih konteks</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Kelas dan mapel harus satu jenjang. Tahun ajaran & semester menjadi arsip nilai.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/40 p-4 space-y-2">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300 text-sm font-black">2</span>
                        <h4 class="text-xs font-black text-slate-900 dark:text-white">Input atau unggah</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Isi manual di tabel, atau unduh template Excel lalu unggah kembali.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/40 p-4 space-y-2">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 text-sm font-black">3</span>
                        <h4 class="text-xs font-black text-slate-900 dark:text-white">Terbitkan</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Nilai tersimpan sebagai draft. Setelah terbit, nilai terlihat oleh wali santri.</p>
                    </div>
                </div>
            </div>
        @else
            @php
                $kartu = [
                    ['label' => 'Santri di kelas', 'nilai' => $ringkasan['total'], 'ikon' => 'fa-users', 'warna' => 'text-slate-700 dark:text-slate-200'],
                    ['label' => 'Sudah diisi', 'nilai' => $ringkasan['terisi'], 'ikon' => 'fa-pen-nib', 'warna' => 'text-sky-700 dark:text-sky-300'],
                    ['label' => 'Draft', 'nilai' => $ringkasan['draft'], 'ikon' => 'fa-clock', 'warna' => 'text-amber-700 dark:text-amber-300'],
                    ['label' => 'Terbit', 'nilai' => $ringkasan['terbit'], 'ikon' => 'fa-circle-check', 'warna' => 'text-emerald-700 dark:text-emerald-300'],
                    ['label' => 'Rata-rata', 'nilai' => Akademik::formatNilai($ringkasan['rata']), 'ikon' => 'fa-chart-simple', 'warna' => 'text-violet-700 dark:text-violet-300'],
                ];
            @endphp

            <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
                @foreach ($kartu as $k)
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $k['label'] }}</span>
                            <i class="fa-solid {{ $k['ikon'] }} text-xs text-slate-300"></i>
                        </div>
                        <p class="mt-2 text-xl font-black {{ $k['warna'] }}">{{ $k['nilai'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <div class="flex items-start gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-sky-600 text-white text-sm shrink-0"><i class="fa-solid fa-file-excel"></i></span>
                    <div>
                        <h4 class="text-xs font-black text-slate-900 dark:text-white">Template Excel & unggah massal</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            Template memuat {{ $siswa->count() }} santri di kelas {{ $filter['kelas']->name }}.
                            Kolom nilai yang sudah ada ikut terisi otomatis.
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($bisaUbah)
                        <a href="{{ route('admin.akademik.nilai.template', $queryKonteks) }}"
                            class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10 px-3.5 py-2.5 text-[11px] font-bold text-emerald-800 dark:text-emerald-300 hover:border-emerald-400 transition">
                            <i class="fa-solid fa-download"></i> Template Excel
                        </a>
                        <a href="{{ route('admin.akademik.nilai.upload', $queryKonteks) }}"
                            class="inline-flex items-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 px-3.5 py-2.5 text-[11px] font-bold text-white transition">
                            <i class="fa-solid fa-upload"></i> Upload Excel
                        </a>
                    @endif
                    @if ($bisaTerbit)
                        <form method="POST" action="{{ route('admin.akademik.nilai.terbitkan') }}"
                            onsubmit="return confirm('Terbitkan semua nilai draft pada konteks ini? Nilai akan langsung terlihat di Portal Wali Santri.')">
                            @csrf
                            @foreach ($queryKonteks as $nama => $nilai)
                                <input type="hidden" name="{{ $nama }}" value="{{ $nilai }}">
                            @endforeach
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-amber-500 hover:bg-amber-600 px-3.5 py-2.5 text-[11px] font-black text-slate-900 transition">
                                <i class="fa-solid fa-paper-plane"></i> Terbitkan
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if ($siswa->isEmpty())
                <div class="rounded-3xl border border-amber-200 dark:border-amber-500/30 bg-amber-50 dark:bg-amber-500/10 p-8 text-center space-y-2">
                    <i class="fa-solid fa-user-slash text-2xl text-amber-500"></i>
                    <h3 class="text-sm font-black text-amber-900 dark:text-amber-200">Belum ada santri di kelas ini</h3>
                    <p class="text-xs text-amber-800/80 dark:text-amber-300/80">Plot santri ke kelas melalui menu PPDB / Data Santri terlebih dahulu.</p>
                </div>
            @else
                <form method="POST" action="{{ route('admin.akademik.nilai.store') }}">
                    @csrf
                    @foreach ($queryKonteks as $nama => $nilai)
                        <input type="hidden" name="{{ $nama }}" value="{{ $nilai }}">
                    @endforeach

                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <h3 class="text-sm font-black text-slate-900 dark:text-white">
                                    {{ $filter['mapel']->nama }} — {{ $filter['kelas']->label() }}
                                </h3>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    {{ $filter['tahun'] }} · Semester {{ Akademik::labelSemester($filter['semester']) }}
                                </p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 px-2.5 py-1 text-[10px] font-bold text-slate-500 dark:text-slate-300">
                                <i class="fa-solid fa-table-cells"></i> Nilai akhir = rata-rata kategori terisi
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[980px] text-left">
                                <thead class="bg-slate-50 dark:bg-slate-800/60">
                                    <tr>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">No</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">NISN</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Nama Santri</th>
                                        @foreach (Akademik::KATEGORI as $label)
                                            <th class="px-3 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center w-24">{{ $label }}</th>
                                        @endforeach
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">Akhir</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">Predikat</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @foreach ($siswa as $student)
                                        @php
                                            $rows = $matriks->get($student->id) ?? collect();
                                            $akhir = NilaiAkademik::nilaiAkhir($rows);
                                            $status = $statusSiswa($rows);
                                        @endphp
                                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                                            <td class="px-4 py-3 text-xs text-slate-400">{{ $loop->iteration }}</td>
                                            <td class="px-4 py-3 text-[11px] font-mono text-slate-500 dark:text-slate-400">{{ $student->nisn }}</td>
                                            <td class="px-4 py-3">
                                                <p class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ $student->name }}</p>
                                                <p class="text-[10px] text-slate-400">Kelas {{ $student->kelas?->name ?? $filter['kelas']->name }}</p>
                                            </td>
                                            @foreach (array_keys(Akademik::KATEGORI) as $kategori)
                                                <td class="px-2 py-3">
                                                    <input type="number" step="0.01" min="0" max="100"
                                                        name="nilai[{{ $student->id }}][{{ $kategori }}]"
                                                        value="{{ old('nilai.' . $student->id . '.' . $kategori, $rows->get($kategori)?->nilai) }}"
                                                        placeholder="0–100"
                                                        @disabled(! $bisaUbah)
                                                        class="w-20 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-2.5 py-2 text-xs font-bold text-center text-slate-800 dark:text-slate-100 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 disabled:bg-slate-100 disabled:text-slate-500 dark:disabled:bg-slate-800">
                                                </td>
                                            @endforeach
                                            <td class="px-4 py-3 text-center text-xs font-black text-slate-900 dark:text-white">{{ Akademik::formatNilai($akhir) }}</td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-800 text-[11px] font-black text-slate-700 dark:text-slate-200">
                                                    {{ Akademik::predikat($akhir) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if ($status === 'published')
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 dark:bg-emerald-500/20 px-2.5 py-1 text-[10px] font-black text-emerald-700 dark:text-emerald-300">
                                                        <i class="fa-solid fa-circle-check"></i> Terbit
                                                    </span>
                                                @elseif ($status === 'draft')
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 dark:bg-amber-500/20 px-2.5 py-1 text-[10px] font-black text-amber-700 dark:text-amber-300">
                                                        <i class="fa-solid fa-clock"></i> Draft
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-1 text-[10px] font-bold text-slate-400">
                                                        Belum diisi
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($bisaUbah)
                            <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <p class="text-[10px] text-slate-400">
                                    Nilai yang diubah otomatis kembali ke draft dan harus diterbitkan ulang.
                                </p>
                                <button type="submit"
                                    class="rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2.5 text-xs font-bold transition inline-flex items-center gap-2">
                                    <i class="fa-solid fa-floppy-disk"></i> Simpan sebagai Draft
                                </button>
                            </div>
                        @endif
                    </div>
                </form>
            @endif
        @endif
    </div>
@endsection
