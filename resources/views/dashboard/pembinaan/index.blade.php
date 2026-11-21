@extends('layouts.admin')

@section('title', 'Pembinaan Santri - Pondok Pesantren Babul Hasanah')

@section('content')
    @php
        $in = 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500';
        $lb = 'mb-1.5 block text-[11px] font-bold text-slate-600 dark:text-slate-300';
        $card = 'rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900';
    @endphp

    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white">Pembinaan Santri</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Input hafalan/setoran, presensi kegiatan, dan catatan pelanggaran. Pembina melayani seluruh jenjang; Admin Jenjang dibatasi ke jenjangnya.
            </p>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-xs font-bold text-emerald-700">
                <i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-xs font-bold text-rose-700">
                <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ session('error') }}
            </div>
        @endif

        <form method="GET" action="{{ route('admin.pembinaan.index') }}"
            class="flex flex-col gap-2 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:flex-row sm:items-end">
            @if ($bisaPilihJenjang)
                <div class="sm:w-60">
                    <label class="{{ $lb }}">Filter Jenjang</label>
                    <select name="jenjang" class="{{ $in }}">
                        <option value="">Semua jenjang</option>
                        @foreach ($daftarJenjang as $j)
                            <option value="{{ $j['slug'] }}" @selected(\App\Support\Jenjang::normalisasi($filterJenjang) === $j['slug'])>
                                {{ $j['kode'] }} — {{ $j['nama'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="sm:flex-1">
                <label class="{{ $lb }}">Pilih Santri</label>
                <select name="santri" class="{{ $in }}">
                    <option value="">— Pilih santri —</option>
                    @foreach ($santris as $s)
                        <option value="{{ $s->id }}" @selected($santri?->id === $s->id)>
                            {{ $s->nama_lengkap }} — {{ \App\Support\Jenjang::label($s->jenjang_pendidikan) }} {{ $s->kelas }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-black text-white hover:bg-emerald-700">
                <i class="fa-solid fa-magnifying-glass mr-1"></i> Tampilkan
            </button>
        </form>

        @if (! $santri)
            <div class="{{ $card }}">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white">Daftar Santri Aktif</h3>
                        <p class="text-[10px] text-slate-400">Pilih satu santri untuk mencatat pembinaan.</p>
                    </div>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-black text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $santris->count() }} santri</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-400 dark:bg-slate-800/50">
                                <th class="px-3 py-3 font-extrabold">Nama</th>
                                <th class="px-3 py-3 font-extrabold">Jenjang</th>
                                <th class="px-3 py-3 font-extrabold">Kelas</th>
                                <th class="px-3 py-3 text-right font-extrabold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse ($santris as $s)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                    <td class="px-3 py-3 font-bold text-slate-700 dark:text-slate-200">{{ $s->nama_lengkap }}</td>
                                    <td class="px-3 py-3 text-slate-500">{{ \App\Support\Jenjang::label($s->jenjang_pendidikan) ?: '-' }}</td>
                                    <td class="px-3 py-3 text-slate-500">{{ $s->kelas ?: '-' }}</td>
                                    <td class="px-3 py-3 text-right">
                                        <a href="{{ route('admin.pembinaan.index', array_filter(['jenjang' => $filterJenjang, 'santri' => $s->id])) }}"
                                            class="inline-flex items-center gap-1 rounded-xl bg-emerald-50 px-3 py-1.5 text-[11px] font-bold text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-300">
                                            <i class="fa-solid fa-clipboard-list"></i> Catat
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-3 py-10 text-center text-[11px] text-slate-400">Tidak ada santri aktif pada filter ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="flex flex-col gap-3 rounded-2xl border border-emerald-100 bg-emerald-50/60 p-5 dark:border-emerald-900/40 dark:bg-emerald-950/20 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-600 text-white">
                        <i class="fa-solid fa-user-graduate"></i>
                    </span>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white">{{ $santri->nama_lengkap }}</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            {{ \App\Support\Jenjang::label($santri->jenjang_pendidikan) }}
                            {{ $santri->kelas ? '· Kelas '.$santri->kelas : '' }}
                            {{ $santri->kamar ? '· Kamar '.$santri->kamar : '' }}
                            @if ($santri->kelasModel?->waliKelas)
                                · Wali Kelas: {{ $santri->kelasModel->waliKelas->nama }}
                            @endif
                        </p>
                    </div>
                </div>
                <a href="{{ route('admin.pembinaan.index', array_filter(['jenjang' => $filterJenjang])) }}"
                    class="self-start rounded-xl border border-slate-200 bg-white px-4 py-2 text-[11px] font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Pilih santri lain
                </a>
            </div>

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                {{-- HAFALAN --}}
                <div class="{{ $card }}">
                    <div class="mb-4 flex items-center gap-3 border-b border-slate-100 pb-3 dark:border-slate-800">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400"><i class="fa-solid fa-book-quran"></i></span>
                        <div>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white">Hafalan / Setoran</h3>
                            <p class="text-[10px] text-slate-400">Catat setoran, ziyadah, dan murojaah.</p>
                        </div>
                    </div>

                    @if ($bolehUbah)
                        <form method="POST" action="{{ route('admin.pembinaan.hafalan.store') }}" class="space-y-3">
                            @csrf
                            <input type="hidden" name="santri_id" value="{{ $santri->id }}">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="{{ $lb }}">Tanggal</label>
                                    <input type="date" name="tanggal" value="{{ now()->toDateString() }}" required class="{{ $in }}">
                                </div>
                                <div>
                                    <label class="{{ $lb }}">Jenis</label>
                                    <select name="jenis" required class="{{ $in }}">
                                        @foreach ($jenisHafalan as $k => $label)
                                            <option value="{{ $k }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="{{ $lb }}">Surat</label>
                                    <input type="text" name="surat" maxlength="100" class="{{ $in }}" placeholder="Al-Baqarah">
                                </div>
                                <div>
                                    <label class="{{ $lb }}">Ayat</label>
                                    <input type="text" name="ayat" maxlength="50" class="{{ $in }}" placeholder="1-5">
                                </div>
                                <div>
                                    <label class="{{ $lb }}">Juz</label>
                                    <input type="text" name="juz" maxlength="10" class="{{ $in }}" placeholder="1">
                                </div>
                                <div>
                                    <label class="{{ $lb }}">Nilai (0-100)</label>
                                    <input type="number" name="nilai" min="0" max="100" step="0.01" class="{{ $in }}">
                                </div>
                                <div class="col-span-2">
                                    <label class="{{ $lb }}">Predikat</label>
                                    <input type="text" name="predikat" maxlength="30" class="{{ $in }}" placeholder="Mumtaz / Jayyid">
                                </div>
                            </div>
                            <div>
                                <label class="{{ $lb }}">Catatan</label>
                                <textarea name="catatan" rows="2" maxlength="1000" class="{{ $in }}"></textarea>
                            </div>
                            <button class="w-full rounded-xl bg-teal-600 px-4 py-2.5 text-[11px] font-black text-white hover:bg-teal-700">
                                <i class="fa-solid fa-plus mr-1"></i> Simpan Setoran
                            </button>
                        </form>
                    @endif

                    <div class="mt-5 space-y-2">
                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Riwayat Terakhir</p>
                        @forelse ($santri->hafalan as $h)
                            <div class="flex items-start justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2.5 dark:border-slate-800 dark:bg-slate-800/40">
                                <div class="min-w-0">
                                    <p class="text-[11px] font-bold text-slate-800 dark:text-slate-100">
                                        {{ $h->labelJenis() }} · {{ $h->surat ?: '-' }} {{ $h->ayat ? 'ayat '.$h->ayat : '' }}
                                        {{ $h->juz ? '· Juz '.$h->juz : '' }}
                                    </p>
                                    <p class="text-[10px] text-slate-400">
                                        {{ $h->tanggal?->translatedFormat('d M Y') }}
                                        @if ($h->nilai !== null) · Nilai {{ rtrim(rtrim(number_format((float) $h->nilai, 2, ',', '.'), '0'), ',') }} @endif
                                        @if ($h->predikat) · {{ $h->predikat }} @endif
                                    </p>
                                    @if ($h->catatan)<p class="mt-0.5 text-[10px] text-slate-500">{{ $h->catatan }}</p>@endif
                                </div>
                                @if ($bolehUbah)
                                    <form method="POST" action="{{ route('admin.pembinaan.hafalan.hapus', $h) }}" onsubmit="return confirm('Hapus catatan hafalan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-rose-500 hover:text-rose-700" title="Hapus"><i class="fa-solid fa-trash text-[11px]"></i></button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <p class="py-4 text-center text-[11px] text-slate-400">Belum ada catatan hafalan.</p>
                        @endforelse
                    </div>
                </div>

                {{-- PELANGGARAN --}}
                <div class="{{ $card }}">
                    <div class="mb-4 flex items-center gap-3 border-b border-slate-100 pb-3 dark:border-slate-800">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400"><i class="fa-solid fa-triangle-exclamation"></i></span>
                        <div>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white">Pelanggaran &amp; Pembinaan</h3>
                            <p class="text-[10px] text-slate-400">Catatan pelanggaran tetap terlihat pada riwayat alumni.</p>
                        </div>
                    </div>

                    @if ($bolehUbah)
                        <form method="POST" action="{{ route('admin.pembinaan.pelanggaran.store') }}" class="space-y-3">
                            @csrf
                            <input type="hidden" name="santri_id" value="{{ $santri->id }}">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="{{ $lb }}">Tanggal</label>
                                    <input type="date" name="tanggal" value="{{ now()->toDateString() }}" required class="{{ $in }}">
                                </div>
                                <div>
                                    <label class="{{ $lb }}">Poin</label>
                                    <input type="number" name="poin" min="0" max="1000" value="0" class="{{ $in }}">
                                </div>
                                <div class="col-span-2">
                                    <label class="{{ $lb }}">Jenis Pelanggaran</label>
                                    <input type="text" name="jenis" required maxlength="100" class="{{ $in }}" placeholder="Telat sholat Subuh">
                                </div>
                            </div>
                            <div>
                                <label class="{{ $lb }}">Deskripsi</label>
                                <textarea name="deskripsi" rows="2" maxlength="1000" class="{{ $in }}"></textarea>
                            </div>
                            <div>
                                <label class="{{ $lb }}">Tindakan / Pembinaan</label>
                                <textarea name="tindakan" rows="2" maxlength="1000" class="{{ $in }}"></textarea>
                            </div>
                            <button class="w-full rounded-xl bg-rose-600 px-4 py-2.5 text-[11px] font-black text-white hover:bg-rose-700">
                                <i class="fa-solid fa-plus mr-1"></i> Simpan Pelanggaran
                            </button>
                        </form>
                    @endif

                    <div class="mt-5 space-y-2">
                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Riwayat Terakhir</p>
                        @forelse ($santri->pelanggaran as $pel)
                            <div class="flex items-start justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2.5 dark:border-slate-800 dark:bg-slate-800/40">
                                <div class="min-w-0">
                                    <p class="text-[11px] font-bold text-slate-800 dark:text-slate-100">{{ $pel->jenis }} <span class="ml-1 rounded-full bg-rose-100 px-2 py-0.5 text-[9px] font-black text-rose-700">{{ $pel->poin }} poin</span></p>
                                    <p class="text-[10px] text-slate-400">{{ $pel->tanggal?->translatedFormat('d M Y') }}</p>
                                    @if ($pel->deskripsi)<p class="mt-0.5 text-[10px] text-slate-500">{{ $pel->deskripsi }}</p>@endif
                                    @if ($pel->tindakan)<p class="mt-0.5 text-[10px] text-emerald-600">Tindakan: {{ $pel->tindakan }}</p>@endif
                                </div>
                                @if ($bolehUbah)
                                    <form method="POST" action="{{ route('admin.pembinaan.pelanggaran.hapus', $pel) }}" onsubmit="return confirm('Hapus catatan pelanggaran ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-rose-500 hover:text-rose-700" title="Hapus"><i class="fa-solid fa-trash text-[11px]"></i></button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <p class="py-4 text-center text-[11px] text-slate-400">Belum ada catatan pelanggaran.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- PRESENSI KEGIATAN --}}
            <div class="{{ $card }}">
                <div class="mb-4 flex items-center gap-3 border-b border-slate-100 pb-3 dark:border-slate-800">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400"><i class="fa-solid fa-clipboard-check"></i></span>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white">Presensi Kegiatan</h3>
                        <p class="text-[10px] text-slate-400">Sholat berjamaah, tahfidz, sekolah formal, kegiatan asrama, dan piket kamar.</p>
                    </div>
                </div>

                @if ($bolehUbah)
                    <form method="POST" action="{{ route('admin.pembinaan.absensi.store') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-5">
                        @csrf
                        <input type="hidden" name="santri_id" value="{{ $santri->id }}">
                        <div>
                            <label class="{{ $lb }}">Tanggal</label>
                            <input type="date" name="tanggal" value="{{ now()->toDateString() }}" required class="{{ $in }}">
                        </div>
                        <div>
                            <label class="{{ $lb }}">Kegiatan</label>
                            <select name="jenis_kegiatan" required class="{{ $in }}">
                                @foreach ($jenisKegiatan as $keg)
                                    <option value="{{ $keg }}">{{ $keg }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="{{ $lb }}">Status</label>
                            <select name="status_kehadiran" required class="{{ $in }}">
                                @foreach ($statusAbsensi as $k => $label)
                                    <option value="{{ $k }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="{{ $lb }}">Keterangan</label>
                            <div class="flex gap-2">
                                <input type="text" name="keterangan" maxlength="1000" class="{{ $in }}" placeholder="Opsional">
                                <button class="shrink-0 rounded-xl bg-sky-600 px-4 py-2.5 text-[11px] font-black text-white hover:bg-sky-700">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                @endif

                <div class="mt-5 overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-400 dark:bg-slate-800/50">
                                <th class="px-3 py-2.5 font-extrabold">Tanggal</th>
                                <th class="px-3 py-2.5 font-extrabold">Kegiatan</th>
                                <th class="px-3 py-2.5 font-extrabold">Status</th>
                                <th class="px-3 py-2.5 font-extrabold">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse ($santri->absensi as $ab)
                                @php
                                    $badge = match ($ab->status_kehadiran) {
                                        'hadir' => 'bg-emerald-100 text-emerald-700',
                                        'izin' => 'bg-sky-100 text-sky-700',
                                        'sakit' => 'bg-amber-100 text-amber-700',
                                        default => 'bg-rose-100 text-rose-700',
                                    };
                                @endphp
                                <tr>
                                    <td class="px-3 py-2.5 text-slate-500">{{ $ab->tanggal?->translatedFormat('d M Y') ?? '-' }}</td>
                                    <td class="px-3 py-2.5 font-bold text-slate-700 dark:text-slate-200">{{ $ab->jenis_kegiatan ?? 'Kegiatan' }}</td>
                                    <td class="px-3 py-2.5"><span class="rounded-full px-2 py-0.5 text-[10px] font-black {{ $badge }}">{{ $statusAbsensi[$ab->status_kehadiran] ?? $ab->status_kehadiran }}</span></td>
                                    <td class="px-3 py-2.5 text-slate-500">{{ $ab->keterangan ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-3 py-8 text-center text-[11px] text-slate-400">Belum ada catatan presensi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
