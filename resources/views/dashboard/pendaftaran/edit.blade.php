@extends('layouts.admin')

@section('title', 'Edit Pendaftaran - ' . $pendaftaran->nama_lengkap)

@section('content')
    @php
        $labelCls = 'block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5';
        $inputCls = 'w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/60 text-slate-800 dark:text-slate-100 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none';
        $status = strtolower($pendaftaran->status_pendaftaran ?? 'pending');
    @endphp

    <div class="max-w-4xl mx-auto py-6 sm:py-8 px-4 sm:px-6 space-y-6">

        <!-- HEADER -->
        <div
            class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div>
                <span
                    class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-extrabold uppercase tracking-wider border border-emerald-500/20 mb-1">
                    Modul Seleksi
                </span>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Edit Data Pendaftar</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    <span class="font-mono font-bold">{{ $pendaftaran->no_pendaftaran }}</span>
                    &middot; Status: <span class="font-bold">{{ ucfirst($status) }}</span>
                </p>
            </div>
            <a href="{{ route('admin.pendaftaran.index') }}"
                class="self-start sm:self-center bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        @if ($status === 'diterima')
            <div
                class="bg-sky-50 dark:bg-sky-500/10 border border-sky-200 dark:border-sky-500/20 text-sky-800 dark:text-sky-300 px-4 py-3.5 rounded-2xl text-xs font-medium flex items-start gap-2.5">
                <i class="fa-solid fa-circle-info text-sky-600 dark:text-sky-400 mt-0.5"></i>
                <span>Pendaftar ini sudah diverifikasi. Perubahan data akan ikut memperbarui Data Santri dan akun Portal
                    Wali yang tertaut.</span>
            </div>
        @endif

        <!-- ERROR VALIDASI -->
        @if ($errors->any())
            <div
                class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-300 p-4 rounded-2xl text-xs">
                <p class="font-black mb-1">Data belum bisa disimpan:</p>
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- FORM -->
        <form action="{{ route('admin.pendaftaran.update', $pendaftaran->id) }}" method="POST"
            class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="{{ $labelCls }}" for="nama_lengkap">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input id="nama_lengkap" type="text" name="nama_lengkap" required maxlength="255"
                        value="{{ old('nama_lengkap', $pendaftaran->nama_lengkap) }}" class="{{ $inputCls }}">
                </div>

                <div>
                    <label class="{{ $labelCls }}" for="tempat_tanggal_lahir">Tempat Tanggal Lahir <span class="text-rose-500">*</span></label>
                    <input id="tempat_tanggal_lahir" type="text" name="tempat_tanggal_lahir" required maxlength="255"
                        value="{{ old('tempat_tanggal_lahir', $pendaftaran->tempat_tanggal_lahir) }}" class="{{ $inputCls }}"
                        placeholder="Contoh: Padang Lawas, 12 Januari 2012">
                </div>

                <div>
                    <label class="{{ $labelCls }}" for="no_hp_aktif">Nomor Handphone Aktif (WA) <span class="text-rose-500">*</span></label>
                    <input id="no_hp_aktif" type="text" name="no_hp_aktif" required maxlength="30"
                        value="{{ old('no_hp_aktif', $pendaftaran->no_hp_aktif) }}" class="{{ $inputCls }}">
                </div>

                <div class="sm:col-span-2">
                    <label class="{{ $labelCls }}" for="alamat_domisili">Alamat Tinggal (Domisili) <span class="text-rose-500">*</span></label>
                    <textarea id="alamat_domisili" name="alamat_domisili" rows="3" required maxlength="1000"
                        class="{{ $inputCls }}">{{ old('alamat_domisili', $pendaftaran->alamat_domisili) }}</textarea>
                </div>

                <div>
                    <label class="{{ $labelCls }}" for="email">Email Aktif <span class="text-rose-500">*</span></label>
                    <input id="email" type="email" name="email" required maxlength="255"
                        value="{{ old('email', $pendaftaran->email) }}" class="{{ $inputCls }}">
                </div>

                <div>
                    <label class="{{ $labelCls }}" for="jenjang_tujuan">Pilihan Jenjang <span class="text-rose-500">*</span></label>
                    <select id="jenjang_tujuan" name="jenjang_tujuan" required class="{{ $inputCls }}">
                        <option value="">-- Pilih Jenjang --</option>
                        @foreach ($jenjangList as $jenjang)
                            <option value="{{ $jenjang }}"
                                {{ old('jenjang_tujuan', $pendaftaran->jenjang_tujuan) === $jenjang ? 'selected' : '' }}>
                                {{ $jenjang }}
                            </option>
                        @endforeach
                        @if ($pendaftaran->jenjang_tujuan && ! in_array($pendaftaran->jenjang_tujuan, $jenjangList))
                            <option value="{{ $pendaftaran->jenjang_tujuan }}" selected>{{ $pendaftaran->jenjang_tujuan }}</option>
                        @endif
                    </select>
                </div>

                <div>
                    <label class="{{ $labelCls }}" for="nama_ortu_wali">Nama Orang Tua (Wali) <span class="text-rose-500">*</span></label>
                    <input id="nama_ortu_wali" type="text" name="nama_ortu_wali" required maxlength="255"
                        value="{{ old('nama_ortu_wali', $pendaftaran->nama_ortu_wali) }}" class="{{ $inputCls }}">
                </div>

                <div>
                    <label class="{{ $labelCls }}" for="no_hp_ortu">Nomor Handphone Orang Tua <span class="text-rose-500">*</span></label>
                    <input id="no_hp_ortu" type="text" name="no_hp_ortu" required maxlength="30"
                        value="{{ old('no_hp_ortu', $pendaftaran->no_hp_ortu) }}" class="{{ $inputCls }}">
                </div>

                <div class="sm:col-span-2">
                    <label class="{{ $labelCls }}" for="asal_sekolah">Sekolah Asal <span class="text-rose-500">*</span></label>
                    <input id="asal_sekolah" type="text" name="asal_sekolah" required maxlength="255"
                        value="{{ old('asal_sekolah', $pendaftaran->asal_sekolah) }}" class="{{ $inputCls }}">
                </div>

                <div class="sm:col-span-2">
                    <label class="{{ $labelCls }}" for="alamat_sekolah">Alamat Sekolah Asal <span class="text-rose-500">*</span></label>
                    <textarea id="alamat_sekolah" name="alamat_sekolah" rows="3" required maxlength="1000"
                        class="{{ $inputCls }}">{{ old('alamat_sekolah', $pendaftaran->alamat_sekolah) }}</textarea>
                </div>

                <div>
                    <label class="{{ $labelCls }}" for="tahun_lulus">Tahun Lulus <span class="text-rose-500">*</span></label>
                    <input id="tahun_lulus" type="text" name="tahun_lulus" required inputmode="numeric" maxlength="4"
                        pattern="[0-9]{4}" value="{{ old('tahun_lulus', $pendaftaran->tahun_lulus) }}" class="{{ $inputCls }}">
                </div>

                <div>
                    <label class="{{ $labelCls }}" for="nisn">NISN</label>
                    <input id="nisn" type="text" name="nisn" maxlength="20" inputmode="numeric"
                        value="{{ old('nisn', $pendaftaran->nisn) }}" class="{{ $inputCls }}">
                </div>
            </div>

            <div class="pt-2 flex flex-col-reverse sm:flex-row gap-2 sm:justify-between">
                <a href="{{ route('admin.pendaftaran.index') }}"
                    class="text-center bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold px-6 py-3 rounded-xl text-xs transition">
                    Batal
                </a>
                <button type="submit"
                    class="bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold px-6 py-3 rounded-xl text-xs transition shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>

        <!-- ZONA HAPUS -->
        <div
            class="bg-white dark:bg-slate-900 rounded-3xl border border-rose-200 dark:border-rose-500/20 p-6 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-sm font-black text-rose-700 dark:text-rose-400">Hapus Data Pendaftaran</h2>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                    Data santri hasil verifikasi ikut terhapus. Akun portal wali tetap dipertahankan.
                </p>
            </div>
            <form action="{{ route('admin.pendaftaran.destroy', $pendaftaran->id) }}" method="POST"
                class="self-start sm:self-center"
                onsubmit="return confirm('Hapus data pendaftaran ini secara permanen? Data santri hasil verifikasi juga akan dihapus.');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-bold px-5 py-2.5 rounded-xl text-xs transition flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-trash"></i> Hapus Pendaftaran
                </button>
            </form>
        </div>

    </div>
@endsection
