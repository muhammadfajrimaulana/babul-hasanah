@extends('layouts.admin')

@section('title', 'Kegiatan Pesantren - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white">Kegiatan Pesantren</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Monitoring agenda, catatan kalender, dan kegiatan harian santri. Halaman ini bersifat
                hanya baca dan mengikuti batas jenjang Anda ({{ $namaJenjang }}).
            </p>
        </div>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <!-- AGENDA / JADWAL -->
            <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-4 flex items-center gap-3 border-b border-slate-100 pb-3 dark:border-slate-800">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                        <i class="fa-solid fa-calendar-day"></i>
                    </span>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white">Agenda Pesantren</h3>
                        <p class="text-[10px] text-slate-400">Jadwal aktif dari Info Harian</p>
                    </div>
                </div>

                @if ($jadwal->isNotEmpty())
                    <ul class="space-y-2">
                        @foreach ($jadwal as $item)
                            <li class="flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2.5 dark:border-slate-800 dark:bg-slate-800/40">
                                <i class="fa-solid {{ $item->ikon }} mt-0.5 text-xs text-emerald-600 dark:text-emerald-400"></i>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ $item->judul }}</p>
                                    <p class="mt-0.5 text-[10px] text-slate-400">
                                        {{ $item->jam ?: 'Jam tidak ditentukan' }}
                                        @if ($item->kategori) · {{ $item->kategori }} @endif
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="py-6 text-center text-[11px] text-slate-400">Belum ada jadwal aktif.</p>
                @endif
            </section>

            <!-- CATATAN KALENDER -->
            <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-4 flex items-center gap-3 border-b border-slate-100 pb-3 dark:border-slate-800">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                        <i class="fa-solid fa-note-sticky"></i>
                    </span>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white">Catatan Kalender Saya</h3>
                        <p class="text-[10px] text-slate-400">Catatan mendatang milik akun Anda</p>
                    </div>
                </div>

                @if ($catatan->isNotEmpty())
                    <ul class="space-y-2">
                        @foreach ($catatan as $item)
                            <li class="flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2.5 dark:border-slate-800 dark:bg-slate-800/40">
                                <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full" style="background: {{ $item->warna ?: '#10b981' }}"></span>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ $item->catatan }}</p>
                                    <p class="mt-0.5 text-[10px] text-slate-400">{{ $item->tanggal?->translatedFormat('D, d M Y') }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="py-6 text-center text-[11px] text-slate-400">Tidak ada catatan mendatang.</p>
                @endif
            </section>
        </div>

        <!-- ABSENSI / AKTIVITAS SANTRI -->
        <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-4 flex items-center gap-3 border-b border-slate-100 pb-3 dark:border-slate-800">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400">
                    <i class="fa-solid fa-clipboard-check"></i>
                </span>
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Aktivitas Kegiatan Santri</h3>
                    <p class="text-[10px] text-slate-400">Catatan presensi kegiatan terbaru pada jenjang Anda</p>
                </div>
            </div>

            @if ($absensi->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-[10px] uppercase tracking-wider text-slate-400 dark:border-slate-800">
                                <th class="px-3 py-2">Tanggal</th>
                                <th class="px-3 py-2">Santri</th>
                                <th class="px-3 py-2">Kegiatan</th>
                                <th class="px-3 py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($absensi as $item)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
                                    <td class="px-3 py-2.5 text-slate-500">{{ $item->tanggal ?? '-' }}</td>
                                    <td class="px-3 py-2.5">
                                        <p class="font-bold text-slate-800 dark:text-slate-100">{{ $item->santri?->nama_lengkap ?? '-' }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $item->santri?->kelas ?? '-' }}</p>
                                    </td>
                                    <td class="px-3 py-2.5 font-bold text-slate-700 dark:text-slate-200">{{ $item->jenis_kegiatan ?? 'Kegiatan' }}</td>
                                    <td class="px-3 py-2.5 text-slate-500">{{ $item->status_kehadiran ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-6 py-12 text-center dark:border-slate-700 dark:bg-slate-900">
                    <i class="fa-solid fa-calendar-days mb-3 block text-3xl text-slate-300"></i>
                    <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum ada aktivitas kegiatan yang tercatat.</p>
                    <p class="mt-1 text-[11px] text-slate-400">
                        Kegiatan harian dicatat melalui menu Pembinaan. Data presensi dan setoran juga tampil pada detail santri.
                    </p>
                </div>
            @endif
        </section>
    </div>
@endsection
