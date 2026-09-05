@extends('layouts.portal')

@section('content')
    <div class="space-y-6">

        <!-- BANNER SELAMAT DATANG -->
        <div class="bg-gradient-to-r from-emerald-900 to-slate-900 text-white p-8 rounded-3xl shadow-lg space-y-3">
            <span
                class="bg-emerald-500/20 text-emerald-400 text-xs font-bold px-3 py-1 rounded-full border border-emerald-500/30 inline-block">
                Monitoring Santri Aktif
            </span>
            <h2 class="text-2xl sm:text-3xl font-black">Selamat Datang, {{ Auth::user()->name }}</h2>
            <p class="text-slate-300 text-xs sm:text-sm max-w-2xl leading-relaxed">
                Pantau perkembangan hafalan, rekap absensi harian, dan informasi pembayaran infaq pendidikan putra/putri
                Anda secara real-time dari rumah.
            </p>
        </div>

        <!-- KARTU INFORMASI ANAK -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Status Kehadiran -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-3 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status Kehadiran</span>
                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                </div>
                <h3 class="text-lg font-black text-slate-900">Hadir di Pesantren</h3>
                <p class="text-xs text-slate-500 font-medium">Terakhir absen: Masuk Ashar (Pukul 15:30 WIB)</p>
            </div>

            <!-- Perkembangan Tahfidz -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-3 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Perkembangan Tahfidz</span>
                    <span
                        class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-book-quran"></i>
                    </span>
                </div>
                <h3 class="text-lg font-black text-slate-900">Juz 28 (Al-Mujadilah)</h3>
                <p class="text-xs text-slate-500 font-medium">Setoran terakhir: Surat Al-Hasyr</p>
            </div>

            <!-- Administrasi Infaq -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-3 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Administrasi Infaq</span>
                    <span
                        class="bg-emerald-100 text-emerald-800 px-2.5 py-1 rounded-full text-[10px] font-bold">Lunas</span>
                </div>
                <h3 class="text-lg font-black text-slate-900">Bulan Agustus 2026</h3>
                <p class="text-xs text-slate-500 font-medium">Tidak ada tanggungan pembayaran aktif.</p>
            </div>

        </div>

    </div>
@endsection
