@extends('layouts.app')

@section('title', 'Profil Pondok Pesantren Babul Hasanah')

@section('content')

    @php
        // $konten disiapkan di Public\ProfilController@index.
        $kv = fn (string $key, string $default = '') => $konten[$key] ?? $default;
        $foto = $kv('sejarah_foto');
        $fotoUrl = $foto ? asset('storage/'.preg_replace('#^storage/#', '', $foto)) : null;
    @endphp

    <!-- HERO SECTION PROFIL -->
    <section
        class="relative bg-gradient-to-br from-emerald-900 via-emerald-800 to-slate-900 text-white py-24 lg:py-32 overflow-hidden">
        <div
            class="absolute inset-0 opacity-10 bg-[radial-gradient(#d1fae5_1px,transparent_1px)] [background-size:16px_16px]">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-6">
            <span
                class="inline-block bg-emerald-700/60 text-emerald-200 text-xs font-semibold px-3.5 py-1.5 rounded-full border border-emerald-500/30 uppercase tracking-wider">
                <i class="fa-solid fa-sparkles mr-1"></i> Tentang Kami
            </span>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight">
                {!! $kv('profil_hero_title', 'Profil & Visi Misi Pondok Pesantren') !!}
            </h1>
            <p class="text-lg text-slate-300 max-w-2xl mx-auto leading-relaxed">
                {{ $kv('profil_hero_desc') }}
            </p>
        </div>
    </section>

    <!-- SEJARAH SINGKAT -->
    <section class="py-14 sm:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <h2 class="text-xs font-semibold text-emerald-600 uppercase tracking-widest">Sejarah Berdirinya</h2>
                    <h3 class="text-3xl font-bold text-slate-900 leading-tight">{{ $kv('sejarah_judul') }}</h3>
                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                        {{ $kv('sejarah_p1') }}
                    </p>
                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                        {{ $kv('sejarah_p2') }}
                    </p>
                </div>
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/60 shadow-sm overflow-hidden">
                    @if ($fotoUrl)
                        <img src="{{ $fotoUrl }}" alt="Foto komplek dan gedung pesantren Babul Hasanah"
                            class="w-full h-80 object-cover rounded-xl">
                    @else
                        <div
                            class="h-80 bg-slate-200/80 rounded-xl flex items-center justify-center text-slate-500 font-medium text-sm">
                            [ Foto Komplek / Gedung Pesantren ]
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- VISI & MISI -->
    <section class="py-14 sm:py-20 bg-slate-50 border-y border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16 space-y-3">
                <h2 class="text-xs font-semibold text-emerald-600 uppercase tracking-widest">Landasan Utama</h2>
                <h3 class="text-3xl font-bold text-slate-900">Visi & Misi Pesantren</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Visi -->
                <div
                    class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200/60 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div
                            class="w-12 h-12 bg-emerald-100 text-emerald-700 rounded-xl flex items-center justify-center font-bold text-lg mb-6">
                            <i class="fa-solid fa-eye"></i>
                        </div>
                        <h4 class="text-2xl font-bold text-slate-900 mb-4">Visi</h4>
                        <p
                            class="text-slate-700 leading-relaxed italic bg-emerald-50/50 p-6 rounded-xl border border-emerald-100">
                            "{!! nl2br(e($kv('visi'))) !!}"
                        </p>
                    </div>
                </div>

                <!-- Misi -->
                <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200/60 hover:shadow-md transition">
                    <div
                        class="w-12 h-12 bg-emerald-100 text-emerald-700 rounded-xl flex items-center justify-center font-bold text-lg mb-6">
                        <i class="fa-solid fa-bullseye"></i>
                    </div>
                    <h4 class="text-2xl font-bold text-slate-900 mb-4">Misi</h4>
                    <div class="space-y-3 text-slate-600 text-sm leading-relaxed">
                        {!! nl2br(e($kv('misi'))) !!}
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
