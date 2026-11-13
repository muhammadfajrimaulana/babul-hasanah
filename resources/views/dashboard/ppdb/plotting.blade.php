@extends('layouts.admin')

@section('title', 'Plotting Kelas - Pondok Pesantren Babul Hasanah')

@section('content')
    @php
        $statusBadge = fn ($status) => match ($status) {
            'calon' => 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300',
            'aktif' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300',
            'lulus' => 'bg-sky-100 text-sky-800 dark:bg-sky-500/20 dark:text-sky-300',
            default => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300',
        };
    @endphp

    <div class="space-y-5 pb-8">
        <div class="flex flex-col gap-3 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:flex-row sm:items-center sm:justify-between sm:rounded-3xl sm:p-6">
            <div>
                <span class="inline-block rounded-full border border-sky-500/20 bg-sky-50 px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider text-sky-700 dark:bg-sky-500/10 dark:text-sky-300">
                    Modul PPDB
                </span>
                <h1 class="mt-1 text-lg font-black tracking-tight text-slate-900 dark:text-white sm:text-2xl">Plotting Santri ke Kelas</h1>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                    Tempatkan santri ke kelas. Data disimpan pada pivot <code>class_student</code>.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.ppdb.kelulusan') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-3.5 py-2 text-xs font-bold text-white transition hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700">
                    <i class="fa-solid fa-user-check text-emerald-400"></i> Kelulusan PPDB
                </a>
                <a href="{{ route('admin.santri.import.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                    <i class="fa-solid fa-file-import"></i> Import Santri
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="flex items-center gap-2.5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-xs font-bold text-emerald-800 shadow-sm dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
                <i class="fa-solid fa-circle-check text-sm text-emerald-600"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center gap-2.5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-xs font-bold text-rose-800 shadow-sm dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-300">
                <i class="fa-solid fa-circle-exclamation text-sm text-rose-600"></i>
                {{ session('error') }}
            </div>
        @endif

        @if (session('plot_gagal'))
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-900 shadow-sm dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-200">
                <p class="mb-2 font-extrabold"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Sebagian santri tidak bisa diplot</p>
                <ul class="space-y-1">
                    @foreach (session('plot_gagal') as $gagal)
                        <li class="rounded-lg bg-white/60 px-2.5 py-1.5 dark:bg-slate-900/40">
                            <b>{{ $gagal['nama'] }}</b> ({{ $gagal['nisn'] }}) — {{ $gagal['alasan'] }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            @foreach ([
                ['label' => 'Total Santri', 'nilai' => $statistik['total'], 'ikon' => 'fa-users', 'warna' => 'slate'],
                ['label' => 'Belum Diplot', 'nilai' => $statistik['belum'], 'ikon' => 'fa-hourglass-half', 'warna' => 'amber'],
                ['label' => 'Sudah Diplot', 'nilai' => $statistik['sudah'], 'ikon' => 'fa-layer-group', 'warna' => 'emerald'],
                ['label' => 'Calon Santri', 'nilai' => $statistik['calon'], 'ikon' => 'fa-user-clock', 'warna' => 'sky'],
            ] as $kartu)
                @php
                    $warna = match ($kartu['warna']) {
                        'amber' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-300',
                        'emerald' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300',
                        'sky' => 'bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-300',
                        default => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300',
                    };
                @endphp
                <div class="flex items-center gap-3 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $warna }}">
                        <i class="fa-solid {{ $kartu['ikon'] }}"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">{{ $kartu['label'] }}</p>
                        <p class="text-xl font-black text-slate-900 dark:text-white">{{ $kartu['nilai'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <details class="group rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:rounded-3xl">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-3">
                <span class="flex items-center gap-2 text-xs font-black text-slate-700 dark:text-slate-200">
                    <i class="fa-solid fa-plus text-emerald-600"></i> Tambah kelas baru
                </span>
                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition group-open:rotate-180"></i>
            </summary>
            <form method="POST" action="{{ route('admin.ppdb.kelas.store') }}" class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-4">
                @csrf
                <div>
                    <label class="mb-1 block text-[11px] font-bold text-slate-600 dark:text-slate-300">Jenjang *</label>
                    @if ($jenjangSlug)
                        <input type="hidden" name="jenjang" value="{{ $jenjangSlug }}">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-bold text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            {{ \App\Support\Jenjang::label($jenjangSlug) }}
                        </div>
                    @else
                        <select name="jenjang" required
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                            <option value="">- Pilih jenjang -</option>
                            @foreach ($jenjangList as $slug => $teks)
                                <option value="{{ $slug }}" @selected(old('jenjang') === $slug)>{{ $teks }}</option>
                            @endforeach
                        </select>
                    @endif
                    @error('jenjang') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-[11px] font-bold text-slate-600 dark:text-slate-300">Nama kelas *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required maxlength="50" placeholder="Contoh: VII A"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs text-slate-800 focus:border-emerald-500 focus:outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                    @error('name') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-[11px] font-bold text-slate-600 dark:text-slate-300">Tingkat</label>
                    <input type="text" name="tingkat" value="{{ old('tingkat') }}" maxlength="20" placeholder="VII"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs text-slate-800 focus:border-emerald-500 focus:outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                </div>
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Kelas
                    </button>
                </div>
            </form>
        </details>

        <form method="GET" action="{{ route('admin.ppdb.plotting') }}"
            class="grid grid-cols-1 gap-2 rounded-2xl border border-slate-200/80 bg-white p-3.5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:grid-cols-2 sm:rounded-3xl lg:grid-cols-5">
            <div class="relative lg:col-span-2">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="q" value="{{ $filter['q'] ?? '' }}" placeholder="Cari nama, NISN, email wali..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-xs text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-100">
            </div>
            <select name="class_id"
                class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:outline-none dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-200">
                <option value="">Semua kelas</option>
                @foreach ($kelasPerJenjang as $namaJenjang => $daftarKelas)
                    <optgroup label="{{ $namaJenjang }}">
                        @foreach ($daftarKelas as $kelas)
                            <option value="{{ $kelas->id }}" @selected((string) ($filter['class_id'] ?? '') === (string) $kelas->id)>{{ $kelas->name }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            @if (! $jenjangSlug)
                <select name="jenjang"
                    class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:outline-none dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-200">
                    <option value="">Semua jenjang</option>
                    @foreach ($jenjangList as $slug => $teks)
                        <option value="{{ $slug }}" @selected(($filter['jenjang'] ?? '') === $slug)>{{ $teks }}</option>
                    @endforeach
                </select>
            @else
                <select name="status"
                    class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:outline-none dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-200">
                    <option value="">Semua status</option>
                    @foreach (['calon' => 'Calon', 'aktif' => 'Aktif', 'lulus' => 'Lulus', 'keluar' => 'Keluar', 'pindah' => 'Pindah'] as $nilai => $teks)
                        <option value="{{ $nilai }}" @selected(($filter['status'] ?? '') === $nilai)>{{ $teks }}</option>
                    @endforeach
                </select>
            @endif
            <div class="flex gap-2">
                <button type="submit"
                    class="flex-1 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                    Filter
                </button>
                <a href="{{ route('admin.ppdb.plotting') }}"
                    class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    Reset
                </a>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.ppdb.plot') }}" id="formPlot" class="space-y-3">
            @csrf

            <div class="flex flex-col gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4 shadow-sm dark:border-emerald-500/20 dark:bg-emerald-500/10 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <label class="mb-1 block text-[11px] font-bold text-emerald-800 dark:text-emerald-200">Kelas tujuan *</label>
                    <select name="class_id" required
                        class="w-full rounded-xl border border-emerald-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:outline-none dark:border-emerald-500/30 dark:bg-slate-900 dark:text-slate-200">
                        <option value="">- Pilih kelas -</option>
                        @foreach ($kelasPerJenjang as $namaJenjang => $daftarKelas)
                            <optgroup label="{{ $namaJenjang }}">
                                @foreach ($daftarKelas as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->label() }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('class_id') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                </div>
                <button type="submit"
                    class="rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                    @disabled($kelasList->isEmpty())>
                    <i class="fa-solid fa-layer-group mr-1"></i> Plot Santri Terpilih
                </button>
            </div>

            @error('student_ids') <p class="text-[11px] font-bold text-rose-600">{{ $message }}</p> @enderror

            @if ($kelasList->isEmpty())
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs font-bold text-amber-800 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-200">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                    Belum ada kelas pada jenjang ini. Tambahkan kelas lewat form "Tambah kelas baru" di atas.
                </div>
            @endif

            <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:rounded-3xl">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-[10px] font-extrabold uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-400">
                                <th class="px-4 py-3">
                                    <input type="checkbox"
                                        onchange="document.querySelectorAll('.pilih-santri').forEach(function (c) { c.checked = this.checked; }.bind(this))"
                                        class="h-3.5 w-3.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                        title="Pilih semua di halaman ini">
                                </th>
                                <th class="px-4 py-3">Santri</th>
                                <th class="px-4 py-3">NISN</th>
                                <th class="px-4 py-3">Jenjang</th>
                                <th class="px-4 py-3">Wali</th>
                                <th class="px-4 py-3">Kelas Saat Ini</th>
                                <th class="px-4 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700 dark:divide-slate-800 dark:text-slate-300">
                            @forelse ($students as $student)
                                @php
                                    $kelasSekarang = $student->classes->first() ?: $student->kelas;
                                @endphp
                                <tr class="transition hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                    <td class="px-4 py-3.5">
                                        <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                            class="pilih-santri h-3.5 w-3.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <p class="font-black text-slate-900 dark:text-white">{{ $student->name }}</p>
                                        <p class="mt-0.5 text-[10px] text-slate-400">{{ $student->pendaftaranOnline?->no_pendaftaran ?: 'Data import/manual' }}</p>
                                    </td>
                                    <td class="px-4 py-3.5 font-mono text-slate-500">{{ $student->nisn }}</td>
                                    <td class="px-4 py-3.5">
                                        <span class="rounded-lg bg-slate-100 px-2 py-1 text-[10px] font-bold dark:bg-slate-800">
                                            {{ \App\Support\Jenjang::label($student->jenjang) ?: ($student->jenjang ?: '-') }}
                                        </span>
                                    </td>
                                    <td class="max-w-[170px] break-all px-4 py-3.5 text-slate-500">
                                        {{ $student->parent_email ?: ($student->user?->email ?: '-') }}
                                    </td>
                                    <td class="px-4 py-3.5">
                                        @if ($kelasSekarang)
                                            <span class="rounded-lg bg-emerald-50 px-2 py-1 text-[10px] font-bold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                                                {{ $kelasSekarang->label() }}
                                            </span>
                                        @else
                                            <span class="text-[10px] italic text-slate-400">Belum diplot</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <span class="rounded-full px-2.5 py-1 text-[10px] font-bold {{ $statusBadge($student->status) }}">
                                            {{ $student->labelStatus() }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center text-xs italic text-slate-400">
                                        Tidak ada santri yang sesuai filter. Import data atau terima pendaftar PPDB terlebih dahulu.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($students, 'links'))
                    <div class="border-t border-slate-100 p-4 dark:border-slate-800">
                        {{ $students->links() }}
                    </div>
                @endif
            </div>
        </form>
    </div>
@endsection
