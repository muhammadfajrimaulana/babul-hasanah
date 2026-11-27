@extends('layouts.admin')

@section('title', 'Detail Data Santri - ' . $santri->nama_lengkap)

@section('content')
    @php
        $p = $pendaftaranOnline ?? null;
        $v = fn ($value) => filled($value) ? $value : '-';

        $status = strtolower($p->status_pendaftaran ?? 'pending');
        $statusCls = match ($status) {
            'diterima' => 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300',
            'ditolak' => 'bg-rose-100 dark:bg-rose-500/20 text-rose-800 dark:text-rose-300',
            default => 'bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300',
        };

        $labelCls = 'font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider text-[10px] mb-1';
        $boxCls = 'bg-slate-50 dark:bg-slate-800/50 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800/80';
        $cardCls = 'bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 shadow-sm';
    @endphp

    <div class="max-w-6xl mx-auto py-6 sm:py-8 px-4 sm:px-6 space-y-6">

        @if (session('success'))
            <div
                class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300 px-4 py-3.5 rounded-2xl text-xs font-bold flex items-center gap-2.5 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- HEADER -->
        <div
            class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
                <div>
                    <span
                        class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-extrabold uppercase tracking-wider border border-emerald-500/20 mb-1">
                        Modul Santri
                    </span>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $santri->nama_lengkap }}</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ $v($santri->jenjang_pendidikan) }}
                        @if ($santri->kelas)
                            &middot; Kelas {{ $santri->kelas }}
                        @endif
                        @if ($santri->nomor_induk)
                            &middot; NI {{ $santri->nomor_induk }}
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 self-start sm:self-center">
                @if (auth()->user()->bisa('santri.ubah'))
                    <a href="{{ route('admin.santri.edit', $santri->id) }}"
                        class="bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 dark:hover:bg-amber-500/20 text-amber-700 dark:text-amber-400 px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2">
                        <i class="fa-solid fa-pen"></i> Edit
                    </a>
                    @if (\App\Support\Akses::superAdmin(auth()->user()))
                        <form action="{{ route('admin.santri.destroy', $santri->id) }}" method="POST"
                            onsubmit="return confirm('Pindahkan data santri ini ke Tempat Sampah? Tindakan ini hanya untuk Superadmin dan dapat dipulihkan.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-rose-50 dark:bg-rose-500/10 hover:bg-rose-100 dark:hover:bg-rose-500/20 text-rose-700 dark:text-rose-400 px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </button>
                        </form>
                    @endif
                @endif
                <a href="{{ route('admin.santri.index') }}"
                    class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <!-- STATUS KELULUSAN & TAUTAN DATA ALUMNI -->
        <div class="{{ $cardCls }}">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Kelulusan &amp; Data Alumni</h2>
                        <p class="text-[11px] text-slate-400">Santri yang ditandai lulus otomatis masuk ke halaman Data Alumni.</p>
                    </div>
                </div>
                @if ($kelulusanSiap && $santri->sudah_lulus)
                    <span class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 px-3 py-2 text-[11px] font-black text-indigo-700 dark:text-indigo-300">
                        <i class="fa-solid fa-circle-check"></i>
                        Lulus{{ $santri->tahun_lulus ? ' '.$santri->tahun_lulus : '' }}
                    </span>
                @endif
            </div>

            @if (! $kelulusanSiap)
                <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800">
                    <p class="font-black"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Kolom kelulusan belum tersedia.</p>
                    <p class="mt-1">Jalankan <b>php artisan migrate</b> agar fitur kelulusan &amp; tautan alumni aktif.</p>
                </div>
            @elseif ($santri->sudah_lulus)
                <dl class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">Status</dt>
                        <dd class="font-bold text-indigo-700 dark:text-indigo-300">Lulus</dd>
                    </div>
                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">Tahun Lulus</dt>
                        <dd class="font-bold text-slate-900 dark:text-white">{{ $santri->tahun_lulus ?? '-' }}</dd>
                    </div>
                    <div class="{{ $boxCls }} sm:col-span-2">
                        <dt class="{{ $labelCls }}">Data Alumni</dt>
                        <dd class="font-medium text-slate-900 dark:text-white">
                            @if (! $tautanSiap)
                                Jalankan migrasi agar data alumni dapat ditautkan.
                            @elseif ($alumniTertaut)
                                <span class="text-emerald-700 dark:text-emerald-400 font-bold">
                                    <i class="fa-solid fa-link mr-1"></i> Tertaut ke Data Alumni
                                </span>
                                @if (auth()->user()->bisa('alumni.lihat'))
                                    <a href="{{ route('admin.alumni.index', ['q' => $santri->nomor_induk]) }}"
                                        class="ml-2 text-[11px] font-bold text-emerald-700 dark:text-emerald-400 hover:underline">Lihat</a>
                                @endif
                                @if (auth()->user()->bisa('alumni.ubah'))
                                    <a href="{{ route('admin.alumni.edit', $alumniTertaut) }}"
                                        class="ml-1 text-[11px] font-bold text-amber-600 hover:underline">Lengkapi</a>
                                @endif
                            @else
                                <span class="text-amber-700 dark:text-amber-400 font-bold">
                                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> Belum ada data alumni tertaut
                                </span>
                                <span class="text-slate-400">— buka Data Alumni lalu klik “Tarik Santri Lulus”.</span>
                            @endif
                        </dd>
                    </div>
                </dl>

                @if (auth()->user()->bisa('santri.ubah'))
                    <form action="{{ route('admin.santri.batalkan-kelulusan', $santri->id) }}" method="POST"
                        class="mt-5"
                        onsubmit="return confirm('Batalkan status lulus {{ $santri->nama_lengkap }}? Data alumni yang dibuat otomatis akan ikut dihapus.');">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-[11px] font-bold text-rose-700 hover:bg-rose-100 transition dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
                            <i class="fa-solid fa-rotate-left"></i> Batalkan Kelulusan
                        </button>
                    </form>
                @endif
            @elseif (auth()->user()->bisa('santri.ubah'))
                <form action="{{ route('admin.santri.luluskan', $santri->id) }}" method="POST"
                    class="mt-5 flex flex-col sm:flex-row sm:items-end gap-3"
                    onsubmit="return confirm('Tandai {{ $santri->nama_lengkap }} sebagai LULUS dan buat data alumni?');">
                    @csrf
                    <div class="sm:w-44">
                        <label for="tahun_lulus" class="{{ $labelCls }}">Tahun Lulus</label>
                        <input id="tahun_lulus" type="number" name="tahun_lulus" min="1998" max="{{ now()->year + 1 }}"
                            value="{{ old('tahun_lulus', now()->year) }}"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    </div>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-[11px] font-black text-white shadow-sm transition hover:bg-indigo-700">
                        <i class="fa-solid fa-graduation-cap"></i> Tandai Lulus &amp; Masukkan ke Alumni
                    </button>
                </form>
                <p class="mt-2 text-[10px] text-slate-400">
                    Data alumni dibuat dari nama, jenjang, dan kontak santri. Kegiatan setelah lulus bisa dilengkapi di halaman Data Alumni.
                </p>
            @else
                <p class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-[11px] text-slate-500 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-300">
                    <i class="fa-solid fa-eye mr-1"></i>
                    Status kelulusan saat ini aktif. Hanya Super Admin dan Admin Jenjang yang dapat menandai kelulusan.
                </p>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- DATA SANTRI -->
            <div class="{{ $cardCls }}">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-id-card"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Data Santri</h2>
                        <p class="text-[11px] text-slate-400">Data aktif setelah verifikasi</p>
                    </div>
                </div>

                <dl class="grid grid-cols-1 gap-4 text-xs">
                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">Nomor Induk</dt>
                        <dd class="font-bold text-slate-900 dark:text-white text-sm font-mono">{{ $v($santri->nomor_induk) }}</dd>
                    </div>
                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">Nama Lengkap</dt>
                        <dd class="font-bold text-slate-900 dark:text-white text-sm">{{ $santri->nama_lengkap }}</dd>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="{{ $boxCls }}">
                            <dt class="{{ $labelCls }}">Jenjang Pendidikan</dt>
                            <dd class="font-bold text-slate-900 dark:text-white">{{ $v($santri->jenjang_pendidikan) }}</dd>
                        </div>
                        <div class="{{ $boxCls }}">
                            <dt class="{{ $labelCls }}">Kelas</dt>
                            <dd class="font-bold text-emerald-600 dark:text-emerald-400">{{ $v($santri->kelas) }}</dd>
                        </div>
                    </div>
                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">Alamat</dt>
                        <dd class="font-medium text-slate-900 dark:text-white leading-relaxed">{{ $v($santri->alamat) }}</dd>
                    </div>
                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">Nama Wali</dt>
                        <dd class="font-bold text-slate-900 dark:text-white">{{ $v($santri->nama_wali) }}</dd>
                    </div>
                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">Email Santri <span class="font-normal">(login alumni)</span></dt>
                        <dd class="font-medium text-slate-700 dark:text-slate-300 break-all">{{ $v($santri->email_santri) }}</dd>
                    </div>
                </dl>
            </div>

            <!-- AKUN PORTAL WALI -->
            <div class="{{ $cardCls }}">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-10 h-10 rounded-2xl bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Akun Portal Wali</h2>
                        <p class="text-[11px] text-slate-400">Akun login orang tua / wali</p>
                    </div>
                </div>

                @if ($santri->user)
                    <dl class="grid grid-cols-1 gap-4 text-xs">
                        <div class="{{ $boxCls }}">
                            <dt class="{{ $labelCls }}">Nama Akun</dt>
                            <dd class="font-bold text-slate-900 dark:text-white">{{ $santri->user->name }}</dd>
                        </div>
                        <div class="{{ $boxCls }}">
                            <dt class="{{ $labelCls }}">Email Orang Tua / Wali</dt>
                            <dd class="font-medium text-slate-700 dark:text-slate-300 break-all">{{ $santri->user->email }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="{{ $boxCls }}">
                                <dt class="{{ $labelCls }}">No. Telepon</dt>
                                <dd class="font-bold text-slate-900 dark:text-white">{{ $v($santri->user->phone) }}</dd>
                            </div>
                            <div class="{{ $boxCls }}">
                                <dt class="{{ $labelCls }}">Role</dt>
                                <dd class="font-bold text-slate-900 dark:text-white">{{ $santri->user->role }}</dd>
                            </div>
                        </div>
                    </dl>
                @else
                    <div class="text-center py-10 text-slate-400 text-xs italic">
                        Santri ini belum terhubung dengan akun portal wali.
                    </div>
                @endif
            </div>
        </div>

        <!-- DATA PENDAFTARAN ONLINE -->
        @if ($p)
            <div class="{{ $cardCls }}">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Data Pendaftaran Online</h2>
                            <p class="text-[11px] text-slate-400">Arsip data saat proses pendaftaran</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-[11px] font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-2.5 py-1 rounded-lg border border-emerald-500/20">
                            {{ $p->no_pendaftaran }}
                        </span>
                        <span class="{{ $statusCls }} text-[10px] font-bold px-2.5 py-1 rounded-full">{{ ucfirst($status) }}</span>
                    </div>
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">Nama Lengkap</dt>
                        <dd class="font-bold text-slate-900 dark:text-white text-sm">{{ $v($p->nama_lengkap) }}</dd>
                    </div>
                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">Tempat Tanggal Lahir</dt>
                        <dd class="font-bold text-slate-900 dark:text-white">{{ $v($p->tempat_tanggal_lahir) }}</dd>
                    </div>
                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">Nomor Handphone Aktif (WA)</dt>
                        <dd class="font-bold text-slate-900 dark:text-white font-mono">{{ $v($p->no_hp_aktif) }}</dd>
                    </div>
                    <div class="{{ $boxCls }} sm:col-span-2">
                        <dt class="{{ $labelCls }}">Alamat Tinggal (Domisili)</dt>
                        <dd class="font-medium text-slate-900 dark:text-white leading-relaxed">{{ $v($p->alamat_domisili) }}</dd>
                    </div>
                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">Nama Orang Tua (Wali)</dt>
                        <dd class="font-bold text-slate-900 dark:text-white">{{ $v($p->nama_ortu_wali) }}</dd>
                    </div>
                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">Nomor Handphone Orang Tua</dt>
                        <dd class="font-bold text-slate-900 dark:text-white font-mono">{{ $v($p->no_hp_ortu) }}</dd>
                    </div>
                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">Email Orang Tua / Wali <span class="font-normal">(login wali)</span></dt>
                        <dd class="font-medium text-slate-700 dark:text-slate-300 break-all">{{ $v($p->email) }}</dd>
                    </div>
                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">Pilihan Jenjang</dt>
                        <dd class="font-bold text-slate-900 dark:text-white">{{ $v($p->jenjang_tujuan) }}</dd>
                    </div>
                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">Sekolah Asal</dt>
                        <dd class="font-bold text-slate-900 dark:text-white">{{ $v($p->asal_sekolah) }}</dd>
                    </div>
                    <div class="{{ $boxCls }} sm:col-span-2">
                        <dt class="{{ $labelCls }}">Alamat Sekolah Asal</dt>
                        <dd class="font-medium text-slate-900 dark:text-white leading-relaxed">{{ $v($p->alamat_sekolah) }}</dd>
                    </div>
                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">Tahun Lulus</dt>
                        <dd class="font-bold text-slate-900 dark:text-white">{{ $v($p->tahun_lulus) }}</dd>
                    </div>
                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">NISN</dt>
                        <dd class="font-bold text-slate-900 dark:text-white font-mono">{{ $v($p->nisn) }}</dd>
                    </div>
                    <div class="{{ $boxCls }}">
                        <dt class="{{ $labelCls }}">Tanggal Mendaftar</dt>
                        <dd class="font-bold text-slate-900 dark:text-white">{{ optional($p->created_at)->translatedFormat('d F Y, H:i') ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        @else
            <div class="{{ $cardCls }} text-center py-10 text-slate-400 text-xs italic">
                Santri ini tidak memiliki arsip pendaftaran online.
            </div>
        @endif

        @if (auth()->user()->bisa('santri.ubah'))
            {{-- AKSI SANTRI --}}
            <div class="{{ $cardCls }}">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Aksi Data Santri</h2>
                        <p class="text-[11px] text-slate-400">Reset akun wali, pindah jenjang, naik kelas, dan arsip. Data tidak dihapus permanen kecuali oleh Superadmin.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    {{-- Reset password wali --}}
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-4 dark:border-slate-800 dark:bg-slate-800/40">
                        <h3 class="text-xs font-black text-slate-800 dark:text-slate-100"><i class="fa-solid fa-key mr-1 text-emerald-600"></i> Reset Password Wali</h3>
                        <p class="mt-1 text-[10px] leading-relaxed text-slate-500 dark:text-slate-400">
                            Password sementara baru berlaku 24 jam dan dikirim ke email wali. Admin tidak bisa melihat password.
                        </p>
                        @if ($santri->user)
                            <form action="{{ route('admin.santri.reset-wali', $santri->id) }}" method="POST" class="mt-3"
                                onsubmit="return confirm('Reset password akun wali {{ $santri->user->email }}?');">
                                @csrf
                                <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-[11px] font-black text-white hover:bg-emerald-700">
                                    <i class="fa-solid fa-rotate mr-1"></i> Reset &amp; Kirim Email
                                </button>
                            </form>
                        @else
                            <p class="mt-3 rounded-lg bg-amber-50 px-3 py-2 text-[10px] font-bold text-amber-700">Belum ada akun wali tertaut.</p>
                        @endif
                    </div>

                    {{-- Pindah jenjang --}}
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-4 dark:border-slate-800 dark:bg-slate-800/40">
                        <h3 class="text-xs font-black text-slate-800 dark:text-slate-100"><i class="fa-solid fa-right-left mr-1 text-sky-600"></i> Pindah Jenjang</h3>
                        <p class="mt-1 text-[10px] leading-relaxed text-slate-500 dark:text-slate-400">
                            Data, riwayat, dan akun wali tetap sama. Hanya jenjang/kelas yang dilanjutkan.
                        </p>
                        <form action="{{ route('admin.santri.pindah-jenjang', $santri->id) }}" method="POST" class="mt-3 space-y-2">
                            @csrf
                            <select name="jenjang" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                <option value="">— Jenjang tujuan —</option>
                                @foreach (\App\Support\Jenjang::semua() as $j)
                                    @if (\App\Support\Jenjang::normalisasi($santri->jenjang_pendidikan) !== $j['slug'])
                                        <option value="{{ $j['slug'] }}">{{ $j['kode'] }} — {{ $j['nama'] }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <input type="text" name="kelas" required maxlength="50" placeholder="Kelas tujuan (mis. 10A)"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            <input type="text" name="alasan" required maxlength="500" placeholder="Alasan pindah jenjang"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            <button type="submit" class="rounded-xl bg-sky-600 px-4 py-2 text-[11px] font-black text-white hover:bg-sky-700"
                                onclick="return confirm('Pindahkan santri ke jenjang tujuan?')">
                                <i class="fa-solid fa-arrow-right mr-1"></i> Proses Pindah
                            </button>
                        </form>
                    </div>

                    {{-- Naik kelas --}}
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-4 dark:border-slate-800 dark:bg-slate-800/40">
                        <h3 class="text-xs font-black text-slate-800 dark:text-slate-100"><i class="fa-solid fa-arrow-up-right-dots mr-1 text-indigo-600"></i> Naik / Tinggal Kelas</h3>
                        <p class="mt-1 text-[10px] leading-relaxed text-slate-500 dark:text-slate-400">
                            Proses massal per kelas. Nilai lama tetap terkunci. Rollback hanya oleh Superadmin dengan alasan.
                        </p>
                        <a href="{{ route('admin.santri.naik-kelas') }}"
                            class="mt-3 inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2 text-[11px] font-black text-white hover:bg-indigo-700">
                            <i class="fa-solid fa-arrow-up-right-dots"></i> Buka Proses Naik Kelas
                        </a>
                    </div>

                    {{-- Arsip --}}
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-4 dark:border-slate-800 dark:bg-slate-800/40">
                        <h3 class="text-xs font-black text-slate-800 dark:text-slate-100"><i class="fa-solid fa-box-archive mr-1 text-rose-600"></i> Arsipkan (Keluar / Berhenti)</h3>
                        <p class="mt-1 text-[10px] leading-relaxed text-slate-500 dark:text-slate-400">
                            Data tidak dihapus. Akun wali tetap aktif untuk melihat riwayat anak.
                        </p>
                        @if ($santri->sudah_lulus)
                            <p class="mt-3 rounded-lg bg-indigo-50 px-3 py-2 text-[10px] font-bold text-indigo-700">Santri sudah lulus — tidak perlu diarsipkan.</p>
                        @else
                            <form action="{{ route('admin.santri.arsipkan', $santri->id) }}" method="POST" class="mt-3 space-y-2"
                                onsubmit="return confirm('Arsipkan data santri ini?');">
                                @csrf
                                <select name="status" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                    <option value="keluar">Keluar / Berhenti</option>
                                    <option value="arsip">Diarsipkan</option>
                                </select>
                                <input type="text" name="alasan" required maxlength="500" placeholder="Alasan keluar/berhenti"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2 text-[11px] font-black text-white hover:bg-rose-700">
                                    <i class="fa-solid fa-box-archive mr-1"></i> Arsipkan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- RIWAYAT KELAS --}}
            <div class="{{ $cardCls }}">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Riwayat Kelas &amp; Jenjang</h2>
                        <p class="text-[11px] text-slate-400">Setiap kenaikan/pindah/keluar tercatat dan tidak terhapus.</p>
                    </div>
                </div>

                <div class="space-y-2">
                    @forelse ($santri->riwayatKelas as $r)
                        <div class="flex flex-col gap-2 rounded-xl border border-slate-100 bg-slate-50/60 px-3.5 py-2.5 dark:border-slate-800 dark:bg-slate-800/40 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-[11px] font-bold text-slate-800 dark:text-slate-100">
                                    {{ \App\Support\Jenjang::label($r->jenjang) }} · {{ $r->kelas ?: '-' }}
                                    @if ($r->kamar) · Kamar {{ $r->kamar }} @endif
                                </p>
                                <p class="text-[10px] text-slate-400">{{ $r->tahun_ajaran }} · Semester {{ $r->semester }}{{ $r->keterangan ? ' · '.$r->keterangan : '' }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="rounded-full bg-slate-200 px-2 py-0.5 text-[9px] font-black text-slate-600 dark:bg-slate-700 dark:text-slate-300">{{ $r->labelStatus() }}</span>
                                @if (\App\Support\Akses::superAdmin(auth()->user()) && $r->status !== 'dibatalkan')
                                    <form action="{{ route('admin.riwayat.rollback', $r) }}" method="POST"
                                        class="flex items-center gap-1"
                                        onsubmit="return confirm('Rollback kenaikan ini? Wajib alasan dan akan tercatat di log.');">
                                        @csrf
                                        <input type="text" name="alasan" required maxlength="500" placeholder="Alasan rollback"
                                            class="w-40 rounded-lg border border-rose-200 px-2 py-1 text-[10px] dark:border-rose-900/50 dark:bg-slate-900 dark:text-white">
                                        <button type="submit" class="rounded-lg bg-rose-600 px-2 py-1 text-[10px] font-black text-white hover:bg-rose-700">Rollback</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="py-6 text-center text-[11px] text-slate-400">Belum ada riwayat kelas.</p>
                    @endforelse
                </div>
            </div>
        @endif

        {{-- DOKUMEN KELULUSAN --}}
        <div class="{{ $cardCls }}">
            <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="w-10 h-10 rounded-2xl bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Dokumen Kelulusan</h2>
                    <p class="text-[11px] text-slate-400">Ijazah, SKL, transkrip, dan sertifikat diunggah Admin Jenjang; alumni hanya mengunduh.</p>
                </div>
            </div>

            @if (auth()->user()->bisa('alumni.ubah'))
                <form action="{{ route('admin.santri.dokumen.store', $santri->id) }}" method="POST" enctype="multipart/form-data"
                    class="mb-5 grid grid-cols-1 gap-3 rounded-2xl border border-slate-100 bg-slate-50/60 p-4 dark:border-slate-800 dark:bg-slate-800/40 sm:grid-cols-3">
                    @csrf
                    <div>
                        <label class="mb-1 block text-[10px] font-bold text-slate-500">Jenis Dokumen</label>
                        <select name="jenis" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            @foreach (\App\Models\DokumenSantri::JENIS as $kunci => $label)
                                <option value="{{ $kunci }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-[10px] font-bold text-slate-500">Berkas (PDF/JPG/PNG, maks 10 MB)</label>
                        <div class="flex gap-2">
                            <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-[11px] dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            <button type="submit" class="shrink-0 rounded-xl bg-sky-600 px-4 py-2 text-[11px] font-black text-white hover:bg-sky-700">
                                <i class="fa-solid fa-upload mr-1"></i> Unggah
                            </button>
                        </div>
                    </div>
                </form>
            @endif

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @php $dokumenTersedia = $santri->dokumen->keyBy('jenis'); @endphp
                @foreach (\App\Models\DokumenSantri::JENIS as $kunci => $label)
                    @php $d = $dokumenTersedia->get($kunci); @endphp
                    <div class="rounded-2xl border {{ $d ? 'border-emerald-200 bg-emerald-50/50' : 'border-dashed border-slate-200 bg-slate-50/50' }} p-3.5">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-[11px] font-black text-slate-800 dark:text-slate-100">{{ $label }}</p>
                                @if ($d)
                                    <p class="mt-0.5 truncate text-[10px] text-slate-500">{{ $d->file_name ?: 'Berkas' }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $d->ukuranManusia() }}</p>
                                @else
                                    <p class="mt-0.5 text-[10px] text-slate-400">Belum diunggah.</p>
                                @endif
                            </div>
                            @if ($d)
                                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[9px] font-black text-emerald-700">Ada</span>
                            @endif
                        </div>
                        @if ($d)
                            <div class="mt-2 flex items-center gap-2">
                                <a href="{{ asset('storage/'.$d->file_path) }}" target="_blank" rel="noopener"
                                    class="inline-flex items-center gap-1 rounded-lg bg-emerald-700 px-2.5 py-1.5 text-[10px] font-bold text-white hover:bg-emerald-800">
                                    <i class="fa-solid fa-eye"></i> Lihat
                                </a>
                                @if (auth()->user()->bisa('alumni.ubah'))
                                    <form action="{{ route('admin.santri.dokumen.hapus', $d) }}" method="POST" onsubmit="return confirm('Hapus dokumen ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-rose-50 px-2.5 py-1.5 text-[10px] font-bold text-rose-700 hover:bg-rose-100 dark:bg-rose-500/10 dark:text-rose-300">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

    </div>
@endsection
