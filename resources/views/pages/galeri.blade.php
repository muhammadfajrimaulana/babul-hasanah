@extends('layouts.app')

@section('title', 'Galeri Pondok Pesantren Babul Hasanah')

@section('content')
    <!-- SECTION GALERI & DOKUMENTASI -->
    <div class="py-16 lg:py-24 bg-slate-100 text-slate-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header Halaman Galeri -->
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-12">
                <span
                    class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-800 text-xs font-bold px-4 py-1.5 rounded-full uppercase tracking-wider">
                    <i class="fa-solid fa-images text-emerald-600"></i> Dokumentasi Visual
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-slate-900">
                    Galeri Kegiatan & <span class="text-emerald-600">Fasilitas Pesantren</span>
                </h1>
                <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                    Merekam momen kebersamaan, aktivitas belajar mengajar, kegiatan ekstrakurikuler, serta suasana asrama di
                    Pondok Pesantren Babul Hasanah.
                </p>
            </div>

            <!-- Filter Kategori Galeri -->
            <div class="flex flex-wrap items-center justify-center gap-2 mb-12">
                <button type="button"
                    class="bg-emerald-600 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-md transition">Semua
                    Foto</button>
                <button type="button"
                    class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold px-5 py-2.5 rounded-xl transition">Kegiatan
                    Santri</button>
                <button type="button"
                    class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold px-5 py-2.5 rounded-xl transition">Kajian
                    Kitab</button>
                <button type="button"
                    class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold px-5 py-2.5 rounded-xl transition">Fasilitas
                    Asrama</button>
                <button type="button"
                    class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold px-5 py-2.5 rounded-xl transition">Wisuda
                    & Haflah</button>
            </div>

            <!-- Grid Galeri (Masonry / Modern Grid Style) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Item Galeri 1 -->
                <div
                    class="group relative bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-md aspect-[4/3]">
                    <img src="{{ asset('assets/img/banner/banner.png') }}" alt="Dokumentasi 1"
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-6">
                        <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Kajian
                            Kitab</span>
                        <h3 class="text-white font-bold text-base leading-snug">Bandongan Kitab Kuning Bersama Asatidz</h3>
                        <p class="text-slate-300 text-xs mt-1">Suasana belajar santri di aula utama pondok pesantren.</p>
                    </div>
                </div>

                <!-- Item Galeri 2 -->
                <div
                    class="group relative bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-md aspect-[4/3]">
                    <img src="{{ asset('assets/img/banner/banner.png') }}" alt="Dokumentasi 2"
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-6">
                        <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Kegiatan
                            Santri</span>
                        <h3 class="text-white font-bold text-base leading-snug">Praktik Muhadharah 3 Bahasa</h3>
                        <p class="text-slate-300 text-xs mt-1">Melatih mental dan retorika dakwah santri tingkat MA.</p>
                    </div>
                </div>

                <!-- Item Galeri 3 -->
                <div
                    class="group relative bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-md aspect-[4/3]">
                    <img src="{{ asset('assets/img/banner/banner.png') }}" alt="Dokumentasi 3"
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-6">
                        <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Fasilitas</span>
                        <h3 class="text-white font-bold text-base leading-snug">Gedung Asrama Putra & Lingkungan</h3>
                        <p class="text-slate-300 text-xs mt-1">Kondisi asrama yang bersih, nyaman, dan kondusif untuk
                            belajar.</p>
                    </div>
                </div>

                <!-- Item Galeri 4 -->
                <div
                    class="group relative bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-md aspect-[4/3]">
                    <img src="{{ asset('assets/img/banner/banner.png') }}" alt="Dokumentasi 4"
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-6">
                        <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Wisuda &
                            Haflah</span>
                        <h3 class="text-white font-bold text-base leading-snug">Wisuda Tahfidz & Haflah Akhirussanah</h3>
                        <p class="text-slate-300 text-xs mt-1">Momen haru dan bahagia pelepasan santri angkatan kelima.</p>
                    </div>
                </div>

                <!-- Item Galeri 5 -->
                <div
                    class="group relative bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-md aspect-[4/3]">
                    <img src="{{ asset('assets/img/banner/banner.png') }}" alt="Dokumentasi 5"
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-6">
                        <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Kegiatan
                            Santri</span>
                        <h3 class="text-white font-bold text-base leading-snug">Ekstrakulikuler Tim Hadroh</h3>
                        <p class="text-slate-300 text-xs mt-1">Penyaluran bakat seni Islami di sela-sela waktu luang.</p>
                    </div>
                </div>

                <!-- Item Galeri 6 -->
                <div
                    class="group relative bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-md aspect-[4/3]">
                    <img src="{{ asset('assets/img/banner/banner.png') }}" alt="Dokumentasi 6"
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-6">
                        <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Kajian
                            Kitab</span>
                        <h3 class="text-white font-bold text-base leading-snug">Setoran Hafalan Al-Qur'an (Tasmi')</h3>
                        <p class="text-slate-300 text-xs mt-1">Program pembinaan hafalan intensif harian.</p>
                    </div>
                </div>

            </div>

            <!-- Tombol Muat Lebih Banyak -->
            <div class="text-center mt-12">
                <button type="button"
                    class="bg-white hover:bg-slate-50 text-slate-900 border border-slate-200 font-semibold px-8 py-3.5 rounded-xl shadow-sm transition text-sm">
                    Muat Lebih Banyak Foto
                </button>
            </div>

        </div>
    </div>
@endsection
