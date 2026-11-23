@extends('layouts.app')

@section('title', 'Fasilitas - Pondok Pesantren Babul Hasanah')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Section Fasilitas di Bawah Kata Alumni (seperti di halaman beranda) --}}
    <section class="py-20 bg-slate-50">
        <div class="container">
            <div class="eyebrow light">Fasilitas</div>
            <h2 class="text-2xl font-black text-slate-900 mb-6">Fasilitas Pesantren</h2>
            
            {{-- Gallery Fasilitas Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Load konten fasilitas --}}
                @php
                    $kontenFasilitas = \App\Models\Konten::where('key', 'fasilitas_photos')->first();
                    $photos = json_decode($kontenFasilitas->value ?? '[]', true);
                    $photoCount = count($photos);
                @endphp

                {{-- Fasilitas Pertama --}}
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 bg-emerald-100 text-emerald-700 rounded-xl flex items-center justify-center mb-3">
                        <i class="fa-solid fa-building-column"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Gedung Pembelajaran</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Gedung berkaca dua lantai dilengkapi kursi majelis, papan tulis putih, dan sistem audiovisual untuk pengajian kitab kuning dan pembelajaran modern.
                    </p>
                    @if ($photoCount > 0)
                        <div class="mt-3">
                            <span class="text-xs text-emerald-600 font-medium">Foto: {{ min($photoCount, 3) }} gambar</span>
                            <div class="grid grid-cols-3 gap-2 mt-2">
                                @foreach (array_slice($photos, 0, 3) as $idx => $photo)
                                    <img src="{{ asset('storage/' . $photo) }}" alt="Fasilitas Gedung" class="w-full h-24 object-cover rounded-lg">
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Fasilitas Kedua --}}
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 bg-emerald-100 text-emerald-700 rounded-xl flex items-center justify-center mb-3">
                        <i class="fa-solid fa-user-group"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Asrama Santri</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Asrama terpisah untuk santri putra dan putri dengan fasilitas tidur berkualitas, area studi, dan ruang bersama untuk kegiatan ekstrakurikuler.
                    </p>
                    @if ($photoCount > 1)
                        <div class="mt-3">
                            <span class="text-xs text-emerald-600 font-medium">Foto: {{ min($photoCount - 1, 3) }} gambar</span>
                            <div class="grid grid-cols-3 gap-2 mt-2">
                                @foreach (array_slice($photos, 1, 3) as $idx => $photo)
                                    <img src="{{ asset('storage/' . $photo) }}" alt="Fasilitas Asrama" class="w-full h-24 object-cover rounded-lg">
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Fasilitas Ketiga --}}
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 bg-emerald-100 text-emerald-700 rounded-xl flex items-center justify-center mb-3">
                        <i class="fa-solid fa-mosque"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Masjid</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Masjid utama dengan kubah indah, area pengasuhan, dan tempat berlaksana shalat jamaah lima waktu beserta jumat dan raya.
                    </p>
                    @if ($photoCount > 2)
                        <div class="mt-3">
                            <span class="text-xs text-emerald-600 font-medium">Foto: {{ min($photoCount - 2, 3) }} gambar</span>
                            <div class="grid grid-cols-3 gap-2 mt-2">
                                @foreach (array_slice($photos, 2, 3) as $idx => $photo)
                                    <img src="{{ asset('storage/' . $photo) }}" alt="Fasilitas Masjid" class="w-full h-24 object-cover rounded-lg">
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Fasilitas Keempat --}}
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 bg-emerald-100 text-emerald-700 rounded-xl flex items-center justify-center mb-3">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Perpustakaran</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Perpustakaran kaya koleksi kitab kuning, buku pelajaran, dan referensi ilmu pengetahuan yang tersedia untuk santri dan pengunjung.
                    </p>
                    @if ($photoCount > 3)
                        <div class="mt-3">
                            <span class="text-xs text-emerald-600 font-medium">Foto: {{ min($photoCount - 3, 3) }} gambar</span>
                            <div class="grid grid-cols-3 gap-2 mt-2">
                                @foreach (array_slice($photos, 3, 3) as $idx => $photo)
                                    <img src="{{ asset('storage/' . $photo) }}" alt="Fasilitas Perpustakaran" class="w-full h-24 object-cover rounded-lg">
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </section>

    {{-- Tambahan Fasilitas Lain --}}
    <div class="mt-8 pt-8 border-t border-slate-200/60">

        <h3 class="text-sm font-semibold text-emerald-600 uppercase tracking-widest mb-4">Fasilitas Pendukung Lainnya</h3>

        <div class="grid grid-cols-2 gap-4">
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200/60">
                <p class="text-xs font-medium text-emerald-600 uppercase tracking-wider mb-2">Kantin</p>
                <p class="text-slate-600 text-sm">Tersedia makanan dan minum yang bersih sesuai aturan pesantren.</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200/60">
                <p class="text-xs font-medium text-emerald-600 uppercase tracking-wider mb-2">Lapangan Olahraga</p>
                <p class="text-slate-600 text-sm">Lapangan terbuka untuk olahraga dan kegiatan ekstrakurikuler.</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200/60">
                <p class="text-xs font-medium text-emerald-600 uppercase tracking-wider mb-2">Lingkungan Bersih</p>
                <p class="text-slate-600 text-sm">Pengelolaan sampah dan kebersihan rutin seluruh area pondok.</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200/60">
                <p class="text-xs font-medium text-emerald-600 uppercase tracking-wider mb-2">Parkir</p>
                <p class="text-slate-600 text-sm">Area parkir terpisah untuk motor dan mobil pengunjung.</p>
            </div>
        </div>

    </div>

    {{-- Form Upload Gambar (untuk admin) --}}
    @if(auth()->check() && auth()->user()->role === 'admin')
    <div class="mt-8 pt-6 border-t border-slate-200/60">
        <h3 class="text-sm font-semibold text-emerald-600 uppercase tracking-wider mb-4">Upload Foto Fasilitas</h3>
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('POST')
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Pilih Foto</label>
                <input type="file" name="foto[]" multiple class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <p class="text-xs text-emerald-500 mt-1">Pilih multiple foto (maksimal 12 gambar)</p>
            </div>
            <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 transition">
                <i class="fa-solid fa-upload mr-2"></i> Unggah Foto
            </button>
        </form>
    </div>
    @endif

</div>

@endsection