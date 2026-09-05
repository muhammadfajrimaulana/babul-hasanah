<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pondok Pesantren Babul Hasanah')</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo/logo.png') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        :root {
            /* Palette Colors */
            --color-white: #fefefe;
            --color-pharmaceutical-green: #1b7b32;
            --color-kashmir: #698d6e;
            --color-misty-marsh: #cdd9cf;
            --color-copper-patina: #9cb49d;
            --color-woodgrain: #916438;
            --color-queen-of-the-night: #44467e;
            --color-bourbon-peach: #ee8a3e;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased flex flex-col min-h-screen">

    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/60 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">

                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 rounded-xl overflow-hidden bg-emerald-600/10 flex items-center justify-center shadow-xs">
                        <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Logo Babul Hasanah"
                            class="w-full h-full object-contain">
                    </div>
                    <div>
                        <span class="text-xs text-emerald-700 font-medium tracking-wide">Pondok Pesantren</span>
                        <a href="{{ url('/') }}"
                            class="text-lg font-extrabold text-slate-900 tracking-tight block leading-none">
                            Babul Hasanah
                        </a>
                    </div>
                </div>

                <nav class="hidden md:flex items-center gap-8 font-medium text-sm text-slate-600">
                    <a href="{{ url('/') }}" class="hover:text-emerald-600 transition">Beranda</a>
                    
                    <!-- Dropdown Profil -->
                    <div class="relative" x-data="{ openProfil: false }">
                        <button @click="openProfil = !openProfil" @click.outside="openProfil = false" class="flex items-center gap-1.5 hover:text-emerald-600 transition py-2 focus:outline-none group">
                            <span>Profil</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-emerald-600 transition-transform duration-200" :class="{ 'rotate-180': openProfil }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="openProfil"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-3 scale-95"
                             class="absolute left-0 top-full w-50 bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 p-2 z-50"
                             style="display: none;">
                            <div class="px-3 py-2 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 mb-1">
                                Tentang Pesantren
                            </div>
                            <a href="{{ url('/profil/sejarah') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50/70 hover:text-emerald-600 transition group/item">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs group-hover/item:bg-emerald-600 group-hover/item:text-white transition shrink-0">
                                    <i class="fa-solid fa-landmark"></i>
                                </div>
                                <span class="font-semibold text-slate-700 group-hover/item:text-emerald-600">Sejarah</span>
                            </a>
                            <a href="{{ url('/profil/visi-misi') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50/70 hover:text-emerald-600 transition group/item">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs group-hover/item:bg-emerald-600 group-hover/item:text-white transition shrink-0">
                                    <i class="fa-solid fa-bullseye"></i>
                                </div>
                                <span class="font-semibold text-slate-700 group-hover/item:text-emerald-600">Visi & Misi</span>
                            </a>
                            <a href="{{ url('/profil/struktur-organisasi') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50/70 hover:text-emerald-600 transition group/item">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs group-hover/item:bg-emerald-600 group-hover/item:text-white transition shrink-0">
                                    <i class="fa-solid fa-sitemap"></i>
                                </div>
                                <span class="font-semibold text-slate-700 group-hover/item:text-emerald-600">Struktur Organisasi</span>
                            </a>
                            <a href="{{ url('/profil/pembelajaran') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50/70 hover:text-emerald-600 transition group/item">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs group-hover/item:bg-emerald-600 group-hover/item:text-white transition shrink-0">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                </div>
                                <span class="font-semibold text-slate-700 group-hover/item:text-emerald-600">Pembelajaran</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Dropdown Program -->
                    <div class="relative" x-data="{ openProgram: false }">
                        <button @click="openProgram = !openProgram" @click.outside="openProgram = false" class="flex items-center gap-1.5 hover:text-emerald-600 transition py-2 focus:outline-none group">
                            <span>Program</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-emerald-600 transition-transform duration-200" :class="{ 'rotate-180': openProgram }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="openProgram"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-3 scale-95"
                             class="absolute left-0 top-full w-50 bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 p-2 z-50"
                             style="display: none;">
                            <div class="px-3 py-2 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 mb-1">
                                Pilihan Program
                            </div>
                            <a href="{{ url('/program/kategori-1') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50/70 hover:text-emerald-600 transition group/item">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs group-hover/item:bg-emerald-600 group-hover/item:text-white transition shrink-0">
                                    <i class="fa-solid fa-book-quran"></i>
                                </div>
                                <span class="font-semibold text-slate-700 group-hover/item:text-emerald-600">Program 1</span>
                            </a>
                            <a href="{{ url('/program/kategori-2') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50/70 hover:text-emerald-600 transition group/item">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs group-hover/item:bg-emerald-600 group-hover/item:text-white transition shrink-0">
                                    <i class="fa-solid fa-award"></i>
                                </div>
                                <span class="font-semibold text-slate-700 group-hover/item:text-emerald-600">Program 2</span>
                            </a>
                            <a href="{{ url('/program/kategori-3') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50/70 hover:text-emerald-600 transition group/item">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs group-hover/item:bg-emerald-600 group-hover/item:text-white transition shrink-0">
                                    <i class="fa-solid fa-star"></i>
                                </div>
                                <span class="font-semibold text-slate-700 group-hover/item:text-emerald-600">Program 3</span>
                            </a>
                        </div>
                    </div>

                    <a href="{{ url('/berita') }}" class="hover:text-emerald-600 transition">Berita</a>
                    <a href="{{ url('/galeri') }}" class="hover:text-emerald-600 transition">Galeri</a>
                    <a href="{{ url('/') }}" class="hover:text-emerald-600 transition">Prestasi</a>
                    <a href="{{ url('/') }}" class="hover:text-emerald-600 transition">Kontak</a>
                </nav>

                <div class="hidden md:flex items-center gap-3">
                    <a href="{{ route('login') }}"
                        class="text-slate-700 hover:text-emerald-600 font-semibold text-sm px-4 py-2.5 transition">
                        Masuk
                    </a>
                    <a href="{{ route('pendaftaran.index') }}"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm px-5 py-2.5 rounded-xl shadow-sm transition">
                        Daftar Online
                    </a>
                </div>

                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-button"
                        class="text-slate-700 hover:text-emerald-600 focus:outline-none p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-white border-b border-slate-200 px-4 pt-2 pb-6 space-y-2">
            <a href="{{ url('/') }}"
                class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-emerald-50 hover:text-emerald-600">Beranda</a>
            <a href="{{ url('/profil/sejarah') }}"
                class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-emerald-50 hover:text-emerald-600">Profil</a>
            <a href="{{ url('/program/kategori-1') }}"
                class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-emerald-50 hover:text-emerald-600">Program</a>
            <a href="{{ url('/berita') }}"
                class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-emerald-50 hover:text-emerald-600">Berita</a>
            <a href="{{ url('/galeri') }}"
                class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-emerald-50 hover:text-emerald-600">Galeri</a>

            <div class="pt-4 border-t border-slate-100 flex flex-col gap-2">
                <a href="{{ route('login') }}"
                    class="w-full text-center text-slate-700 bg-slate-100 font-semibold py-3 rounded-xl hover:bg-slate-200 transition">Masuk
                    Sistem</a>
                <a href="{{ route('pendaftaran.index') }}"
                    class="w-full text-center bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-xl shadow-sm transition">Daftar
                    Online</a>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-slate-900 text-slate-300 pt-16 pb-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">

                <div class="space-y-4 md:col-span-1">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 flex items-center justify-center">
                            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Logo Babul Hasanah"
                                class="w-full h-full object-contain">
                        </div>
                        <div>
                            <span class="text-xs text-emerald-700 font-medium tracking-wide">Pondok Pesantren</span>
                            <a href="{{ url('/') }}"
                                class="text-lg font-extrabold text-white tracking-tight block leading-none">
                                Babul Hasanah
                            </a>
                        </div>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Pusat pendidikan islam terpadu yang berdedikasi membangun generasi santri unggul, cerdas,
                        berakhlak mulia, dan siap menghadapi tantangan zaman.
                    </p>
                </div>

                <div class="space-y-3">
                    <h4 class="text-white font-semibold text-base">Lokasi Pesantren</h4>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Jl. Lintas Pinarik-Papaso Km. 14<br>
                        Desa Manggis, Kec. Batang Lubu Sutam<br>
                        Kabupaten Padang Lawas, Sumatera Utara, 22763
                    </p>
                </div>

                <div class="space-y-3">
                    <h4 class="text-white font-semibold text-base">Tautan Cepat</h4>
                    <ul class="space-y-2 text-sm text-slate-400">
                        <li><a href="{{ url('/profil/sejarah') }}" class="hover:text-emerald-400 transition">Tentang
                                Pesantren</a></li>
                        <li><a href="{{ url('/program/kategori-1') }}" class="hover:text-emerald-400 transition">Program
                                Pesantren</a></li>
                        <li><a href="{{ route('pendaftaran.index') }}"
                                class="hover:text-emerald-400 transition">Pendaftaran Santri Baru</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-emerald-400 transition">Portal Wali
                                Santri</a></li>
                    </ul>
                </div>

                <div class="space-y-3">
                    <h4 class="text-white font-semibold text-base">Layanan & Informasi</h4>
                    <p class="text-sm text-slate-400">
                        Email: info@babulhasanah.sch.id<br>
                        Telepon/WA: 0812-3456-7890
                    </p>
                    <div class="pt-2 flex gap-3 text-emerald-400">
                        <span class="text-xs bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">Instagram</span>
                        <span class="text-xs bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">YouTube</span>
                        <span class="text-xs bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">Facebook</span>
                    </div>
                </div>

            </div>

            <div
                class="pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} Pondok Pesantren Babul Hasanah. All rights reserved.</p>
                <p class="mt-2 sm:mt-0">Developed with Laravel & Tailwind CSS</p>
            </div>
        </div>
    </footer>

    <script>
        const btn = document.getElementById('mobile-menu-button');
        const menu = document.getElementById('mobile-menu');

        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    </script>
</body>

</html>