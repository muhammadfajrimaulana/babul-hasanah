@extends('layouts.admin')

@section('title', 'Import Santri - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-5 pb-8">
        <div class="flex flex-col gap-3 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:flex-row sm:items-center sm:justify-between sm:rounded-3xl sm:p-6">
            <div>
                <span class="inline-block rounded-full border border-sky-500/20 bg-sky-50 px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider text-sky-700 dark:bg-sky-500/10 dark:text-sky-300">
                    Modul PPDB
                </span>
                <h1 class="mt-1 text-lg font-black tracking-tight text-slate-900 dark:text-white sm:text-2xl">Import Data Santri</h1>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                    Unggah file Excel/CSV untuk mengisi tabel <code>students</code>. Cakupan:
                    <b>{{ $targetJenjang }}</b>.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.santri.template') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-3.5 py-2 text-xs font-bold text-white transition hover:bg-emerald-700">
                    <i class="fa-solid fa-download"></i> Unduh Template
                </a>
                <a href="{{ route('admin.santri.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                    <i class="fa-solid fa-arrow-left"></i> Data Santri
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

        @error('file_excel')
            <div class="flex items-center gap-2.5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-xs font-bold text-rose-800 shadow-sm dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-300">
                <i class="fa-solid fa-circle-exclamation text-sm text-rose-600"></i>
                {{ $message }}
            </div>
        @enderror

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-5">
            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:rounded-3xl lg:col-span-3">
                <h2 class="mb-3 flex items-center gap-2 text-sm font-black text-slate-900 dark:text-white">
                    <i class="fa-solid fa-file-excel text-emerald-600"></i> Unggah Berkas
                </h2>

                <form method="POST" action="{{ route('admin.santri.import') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="mb-1 block text-[11px] font-bold text-slate-600 dark:text-slate-300">File Excel / CSV *</label>
                        <input type="file" name="file_excel" accept=".xlsx,.xls,.csv" required
                            class="w-full cursor-pointer rounded-xl border border-slate-200 text-xs text-slate-500 file:mr-4 file:rounded-xl file:border-0 file:bg-emerald-50 file:px-4 file:py-2.5 file:text-xs file:font-bold file:text-emerald-700 hover:file:bg-emerald-100 dark:border-slate-700 dark:text-slate-400 dark:file:bg-emerald-950/50 dark:file:text-emerald-400">
                        <p class="mt-1 text-[10px] text-slate-400">Format .xlsx, .xls, atau .csv. Maksimal 5 MB.</p>
                    </div>

                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                        <i class="fa-solid fa-upload"></i> Proses Import
                    </button>
                </form>

                <div class="mt-5 rounded-2xl border border-sky-100 bg-sky-50/70 p-4 text-[11px] leading-relaxed text-sky-900 dark:border-sky-500/20 dark:bg-sky-500/10 dark:text-sky-200">
                    <p class="mb-1 font-extrabold">Cara kerja impor</p>
                    <ul class="list-disc space-y-0.5 pl-4">
                        <li>Baris judul wajib berisi: <code>nisn</code>, <code>nama_santri</code>, <code>jenjang</code>, <code>parent_email</code>.</li>
                        <li>NISN menjadi kunci: data yang sudah ada akan <b>diperbarui</b>, bukan diduplikasi.</li>
                        <li>Email wali otomatis dicarikan akun. Jika belum ada, akun wali dibuat dengan
                            <b>kata sandi acak unik</b> yang ditampilkan pada rincian hasil impor.</li>
                        <li>Baris yang gagal tidak membatalkan baris lain; rinciannya tampil di bawah.</li>
                    </ul>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:rounded-3xl lg:col-span-2">
                <h2 class="mb-3 flex items-center gap-2 text-sm font-black text-slate-900 dark:text-white">
                    <i class="fa-solid fa-list-check text-sky-600"></i> Aturan Kolom
                </h2>
                <dl class="space-y-2.5 text-[11px]">
                    @foreach ([
                        ['nisn', 'Wajib, unik, maksimal 30 karakter.'],
                        ['nama_santri', 'Wajib, maksimal 255 karakter.'],
                        ['jenjang', 'Wajib. Kode/nama resmi: MDTA, PPS Wustha, MTs, MA, PDF Wustha, PDF Ulya, Ma\'had \'Aly.'],
                        ['parent_email', 'Wajib, format email valid. Dipakai sebagai akun wali.'],
                    ] as [$kolom, $aturan])
                        <div class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-2 dark:border-slate-800 dark:bg-slate-800/50">
                            <dt class="font-mono text-[11px] font-bold text-emerald-700 dark:text-emerald-400">{{ $kolom }}</dt>
                            <dd class="mt-0.5 text-slate-500 dark:text-slate-400">{{ $aturan }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>

        @if ($laporan)
            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:rounded-3xl">
                <h2 class="mb-3 flex items-center gap-2 text-sm font-black text-slate-900 dark:text-white">
                    <i class="fa-solid fa-clipboard-check text-emerald-600"></i> Laporan Import
                </h2>

                <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                    @foreach ([
                        ['Total Baris', $laporan['total'], 'slate'],
                        ['Berhasil', $laporan['berhasil'], 'emerald'],
                        ['Dibuat Baru', $laporan['dibuat'], 'sky'],
                        ['Diperbarui', $laporan['diperbarui'], 'amber'],
                    ] as [$teks, $nilai, $warna])
                        @php
                            $kelasWarna = match ($warna) {
                                'emerald' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
                                'sky' => 'bg-sky-50 text-sky-700 dark:bg-sky-500/10 dark:text-sky-300',
                                'amber' => 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300',
                                default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                            };
                        @endphp
                        <div class="rounded-2xl border border-slate-100 px-4 py-3 dark:border-slate-800 {{ $kelasWarna }}">
                            <p class="text-[10px] font-extrabold uppercase tracking-wider opacity-70">{{ $teks }}</p>
                            <p class="text-xl font-black">{{ $nilai }}</p>
                        </div>
                    @endforeach
                </div>

                @if (! empty($laporan['gagal']))
                    <div class="mt-4">
                        <p class="mb-2 text-xs font-extrabold text-rose-700 dark:text-rose-300">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                            {{ count($laporan['gagal']) }} baris gagal
                        </p>
                        <div class="overflow-x-auto rounded-xl border border-rose-100 dark:border-rose-500/20">
                            <table class="w-full text-left text-[11px]">
                                <thead class="bg-rose-50 text-rose-800 dark:bg-rose-500/10 dark:text-rose-200">
                                    <tr>
                                        <th class="px-3 py-2">Baris</th>
                                        <th class="px-3 py-2">NISN</th>
                                        <th class="px-3 py-2">Nama</th>
                                        <th class="px-3 py-2">Pesan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-rose-50 dark:divide-rose-500/10">
                                    @foreach ($laporan['gagal'] as $gagal)
                                        <tr class="text-slate-700 dark:text-slate-300">
                                            <td class="px-3 py-2 font-mono">{{ $gagal['baris'] }}</td>
                                            <td class="px-3 py-2 font-mono">{{ $gagal['nisn'] ?: '-' }}</td>
                                            <td class="px-3 py-2">{{ $gagal['nama'] ?: '-' }}</td>
                                            <td class="px-3 py-2 text-rose-700 dark:text-rose-300">{{ $gagal['pesan'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if (! empty($laporan['detail']))
                    <details class="mt-4">
                        <summary class="cursor-pointer text-xs font-bold text-emerald-700 dark:text-emerald-400">
                            Lihat {{ count($laporan['detail']) }} baris berhasil
                        </summary>
                        <div class="mt-2 overflow-x-auto rounded-xl border border-emerald-100 dark:border-emerald-500/20">
                            <table class="w-full text-left text-[11px]">
                                <thead class="bg-emerald-50 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-200">
                                    <tr>
                                        <th class="px-3 py-2">Baris</th>
                                        <th class="px-3 py-2">NISN</th>
                                        <th class="px-3 py-2">Nama</th>
                                        <th class="px-3 py-2">Jenjang</th>
                                        <th class="px-3 py-2">Status</th>
                                        <th class="px-3 py-2">Akun Wali</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-emerald-50 dark:divide-emerald-500/10">
                                    @foreach ($laporan['detail'] as $baris)
                                        <tr class="text-slate-700 dark:text-slate-300">
                                            <td class="px-3 py-2 font-mono">{{ $baris['baris'] }}</td>
                                            <td class="px-3 py-2 font-mono">{{ $baris['nisn'] }}</td>
                                            <td class="px-3 py-2">{{ $baris['nama'] }}</td>
                                            <td class="px-3 py-2">{{ $baris['jenjang'] }}</td>
                                            <td class="px-3 py-2 capitalize">{{ $baris['status'] }}</td>
                                            <td class="px-3 py-2 break-all">
                                                {{ $baris['email'] }}
                                                @if (! empty($baris['password']))
                                                    <br><span class="font-mono text-emerald-700 dark:text-emerald-400">{{ $baris['password'] }}</span>
                                                    <br><span class="text-[10px] text-slate-400">Catat kata sandi ini; hanya tampil sekali.</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </details>
                @endif
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:rounded-3xl">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                <h2 class="text-sm font-black text-slate-900 dark:text-white">Data Santri Terbaru</h2>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tabel students</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-[10px] font-extrabold uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-400">
                            <th class="px-4 py-3">NISN</th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Jenjang</th>
                            <th class="px-4 py-3">Email Wali</th>
                            <th class="px-4 py-3">Kelas</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700 dark:divide-slate-800 dark:text-slate-300">
                        @forelse ($students as $student)
                            <tr class="transition hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                <td class="px-4 py-3 font-mono text-slate-500">{{ $student->nisn }}</td>
                                <td class="px-4 py-3 font-black text-slate-900 dark:text-white">{{ $student->name }}</td>
                                <td class="px-4 py-3">{{ \App\Support\Jenjang::label($student->jenjang) ?: ($student->jenjang ?: '-') }}</td>
                                <td class="max-w-[190px] break-all px-4 py-3 text-slate-500">{{ $student->parent_email ?: ($student->user?->email ?: '-') }}</td>
                                <td class="px-4 py-3">{{ $student->kelas?->label() ?: '—' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                        {{ $student->labelStatus() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-xs italic text-slate-400">
                                    Belum ada data santri pada cakupan jenjang ini.
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
    </div>
@endsection
