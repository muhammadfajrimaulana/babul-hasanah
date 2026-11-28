@extends('layouts.admin')

@section('title', 'Detail Alumni - ' . $alumni->nama)

@section('content')
    @php
        $v = fn ($value) => filled($value) ? $value : '-';
        $labelCls = 'font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider text-[10px] mb-1';
        $boxCls = 'bg-slate-50/80 dark:bg-slate-800/40 p-3 rounded-xl';
        $cardCls = 'bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 shadow-sm';
        $iconCls = 'w-10 h-10 rounded-2xl flex items-center justify-center font-bold shrink-0';
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

        <!-- LOMPATAN CEPAT -->
        <div class="flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200/80 bg-white px-4 py-3 dark:border-slate-800 dark:bg-slate-900">
            <span class="mr-1 text-[10px] font-black uppercase tracking-wider text-slate-400">Lompat ke</span>
            @foreach ([
                '#data-diri' => 'Data Diri',
                '#kontak' => 'Kontak',
                '#akun' => 'Akun',
                '#dokumen' => 'Dokumen & Nilai',
            ] as $target => $label)
                <a href="{{ $target }}"
                    class="rounded-full border border-slate-200 px-3 py-1.5 text-[10px] font-bold text-slate-600 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700 dark:border-slate-700 dark:text-slate-300 dark:hover:border-emerald-500/40 dark:hover:bg-emerald-500/10 dark:hover:text-emerald-300">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- DATA PRIBADI & AKADEMIK -->
            <div class="{{ $cardCls }} scroll-mt-24" id="data-diri">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="{{ $iconCls }} bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
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
            <div class="{{ $cardCls }} scroll-mt-24" id="kontak">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="{{ $iconCls }} bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400">
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
            <div class="{{ $cardCls }} scroll-mt-24" id="akun">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="{{ $iconCls }} bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
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
                    <div class="{{ $iconCls }} bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400">
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
                    <div class="{{ $iconCls }} bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400">
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

        <!-- DOKUMEN KELULUSAN & TRANSKRIP (SATU JALUR, TAMPIL DI PORTAL WALI & ALUMNI) -->
        <div class="{{ $cardCls }} scroll-mt-24" id="dokumen">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-5">
                <div class="flex items-center gap-3">
                    <div class="{{ $iconCls }} bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                        <i class="fa-solid fa-folder-open"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Dokumen &amp; Transkrip Alumni</h2>
                        <p class="text-[11px] text-slate-400">
                            Satu jalur unggah untuk ijazah, SKL, transkrip nilai, dan sertifikat. Berkas tampil di
                            <b>/portal/dokumen</b> (wali) dan <b>/alumni/dokumen</b> (alumni).
                        </p>
                    </div>
                </div>
                @if ($tautanSiap && $alumni->santri && $bisaUbah)
                    <a href="{{ route('admin.santri.detail', $alumni->santri_id) }}"
                        class="inline-flex shrink-0 items-center gap-1.5 rounded-xl border border-slate-200 px-3 py-2 text-[10px] font-bold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Detail santri
                    </a>
                @endif
            </div>

            @if ($alumni->nilai_path)
                <div class="mb-5 flex flex-col gap-2 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300 sm:flex-row sm:items-center sm:justify-between">
                    <p class="font-bold">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        Masih ada berkas nilai lama. Unggah ulang sebagai <b>Transkrip Nilai Final</b> agar tampil di portal.
                    </p>
                    <a href="{{ route('admin.alumni.nilai.unduh', $alumni) }}"
                        class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-white px-3 py-1.5 text-[10px] font-black text-amber-700 shadow-sm hover:bg-amber-100 dark:bg-slate-900 dark:text-amber-300">
                        <i class="fa-solid fa-download"></i> Unduh nilai lama
                    </a>
                </div>
            @endif

            @if (! $tautanSiap || ! $alumni->santri)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
                    <p class="font-black"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Alumni belum tertaut ke data santri.</p>
                    <p class="mt-1">Tautkan santri asal terlebih dahulu agar dokumen bisa diunggah dan tampil di portal wali/alumni.</p>
                </div>
            @else
                @php
                    $santriDokumen = $alumni->santri;
                    $dokumenTersedia = $santriDokumen->dokumen->keyBy('jenis');
                @endphp

                @if ($bisaUbah)
                    <form action="{{ route('admin.santri.dokumen.store', $santriDokumen->id) }}" method="POST" enctype="multipart/form-data"
                        class="mb-5 grid grid-cols-1 gap-3 rounded-2xl border border-dashed border-slate-300 bg-slate-50/60 p-4 dark:border-slate-700 dark:bg-slate-800/30 sm:grid-cols-3">
                        @csrf
                        <div>
                            <label class="mb-1 block text-[10px] font-bold text-slate-500 dark:text-slate-400">Jenis Dokumen</label>
                            <select name="jenis" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                @foreach (\App\Models\DokumenSantri::JENIS as $kunci => $label)
                                    <option value="{{ $kunci }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('jenis') <p class="mt-1 text-[10px] text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1 block text-[10px] font-bold text-slate-500 dark:text-slate-400">Berkas (PDF/JPG/PNG, maks 10 MB)</label>
                            <div class="flex gap-2">
                                <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-[11px] text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-sky-600 file:px-4 file:py-2 file:text-[11px] file:font-black file:text-white hover:file:bg-sky-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                                <button type="submit" class="shrink-0 rounded-xl bg-sky-600 px-4 py-2 text-[11px] font-black text-white hover:bg-sky-700">
                                    <i class="fa-solid fa-upload mr-1"></i> Unggah
                                </button>
                            </div>
                            @error('file') <p class="mt-1 text-[10px] text-rose-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-[10px] text-slate-400">Unggah ulang jenis yang sama akan menimpa berkas lama.</p>
                        </div>
                    </form>
                @endif

                <div class="divide-y divide-slate-100 overflow-hidden rounded-2xl border border-slate-200/80 dark:divide-slate-800 dark:border-slate-800">
                    @foreach (\App\Models\DokumenSantri::JENIS as $kunci => $label)
                        @php $d = $dokumenTersedia->get($kunci); @endphp
                        <div class="flex items-center justify-between gap-3 px-4 py-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ $d ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500' }}">
                                    <i class="fa-solid {{ $d ? 'fa-circle-check' : 'fa-circle' }} text-xs"></i>
                                </span>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ $label }}</p>
                                    <p class="truncate text-[10px] text-slate-400">
                                        @if ($d)
                                            {{ $d->file_name ?: 'Berkas' }} · {{ $d->ukuranManusia() }}{{ $d->created_at ? ' · '.$d->created_at->translatedFormat('d M Y') : '' }}
                                        @else
                                            Belum diunggah
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                @if ($d)
                                    <a href="{{ $d->url() }}" target="_blank" rel="noopener" title="Lihat {{ $label }}"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-600 text-[11px] font-bold text-white transition hover:bg-emerald-700">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if ($bisaUbah)
                                        <form action="{{ route('admin.santri.dokumen.hapus', $d) }}" method="POST" onsubmit="return confirm('Hapus dokumen {{ $label }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus {{ $label }}"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-[11px] font-bold text-rose-600 transition hover:bg-rose-100 dark:bg-rose-500/10 dark:text-rose-300">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <span class="text-[10px] font-bold text-slate-300 dark:text-slate-600">—</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
@endsection
