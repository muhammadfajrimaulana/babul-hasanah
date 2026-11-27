@extends('layouts.admin')

@section('title', 'Data Santri - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-6" x-data="{ importModalOpen: false }">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Manajemen Data Santri</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Kelola informasi data santri aktif Pondok Pesantren
                    Babul Hasanah{{ ($bisaFilterJenjang ?? false) ? ' dari semua jenjang' : '' }}. Santri yang ditandai lulus otomatis masuk ke Data Alumni.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if (auth()->user()->bisa('santri.ubah'))
                    <!-- Tombol Trigger Modal Import -->
                    <button @click="importModalOpen = true"
                        class="bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-file-csv"></i> Import CSV
                    </button>

                    <a href="{{ route('admin.santri.create') }}"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-plus"></i> Tambah Santri Baru
                    </a>
                    @if (\App\Support\Akses::superAdmin(auth()->user()))
                        <a href="{{ route('admin.santri.terhapus') }}"
                            class="bg-slate-700 hover:bg-slate-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                            <i class="fa-solid fa-trash-arrow-up"></i> Tempat Sampah
                        </a>
                    @endif
                @else
                    <span class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-[11px] font-bold text-slate-500 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-300">
                        <i class="fa-solid fa-eye mr-1"></i> Mode monitoring (hanya baca)
                    </span>
                @endif
            </div>
        </div>

        <!-- PENCARIAN & FILTER JENJANG -->
        <form method="GET" action="{{ route('admin.santri.index') }}"
            class="flex flex-col gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 lg:flex-row lg:items-end lg:justify-between">
            <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-end">
                <div class="min-w-[220px] flex-1">
                    <label for="q" class="mb-1.5 block text-[10px] font-black uppercase tracking-wider text-slate-400">Cari Santri</label>
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                        <input id="q" type="text" name="q" value="{{ $cari ?? '' }}"
                            placeholder="Nama, No. Induk, NISN, kelas, wali, atau email..."
                            class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-9 pr-3 text-xs text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                    </div>
                </div>
                @if ($bisaFilterJenjang ?? false)
                    <div class="sm:w-64">
                        <label for="jenjang" class="mb-1.5 block text-[10px] font-black uppercase tracking-wider text-slate-400">Jenjang</label>
                        <select id="jenjang" name="jenjang"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                            <option value="">Semua jenjang</option>
                            @foreach ($daftarJenjang as $j)
                                <option value="{{ $j['slug'] }}" @selected(($filterJenjang ?? '') === $j['slug'])>
                                    {{ $j['kode'] }} — {{ $j['nama'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                        <i class="fa-solid fa-magnifying-glass"></i> Cari
                    </button>
                    @if (filled($cari ?? null) || (($bisaFilterJenjang ?? false) && filled($filterJenjang ?? null)))
                        <a href="{{ route('admin.santri.index') }}"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3 py-2.5 text-[11px] font-bold text-slate-500 transition hover:text-slate-700 dark:border-slate-700 dark:text-slate-400">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
            @if (! ($bisaFilterJenjang ?? false) && filled($namaJenjangAdmin ?? null))
                <div class="flex items-center gap-2 rounded-xl border border-emerald-100 bg-emerald-50/70 px-3 py-2.5 text-[11px] font-bold text-emerald-800 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
                    <i class="fa-solid fa-layer-group"></i>
                    Data dibatasi pada jenjang {{ $namaJenjangAdmin }}.
                </div>
            @endif
        </form>

        @if (filled($cari ?? null) || (($bisaFilterJenjang ?? false) && filled($filterJenjang ?? null)))
            <div class="flex items-center gap-2 text-[11px] font-semibold text-slate-500 dark:text-slate-400">
                <i class="fa-solid fa-circle-info"></i>
                Menampilkan {{ $santris->total() }} santri
                @if (filled($cari ?? null))
                    untuk pencarian "<span class="font-black text-slate-700 dark:text-slate-200">{{ $cari }}</span>"
                @endif
                @if (($bisaFilterJenjang ?? false) && filled($filterJenjang ?? null))
                    pada jenjang <span class="font-black text-slate-700 dark:text-slate-200">{{ $filterJenjang }}</span>
                @endif
            </div>
        @endif

        <!-- LULUSKAN MASSAL -->
        @if ($kelulusanSiap && auth()->user()->bisa('santri.ubah'))
            <form id="form-luluskan-massal" action="{{ route('admin.santri.luluskan-massal') }}" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="tahun_lulus" id="lulus-massal-tahun">
                <div id="lulus-massal-ids"></div>
            </form>
            <div class="rounded-2xl border border-indigo-100 bg-indigo-50/60 p-4 dark:border-indigo-500/20 dark:bg-indigo-500/10">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-300">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </span>
                        <div>
                            <p class="text-[11px] font-black uppercase tracking-wider text-indigo-800 dark:text-indigo-300">Luluskan Massal</p>
                            <p class="text-[11px] leading-relaxed text-indigo-900/70 dark:text-indigo-300/70">
                                Centang santri yang belum lulus pada tabel, lalu tentukan tahun lulus. Data alumni,
                                akun alumni, dan email kredensial dibuat otomatis untuk masing-masing santri.
                                Santri tanpa email santri tetap memakai email wali/alamat fallback seperti sebelumnya.
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <label for="tahun-lulus-massal" class="text-[10px] font-bold text-indigo-800 dark:text-indigo-300">Tahun lulus</label>
                        <input id="tahun-lulus-massal" type="number" min="1998" max="{{ now()->year + 1 }}"
                            value="{{ now()->year }}"
                            class="w-28 rounded-xl border border-indigo-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 dark:border-indigo-500/30 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <button type="button" onclick="luluskanMassal()"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2.5 text-[11px] font-black text-white transition hover:bg-indigo-700">
                            <i class="fa-solid fa-graduation-cap"></i> Luluskan yang Dipilih
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- PESAN SUKSES / ERROR -->
        @if (session('success'))
            <div
                class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300 px-4 py-3.5 rounded-2xl text-xs font-bold flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
                <span class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-sm"></i>
                    {{ session('success') }}
                </span>
                @if (session('wa_link'))
                    <a href="{{ session('wa_link') }}" target="_blank" rel="noopener"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-xl text-[11px] font-bold transition flex items-center gap-1.5 self-start sm:self-center">
                        <i class="fa-brands fa-whatsapp"></i> Kirim akun ke wali {{ session('nama_santri') }}
                    </a>
                @endif
            </div>
        @endif

        @if (session('error'))
            <div
                class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-300 px-4 py-3.5 rounded-2xl text-xs font-bold flex items-center gap-2.5 shadow-sm">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 dark:text-rose-400 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if (session('import_errors'))
            <div
                class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-300 px-4 py-3.5 rounded-2xl text-xs shadow-sm">
                <p class="font-black flex items-center gap-2">
                    <i class="fa-solid fa-list-check"></i> Baris yang dilewati saat import:
                </p>
                <ul class="mt-2 space-y-1 pl-5 list-disc">
                    @foreach (session('import_errors') as $pesan)
                        <li>{{ $pesan }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- MODAL IMPORT CSV -->
        <div x-show="importModalOpen" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-fade-in"
            @keydown.escape.window="importModalOpen = false">

            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-800 max-w-md w-full p-6 sm:p-8 space-y-5 relative"
                @click.away="importModalOpen = false">

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-xl flex items-center justify-center text-lg">
                            <i class="fa-solid fa-file-csv"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white">Import Data Santri</h3>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">Unggah file berformat .csv (maks. 5 MB)
                            </p>
                        </div>
                    </div>
                    <button @click="importModalOpen = false"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Form Upload -->
                <form action="{{ route('admin.santri.import') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Pilih Berkas CSV</label>
                        <input type="file" name="file_excel" accept=".csv, .txt, text/csv" required
                            class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 dark:file:bg-emerald-950/50 dark:file:text-emerald-400 hover:file:bg-emerald-100 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer">
                        <p class="text-[10px] text-slate-400">Gunakan template CSV agar struktur kolom sesuai. Baris yang tidak valid dilewati dan dilaporkan.</p>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/50 p-3 rounded-2xl flex items-center justify-between text-xs">
                        <span class="text-slate-600 dark:text-slate-400">Butuh format acuannya?</span>
                        <a href="{{ route('admin.santri.template') }}"
                            class="text-emerald-600 dark:text-emerald-400 font-bold hover:underline flex items-center gap-1.5">
                            <i class="fa-solid fa-download"></i> Download Template
                        </a>
                    </div>

                    <div class="pt-2 flex gap-2">
                        <button type="button" @click="importModalOpen = false"
                            class="flex-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold px-4 py-2.5 rounded-xl text-xs transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-upload"></i> Proses Import
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <!-- TABEL DATA SANTRI -->
        <div
            class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-300">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                    <thead>
                        <tr
                            class="bg-slate-50 dark:bg-slate-800/50 text-slate-400 dark:text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-100 dark:border-slate-800">
                            @if ($kelulusanSiap && auth()->user()->bisa('santri.ubah'))
                                <th class="py-3 px-4 font-extrabold text-center">
                                    <input type="checkbox" onclick="pilihSemuaSantri(this)"
                                        title="Pilih semua santri yang belum lulus">
                                </th>
                            @else
                                <th class="py-3 px-4 font-extrabold"></th>
                            @endif
                            <th class="py-3 px-4 font-extrabold">No. Induk</th>
                            <th class="py-3 px-4 font-extrabold">Nama Lengkap</th>
                            <th class="py-3 px-4 font-extrabold">Tempat Tanggal Lahir</th>
                            <th class="py-3 px-4 font-extrabold">No. HP Aktif (WA)</th>
                            <th class="py-3 px-4 font-extrabold">Alamat Tinggal (Domisili)</th>
                            <th class="py-3 px-4 font-extrabold">Nama Orang Tua (Wali)</th>
                            <th class="py-3 px-4 font-extrabold">No. HP Orang Tua</th>
                            <th class="py-3 px-4 font-extrabold">Email Orang Tua / Wali</th>
                            <th class="py-3 px-4 font-extrabold">Sekolah Asal</th>
                            <th class="py-3 px-4 font-extrabold">Alamat Sekolah Asal</th>
                            <th class="py-3 px-4 font-extrabold">Tahun Lulus</th>
                            <th class="py-3 px-4 font-extrabold">NISN</th>
                            <th class="py-3 px-4 font-extrabold">Pilihan Jenjang</th>
                            <th class="py-3 px-4 font-extrabold text-center">Status</th>
                            <th class="py-3 px-4 font-extrabold text-center">Kelulusan</th>
                            <th class="py-3 px-4 font-extrabold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse($santris as $santri)
                            @php
                                // Arsip pendaftaran online yang menjadi sumber data santri ini
                                $p = $santri->pendaftaranOnline ?? null;
                                $status = strtolower($p->status_pendaftaran ?? '');
                                $v = fn ($value) => filled($value) ? $value : '-';
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                {{-- Pilihan lulus massal --}}
                                @if ($kelulusanSiap && ! $santri->sudah_lulus && auth()->user()->bisa('santri.ubah'))
                                    <td class="py-4 px-4 text-center">
                                        <input type="checkbox" name="pilih_santri[]" value="{{ $santri->id }}"
                                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    </td>
                                @else
                                    <td class="py-4 px-4 text-center text-slate-300">-</td>
                                @endif

                                {{-- No. Induk --}}
                                <td class="py-4 px-4 font-mono text-[11px] text-slate-500">{{ $santri->nomor_induk ?? '-' }}</td>

                                {{-- Nama Lengkap --}}
                                <td class="py-4 px-4">
                                    <div class="font-black text-slate-900 dark:text-white">{{ $v($santri->nama_lengkap) }}</div>
                                </td>

                                {{-- Tempat Tanggal Lahir --}}
                                <td class="py-4 px-4">{{ $v($p->tempat_tanggal_lahir ?? null) }}</td>

                                {{-- No. HP Aktif (WA) --}}
                                <td class="py-4 px-4 font-mono">{{ $v($p->no_hp_aktif ?? null) }}</td>

                                {{-- Alamat Tinggal (Domisili) --}}
                                <td class="py-4 px-4 max-w-xs whitespace-normal">
                                    {{ $v($p->alamat_domisili ?? ($santri->alamat ?: null)) }}
                                </td>

                                {{-- Nama Orang Tua (Wali) --}}
                                <td class="py-4 px-4">{{ $v($p->nama_ortu_wali ?? ($santri->nama_wali ?: null)) }}</td>

                                {{-- No. HP Orang Tua --}}
                                <td class="py-4 px-4 font-mono">{{ $v($p->no_hp_ortu ?? null) }}</td>

                                {{-- Email Orang Tua / Wali (akun login) --}}
                                <td class="py-4 px-4 text-slate-500 break-all">
                                    {{ $v($p->email ?? ($santri->user->email ?? null)) }}
                                </td>

                                {{-- Sekolah Asal --}}
                                <td class="py-4 px-4">{{ $v($p->asal_sekolah ?? null) }}</td>

                                {{-- Alamat Sekolah Asal --}}
                                <td class="py-4 px-4 max-w-xs whitespace-normal">{{ $v($p->alamat_sekolah ?? null) }}</td>

                                {{-- Tahun Lulus --}}
                                <td class="py-4 px-4 text-center">{{ $v($p->tahun_lulus ?? null) }}</td>

                                {{-- NISN --}}
                                <td class="py-4 px-4 font-mono text-slate-500">{{ $v($p->nisn ?? null) }}</td>

                                {{-- Pilihan Jenjang --}}
                                <td class="py-4 px-4">
                                    <span
                                        class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 rounded-lg text-[10px] font-bold">
                                        {{ $v($p->jenjang_tujuan ?? ($santri->jenjang_pendidikan ?: null)) }}
                                    </span>
                                </td>

                                {{-- Status Pendaftaran --}}
                                <td class="py-4 px-4 text-center">
                                    @if ($status === 'diterima')
                                        <span
                                            class="bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-[10px] font-bold px-2.5 py-0.5 rounded-full">Diterima</span>
                                    @elseif ($status === 'ditolak')
                                        <span
                                            class="bg-rose-100 dark:bg-rose-500/20 text-rose-800 dark:text-rose-300 text-[10px] font-bold px-2.5 py-0.5 rounded-full">Ditolak</span>
                                    @elseif ($status === 'pending')
                                        <span
                                            class="bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 text-[10px] font-bold px-2.5 py-0.5 rounded-full">Pending</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>

                                {{-- Status Kelulusan --}}
                                <td class="py-4 px-4 text-center">
                                    @if (! $kelulusanSiap)
                                        <span class="text-slate-400">-</span>
                                    @elseif ($santri->sudah_lulus)
                                        <span class="inline-flex items-center gap-1 bg-indigo-100 dark:bg-indigo-500/20 text-indigo-800 dark:text-indigo-300 text-[10px] font-bold px-2.5 py-0.5 rounded-full">
                                            <i class="fa-solid fa-graduation-cap text-[9px]"></i>
                                            Lulus{{ $santri->tahun_lulus ? ' '.$santri->tahun_lulus : '' }}
                                        </span>
                                        @if ($tautanSiap && $santri->alumni && auth()->user()->bisa('alumni.lihat'))
                                            <div class="mt-1">
                                                <a href="{{ route('admin.alumni.index', ['q' => $santri->nomor_induk]) }}"
                                                    class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 hover:underline">Data alumni</a>
                                            </div>
                                        @endif
                                    @else
                                        <span class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-bold px-2.5 py-0.5 rounded-full">Aktif</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="py-4 px-4 text-right space-x-1 whitespace-nowrap">
                                    <a href="{{ route('admin.santri.detail', $santri->id) }}"
                                        class="px-2.5 py-1 bg-sky-50 dark:bg-sky-950/50 text-sky-600 hover:bg-sky-100 rounded-lg text-[10px] font-bold transition">Detail</a>
                                    @if (auth()->user()->bisa('santri.ubah'))
                                        <a href="{{ route('admin.santri.edit', $santri->id) }}"
                                            class="px-2.5 py-1 bg-amber-50 dark:bg-amber-950/50 text-amber-600 hover:bg-amber-100 rounded-lg text-[10px] font-bold transition">Edit</a>
                                        @if ($kelulusanSiap && ! $santri->sudah_lulus)
                                            <form action="{{ route('admin.santri.luluskan', $santri->id) }}" method="POST"
                                                class="inline-block"
                                                onsubmit="var tahun = prompt('Tahun lulus {{ $santri->nama_lengkap }}:', '{{ now()->year }}'); if (tahun === null || tahun === '') { return false; } this.tahun_lulus.value = tahun; return confirm('Tandai santri ini LULUS dan buat data alumni?');">
                                                @csrf
                                                <input type="hidden" name="tahun_lulus" value="{{ now()->year }}">
                                                <button type="submit"
                                                    class="px-2.5 py-1 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 hover:bg-indigo-100 rounded-lg text-[10px] font-bold transition">Luluskan</button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.santri.destroy', $santri->id) }}" method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('Pindahkan data santri ini ke Tempat Sampah? Riwayat tetap tersimpan dan dapat dipulihkan Superadmin.');">
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
                                <td colspan="17" class="py-8 text-center text-slate-400 text-xs">Belum ada data santri
                                    yang tersimpan di database.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($santris, 'links'))
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $santris->links() }}
                </div>
            @endif
        </div>
    </div>

    @if ($kelulusanSiap && auth()->user()->bisa('santri.ubah'))
        <script>
            function pilihSemuaSantri(checkbox) {
                document.querySelectorAll('input[name="pilih_santri[]"]').forEach(function (cb) {
                    cb.checked = checkbox.checked;
                });
            }

            function luluskanMassal() {
                const terpilih = Array.from(document.querySelectorAll('input[name="pilih_santri[]"]:checked'))
                    .map(function (cb) {
                        return cb.value;
                    });

                if (terpilih.length === 0) {
                    alert('Pilih minimal satu santri yang belum lulus.');
                    return;
                }

                const tahun = document.getElementById('tahun-lulus-massal').value.trim();
                if (! /^\d{4}$/.test(tahun) || Number(tahun) < 1998 || Number(tahun) > {{ now()->year + 1 }}) {
                    alert('Tahun lulus tidak valid.');
                    return;
                }

                const wadah = document.getElementById('lulus-massal-ids');
                wadah.innerHTML = '';
                terpilih.forEach(function (id) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    wadah.appendChild(input);
                });

                document.getElementById('lulus-massal-tahun').value = tahun;

                if (confirm('Tandai ' + terpilih.length + ' santri sebagai LULUS dan buat akun alumni mereka?')) {
                    document.getElementById('form-luluskan-massal').submit();
                }
            }
        </script>
    @endif
@endsection