@extends('layouts.app')

@section('title', 'Berita Pondok Pesantren Babul Hasanah')

@section('content')
    <!-- SECTION PORTAL BERITA ALA DETIK/KOMPAS -->
    <div class="py-12 lg:py-16 bg-slate-100 text-slate-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- 1. TOP BAR / KATEGORI & LIVE TICKER PENGUMUMAN -->
            <div
                class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3 overflow-x-auto w-full md:w-auto pb-2 md:pb-0">
                    <span
                        class="bg-red-600 text-white text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-md tracking-wider flex-shrink-0 animate-pulse">Fokus</span>
                    <div class="text-xs font-semibold text-slate-700 whitespace-nowrap">
                        <span class="text-emerald-600 font-bold mr-2">TERBARU:</span> Pendaftaran Santri Baru Tahun Ajaran
                        2026/2027 Resmi Dibuka!
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-500 font-medium flex-shrink-0">
                    <i class="fa-regular fa-calendar-days text-emerald-600"></i> Selasa, 25 Agustus 2026
                </div>
            </div>

            <!-- 2. MAIN GRID (Headline Utama + Sub-Headline + Trending Sidebar) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">

                <!-- KOLOM KIRI & TENGAH: HEADLINE UTAMA & 2 SUB-HEADLINE (8 Kolom) -->
                <div class="lg:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Headline Utama (Memakan 2 Kolom di Tablet/Desktop) -->
                    <article
                        class="md:col-span-2 bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-lg group flex flex-col md:flex-row">
                        <div class="md:w-1/2 h-64 md:h-auto bg-slate-200 relative overflow-hidden">
                            <img src="{{ asset('assets/img/banner/banner.png') }}" alt="Headline"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <span
                                class="absolute top-4 left-4 bg-slate-900/80 backdrop-blur-md text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase">
                                Utamanya
                            </span>
                        </div>
                        <div class="md:w-1/2 p-6 sm:p-8 flex flex-col justify-between space-y-4">
                            <div class="space-y-3">
                                <div
                                    class="flex items-center gap-2 text-[11px] font-bold text-emerald-600 uppercase tracking-wider">
                                    <span>Pendidikan</span> &bull; <span>10 Menit yang lalu</span>
                                </div>
                                <h1
                                    class="text-xl sm:text-2xl font-black text-slate-900 group-hover:text-emerald-600 transition leading-snug">
                                    <a href="#">Pondok Pesantren Babul Hasanah Cetak Generasi Unggul Penguasa Kitab
                                        Kuning</a>
                                </h1>
                                <p class="text-slate-600 text-xs sm:text-sm line-clamp-3 leading-relaxed">
                                    Komitmen tinggi dalam memadukan kurikulum salafiyah murni dan sistem pendidikan modern
                                    membawa hasil gemilang bagi perkembangan santri di Padang Lawas...
                                </p>
                            </div>
                            <span
                                class="text-xs font-bold text-slate-900 group-hover:text-emerald-600 flex items-center gap-1">
                                Baca Berita <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </span>
                        </div>
                    </article>

                    <!-- Sub-Headline 1 -->
                    <article
                        class="bg-white rounded-2xl overflow-hidden border border-slate-200/80 shadow-sm group flex flex-col justify-between p-5">
                        <div class="space-y-3">
                            <div class="h-36 bg-slate-200 rounded-xl overflow-hidden mb-3">
                                <img src="{{ asset('assets/img/banner/banner.png') }}" alt="Sub 1"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            </div>
                            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Pengumuman</span>
                            <h2
                                class="text-sm font-bold text-slate-900 group-hover:text-emerald-600 transition leading-snug line-clamp-2">
                                <a href="#">Jadwal Seleksi Masuk Gelombang 1 Resmi Diumumkan Oleh Panitia</a>
                            </h2>
                        </div>
                        <div
                            class="text-[11px] text-slate-400 pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span>2 jam lalu</span>
                            <span>Oleh Admin</span>
                        </div>
                    </article>

                    <!-- Sub-Headline 2 -->
                    <article
                        class="bg-white rounded-2xl overflow-hidden border border-slate-200/80 shadow-sm group flex flex-col justify-between p-5">
                        <div class="space-y-3">
                            <div class="h-36 bg-slate-200 rounded-xl overflow-hidden mb-3">
                                <img src="{{ asset('assets/img/banner/banner.png') }}" alt="Sub 2"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            </div>
                            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Kegiatan</span>
                            <h2
                                class="text-sm font-bold text-slate-900 group-hover:text-emerald-600 transition leading-snug line-clamp-2">
                                <a href="#">Santri Gelar Pelatihan Pidato 3 Bahasa (Arab, Inggris, Indonesia)</a>
                            </h2>
                        </div>
                        <div
                            class="text-[11px] text-slate-400 pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span>5 jam lalu</span>
                            <span>Oleh Pers Santri</span>
                        </div>
                    </article>

                </div>

                <!-- KOLOM KANAN: SIDEBAR TERPOPULER / INDEKS (4 Kolom) -->
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-lg space-y-6">
                        <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                            <h3 class="text-base font-black text-slate-900 uppercase tracking-wide flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span> Terpopuler
                            </h3>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Hourly</span>
                        </div>

                        <div class="space-y-5">
                            <!-- Trending Item 1 -->
                            <article class="flex items-start gap-4 group">
                                <span
                                    class="text-3xl font-black text-slate-200 group-hover:text-emerald-600 transition leading-none">1</span>
                                <div class="space-y-1">
                                    <span class="text-[10px] font-bold text-emerald-600 uppercase">Akademik</span>
                                    <h4
                                        class="text-xs font-bold text-slate-900 group-hover:text-emerald-600 transition leading-snug">
                                        <a href="#">Mengenal Metode Bandongan & Sorogan Kitab Kuning di Pesantren</a>
                                    </h4>
                                </div>
                            </article>

                            <!-- Trending Item 2 -->
                            <article class="flex items-start gap-4 group">
                                <span
                                    class="text-3xl font-black text-slate-200 group-hover:text-emerald-600 transition leading-none">2</span>
                                <div class="space-y-1">
                                    <span class="text-[10px] font-bold text-emerald-600 uppercase">Prestasi</span>
                                    <h4
                                        class="text-xs font-bold text-slate-900 group-hover:text-emerald-600 transition leading-snug">
                                        <a href="#">Alumni Babul Hasanah Berhasil Lanjut Studi ke Universitas Al-Azhar
                                            Kairo</a>
                                    </h4>
                                </div>
                            </article>

                            <!-- Trending Item 3 -->
                            <article class="flex items-start gap-4 group">
                                <span
                                    class="text-3xl font-black text-slate-200 group-hover:text-emerald-600 transition leading-none">3</span>
                                <div class="space-y-1">
                                    <span class="text-[10px] font-bold text-emerald-600 uppercase">Fasilitas</span>
                                    <h4
                                        class="text-xs font-bold text-slate-900 group-hover:text-emerald-600 transition leading-snug">
                                        <a href="#">Pembangunan Gedung Asrama Putra Tahap II Rampung Tepat Waktu</a>
                                    </h4>
                                </div>
                            </article>

                            <!-- Trending Item 4 -->
                            <article class="flex items-start gap-4 group">
                                <span
                                    class="text-3xl font-black text-slate-200 group-hover:text-emerald-600 transition leading-none">4</span>
                                <div class="space-y-1">
                                    <span class="text-[10px] font-bold text-emerald-600 uppercase">Kegiatan</span>
                                    <h4
                                        class="text-xs font-bold text-slate-900 group-hover:text-emerald-600 transition leading-snug">
                                        <a href="#">Peringatan Maulid Nabi Diisi Dengan Tabligh Akbar Bersama Ulama
                                            Tamu</a>
                                    </h4>
                                </div>
                            </article>
                        </div>
                    </div>

                    <!-- Banner / Call to Action Pendaftaran di Sidebar -->
                    <div
                        class="bg-gradient-to-br from-slate-900 to-emerald-950 rounded-3xl p-6 text-white shadow-xl space-y-4 border border-emerald-500/30">
                        <span
                            class="bg-emerald-500/20 text-emerald-400 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider border border-emerald-500/30 inline-block">PPDB
                            2026</span>
                        <h4 class="text-lg font-black leading-snug">Ingin Mendaftarkan Putra-Putri Anda?</h4>
                        <p class="text-slate-300 text-xs leading-relaxed">Dapatkan informasi lengkap mengenai syarat
                            pendaftaran, biaya, dan fasilitas asrama.</p>
                        <a href="{{ route('pendaftaran.index') }}"
                            class="block text-center bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 rounded-xl transition text-xs shadow-lg shadow-emerald-900/50">
                            Daftar Online Sekarang
                        </a>
                    </div>
                </div>

            </div>

            <!-- 3. FOKUS / BERITA TERBARU GRID (Gaya List Berita Panjang ala Detik) -->
            <div class="space-y-6">
                <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight flex items-center gap-2">
                        <i class="fa-solid fa-newspaper text-emerald-600"></i> Indeks Berita Terbaru
                    </h3>
                    <a href="#" class="text-xs font-bold text-emerald-600 hover:underline">Lihat Semua Indeks
                        &rarr;</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- List Item Berita 1 -->
                    <article
                        class="bg-white rounded-2xl overflow-hidden border border-slate-200/80 shadow-sm flex flex-col justify-between group p-4">
                        <div class="space-y-3">
                            <div class="h-40 bg-slate-200 rounded-xl overflow-hidden">
                                <img src="{{ asset('assets/img/banner/banner.png') }}" alt="News"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            </div>
                            <div class="flex items-center gap-2 text-[10px] font-bold text-emerald-600 uppercase">
                                <span>Pesantren</span> &bull; <span>Kemarin</span>
                            </div>
                            <h4
                                class="text-sm font-bold text-slate-900 group-hover:text-emerald-600 transition leading-snug">
                                <a href="#">Pentingnya Menjaga Kedisiplinan Bahasa Di Lingkungan Asrama Pesantren</a>
                            </h4>
                            <p class="text-slate-600 text-xs line-clamp-2 leading-relaxed">
                                Pembiasaan berbahasa Arab dan Inggris harian terbukti efektif meningkatkan kepercayaan diri
                                santri...
                            </p>
                        </div>
                    </article>

                    <!-- List Item Berita 2 -->
                    <article
                        class="bg-white rounded-2xl overflow-hidden border border-slate-200/80 shadow-sm flex flex-col justify-between group p-4">
                        <div class="space-y-3">
                            <div class="h-40 bg-slate-200 rounded-xl overflow-hidden">
                                <img src="{{ asset('assets/img/banner/banner.png') }}" alt="News"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            </div>
                            <div class="flex items-center gap-2 text-[10px] font-bold text-emerald-600 uppercase">
                                <span>Akademik</span> &bull; <span>23 Agustus 2026</span>
                            </div>
                            <h4
                                class="text-sm font-bold text-slate-900 group-hover:text-emerald-600 transition leading-snug">
                                <a href="#">Evaluasi Ujian Semester Ganjil Kitab Kuning Tingkat MTs & MA</a>
                            </h4>
                            <p class="text-slate-600 text-xs line-clamp-2 leading-relaxed">
                                Proses penilaian munaqasyah hafalan dan pemahaman teks klasik berjalan dengan lancar...
                            </p>
                        </div>
                    </article>

                    <!-- List Item Berita 3 -->
                    <article
                        class="bg-white rounded-2xl overflow-hidden border border-slate-200/80 shadow-sm flex flex-col justify-between group p-4">
                        <div class="space-y-3">
                            <div class="h-40 bg-slate-200 rounded-xl overflow-hidden">
                                <img src="{{ asset('assets/img/banner/banner.png') }}" alt="News"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            </div>
                            <div class="flex items-center gap-2 text-[10px] font-bold text-emerald-600 uppercase">
                                <span>Ekstrakurikuler</span> &bull; <span>20 Agustus 2026</span>
                            </div>
                            <h4
                                class="text-sm font-bold text-slate-900 group-hover:text-emerald-600 transition leading-snug">
                                <a href="#">Tim Hadroh Babul Hasanah Tampil Memukau di Acara Desa Manggis</a>
                            </h4>
                            <p class="text-slate-600 text-xs line-clamp-2 leading-relaxed">
                                Penampilan seni Islami tradisional ini mendapat apresiasi hangat dari warga sekitar...
                            </p>
                        </div>
                    </article>
                </div>
            </div>

        </div>
    </div>
@endsection
