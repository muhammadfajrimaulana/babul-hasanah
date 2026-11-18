@extends('layouts.admin')

@section('title', 'Input Prestasi Santri - Pondok Pesantren Babul Hasanah')

@section('content')
    @php
        $inputCls = 'w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 px-3.5 py-3 focus:outline-none focus:ring-2 focus:ring-amber-500/40 focus:border-amber-500';
        $labelCls = 'block text-[11px] font-black text-slate-600 dark:text-slate-300 mb-1.5';
        $cardCls = 'rounded-3xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900';
    @endphp

    <div class="space-y-6">

        {{-- Header halaman --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-amber-700 via-amber-600 to-slate-900 p-6 sm:p-8 text-white shadow-lg">
            <div class="absolute -right-8 -top-10 text-[160px] text-white/5">
                <i class="fa-solid fa-trophy"></i>
            </div>
            <div class="relative flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-white/15 border border-white/25 px-3 py-1 text-[10px] font-black uppercase tracking-wider">
                            {{ $bolehPrestasi ? 'Input Prestasi' : 'Daftar Prestasi' }}
                        </span>
                        <span class="rounded-full bg-amber-300/20 border border-amber-200/30 px-3 py-1 text-[10px] font-black">
                            {{ $jenjang ? $jenjang['kode'].' — '.$jenjang['nama'] : ($tanpaJenjang ? 'Jenjang belum ditautkan' : 'Semua jenjang') }}
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black leading-tight">
                        {{ $bolehPrestasi ? 'Input Prestasi Santri' : 'Prestasi Santri' }}
                    </h1>
                    <p class="text-xs sm:text-sm text-amber-100/90 max-w-2xl leading-relaxed">
                        @if ($bolehPrestasi)
                            Catat capaian santri. Prestasi langsung tampil di halaman publik <b>/prestasi</b>,
                            di Portal Wali, dan notifikasi email dikirim ke akun wali yang tertaut.
                        @else
                            Pantau capaian santri yang tampil di halaman publik <b>/prestasi</b> dan Portal Wali.
                            Halaman ini bersifat hanya baca.
                        @endif
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.dashboard.jenjang', $jenjangPilihan ? ['jenjang' => $jenjangPilihan] : []) }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 px-4 py-2.5 text-xs font-bold text-white transition">
                        <i class="fa-solid fa-arrow-left"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.prestasi.index') }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-amber-300 hover:bg-amber-200 px-4 py-2.5 text-xs font-black text-amber-950 transition">
                        <i class="fa-solid fa-table-list"></i> Kelola Prestasi
                    </a>
                </div>
            </div>
        </div>

        @include('dashboard.roles._pilih-jenjang')

        @unless ($jenjang)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-xs text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-200">
                @if ($tanpaJenjang)
                    <p class="font-black"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Akun belum ditautkan ke jenjang.</p>
                    <p class="mt-1 leading-relaxed">Hubungi Super Admin agar akun ini diberi jenjang melalui menu Pengguna.</p>
                @else
                    <p class="font-black"><i class="fa-solid fa-circle-info mr-1"></i> Pilih jenjang terlebih dahulu.</p>
                    <p class="mt-1 leading-relaxed">Gunakan menu <b>Pilih Jenjang</b> di atas untuk memuat daftar santri jenjang tersebut.</p>
                @endif
            </div>
        @endunless

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-xs font-bold text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300 flex items-start gap-2.5">
                <i class="fa-solid fa-circle-check mt-0.5"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-xs font-bold text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300 flex items-start gap-2.5">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-xs text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300 space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="flex items-start gap-2"><i class="fa-solid fa-triangle-exclamation mt-0.5"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- Form --}}
            <div class="xl:col-span-2 {{ $cardCls }}">
                <div class="flex items-start justify-between gap-3 mb-5">
                    <div>
                        <h2 class="text-base font-black text-slate-900 dark:text-white">
                            <i class="fa-solid fa-pen-to-square mr-1.5 text-amber-500"></i> Form Prestasi
                        </h2>
                        <p class="text-[11px] text-slate-400 mt-1">
                            Nama dan kelas santri terisi otomatis. Notifikasi email dikirim ke akun wali yang tertaut.
                        </p>
                    </div>
                    <span class="hidden sm:inline-flex items-center gap-1.5 rounded-xl bg-amber-50 px-3 py-2 text-[10px] font-black text-amber-700 dark:bg-amber-500/10 dark:text-amber-300">
                        <i class="fa-solid fa-earth-asia"></i> Tampil publik
                    </span>
                </div>

                @if (! $jenjang)
                    <div class="rounded-2xl border border-sky-200 bg-sky-50 px-4 py-4 text-xs text-sky-800">
                        <p class="font-black"><i class="fa-solid fa-circle-info mr-1"></i> Pilih jenjang terlebih dahulu.</p>
                        <p class="mt-1 leading-relaxed">Form prestasi akan aktif setelah Anda memilih salah satu jenjang pada menu di atas.</p>
                    </div>
                @elseif (! $bolehPrestasi)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-xs text-slate-600 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-300">
                        <p class="font-black"><i class="fa-solid fa-eye mr-1"></i> Mode hanya baca.</p>
                        <p class="mt-1 leading-relaxed">Form input prestasi hanya tersedia untuk Super Admin dan Admin Jenjang. Daftar prestasi tetap dapat dilihat.</p>
                    </div>
                @elseif (! $prestasiSiap)
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 text-xs text-amber-800">
                        <p class="font-black"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Kolom penghubung prestasi belum tersedia.</p>
                        <p class="mt-1 leading-relaxed">Jalankan <b>php artisan migrate</b> terlebih dahulu agar prestasi dapat ditautkan ke data santri.</p>
                    </div>
                @elseif ($santriList->isEmpty())
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 text-xs text-amber-800">
                        <p class="font-black"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Belum ada santri pada jenjang ini.</p>
                        <p class="mt-1 leading-relaxed">
                            Tambahkan data santri atau verifikasi pendaftar PPDB terlebih dahulu melalui
                            <a href="{{ route('admin.santri.index') }}" class="underline font-bold">Data Santri</a>.
                        </p>
                    </div>
                @else
                    <form method="POST" action="{{ route('admin.dashboard.jenjang.prestasi') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="santri_id" class="{{ $labelCls }}">Santri <span class="text-rose-500">*</span></label>
                            <select id="santri_id" name="santri_id" required class="{{ $inputCls }}">
                                <option value="" disabled @selected(old('santri_id') === null)>Pilih santri</option>
                                @foreach ($santriList as $s)
                                    <option value="{{ $s->id }}" @selected((string) old('santri_id') === (string) $s->id)>
                                        {{ $s->nama_lengkap }} — Kelas {{ $s->kelas ?: '-' }} ({{ $s->nomor_induk ?: 'tanpa induk' }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-slate-400 mt-1.5">
                                Nama & kelas diambil otomatis dari data santri yang dipilih.
                            </p>
                        </div>

                        <div>
                            <label for="judul" class="{{ $labelCls }}">Nama Lomba / Capaian <span class="text-rose-500">*</span></label>
                            <input id="judul" type="text" name="judul" maxlength="150" required
                                value="{{ old('judul') }}" class="{{ $inputCls }}"
                                placeholder="Contoh: Musabaqah Hifzhil Qur'an 30 Juz">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="kategori" class="{{ $labelCls }}">Bidang Lomba <span class="text-rose-500">*</span></label>
                                <input id="kategori" type="text" name="kategori" list="kategori-prestasi"
                                    maxlength="60" required value="{{ old('kategori') }}" class="{{ $inputCls }}"
                                    placeholder="Contoh: Tahfidz">
                                <datalist id="kategori-prestasi">
                                    @foreach ($kategoriPrestasi as $k)
                                        <option value="{{ $k }}"></option>
                                    @endforeach
                                    <option value="Tahfidz"></option>
                                    <option value="Tilawah"></option>
                                    <option value="Pidato"></option>
                                    <option value="Kaligrafi"></option>
                                    <option value="Olimpiade"></option>
                                </datalist>
                            </div>
                            <div>
                                <label for="tingkat" class="{{ $labelCls }}">Tingkat <span class="text-rose-500">*</span></label>
                                <select id="tingkat" name="tingkat" required class="{{ $inputCls }}">
                                    <option value="" disabled @selected(old('tingkat') === null)>Pilih tingkat</option>
                                    @foreach ($tingkatPrestasi as $t)
                                        <option value="{{ $t }}" @selected(old('tingkat') === $t)>{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="wilayah" class="{{ $labelCls }}">Lokasi Lomba <span class="text-rose-500">*</span></label>
                                <input id="wilayah" type="text" name="wilayah" maxlength="100" required
                                    value="{{ old('wilayah') }}" class="{{ $inputCls }}" placeholder="Contoh: Medan">
                            </div>
                            <div>
                                <label for="tahun" class="{{ $labelCls }}">Tahun <span class="text-rose-500">*</span></label>
                                <input id="tahun" type="number" name="tahun" min="2000" max="{{ date('Y') + 1 }}"
                                    value="{{ old('tahun', date('Y')) }}" required class="{{ $inputCls }}">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="peringkat" class="{{ $labelCls }}">Peringkat <span class="text-rose-500">*</span></label>
                                <input id="peringkat" type="number" name="peringkat" min="0" max="99"
                                    value="{{ old('peringkat', 1) }}" required class="{{ $inputCls }}">
                                <p class="text-[10px] text-slate-400 mt-1.5">Isi 1–3 untuk juara, 0 untuk penghargaan non-juara.</p>
                            </div>
                            <div>
                                <label for="label" class="{{ $labelCls }}">Label Kartu (opsional)</label>
                                <input id="label" type="text" name="label" maxlength="40"
                                    value="{{ old('label') }}" class="{{ $inputCls }}" placeholder="Contoh: Juara 1">
                                <p class="text-[10px] text-slate-400 mt-1.5">Kosongkan untuk label otomatis.</p>
                            </div>
                        </div>

                        <div>
                            <label for="deskripsi" class="{{ $labelCls }}">Deskripsi (opsional)</label>
                            <textarea id="deskripsi" name="deskripsi" rows="3" maxlength="1000" class="{{ $inputCls }}"
                                placeholder="Keterangan singkat prestasi">{{ old('deskripsi') }}</textarea>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-t border-slate-100 dark:border-slate-800 pt-4">
                            <p class="text-[10px] text-slate-400 leading-relaxed">
                                <i class="fa-solid fa-circle-info mr-1 text-amber-600"></i>
                                Prestasi langsung tampil di /prestasi dan portal wali.
                            </p>
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 hover:bg-amber-600 px-6 py-3 text-xs font-black text-white transition shadow-sm">
                                <i class="fa-solid fa-floppy-disk"></i> Simpan & Kirim ke Wali
                            </button>
                        </div>
                    </form>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">

                {{-- Alur --}}
                <div class="{{ $cardCls }}">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">
                        <i class="fa-solid fa-share-nodes mr-1.5 text-amber-500"></i> Ke Mana Data Ini Pergi?
                    </h3>
                    <div class="mt-4 space-y-3">
                        <div class="flex gap-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 px-4 py-3">
                            <span class="w-8 h-8 shrink-0 rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-earth-asia"></i>
                            </span>
                            <div>
                                <p class="text-[11px] font-black text-slate-800 dark:text-slate-100">Halaman /prestasi</p>
                                <p class="text-[10px] text-slate-400 leading-relaxed">Langsung tampil sebagai kartu prestasi di website pesantren.</p>
                            </div>
                        </div>
                        <div class="flex gap-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 px-4 py-3">
                            <span class="w-8 h-8 shrink-0 rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-house-user"></i>
                            </span>
                            <div>
                                <p class="text-[11px] font-black text-slate-800 dark:text-slate-100">Portal Wali</p>
                                <p class="text-[10px] text-slate-400 leading-relaxed">Muncul di menu Prestasi milik akun wali yang tertaut.</p>
                            </div>
                        </div>
                        <div class="flex gap-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 px-4 py-3">
                            <span class="w-8 h-8 shrink-0 rounded-xl bg-sky-100 text-sky-700 dark:bg-sky-500/15 dark:text-sky-300 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <div>
                                <p class="text-[11px] font-black text-slate-800 dark:text-slate-100">Email Wali</p>
                                <p class="text-[10px] text-slate-400 leading-relaxed">Notifikasi dikirim ke email akun wali. Bergantung pada konfigurasi SMTP.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Prestasi terbaru --}}
                <div class="{{ $cardCls }}">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-sm font-black text-slate-900 dark:text-white">
                            <i class="fa-solid fa-clock-rotate-left mr-1.5 text-amber-500"></i> Prestasi Terbaru
                        </h3>
                        <a href="{{ route('admin.prestasi.index') }}" class="text-[10px] font-bold text-amber-600 hover:underline">Lihat semua</a>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse ($prestasiTerbaru as $p)
                            <div class="rounded-2xl border border-slate-100 dark:border-slate-800 px-4 py-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-[11px] font-black text-slate-800 dark:text-slate-100 leading-snug">
                                            {{ $p->judul }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 truncate mt-0.5">
                                            {{ $p->nama }} · {{ $p->tingkat }} · {{ $p->tahun }}
                                        </p>
                                    </div>
                                    <span class="shrink-0 rounded-xl bg-amber-50 px-2.5 py-1 text-[10px] font-black text-amber-700 dark:bg-amber-500/10 dark:text-amber-300">
                                        {{ $p->label }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-200 dark:border-slate-700 px-4 py-8 text-center text-[11px] text-slate-400">
                                Belum ada prestasi{{ $jenjang ? ' pada jenjang ini' : '' }}.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
