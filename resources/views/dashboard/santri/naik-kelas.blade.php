@extends('layouts.admin')

@section('title', 'Naik / Tinggal Kelas - Pondok Pesantren Babul Hasanah')

@section('content')
    @php
        $in = 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500';
        $lb = 'mb-1.5 block text-[11px] font-bold text-slate-600 dark:text-slate-300';
        $card = 'rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900';
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Naik / Tinggal Kelas</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Proses massal per kelas. Nilai dan riwayat lama tetap tersimpan. Rollback hanya dapat dilakukan Superadmin dengan alasan.
                </p>
            </div>
            <a href="{{ route('admin.santri.index') }}"
                class="inline-flex items-center gap-1.5 self-start rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-[11px] font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Data Santri
            </a>
        </div>

        @if (session('error'))
            <div class="rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-xs font-bold text-rose-700">
                <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-xs font-bold text-rose-700">
                <i class="fa-solid fa-circle-exclamation mr-1"></i> Periksa kembali isian yang ditandai.
            </div>
        @endif

        <form method="GET" action="{{ route('admin.santri.naik-kelas') }}" class="{{ $card }} flex flex-col gap-3 sm:flex-row sm:items-end">
            @if ($bisaPilihJenjang)
                <div class="sm:w-64">
                    <label class="{{ $lb }}">Jenjang</label>
                    <select name="jenjang" class="{{ $in }}">
                        <option value="">Semua jenjang</option>
                        @foreach (\App\Support\Jenjang::semua() as $j)
                            <option value="{{ $j['slug'] }}" @selected(\App\Support\Jenjang::normalisasi($jenjang) === $j['slug'])>
                                {{ $j['kode'] }} — {{ $j['nama'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="sm:w-64">
                <label class="{{ $lb }}">Kelas</label>
                <select name="kelas" class="{{ $in }}">
                    <option value="">— Pilih kelas —</option>
                    @foreach ($daftarKelas as $k)
                        <option value="{{ $k }}" @selected((string) request('kelas') === (string) $k)>{{ $k }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-black text-white hover:bg-emerald-700">
                <i class="fa-solid fa-magnifying-glass mr-1"></i> Tampilkan Santri
            </button>
        </form>

        @if ($santris->isEmpty())
            <div class="{{ $card }} py-12 text-center text-xs text-slate-400">
                <i class="fa-solid fa-arrow-up-right-dots mb-3 block text-3xl text-slate-300"></i>
                @if ($daftarKelas->isEmpty())
                    Belum ada kelas yang dapat diproses. Tambahkan kelas terlebih dahulu.
                @else
                    Pilih kelas untuk menampilkan daftar santri yang akan diproses.
                @endif
            </div>
        @else
            <form method="POST" action="{{ route('admin.santri.naik-kelas.proses') }}" class="{{ $card }} space-y-5">
                @csrf

                <div class="grid grid-cols-1 gap-4 border-b border-slate-100 pb-4 dark:border-slate-800 sm:grid-cols-3">
                    <div>
                        <label class="{{ $lb }}">Tahun Ajaran</label>
                        <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', $tahunAjaran) }}" required
                            class="{{ $in }}" placeholder="2026/2027">
                        @error('tahun_ajaran') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $lb }}">Semester</label>
                        <select name="semester" required class="{{ $in }}">
                            <option value="1" @selected(old('semester') === '1')>Semester 1</option>
                            <option value="2" @selected(old('semester') === '2')>Semester 2</option>
                        </select>
                        @error('semester') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-end">
                        <div class="w-full rounded-xl bg-slate-50 px-4 py-2.5 text-[11px] font-semibold text-slate-500 dark:bg-slate-800/60 dark:text-slate-300">
                            {{ $santris->count() }} santri pada kelas {{ request('kelas') }}
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-400 dark:bg-slate-800/50">
                                <th class="px-3 py-3 font-extrabold">#</th>
                                <th class="px-3 py-3 font-extrabold">Nama Santri</th>
                                <th class="px-3 py-3 font-extrabold">Jenjang</th>
                                <th class="px-3 py-3 font-extrabold">Kelas Saat Ini</th>
                                <th class="px-3 py-3 font-extrabold">Keputusan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($santris as $i => $s)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                    <td class="px-3 py-3 text-slate-400">{{ $i + 1 }}</td>
                                    <td class="px-3 py-3">
                                        <p class="font-bold text-slate-700 dark:text-slate-200">{{ $s->nama_lengkap }}</p>
                                        <p class="text-[10px] font-mono text-slate-400">{{ $s->nomor_induk ?: '-' }}</p>
                                    </td>
                                    <td class="px-3 py-3 text-slate-500">{{ \App\Support\Jenjang::label($s->jenjang_pendidikan) ?: '-' }}</td>
                                    <td class="px-3 py-3 text-slate-500">{{ $s->kelas ?: '-' }}</td>
                                    <td class="px-3 py-3">
                                        <input type="hidden" name="santri_id[]" value="{{ $s->id }}">
                                        <select name="status[{{ $s->id }}]" required
                                            class="rounded-xl border border-slate-200 px-3 py-2 text-[11px] font-bold dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                            <option value="naik" @selected(old('status.'.$s->id) === 'naik')>Naik Kelas</option>
                                            <option value="tinggal" @selected(old('status.'.$s->id) === 'tinggal')>Tinggal Kelas</option>
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @error('santri_id') <p class="text-[11px] text-rose-600">{{ $message }}</p> @enderror

                <div class="flex flex-col gap-2 border-t border-slate-100 pt-4 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-[10px] leading-relaxed text-slate-400">
                        <i class="fa-solid fa-circle-info mr-1"></i>
                        Proses ini mencatat riwayat kelas baru. Nilai semester lama tidak berubah.
                    </p>
                    <button type="submit"
                        class="rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-black text-white hover:bg-indigo-700"
                        onclick="return confirm('Proses kenaikan/tinggal kelas untuk {{ $santris->count() }} santri?');">
                        <i class="fa-solid fa-arrow-up-right-dots mr-1"></i> Proses {{ $santris->count() }} Santri
                    </button>
                </div>
            </form>
        @endif
    </div>
@endsection
