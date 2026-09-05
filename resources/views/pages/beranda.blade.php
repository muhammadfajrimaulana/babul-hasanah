@extends('layouts.app')

@section('title', 'Pondok Pesantren Babul Hasanah')

@section('content')
    {{-- HERO SECTION --}}
    <div class="relative bg-slate-950 text-white py-16 lg:py-32 bg-[image:var(--banner-img)] bg-cover bg-center">
        <div class="absolute inset-0 bg-slate-950/90 z-0"
            style="background-image: linear-gradient(to right, rgba(2, 6, 23, 0.85), rgba(2, 6, 23, 0.485)), url('{{ asset('assets/img/banner/banner.png') }}'); background-size: cover; background-position: center;">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full flex flex-col justify-between min-h-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 bg-emerald-500/10 text-emerald-300 text-xs font-semibold px-4 py-2 rounded-full border border-emerald-500/20 backdrop-blur-md uppercase tracking-wider">
                        <i class="fa-solid fa-sparkles text-emerald-400"></i> Selamat Datang di Official Website
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-6xl font-extrabold tracking-tight leading-tight">
                        Membangun Generasi Unggul Dalam Penguasaan <span class="text-emerald-400">Kitab Kuning</span>
                    </h1>

                    <p class="text-sm sm:text-base lg:text-lg text-slate-300 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                        Pondok Pesantren Babul Hasanah berkomitmen mendidik santri yang mendalami ilmu agama, unggul dalam
                        kitab kuning, berakhlak mulia serta berani menghadapi tantangan zaman.
                    </p>

                    <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-4 pt-2">
                        <a href="{{ route('pendaftaran.index') }}"
                            class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold px-8 py-3.5 rounded-xl shadow-lg shadow-emerald-900/40 transition duration-200 text-center flex items-center justify-center gap-2">
                            <span>Daftar Santri Baru</span>
                        </a>
                        <a href="#profil"
                            class="bg-slate-800/80 hover:bg-slate-800 text-slate-200 hover:text-white font-semibold px-8 py-3.5 rounded-xl border border-slate-700 backdrop-blur-md transition duration-200 text-center flex items-center justify-center gap-2">
                            Pelajari Selengkapnya
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RUNNING TEXT / INFO BANNER --}}
    <div class="bg-slate-900 py-4 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-xl bg-emerald-950/80 border border-emerald-500/30 backdrop-blur-md px-4 py-3 flex items-center shadow-lg">
                <div class="flex items-center gap-2 bg-emerald-600 text-white text-xs font-bold px-3 py-1 rounded-lg shrink-0 mr-3 shadow-sm">
                    <i class="fa-solid fa-bullhorn"></i> INFO
                </div>
                <div class="overflow-hidden whitespace-nowrap w-full">
                    <marquee scrollamount="5" class="text-xs sm:text-sm text-emerald-100 font-medium tracking-wide">
                        Selamat datang di portal resmi Pondok Pesantren Babul Hasanah. Pendaftaran Santri Baru Tahun Ajaran Baru telah dibuka, mari bergabung bersama kami untuk mencetak generasi unggul!
                    </marquee>
                </div>
            </div>
        </div>
    </div>

    {{-- STATISTIK QUICK CARDS --}}
    <div class="bg-white py-12 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <a href="{{ url('/santri') }}" class="p-6 rounded-2xl bg-slate-50 border border-slate-100 shadow-sm hover:border-emerald-500 hover:shadow-md hover:-translate-y-1 transition duration-200 block group">
                    <h3 class="text-3xl font-extrabold text-emerald-700 group-hover:scale-105 transition transform">500+</h3>
                    <p class="text-sm text-slate-600 mt-1 font-medium">Santri Aktif</p>
                </a>

                <a href="{{ url('/profil/struktur-organisasi') }}" class="p-6 rounded-2xl bg-slate-50 border border-slate-100 shadow-sm hover:border-emerald-500 hover:shadow-md hover:-translate-y-1 transition duration-200 block group">
                    <h3 class="text-3xl font-extrabold text-emerald-700 group-hover:scale-105 transition transform">30+</h3>
                    <p class="text-sm text-slate-600 mt-1 font-medium">Ustadz / Ustadzah</p>
                </a>

                <a href="{{ url('/program/kategori-1') }}" class="p-6 rounded-2xl bg-slate-50 border border-slate-100 shadow-sm hover:border-emerald-500 hover:shadow-md hover:-translate-y-1 transition duration-200 block group">
                    <h3 class="text-3xl font-extrabold text-emerald-700 group-hover:scale-105 transition transform">2</h3>
                    <p class="text-sm text-slate-600 mt-1 font-medium">Program Unggulan</p>
                </a>

                <a href="{{ url('/ekstrakurikuler') }}" class="p-6 rounded-2xl bg-slate-50 border border-slate-100 shadow-sm hover:border-emerald-500 hover:shadow-md hover:-translate-y-1 transition duration-200 block group">
                    <h3 class="text-3xl font-extrabold text-emerald-700 group-hover:scale-105 transition transform">15+</h3>
                    <p class="text-sm text-slate-600 mt-1 font-medium">Ekstrakurikuler</p>
                </a>
            </div>
        </div>
    </div>

    {{-- BAGIAN SAMBUTAN PIMPINAN --}}
    <div id="profil" class="py-16 lg:py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-10 lg:gap-14">
                
                {{-- Foto Pimpinan --}}
                <div class="w-full lg:w-1/2 flex-1">
                    <div class="relative w-full">
                        <div class="absolute inset-0 bg-emerald-500/10 rounded-3xl transform rotate-2 scale-102"></div>
                        <div class="relative bg-white p-3 sm:p-4 rounded-3xl shadow-xl border border-slate-200/60 overflow-hidden w-full aspect-square">
                            <img src="{{ asset('assets/img/banner/pimpinan.jpg') }}" 
                                 alt="KH. Mardin Hasibuan, Asshiddiqy, M.MPd." 
                                 class="w-full h-full object-cover object-top rounded-2xl">
                        </div>
                    </div>
                </div>

                {{-- Teks Sambutan --}}
                <div class="w-full lg:w-1/2 flex-1 space-y-6 text-center lg:text-left">
                    <div class="space-y-2">
                        <span class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Mengenal Lebih Dekat</span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Sambutan Pimpinan Pondok</h2>
                        <div class="w-16 h-1 bg-emerald-500 mx-auto lg:mx-0 rounded-full"></div>
                    </div>

                    <div>
                        <h3 class="text-xl sm:text-2xl font-bold text-emerald-800">KH. Mardin Hasibuan, Asshiddiqy, M.MPd.</h3>
                        <p class="text-xs sm:text-sm text-slate-500 font-medium">Pimpinan Pondok Pesantren Babul Hasanah</p>
                    </div>

                    <div class="space-y-4 text-slate-600 text-sm sm:text-base leading-relaxed">
                        <p>
                            Pondok Pesantren Babul Hasanah hadir di Kabupaten Padang Lawas untuk memberikan fasilitas pendidikan agama yang kokoh serta penguasaan ilmu-ilmu keislaman klasik (kitab kuning) yang berpadu dengan sains dan teknologi modern.
                        </p>
                        <p>
                            Sebagai institusi pendidikan Islam yang berdedikasi, kami berkomitmen mendidik generasi santri yang tidak hanya mendalami ilmu agama, tetapi juga berakhlak mulia, berwawasan global, serta siap menghadapi tantangan zaman.
                        </p>
                    </div>

                    <div class="pt-2 flex justify-center lg:justify-start">
                        <a href="{{ url('/profil/sambutan') }}" 
                           class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-3.5 rounded-xl shadow-md hover:shadow-lg shadow-emerald-900/10 transition duration-200 text-sm">
                            <span>Baca Selengkapnya</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- PROGRAM UNGGULAN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <style>
        .custom-swiper-pagination {
            position: relative !important;
            bottom: 0 !important;
            margin-top: 2rem !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
        }
    </style>

    <div class="py-20 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <h2 class="text-xs font-semibold text-emerald-600 uppercase tracking-widest">Program Unggulan</h2>
                <h3 class="text-3xl sm:text-4xl font-bold text-slate-900">Pusat Pendidikan Islam Berwawasan Global</h3>
                <p class="text-slate-600">
                    Pondok Pesantren Babul Hasanah hadir di Kabupaten Padang Lawas untuk memberikan fasilitas pendidikan
                    agama yang kokoh serta penguasaan sains dan teknologi.
                </p>
            </div>

            <div class="swiper mySwiper !px-2">
                <div class="swiper-wrapper">

                    <div class="swiper-slide !h-auto">
                        <div class="bg-white rounded-3xl shadow-md border border-slate-200/80 overflow-hidden flex flex-col h-full hover:shadow-xl transition-all duration-300">
                            <div class="bg-cover bg-center text-white p-8 pb-12 text-center relative" style="background-image: url('https://images.unsplash.com/photo-1609599006353-e629aaabfeae?q=80&w=500&auto=format&fit=crop');">
                                <div class="absolute inset-0 bg-slate-900/50"></div>
                                <div class="relative z-10">
                                    <div class="text-4xl mb-3"><i class="fa-solid fa-book-quran"></i></div>
                                    <h4 class="text-lg font-bold uppercase tracking-wider">Tahfidz</h4>
                                </div>
                                <svg class="absolute bottom-0 left-0 right-0 w-full text-white fill-current z-10" viewBox="0 0 1440 120">
                                    <path d="M0,32L120,42.7C240,53,480,75,720,75C960,75,1200,53,1320,42.7L1440,32L1440,120L1320,120C480,120,240,120,120,120L0,120Z"></path>
                                </svg>
                            </div>
                            <div class="p-6 pt-2 flex-1 flex flex-col justify-between text-center">
                                <div>
                                    <h5 class="text-xl font-bold text-slate-900 mb-3">Tahfidzul Qur'an</h5>
                                    <p class="text-slate-600 text-sm leading-relaxed mb-6">
                                        Program khusus hafalan Al-Qur'an dengan bimbingan intensif dan metode talaqqi yang teruji menghasilkan hafizh dan hafizhah handal.
                                    </p>
                                </div>
                                <a href="#" class="block w-full bg-slate-900 text-white font-semibold py-3 rounded-xl hover:bg-emerald-600 transition-colors">DAFTAR</a>
                            </div>
                        </div>
                    </div>

                    <div class="swiper-slide !h-auto">
                        <div class="bg-white rounded-3xl shadow-md border border-slate-200/80 overflow-hidden flex flex-col h-full hover:shadow-xl transition-all duration-300">
                            <div class="bg-cover bg-center text-white p-8 pb-12 text-center relative" style="background-image: url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=500&auto=format&fit=crop');">
                                <div class="absolute inset-0 bg-slate-900/50"></div>
                                <div class="relative z-10">
                                    <div class="text-4xl mb-3"><i class="fa-solid fa-school"></i></div>
                                    <h4 class="text-lg font-bold uppercase tracking-wider">Formal</h4>
                                </div>
                                <svg class="absolute bottom-0 left-0 right-0 w-full text-white fill-current z-10" viewBox="0 0 1440 120">
                                    <path d="M0,32L120,42.7C240,53,480,75,720,75C960,75,1200,53,1320,42.7L1440,32L1440,120L1320,120C480,120,240,120,120,120L0,120Z"></path>
                                </svg>
                            </div>
                            <div class="p-6 pt-2 flex-1 flex flex-col justify-between text-center">
                                <div>
                                    <h5 class="text-xl font-bold text-slate-900 mb-3">Kulliyatul Mu'allimin</h5>
                                    <p class="text-slate-600 text-sm leading-relaxed mb-6">
                                        Pendidikan formal tingkat MTs dan MA dengan kurikulum memadukan DEPAG, pesantren salafiyah, serta penguasaan bahasa asing.
                                    </p>
                                </div>
                                <a href="#" class="block w-full bg-slate-900 text-white font-semibold py-3 rounded-xl hover:bg-amber-600 transition-colors">DAFTAR</a>
                            </div>
                        </div>
                    </div>

                    <div class="swiper-slide !h-auto">
                        <div class="bg-white rounded-3xl shadow-md border border-slate-200/80 overflow-hidden flex flex-col h-full hover:shadow-xl transition-all duration-300">
                            <div class="bg-cover bg-center text-white p-8 pb-12 text-center relative" style="background-image: url('https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=500&auto=format&fit=crop');">
                                <div class="absolute inset-0 bg-slate-900/50"></div>
                                <div class="relative z-10">
                                    <div class="text-4xl mb-3"><i class="fa-solid fa-laptop-code"></i></div>
                                    <h4 class="text-lg font-bold uppercase tracking-wider">Teknologi</h4>
                                </div>
                                <svg class="absolute bottom-0 left-0 right-0 w-full text-white fill-current z-10" viewBox="0 0 1440 120">
                                    <path d="M0,32L120,42.7C240,53,480,75,720,75C960,75,1200,53,1320,42.7L1440,32L1440,120L1320,120C480,120,240,120,120,120L0,120Z"></path>
                                </svg>
                            </div>
                            <div class="p-6 pt-2 flex-1 flex flex-col justify-between text-center">
                                <div>
                                    <h5 class="text-xl font-bold text-slate-900 mb-3">Kemandirian & Teknologi</h5>
                                    <p class="text-slate-600 text-sm leading-relaxed mb-6">
                                        Membekali santri dengan kecakapan hidup (life skills), kewirausahaan, serta literasi digital guna menghadapi tantangan zaman modern.
                                    </p>
                                </div>
                                <a href="#" class="block w-full bg-slate-900 text-white font-semibold py-3 rounded-xl hover:bg-indigo-600 transition-colors">DAFTAR</a>
                            </div>
                        </div>
                    </div>

                    <div class="swiper-slide !h-auto">
                        <div class="bg-white rounded-3xl shadow-md border border-slate-200/80 overflow-hidden flex flex-col h-full hover:shadow-xl transition-all duration-300">
                            <div class="bg-cover bg-center text-white p-8 pb-12 text-center relative" style="background-image: url('https://images.unsplash.com/photo-1532012197267-da84d127e765?q=80&w=500&auto=format&fit=crop');">
                                <div class="absolute inset-0 bg-slate-900/50"></div>
                                <div class="relative z-10">
                                    <div class="text-4xl mb-3"><i class="fa-solid fa-book-open"></i></div>
                                    <h4 class="text-lg font-bold uppercase tracking-wider">Salafiyah</h4>
                                </div>
                                <svg class="absolute bottom-0 left-0 right-0 w-full text-white fill-current z-10" viewBox="0 0 1440 120">
                                    <path d="M0,32L120,42.7C240,53,480,75,720,75C960,75,1200,53,1320,42.7L1440,32L1440,120L1320,120C480,120,240,120,120,120L0,120Z"></path>
                                </svg>
                            </div>
                            <div class="p-6 pt-2 flex-1 flex flex-col justify-between text-center">
                                <div>
                                    <h5 class="text-xl font-bold text-slate-900 mb-3">Kajian Kitab Kuning</h5>
                                    <p class="text-slate-600 text-sm leading-relaxed mb-6">
                                        Pendalaman ilmu agama klasik bermetode muhafazhah dan pendalaman kaidah Bahasa Arab Nahwu & Shorof secara terstruktur.
                                    </p>
                                </div>
                                <a href="#" class="block w-full bg-slate-900 text-white font-semibold py-3 rounded-xl hover:bg-sky-600 transition-colors">DAFTAR</a>
                            </div>
                        </div>
                    </div>

                    <div class="swiper-slide !h-auto">
                        <div class="bg-white rounded-3xl shadow-md border border-slate-200/80 overflow-hidden flex flex-col h-full hover:shadow-xl transition-all duration-300">
                            <div class="bg-cover bg-center text-white p-8 pb-12 text-center relative" style="background-image: url('https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?q=80&w=500&auto=format&fit=crop');">
                                <div class="absolute inset-0 bg-slate-900/50"></div>
                                <div class="relative z-10">
                                    <div class="text-4xl mb-3"><i class="fa-solid fa-language"></i></div>
                                    <h4 class="text-lg font-bold uppercase tracking-wider">Bahasa</h4>
                                </div>
                                <svg class="absolute bottom-0 left-0 right-0 w-full text-white fill-current z-10" viewBox="0 0 1440 120">
                                    <path d="M0,32L120,42.7C240,53,480,75,720,75C960,75,1200,53,1320,42.7L1440,32L1440,120L1320,120C480,120,240,120,120,120L0,120Z"></path>
                                </svg>
                            </div>
                            <div class="p-6 pt-2 flex-1 flex flex-col justify-between text-center">
                                <div>
                                    <h5 class="text-xl font-bold text-slate-900 mb-3">Bahasa Asing Intensif</h5>
                                    <p class="text-slate-600 text-sm leading-relaxed mb-6">
                                        Pembiasaan percakapan harian (Arabic & English Club) guna membentuk santri yang komunikatif dan berwawasan global.
                                    </p>
                                </div>
                                <a href="#" class="block w-full bg-slate-900 text-white font-semibold py-3 rounded-xl hover:bg-rose-600 transition-colors">DAFTAR</a>
                            </div>
                        </div>
                    </div>

                    <div class="swiper-slide !h-auto">
                        <div class="bg-white rounded-3xl shadow-md border border-slate-200/80 overflow-hidden flex flex-col h-full hover:shadow-xl transition-all duration-300">
                            <div class="bg-cover bg-center text-white p-8 pb-12 text-center relative" style="background-image: url('https://images.unsplash.com/photo-1511632765486-a01980e01a18?q=80&w=500&auto=format&fit=crop');">
                                <div class="absolute inset-0 bg-slate-900/50"></div>
                                <div class="relative z-10">
                                    <div class="text-4xl mb-3"><i class="fa-solid fa-award"></i></div>
                                    <h4 class="text-lg font-bold uppercase tracking-wider">Ekstra</h4>
                                </div>
                                <svg class="absolute bottom-0 left-0 right-0 w-full text-white fill-current z-10" viewBox="0 0 1440 120">
                                    <path d="M0,32L120,42.7C240,53,480,75,720,75C960,75,1200,53,1320,42.7L1440,32L1440,120L1320,120C480,120,240,120,120,120L0,120Z"></path>
                                </svg>
                            </div>
                            <div class="p-6 pt-2 flex-1 flex flex-col justify-between text-center">
                                <div>
                                    <h5 class="text-xl font-bold text-slate-900 mb-3">Minat & Bakat Santri</h5>
                                    <p class="text-slate-600 text-sm leading-relaxed mb-6">
                                        Wadah pengembangan kepemimpinan melalui pramuka, seni bela diri, kaligrafi, pidato 3 bahasa, dan keolahragaan.
                                    </p>
                                </div>
                                <a href="#" class="block w-full bg-slate-900 text-white font-semibold py-3 rounded-xl hover:bg-violet-600 transition-colors">DAFTAR</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="custom-swiper-pagination"></div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            new Swiper(".mySwiper", {
                slidesPerView: 1,
                spaceBetween: 24,
                loop: true,
                autoplay: {
                    delay: 2000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".custom-swiper-pagination",
                    clickable: true,
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 24,
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 32,
                    },
                },
            });
        });
    </script>

    {{-- CALL TO ACTION --}}
    <div class="py-20 bg-emerald-900 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#d1fae5_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 space-y-6">
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Mari Bergabung Bersama Keluarga Besar Babul Hasanah</h2>
            <p class="text-emerald-100 max-w-2xl mx-auto text-base sm:text-lg">
                Pendaftaran santri baru tahun ajaran baru telah dibuka. Daftarkan putra-putri Anda untuk mendapatkan
                pendidikan islami yang berkualitas.
            </p>
            <div class="pt-4 flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('pendaftaran.index') }}"
                    class="bg-white hover:bg-emerald-50 text-emerald-900 font-bold px-8 py-4 rounded-xl shadow-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-file-lines"></i> Formulir Pendaftaran Online
                </a>
                <a href="https://wa.me/" target="_blank"
                    class="bg-emerald-800 hover:bg-emerald-700 text-white border border-emerald-600 font-semibold px-8 py-4 rounded-xl transition duration-200 flex items-center justify-center gap-2">
                    <i class="fa-brands fa-whatsapp text-lg"></i> Hubungi Panitia (WhatsApp)
                </a>
            </div>
        </div>
    </div>

    {{-- BERITA TERBARU --}}
    <div class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12">
                <div>
                    <h2 class="text-xs font-semibold text-emerald-600 uppercase tracking-widest">Informasi & Kegiatan</h2>
                    <h3 class="text-3xl font-bold text-slate-900 mt-1">Berita Terbaru Pesantren</h3>
                </div>
                <a href="{{ route('berita.index') }}"
                    class="mt-4 md:mt-0 text-emerald-600 hover:text-emerald-700 font-semibold text-sm flex items-center gap-1">
                    Lihat Semua Berita <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-slate-50 border border-slate-200/60 rounded-2xl overflow-hidden shadow-sm flex flex-col">
                    <div class="h-48 bg-slate-200 flex items-center justify-center text-slate-400 font-medium">
                        [ Gambar Kegiatan ]
                    </div>
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-semibold text-emerald-600 uppercase">Pengumuman</span>
                            <h4 class="text-lg font-bold text-slate-900 mt-2 mb-3">Pelaksanaan Ujian Semester Ganjil Santri Babul Hasanah</h4>
                            <p class="text-slate-600 text-sm line-clamp-3">
                                Ujian akhir semester ganjil tahun ajaran ini resmi dibuka oleh pimpinan pondok dengan sistem terpadu...
                            </p>
                        </div>
                        <div class="pt-4 mt-4 border-t border-slate-200/60 flex items-center justify-between text-xs text-slate-500">
                            <span><i class="fa-regular fa-calendar mr-1"></i> 24 Agustus 2026</span>
                            <a href="#" class="text-emerald-600 font-semibold hover:underline">Baca Selengkapnya</a>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 border border-slate-200/60 rounded-2xl overflow-hidden shadow-sm flex flex-col">
                    <div class="h-48 bg-slate-200 flex items-center justify-center text-slate-400 font-medium">
                        [ Gambar Kegiatan ]
                    </div>
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-semibold text-emerald-600 uppercase">Prestasi</span>
                            <h4 class="text-lg font-bold text-slate-900 mt-2 mb-3">Santri Babul Hasanah Juara 1 Musabaqah Tilawatil Qur'an</h4>
                            <p class="text-slate-600 text-sm line-clamp-3">
                                Kebanggaan kembali hadir setelah perwakilan santri berhasil menyabet juara pertama tingkat Kabupaten Padang Lawas...
                            </p>
                        </div>
                        <div class="pt-4 mt-4 border-t border-slate-200/60 flex items-center justify-between text-xs text-slate-500">
                            <span><i class="fa-regular fa-calendar mr-1"></i> 20 Agustus 2026</span>
                            <a href="#" class="text-emerald-600 font-semibold hover:underline">Baca Selengkapnya</a>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 border border-slate-200/60 rounded-2xl overflow-hidden shadow-sm flex flex-col">
                    <div class="h-48 bg-slate-200 flex items-center justify-center text-slate-400 font-medium">
                        [ Gambar Kegiatan ]
                    </div>
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-semibold text-emerald-600 uppercase">Kegiatan Pondok</span>
                            <h4 class="text-lg font-bold text-slate-900 mt-2 mb-3">Kajian Kitab Kuning Bulanan Bersama Ulama Tamu</h4>
                            <p class="text-slate-600 text-sm line-clamp-3">
                                Kegiatan rutin pengajian kitab kuning penunjang pemahaman fiqih santri dilaksanakan di aula utama pesantren...
                            </p>
                        </div>
                        <div class="pt-4 mt-4 border-t border-slate-200/60 flex items-center justify-between text-xs text-slate-500">
                            <span><i class="fa-regular fa-calendar mr-1"></i> 15 Agustus 2026</span>
                            <a href="#" class="text-emerald-600 font-semibold hover:underline">Baca Selengkapnya</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection