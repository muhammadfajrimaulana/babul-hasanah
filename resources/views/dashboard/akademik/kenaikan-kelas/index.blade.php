@extends('layouts.admin')

@section('title', 'Kenaikan Kelas & Kelulusan')

@section('content')
    @php
        $user = auth()->user();
        $bisaUbah = $user->bisa('akademik.ubah');
        $aksiLama = in_array(old('aksi'), ['naik', 'lulus'], true) ? old('aksi') : 'naik';
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                    <i class="fa-solid fa-book-open-reader"></i> Akademik
                </div>
                <h2 class="mt-1 text-xl font-black text-slate-900 dark:text-white">Kenaikan Kelas &amp; Kelulusan</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-2xl">
                    Pindahkan santri ke kelas baru atau luluskan santri. Nilai lama tidak dihapus —
                    setiap nilai tetap menempel pada tahun ajarannya melalui <b>academic_year_id</b>.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10 px-3 py-2 text-[11px] font-bold text-emerald-800 dark:text-emerald-300">
                    <i class="fa-solid fa-building-columns"></i> {{ $user->namaJenjang() }}
                </span>
                @if (! $bisaUbah)
                    <span class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 px-3 py-2 text-[11px] font-bold text-slate-600 dark:text-slate-300">
                        <i class="fa-solid fa-eye"></i> Mode monitoring (hanya baca)
                    </span>
                @endif
            </div>
        </div>

        @include('dashboard.akademik._tabs')
        @include('dashboard.akademik._peringatan-jenjang')

        @foreach (['success' => 'emerald', 'error' => 'rose', 'info' => 'sky'] as $jenis => $warna)
            @if (session($jenis))
                <div class="rounded-2xl border border-{{ $warna }}-200 dark:border-{{ $warna }}-500/30 bg-{{ $warna }}-50 dark:bg-{{ $warna }}-500/10 px-4 py-3.5 text-xs font-bold text-{{ $warna }}-800 dark:text-{{ $warna }}-300 flex items-start gap-2.5">
                    <i class="fa-solid {{ $jenis === 'success' ? 'fa-circle-check' : ($jenis === 'error' ? 'fa-triangle-exclamation' : 'fa-circle-info') }} mt-0.5"></i>
                    <span>{{ session($jenis) }}</span>
                </div>
            @endif
        @endforeach

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 dark:border-rose-500/30 bg-rose-50 dark:bg-rose-500/10 px-4 py-3.5 text-xs text-rose-800 dark:text-rose-300 space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="flex items-start gap-2"><i class="fa-solid fa-triangle-exclamation mt-0.5"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (session('hasil_dilewati'))
            <div class="rounded-2xl border border-amber-200 dark:border-amber-500/30 bg-amber-50 dark:bg-amber-500/10 px-4 py-3.5 text-xs text-amber-900 dark:text-amber-200 space-y-1.5">
                <p class="font-black flex items-center gap-2"><i class="fa-solid fa-forward"></i> Santri yang dilewati:</p>
                @foreach (session('hasil_dilewati') as $lewat)
                    <p class="pl-5">
                        <b>{{ $lewat['nama'] }}</b> ({{ $lewat['nisn'] ?: '—' }}) — {{ $lewat['alasan'] }}
                    </p>
                @endforeach
            </div>
        @endif

        @if (session('hasil_ditahan'))
            <div class="rounded-2xl border border-sky-200 dark:border-sky-500/30 bg-sky-50 dark:bg-sky-500/10 px-4 py-3.5 text-xs text-sky-900 dark:text-sky-200 space-y-1.5">
                <p class="font-black flex items-center gap-2"><i class="fa-solid fa-user-shield"></i> Peran akun tidak dialihkan otomatis:</p>
                @foreach (session('hasil_ditahan') as $ditahan)
                    <p class="pl-5"><b>{{ $ditahan['nama'] }}</b> — {{ $ditahan['alasan'] }}</p>
                @endforeach
            </div>
        @endif

        {{-- Filter kelas & tahun ajaran --}}
        <form method="GET" action="{{ route('admin.akademik.kenaikan-kelas.index') }}"
            class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <div>
                    <label for="class_id" class="block text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Kelas asal</label>
                    <select id="class_id" name="class_id"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                        <option value="">— Pilih kelas —</option>
                        @foreach ($daftarKelas as $pilihan)
                            <option value="{{ $pilihan->id }}" @selected((int) $filter['class_id'] === $pilihan->id)>
                                {{ $pilihan->name }} · {{ \App\Support\Jenjang::label($pilihan->jenjang) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="academic_year_id" class="block text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Tahun ajaran</label>
                    <select id="academic_year_id" name="academic_year_id"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                        @forelse ($daftarTahun as $pilihan)
                            <option value="{{ $pilihan->id }}" @selected($filter['academic_year_id'] === $pilihan->id)>
                                {{ $pilihan->nama }}{{ $pilihan->aktif ? ' · aktif' : '' }}
                            </option>
                        @empty
                            <option value="">Belum ada tahun ajaran</option>
                        @endforelse
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 px-4 py-2.5 text-xs font-black text-white transition">
                        <i class="fa-solid fa-filter"></i> Tampilkan
                    </button>
                </div>
            </div>
        </form>

        @if ($bisaUbah)
            <div x-data="{ buka: false }" class="flex justify-end">
                <div class="w-full sm:w-80">
                    <button type="button" @click="buka = !buka"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-4 py-2.5 text-xs font-black text-slate-700 dark:text-slate-200 hover:border-emerald-400 transition">
                        <i class="fa-solid fa-calendar-plus"></i> Tahun ajaran baru
                    </button>
                    <div x-show="buka" x-cloak
                        class="mt-2 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm p-3">
                        <form method="POST" action="{{ route('admin.akademik.kenaikan-kelas.tahun') }}" class="space-y-2">
                            @csrf
                            <input type="text" name="nama" value="{{ old('nama') }}" placeholder="2027/2028"
                                class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-2.5 py-2 text-xs font-semibold">
                            <label class="flex items-center gap-2 text-[11px] font-semibold text-slate-600 dark:text-slate-300">
                                <input type="checkbox" name="aktif" value="1"
                                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                Jadikan tahun ajaran aktif
                            </label>
                            <button type="submit"
                                class="w-full rounded-lg bg-slate-800 hover:bg-slate-900 px-3 py-2 text-[11px] font-black text-white transition">
                                Simpan tahun ajaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @if (! $tahun)
            <div class="rounded-2xl border border-amber-200 dark:border-amber-500/30 bg-amber-50 dark:bg-amber-500/10 px-4 py-3.5 text-xs font-bold text-amber-800 dark:text-amber-300">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                Belum ada tahun ajaran. Tambahkan tahun ajaran terlebih dahulu sebelum memproses kenaikan kelas.
            </div>
        @endif

        @if (! $kelas)
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-8 text-center space-y-3">
                <div class="mx-auto w-14 h-14 rounded-2xl bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-people-roof"></i>
                </div>
                <h3 class="text-base font-black text-slate-900 dark:text-white">Pilih kelas asal</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                    Tentukan kelas dan tahun ajaran di atas untuk menampilkan daftar santri yang akan
                    dinaikkan atau diluluskan.
                </p>
            </div>
        @else
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach ([
                    ['label' => 'Santri di kelas', 'nilai' => $ringkasan['siswa'], 'ikon' => 'fa-users', 'warna' => 'text-slate-700 dark:text-slate-200'],
                    ['label' => 'Nilai tersimpan', 'nilai' => $ringkasan['nilai'], 'ikon' => 'fa-database', 'warna' => 'text-sky-700 dark:text-sky-300'],
                    ['label' => 'Kelas tujuan', 'nilai' => $ringkasan['kelas_tujuan'], 'ikon' => 'fa-arrow-right', 'warna' => 'text-violet-700 dark:text-violet-300'],
                    ['label' => 'Tahun ajaran', 'nilai' => $ringkasan['tahun_ajaran'], 'ikon' => 'fa-calendar-days', 'warna' => 'text-emerald-700 dark:text-emerald-300'],
                ] as $kartu)
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                        <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <i class="fa-solid {{ $kartu['ikon'] }}"></i> {{ $kartu['label'] }}
                        </div>
                        <p class="mt-1.5 text-xl font-black {{ $kartu['warna'] }}">{{ $kartu['nilai'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="rounded-2xl border border-sky-200 dark:border-sky-500/30 bg-sky-50 dark:bg-sky-500/10 px-4 py-3 text-[11px] font-semibold text-sky-800 dark:text-sky-300 flex items-start gap-2">
                <i class="fa-solid fa-shield-halved mt-0.5"></i>
                <span>
                    Kenaikan kelas hanya memindahkan penempatan aktif santri. Seluruh nilai pada kelas dan
                    tahun ajaran lama tetap tersimpan dan tetap muncul di transkrip alumni.
                </span>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden"
                x-data="{ aksi: '{{ $aksiLama }}' }">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white">{{ $kelas->name }}</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            {{ \App\Support\Jenjang::label($kelas->jenjang) }}
                            · Tahun ajaran {{ $tahun?->nama ?? '—' }}
                        </p>
                    </div>
                    @if ($siswa->isNotEmpty())
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 px-2.5 py-1 text-[10px] font-black text-slate-600 dark:text-slate-300">
                            <i class="fa-solid fa-list-check"></i> {{ $siswa->count() }} santri aktif
                        </span>
                    @endif
                </div>

                @if ($siswa->isEmpty())
                    <div class="p-8 text-center space-y-2">
                        <div class="mx-auto w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-user-slash"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-600 dark:text-slate-300">Tidak ada santri aktif di kelas ini.</p>
                        <p class="text-[11px] text-slate-400">
                            Santri yang sudah lulus atau pindah tidak lagi ditampilkan pada daftar kenaikan kelas.
                        </p>
                    </div>
                @else
                    <form method="POST" action="{{ route('admin.akademik.kenaikan-kelas.proses') }}" class="p-5 space-y-5">
                        @csrf
                        <input type="hidden" name="class_id" value="{{ $kelas->id }}">
                        <input type="hidden" name="academic_year_id" value="{{ $tahun?->id }}">

                        <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800">
                            <table class="min-w-full text-xs">
                                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400">
                                    <tr>
                                        <th class="px-3 py-2.5 text-left w-10">
                                            <input type="checkbox" id="pilih-semua"
                                                class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                        </th>
                                        <th class="px-3 py-2.5 text-left font-black uppercase tracking-wider text-[10px]">NISN</th>
                                        <th class="px-3 py-2.5 text-left font-black uppercase tracking-wider text-[10px]">Nama Santri</th>
                                        <th class="px-3 py-2.5 text-center font-black uppercase tracking-wider text-[10px]">Nilai Tersimpan</th>
                                        <th class="px-3 py-2.5 text-left font-black uppercase tracking-wider text-[10px]">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @foreach ($siswa as $anak)
                                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                                            <td class="px-3 py-2.5">
                                                <input type="checkbox" name="student_ids[]" value="{{ $anak->id }}"
                                                    class="pilih-santri rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                                    @checked(in_array($anak->id, array_map('intval', (array) old('student_ids', []))))>
                                            </td>
                                            <td class="px-3 py-2.5 font-mono text-[11px] text-slate-500 dark:text-slate-400">{{ $anak->nisn ?: '—' }}</td>
                                            <td class="px-3 py-2.5">
                                                <p class="font-bold text-slate-800 dark:text-slate-100">{{ $anak->name }}</p>
                                                @if ($anak->alumni)
                                                    <p class="text-[10px] text-emerald-600 dark:text-emerald-400">
                                                        <i class="fa-solid fa-graduation-cap"></i> Data alumni sudah ada
                                                    </p>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2.5 text-center">
                                                <span class="inline-flex min-w-[2rem] justify-center rounded-lg bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-[11px] font-bold text-slate-600 dark:text-slate-300">
                                                    {{ (int) ($nilaiPerSiswa[$anak->id] ?? 0) }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2.5">
                                                <span class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-300">
                                                    {{ $anak->labelStatus() }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                            <label class="cursor-pointer rounded-2xl border-2 p-4 transition"
                                :class="aksi === 'naik' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-500/10' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900'">
                                <span class="flex items-start gap-3">
                                    <input type="radio" name="aksi" value="naik" x-model="aksi"
                                        class="mt-0.5 border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                    <span>
                                        <span class="block text-xs font-black text-slate-900 dark:text-white">Naikkan ke kelas baru</span>
                                        <span class="block text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                            Santri terpilih dipindahkan ke kelas tujuan pada tahun ajaran di atas.
                                        </span>
                                    </span>
                                </span>
                            </label>

                            <label class="cursor-pointer rounded-2xl border-2 p-4 transition"
                                :class="aksi === 'lulus' ? 'border-violet-500 bg-violet-50 dark:bg-violet-500/10' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900'">
                                <span class="flex items-start gap-3">
                                    <input type="radio" name="aksi" value="lulus" x-model="aksi"
                                        class="mt-0.5 border-slate-300 text-violet-600 focus:ring-violet-500">
                                    <span>
                                        <span class="block text-xs font-black text-slate-900 dark:text-white">Luluskan santri</span>
                                        <span class="block text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                            Status menjadi <b>graduated</b>, data alumni dibuat, dan akun wali dialihkan menjadi alumni.
                                        </span>
                                    </span>
                                </span>
                            </label>
                        </div>

                        <div x-show="aksi === 'naik'" x-cloak class="grid grid-cols-1 md:grid-cols-[minmax(0,1fr)_auto] gap-3 items-end">
                            <div>
                                <label for="class_tujuan_id" class="block text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Kelas tujuan</label>
                                <select id="class_tujuan_id" name="class_tujuan_id"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                                    <option value="">— Pilih kelas tujuan —</option>
                                    @foreach ($kelasTujuan as $pilihan)
                                        <option value="{{ $pilihan->id }}" @selected((int) old('class_tujuan_id') === $pilihan->id)>
                                            {{ $pilihan->name }} · {{ \App\Support\Jenjang::label($pilihan->jenjang) }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($kelasTujuan->isEmpty())
                                    <p class="mt-1.5 text-[11px] font-semibold text-amber-600 dark:text-amber-400">
                                        Belum ada kelas lain pada jenjang ini. Tambahkan kelas terlebih dahulu.
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div x-show="aksi === 'lulus'" x-cloak
                            class="rounded-2xl border border-violet-200 dark:border-violet-500/30 bg-violet-50 dark:bg-violet-500/10 px-4 py-3 text-[11px] font-semibold text-violet-800 dark:text-violet-300">
                            <i class="fa-solid fa-circle-info mr-1"></i>
                            Pastikan nilai santri sudah diterbitkan sebelum diluluskan agar transkrip dan ijazah alumni lengkap.
                        </div>

                        @if ($bisaUbah && $tahun)
                            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 dark:border-slate-800 pt-4">
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    <i class="fa-solid fa-shield-halved mr-1 text-emerald-600"></i>
                                    Record nilai lama tidak akan dihapus oleh proses ini.
                                </p>
                                <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-xs font-black text-white transition"
                                    :class="aksi === 'lulus' ? 'bg-violet-600 hover:bg-violet-700' : 'bg-emerald-700 hover:bg-emerald-800'">
                                    <i class="fa-solid" :class="aksi === 'lulus' ? 'fa-graduation-cap' : 'fa-arrow-up-right-dots'"></i>
                                    <span x-text="aksi === 'lulus' ? 'Luluskan santri terpilih' : 'Naikkan santri terpilih'"></span>
                                </button>
                            </div>
                        @elseif (! $bisaUbah)
                            <div class="rounded-xl bg-slate-100 dark:bg-slate-800 px-4 py-3 text-[11px] font-semibold text-slate-500 dark:text-slate-400">
                                <i class="fa-solid fa-eye mr-1"></i> Anda hanya dapat melihat data pada halaman ini.
                            </div>
                        @endif
                    </form>

                    <script>
                        document.getElementById('pilih-semua')?.addEventListener('change', function () {
                            document.querySelectorAll('.pilih-santri').forEach(function (kotak) {
                                kotak.checked = this.checked;
                            }, this);
                        });
                    </script>
                @endif
            </div>
        @endif
    </div>
@endsection
