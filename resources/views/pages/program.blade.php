@extends('layouts.app')

@section('title', 'Program Pondok Pesantren Babul Hasanah')

@section('content')
    <!-- SECTION PROGRAM PENDIDIKAN: Modern Bento Style -->
    <div class="py-20 lg:py-28 bg-slate-50 text-slate-900 min-h-screen relative overflow-hidden">

        <!-- Background Dekorasi Halus -->
        <div
            class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-96 bg-gradient-to-b from-emerald-100/50 to-transparent pointer-events-none">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

            <!-- Section Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
                <div class="space-y-4 max-w-2xl">
                    <span
                        class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-800 text-xs font-bold px-4 py-1.5 rounded-full uppercase tracking-wider">
                        <i class="fa-solid fa-graduation-cap text-emerald-600"></i> Kurikulum Unggulan
                    </span>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-slate-900">
                        Pilihan Jenjang & <span class="text-emerald-600">Program Studi</span>
                    </h1>
                </div>
                <p class="text-slate-600 text-sm sm:text-base max-w-md leading-relaxed">
                    Dirancang secara sistematis untuk mencetak generasi yang cerdas secara intelektual dan matang dalam
                    penguasaan ilmu agama.
                </p>
            </div>

            <!-- Bento Grid Container -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- Card Program 1: MTs (Span 6 Kolom) -->
                <div
                    class="lg:col-span-6 bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/80 shadow-xl shadow-slate-200/50 flex flex-col justify-between hover:border-emerald-500/50 transition duration-300 group">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div
                                class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl font-bold group-hover:bg-emerald-600 group-hover:text-white transition duration-300">
                                <i class="fa-solid fa-school"></i>
                            </div>
                            <span
                                class="text-xs font-bold bg-slate-100 text-slate-700 px-3 py-1 rounded-full uppercase tracking-wider">Tingkat
                                Pertama</span>
                        </div>

                        <div>
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mb-3">Madrasah Tsanawiyah (MTs)
                            </h2>
                            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                                Fokus pada penanaman fondasi ilmu agama dasar, pembiasaan ibadah yaumiyah, serta pengenalan
                                dasar nahwu-shorof bagi santri pemula.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            <div
                                class="flex items-center gap-2.5 text-xs font-medium text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <i class="fa-solid fa-check text-emerald-600"></i> Tahfidz Juz Amma
                            </div>
                            <div
                                class="flex items-center gap-2.5 text-xs font-medium text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <i class="fa-solid fa-check text-emerald-600"></i> Kitab Dasar (Safinah)
                            </div>
                        </div>
                    </div>

                    <div class="pt-8">
                        <a href="{{ route('pendaftaran.index') }}"
                            class="inline-flex items-center justify-center w-full bg-slate-900 hover:bg-emerald-600 text-white font-semibold py-4 rounded-2xl transition duration-300 text-sm gap-2 shadow-lg shadow-slate-900/10">
                            <span>Daftar MTs Sekarang</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Card Program 2: MA (Span 6 Kolom) -->
                <div
                    class="lg:col-span-6 bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/80 shadow-xl shadow-slate-200/50 flex flex-col justify-between hover:border-emerald-500/50 transition duration-300 group">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div
                                class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl font-bold group-hover:bg-emerald-600 group-hover:text-white transition duration-300">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </div>
                            <span
                                class="text-xs font-bold bg-slate-100 text-slate-700 px-3 py-1 rounded-full uppercase tracking-wider">Tingkat
                                Menengah Atas</span>
                        </div>

                        <div>
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mb-3">Madrasah Aliyah (MA)</h2>
                            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                                Fokus pada pendalaman kutub al-mu'tabarah (kitab kuning tingkat lanjut), pelatihan retorika
                                dakwah, serta bahasa aktif.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            <div
                                class="flex items-center gap-2.5 text-xs font-medium text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <i class="fa-solid fa-check text-emerald-600"></i> Kajian Kitab Lanjutan
                            </div>
                            <div
                                class="flex items-center gap-2.5 text-xs font-medium text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <i class="fa-solid fa-check text-emerald-600"></i> Praktik Muhadharah
                            </div>
                        </div>
                    </div>

                    <div class="pt-8">
                        <a href="{{ route('pendaftaran.index') }}"
                            class="inline-flex items-center justify-center w-full bg-slate-900 hover:bg-emerald-600 text-white font-semibold py-4 rounded-2xl transition duration-300 text-sm gap-2 shadow-lg shadow-slate-900/10">
                            <span>Daftar MA Sekarang</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Card Program 1: MTs (Span 6 Kolom) -->
                <div
                    class="lg:col-span-6 bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/80 shadow-xl shadow-slate-200/50 flex flex-col justify-between hover:border-emerald-500/50 transition duration-300 group">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div
                                class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl font-bold group-hover:bg-emerald-600 group-hover:text-white transition duration-300">
                                <i class="fa-solid fa-school"></i>
                            </div>
                            <span
                                class="text-xs font-bold bg-slate-100 text-slate-700 px-3 py-1 rounded-full uppercase tracking-wider">Tingkat
                                Pertama</span>
                        </div>

                        <div>
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mb-3">Madrasah Tsanawiyah (MTs)
                            </h2>
                            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                                Fokus pada penanaman fondasi ilmu agama dasar, pembiasaan ibadah yaumiyah, serta pengenalan
                                dasar nahwu-shorof bagi santri pemula.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            <div
                                class="flex items-center gap-2.5 text-xs font-medium text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <i class="fa-solid fa-check text-emerald-600"></i> Tahfidz Juz Amma
                            </div>
                            <div
                                class="flex items-center gap-2.5 text-xs font-medium text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <i class="fa-solid fa-check text-emerald-600"></i> Kitab Dasar (Safinah)
                            </div>
                        </div>
                    </div>

                    <div class="pt-8">
                        <a href="{{ route('pendaftaran.index') }}"
                            class="inline-flex items-center justify-center w-full bg-slate-900 hover:bg-emerald-600 text-white font-semibold py-4 rounded-2xl transition duration-300 text-sm gap-2 shadow-lg shadow-slate-900/10">
                            <span>Daftar MTs Sekarang</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Card Program 2: MA (Span 6 Kolom) -->
                <div
                    class="lg:col-span-6 bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/80 shadow-xl shadow-slate-200/50 flex flex-col justify-between hover:border-emerald-500/50 transition duration-300 group">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div
                                class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl font-bold group-hover:bg-emerald-600 group-hover:text-white transition duration-300">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </div>
                            <span
                                class="text-xs font-bold bg-slate-100 text-slate-700 px-3 py-1 rounded-full uppercase tracking-wider">Tingkat
                                Menengah Atas</span>
                        </div>

                        <div>
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mb-3">Madrasah Aliyah (MA)</h2>
                            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                                Fokus pada pendalaman kutub al-mu'tabarah (kitab kuning tingkat lanjut), pelatihan retorika
                                dakwah, serta bahasa aktif.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            <div
                                class="flex items-center gap-2.5 text-xs font-medium text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <i class="fa-solid fa-check text-emerald-600"></i> Kajian Kitab Lanjutan
                            </div>
                            <div
                                class="flex items-center gap-2.5 text-xs font-medium text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <i class="fa-solid fa-check text-emerald-600"></i> Praktik Muhadharah
                            </div>
                        </div>
                    </div>

                    <div class="pt-8">
                        <a href="{{ route('pendaftaran.index') }}"
                            class="inline-flex items-center justify-center w-full bg-slate-900 hover:bg-emerald-600 text-white font-semibold py-4 rounded-2xl transition duration-300 text-sm gap-2 shadow-lg shadow-slate-900/10">
                            <span>Daftar MA Sekarang</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection
