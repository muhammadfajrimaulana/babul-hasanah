@extends('layouts.portal')

@section('content')
    @php
        $label = 'text-[10px] font-black uppercase tracking-wider text-slate-400';
        $box = 'rounded-2xl border border-slate-100 bg-slate-50/60 p-3.5';
        $card = 'rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm';
        $statusBadge = match ($santri->status_kelulusan) {
            'lulus' => 'bg-indigo-100 text-indigo-700',
            'keluar' => 'bg-rose-100 text-rose-700',
            'arsip' => 'bg-slate-200 text-slate-600',
            default => 'bg-emerald-100 text-emerald-700',
        };
        $presensi = $santri->absensi->groupBy('status_kehadiran');
    @endphp

    <div class="space-y-6">
        {{-- HEADER --}}
        <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-start gap-4">
                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 text-xl">
                        <i class="fa-solid fa-user-graduate"></i>
                    </span>
                    <div>
                        <div class="mb-1 flex flex-wrap items-center gap-2">
                            <span class="rounded-full px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider {{ $statusBadge }}">{{ $santri->labelStatus() }}</span>
                            <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold text-slate-600">{{ \App\Support\Jenjang::label($santri->jenjang_pendidikan) ?: 'Jenjang Umum' }}</span>
                        </div>
                        <h2 class="text-2xl font-black text-slate-900">{{ $santri->nama_lengkap }}</h2>
                        <p class="text-xs text-slate-500">
                            @if ($santri->nomor_induk) NI {{ $santri->nomor_induk }} · @endif
                            Kelas {{ $santri->kelas ?: '-' }}
                            @if ($santri->kamar) · Kamar {{ $santri->kamar }} @endif
                            @if ($santri->kelasModel?->waliKelas) · Wali Kelas {{ $santri->kelasModel->waliKelas->nama }} @endif
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('portal.pengajuan.index') }}"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-amber-200 bg-amber-50 px-3.5 py-2 text-[11px] font-bold text-amber-700 transition hover:bg-amber-100">
                        <i class="fa-solid fa-pen-to-square"></i> Ajukan Perubahan Data
                    </a>
                    <a href="{{ route('portal.izin.index') }}"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-sky-200 bg-sky-50 px-3.5 py-2 text-[11px] font-bold text-sky-700 transition hover:bg-sky-100">
                        <i class="fa-solid fa-plane-departure"></i> Ajukan Izin Pulang
                    </a>
                    <a href="{{ route('portal.dashboard') }}"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-[11px] font-bold text-slate-600 hover:bg-slate-50">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            @if ($anakList->count() > 1)
                <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-3">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Anak lain:</span>
                    @foreach ($anakList as $lain)
                        @if ($lain->id !== $santri->id)
                            <a href="{{ route('portal.anak.show', $lain) }}"
                                class="rounded-lg bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-600 hover:bg-emerald-100 hover:text-emerald-700">
                                {{ $lain->nama_lengkap }}
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif

            <p class="mt-4 rounded-xl bg-slate-50 px-3 py-2 text-[10px] leading-relaxed text-slate-500">
                <i class="fa-solid fa-lock mr-1 text-slate-400"></i>
                Halaman ini hanya-baca. Perubahan data harus diajukan ke Admin Jenjang melalui tombol di atas.
            </p>
        </div>

        {{-- TAB --}}
        <div x-data="{ tab: 'ringkasan' }" class="space-y-4">
            <div class="flex flex-wrap gap-1.5 rounded-2xl border border-slate-200/80 bg-white p-2 shadow-sm">
                @php
                    $tabItems = [
                        'ringkasan' => 'Ringkasan',
                        'nilai' => 'Nilai',
                        'hafalan' => 'Hafalan',
                        'presensi' => 'Presensi',
                        'pelanggaran' => 'Pelanggaran',
                        'izin' => 'Izin',
                        'riwayat' => 'Riwayat Kelas',
                    ];
                @endphp
                @foreach ($tabItems as $kunci => $teks)
                    <button type="button" @click="tab = '{{ $kunci }}'"
                        :class="tab === '{{ $kunci }}' ? 'bg-emerald-700 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'"
                        class="rounded-xl px-3.5 py-2 text-[11px] font-bold transition">
                        {{ $teks }}
                    </button>
                @endforeach
            </div>

            {{-- RINGKASAN --}}
            <div x-show="tab === 'ringkasan'" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="{{ $card }} lg:col-span-2">
                    <h3 class="mb-4 text-sm font-black text-slate-900">Profil Santri</h3>
                    <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="{{ $box }}"><dt class="{{ $label }}">Nama Lengkap</dt><dd class="mt-0.5 text-xs font-bold text-slate-800">{{ $santri->nama_lengkap }}</dd></div>
                        <div class="{{ $box }}"><dt class="{{ $label }}">Nomor Induk</dt><dd class="mt-0.5 font-mono text-xs font-bold text-slate-800">{{ $santri->nomor_induk ?: '-' }}</dd></div>
                        <div class="{{ $box }}"><dt class="{{ $label }}">Jenjang</dt><dd class="mt-0.5 text-xs font-bold text-slate-800">{{ \App\Support\Jenjang::label($santri->jenjang_pendidikan) ?: '-' }}</dd></div>
                        <div class="{{ $box }}"><dt class="{{ $label }}">Kelas / Kamar</dt><dd class="mt-0.5 text-xs font-bold text-slate-800">{{ $santri->kelas ?: '-' }} / {{ $santri->kamar ?: '-' }}</dd></div>
                        <div class="{{ $box }}"><dt class="{{ $label }}">Wali Kelas</dt><dd class="mt-0.5 text-xs font-bold text-slate-800">{{ $santri->kelasModel?->waliKelas?->nama ?? 'Belum ditentukan' }}</dd></div>
                        <div class="{{ $box }}"><dt class="{{ $label }}">Nama Wali</dt><dd class="mt-0.5 text-xs font-bold text-slate-800">{{ $santri->nama_wali ?: '-' }}</dd></div>
                        <div class="{{ $box }} sm:col-span-2"><dt class="{{ $label }}">Alamat</dt><dd class="mt-0.5 text-xs font-medium leading-relaxed text-slate-700">{{ $santri->alamat ?: '-' }}</dd></div>
                        @if ($santri->alumni)
                            <div class="{{ $box }}"><dt class="{{ $label }}">Tahun Lulus</dt><dd class="mt-0.5 text-xs font-bold text-slate-800">{{ $santri->alumni->tahun_lulus ?: '-' }}</dd></div>
                            <div class="{{ $box }}"><dt class="{{ $label }}">Nomor Ijazah</dt><dd class="mt-0.5 font-mono text-xs font-bold text-slate-800">{{ $santri->alumni->nomor_ijazah ?: '-' }}</dd></div>
                        @endif
                    </dl>
                </div>

                <div class="{{ $card }}">
                    <h3 class="mb-4 text-sm font-black text-slate-900">Ringkasan Cepat</h3>
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between rounded-xl bg-emerald-50 px-3.5 py-2.5">
                            <span class="text-[11px] font-bold text-emerald-700"><i class="fa-solid fa-book-quran mr-1.5"></i> Total Hafalan</span>
                            <span class="text-sm font-black text-emerald-800">{{ $santri->hafalan->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl bg-sky-50 px-3.5 py-2.5">
                            <span class="text-[11px] font-bold text-sky-700"><i class="fa-solid fa-clipboard-check mr-1.5"></i> Presensi Tercatat</span>
                            <span class="text-sm font-black text-sky-800">{{ $santri->absensi->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl bg-rose-50 px-3.5 py-2.5">
                            <span class="text-[11px] font-bold text-rose-700"><i class="fa-solid fa-triangle-exclamation mr-1.5"></i> Pelanggaran</span>
                            <span class="text-sm font-black text-rose-800">{{ $santri->pelanggaran->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl bg-amber-50 px-3.5 py-2.5">
                            <span class="text-[11px] font-bold text-amber-700"><i class="fa-solid fa-file-lines mr-1.5"></i> Nilai</span>
                            <span class="text-sm font-black text-amber-800">{{ $santri->nilai->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- NILAI --}}
            <div x-show="tab === 'nilai'" class="{{ $card }}">
                <h3 class="mb-3 text-sm font-black text-slate-900">Nilai Santri</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-400">
                                <th class="px-3 py-2.5 font-extrabold">Mata Pelajaran</th>
                                <th class="px-3 py-2.5 font-extrabold">Tahun / Semester</th>
                                <th class="px-3 py-2.5 font-extrabold">Nilai</th>
                                <th class="px-3 py-2.5 font-extrabold">Predikat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($santri->nilai as $n)
                                <tr>
                                    <td class="px-3 py-2.5 font-bold text-slate-700">{{ $n->mataPelajaran?->nama ?? '-' }}</td>
                                    <td class="px-3 py-2.5 text-slate-500">{{ $n->tahun_ajaran ?: '-' }} · Semester {{ $n->semester ?: '-' }}</td>
                                    <td class="px-3 py-2.5 font-black text-emerald-700">{{ $n->nilai !== null ? rtrim(rtrim(number_format((float) $n->nilai, 2, ',', '.'), '0'), ',') : '-' }}</td>
                                    <td class="px-3 py-2.5 text-slate-500">{{ $n->predikat ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-3 py-8 text-center text-[11px] text-slate-400">Belum ada nilai.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- HAFALAN --}}
            <div x-show="tab === 'hafalan'" class="{{ $card }}">
                <h3 class="mb-3 text-sm font-black text-slate-900">Hafalan / Setoran</h3>
                <div class="space-y-2">
                    @forelse ($santri->hafalan as $h)
                        <div class="{{ $box }}">
                            <p class="text-[11px] font-bold text-slate-800">{{ $h->labelJenis() }} · {{ $h->surat ?: '-' }} {{ $h->ayat ? 'ayat '.$h->ayat : '' }} {{ $h->juz ? '· Juz '.$h->juz : '' }}</p>
                            <p class="text-[10px] text-slate-400">{{ $h->tanggal?->translatedFormat('d M Y') }} @if ($h->nilai !== null) · Nilai {{ $h->nilai }} @endif @if ($h->predikat) · {{ $h->predikat }} @endif</p>
                            @if ($h->catatan)<p class="mt-0.5 text-[10px] text-slate-500">{{ $h->catatan }}</p>@endif
                        </div>
                    @empty
                        <p class="py-6 text-center text-[11px] text-slate-400">Belum ada catatan hafalan.</p>
                    @endforelse
                </div>
            </div>

            {{-- PRESENSI --}}
            <div x-show="tab === 'presensi'" class="{{ $card }}">
                <h3 class="mb-3 text-sm font-black text-slate-900">Presensi Kegiatan</h3>
                <div class="mb-4 flex flex-wrap gap-2">
                    @foreach (['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa'] as $kunci => $teks)
                        <span class="rounded-xl bg-slate-100 px-3 py-1.5 text-[10px] font-bold text-slate-600">{{ $teks }}: {{ $presensi->get($kunci)?->count() ?? 0 }}</span>
                    @endforeach
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-400">
                                <th class="px-3 py-2.5 font-extrabold">Tanggal</th>
                                <th class="px-3 py-2.5 font-extrabold">Kegiatan</th>
                                <th class="px-3 py-2.5 font-extrabold">Status</th>
                                <th class="px-3 py-2.5 font-extrabold">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($santri->absensi as $ab)
                                <tr>
                                    <td class="px-3 py-2.5 text-slate-500">{{ $ab->tanggal?->translatedFormat('d M Y') ?? '-' }}</td>
                                    <td class="px-3 py-2.5 font-bold text-slate-700">{{ $ab->jenis_kegiatan ?? '-' }}</td>
                                    <td class="px-3 py-2.5 text-slate-600">{{ ucfirst($ab->status_kehadiran ?? '-') }}</td>
                                    <td class="px-3 py-2.5 text-slate-500">{{ $ab->keterangan ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-3 py-8 text-center text-[11px] text-slate-400">Belum ada presensi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- PELANGGARAN --}}
            <div x-show="tab === 'pelanggaran'" class="{{ $card }}">
                <h3 class="mb-3 text-sm font-black text-slate-900">Riwayat Pelanggaran</h3>
                <div class="space-y-2">
                    @forelse ($santri->pelanggaran as $pel)
                        <div class="rounded-2xl border border-rose-100 bg-rose-50/50 p-3.5">
                            <p class="text-[11px] font-bold text-slate-800">{{ $pel->jenis }} <span class="ml-1 rounded-full bg-rose-100 px-2 py-0.5 text-[9px] font-black text-rose-700">{{ $pel->poin }} poin</span></p>
                            <p class="text-[10px] text-slate-400">{{ $pel->tanggal?->translatedFormat('d M Y') }}</p>
                            @if ($pel->deskripsi)<p class="mt-0.5 text-[10px] text-slate-600">{{ $pel->deskripsi }}</p>@endif
                            @if ($pel->tindakan)<p class="mt-0.5 text-[10px] text-emerald-600">Tindakan: {{ $pel->tindakan }}</p>@endif
                        </div>
                    @empty
                        <p class="py-6 text-center text-[11px] text-slate-400">Tidak ada catatan pelanggaran.</p>
                    @endforelse
                </div>
            </div>

            {{-- IZIN --}}
            <div x-show="tab === 'izin'" class="{{ $card }}">
                <h3 class="mb-3 text-sm font-black text-slate-900">Izin Pulang</h3>
                <div class="space-y-2">
                    @forelse ($santri->izin as $iz)
                        <div class="{{ $box }}">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-lg border px-2 py-0.5 text-[10px] font-black uppercase {{ $iz->kelasStatus() }}">{{ $iz->labelStatus() }}</span>
                                <span class="text-[10px] text-slate-400">{{ $iz->tanggal_pulang?->translatedFormat('d M') }} – {{ $iz->tanggal_kembali?->translatedFormat('d M Y') }}</span>
                            </div>
                            <p class="mt-1 text-[11px] text-slate-600">{{ $iz->alasan }}</p>
                            @if ($iz->alasan_penolakan)<p class="mt-1 text-[10px] text-rose-600">Ditolak: {{ $iz->alasan_penolakan }}</p>@endif
                        </div>
                    @empty
                        <p class="py-6 text-center text-[11px] text-slate-400">Belum ada izin.</p>
                    @endforelse
                </div>
            </div>

            {{-- RIWAYAT KELAS --}}
            <div x-show="tab === 'riwayat'" class="{{ $card }}">
                <h3 class="mb-3 text-sm font-black text-slate-900">Riwayat Kelas &amp; Jenjang</h3>
                <div class="space-y-2">
                    @forelse ($santri->riwayatKelas as $r)
                        <div class="flex flex-wrap items-center justify-between gap-2 rounded-2xl border border-slate-100 bg-slate-50/60 px-3.5 py-2.5">
                            <div>
                                <p class="text-[11px] font-bold text-slate-800">
                                    {{ \App\Support\Jenjang::label($r->jenjang) }} · {{ $r->kelas ?: '-' }}
                                    @if ($r->kamar) · Kamar {{ $r->kamar }} @endif
                                </p>
                                <p class="text-[10px] text-slate-400">{{ $r->tahun_ajaran }} · Semester {{ $r->semester }}{{ $r->keterangan ? ' · '.$r->keterangan : '' }}</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[9px] font-black text-slate-600">{{ $r->labelStatus() }}</span>
                        </div>
                    @empty
                        <p class="py-6 text-center text-[11px] text-slate-400">Belum ada riwayat kelas.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
