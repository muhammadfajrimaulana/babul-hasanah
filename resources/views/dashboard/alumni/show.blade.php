@extends('layouts.admin')

@section('title', 'Detail Alumni - ' . $alumni->nama)

@section('content')
    @php
        $v = fn ($value) => filled($value) ? $value : '-';
        $labelCls = 'font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider text-[10px] mb-1';
        $boxCls = 'bg-slate-50 dark:bg-slate-800/50 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800/80';
        $cardCls = 'bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 shadow-sm';
        $bisaUbah = auth()->user()->bisa('alumni.ubah');
    @endphp

    <div class="max-w-6xl mx-auto py-6 sm:py-8 px-4 sm:px-6 space-y-6">

        @if (session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300 px-4 py-3.5 rounded-2xl text-xs font-bold flex items-center gap-2.5 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-300 px-4 py-3.5 rounded-2xl text-xs font-bold flex items-center gap-2.5 shadow-sm">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 dark:text-rose-400 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- HEADER -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="flex items-center gap-4">
                @if ($alumni->foto_url)
                    <img src="{{ $alumni->foto_url }}" alt="Foto {{ $alumni->nama }}"
                        class="w-16 h-16 rounded-2xl object-cover border border-slate-100 dark:border-slate-700">
                @else
                    <div class="w-16 h-16 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl font-black">
                        {{ mb_strtoupper(mb_substr($alumni->nama, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-extrabold uppercase tracking-wider border border-emerald-500/20 mb-1">
                        Data Alumni
                    </span>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $alumni->nama }}</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ $v($alumni->jenjang) }}
                        &middot; Angkatan {{ $alumni->tahun_lulus }}
                        @if ($alumni->nisn)
                            &middot; NISN {{ $alumni->nisn }}
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 self-start sm:self-center">
                @if ($bisaUbah)
                    <a href="{{ route('admin.alumni.edit', $alumni) }}"
                        class="bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 dark:hover:bg-amber-500/20 text-amber-700 dark:text-amber-400 px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2">
                        <i class="fa-solid fa-pen"></i> Edit
                    </a>
                    @if ($akunSiap)
                        <form action="{{ route('admin.alumni.reset-password', $alumni) }}" method="POST"
                            onsubmit="return confirm('{{ $akunAlumni ? 'Reset password' : 'Buat akun' }} alumni {{ $alumni->nama }}? Kredensial dikirim ke {{ $akunAlumni?->email ?: ($alumni->email ?: 'email alumni') }} dan langsung bisa dipakai.');">
                            @csrf
                            <button type="submit"
                                class="bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2">
                                <i class="fa-solid fa-rotate"></i>
                                {{ $akunAlumni ? 'Reset Password Alumni' : 'Buat Akun Alumni' }}
                            </button>
                        </form>
                    @endif
                @endif
                <a href="{{ route('admin.alumni.index') }}"
                    class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- DATA PRIBADI & AKADEMIK -->
            <div class="{{ $cardCls }}">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-id-card"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Data Pribadi &amp; Akademik</h2>
                        <p class="text-[11px] text-slate-400">Identitas alumni saat menempuh pendidikan</p>
                    </div>
                </div>

                <dl class="grid grid-cols-1 gap-4 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="{{ $boxCls }}">
                            <dt class="{{ $labelCls }}">NIS / No. Induk</dt>
                            <dd class="font-bold text-slate-900 dark:text-white font-mono">
                                @if ($tautanSiap && $alumni->santri)
                                    <a href="{{ route('admin.santri.detail', $alumni->santri->id) }}"
                                        class="text-indigo-600 hover:underline dark:text-indigo-400">
                                        {{ $v($alumni->santri->nomor_induk) }}
                                    </a>
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                        <div class="{{ $boxCls }}">
                            <dt class="{{ $labelCls }}">NISN</dt>
                            <dd class="font-bold text-slate-900 dark:text-white font-mono">{{ $v($alumni->nisn) }}</dd>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="{{ $boxCls }}">
                            <dt class="{{ $labelCls }}">Jenis Kelamin</dt>
                            <dd class="font-bold text-slate-900 dark:text-white">
                                {{ $alumni->jenis_kelamin === 'L' ? 'Laki-laki' : ($alumni->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}
                            </dd>
                        </div>
                        <div class="{{ $boxCls }}">
                            <dt class="{{ $labelCls }}">Jenjang Terakhir</dt>
                            <dd class="font-bold text-emerald-700 dark:text-emerald-400">{{ $v($alumni->jenjang) }}</dd>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="{{ $boxCls }}">
                            <dt class="{{ $labelCls }}">Tahun Masuk</dt>
                            <dd class="font-bold text-slate-900 dark:text-white">{{ $v($alumni->tahun_masuk) }}</dd>
                        </div>
                        <div class="{{ $boxCls }}">
                            <dt class="{{ $labelCls }}">Tahun Lulus</dt>
                            <dd class="font-bold text-slate-900 dark:text-white">{{ $v($alumni->tahun_lulus) }}</dd>
                        </div>
                    </div>

                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">Status Data</dt>
                        <dd class="font-medium text-slate-900 dark:text-white">
                            @if ($tautanSiap && $alumni->santri)
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-2.5 py-1 text-[10px] font-bold text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">
                                    <i class="fa-solid fa-link text-[9px]"></i>
                                    Tertaut ke santri {{ $alumni->santri->nama_lengkap }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                    <i class="fa-solid fa-pen text-[9px]"></i> Diinput manual
                                </span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- KONTAK & DOMISILI -->
            <div class="{{ $cardCls }}">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-10 h-10 rounded-2xl bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-address-book"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Kontak &amp; Domisili</h2>
                        <p class="text-[11px] text-slate-400">Data yang dibutuhkan untuk menghubungi alumni</p>
                    </div>
                </div>

                <dl class="grid grid-cols-1 gap-4 text-xs">
                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">No. HP / WhatsApp</dt>
                        <dd class="font-bold text-slate-900 dark:text-white font-mono">{{ $v($alumni->no_hp) }}</dd>
                    </div>
                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">Email</dt>
                        <dd class="font-medium text-slate-900 dark:text-white break-all">{{ $v($alumni->email) }}</dd>
                    </div>
                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">Alamat</dt>
                        <dd class="font-medium text-slate-900 dark:text-white leading-relaxed">{{ $v($alumni->alamat) }}</dd>
                    </div>
                </dl>
            </div>

            <!-- AKUN LOGIN ALUMNI -->
            <div class="{{ $cardCls }}">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-user-lock"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Akun Login Alumni</h2>
                        <p class="text-[11px] text-slate-400">Username dan status kredensial Portal Alumni</p>
                    </div>
                </div>

                @if (! $akunSiap)
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
                        <p class="font-black"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Kolom akun alumni belum tersedia.</p>
                        <p class="mt-1">Jalankan <b>php artisan migrate</b> terlebih dahulu.</p>
                    </div>
                @elseif ($akunAlumni)
                    <dl class="grid grid-cols-1 gap-4 text-xs">
                        <div class="{{ $boxCls }}">
                            <dt class="{{ $labelCls }}">Username (Email)</dt>
                            <dd class="font-bold text-slate-900 dark:text-white break-all">{{ $akunAlumni->email }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="{{ $boxCls }}">
                                <dt class="{{ $labelCls }}">Status Akun</dt>
                                <dd class="font-bold text-slate-900 dark:text-white">
                                    @if ($akunAlumni->is_active)
                                        <span class="text-emerald-700 dark:text-emerald-400">Aktif</span>
                                    @else
                                        <span class="text-rose-600 dark:text-rose-400">Nonaktif</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="{{ $boxCls }}">
                                <dt class="{{ $labelCls }}">Password</dt>
                                <dd class="font-bold text-slate-900 dark:text-white">
                                    @if ($akunAlumni->must_change_password)
                                        <span class="inline-flex items-center gap-1 rounded-lg bg-indigo-50 px-2.5 py-1 text-[10px] font-bold text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">
                                            Sementara{{ $akunAlumni->passwordSementaraKedaluwarsa() ? ' · kedaluwarsa' : '' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                                            Langsung dipakai
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        </div>
                    </dl>
                    <p class="mt-4 text-[10px] leading-relaxed text-slate-400">
                        Gunakan tombol <b>Reset Password Alumni</b> untuk mengirim password baru ke username di atas.
                        Password langsung bisa dipakai, hanya dikirim lewat email, dan tidak dapat dilihat oleh admin.
                    </p>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-6 text-center text-xs text-slate-400 dark:border-slate-700">
                        <i class="fa-solid fa-user-lock mb-2 block text-2xl text-slate-300"></i>
                        Alumni ini belum memiliki akun login. Gunakan <b>Buat Akun Massal</b> pada halaman Data Alumni
                        atau tombol Reset Password Alumni untuk membuat akun sekaligus mengirim kredensial.
                    </div>
                @endif
            </div>

            <!-- KEGIATAN SETELAH LULUS -->
            <div class="{{ $cardCls }}">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Kegiatan Setelah Lulus</h2>
                        <p class="text-[11px] text-slate-400">Kuliah, bekerja, wirausaha, atau mengabdi</p>
                    </div>
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">Kegiatan</dt>
                        <dd class="font-bold text-slate-900 dark:text-white">
                            @if ($alumni->kegiatan)
                                <span class="inline-block rounded-lg bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                                    {{ $alumni->kegiatan }}
                                </span>
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">Kampus / Tempat Kerja / Usaha</dt>
                        <dd class="font-bold text-slate-900 dark:text-white">{{ $v($alumni->instansi) }}</dd>
                    </div>
                </dl>
            </div>

            <!-- TESTIMONI -->
            <div class="{{ $cardCls }}">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-10 h-10 rounded-2xl bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-quote-left"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Testimoni</h2>
                        <p class="text-[11px] text-slate-400">Kesan dan pesan untuk pesantren</p>
                    </div>
                </div>

                @if (filled($alumni->testimoni))
                    <blockquote class="rounded-2xl border border-slate-100 bg-slate-50 p-4 text-xs italic leading-relaxed text-slate-700 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-300">
                        “{{ $alumni->testimoni }}”
                    </blockquote>
                @else
                    <p class="text-xs italic text-slate-400">Belum ada testimoni.</p>
                @endif
            </div>
        </div>

        <!-- NILAI / TRANSKRIP -->
        <div class="{{ $cardCls }}" id="nilai">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-file-pdf"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Nilai / Transkrip Alumni</h2>
                        <p class="text-[11px] text-slate-400">Unggah satu berkas PDF nilai atau transkrip untuk alumni ini.</p>
                    </div>
                </div>
                @if ($alumni->nilai_path)
                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-xl bg-emerald-50 px-3 py-2 text-[10px] font-black text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                        <i class="fa-solid fa-circle-check"></i> Berkas tersedia
                    </span>
                @endif
            </div>

            @if (! $nilaiSiap)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
                    <p class="font-black"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Fitur nilai alumni belum aktif.</p>
                    <p class="mt-1">Jalankan <b>php artisan migrate</b> terlebih dahulu.</p>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
                    <div class="lg:col-span-3 space-y-4">
                        @if ($alumni->nilai_path)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/50">
                                <p class="text-xs font-black text-slate-800 dark:text-slate-100 break-all">
                                    <i class="fa-solid fa-file-pdf mr-1 text-rose-600"></i>
                                    {{ $alumni->nilai_nama ?: 'nilai-'.$alumni->nama.'.pdf' }}
                                </p>
                                <p class="mt-1 text-[10px] text-slate-400">
                                    Diunggah {{ optional($alumni->nilai_diunggah_pada)->translatedFormat('d F Y, H:i') ?? '-' }}
                                </p>
                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    <a href="{{ route('admin.alumni.nilai.unduh', $alumni) }}"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-[10px] font-bold text-white hover:bg-emerald-700 transition">
                                        <i class="fa-solid fa-download"></i> Unduh PDF
                                    </a>
                                    <a href="{{ $alumni->nilai_url }}" target="_blank" rel="noopener"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-[10px] font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                                        <i class="fa-solid fa-up-right-from-square"></i> Buka
                                    </a>
                                    @if ($bisaUbah)
                                        <form action="{{ route('admin.alumni.nilai.hapus', $alumni) }}" method="POST"
                                            onsubmit="return confirm('Hapus berkas nilai {{ $alumni->nama }}? Tindakan ini tidak bisa dibatalkan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-rose-50 px-3 py-2 text-[10px] font-bold text-rose-600 hover:bg-rose-100 transition dark:bg-rose-500/10 dark:text-rose-300">
                                                <i class="fa-solid fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 dark:border-slate-700 dark:bg-slate-800">
                                <iframe src="{{ $alumni->nilai_url }}#toolbar=1&navpanes=0&view=FitH"
                                    class="h-[420px] w-full" title="Pratinjau nilai {{ $alumni->nama }}"></iframe>
                            </div>
                        @else
                            <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-xs text-slate-400 dark:border-slate-700">
                                <i class="fa-solid fa-file-circle-plus mb-2 block text-2xl text-slate-300"></i>
                                Belum ada berkas nilai/transkrip untuk alumni ini.
                            </div>
                        @endif
                    </div>

                    @if ($bisaUbah)
                        <div class="lg:col-span-2">
                            <form action="{{ route('admin.alumni.nilai.upload', $alumni) }}" method="POST"
                                enctype="multipart/form-data"
                                class="space-y-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/50">
                                @csrf
                                <div>
                                    <p class="text-xs font-black text-slate-800 dark:text-slate-100">
                                        {{ $alumni->nilai_path ? 'Ganti Berkas Nilai' : 'Unggah Berkas Nilai' }}
                                    </p>
                                    <p class="mt-1 text-[10px] leading-relaxed text-slate-400">
                                        Format PDF, maksimal 10 MB. Unggah ulang akan menimpa berkas lama.
                                    </p>
                                </div>

                                <div>
                                    <label for="file_nilai" class="mb-1.5 block text-[10px] font-black uppercase tracking-wider text-slate-400">
                                        Berkas PDF <span class="text-rose-500">*</span>
                                    </label>
                                    <input id="file_nilai" type="file" name="file_nilai" accept="application/pdf,.pdf" required
                                        class="block w-full cursor-pointer rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-rose-600 file:px-4 file:py-2 file:text-[11px] file:font-black file:text-white hover:file:bg-rose-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                    @error('file_nilai') <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <button type="submit"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-rose-600 px-5 py-2.5 text-[11px] font-black text-white transition hover:bg-rose-700">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                    {{ $alumni->nilai_path ? 'Unggah Ulang' : 'Unggah Nilai' }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endif
        </div>

    </div>
@endsection
