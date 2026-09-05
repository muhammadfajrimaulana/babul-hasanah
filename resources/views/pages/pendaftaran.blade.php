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

                            <!-- NIK -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">NIK Santri / Kartu
                                    Keluarga</label>
                                <input type="text" name="nik" value="{{ old('nik') }}" required
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="3201xxxxxxxxxxxx">
                            </div>

                            <!-- Tempat, Tanggal Lahir -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tempat, Tanggal Lahir</label>
                                <input type="text" name="tempat_tanggal_lahir" value="{{ old('tempat_tanggal_lahir') }}"
                                    required
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="Contoh: Bogor, 12 Januari 2012">
                            </div>

                            <!-- Jenjang Tujuan -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jenjang Pendidikan
                                    Tujuan</label>
                                <select name="jenjang_tujuan" required
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none bg-white">
                                    <option value="">-- Pilih Jenjang --</option>
                                    <option value="MTs" {{ old('jenjang_tujuan') == 'MTs' ? 'selected' : '' }}>MTs
                                        (Tingkat SLTP)</option>
                                    <option value="MA" {{ old('jenjang_tujuan') == 'MA' ? 'selected' : '' }}>MA (Tingkat
                                        SLTA)</option>
                                    <option value="Diniyah" {{ old('jenjang_tujuan') == 'Diniyah' ? 'selected' : '' }}>
                                        Madrasah Diniyah</option>
                                </select>
                            </div>

                            <!-- Asal Sekolah -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Asal Sekolah Sebelumnya</label>
                                <input type="text" name="asal_sekolah" value="{{ old('asal_sekolah') }}" required
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="Contoh: SD Negeri 01 Parung">
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
                            <!-- Nama Ortu/Wali -->
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Orang Tua / Wali</label>
                                <input type="text" name="nama_ortu_wali" value="{{ old('nama_ortu_wali') }}" required
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="Contoh: Budi Santoso">
                            </div>

                            <!-- Email (Akun Login) -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Aktif (Untuk Login
                                    Portal)</label>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="budi@gmail.com">
                            </div>

                            <!-- No HP / WhatsApp -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor WhatsApp Aktif</label>
                                <input type="text" name="no_hp_ortu" value="{{ old('no_hp_ortu') }}" required
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none"
                                    placeholder="081234567890">
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
