{{--
    Kartu form input nilai cepat untuk satu jenjang.

    Dipakai di Dashboard Jenjang, dashboard Super Admin, dan halaman
    /admin/jenjang/{slug} agar tampilannya selalu sama.
--}}
@php
    $cardCls = $cardCls ?? 'rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-4';
    $labelCls = $labelCls ?? 'block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1.5';
    $inputCls = $inputCls ?? 'w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500';
    $bolehNilai = $bolehNilai ?? auth()->user()->bisa('akademik.ubah');
@endphp

<div class="{{ $cardCls }}">
    <div class="flex items-start justify-between gap-3">
        <div>
            <h2 class="text-sm font-black text-slate-900 dark:text-white">
                <i class="fa-solid fa-book-open-reader mr-1 text-emerald-600"></i> Input Nilai Santri
            </h2>
            <p class="text-[10px] text-slate-400 mt-0.5">
                Nilai langsung terbit di portal wali santri.
            </p>
        </div>
        <div class="flex shrink-0 flex-col items-end gap-1">
            <a href="{{ route('admin.dashboard.jenjang.nilai.form', $jenjangPilihan ? ['jenjang' => $jenjangPilihan] : []) }}"
                class="text-[10px] font-black text-emerald-600 hover:underline whitespace-nowrap">
                {{ $bolehNilai ? 'Halaman khusus' : 'Daftar nilai' }}
            </a>
            @if (auth()->user()->bisa('akademik.lihat'))
                <a href="{{ route('admin.akademik.nilai.index') }}"
                    class="text-[10px] font-bold text-slate-400 hover:underline whitespace-nowrap">Nilai lengkap</a>
            @endif
        </div>
    </div>

    @if (! $jenjang)
        <div class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-[11px] text-sky-800">
            Pilih jenjang di menu atas untuk mengisi nilai. Data di bawah menampilkan nilai terbaru
            @if ($modeAgregat) dari seluruh jenjang @endif.
        </div>
    @elseif (! $bolehNilai)
        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[11px] text-slate-600 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-300">
            <i class="fa-solid fa-eye mr-1"></i> Mode hanya baca. Form input nilai hanya untuk Super Admin dan Admin Jenjang.
        </div>
    @elseif (! $akademikSiap)
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-[11px] text-amber-800">
            Modul akademik belum siap. Jalankan migrasi akademik terlebih dahulu.
        </div>
    @elseif ($santriList->isEmpty())
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-[11px] text-amber-800">
            Belum ada data santri pada jenjang ini. Tambahkan atau verifikasi data santri terlebih dahulu.
        </div>
    @elseif ($mapelList->isEmpty())
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-[11px] text-amber-800">
            Belum ada mata pelajaran aktif untuk jenjang ini.
            @if ($bolehNilai)
                <a href="{{ route('admin.akademik.mapel.index') }}" class="underline font-bold">Kelola mata pelajaran</a>.
            @endif
        </div>
    @else
        <form method="POST" action="{{ route('admin.dashboard.jenjang.nilai') }}" class="space-y-3">
            @csrf
            <input type="hidden" name="jenjang" value="{{ $jenjangPilihan }}">

            <div>
                <label for="nilai_santri_id" class="{{ $labelCls }}">Santri</label>
                <select id="nilai_santri_id" name="santri_id" required class="{{ $inputCls }}">
                    <option value="" disabled @selected(old('santri_id') === null)>Pilih santri</option>
                    @foreach ($santriList as $s)
                        <option value="{{ $s->id }}" @selected((string) old('santri_id') === (string) $s->id)>
                            {{ $s->nama_lengkap }} — Kelas {{ $s->kelas ?: '-' }} ({{ $s->nomor_induk ?: 'tanpa induk' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="nilai_mapel" class="{{ $labelCls }}">Mata Pelajaran</label>
                <select id="nilai_mapel" name="mata_pelajaran_id" required class="{{ $inputCls }}">
                    <option value="" disabled @selected(old('mata_pelajaran_id') === null)>Pilih mata pelajaran</option>
                    @foreach ($mapelList as $mapel)
                        <option value="{{ $mapel->id }}" @selected((string) old('mata_pelajaran_id') === (string) $mapel->id)>
                            {{ $mapel->kode ? $mapel->kode.' — ' : '' }}{{ $mapel->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="nilai_tahun" class="{{ $labelCls }}">Tahun Ajaran</label>
                    <select id="nilai_tahun" name="tahun_ajaran" required class="{{ $inputCls }}">
                        @foreach ($tahunPilihan as $tahun)
                            <option value="{{ $tahun }}" @selected(old('tahun_ajaran', $tahunSekarang) === $tahun)>{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="nilai_semester" class="{{ $labelCls }}">Semester</label>
                    <select id="nilai_semester" name="semester" required class="{{ $inputCls }}">
                        @foreach ($semesterList as $kunci => $label)
                            <option value="{{ $kunci }}" @selected(old('semester', $semesterSekarang) === $kunci)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="nilai_kategori" class="{{ $labelCls }}">Kategori</label>
                    <select id="nilai_kategori" name="kategori" required class="{{ $inputCls }}">
                        @foreach ($kategoriNilai as $kunci => $label)
                            <option value="{{ $kunci }}" @selected(old('kategori', 'tugas') === $kunci)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="nilai_angka" class="{{ $labelCls }}">Nilai (0–100)</label>
                    <input id="nilai_angka" type="number" name="nilai" min="0" max="100" step="0.01"
                        value="{{ old('nilai') }}" required class="{{ $inputCls }}" placeholder="Contoh: 85">
                </div>
            </div>

            <div>
                <label for="nilai_catatan" class="{{ $labelCls }}">Catatan (opsional)</label>
                <input id="nilai_catatan" type="text" name="catatan" maxlength="255"
                    value="{{ old('catatan') }}" class="{{ $inputCls }}" placeholder="Catatan untuk wali santri">
            </div>

            <button type="submit"
                class="w-full rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 text-xs font-black text-white transition inline-flex items-center justify-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Simpan & Terbitkan Nilai
            </button>
        </form>
    @endif

    @if ($nilaiTerbaru->isNotEmpty())
        <div class="border-t border-slate-100 dark:border-slate-800 pt-3">
            <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-2">Nilai terbaru</p>
            <div class="space-y-2">
                @foreach ($nilaiTerbaru as $n)
                    <div class="flex items-center justify-between gap-3 text-[11px]">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-700 dark:text-slate-200 truncate">{{ $n->santri?->nama_lengkap ?? '-' }}</p>
                            <p class="text-slate-400 truncate">{{ $n->mataPelajaran?->nama ?? '-' }} · {{ ucfirst($n->kategori) }}</p>
                        </div>
                        <span class="shrink-0 rounded-lg bg-emerald-50 px-2 py-1 font-black text-emerald-700">{{ $n->nilai }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
