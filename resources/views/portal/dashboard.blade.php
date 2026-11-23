@extends('layouts.portal')

@use('App\Support\Akademik')

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
            <div class="flex flex-wrap items-center gap-2 pt-1">
                <a href="{{ route('portal.nilai') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-900 px-4 py-2.5 text-xs font-black transition">
                    <i class="fa-solid fa-book-open"></i> Lihat Nilai Santri
                </a>
                <a href="{{ route('portal.prestasi') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-white/25 hover:bg-white/10 text-white px-4 py-2.5 text-xs font-black transition">
                    <i class="fa-solid fa-trophy"></i> Lihat Prestasi
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

        <!-- NOTIFIKASI PRESTASI BARU -->
        @if (! empty($adaPrestasiBaru) && $adaPrestasiBaru)
            @php($prestasiPertama = $prestasiTerbaru->first())
            <a href="{{ route('portal.prestasi') }}"
                class="flex items-start gap-3 rounded-3xl border border-amber-200 bg-amber-50 p-5 hover:border-amber-400 transition">
                <span class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-sm shrink-0">
                    <i class="fa-solid fa-trophy"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-xs font-black text-amber-900">Prestasi baru ananda</p>
                    <p class="text-[11px] text-amber-700 leading-relaxed">
                        @if ($prestasiPertama)
                            <b>{{ $prestasiPertama->judul }}</b> &mdash; {{ $prestasiPertama->label }}
                            ({{ $prestasiPertama->nama }}). Klik untuk melihat seluruh prestasi ananda.
                        @else
                            Ada prestasi baru yang tercatat untuk ananda.
                        @endif
                    </p>
                </div>
                <i class="fa-solid fa-chevron-right text-amber-500 text-xs mt-3 ml-auto shrink-0"></i>
            </a>
        @endif

        <!-- DAFTAR ANAK / SANTRI YANG TERHUBUNG -->
        <div class="space-y-6">
            @forelse($daftarAnak as $anak)
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
                    
                    <!-- Header Info Identitas Anak -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full uppercase">
                                    {{ $anak->jenjang_pendidikan ?? 'Jenjang Umum' }}
                                </span>
                                <span class="text-xs text-slate-400 font-medium">
                                    Kelas: {{ $anak->kelas ?? '-' }}
                                </span>
                                <span class="rounded-full px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider
                                    {{ $anak->sudah_lulus ? 'bg-indigo-100 text-indigo-700' : ($anak->non_aktif ? 'bg-slate-200 text-slate-600' : 'bg-emerald-100 text-emerald-700') }}">
                                    {{ $anak->labelStatus() }}
                                </span>
                            </div>
                            <h3 class="text-xl font-black text-slate-900">{{ $anak->nama_lengkap }}</h3>
                            <p class="text-xs text-slate-500 font-mono mt-0.5">NISN / NIAT: {{ $anak->nomor_induk }}</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            @php($ringkasNilai = $infoNilai->get($anak->id))
                            @if ($ringkasNilai)
                                <span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 border border-emerald-200 px-3 py-1.5 text-[11px] font-bold text-emerald-700">
                                    <i class="fa-solid fa-book-open"></i>
                                    {{ $ringkasNilai->jumlah }} nilai terbit
                                </span>
                            @endif
                            <a href="{{ route('portal.nilai', ['anak' => $anak->id]) }}"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 text-[11px] font-bold transition">
                                <i class="fa-solid fa-eye"></i> Lihat Nilai
                            </a>
                            <a href="{{ route('portal.anak.show', $anak) }}"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-[11px] font-bold text-emerald-700 transition hover:bg-emerald-100">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                                {{ $anak->non_aktif ? 'Riwayat Anak' : 'Detail Anak' }}
                            </a>
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
                            <h4 class="text-base font-bold text-slate-900">
                                {{ $anak->sudah_lulus ? 'Telah Lulus (Alumni)' : ($anak->non_aktif ? $anak->labelStatus() : 'Aktif di Pesantren') }}
                            </h4>
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

                    <!-- RINGKASAN NILAI TERBARU DARI ADMIN JENJANG -->
                    @php($terbaru = $nilaiTerbaru->get($anak->id) ?? collect())
                    <div class="space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <h4 class="text-sm font-black text-slate-900">Nilai Terbaru</h4>
                                <p class="text-[11px] text-slate-500">
                                    Nilai yang diinput dan diterbitkan oleh Admin Jenjang.
                                </p>
                            </div>
                            <a href="{{ route('portal.nilai', ['anak' => $anak->id]) }}"
                                class="inline-flex shrink-0 items-center gap-1.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 px-3.5 py-2 text-[11px] font-bold text-white transition">
                                <i class="fa-solid fa-book-open"></i> Lihat Semua Nilai
                            </a>
                        </div>

                        @if ($terbaru->isEmpty())
                            <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-6 text-center text-[11px] text-slate-400">
                                <i class="fa-solid fa-clock mr-1"></i>
                                Belum ada nilai yang diterbitkan untuk ananda. Silakan cek kembali nanti.
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                @foreach ($terbaru as $n)
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4 space-y-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-[9px] font-black uppercase tracking-wider text-slate-400">
                                                {{ Akademik::labelKategori($n->kategori) }}
                                            </span>
                                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[9px] font-black text-emerald-700">
                                                {{ Akademik::predikat($n->nilai) }}
                                            </span>
                                        </div>
                                        <p class="truncate text-[11px] font-bold text-slate-700">
                                            {{ $n->mataPelajaran?->nama ?? '-' }}
                                        </p>
                                        <p class="text-2xl font-black text-emerald-700">
                                            {{ Akademik::formatNilai($n->nilai) }}
                                        </p>
                                        <p class="text-[10px] text-slate-400">
                                            {{ $n->tahun_ajaran }} · Semester {{ Akademik::labelSemester($n->semester) }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
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