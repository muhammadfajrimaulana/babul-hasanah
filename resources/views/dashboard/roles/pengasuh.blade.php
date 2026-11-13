@extends('layouts.admin')

@section('title', 'Dashboard Pengasuh - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-6">

        @include('dashboard.roles._hero', [
            'badge' => 'Pengasuh',
            'ikon' => 'fa-mosque',
            'judul' => 'Dashboard Pengasuh Pesantren',
            'deskripsi' => 'Pantau perkembangan pesantren secara menyeluruh: jumlah santri, pendaftar baru, alumni, prestasi, dan sebaran per jenjang. Halaman ini bersifat monitoring (hanya baca).',
        ])

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @include('dashboard.roles._stat', [
                'label' => 'Total Santri',
                'nilai' => number_format($totalSantri, 0, ',', '.'),
                'sub' => 'Seluruh jenjang',
                'ikon' => 'fa-people-group',
                'href' => route('admin.santri.index'),
            ])
            @include('dashboard.roles._stat', [
                'label' => 'Pendaftar Baru',
                'nilai' => number_format($totalPendaftar, 0, ',', '.'),
                'sub' => $pendaftarMenunggu.' menunggu verifikasi',
                'ikon' => 'fa-address-book',
                'ikonBg' => 'bg-sky-50 text-sky-600 dark:bg-sky-950/50 dark:text-sky-400',
                'href' => route('admin.pendaftaran.index'),
            ])
            @include('dashboard.roles._stat', [
                'label' => 'Alumni',
                'nilai' => number_format($totalAlumni, 0, ',', '.'),
                'sub' => 'Lulusan tercatat',
                'ikon' => 'fa-user-graduate',
                'ikonBg' => 'bg-violet-50 text-violet-600 dark:bg-violet-950/50 dark:text-violet-400',
                'href' => route('admin.alumni.index'),
            ])
            @include('dashboard.roles._stat', [
                'label' => 'Prestasi Santri',
                'nilai' => number_format($totalPrestasi, 0, ',', '.'),
                'sub' => 'Seluruh jenjang',
                'ikon' => 'fa-trophy',
                'ikonBg' => 'bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400',
                'href' => route('admin.prestasi.index'),
            ])
            @include('dashboard.roles._stat', [
                'label' => 'Asatidz / Pengurus',
                'nilai' => number_format($totalAsatidz, 0, ',', '.'),
                'sub' => 'Data kepengurusan',
                'ikon' => 'fa-user-tie',
                'ikonBg' => 'bg-cyan-50 text-cyan-600 dark:bg-cyan-950/50 dark:text-cyan-400',
            ])
            @include('dashboard.roles._stat', [
                'label' => 'Berita Terbit',
                'nilai' => number_format($totalBerita, 0, ',', '.'),
                'sub' => 'Website pesantren',
                'ikon' => 'fa-newspaper',
                'ikonBg' => 'bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400',
            ])
        </div>

        {{-- Sebaran per jenjang --}}
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm font-black text-slate-900 dark:text-white">Sebaran per Jenjang</h2>
                    <p class="text-[10px] text-slate-400">Santri, pendaftar, dan alumni pada 7 jenjang resmi.</p>
                </div>
                <a href="{{ route('admin.jenjang.index') }}" class="text-[10px] font-bold text-emerald-600 hover:underline">Kelola data jenjang</a>
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($jenjangStats as $j)
                    <a href="{{ route('admin.jenjang.show', $j['slug']) }}"
                        class="rounded-xl border border-slate-100 bg-slate-50/60 p-4 transition hover:border-emerald-200 hover:bg-white dark:border-slate-800 dark:bg-slate-800/40 dark:hover:bg-slate-800">
                        <div class="flex items-center gap-2">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg text-white" style="background: {{ $j['warna'] }}">
                                <i class="fa-solid {{ $j['ikon'] }} text-[11px]"></i>
                            </span>
                            <p class="text-[11px] font-black text-slate-800 dark:text-slate-100">{{ $j['kode'] }}</p>
                        </div>
                        <div class="mt-3 grid grid-cols-3 gap-1 text-center">
                            <div>
                                <p class="text-sm font-black text-slate-900 dark:text-white">{{ number_format($j['santri'], 0, ',', '.') }}</p>
                                <p class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Santri</p>
                            </div>
                            <div>
                                <p class="text-sm font-black text-slate-900 dark:text-white">{{ number_format($j['pendaftar'], 0, ',', '.') }}</p>
                                <p class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Daftar</p>
                            </div>
                            <div>
                                <p class="text-sm font-black text-slate-900 dark:text-white">{{ number_format($j['alumni'], 0, ',', '.') }}</p>
                                <p class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Alumni</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Pendaftar terbaru --}}
        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                <div>
                    <h2 class="text-sm font-black text-slate-900 dark:text-white">Pendaftar Terbaru</h2>
                    <p class="text-[10px] text-slate-400">Seluruh jenjang</p>
                </div>
                <a href="{{ route('admin.pendaftaran.index') }}" class="text-[10px] font-bold text-emerald-600 hover:underline">Lihat semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-400 dark:bg-slate-800/50">
                            <th class="px-5 py-3 font-extrabold">Nama</th>
                            <th class="px-5 py-3 font-extrabold">Jenjang Tujuan</th>
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
                                <td class="px-5 py-3 text-slate-500">{{ $p->jenjang_tujuan ?? '-' }}</td>
                                <td class="px-5 py-3">
                                    <span class="rounded-lg px-2 py-1 text-[10px] font-bold {{ $statusClass }}">{{ ucfirst($status ?: 'menunggu') }}</span>
                                </td>
                                <td class="px-5 py-3 text-slate-500">{{ $p->created_at?->format('d/m/Y') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-8 text-center text-[11px] text-slate-400">Belum ada data pendaftar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-[11px] leading-relaxed text-slate-500 dark:text-slate-400">
                <i class="fa-solid fa-circle-info mr-1 text-emerald-600"></i>
                <b>Batas akses Pengasuh:</b> hanya membaca data pendaftaran, santri, alumni, prestasi, dan jenjang.
                Tombol tambah/ubah/hapus tidak tersedia; percobaan membuka aksi tersebut akan ditolak (403).
            </p>
        </div>
    </div>
@endsection
