@extends('layouts.admin')

@section('title', ($prestasi ? 'Edit' : 'Tambah') . ' Prestasi - Pondok Pesantren Babul Hasanah')

@section('content')
    @php
        $card = 'bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm p-6 space-y-4';
        $input = 'w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500';
        $label = 'block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1.5';
        $note = 'text-[11px] text-slate-400 dark:text-slate-500 leading-relaxed';

        // Nilai isian: data lama setelah gagal validasi, atau data yang sedang diedit
        $v = fn (string $k, $default = '') => old($k, $prestasi?->$k ?? $default);
    @endphp

    <div class="space-y-6 max-w-3xl">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">{{ $prestasi ? 'Edit Prestasi' : 'Tambah Prestasi' }}</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Data ini tampil sebagai kartu di halaman Prestasi
                    Santri.</p>
            </div>
            <a href="{{ route('admin.prestasi.index') }}"
                class="text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white whitespace-nowrap pt-1">Kembali</a>
        </div>

        @if ($errors->any())
            <div class="bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 rounded-2xl px-5 py-4">
                <p class="text-xs font-black text-rose-700 dark:text-rose-400">Data belum tersimpan. Periksa isian berikut:</p>
                <ul class="mt-2 ml-4 list-disc text-xs text-rose-700 dark:text-rose-400 space-y-0.5">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
            action="{{ $prestasi ? route('admin.prestasi.update', $prestasi) : route('admin.prestasi.store') }}"
            class="space-y-6">
            @csrf
            @if ($prestasi)
                @method('PUT')
            @endif

            {{-- ========== Capaian ========== --}}
            <div class="{{ $card }}">
                <h3 class="text-sm font-black text-slate-900 dark:text-white">Capaian</h3>

                <div>
                    <label for="judul" class="{{ $label }}">Nama lomba / capaian</label>
                    <input type="text" id="judul" name="judul" value="{{ $v('judul') }}" maxlength="150" required
                        class="{{ $input }}" placeholder="Contoh: Musabaqah Hifzhil Qur’an 30 Juz">
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="kategori" class="{{ $label }}">Bidang lomba</label>
                        <input type="text" id="kategori" name="kategori" value="{{ $v('kategori') }}" list="daftar-kategori"
                            maxlength="60" required class="{{ $input }}" placeholder="Contoh: Tahfidz">
                        <datalist id="daftar-kategori">
                            @foreach ($kategori as $k)
                                <option value="{{ $k }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label for="tingkat" class="{{ $label }}">Tingkat</label>
                        <select id="tingkat" name="tingkat" required class="{{ $input }}">
                            <option value="" disabled @selected($v('tingkat') === '')>Pilih tingkat</option>
                            @foreach ($tingkatList as $t)
                                <option value="{{ $t }}" @selected($v('tingkat') === $t)>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="wilayah" class="{{ $label }}">Lokasi lomba</label>
                        <input type="text" id="wilayah" name="wilayah" value="{{ $v('wilayah') }}" maxlength="100" required
                            class="{{ $input }}" placeholder="Contoh: Banda Aceh">
                    </div>
                    <div>
                        <label for="tahun" class="{{ $label }}">Tahun</label>
                        <input type="number" id="tahun" name="tahun" value="{{ $v('tahun', date('Y')) }}" min="2000"
                            max="{{ date('Y') + 1 }}" required class="{{ $input }}">
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="peringkat" class="{{ $label }}">Peringkat</label>
                        <input type="number" id="peringkat" name="peringkat" value="{{ $v('peringkat', 1) }}" min="0"
                            max="99" required class="{{ $input }}">
                        <p class="{{ $note }} mt-1.5">Isi 1, 2, atau 3 untuk juara. Isi 0 untuk penghargaan non-juara.</p>
                    </div>
                    <div>
                        <label for="label" class="{{ $label }}">Teks di kartu (opsional)</label>
                        <input type="text" id="label" name="label" value="{{ $v('label') }}" maxlength="40"
                            class="{{ $input }}" placeholder="Contoh: Juara 1, Harapan 2, Terbaik">
                        <p class="{{ $note }} mt-1.5">Jika kosong: otomatis “Juara …” atau “Penghargaan”.</p>
                    </div>
                </div>

                <div>
                    <label for="deskripsi" class="{{ $label }}">Deskripsi singkat (opsional)</label>
                    <textarea id="deskripsi" name="deskripsi" rows="3" maxlength="400" class="{{ $input }}">{{ $v('deskripsi') }}</textarea>
                </div>
            </div>

            {{-- ========== Santri ========== --}}
            <div class="{{ $card }}">
                <h3 class="text-sm font-black text-slate-900 dark:text-white">Santri</h3>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="nama" class="{{ $label }}">Nama santri</label>
                        <input type="text" id="nama" name="nama" value="{{ $v('nama') }}" maxlength="100" required
                            class="{{ $input }}">
                    </div>
                    <div>
                        <label for="kelas" class="{{ $label }}">Kelas</label>
                        <input type="text" id="kelas" name="kelas" value="{{ $v('kelas') }}" maxlength="50" required
                            class="{{ $input }}" placeholder="Contoh: XI MA">
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-6 py-3 rounded-xl transition shadow-sm">
                    {{ $prestasi ? 'Simpan Perubahan' : 'Simpan Prestasi' }}
                </button>
                <a href="{{ route('admin.prestasi.index') }}"
                    class="text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white">Batal</a>
            </div>
        </form>
    </div>
@endsection
