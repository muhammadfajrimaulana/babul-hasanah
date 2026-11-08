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
                <a href="{{ route('admin.santri.edit', $santri->id) }}"
                    class="bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 dark:hover:bg-amber-500/20 text-amber-700 dark:text-amber-400 px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2">
                    <i class="fa-solid fa-pen"></i> Edit
                </a>
                <form action="{{ route('admin.santri.destroy', $santri->id) }}" method="POST"
                    onsubmit="return confirm('Hapus data santri ini? Status pendaftaran terkait akan kembali menjadi Pending.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="bg-rose-50 dark:bg-rose-500/10 hover:bg-rose-100 dark:hover:bg-rose-500/20 text-rose-700 dark:text-rose-400 px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2">
                        <i class="fa-solid fa-trash"></i> Hapus
                    </button>
                </form>
                <a href="{{ route('admin.santri.index') }}"
                    class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>
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

    </div>
@endsection
