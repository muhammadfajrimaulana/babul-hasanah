@extends('layouts.admin')

@section('title', ($item ? 'Edit' : 'Tambah') . ' Jadwal Pelajaran - Akademik')

@use('App\Support\Akademik')

@section('content')
    @php
        $sedangEdit = (bool) $item;
        $aksi = $sedangEdit
            ? route('admin.akademik.jadwal.update', $item)
            : route('admin.akademik.jadwal.store');
        $slugAdmin = auth()->user()->jenjangSlug();
        $inputCls = 'w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20';
        $labelCls = 'block text-[11px] font-bold text-slate-600 dark:text-slate-300';
    @endphp

    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <a href="{{ route('admin.akademik.jadwal.index', ['jenjang' => $jenjang]) }}"
                class="inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-500 hover:text-emerald-700 transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke jadwal
            </a>
            <h2 class="mt-1 text-xl font-black text-slate-900 dark:text-white">
                {{ $sedangEdit ? 'Edit Jadwal Pelajaran' : 'Tambah Jadwal Pelajaran' }}
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Jam bentrok dengan jadwal kelas atau guru lain pada hari yang sama akan ditolak.
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-xs text-rose-800 space-y-1 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
                @foreach ($errors->all() as $error)
                    <p class="flex items-start gap-2"><i class="fa-solid fa-triangle-exclamation mt-0.5"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ $aksi }}"
            class="space-y-5 rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm">
            @csrf
            @if ($sedangEdit)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="space-y-1.5 sm:col-span-2">
                    <label class="{{ $labelCls }}">Jenjang</label>
                    @if ($slugAdmin)
                        <input type="hidden" name="jenjang" value="{{ $slugAdmin }}">
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-xs font-bold text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
                            {{ auth()->user()->namaJenjang() }}
                        </div>
                    @else
                        <select name="jenjang" required class="{{ $inputCls }}">
                            <option value="">Pilih jenjang</option>
                            @foreach ($daftarJenjang as $j)
                                <option value="{{ $j['slug'] }}" @selected(old('jenjang', $jenjang) === $j['slug'])>
                                    {{ $j['kode'] }} — {{ $j['nama'] }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div class="space-y-1.5">
                    <label class="{{ $labelCls }}">Kelas</label>
                    <input type="text" name="kelas" maxlength="50" required
                        value="{{ old('kelas', $item?->kelas) }}" placeholder="Contoh: 7A" class="{{ $inputCls }}">
                </div>

                <div class="space-y-1.5">
                    <label class="{{ $labelCls }}">Hari</label>
                    <select name="hari" required class="{{ $inputCls }}">
                        @foreach ($hari as $nama)
                            <option value="{{ $nama }}" @selected(old('hari', $item?->hari) === $nama)>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="{{ $labelCls }}">Jam Mulai</label>
                    <input type="time" name="jam_mulai" required
                        value="{{ old('jam_mulai', $item ? substr((string) $item->jam_mulai, 0, 5) : '07:00') }}"
                        class="{{ $inputCls }}">
                </div>

                <div class="space-y-1.5">
                    <label class="{{ $labelCls }}">Jam Selesai</label>
                    <input type="time" name="jam_selesai" required
                        value="{{ old('jam_selesai', $item ? substr((string) $item->jam_selesai, 0, 5) : '08:30') }}"
                        class="{{ $inputCls }}">
                </div>

                <div class="space-y-1.5 sm:col-span-2">
                    <label class="{{ $labelCls }}">Mata Pelajaran</label>
                    <select name="mata_pelajaran_id" required class="{{ $inputCls }}">
                        <option value="">Pilih mata pelajaran</option>
                        @foreach ($daftarMapel as $mapel)
                            <option value="{{ $mapel->id }}" @selected((int) old('mata_pelajaran_id', $item?->mata_pelajaran_id) === $mapel->id)>
                                {{ $mapel->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="{{ $labelCls }}">Guru Pengampu <span class="font-normal text-slate-400">(opsional)</span></label>
                    <select name="guru_id" class="{{ $inputCls }}">
                        <option value="">Belum ditentukan</option>
                        @foreach ($daftarGuru as $guru)
                            <option value="{{ $guru->id }}" @selected((int) old('guru_id', $item?->guru_id) === $guru->id)>{{ $guru->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="{{ $labelCls }}">Ruang <span class="font-normal text-slate-400">(opsional)</span></label>
                    <input type="text" name="ruang" maxlength="50" value="{{ old('ruang', $item?->ruang) }}"
                        placeholder="Contoh: R-1" class="{{ $inputCls }}">
                </div>

                <div class="space-y-1.5">
                    <label class="{{ $labelCls }}">Tahun Ajaran</label>
                    <select name="tahun_ajaran" required class="{{ $inputCls }}">
                        @foreach (Akademik::tahunAjaranPilihan() as $tahun)
                            <option value="{{ $tahun }}" @selected(old('tahun_ajaran', $item?->tahun_ajaran ?? Akademik::tahunAjaranSekarang()) === $tahun)>{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="{{ $labelCls }}">Semester</label>
                    <select name="semester" required class="{{ $inputCls }}">
                        @foreach (Akademik::SEMESTER as $nilai => $label)
                            <option value="{{ $nilai }}" @selected(old('semester', $item?->semester ?? Akademik::semesterSekarang()) === $nilai)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="{{ $labelCls }}">Urutan <span class="font-normal text-slate-400">(opsional)</span></label>
                    <input type="number" name="urutan" min="0" max="99" value="{{ old('urutan', $item?->urutan ?? 0) }}"
                        class="{{ $inputCls }}">
                </div>
            </div>

            <div class="flex flex-col gap-2 border-t border-slate-100 pt-4 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-[10px] text-slate-400">Jadwal yang tersimpan langsung tampil di portal wali sesuai jenjang dan kelas anak.</p>
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-6 py-3 text-xs font-black text-white transition hover:bg-emerald-800">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
@endsection
