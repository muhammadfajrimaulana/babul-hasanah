@extends('layouts.app')

@section('title', 'Formulir Pendaftaran Pondok Pesantren Babul Hasanah')

@section('content')
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
                    Silakan isi formulir di bawah ini dengan data yang valid. Setelah diverifikasi oleh admin, akun portal
                    wali santri akan diaktifkan.
                </p>
            </div>

            <!-- ALERT SUKSES (Flash Message dari Controller) -->
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
                <div
                    class="bg-rose-50 border-l-4 border-rose-500 text-rose-900 p-5 rounded-2xl shadow-sm flex items-start gap-3">
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
            <div
                class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/80 p-8 sm:p-10 backdrop-blur-sm">
                <form action="{{ route('pendaftaran.store') }}" method="POST" class="space-y-8">
                    @csrf

                    <!-- BAGIAN A: DATA CALON SANTRI -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                            <span
                                class="flex items-center justify-center w-6 h-6 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-bold">1</span>
                            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                                Informasi Calon Santri
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Nama Lengkap -->
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap Santri</label>
                                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="Contoh: Ahmad Zaky Al-Fatih">
                            </div>

                            <!-- Nama Panggilan -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Panggilan</label>
                                <input type="text" name="nama_panggilan" value="{{ old('nama_panggilan') }}"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="Contoh: Ahmad">
                            </div>

                            <!-- NIK Santri -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">NIK Santri</label>
                                <input type="text" name="nik_santri" value="{{ old('nik_santri') }}"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="3201xxxxxxxxxxxx">
                                <p class="text-xs text-slate-500 mt-1">Opsional, unik di seluruh pendaftar</p>
                            </div>

                            <!-- NISN -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">NISN (Nomor Induk Siswa Nasional)</label>
                                <input type="text" name="nisn" value="{{ old('nisn') }}"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="Contoh: 1234567890">
                                <p class="text-xs text-slate-500 mt-1">Opsional</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Jenis Kelamin -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jenis Kelamin</label>
                                <div class="space-y-2">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="jenis_kelamin" value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'checked' : '' }}
                                            class="w-4 h-4 rounded border-gray-300">
                                        <span>Laki-laki</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="jenis_kelamin" value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'checked' : '' }}
                                            class="w-4 h-4 rounded border-gray-300">
                                        <span>Perempuan</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Tempat, Tanggal Lahir -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tempat, Tanggal Lahir</label>
                                <input type="text" name="tempat_tanggal_lahir" value="{{ old('tempat_tanggal_lahir') }}"
                                    required
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="Contoh: Bogor, 12 Januari 2012">
                            </div>

                            <!-- Asal Sekolah/Madrasah -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Asal Sekolah/Madrasah</label>
                                <input type="text" name="asal_sekolah" value="{{ old('asal_sekolah') }}" required
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="Contoh: SD Negeri 01 Parung">
                            </div>

                            <!-- Jenjang Tujuan -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jenjang Pendidikan Tujuan</label>
                                <select name="jenjang_tujuan" required
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none bg-white">
                                    <option value="">-- Pilih Jenjang --</option>
                                    <option value="MTs">MTs (Tingkat SLTP)</option>
                                    <option value="MA">MA (Tingkat SLTA)</option>
                                    <option value="Madrasah Diniyah Takmiliyah">Madrasah Diniyah Takmiliyah</option>
                                    <option value="PPS Wustha">PPS Wustha</option>
                                    <option value="Diniyah Formal Wustha">Diniyah Formal Wustha</option>
                                    <option value="Diniyah Formal Ulya">Diniyah Formal Ulya</option>
                                    <option value="Ma'had 'Aly">Ma'had 'Aly</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN B: DATA ORANG TUA / WALI -->
                    <div class="space-y-4 pt-4">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                            <span
                                class="flex items-center justify-center w-6 h-6 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-bold">2</span>
                            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                                Informasi Orang Tua / Wali (Untuk Akun Portal)
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Nama Ayah -->
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Ayah</label>
                                <input type="text" name="nama_ayah" value="{{ old('nama_ayah') }}"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="Contoh: Budi Santoso">
                            </div>

                            <!-- Nama Ibu -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Ibu</label>
                                <input type="text" name="nama_ibu" value="{{ old('nama_ibu') }}"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="Contoh: Siti Fatimah">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- NIK Orang Tua -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">NIK Orang Tua</label>
                                <input type="text" name="nik_ortu" value="{{ old('nik_ortu') }}"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="3201xxxxxxxxxxxx">
                                <p class="text-xs text-slate-500 mt-1">Opsional</p>
                            </div>

                            <!-- Pekerjaan Orang Tua -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Pekerjaan Orang Tua</label>
                                <input type="text" name="pekerjaan_ortu" value="{{ old('pekerjaan_ortu') }}"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="Contoh: Pegawai Negeri Sipil">
                            </div>
                        </div>

                        <!-- Email & No HP (ditambahkan) -->
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Email -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Aktif (Untuk Login Portal)</label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="budi@gmail.com">
                            </div>

                            <!-- No HP / WhatsApp -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor WhatsApp Aktif</label>
                                <input type="text" name="no_hp_ortu" value="{{ old('no_hp_ortu') }}"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="081234567890">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- NIK Orang Tua -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">NIK Orang Tua</label>
                                <input type="text" name="nik_ortu" value="{{ old('nik_ortu') }}"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="3201xxxxxxxxxxxx">
                                <p class="text-xs text-slate-500 mt-1">Opsional</p>
                            </div>

                            <!-- Pekerjaan Orang Tua -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Pekerjaan Orang Tua</label>
                                <input type="text" name="pekerjaan_ortu" value="{{ old('pekerjaan_ortu') }}"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="Contoh: Pegawai Negeri Sipil">
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN C: DATA ALAMAT -->
                    <div class="space-y-4 pt-4">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                            <span
                                class="flex items-center justify-center w-6 h-6 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-bold">3</span>
                            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                                Data Alamat
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Alamat Lengkap (Jalan) -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Lengkap (Jalan)</label>
                                <input type="text" name="alamat_jalan" value="{{ old('alamat_jalan') }}"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="Contoh: Jl. Merdeka No. 15">
                            </div>

                            <!-- RT/RW -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">RT/RW</label>
                                <input type="text" name="alamat_rt_rw" value="{{ old('alamat_rt_rw') }}"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="Contoh: RT 01/RW 02">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Desa/Kelurahan -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Desa/Kelurahan</label>
                                <input type="text" name="alamat_desa_kelurahan" value="{{ old('alamat_desa_kelurahan') }}"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="Contoh: Kelurahan Kebun Beringin">
                            </div>

                            <!-- Kecamatan -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Kecamatan</label>
                                <input type="text" name="alamat_kecamatan" value="{{ old('alamat_kecamatan') }}"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="Contoh: Kecamatan Central">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Kabupaten/Kota -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Kabupaten/Kota</label>
                                <input type="text" name="alamat_kabupaten_kota" value="{{ old('alamat_kabupaten_kota') }}"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="Contoh: Kota Jakarta Selatan">
                            </div>

                            <!-- Provinsi -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Provinsi</label>
                                <input type="text" name="alamat_provinsi" value="{{ old('alamat_provinsi') }}"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="Contoh: DKI Jakarta">
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN D: BERKAS DIGITAL -->
                    <div class="space-y-4 pt-4">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                            <span
                                class="flex items-center justify-center w-6 h-6 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-bold">4</span>
                            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                                Berkas Digital
                            </h3>
                        </div>

                        <p class="text-xs text-slate-500">
                            Format gambar: JPG, PNG (maksimal 2 MB). Format berkas lainnya: PDF (maksimal 2 MB).
                            <br>Berkas akan disimpan di server, nama file akan disimpan ke database.
                        </p>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Pasfoto Santri -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Pasfoto Santri</label>
                                <input type="file" name="berkas_pasfoto"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none">
                                <p class="text-xs text-slate-500 mt-1">Format: JPG/PNG, maksimal 2 MB</p>
                            </div>

                            <!-- Scan Kartu Keluarga -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Scan Kartu Keluarga (KK)</label>
                                <input type="file" name="berkas_kk"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none">
                                <p class="text-xs text-slate-500 mt-1">Format: PDF/JPG, maksimal 2 MB</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Scan Akta Kelahiran -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Scan Akta Kelahiran</label>
                                <input type="file" name="berkas_akta_kelahiran"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none">
                                <p class="text-xs text-slate-500 mt-1">Format: PDF/JPG, maksimal 2 MB</p>
                            </div>

                            <!-- Scan Ijazah/SKL -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Scan Ijazah Terakhir / SKL</label>
                                <input type="file" name="berkas_ijazah"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none">
                                <p class="text-xs text-slate-500 mt-1">Format: PDF/JPG, maksimal 2 MB</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Scan KTP Orang Tua -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Scan KTP Orang Tua/Wali</label>
                                <input type="file" name="berkas_ktp_ortu"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none">
                                <p class="text-xs text-slate-500 mt-1">Format: PDF/JPG, maksimal 2 MB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="mt-6 border-t border-slate-100 flex items-center justify-end">
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