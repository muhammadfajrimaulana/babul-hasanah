@extends('layouts.admin')

@section('title', 'Dashboard Admin Jenjang - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-6">

        @include('dashboard.roles._hero', [
            'badge' => 'Admin Jenjang',
            'ikon' => 'fa-layer-group',
            'judul' => 'Dashboard Admin Jenjang',
            'deskripsi' => 'Kelola pendaftaran, data santri, alumni, dan prestasi pada jenjang yang menjadi tanggung jawab Anda. Data di halaman ini otomatis dibatasi ke jenjang tersebut.',
            'identitas' => $jenjang ? $jenjang['kode'].' — '.$jenjang['nama'] : 'Belum ditautkan',
        ])

        @unless ($jenjang)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-xs text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-200">
                <p class="font-black"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Akun belum ditautkan ke jenjang.</p>
                <p class="mt-1 leading-relaxed">Hubungi Super Admin agar akun ini diberi jenjang melalui menu <b>Pengguna</b>. Selama belum diatur, dashboard tidak menampilkan data agar tidak tercampur dengan jenjang lain.</p>
            </div>
        @endunless

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @include('dashboard.roles._stat', [
                'label' => 'Santri Jenjang',
                'nilai' => number_format($totalSantri, 0, ',', '.'),
                'sub' => $jenjang['nama'] ?? 'Belum ditentukan',
                'ikon' => 'fa-people-group',
                'href' => route('admin.santri.index'),
            ])
            @include('dashboard.roles._stat', [
                'label' => 'Pendaftar',
                'nilai' => number_format($totalPendaftar, 0, ',', '.'),
                'sub' => 'Total PPDB jenjang ini',
                'ikon' => 'fa-address-book',
                'ikonBg' => 'bg-sky-50 text-sky-600 dark:bg-sky-950/50 dark:text-sky-400',
                'href' => route('admin.pendaftaran.index'),
            ])
            @include('dashboard.roles._stat', [
                'label' => 'Menunggu Verifikasi',
                'nilai' => number_format($pendaftarMenunggu, 0, ',', '.'),
                'sub' => 'Perlu ditindaklanjuti',
                'ikon' => 'fa-clock',
                'ikonBg' => 'bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400',
                'href' => route('admin.pendaftaran.index'),
            ])
            @include('dashboard.roles._stat', [
                'label' => 'Alumni Jenjang',
                'nilai' => number_format($totalAlumni, 0, ',', '.'),
                'sub' => 'Riwayat lulusan',
                'ikon' => 'fa-user-graduate',
                'ikonBg' => 'bg-violet-50 text-violet-600 dark:bg-violet-950/50 dark:text-violet-400',
                'href' => route('admin.alumni.index'),
            ])
        </div>

        {{-- Aksi cepat --}}
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-3 flex items-center justify-between gap-3">
                <h2 class="text-sm font-black text-slate-900 dark:text-white">Aksi Cepat</h2>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Sesuai hak akses</span>
            </div>
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-5">
                <a href="{{ route('admin.pendaftaran.index') }}"
                    class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-3 py-3 text-[11px] font-bold text-slate-700 transition hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-200">
                    <i class="fa-solid fa-address-book text-emerald-600"></i> Verifikasi PPDB
                </a>
                <a href="{{ route('admin.santri.index') }}"
                    class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-3 py-3 text-[11px] font-bold text-slate-700 transition hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-200">
                    <i class="fa-solid fa-people-group text-emerald-600"></i> Data Santri
                </a>
                <a href="{{ route('admin.alumni.index') }}"
                    class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-3 py-3 text-[11px] font-bold text-slate-700 transition hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-200">
                    <i class="fa-solid fa-user-graduate text-emerald-600"></i> Data Alumni
                </a>
                <a href="{{ route('admin.prestasi.index') }}"
                    class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-3 py-3 text-[11px] font-bold text-slate-700 transition hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-200">
                    <i class="fa-solid fa-trophy text-emerald-600"></i> Prestasi
                </a>
                <a href="{{ route('admin.jenjang.show', $jenjang['slug'] ?? 'mdta') }}"
                    class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-3 py-3 text-[11px] font-bold text-slate-700 transition hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-200">
                    <i class="fa-solid fa-layer-group text-emerald-600"></i> Profil Jenjang
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            {{-- Pendaftar terbaru --}}
            <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                    <div>
                        <h2 class="text-sm font-black text-slate-900 dark:text-white">Pendaftar Terbaru</h2>
                        <p class="text-[10px] text-slate-400">Jenjang {{ $jenjang['nama'] ?? '-' }}</p>
                    </div>
                    <a href="{{ route('admin.pendaftaran.index') }}" class="text-[10px] font-bold text-emerald-600 hover:underline">Lihat semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-400 dark:bg-slate-800/50">
                                <th class="px-5 py-3 font-extrabold">Nama</th>
                                <th class="px-5 py-3 font-extrabold">Status</th>
                                <th class="px-5 py-3 font-extrabold">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse ($pendaftarTerbaru as $p)
                                @php
                                    $status = strtolower((string) ($p->status_pendaftaran ?? 'menunggu'));
                                    $statusClass = match (true) {
                                        in_array($status, ['diterima', 'lulus'], true) => 'bg-emerald-50 text-emerald-700',
                                        in_array($status, ['ditolak', 'gagal'], true) => 'bg-red-50 text-red-600',
                                        default => 'bg-amber-50 text-amber-700',
                                    };
                                @endphp
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                    <td class="px-5 py-3 font-bold text-slate-700 dark:text-slate-200">{{ $p->nama_lengkap ?? '-' }}</td>
                                    <td class="px-5 py-3">
                                        <span class="rounded-lg px-2 py-1 text-[10px] font-bold {{ $statusClass }}">{{ ucfirst($status ?: 'menunggu') }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-slate-500">{{ $p->created_at?->format('d/m/Y') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-5 py-8 text-center text-[11px] text-slate-400">Belum ada pendaftar untuk jenjang ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Santri terbaru --}}
            <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                    <div>
                        <h2 class="text-sm font-black text-slate-900 dark:text-white">Santri Terbaru</h2>
                        <p class="text-[10px] text-slate-400">5 data terakhir</p>
                    </div>
                    <a href="{{ route('admin.santri.index') }}" class="text-[10px] font-bold text-emerald-600 hover:underline">Lihat semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-400 dark:bg-slate-800/50">
                                <th class="px-5 py-3 font-extrabold">Nama</th>
                                <th class="px-5 py-3 font-extrabold">Kelas</th>
                                <th class="px-5 py-3 font-extrabold">Induk</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse ($santriTerbaru as $s)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                    <td class="px-5 py-3 font-bold text-slate-700 dark:text-slate-200">
                                        <a href="{{ route('admin.santri.detail', $s->id) }}" class="hover:text-emerald-600">{{ $s->nama_lengkap ?? '-' }}</a>
                                    </td>
                                    <td class="px-5 py-3 text-slate-500">{{ $s->kelas ?? '-' }}</td>
                                    <td class="px-5 py-3 text-slate-500">{{ $s->nomor_induk ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-5 py-8 text-center text-[11px] text-slate-400">Belum ada data santri.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-[11px] leading-relaxed text-slate-500 dark:text-slate-400">
                <i class="fa-solid fa-circle-info mr-1 text-emerald-600"></i>
                <b>Batas akses Admin Jenjang:</b> pendaftaran, santri, alumni, prestasi, berita, dan galeri.
                Menu <b>Pengguna</b>, <b>Info Harian</b>, <b>Konten</b>, dan <b>Struktur Organisasi</b> hanya untuk Super Admin.
                Anda tidak dapat membuka dashboard role lain.
            </p>
        </div>
    </div>
@endsection
