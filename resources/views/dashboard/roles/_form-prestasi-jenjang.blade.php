{{--
    Kartu form input prestasi untuk satu jenjang.

    Dipakai di Dashboard Jenjang, dashboard Super Admin, dan halaman
    /admin/jenjang/{slug} agar tampilannya selalu sama.
--}}
@php
    $cardCls = $cardCls ?? 'rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-4';
    $labelCls = $labelCls ?? 'block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1.5';
    $inputCls = $inputCls ?? 'w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500';
    $bolehPrestasi = $bolehPrestasi ?? auth()->user()->bisa('prestasi.ubah');
    $kategoriPrestasi = $kategoriPrestasi ?? collect();
@endphp

<div class="{{ $cardCls }}">
    <div class="flex items-start justify-between gap-3">
        <div>
            <h2 class="text-sm font-black text-slate-900 dark:text-white">
                <i class="fa-solid fa-trophy mr-1 text-amber-500"></i> Input Prestasi Santri
            </h2>
            <p class="text-[10px] text-slate-400 mt-0.5">
                Tampil di halaman /prestasi dan portal wali santri.
            </p>
        </div>
        <div class="flex shrink-0 flex-col items-end gap-1">
            <a href="{{ route('admin.dashboard.jenjang.prestasi.form', $jenjangPilihan ? ['jenjang' => $jenjangPilihan] : []) }}"
                class="text-[10px] font-black text-amber-600 hover:underline whitespace-nowrap">
                {{ $bolehPrestasi ? 'Halaman khusus' : 'Daftar prestasi' }}
            </a>
            <a href="{{ route('admin.prestasi.index') }}"
                class="text-[10px] font-bold text-slate-400 hover:underline whitespace-nowrap">Kelola prestasi</a>
        </div>
    </div>

    @if (! $jenjang)
        <div class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-[11px] text-sky-800">
            Pilih jenjang di menu atas untuk mencatat prestasi. Daftar di bawah menampilkan prestasi terbaru
            @if ($modeAgregat) dari seluruh jenjang @endif.
        </div>
    @elseif (! $bolehPrestasi)
        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[11px] text-slate-600 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-300">
            <i class="fa-solid fa-eye mr-1"></i> Mode hanya baca. Form input prestasi hanya untuk Super Admin dan Admin Jenjang.
        </div>
    @elseif (! $prestasiSiap)
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-[11px] text-amber-800">
            Kolom penghubung prestasi belum tersedia. Jalankan <b>php artisan migrate</b> terlebih dahulu.
        </div>
    @elseif ($santriList->isEmpty())
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-[11px] text-amber-800">
            Belum ada data santri pada jenjang ini. Tambahkan atau verifikasi data santri terlebih dahulu.
        </div>
    @else
        <form method="POST" action="{{ route('admin.dashboard.jenjang.prestasi') }}" class="space-y-3">
            @csrf

            <div>
                <label for="prestasi_santri_id" class="{{ $labelCls }}">Santri</label>
                <select id="prestasi_santri_id" name="santri_id" required class="{{ $inputCls }}">
                    <option value="" disabled @selected(old('santri_id') === null)>Pilih santri</option>
                    @foreach ($santriList as $s)
                        <option value="{{ $s->id }}" @selected((string) old('santri_id') === (string) $s->id)>
                            {{ $s->nama_lengkap }} — Kelas {{ $s->kelas ?: '-' }} ({{ $s->nomor_induk ?: 'tanpa induk' }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[10px] text-slate-400 mt-1">
                    Nama & kelas diambil otomatis dari data santri. Notifikasi email dikirim ke akun wali yang tertaut.
                </p>
            </div>

            <div>
                <label for="prestasi_judul" class="{{ $labelCls }}">Nama Lomba / Capaian</label>
                <input id="prestasi_judul" type="text" name="judul" maxlength="150" required
                    value="{{ old('judul') }}" class="{{ $inputCls }}" placeholder="Contoh: Musabaqah Hifzhil Qur'an 30 Juz">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="prestasi_kategori" class="{{ $labelCls }}">Bidang Lomba</label>
                    <input id="prestasi_kategori" type="text" name="kategori" list="kategori-prestasi"
                        maxlength="60" required value="{{ old('kategori') }}" class="{{ $inputCls }}" placeholder="Contoh: Tahfidz">
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
                    <label for="prestasi_tingkat" class="{{ $labelCls }}">Tingkat</label>
                    <select id="prestasi_tingkat" name="tingkat" required class="{{ $inputCls }}">
                        <option value="" disabled @selected(old('tingkat') === null)>Pilih tingkat</option>
                        @foreach ($tingkatPrestasi as $t)
                            <option value="{{ $t }}" @selected(old('tingkat') === $t)>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="prestasi_wilayah" class="{{ $labelCls }}">Lokasi Lomba</label>
                    <input id="prestasi_wilayah" type="text" name="wilayah" maxlength="100" required
                        value="{{ old('wilayah') }}" class="{{ $inputCls }}" placeholder="Contoh: Medan">
                </div>
                <div>
                    <label for="prestasi_tahun" class="{{ $labelCls }}">Tahun</label>
                    <input id="prestasi_tahun" type="number" name="tahun" min="2000" max="{{ date('Y') + 1 }}"
                        value="{{ old('tahun', date('Y')) }}" required class="{{ $inputCls }}">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="prestasi_peringkat" class="{{ $labelCls }}">Peringkat</label>
                    <input id="prestasi_peringkat" type="number" name="peringkat" min="0" max="99"
                        value="{{ old('peringkat', 1) }}" required class="{{ $inputCls }}">
                    <p class="text-[10px] text-slate-400 mt-1">1–3 untuk juara, 0 untuk penghargaan.</p>
                </div>
                <div>
                    <label for="prestasi_label" class="{{ $labelCls }}">Label Kartu (opsional)</label>
                    <input id="prestasi_label" type="text" name="label" maxlength="40"
                        value="{{ old('label') }}" class="{{ $inputCls }}" placeholder="Contoh: Juara 1">
                </div>
            </div>

            <div>
                <label for="prestasi_deskripsi" class="{{ $labelCls }}">Deskripsi (opsional)</label>
                <textarea id="prestasi_deskripsi" name="deskripsi" rows="2" maxlength="1000"
                    class="{{ $inputCls }}" placeholder="Keterangan singkat prestasi">{{ old('deskripsi') }}</textarea>
            </div>

            <button type="submit"
                class="w-full rounded-xl bg-amber-500 hover:bg-amber-600 px-4 py-2.5 text-xs font-black text-white transition inline-flex items-center justify-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Simpan & Kirim ke Wali
            </button>
        </form>
    @endif

    @if ($prestasiTerbaru->isNotEmpty())
        <div class="border-t border-slate-100 dark:border-slate-800 pt-3">
            <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-2">Prestasi terbaru</p>
            <div class="space-y-2">
                @foreach ($prestasiTerbaru as $p)
                    <div class="flex items-center justify-between gap-3 text-[11px]">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-700 dark:text-slate-200 truncate">{{ $p->judul }}</p>
                            <p class="text-slate-400 truncate">{{ $p->nama }} · {{ $p->tingkat }}</p>
                        </div>
                        <span class="shrink-0 rounded-lg bg-amber-50 px-2 py-1 font-black text-amber-700">{{ $p->label }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
