@extends('layouts.admin')

@section('title', 'Manajemen Nilai - Akademik')

@use('App\Support\Akademik')

@section('content')
    @php
        $user = auth()->user();
        $bisaUbah = $user->bisa('akademik.ubah');
        $bisaTerbit = $user->bisa('akademik.terbit');
        $slugAdmin = $user->jenjangSlug();
        $queryKonteks = [
            'jenjang' => $filter['jenjang'],
            'kelas' => $filter['kelas'],
            'mata_pelajaran_id' => $filter['mapel']?->id,
            'tahun_ajaran' => $filter['tahun'],
            'semester' => $filter['semester'],
            'kategori' => $filter['kategori'],
        ];
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                    <i class="fa-solid fa-book-open-reader"></i> Akademik
                </div>
                <h2 class="mt-1 text-xl font-black text-slate-900 dark:text-white">Manajemen Nilai Santri</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-2xl">
                    Input nilai per kelas, unggah massal lewat CSV, lalu terbitkan agar terlihat di portal wali santri.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10 px-3 py-2 text-[11px] font-bold text-emerald-800 dark:text-emerald-300">
                    <i class="fa-solid fa-building-columns"></i>
                    {{ $user->namaJenjang() }}
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

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10 px-4 py-3.5 text-xs font-bold text-emerald-800 dark:text-emerald-300 flex items-start gap-2.5">
                <i class="fa-solid fa-circle-check mt-0.5"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 dark:border-rose-500/30 bg-rose-50 dark:bg-rose-500/10 px-4 py-3.5 text-xs font-bold text-rose-800 dark:text-rose-300 flex items-start gap-2.5">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if (session('info'))
            <div class="rounded-2xl border border-sky-200 dark:border-sky-500/30 bg-sky-50 dark:bg-sky-500/10 px-4 py-3.5 text-xs font-bold text-sky-800 dark:text-sky-300 flex items-start gap-2.5">
                <i class="fa-solid fa-circle-info mt-0.5"></i>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 dark:border-rose-500/30 bg-rose-50 dark:bg-rose-500/10 px-4 py-3.5 text-xs text-rose-800 dark:text-rose-300 space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="flex items-start gap-2"><i class="fa-solid fa-triangle-exclamation mt-0.5"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- FILTER KONTEKS --}}
        <form method="GET" action="{{ route('admin.akademik.nilai.index') }}"
            class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 space-y-4">
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-sm font-black text-slate-900 dark:text-white">
                    <i class="fa-solid fa-filter mr-1.5 text-emerald-600"></i> Pilih Konteks Nilai
                </h3>
                <span class="text-[10px] text-slate-400">Langkah 1 dari 3</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-6 gap-3">
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Jenjang</label>
                    @if ($slugAdmin)
                        <input type="hidden" name="jenjang" value="{{ $slugAdmin }}">
                        <div class="w-full rounded-xl border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10 px-3 py-2.5 text-xs font-bold text-emerald-800 dark:text-emerald-300">
                            {{ $user->namaJenjang() }}
                        </div>
                    @else
                        <select name="jenjang" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                            <option value="">Semua jenjang</option>
                            @foreach ($daftarJenjang as $j)
                                <option value="{{ $j['slug'] }}" @selected($filter['jenjang'] === $j['slug'])>
                                    {{ $j['kode'] }} — {{ $j['nama'] }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Kelas</label>
                    <select name="kelas" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                        <option value="">Pilih kelas</option>
                        @foreach ($daftarKelas as $kelas)
                            <option value="{{ $kelas }}" @selected($filter['kelas'] === $kelas)>{{ $kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Mata Pelajaran</label>
                    <select name="mata_pelajaran_id" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                        <option value="">Pilih mapel</option>
                        @foreach ($daftarMapel as $mapel)
                            <option value="{{ $mapel->id }}" @selected($filter['mapel']?->id === $mapel->id)>
                                {{ $mapel->kode }} — {{ $mapel->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Tahun Ajaran</label>
                    <select name="tahun_ajaran" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                        @foreach ($tahunPilihan as $tahun)
                            <option value="{{ $tahun }}" @selected($filter['tahun'] === $tahun)>{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Semester</label>
                    <select name="semester" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                        @foreach (Akademik::SEMESTER as $nilai => $label)
                            <option value="{{ $nilai }}" @selected($filter['semester'] === $nilai)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Kategori Nilai</label>
                    <select name="kategori" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                        @foreach (Akademik::KATEGORI as $nilai => $label)
                            <option value="{{ $nilai }}" @selected($filter['kategori'] === $nilai)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                <p class="text-[10px] text-slate-400">
                    Jenjang admin otomatis terkunci. Data jenjang lain tidak dapat diakses.
                </p>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.akademik.nilai.index') }}"
                        class="rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        Reset
                    </a>
                    <button type="submit"
                        class="rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2.5 text-xs font-bold transition inline-flex items-center gap-2">
                        <i class="fa-solid fa-magnifying-glass"></i> Tampilkan Nilai
                    </button>
                </div>
            </div>
        </form>

        @if ($filter['mismatch'])
            <div class="rounded-2xl border border-amber-200 dark:border-amber-500/30 bg-amber-50 dark:bg-amber-500/10 px-4 py-3.5 text-xs font-bold text-amber-800 dark:text-amber-300">
                Mata pelajaran yang dipilih bukan untuk jenjang ini. Pilih mapel lain.
            </div>
        @endif

        @if (! $filter['lengkap'])
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-5">
                <div class="max-w-xl space-y-1">
                    <h3 class="text-base font-black text-slate-900 dark:text-white">Mulai input nilai</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Lengkapi pilihan kelas, mata pelajaran, tahun ajaran, semester, dan kategori di atas.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/40 p-4 space-y-2">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-sm font-black">1</span>
                        <h4 class="text-xs font-black text-slate-900 dark:text-white">Pilih kelas & mapel</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Jenjang mengikuti akun Anda. Tahun ajaran dan semester menentukan arsip nilai.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/40 p-4 space-y-2">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300 text-sm font-black">2</span>
                        <h4 class="text-xs font-black text-slate-900 dark:text-white">Input atau unggah</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Isi langsung di tabel, atau unduh template CSV lalu unggah kembali.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/40 p-4 space-y-2">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 text-sm font-black">3</span>
                        <h4 class="text-xs font-black text-slate-900 dark:text-white">Terbitkan</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Nilai berstatus draft. Setelah diterbitkan, nilai tampil di portal wali santri.</p>
                    </div>
                </div>

                @if (! $bisaUbah)
                    <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400">
                        <i class="fa-solid fa-eye mr-1"></i> Anda hanya dapat melihat; tombol simpan dan terbitkan tidak tersedia.
                    </p>
                @endif
            </div>
        @else
            {{-- RINGKASAN --}}
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
                @php
                    $kartu = [
                        ['label' => 'Santri di kelas', 'nilai' => $ringkasan['total'], 'ikon' => 'fa-users', 'warna' => 'text-slate-700 dark:text-slate-200'],
                        ['label' => 'Sudah diisi', 'nilai' => $ringkasan['terisi'], 'ikon' => 'fa-pen-nib', 'warna' => 'text-sky-700 dark:text-sky-300'],
                        ['label' => 'Draft', 'nilai' => $ringkasan['draft'], 'ikon' => 'fa-clock', 'warna' => 'text-amber-700 dark:text-amber-300'],
                        ['label' => 'Terbit', 'nilai' => $ringkasan['terbit'], 'ikon' => 'fa-circle-check', 'warna' => 'text-emerald-700 dark:text-emerald-300'],
                        ['label' => 'Rata-rata', 'nilai' => Akademik::formatNilai($ringkasan['rata']), 'ikon' => 'fa-chart-simple', 'warna' => 'text-violet-700 dark:text-violet-300'],
                    ];
                @endphp
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

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="rounded-2xl border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50/60 dark:bg-emerald-500/10 p-4">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-600 text-white text-sm"><i class="fa-solid fa-keyboard"></i></span>
                        <div>
                            <h4 class="text-xs font-black text-emerald-900 dark:text-emerald-200">Opsi A — Input manual</h4>
                            <p class="text-[11px] text-emerald-800/80 dark:text-emerald-300/80">Isi nilai pada tabel di bawah, lalu simpan sebagai draft.</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-sky-200 dark:border-sky-500/30 bg-sky-50/60 dark:bg-sky-500/10 p-4 flex items-center justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-sky-600 text-white text-sm"><i class="fa-solid fa-file-arrow-up"></i></span>
                        <div>
                            <h4 class="text-xs font-black text-sky-900 dark:text-sky-200">Opsi B — Upload CSV / Excel</h4>
                            <p class="text-[11px] text-sky-800/80 dark:text-sky-300/80">Unduh template, isi nilai, unggah, lalu simpan setelah pratinjau.</p>
                        </div>
                    </div>
                    @if ($bisaUbah)
                        <a href="{{ route('admin.akademik.nilai.upload', $queryKonteks) }}"
                            class="shrink-0 rounded-xl bg-sky-600 hover:bg-sky-700 text-white px-3.5 py-2.5 text-[11px] font-bold transition inline-flex items-center gap-2">
                            <i class="fa-solid fa-upload"></i> Unggah
                        </a>
                    @endif
                </div>
            </div>

            @if ($santris->isEmpty())
                <div class="rounded-3xl border border-amber-200 dark:border-amber-500/30 bg-amber-50 dark:bg-amber-500/10 p-8 text-center space-y-2">
                    <i class="fa-solid fa-user-slash text-2xl text-amber-500"></i>
                    <h3 class="text-sm font-black text-amber-900 dark:text-amber-200">Belum ada santri di kelas ini</h3>
                    <p class="text-xs text-amber-800/80 dark:text-amber-300/80">Pastikan data santri sudah memiliki jenjang dan kelas yang sesuai.</p>
                </div>
            @else
                <form method="POST" action="{{ route('admin.akademik.nilai.store') }}">
                    @csrf
                    <input type="hidden" name="jenjang" value="{{ $filter['jenjang'] }}">
                    <input type="hidden" name="kelas" value="{{ $filter['kelas'] }}">
                    <input type="hidden" name="mata_pelajaran_id" value="{{ $filter['mapel']->id }}">
                    <input type="hidden" name="tahun_ajaran" value="{{ $filter['tahun'] }}">
                    <input type="hidden" name="semester" value="{{ $filter['semester'] }}">
                    <input type="hidden" name="kategori" value="{{ $filter['kategori'] }}">

                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <h3 class="text-sm font-black text-slate-900 dark:text-white">
                                    {{ $filter['mapel']->nama }} — Kelas {{ $filter['kelas'] }}
                                </h3>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    {{ Akademik::labelKategori($filter['kategori']) }} · {{ $filter['tahun'] }} · Semester {{ Akademik::labelSemester($filter['semester']) }}
                                </p>
                            </div>
                            @if ($bisaUbah)
                                <a href="{{ route('admin.akademik.nilai.template', $queryKonteks) }}"
                                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 px-3 py-2 text-[11px] font-bold text-slate-600 dark:text-slate-300 hover:border-emerald-400 transition">
                                    <i class="fa-solid fa-download"></i> Template CSV
                                </a>
                            @endif
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[720px] text-left">
                                <thead class="bg-slate-50 dark:bg-slate-800/60">
                                    <tr>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">No</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">NIS</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Nama Santri</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400 w-32">Nilai</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Status</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @foreach ($santris as $santri)
                                        @php($row = $nilai->get($santri->id))
                                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                                            <td class="px-4 py-3 text-xs text-slate-400">{{ $loop->iteration }}</td>
                                            <td class="px-4 py-3 text-[11px] font-mono text-slate-500 dark:text-slate-400">{{ $santri->nomor_induk }}</td>
                                            <td class="px-4 py-3">
                                                <p class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ $santri->nama_lengkap }}</p>
                                                <p class="text-[10px] text-slate-400">Kelas {{ $santri->kelas }}</p>
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" name="nilai[{{ $santri->id }}]" step="0.01" min="0" max="100"
                                                    value="{{ old('nilai.' . $santri->id, $row?->nilai) }}"
                                                    placeholder="0–100"
                                                    @disabled(! $bisaUbah)
                                                    class="w-24 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-xs font-bold text-slate-800 dark:text-slate-100 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 disabled:bg-slate-100 disabled:text-slate-500 dark:disabled:bg-slate-800">
                                            </td>
                                            <td class="px-4 py-3">
                                                @if ($row)
                                                    @if ($row->status === 'published')
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 dark:bg-emerald-500/20 px-2.5 py-1 text-[10px] font-black text-emerald-700 dark:text-emerald-300">
                                                            <i class="fa-solid fa-circle-check"></i> Terbit
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 dark:bg-amber-500/20 px-2.5 py-1 text-[10px] font-black text-amber-700 dark:text-amber-300">
                                                            <i class="fa-solid fa-clock"></i> Draft
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-1 text-[10px] font-bold text-slate-400">
                                                        Belum diisi
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="text" name="catatan[{{ $santri->id }}]" maxlength="255"
                                                    value="{{ old('catatan.' . $santri->id, $row?->catatan) }}"
                                                    placeholder="Opsional"
                                                    @disabled(! $bisaUbah)
                                                    class="w-full min-w-[140px] rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-xs text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 disabled:bg-slate-100 dark:disabled:bg-slate-800">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <p class="text-[10px] text-slate-400">
                                Nilai yang diubah akan kembali ke status draft dan harus diterbitkan ulang.
                            </p>
                            @if ($bisaUbah || $bisaTerbit)
                                <div class="flex flex-wrap items-center gap-2">
                                    @if ($bisaUbah)
                                        <button type="submit"
                                            class="rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2.5 text-xs font-bold transition inline-flex items-center gap-2">
                                            <i class="fa-solid fa-floppy-disk"></i> Simpan sebagai Draft
                                        </button>
                                    @endif
                                    @if ($bisaTerbit)
                                        <button type="submit" form="formTerbitkan"
                                            onclick="return confirm('Terbitkan semua nilai draft pada konteks ini? Nilai akan langsung terlihat di portal wali santri.')"
                                            class="rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-900 px-4 py-2.5 text-xs font-black transition inline-flex items-center gap-2">
                                            <i class="fa-solid fa-paper-plane"></i> Terbitkan
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </form>

                @if ($bisaTerbit)
                    <form id="formTerbitkan" method="POST" action="{{ route('admin.akademik.nilai.terbitkan') }}" class="hidden">
                        @csrf
                        <input type="hidden" name="jenjang" value="{{ $filter['jenjang'] }}">
                        <input type="hidden" name="kelas" value="{{ $filter['kelas'] }}">
                        <input type="hidden" name="mata_pelajaran_id" value="{{ $filter['mapel']->id }}">
                        <input type="hidden" name="tahun_ajaran" value="{{ $filter['tahun'] }}">
                        <input type="hidden" name="semester" value="{{ $filter['semester'] }}">
                        <input type="hidden" name="kategori" value="{{ $filter['kategori'] }}">
                    </form>
                @endif
            @endif
        @endif
    </div>
@endsection
