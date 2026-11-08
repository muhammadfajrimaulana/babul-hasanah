@extends('layouts.app')

@section('title', 'Formulir Pendaftaran Pondok Pesantren Babul Hasanah')

@section('content')
    @php
        $labelCls = 'block text-xs font-bold text-slate-700 mb-1.5';
        $inputCls = 'w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none';
        $errorCls = 'text-[11px] text-rose-600 mt-1';
        $jenjangList = \App\Http\Controllers\Public\PendaftaranOnlineController::JENJANG;
    @endphp

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50/20 to-slate-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto space-y-6">

            <!-- Header Informasi -->
            <div class="text-center space-y-3">
                <span
                    class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-800 text-xs font-extrabold px-3.5 py-1.5 rounded-full uppercase tracking-wider shadow-sm">
                    <i class="fa-solid fa-graduation-cap"></i> PPDB 2026/2027
                </span>
                <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">Pendaftaran Santri Baru</h1>
                <p class="text-slate-600 text-xs sm:text-sm max-w-xl mx-auto leading-relaxed">
                    Isi data berikut dengan benar. Gunakan <b>email orang tua/wali</b> yang aktif — email tersebut menjadi
                    akun login Portal Wali Santri setelah pendaftaran diverifikasi admin.
                </p>
            </div>

            <!-- ALERT SUKSES -->
            @if (session('success'))
                <div
                    class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-900 p-5 rounded-2xl shadow-sm flex items-start gap-3 animate-fade-in">
                    <div
                        class="bg-emerald-500 text-white rounded-full p-1.5 flex items-center justify-center shrink-0 mt-0.5">
                        <i class="fa-solid fa-check text-xs"></i>
                    </div>
                    <div class="text-xs space-y-1">
                        <p class="font-black text-emerald-900 text-sm">Alhamdulillah, Pendaftaran Berhasil!</p>
                        <p class="text-emerald-700 leading-relaxed">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- ALERT ERROR VALIDASI -->
            @if ($errors->any())
                <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-900 p-5 rounded-2xl shadow-sm flex items-start gap-3">
                    <div class="bg-rose-500 text-white rounded-full p-1.5 flex items-center justify-center shrink-0 mt-0.5">
                        <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                    </div>
                    <div class="text-xs space-y-1 w-full">
                        <p class="font-black text-rose-900 text-sm">Terjadi Kesalahan Pengisian Formulir</p>
                        <ul class="list-disc pl-4 space-y-1 text-rose-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Card Form Pendaftaran -->
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/80 p-8 sm:p-10 backdrop-blur-sm">
                <form action="{{ route('pendaftaran.store') }}" method="POST" class="space-y-8">
                    @csrf

                    <!-- BAGIAN 1: DATA CALON SANTRI -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                            <span class="flex items-center justify-center w-6 h-6 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-bold">1</span>
                            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Data Calon Santri</h3>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="{{ $labelCls }}" for="nama_lengkap">Nama Lengkap <span class="text-rose-500">*</span></label>
                                <input id="nama_lengkap" type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                                    maxlength="255" class="{{ $inputCls }}" placeholder="Contoh: Ahmad Zaky Al-Fatih">
                                @error('nama_lengkap') <p class="{{ $errorCls }}">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="{{ $labelCls }}" for="tempat_tanggal_lahir">Tempat Tanggal Lahir <span class="text-rose-500">*</span></label>
                                <input id="tempat_tanggal_lahir" type="text" name="tempat_tanggal_lahir"
                                    value="{{ old('tempat_tanggal_lahir') }}" required maxlength="255" class="{{ $inputCls }}"
                                    placeholder="Contoh: Padang Lawas, 12 Januari 2012">
                                @error('tempat_tanggal_lahir') <p class="{{ $errorCls }}">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="{{ $labelCls }}" for="no_hp_aktif">Nomor Handphone Aktif (WA) <span class="text-rose-500">*</span></label>
                                <input id="no_hp_aktif" type="text" name="no_hp_aktif" value="{{ old('no_hp_aktif') }}" required
                                    maxlength="30" class="{{ $inputCls }}" placeholder="Contoh: 081234567890">
                                @error('no_hp_aktif') <p class="{{ $errorCls }}">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label class="{{ $labelCls }}" for="alamat_domisili">Alamat Tinggal (Domisili) <span class="text-rose-500">*</span></label>
                                <textarea id="alamat_domisili" name="alamat_domisili" rows="3" required maxlength="1000"
                                    class="{{ $inputCls }}" placeholder="Contoh: Jl. Lintas Pinarik, Desa Manggis, Kec. Batang Lubu Sutam">{{ old('alamat_domisili') }}</textarea>
                                @error('alamat_domisili') <p class="{{ $errorCls }}">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label class="{{ $labelCls }}" for="jenjang_tujuan">Pilihan Jenjang <span class="text-rose-500">*</span></label>
                                <select id="jenjang_tujuan" name="jenjang_tujuan" required class="{{ $inputCls }} bg-white">
                                    <option value="">-- Pilih Jenjang --</option>
                                    @foreach ($jenjangList as $jenjang)
                                        <option value="{{ $jenjang }}" {{ old('jenjang_tujuan') === $jenjang ? 'selected' : '' }}>
                                            {{ $jenjang }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jenjang_tujuan') <p class="{{ $errorCls }}">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN 2: DATA ORANG TUA / WALI -->
                    <div class="space-y-4 pt-4">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                            <span class="flex items-center justify-center w-6 h-6 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-bold">2</span>
                            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Data Orang Tua / Wali</h3>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="{{ $labelCls }}" for="nama_ortu_wali">Nama Orang Tua (Wali) <span class="text-rose-500">*</span></label>
                                <input id="nama_ortu_wali" type="text" name="nama_ortu_wali" value="{{ old('nama_ortu_wali') }}" required
                                    maxlength="255" class="{{ $inputCls }}" placeholder="Contoh: Budi Santoso">
                                @error('nama_ortu_wali') <p class="{{ $errorCls }}">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="{{ $labelCls }}" for="no_hp_ortu">Nomor Handphone Orang Tua <span class="text-rose-500">*</span></label>
                                <input id="no_hp_ortu" type="text" name="no_hp_ortu" value="{{ old('no_hp_ortu') }}" required
                                    maxlength="30" class="{{ $inputCls }}" placeholder="Contoh: 081234567890">
                                @error('no_hp_ortu') <p class="{{ $errorCls }}">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label class="{{ $labelCls }}" for="email">Email Orang Tua / Wali <span class="text-rose-500">*</span></label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="255"
                                    class="{{ $inputCls }}" placeholder="email.orangtua@gmail.com" autocomplete="email">
                                <p class="text-xs text-slate-500 mt-1">
                                    Email milik orang tua/wali. Dipakai untuk <b>login Portal Wali Santri</b> setelah pendaftaran diverifikasi.
                                    Santri tidak perlu memiliki email sendiri.
                                </p>
                                @error('email') <p class="{{ $errorCls }}">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN 3: SEKOLAH ASAL -->
                    <div class="space-y-4 pt-4">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                            <span class="flex items-center justify-center w-6 h-6 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-bold">3</span>
                            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Sekolah Asal</h3>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="{{ $labelCls }}" for="asal_sekolah">Sekolah Asal <span class="text-rose-500">*</span></label>
                                <input id="asal_sekolah" type="text" name="asal_sekolah" value="{{ old('asal_sekolah') }}" required
                                    maxlength="255" class="{{ $inputCls }}" placeholder="Contoh: SD Negeri 01 Manggis">
                                @error('asal_sekolah') <p class="{{ $errorCls }}">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label class="{{ $labelCls }}" for="alamat_sekolah">Alamat Sekolah Asal <span class="text-rose-500">*</span></label>
                                <textarea id="alamat_sekolah" name="alamat_sekolah" rows="3" required maxlength="1000"
                                    class="{{ $inputCls }}" placeholder="Alamat lengkap sekolah asal">{{ old('alamat_sekolah') }}</textarea>
                                @error('alamat_sekolah') <p class="{{ $errorCls }}">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="{{ $labelCls }}" for="tahun_lulus">Tahun Lulus <span class="text-rose-500">*</span></label>
                                <input id="tahun_lulus" type="text" name="tahun_lulus" value="{{ old('tahun_lulus') }}" required
                                    inputmode="numeric" maxlength="4" pattern="[0-9]{4}" class="{{ $inputCls }}" placeholder="Contoh: 2026">
                                @error('tahun_lulus') <p class="{{ $errorCls }}">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="{{ $labelCls }}" for="nisn">NISN</label>
                                <input id="nisn" type="text" name="nisn" value="{{ old('nisn') }}" maxlength="20"
                                    inputmode="numeric" class="{{ $inputCls }}" placeholder="Contoh: 1234567890">
                                <p class="text-xs text-slate-500 mt-1">Opsional jika belum memiliki NISN.</p>
                                @error('nisn') <p class="{{ $errorCls }}">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="mt-6 border-t border-slate-100 pt-6 flex items-center justify-end">
                        <button type="submit"
                            class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-black px-8 py-4 rounded-xl text-xs uppercase tracking-wider transition-all duration-300 shadow-lg shadow-emerald-600/30 hover:shadow-emerald-600/50 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-paper-plane"></i> Kirim Pendaftaran
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection
