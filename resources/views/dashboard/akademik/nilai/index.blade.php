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
        // Alias agar tidak bentrok dengan variabel loop di bawah.
        $nilaiSantri = $nilai;
        $inputCls = 'w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 px-3.5 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500';
        $labelCls = 'block text-[11px] font-black text-slate-600 dark:text-slate-300 mb-1.5';
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                    <i class="fa-solid fa-book-open-reader"></i> Akademik
                </div>
                <h2 class="mt-1 text-xl font-black text-slate-900 dark:text-white">Manajemen Nilai Santri</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-2xl">
                    Satu halaman untuk input massal per kelas (draft → terbit), import nilai CSV, input cepat satu santri, dan unggah rapor PDF.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if ($bisaUbah && $filter['jenjang'])
                    <a href="#input-cepat"
                        class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-[11px] font-bold text-emerald-700 transition hover:bg-emerald-100 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
                        <i class="fa-solid fa-bolt"></i> Input Cepat
                    </a>
                    <a href="#import-nilai"
                        class="inline-flex items-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-3 py-2 text-[11px] font-bold text-sky-700 transition hover:bg-sky-100 dark:border-sky-500/30 dark:bg-sky-500/10 dark:text-sky-300">
                        <i class="fa-solid fa-file-arrow-up"></i> Import CSV
                    </a>
                @endif
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

        @if (session('import_errors'))
            <div class="rounded-2xl border border-amber-200 dark:border-amber-500/30 bg-amber-50 dark:bg-amber-500/10 px-4 py-3.5 text-xs text-amber-800 dark:text-amber-300">
                <p class="font-black flex items-start gap-2">
                    <i class="fa-solid fa-list-check mt-0.5"></i> Sebagian baris dilewati:
                </p>
                <ul class="mt-1.5 list-disc space-y-0.5 pl-5">
                    @foreach (session('import_errors') as $pesanError)
                        <li>{{ $pesanError }}</li>
                    @endforeach
                </ul>
                @if (count(session('import_errors')) >= 20)
                    <p class="mt-1.5 text-[10px] font-bold">Hanya 20 error pertama yang ditampilkan.</p>
                @endif
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

            <div class="grid grid-cols-1 sm:grid-cols-2 {{ $slugAdmin ? 'xl:grid-cols-5' : 'xl:grid-cols-6' }} gap-3">
                @if ($slugAdmin)
                    {{-- Admin Jenjang: jenjang terkunci dan tidak ditampilkan. --}}
                    <input type="hidden" name="jenjang" value="{{ $slugAdmin }}">
                @else
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Jenjang</label>
                        <select name="jenjang" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                            <option value="">Semua jenjang</option>
                            @foreach ($daftarJenjang as $j)
                                <option value="{{ $j['slug'] }}" @selected($filter['jenjang'] === $j['slug'])>
                                    {{ $j['kode'] }} — {{ $j['nama'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

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
                                {{ $mapel->kode }} — {{ $mapel->nama }}{{ $mapel->guru ? ' · '.$mapel->guru->nama : '' }}
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
                        @foreach (Akademik::SEMESTER as $kodeSemester => $labelSemester)
                            <option value="{{ $kodeSemester }}" @selected($filter['semester'] === $kodeSemester)>{{ $labelSemester }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Kategori Nilai</label>
                    <select name="kategori" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                        @foreach (Akademik::KATEGORI as $kodeKategori => $labelKategori)
                            <option value="{{ $kodeKategori }}" @selected($filter['kategori'] === $kodeKategori)>{{ $labelKategori }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                @if ($slugAdmin)
                    <p class="text-[10px] text-slate-400">
                        Data otomatis mengikuti jenjang {{ $user->namaJenjang() }}. Data jenjang lain tidak dapat diakses.
                    </p>
                @else
                    <p class="text-[10px] text-slate-400">
                        Pilih jenjang untuk mempersempit data, atau kosongkan untuk seluruh jenjang.
                    </p>
                @endif
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

        {{-- INPUT CEPAT SATU SANTRI (digabung dari dashboard jenjang) --}}
        @if ($bisaUbah)
            @if ($filter['jenjang'])
                <section id="input-cepat"
                    class="scroll-mt-24 rounded-3xl border border-emerald-200 dark:border-emerald-500/30 bg-white dark:bg-slate-900 p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div class="max-w-2xl">
                            <h3 class="inline-flex items-center gap-2 text-sm font-black text-slate-900 dark:text-white">
                                <i class="fa-solid fa-bolt text-emerald-600"></i> Input Cepat Satu Santri
                            </h3>
                            <p class="mt-1 text-[11px] leading-relaxed text-slate-500 dark:text-slate-400">
                                Menyimpan satu nilai tanpa perlu melengkapi kelas &amp; mapel pada filter di atas.
                                Nilai disimpan dan <b>langsung terbit</b> ke portal wali santri.
                            </p>
                        </div>
                        <span
                            class="inline-flex shrink-0 items-center gap-1.5 rounded-xl bg-emerald-50 px-3 py-2 text-[10px] font-black text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                            <i class="fa-solid fa-paper-plane"></i> Langsung terbit
                        </span>
                    </div>

                    @if ($santriList->isEmpty())
                        <div
                            class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 text-xs text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
                            <p class="font-black"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Belum ada santri pada jenjang ini.</p>
                            <p class="mt-1 leading-relaxed">
                                Tambahkan data santri melalui menu
                                <a href="{{ route('admin.santri.index') }}" class="font-bold underline">Data Santri</a>.
                            </p>
                        </div>
                    @elseif ($mapelList->isEmpty())
                        <div
                            class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 text-xs text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
                            <p class="font-black"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Belum ada mata pelajaran aktif.</p>
                            <p class="mt-1 leading-relaxed">
                                Tambahkan mata pelajaran melalui menu
                                <a href="{{ route('admin.akademik.mapel.index') }}" class="font-bold underline">Akademik → Mata Pelajaran</a>.
                            </p>
                        </div>
                    @else
                        <form method="POST" action="{{ route('admin.dashboard.jenjang.nilai') }}" class="mt-5 space-y-4">
                            @csrf
                            <input type="hidden" name="jenjang" value="{{ $filter['jenjang'] }}">

                            @include('dashboard.roles._santri-picker', ['id' => 'nilai_santri_id'])

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                                <div class="xl:col-span-3">
                                    <label for="mata_pelajaran_id" class="{{ $labelCls }}">Mata Pelajaran <span class="text-rose-500">*</span></label>
                                    <select id="mata_pelajaran_id" name="mata_pelajaran_id" required class="{{ $inputCls }}">
                                        <option value="" disabled @selected(old('mata_pelajaran_id') === null)>Pilih mata pelajaran</option>
                                        @foreach ($mapelList as $mapel)
                                            <option value="{{ $mapel->id }}" @selected((string) old('mata_pelajaran_id') === (string) $mapel->id)">
                                                {{ $mapel->kode ? $mapel->kode.' — ' : '' }}{{ $mapel->nama }}{{ $mapel->guru ? ' · '.$mapel->guru->nama : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="tahun_ajaran" class="{{ $labelCls }}">Tahun Ajaran <span class="text-rose-500">*</span></label>
                                    <select id="tahun_ajaran" name="tahun_ajaran" required class="{{ $inputCls }}">
                                        @foreach ($tahunPilihan as $tahun)
                                            <option value="{{ $tahun }}" @selected(old('tahun_ajaran', $filter['tahun']) === $tahun)>{{ $tahun }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="semester" class="{{ $labelCls }}">Semester <span class="text-rose-500">*</span></label>
                                    <select id="semester" name="semester" required class="{{ $inputCls }}">
                                        @foreach (Akademik::SEMESTER as $kodeSemester => $labelSemester)
                                            <option value="{{ $kodeSemester }}" @selected(old('semester', $filter['semester']) === $kodeSemester)>{{ $labelSemester }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="kategori" class="{{ $labelCls }}">Kategori Nilai <span class="text-rose-500">*</span></label>
                                    <select id="kategori" name="kategori" required class="{{ $inputCls }}">
                                        @foreach (Akademik::KATEGORI as $kodeKategori => $labelKategori)
                                            <option value="{{ $kodeKategori }}" @selected(old('kategori', $filter['kategori']) === $kodeKategori)>{{ $labelKategori }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="xl:col-span-3">
                                    <label for="nilai_cepat" class="{{ $labelCls }}">Nilai (0–100) <span class="text-rose-500">*</span></label>
                                    <input id="nilai_cepat" type="number" name="nilai" min="0" max="100" step="0.01"
                                        value="{{ old('nilai') }}" required class="{{ $inputCls }} text-lg font-black"
                                        placeholder="Contoh: 85">
                                    <p class="mt-1.5 text-[10px] text-slate-400">Boleh desimal, contoh 85.5.</p>
                                </div>

                                <div class="xl:col-span-3">
                                    <label for="catatan" class="{{ $labelCls }}">Catatan untuk Wali (opsional)</label>
                                    <textarea id="catatan" name="catatan" rows="2" maxlength="255" class="{{ $inputCls }}"
                                        placeholder="Contoh: Perlu meningkatkan ketelitian pada soal hitungan.">{{ old('catatan') }}</textarea>
                                </div>
                            </div>

                            <div class="flex flex-col gap-3 border-t border-slate-100 pt-4 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-[10px] leading-relaxed text-slate-400">
                                    <i class="fa-solid fa-circle-info mr-1 text-emerald-600"></i>
                                    Nilai akan langsung terlihat oleh wali santri setelah disimpan.
                                </p>
                                <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-xs font-black text-white shadow-sm transition hover:bg-emerald-700">
                                    <i class="fa-solid fa-floppy-disk"></i> Simpan &amp; Terbitkan
                                </button>
                            </div>
                        </form>
                    @endif
                </section>
            @else
                <div
                    class="rounded-2xl border border-sky-200 dark:border-sky-500/30 bg-sky-50 dark:bg-sky-500/10 px-4 py-3.5 text-xs text-sky-800 dark:text-sky-300">
                    <p class="font-black"><i class="fa-solid fa-circle-info mr-1"></i> Input cepat satu santri belum aktif.</p>
                    <p class="mt-1 leading-relaxed">Pilih <b>jenjang</b> pada filter di atas untuk mengaktifkan form input cepat.</p>
                </div>
            @endif
        @endif

        {{-- IMPORT NILAI MASSAL DARI CSV --}}
        @if ($bisaUbah && $filter['jenjang'])
            <section id="import-nilai"
                class="scroll-mt-24 rounded-3xl border border-sky-200 dark:border-sky-500/30 bg-white dark:bg-slate-900 p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="max-w-2xl">
                        <h3 class="inline-flex items-center gap-2 text-sm font-black text-slate-900 dark:text-white">
                            <i class="fa-solid fa-file-arrow-up text-sky-600"></i> Import Nilai dari CSV
                        </h3>
                        <p class="mt-1 text-[11px] leading-relaxed text-slate-500 dark:text-slate-400">
                            Kolom <b>nomor_induk</b> dan <b>nilai</b> wajib. Jenjang, kelas, mapel, tahun ajaran,
                            semester, dan kategori mengikuti filter di atas, tetapi boleh ditimpa per baris.
                        </p>
                    </div>
                    <a href="{{ route('admin.akademik.nilai.template') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-sky-200 dark:border-sky-500/30 px-4 py-2.5 text-[11px] font-bold text-sky-700 dark:text-sky-300 transition hover:bg-sky-50 dark:hover:bg-sky-500/10">
                        <i class="fa-solid fa-download"></i> Unduh Template
                    </a>
                </div>

                <form method="POST" action="{{ route('admin.akademik.nilai.import') }}" enctype="multipart/form-data"
                    class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-[1fr_auto] sm:items-end">
                    @csrf
                    @foreach ($queryKonteks as $kunciKonteks => $nilaiKonteks)
                        <input type="hidden" name="{{ $kunciKonteks }}" value="{{ $nilaiKonteks }}">
                    @endforeach

                    <div>
                        <label class="{{ $labelCls }}">Berkas CSV</label>
                        <input type="file" name="file_csv" accept=".csv,.txt,text/csv" required class="{{ $inputCls }}">
                        <p class="mt-1 text-[10px] leading-relaxed text-slate-400">
                            Maksimal 5 MB. CSV dari Excel dipisah titik koma; dipisah koma juga didukung.
                            Desimal boleh ditulis <b>85.5</b> atau <b>85,5</b>.
                        </p>
                    </div>

                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-6 py-3 text-xs font-black text-white shadow-sm transition hover:bg-sky-700">
                        <i class="fa-solid fa-upload"></i> Import &amp; Simpan Draft
                    </button>
                </form>

                <div class="mt-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 px-3.5 py-2.5">
                    <p class="text-[10px] leading-relaxed text-slate-500 dark:text-slate-400">
                        <i class="fa-solid fa-circle-info mr-1 text-sky-600"></i>
                        Hasil import berstatus <b>draft</b> dan belum terlihat wali. Gunakan tombol
                        <b>Terbitkan</b> pada konteks ini setelah data diperiksa. Baris yang error dilewati
                        dan daftar errornya ditampilkan di atas halaman.
                    </p>
                </div>
            </section>
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
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Isi langsung di tabel per santri, lalu unggah rapor PDF-nya bila diperlukan.</p>
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
                <div class="rounded-2xl border border-rose-200 dark:border-rose-500/30 bg-rose-50/60 dark:bg-rose-500/10 p-4 flex items-center justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-rose-600 text-white text-sm"><i class="fa-solid fa-file-pdf"></i></span>
                        <div>
                            <h4 class="text-xs font-black text-rose-900 dark:text-rose-200">Opsi B — Unggah Rapor PDF</h4>
                            <p class="text-[11px] text-rose-800/80 dark:text-rose-300/80">Unggah berkas rapor (PDF) per santri; nama file dicocokkan otomatis lewat NIS atau nama.</p>
                        </div>
                    </div>
                    @if ($bisaUbah && $dokumenSiap && $filter['jenjang'])
                        <a href="#rapor-pdf"
                            class="shrink-0 rounded-xl bg-rose-600 hover:bg-rose-700 text-white px-3.5 py-2.5 text-[11px] font-bold transition inline-flex items-center gap-2">
                            <i class="fa-solid fa-upload"></i> Unggah
                        </a>
                    @endif
                </div>
            </div>

            @if ($santris->isEmpty())
                <div class="rounded-3xl border border-amber-200 dark:border-amber-500/30 bg-amber-50 dark:bg-amber-500/10 p-6 sm:p-8 text-center space-y-2">
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
                                <p class="mt-0.5 inline-flex items-center gap-1.5 text-[11px] font-bold text-emerald-700 dark:text-emerald-400">
                                    <i class="fa-solid fa-chalkboard-user text-[10px]"></i>
                                    Guru Pengampu:
                                    {{ $filter['mapel']->guru?->nama ?? 'Belum ditentukan' }}
                                </p>
                            </div>
                            @if ($bisaUbah && $dokumenSiap && $filter['jenjang'])
                                <a href="#rapor-pdf"
                                    class="inline-flex items-center gap-2 rounded-xl border border-rose-200 dark:border-rose-500/30 px-3 py-2 text-[11px] font-bold text-rose-600 dark:text-rose-300 hover:border-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition">
                                    <i class="fa-solid fa-file-pdf"></i> Unggah Rapor PDF
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
                                        @if ($bisaUbah)
                                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @foreach ($santris as $santri)
                                        @php
                                            $row = $nilaiSantri->get($santri->id);
                                        @endphp
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
                                            @if ($bisaUbah)
                                                <td class="px-4 py-3 text-center">
                                                    @if ($row?->status === 'published')
                                                        <a href="{{ route('admin.akademik.nilai.buka-kembali.form', $row) }}"
                                                            title="Buka kembali nilai terbit menjadi draft (wajib alasan)"
                                                            class="inline-flex items-center gap-1 rounded-lg border border-amber-200 px-2.5 py-1.5 text-[10px] font-bold text-amber-700 transition hover:bg-amber-50 dark:border-amber-500/30 dark:text-amber-300 dark:hover:bg-amber-500/10">
                                                            <i class="fa-solid fa-lock-open"></i> Buka
                                                        </a>
                                                    @else
                                                        <span class="text-[10px] text-slate-300 dark:text-slate-600">—</span>
                                                    @endif
                                                </td>
                                            @endif
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

        {{-- UNGGAH RAPOR PDF --}}
        @if (! $dokumenSiap)
            <div class="rounded-2xl border border-amber-200 dark:border-amber-500/30 bg-amber-50 dark:bg-amber-500/10 px-4 py-4 text-xs text-amber-800 dark:text-amber-300">
                <p class="font-black"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Tabel rapor PDF belum tersedia.</p>
                <p class="mt-1 leading-relaxed">Jalankan <b>php artisan migrate</b> terlebih dahulu agar unggah rapor PDF dapat digunakan.</p>
            </div>
        @elseif ($bisaUbah && $filter['jenjang'])
            <section id="rapor-pdf" class="scroll-mt-24 space-y-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="max-w-2xl">
                        <h3 class="inline-flex items-center gap-2 text-sm font-black text-slate-900 dark:text-white">
                            <i class="fa-solid fa-file-pdf text-rose-600"></i> Unggah Rapor PDF
                        </h3>
                        <p class="mt-1 text-[11px] leading-relaxed text-slate-500 dark:text-slate-400">
                            Satu berkas PDF per santri untuk tahun ajaran <b>{{ $filter['tahun'] }}</b> semester
                            <b>{{ Akademik::labelSemester($filter['semester']) }}</b>. Nama file harus memuat
                            <b>NIS</b> atau <b>nama lengkap</b> santri, misalnya
                            <code class="rounded bg-slate-100 px-1 py-0.5 font-mono text-[10px] dark:bg-slate-800">12345-ahmad-fauzi.pdf</code>.
                            Berkas langsung terbit ke portal wali santri.
                        </p>
                    </div>
                    <a href="#dokumen-terbaru"
                        class="inline-flex shrink-0 items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-[11px] font-bold text-slate-600 transition hover:border-emerald-400 dark:border-slate-700 dark:text-slate-300">
                        <i class="fa-solid fa-list-check"></i> Riwayat rapor
                    </a>
                </div>

                @if (session('pdf_hasil'))
                    @php
                        $hasilPdf = session('pdf_hasil');
                    @endphp
                    <div class="space-y-3">
                        @if (! empty($hasilPdf['berhasil']))
                            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4 dark:border-emerald-500/30 dark:bg-emerald-500/10">
                                <p class="text-[11px] font-black text-emerald-800 dark:text-emerald-300">
                                    <i class="fa-solid fa-circle-check mr-1"></i>
                                    {{ count($hasilPdf['berhasil']) }} rapor berhasil diunggah
                                </p>
                                <ul class="mt-2 max-h-40 space-y-1 overflow-y-auto pr-1 text-[11px] text-emerald-900/80 dark:text-emerald-300/80">
                                    @foreach ($hasilPdf['berhasil'] as $b)
                                        <li class="flex flex-wrap items-center gap-1.5">
                                            <i class="fa-solid fa-check text-[9px]"></i>
                                            <b>{{ $b['nama'] }}</b>
                                            <span class="text-emerald-700/60 dark:text-emerald-300/50">(Kelas {{ $b['kelas'] }}, via {{ $b['cara'] }})</span>
                                            <span class="font-mono text-[10px] text-emerald-700/60 dark:text-emerald-300/50">{{ $b['file'] }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (! empty($hasilPdf['gagal']))
                            <div class="rounded-2xl border border-amber-200 bg-amber-50/70 p-4 dark:border-amber-500/30 dark:bg-amber-500/10">
                                <p class="text-[11px] font-black text-amber-800 dark:text-amber-300">
                                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                                    {{ count($hasilPdf['gagal']) }} berkas dilewati
                                </p>
                                <ul class="mt-2 max-h-40 space-y-1.5 overflow-y-auto pr-1 text-[11px] text-amber-900/80 dark:text-amber-300/80">
                                    @foreach ($hasilPdf['gagal'] as $g)
                                        <li>
                                            <span class="font-mono text-[10px] font-bold">{{ $g['file'] }}</span>
                                            — {{ $g['alasan'] }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.dashboard.jenjang.nilai.pdf.massal') }}"
                    enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <input type="hidden" name="jenjang" value="{{ $filter['jenjang'] }}">
                    <input type="hidden" name="tahun_ajaran" value="{{ $filter['tahun'] }}">
                    <input type="hidden" name="semester" value="{{ $filter['semester'] }}">

                    <div>
                        <label for="pdf_files" class="mb-1.5 block text-[10px] font-black uppercase tracking-wider text-slate-400">
                            Berkas PDF <span class="text-rose-500">*</span>
                        </label>
                        <input id="pdf_files" type="file" name="files[]" accept="application/pdf,.pdf" multiple required
                            class="block w-full cursor-pointer rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-rose-600 file:px-4 file:py-2 file:text-[11px] file:font-black file:text-white hover:file:bg-rose-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        <p class="mt-1.5 text-[10px] text-slate-400">
                            Bisa pilih banyak berkas sekaligus (maks. 50 file). Format PDF, maksimal 10 MB per file.
                            Unggah ulang pada santri + periode yang sama akan menimpa file lama.
                        </p>
                        @error('files')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                        @foreach ($errors->getMessages() as $kunci => $daftarPesan)
                            @if (str_starts_with($kunci, 'files.'))
                                <p class="mt-1 text-[11px] text-rose-600">{{ $daftarPesan[0] }}</p>
                            @endif
                        @endforeach
                    </div>

                    <button type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-rose-600 px-5 py-2.5 text-xs font-black text-white transition hover:bg-rose-700 sm:w-auto">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Unggah &amp; Terbitkan
                    </button>
                </form>
            </section>
        @endif

        {{-- RIWAYAT TERBARU --}}
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="inline-flex items-center gap-2 text-sm font-black text-slate-900 dark:text-white">
                        <i class="fa-solid fa-clock-rotate-left text-amber-500"></i> Nilai Terbaru
                    </h3>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-black text-slate-500 dark:bg-slate-800 dark:text-slate-300">
                        {{ $nilaiTerbaru->count() }}
                    </span>
                </div>

                <div class="mt-3 max-h-80 space-y-2 overflow-y-auto pr-1">
                    @forelse ($nilaiTerbaru as $n)
                        <div class="rounded-2xl border border-slate-100 px-4 py-3 dark:border-slate-800">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-[11px] font-black text-slate-800 dark:text-slate-100">
                                        {{ $n->santri?->nama_lengkap ?? '-' }}
                                    </p>
                                    <p class="truncate text-[10px] text-slate-400">
                                        {{ $n->mataPelajaran?->nama ?? '-' }} · {{ Akademik::labelKategori($n->kategori) }}
                                        @if ($n->mataPelajaran?->guru)
                                            · {{ $n->mataPelajaran->guru->nama }}
                                        @endif
                                    </p>
                                </div>
                                <span class="shrink-0 rounded-xl bg-emerald-50 px-2.5 py-1 text-xs font-black text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                                    {{ Akademik::formatNilai($n->nilai) }}
                                </span>
                            </div>
                            <p class="mt-1 flex flex-wrap items-center gap-1.5 text-[10px] text-slate-400">
                                <span>{{ $n->tahun_ajaran }} · {{ Akademik::labelSemester($n->semester) }} · Kelas {{ $n->kelas ?: '-' }}</span>
                                @if ($n->status === 'published')
                                    <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-black text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">Terbit</span>
                                @else
                                    <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[9px] font-black text-amber-700 dark:bg-amber-500/10 dark:text-amber-300">Draft</span>
                                @endif
                            </p>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-[11px] text-slate-400 dark:border-slate-700">
                            Belum ada nilai{{ $filter['jenjang'] ? ' pada jenjang ini' : '' }}.
                        </div>
                    @endforelse
                </div>
            </section>

            <section id="dokumen-terbaru"
                class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="inline-flex items-center gap-2 text-sm font-black text-slate-900 dark:text-white">
                        <i class="fa-solid fa-file-pdf text-rose-600"></i> Rapor PDF Terbaru
                    </h3>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-black text-slate-500 dark:bg-slate-800 dark:text-slate-300">
                        {{ $dokumenTerbaru->count() }}
                    </span>
                </div>

                @if (! $dokumenSiap)
                    <div class="mt-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 text-[11px] text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
                        Tabel rapor PDF belum tersedia. Jalankan <b>php artisan migrate</b> terlebih dahulu.
                    </div>
                @else
                    <div class="mt-3 max-h-80 space-y-2.5 overflow-y-auto pr-1">
                        @forelse ($dokumenTerbaru as $dok)
                            <div class="rounded-2xl border border-slate-100 px-4 py-3 dark:border-slate-800">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="truncate text-[11px] font-black text-slate-800 dark:text-slate-100">{{ $dok->judul }}</p>
                                        <p class="truncate text-[10px] text-slate-400">
                                            {{ $dok->santri?->nama_lengkap ?? '-' }} · Kelas {{ $dok->kelas ?: '-' }}
                                        </p>
                                        <p class="text-[10px] text-slate-400">
                                            {{ $dok->tahun_ajaran }} · {{ Akademik::labelSemester($dok->semester) }} · {{ $dok->ukuranManusia() }}
                                        </p>
                                    </div>
                                    <div class="flex shrink-0 items-center gap-1">
                                        <a href="{{ route('admin.dashboard.jenjang.nilai.pdf.unduh', $dok) }}"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 transition hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-300"
                                            title="Unduh PDF">
                                            <i class="fa-solid fa-download"></i>
                                        </a>
                                        @if ($bisaUbah)
                                            <form method="POST" action="{{ route('admin.dashboard.jenjang.nilai.pdf.hapus', $dok) }}"
                                                onsubmit="return confirm('Hapus rapor PDF {{ $dok->judul }}? Tindakan ini tidak bisa dibatalkan.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600 transition hover:bg-rose-100 dark:bg-rose-500/10 dark:text-rose-300"
                                                    title="Hapus PDF">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                                <p class="mt-2 truncate text-[10px] text-slate-400">
                                    <i class="fa-solid fa-paperclip mr-1"></i>{{ $dok->file_name }}
                                </p>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-[11px] text-slate-400 dark:border-slate-700">
                                Belum ada rapor PDF{{ $filter['jenjang'] ? ' pada jenjang ini' : '' }}.
                            </div>
                        @endforelse
                    </div>
                @endif
            </section>
        </div>

        @include('dashboard.roles._santri-picker-script')
    </div>
@endsection
