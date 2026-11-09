@extends('layouts.portal')

@section('content')
    <div class="space-y-6">

        <!-- BANNER SELAMAT DATANG -->
        <div class="bg-gradient-to-r from-emerald-900 to-slate-900 text-white p-8 rounded-3xl shadow-lg space-y-3">
            <span class="bg-emerald-500/20 text-emerald-400 text-xs font-bold px-3 py-1 rounded-full border border-emerald-500/30 inline-block">
                Monitoring Santri Aktif
            </span>
            <h2 class="text-2xl sm:text-3xl font-black">Selamat Datang, {{ Auth::user()->name }}</h2>
            <p class="text-slate-300 text-xs sm:text-sm max-w-2xl leading-relaxed">
                Pantau perkembangan akademik, rekap absensi harian, dan informasi pendidikan putra/putri 
                Anda secara real-time dari rumah.
            </p>
        </div>

        <!-- DAFTAR ANAK / SANTRI YANG TERHUBUNG -->
        <div class="space-y-6">
            @forelse($daftarAnak as $anak)
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
                    
                    <!-- Header Info Identitas Anak -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full uppercase">
                                    {{ $anak->jenjang_pendidikan ?? 'Jenjang Umum' }}
                                </span>
                                <span class="text-xs text-slate-400 font-medium">
                                    Kelas: {{ $anak->kelas ?? '-' }}
                                </span>
                            </div>
                            <h3 class="text-xl font-black text-slate-900">{{ $anak->nama_lengkap }}</h3>
                            <p class="text-xs text-slate-500 font-mono mt-0.5">NISN / NIAT: {{ $anak->nomor_induk }}</p>
                        </div>

                        <div>
                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700">
                                Wali: {{ $anak->nama_wali ?? Auth::user()->name }}
                            </span>
                        </div>
                    </div>

                    <!-- KARTU INFORMASI/STATISTIK ANAK -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        <!-- Status Kehadiran -->
                        <div class="bg-slate-50/50 p-5 rounded-2xl border border-slate-200/60 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status Kehadiran</span>
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            </div>
                            <h4 class="text-base font-bold text-slate-900">Aktif di Pesantren</h4>
                            <p class="text-xs text-slate-500">Alamat: {{ $anak->alamat ?? 'Tidak dicantumkan' }}</p>
                        </div>

                        <!-- Perkembangan Tahfidz / Akademik -->
                        <div class="bg-slate-50/50 p-5 rounded-2xl border border-slate-200/60 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Informasi Akademik</span>
                                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                </span>
                            </div>
                            <h4 class="text-base font-bold text-slate-900">{{ $anak->jenjang_pendidikan }} - {{ $anak->kelas ?? 'Kelas Belum Diatur' }}</h4>
                            <p class="text-xs text-slate-500">Terdaftar resmi di sistem pesantren</p>
                        </div>

                        <!-- Administrasi / Status -->
                        <div class="bg-slate-50/50 p-5 rounded-2xl border border-slate-200/60 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status Data</span>
                                <span class="bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded-full text-[10px] font-bold">Terverifikasi</span>
                            </div>
                            <h4 class="text-base font-bold text-slate-900">Data Sinkron</h4>
                            <p class="text-xs text-slate-500">Terhubung ke database pusat</p>
                        </div>

                    </div>

                </div>
            @empty
                <!-- Tampilan jika wali santri belum memiliki anak yang di-link ke akunnya -->
                <div class="bg-amber-50 border border-amber-200 p-8 rounded-3xl text-center space-y-3">
                    <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto text-lg">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <h3 class="text-lg font-bold text-amber-900">Belum Ada Data Anak Terhubung</h3>
                    <p class="text-xs sm:text-sm text-amber-700 max-w-md mx-auto leading-relaxed">
                        Akun Anda belum dikaitkan dengan data santri manapun. Silakan informasikan Nomor Induk atau Nama Santri Anda ke pihak Admin Pesantren.
                    </p>
                </div>
            @endforelse
        </div>

    </div>
@endsection