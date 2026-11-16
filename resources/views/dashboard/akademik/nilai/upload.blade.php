@extends('layouts.admin')

@section('title', 'Unggah Nilai - Akademik')

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
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-sky-700 dark:text-sky-400">
                    <i class="fa-solid fa-file-arrow-up"></i> Akademik / Unggah Massal
                </div>
                <h2 class="mt-1 text-xl font-black text-slate-900 dark:text-white">Unggah Nilai dari CSV</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-2xl">
                    Unduh template, isi kolom nilai, lalu unggah kembali. Sistem akan menampilkan pratinjau sebelum data disimpan.
                </p>
            </div>
            <a href="{{ route('admin.akademik.nilai.index', $queryKonteks) }}"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-300 hover:border-emerald-400 transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Input Nilai
            </a>
        </div>

        @include('dashboard.akademik._tabs')

        @include('dashboard.akademik._peringatan-jenjang')

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 dark:border-rose-500/30 bg-rose-50 dark:bg-rose-500/10 px-4 py-3.5 text-xs text-rose-800 dark:text-rose-300 space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="flex items-start gap-2"><i class="fa-solid fa-triangle-exclamation mt-0.5"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (! $bisaUbah)
            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 px-4 py-3.5 text-xs font-bold text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-eye mr-1"></i> Mode monitoring: unggah nilai hanya dapat dilakukan Super Admin dan Admin Jenjang.
            </div>
        @endif

        {{-- KONTEKS --}}
        <form method="GET" action="{{ route('admin.akademik.nilai.upload') }}"
            class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 space-y-4">
            <h3 class="text-sm font-black text-slate-900 dark:text-white"><i class="fa-solid fa-sliders mr-1.5 text-sky-600"></i> Konteks Nilai</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-6 gap-3">
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Jenjang</label>
                    @if ($slugAdmin)
                        <input type="hidden" name="jenjang" value="{{ $slugAdmin }}">
                        <div class="rounded-xl border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10 px-3 py-2.5 text-xs font-bold text-emerald-800 dark:text-emerald-300">
                            {{ $user->namaJenjang() }}
                        </div>
                    @else
                        <select name="jenjang" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                            <option value="">Semua jenjang</option>
                            @foreach ($daftarJenjang as $j)
                                <option value="{{ $j['slug'] }}" @selected($filter['jenjang'] === $j['slug'])>{{ $j['kode'] }} — {{ $j['nama'] }}</option>
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
                            <option value="{{ $mapel->id }}" @selected($filter['mapel']?->id === $mapel->id)>{{ $mapel->kode }} — {{ $mapel->nama }}</option>
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
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Kategori</label>
                    <select name="kategori" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                        @foreach (Akademik::KATEGORI as $nilai => $label)
                            <option value="{{ $nilai }}" @selected($filter['kategori'] === $nilai)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="rounded-xl bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white px-4 py-2.5 text-xs font-bold transition inline-flex items-center gap-2">
                    <i class="fa-solid fa-rotate"></i> Terapkan Konteks
                </button>
            </div>
        </form>

        @if (! $filter['lengkap'])
            <div class="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-8 text-center space-y-2">
                <i class="fa-solid fa-arrow-pointer text-2xl text-slate-300"></i>
                <h3 class="text-sm font-black text-slate-900 dark:text-white">Lengkapi konteks terlebih dahulu</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Pilih kelas, mata pelajaran, tahun ajaran, semester, dan kategori di atas.</p>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2 rounded-3xl border border-sky-200 dark:border-sky-500/30 bg-sky-50/60 dark:bg-sky-500/10 p-5 space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-black text-sky-900 dark:text-sky-200">1. Unduh template CSV</h3>
                            <p class="text-[11px] text-sky-800/80 dark:text-sky-300/80">
                                Template sudah berisi NIS dan nama santri kelas {{ $filter['kelas'] }}.
                            </p>
                        </div>
                        @if ($bisaUbah)
                            <a href="{{ route('admin.akademik.nilai.template', $queryKonteks) }}"
                                class="shrink-0 rounded-xl bg-sky-600 hover:bg-sky-700 text-white px-3.5 py-2.5 text-[11px] font-bold transition inline-flex items-center gap-2">
                                <i class="fa-solid fa-download"></i> Template
                            </a>
                        @endif
                    </div>

                    <div class="rounded-2xl bg-white/70 dark:bg-slate-900/50 border border-sky-100 dark:border-sky-500/20 p-3 text-[11px] text-sky-900/80 dark:text-sky-200/80 space-y-1">
                        <p><span class="font-black">Kolom:</span> nis, nama, kelas, kategori, nilai, catatan.</p>
                        <p>Isi kolom <span class="font-black">nilai</span> dengan angka 0–100. Jangan mengubah NIS.</p>
                        <p>File harus <span class="font-black">CSV UTF-8</span>. Excel: Simpan Sebagai → CSV UTF-8.</p>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Konteks aktif</h3>
                    <dl class="mt-3 space-y-2 text-[11px]">
                        <div class="flex justify-between gap-3"><dt class="text-slate-400">Jenjang</dt><dd class="font-bold text-slate-700 dark:text-slate-200">{{ $filter['jenjang'] }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-400">Kelas</dt><dd class="font-bold text-slate-700 dark:text-slate-200">{{ $filter['kelas'] }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-400">Mapel</dt><dd class="font-bold text-slate-700 dark:text-slate-200">{{ $filter['mapel']->nama }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-400">Tahun</dt><dd class="font-bold text-slate-700 dark:text-slate-200">{{ $filter['tahun'] }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-400">Semester</dt><dd class="font-bold text-slate-700 dark:text-slate-200">{{ Akademik::labelSemester($filter['semester']) }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-400">Kategori</dt><dd class="font-bold text-slate-700 dark:text-slate-200">{{ Akademik::labelKategori($filter['kategori']) }}</dd></div>
                    </dl>
                </div>
            </div>

            @if ($bisaUbah)
                <form method="POST" action="{{ route('admin.akademik.nilai.preview') }}" enctype="multipart/form-data"
                    class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 space-y-4">
                    @csrf
                    <input type="hidden" name="jenjang" value="{{ $filter['jenjang'] }}">
                    <input type="hidden" name="kelas" value="{{ $filter['kelas'] }}">
                    <input type="hidden" name="mata_pelajaran_id" value="{{ $filter['mapel']->id }}">
                    <input type="hidden" name="tahun_ajaran" value="{{ $filter['tahun'] }}">
                    <input type="hidden" name="semester" value="{{ $filter['semester'] }}">
                    <input type="hidden" name="kategori" value="{{ $filter['kategori'] }}">

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white">2. Unggah file CSV</h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Maksimal 5 MB. Data akan divalidasi per baris.</p>
                        </div>
                        <button type="submit"
                            class="rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2.5 text-xs font-bold transition inline-flex items-center gap-2">
                            <i class="fa-solid fa-magnifying-glass-chart"></i> Pratinjau & Validasi
                        </button>
                    </div>

                    <input type="file" name="file_nilai" accept=".csv,text/csv" required
                        class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 dark:file:bg-emerald-950/50 dark:file:text-emerald-400 hover:file:bg-emerald-100 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer">
                </form>
            @endif

            @if ($preview)
                @php($errorBaris = $errorBaris ?? [])
                @php($adaError = count($errorBaris) > 0)
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white">3. Pratinjau {{ $preview['file'] }}</h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                {{ count($preview['baris']) }} baris terbaca · {{ count($errorBaris) }} error
                            </p>
                        </div>
                        @if (! $adaError)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 dark:bg-emerald-500/20 px-3 py-1 text-[10px] font-black text-emerald-700 dark:text-emerald-300">
                                <i class="fa-solid fa-circle-check"></i> Siap disimpan
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 dark:bg-rose-500/20 px-3 py-1 text-[10px] font-black text-rose-700 dark:text-rose-300">
                                <i class="fa-solid fa-triangle-exclamation"></i> Perbaiki file dulu
                            </span>
                        @endif
                    </div>

                    @if ($adaError)
                        <div class="px-5 py-4 bg-rose-50/60 dark:bg-rose-500/10 border-b border-rose-100 dark:border-rose-500/20 space-y-1">
                            @foreach ($errorBaris as $error)
                                <p class="text-[11px] font-bold text-rose-800 dark:text-rose-300"><i class="fa-solid fa-xmark mr-1"></i> {{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    @if (count($preview['baris']) > 0)
                        <div class="overflow-x-auto max-h-[420px]">
                            <table class="w-full min-w-[640px] text-left">
                                <thead class="bg-slate-50 dark:bg-slate-800/60 sticky top-0">
                                    <tr>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">NIS</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Nama</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Nilai</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Aksi</th>
                                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @foreach ($preview['baris'] as $baris)
                                        <tr>
                                            <td class="px-4 py-3 text-[11px] font-mono text-slate-500">{{ $baris['nis'] }}</td>
                                            <td class="px-4 py-3 text-xs font-bold text-slate-800 dark:text-slate-100">{{ $baris['nama'] }}</td>
                                            <td class="px-4 py-3 text-xs font-black text-slate-900 dark:text-white">{{ Akademik::formatNilai($baris['nilai']) }}</td>
                                            <td class="px-4 py-3">
                                                @if ($baris['aksi'] === 'Perbarui')
                                                    <span class="rounded-full bg-amber-100 dark:bg-amber-500/20 px-2.5 py-1 text-[10px] font-black text-amber-700 dark:text-amber-300">Perbarui</span>
                                                @else
                                                    <span class="rounded-full bg-sky-100 dark:bg-sky-500/20 px-2.5 py-1 text-[10px] font-black text-sky-700 dark:text-sky-300">Baru</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-[11px] text-slate-500 dark:text-slate-400">{{ $baris['catatan'] ?: '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <p class="text-[10px] text-slate-400">Pilih simpan sebagai draft untuk diperiksa, atau langsung publikasikan ke portal wali.</p>
                        <form method="POST" action="{{ route('admin.akademik.nilai.import') }}" class="flex flex-wrap items-center gap-2">
                            @csrf
                            <button type="submit" name="aksi" value="draft" @disabled($adaError || count($preview['baris']) === 0)
                                class="rounded-xl border border-slate-200 dark:border-slate-700 hover:border-emerald-400 text-slate-700 dark:text-slate-200 disabled:border-slate-200 disabled:text-slate-400 disabled:cursor-not-allowed px-4 py-2.5 text-xs font-bold transition inline-flex items-center gap-2">
                                <i class="fa-solid fa-floppy-disk"></i> Simpan sebagai Draft
                            </button>
                            @if ($bisaTerbit)
                                <button type="submit" name="aksi" value="terbit" @disabled($adaError || count($preview['baris']) === 0)
                                    onclick="return confirm('Simpan dan terbitkan semua nilai draft pada konteks ini? Nilai akan langsung terlihat di portal wali santri.')"
                                    class="rounded-xl bg-emerald-700 hover:bg-emerald-800 disabled:bg-slate-300 disabled:cursor-not-allowed text-white px-4 py-2.5 text-xs font-bold transition inline-flex items-center gap-2">
                                    <i class="fa-solid fa-paper-plane"></i> Simpan &amp; Publikasikan
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            @endif
        @endif
    </div>
@endsection
