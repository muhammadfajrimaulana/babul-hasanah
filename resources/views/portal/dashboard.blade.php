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
                Pantau perkembangan akademik, setoran tahfidz, rekap absensi, dan administrasi
                putra/putri Anda secara real-time dari rumah.
            </p>
            <div class="flex flex-wrap items-center gap-2 pt-1">
                <a href="{{ route('portal.nilai') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-900 px-4 py-2.5 text-xs font-black transition">
                    <i class="fa-solid fa-book-open"></i> Buka Rapor &amp; Transkrip
                </a>
            </div>
        </div>

        <!-- NOTIFIKASI NILAI BARU -->
        @if (! empty($adaNilaiBaru) && $adaNilaiBaru)
            <a href="{{ route('portal.nilai') }}"
                class="flex items-start gap-3 rounded-3xl border border-emerald-200 bg-emerald-50 p-5 hover:border-emerald-400 transition">
                <span class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm shrink-0">
                    <i class="fa-solid fa-bell"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-xs font-black text-emerald-900">Nilai baru telah diterbitkan</p>
                    <p class="text-[11px] text-emerald-700 leading-relaxed">
                        Pengajar baru saja menerbitkan nilai untuk ananda. Klik untuk melihat rincian nilai dan mengunduh rapor PDF.
                    </p>
                </div>
                <i class="fa-solid fa-chevron-right text-emerald-500 text-xs mt-3 ml-auto shrink-0"></i>
            </a>
        @endif

        <!-- DAFTAR ANAK / SANTRI YANG TERHUBUNG -->
        <div class="space-y-6">
            @forelse($anakList as $anak)
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full uppercase">
                                    {{ $anak->jenjang ?? 'Jenjang Umum' }}
                                </span>
                                <span class="text-xs text-slate-400 font-medium">
                                    Kelas: {{ $anak->kelas ?? '-' }}
                                </span>
                            </div>
                            <h3 class="text-xl font-black text-slate-900">{{ $anak->nama }}</h3>
                            <p class="text-xs text-slate-500 font-mono mt-0.5">NISN / NIAT: {{ $anak->nis }}</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            @php($ringkasNilai = $infoNilai->get($anak->key))
                            @if ($ringkasNilai && $ringkasNilai->jumlah > 0)
                                <span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 border border-emerald-200 px-3 py-1.5 text-[11px] font-bold text-emerald-700">
                                    <i class="fa-solid fa-book-open"></i>
                                    {{ $ringkasNilai->jumlah }} nilai terbit
                                </span>
                            @endif
                            <a href="{{ route('portal.nilai', ['anak' => $anak->key]) }}"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 text-[11px] font-bold transition">
                                <i class="fa-solid fa-eye"></i> Buka Portal
                            </a>
                            <a href="{{ route('portal.nilai', ['anak' => $anak->key, 'tab' => 'transkrip']) }}"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700 px-3 py-1.5 text-[11px] font-bold transition">
                                <i class="fa-solid fa-chart-line"></i> Transkrip
                            </a>
                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700">
                                Wali: {{ $anak->namaWali ?? Auth::user()->name }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-slate-50/50 p-5 rounded-2xl border border-slate-200/60 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status Kehadiran</span>
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            </div>
                            <h4 class="text-base font-bold text-slate-900">Aktif di Pesantren</h4>
                            <p class="text-xs text-slate-500">Alamat: {{ $anak->alamat ?? 'Tidak dicantumkan' }}</p>
                        </div>

                        <div class="bg-slate-50/50 p-5 rounded-2xl border border-slate-200/60 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Informasi Akademik</span>
                                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                </span>
                            </div>
                            <h4 class="text-base font-bold text-slate-900">{{ $anak->jenjang ?? 'Jenjang Umum' }} - {{ $anak->kelas ?? 'Kelas Belum Diatur' }}</h4>
                            <p class="text-xs text-slate-500">Terdaftar resmi di sistem pesantren</p>
                        </div>

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
                <div class="bg-amber-50 border border-amber-200 p-8 rounded-3xl text-center space-y-3">
                    <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto text-lg">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <h3 class="text-lg font-bold text-amber-900">Belum Ada Data Anak Terhubung</h3>
                    <p class="text-xs sm:text-sm text-amber-700 max-w-md mx-auto leading-relaxed">
                        Akun Anda belum dikaitkan dengan data santri manapun. Pastikan email akun ini sama
                        dengan <span class="font-mono">parent_email</span> santri, atau hubungi Admin Pesantren.
                    </p>
                </div>
            @endforelse
        </div>

    </div>
@endsection
