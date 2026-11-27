@extends('layouts.admin')

@use('App\Support\Akses')

@section('title', 'Data Alumni - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Manajemen Data Alumni</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Kelola data lulusan Pondok Pesantren Babul Hasanah beserta kegiatan, kontak, testimoni, dan
                    nilai/transkrip mereka. Santri yang ditandai lulus otomatis tersinkron ke halaman ini, dan data
                    yang Anda isi langsung tampil di halaman publik
                    <a href="{{ route('alumni.index') }}" target="_blank" rel="noopener" class="font-bold underline">/alumni</a>.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('alumni.index') }}" target="_blank" rel="noopener"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                    <i class="fa-solid fa-up-right-from-square mr-1"></i> Lihat Publik
                </a>
                @if (auth()->user()->bisa('alumni.ubah'))
                    <a href="{{ route('admin.alumni.create') }}"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-plus"></i> Tambah Alumni
                    </a>
                @else
                    <span class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-[11px] font-bold text-slate-500 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-300">
                        <i class="fa-solid fa-eye mr-1"></i> Mode monitoring (hanya baca)
                    </span>
                @endif
            </div>
        </div>

        <!-- PESAN SUKSES / ERROR -->
        @if (session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300 px-4 py-3.5 rounded-2xl text-xs font-bold flex items-center gap-2.5 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-300 px-4 py-3.5 rounded-2xl text-xs font-bold flex items-center gap-2.5 shadow-sm">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 dark:text-rose-400 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if (! $tautanSiap || ! $nilaiSiap)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
                <p class="font-black"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Sebagian fitur alumni belum aktif.</p>
                <p class="mt-1">Jalankan <b>php artisan migrate</b> agar sinkronisasi santri lulus dan unggah nilai/transkrip alumni dapat digunakan.</p>
            </div>
        @endif

        @if ($tautanSiap && $santriLulus->isNotEmpty())
            <div class="rounded-3xl border border-indigo-200 bg-indigo-50/70 p-5 dark:border-indigo-500/30 dark:bg-indigo-500/10">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                    <div class="space-y-2">
                        <p class="text-xs font-black text-indigo-800 dark:text-indigo-300">
                            <i class="fa-solid fa-graduation-cap mr-1.5"></i>
                            {{ $santriLulus->count() }} santri sudah lulus tetapi belum masuk Data Alumni
                        </p>
                        <p class="text-[11px] leading-relaxed text-indigo-900/70 dark:text-indigo-300/70">
                            Santri berikut sudah ditandai <b>Lulus</b> pada halaman Data Santri. Tarik datanya agar
                            otomatis muncul di tabel alumni, lalu lengkapi kegiatan, kontak, dan nilai mereka.
                            @if (Akses::dibatasiJenjang(auth()->user()))
                                Hanya santri pada jenjang yang Anda tangani yang akan ditarik.
                            @endif
                        </p>
                        <ul class="flex flex-wrap gap-1.5 pt-1">
                            @foreach ($santriLulus->take(8) as $s)
                                <li class="rounded-lg bg-white/80 px-2.5 py-1 text-[10px] font-bold text-indigo-800 dark:bg-slate-900/50 dark:text-indigo-300">
                                    {{ $s->nama_lengkap }}
                                    <span class="font-normal text-indigo-700/60 dark:text-indigo-300/50">
                                        · {{ $s->jenjang_pendidikan }}{{ $s->tahun_lulus ? ' · '.$s->tahun_lulus : '' }}
                                    </span>
                                </li>
                            @endforeach
                            @if ($santriLulus->count() > 8)
                                <li class="rounded-lg bg-white/80 px-2.5 py-1 text-[10px] font-bold text-indigo-800 dark:bg-slate-900/50 dark:text-indigo-300">
                                    +{{ $santriLulus->count() - 8 }} lainnya
                                </li>
                            @endif
                        </ul>
                    </div>

                    @if (auth()->user()->bisa('alumni.ubah'))
                        <form action="{{ route('admin.alumni.tarik-santri-lulus') }}" method="POST" class="shrink-0"
                            onsubmit="return confirm('Tarik {{ $santriLulus->count() }} santri lulus ke Data Alumni?');">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-[11px] font-black text-white shadow-sm transition hover:bg-indigo-700">
                                <i class="fa-solid fa-arrow-down-to-line"></i> Tarik Santri Lulus
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @elseif ($tautanSiap && $totalLulus === 0)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-[11px] font-bold text-slate-600 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-300">
                <i class="fa-solid fa-circle-info mr-1"></i>
                Belum ada santri yang ditandai lulus.
                <a href="{{ route('admin.santri.index') }}" class="underline">Tandai kelulusan di halaman Data Santri</a>
                agar otomatis muncul di sini.
            </div>
        @elseif ($tautanSiap && auth()->user()->bisa('alumni.ubah'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50/60 px-4 py-3 text-[11px] font-bold text-emerald-800 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
                <i class="fa-solid fa-circle-check mr-1"></i>
                Semua santri yang sudah lulus telah terhubung ke Data Alumni.
                <a href="{{ route('admin.santri.index') }}" class="underline">Kelola kelulusan santri</a>.
            </div>
        @endif

        @if ($tautanSiap && $tanpaAkun->isNotEmpty())
            <div class="rounded-3xl border border-emerald-200 bg-emerald-50/70 p-5 dark:border-emerald-500/30 dark:bg-emerald-500/10">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                    <div class="space-y-2">
                        <p class="text-xs font-black text-emerald-800 dark:text-emerald-300">
                            <i class="fa-solid fa-user-lock mr-1.5"></i>
                            {{ $tanpaAkun->count() }} alumni belum punya akun login
                        </p>
                        <p class="text-[11px] leading-relaxed text-emerald-900/70 dark:text-emerald-300/70">
                            Akun alumni seharusnya dibuat otomatis saat kelulusan. Untuk data lama yang belum punya akun,
                            centang nama di bawah lalu buat sekaligus. Kredensial dikirim ke email santri bila tersedia,
                            atau ke email alumni yang tersimpan.
                        </p>
                    </div>

                    @if (auth()->user()->bisa('alumni.ubah'))
                        <form action="{{ route('admin.alumni.buat-akun-massal') }}" method="POST" class="lg:w-96 shrink-0"
                            onsubmit="return confirm('Buat akun dan kirim kredensial untuk ' + document.querySelectorAll('.pilih-akun-alumni:checked').length + ' alumni?');">
                            @csrf
                            <div class="max-h-44 overflow-y-auto custom-scrollbar rounded-xl border border-emerald-200 bg-white/80 p-2 dark:border-emerald-500/20 dark:bg-slate-900/50">
                                @foreach ($tanpaAkun as $a)
                                    <label class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-[11px] font-bold text-emerald-900 dark:text-emerald-200 hover:bg-emerald-100/60 dark:hover:bg-emerald-500/10">
                                        <input type="checkbox" name="ids[]" value="{{ $a->id }}" checked
                                            class="pilih-akun-alumni rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                                        <span class="truncate">{{ $a->nama }}</span>
                                        <span class="font-normal text-emerald-800/60 dark:text-emerald-300/60">
                                            · {{ $a->tahun_lulus }}{{ $a->jenjang ? ' · '.$a->jenjang : '' }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                <button type="button"
                                    onclick="document.querySelectorAll('.pilih-akun-alumni').forEach(function (cb) { cb.checked = true; })"
                                    class="rounded-lg border border-emerald-300 bg-white/70 px-2.5 py-2 text-[10px] font-bold text-emerald-800 transition hover:bg-white dark:border-emerald-500/30 dark:bg-slate-900/50 dark:text-emerald-300">
                                    Pilih semua
                                </button>
                                <button type="button"
                                    onclick="document.querySelectorAll('.pilih-akun-alumni').forEach(function (cb) { cb.checked = false; })"
                                    class="rounded-lg border border-emerald-200 bg-white/70 px-2.5 py-2 text-[10px] font-bold text-emerald-700 transition hover:bg-white dark:border-emerald-500/20 dark:bg-slate-900/50 dark:text-emerald-300">
                                    Kosongkan
                                </button>
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3.5 py-2 text-[11px] font-black text-white transition hover:bg-emerald-700">
                                    <i class="fa-solid fa-user-plus"></i> Buat Akun Massal
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        @endif

        <!-- RINGKASAN ALUMNI (mengikuti halaman publik /alumni) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <span class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 flex items-center justify-center text-base shrink-0">
                    <i class="fa-solid fa-user-graduate"></i>
                </span>
                <div class="min-w-0">
                    <b class="block text-lg font-black leading-tight text-slate-900 dark:text-white">{{ $statistik['total'] }}</b>
                    <span class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Total alumni</span>
                </div>
            </div>

            <div class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <span class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 flex items-center justify-center text-base shrink-0">
                    <i class="fa-solid fa-layer-group"></i>
                </span>
                <div class="min-w-0">
                    <b class="block text-lg font-black leading-tight text-slate-900 dark:text-white">{{ $statistik['angkatan'] }}</b>
                    <span class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Angkatan lulus</span>
                </div>
            </div>

            <div class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <span class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400 flex items-center justify-center text-base shrink-0">
                    <i class="fa-solid fa-graduation-cap"></i>
                </span>
                <div class="min-w-0">
                    <b class="block text-lg font-black leading-tight text-slate-900 dark:text-white">{{ $statistik['kuliah'] }}</b>
                    <span class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Melanjutkan kuliah</span>
                </div>
            </div>

            <div class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <span class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 flex items-center justify-center text-base shrink-0">
                    <i class="fa-solid fa-briefcase"></i>
                </span>
                <div class="min-w-0">
                    <b class="block text-lg font-black leading-tight text-slate-900 dark:text-white">{{ $statistik['bekerja'] }}</b>
                    <span class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Bekerja &amp; wirausaha</span>
                </div>
            </div>
        </div>

        <!-- FILTER -->
        <form method="GET"
            class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm p-4 flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label for="q" class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1.5">Cari</label>
                <input id="q" type="text" name="q" value="{{ request('q') }}"
                    placeholder="Nama, NIS, NISN, jenjang, instansi, testimoni, email, atau no. HP..."
                    class="w-full text-xs px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="min-w-[150px]">
                <label for="angkatan" class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1.5">Angkatan</label>
                <select id="angkatan" name="angkatan"
                    class="w-full text-xs px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua angkatan</option>
                    @foreach ($daftarAngkatan as $th)
                        <option value="{{ $th }}" @selected(request('angkatan') == $th)>{{ $th }}</option>
                    @endforeach
                </select>
            </div>

            @if (count($daftarJenjang) > 1 && ! \App\Support\Akses::dibatasiJenjang(auth()->user()))
                <div class="min-w-[150px]">
                    <label for="jenjang" class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1.5">Jenjang</label>
                    <select id="jenjang" name="jenjang"
                        class="w-full text-xs px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Semua jenjang</option>
                        @foreach ($daftarJenjang as $j)
                            <option value="{{ $j }}" @selected(\App\Support\Jenjang::normalisasi(request('jenjang')) === \App\Support\Jenjang::normalisasi($j))>{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if (count($daftarKegiatan) > 0)
                <div class="min-w-[150px]">
                    <label for="kegiatan" class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1.5">Kegiatan</label>
                    <select id="kegiatan" name="kegiatan"
                        class="w-full text-xs px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Semua kegiatan</option>
                        @foreach ($daftarKegiatan as $keg)
                            <option value="{{ $keg }}" @selected(request('kegiatan') === $keg)>{{ $keg }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="flex items-center gap-2">
                <button class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold inline-flex items-center gap-2">
                    <i class="fa-solid fa-magnifying-glass"></i> Cari
                </button>
                @if (request()->filled('q') || request()->filled('angkatan') || request()->filled('jenjang') || request()->filled('kegiatan'))
                    <a href="{{ route('admin.alumni.index') }}"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold">Reset</a>
                @endif
            </div>
        </form>

        <!-- TABEL DATA ALUMNI -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-300">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-100 dark:border-slate-800">
                            <th class="py-3 px-4 font-extrabold">No. Induk</th>
                            <th class="py-3 px-4 font-extrabold">Nama Lengkap</th>
                            <th class="py-3 px-4 font-extrabold">NISN</th>
                            <th class="py-3 px-4 font-extrabold">Jenis Kelamin</th>
                            <th class="py-3 px-4 font-extrabold">Jenjang</th>
                            <th class="py-3 px-4 font-extrabold text-center">Tahun Masuk</th>
                            <th class="py-3 px-4 font-extrabold text-center">Tahun Lulus</th>
                            <th class="py-3 px-4 font-extrabold">Kegiatan Sekarang</th>
                            <th class="py-3 px-4 font-extrabold">Instansi</th>
                            <th class="py-3 px-4 font-extrabold text-center">Testimoni</th>
                            <th class="py-3 px-4 font-extrabold">No. HP</th>
                            <th class="py-3 px-4 font-extrabold">Email</th>
                            <th class="py-3 px-4 font-extrabold">Alamat</th>
                            <th class="py-3 px-4 font-extrabold text-center">Nilai</th>
                            <th class="py-3 px-4 font-extrabold text-center">Status</th>
                            <th class="py-3 px-4 font-extrabold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse ($alumnis as $a)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                {{-- No. Induk --}}
                                <td class="py-4 px-4 font-mono text-[11px] text-slate-500">
                                    @if ($tautanSiap && $a->santri)
                                        <a href="{{ route('admin.santri.detail', $a->santri->id) }}"
                                            class="font-bold text-indigo-600 hover:underline dark:text-indigo-400">
                                            {{ $a->santri->nomor_induk ?: '-' }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- Nama + Foto --}}
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        @if ($a->foto_url)
                                            <img src="{{ $a->foto_url }}" alt="Foto {{ $a->nama }}"
                                                class="w-9 h-9 object-cover rounded-full border border-slate-100 dark:border-slate-700">
                                        @else
                                            <span class="w-9 h-9 rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 flex items-center justify-center text-[11px] font-black">
                                                {{ mb_strtoupper(mb_substr($a->nama, 0, 1)) }}
                                            </span>
                                        @endif
                                        <div>
                                            <div class="font-black text-slate-900 dark:text-white">{{ $a->nama }}</div>
                                            <div class="text-[10px] text-slate-400">
                                                @if ($a->kegiatan)
                                                    {{ $a->kegiatan }}
                                                @else
                                                    Belum ada kegiatan
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-4 px-4 font-mono text-[11px] text-slate-500">{{ filled($a->nisn) ? $a->nisn : '-' }}</td>
                                <td class="py-4 px-4">
                                    {{ $a->jenis_kelamin === 'L' ? 'Laki-laki' : ($a->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}
                                </td>
                                <td class="py-4 px-4">
                                    @if ($a->jenjang)
                                        <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 rounded-lg text-[10px] font-bold">{{ $a->jenjang }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-center">{{ filled($a->tahun_masuk) ? $a->tahun_masuk : '-' }}</td>
                                <td class="py-4 px-4 text-center font-bold">{{ $a->tahun_lulus }}</td>
                                <td class="py-4 px-4">
                                    @if ($a->kegiatan)
                                        <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 rounded-lg text-[10px] font-bold">{{ $a->kegiatan }}</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 max-w-[180px] truncate">{{ filled($a->instansi) ? $a->instansi : '-' }}</td>
                                <td class="py-4 px-4 text-center">
                                    @if (filled($a->testimoni))
                                        <span class="inline-flex items-center gap-1 rounded-lg bg-amber-50 px-2.5 py-1 text-[10px] font-bold text-amber-700 dark:bg-amber-500/10 dark:text-amber-300"
                                            title="{{ $a->testimoni }}">
                                            <i class="fa-solid fa-quote-left text-[8px]"></i> Ada
                                        </span>
                                    @else
                                        <span class="text-[10px] text-slate-400">Belum</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 font-mono text-[11px]">{{ filled($a->no_hp) ? $a->no_hp : '-' }}</td>
                                <td class="py-4 px-4 text-slate-500 break-all">{{ filled($a->email) ? $a->email : '-' }}</td>
                                <td class="py-4 px-4 max-w-xs whitespace-normal">{{ filled($a->alamat) ? $a->alamat : '-' }}</td>

                                {{-- Nilai / Transkrip --}}
                                <td class="py-4 px-4 text-center">
                                    @if (! $nilaiSiap)
                                        <span class="text-slate-400">-</span>
                                    @elseif ($a->nilai_path)
                                        <a href="{{ route('admin.alumni.show', $a) }}"
                                            class="inline-flex items-center gap-1 bg-rose-50 text-rose-600 hover:bg-rose-100 px-2.5 py-1 rounded-lg text-[10px] font-bold transition"
                                            title="{{ $a->nilai_nama }}">
                                            <i class="fa-solid fa-file-pdf"></i> PDF
                                        </a>
                                    @elseif (auth()->user()->bisa('alumni.ubah'))
                                        <a href="{{ route('admin.alumni.show', $a) }}#nilai"
                                            class="inline-flex items-center gap-1 bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 px-2.5 py-1 rounded-lg text-[10px] font-bold transition">
                                            <i class="fa-solid fa-cloud-arrow-up"></i> Unggah
                                        </a>
                                    @else
                                        <span class="text-slate-400 text-[10px]">Belum</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="py-4 px-4 text-center">
                                    @include('dashboard.alumni._status', ['alumni' => $a])
                                </td>

                                {{-- Aksi --}}
                                <td class="py-4 px-4 text-right space-x-1 whitespace-nowrap">
                                    <a href="{{ route('admin.alumni.show', $a) }}"
                                        class="px-2.5 py-1 bg-sky-50 dark:bg-sky-950/50 text-sky-600 hover:bg-sky-100 rounded-lg text-[10px] font-bold transition">Detail</a>
                                    @if (auth()->user()->bisa('alumni.ubah'))
                                        <a href="{{ route('admin.alumni.edit', $a) }}"
                                            class="px-2.5 py-1 bg-amber-50 dark:bg-amber-950/50 text-amber-600 hover:bg-amber-100 rounded-lg text-[10px] font-bold transition">Edit</a>
                                        @if ($a->user_id)
                                            <form action="{{ route('admin.alumni.reset-password', $a) }}" method="POST"
                                                class="inline-block"
                                                onsubmit="return confirm('Reset password alumni {{ $a->nama }}? Password sementara akan dikirim ke {{ $a->user?->email ?: $a->email }} dan berlaku 24 jam.');">
                                                @csrf
                                                <button type="submit"
                                                    class="px-2.5 py-1 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 hover:bg-indigo-100 rounded-lg text-[10px] font-bold transition">Reset Password</button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.alumni.destroy', $a) }}" method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('Hapus data alumni {{ $a->nama }}? Berkas nilai/transkrip yang tersimpan juga akan dihapus.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-2.5 py-1 bg-rose-50 dark:bg-rose-950/50 text-rose-600 hover:bg-rose-100 rounded-lg text-[10px] font-bold transition">Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="16" class="py-8 text-center text-slate-400 text-xs">
                                    Belum ada data alumni. Tambahkan manual atau tarik santri yang sudah lulus.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($alumnis, 'links'))
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $alumnis->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
