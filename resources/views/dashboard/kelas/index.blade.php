@extends('layouts.admin')

@section('title', 'Kelas & Guru - Pondok Pesantren Babul Hasanah')

@section('content')
    @php
        $in = 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500';
        $lb = 'mb-1.5 block text-[11px] font-bold text-slate-600 dark:text-slate-300';
        $card = 'rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900';
    @endphp

    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white">Kelas &amp; Guru</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Satu kelas hanya memiliki satu Wali Kelas. Guru dan Wali Kelas tidak memiliki akun login — hanya data nama/jabatan.
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

        @if ($bolehUbah)
            <div class="{{ $card }}" x-data="{ buka: false }">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400"><i class="fa-solid fa-chalkboard"></i></span>
                        <div>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white">Tambah Kelas</h3>
                            <p class="text-[10px] text-slate-400">Perubahan wali kelas langsung tampil di portal wali.</p>
                        </div>
                    </div>
                    <button type="button" @click="buka = !buka" class="rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-emerald-700">
                        <i class="fa-solid fa-plus mr-1"></i> <span x-text="buka ? 'Tutup' : 'Tambah'"></span>
                    </button>
                </div>

                <form x-show="buka" x-transition method="POST" action="{{ route('admin.kelas.store') }}" class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    @csrf
                    <div>
                        <label class="{{ $lb }}">Jenjang</label>
                        @if ($jenjangAdmin)
                            <input type="hidden" name="jenjang" value="{{ $jenjangAdmin }}">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-600 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-300">
                                {{ \App\Support\Jenjang::label($jenjangAdmin) }}
                            </div>
                        @else
                            <select name="jenjang" required class="{{ $in }}">
                                <option value="">— Pilih —</option>
                                @foreach ($daftarJenjang as $j)
                                    <option value="{{ $j['slug'] }}" @selected(old('jenjang') === $j['slug'])>{{ $j['kode'] }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div>
                        <label class="{{ $lb }}">Nama Kelas</label>
                        <input type="text" name="nama" required maxlength="50" value="{{ old('nama') }}" class="{{ $in }}" placeholder="8A">
                    </div>
                    <div>
                        <label class="{{ $lb }}">Tingkat</label>
                        <input type="number" name="tingkat" min="1" max="12" value="{{ old('tingkat') }}" class="{{ $in }}" placeholder="8">
                    </div>
                    <div>
                        <label class="{{ $lb }}">Tahun Ajaran</label>
                        <input type="text" name="tahun_ajaran" required value="{{ old('tahun_ajaran', $tahunAjaran) }}" class="{{ $in }}" placeholder="2026/2027">
                    </div>
                    <div>
                        <label class="{{ $lb }}">Wali Kelas</label>
                        <select name="wali_kelas_id" class="{{ $in }}">
                            <option value="">— Belum ditentukan —</option>
                            @foreach ($gurus as $g)
                                <option value="{{ $g->id }}" @selected((string) old('wali_kelas_id') === (string) $g->id)>{{ $g->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-5">
                        <button class="rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-black text-white hover:bg-emerald-700">
                            <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Kelas
                        </button>
                    </div>
                </form>
            </div>
        @endif

        @if (! $jenjangAdmin)
            <form method="GET" action="{{ route('admin.kelas.index') }}" class="flex flex-col gap-2 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:flex-row sm:items-center">
                <label class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Filter jenjang:</label>
                <select name="jenjang" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 sm:w-56">
                    <option value="">Semua</option>
                    @foreach ($daftarJenjang as $j)
                        <option value="{{ $j['slug'] }}" @selected(\App\Support\Jenjang::normalisasi($filterJenjang) === $j['slug'])>{{ $j['kode'] }}</option>
                    @endforeach
                </select>
                <button class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white">Terapkan</button>
            </form>
        @endif

        <div class="{{ $card }}">
            <div class="mb-4 flex items-center justify-between gap-2">
                <h3 class="text-sm font-black text-slate-900 dark:text-white">Daftar Kelas</h3>
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-black text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $kelas->count() }} kelas</span>
            </div>

            <div class="space-y-3">
                @forelse ($kelas as $k)
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-4 dark:border-slate-800 dark:bg-slate-800/30" x-data="{ edit: false }">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-[10px] font-black text-emerald-700">{{ \App\Support\Jenjang::label($k->jenjang) }}</span>
                                    <span class="text-sm font-black text-slate-900 dark:text-white">{{ $k->nama }}</span>
                                    @if ($k->tahun_ajaran)<span class="text-[10px] font-semibold text-slate-400">TA {{ $k->tahun_ajaran }}</span>@endif
                                </div>
                                <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                                    <i class="fa-solid fa-user-tie mr-1 text-slate-400"></i>
                                    Wali Kelas: <b>{{ $k->waliKelas?->nama ?? 'Belum ditentukan' }}</b>
                                    &nbsp;·&nbsp; <i class="fa-solid fa-users mr-1 text-slate-400"></i> {{ $k->santri_count }} santri
                                </p>
                            </div>

                            @if ($bolehUbah)
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="edit = !edit" class="rounded-xl bg-amber-50 px-3 py-2 text-[11px] font-bold text-amber-700 hover:bg-amber-100 dark:bg-amber-500/10 dark:text-amber-300">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </button>
                                    <form method="POST" action="{{ route('admin.kelas.destroy', $k) }}" onsubmit="return confirm('Hapus kelas {{ $k->nama }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-xl bg-rose-50 px-3 py-2 text-[11px] font-bold text-rose-700 hover:bg-rose-100 dark:bg-rose-500/10 dark:text-rose-300"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        @if ($bolehUbah)
                            <form x-show="edit" x-transition method="POST" action="{{ route('admin.kelas.update', $k) }}" class="mt-4 grid grid-cols-2 gap-3 border-t border-slate-200 pt-4 dark:border-slate-700 lg:grid-cols-5">
                                @csrf
                                @method('PUT')
                                <div>
                                    <label class="{{ $lb }}">Jenjang</label>
                                    @if ($jenjangAdmin)
                                        <input type="hidden" name="jenjang" value="{{ $jenjangAdmin }}">
                                        <div class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">{{ \App\Support\Jenjang::label($jenjangAdmin) }}</div>
                                    @else
                                        <select name="jenjang" class="{{ $in }}">
                                            @foreach ($daftarJenjang as $j)
                                                <option value="{{ $j['slug'] }}" @selected($k->jenjang === $j['slug'])>{{ $j['kode'] }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                                <div>
                                    <label class="{{ $lb }}">Nama</label>
                                    <input type="text" name="nama" value="{{ $k->nama }}" required class="{{ $in }}">
                                </div>
                                <div>
                                    <label class="{{ $lb }}">Tingkat</label>
                                    <input type="number" name="tingkat" value="{{ $k->tingkat }}" min="1" max="12" class="{{ $in }}">
                                </div>
                                <div>
                                    <label class="{{ $lb }}">Tahun Ajaran</label>
                                    <input type="text" name="tahun_ajaran" value="{{ $k->tahun_ajaran }}" required class="{{ $in }}">
                                </div>
                                <div>
                                    <label class="{{ $lb }}">Wali Kelas</label>
                                    <select name="wali_kelas_id" class="{{ $in }}">
                                        <option value="">— Belum ditentukan —</option>
                                        @foreach ($gurus as $g)
                                            <option value="{{ $g->id }}" @selected((int) $k->wali_kelas_id === (int) $g->id)>{{ $g->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-2 lg:col-span-5">
                                    <button class="rounded-xl bg-emerald-600 px-4 py-2.5 text-[11px] font-black text-white hover:bg-emerald-700">
                                        <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="py-10 text-center text-[11px] text-slate-400">Belum ada kelas. Tambahkan kelas terlebih dahulu.</p>
                @endforelse
            </div>
        </div>

        {{-- GURU --}}
        <div class="{{ $card }}">
            <div class="mb-4 flex items-center justify-between gap-2">
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Data Guru</h3>
                    <p class="text-[10px] text-slate-400">Guru hanya data nama — tidak memiliki akun login.</p>
                </div>
                @if ($bolehGuru)
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-black text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $gurus->count() }} guru</span>
                @endif
            </div>

            @if ($bolehGuru)
                <form method="POST" action="{{ route('admin.guru.store') }}" class="grid grid-cols-1 gap-3 rounded-2xl border border-slate-100 bg-slate-50/60 p-4 dark:border-slate-800 dark:bg-slate-800/30 sm:grid-cols-5">
                    @csrf
                    <div class="sm:col-span-2">
                        <label class="{{ $lb }}">Nama Guru</label>
                        <input type="text" name="nama" required maxlength="150" class="{{ $in }}" placeholder="Ust. Ahmad">
                    </div>
                    <div>
                        <label class="{{ $lb }}">NIP</label>
                        <input type="text" name="nip" maxlength="50" class="{{ $in }}">
                    </div>
                    <div>
                        <label class="{{ $lb }}">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="{{ $in }}">
                            <option value="">—</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <input type="hidden" name="aktif" value="0">
                        <label class="flex items-center gap-2 pb-3 text-[11px] font-bold text-slate-600 dark:text-slate-300">
                            <input type="checkbox" name="aktif" value="1" checked class="rounded border-slate-300 text-emerald-600">
                            Aktif
                        </label>
                    </div>
                    <div class="sm:col-span-5">
                        <button class="rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-black text-white hover:bg-emerald-700">
                            <i class="fa-solid fa-user-plus mr-1"></i> Tambah Guru
                        </button>
                    </div>
                </form>
            @endif

            <div class="mt-4 space-y-2">
                @forelse ($gurus as $g)
                    <div class="rounded-xl border border-slate-100 bg-white px-4 py-3 dark:border-slate-800 dark:bg-slate-900" x-data="{ edit: false }">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-[12px] font-bold text-slate-800 dark:text-slate-100">
                                    {{ $g->nama }}
                                    @if ($g->nip)<span class="ml-1 font-mono text-[10px] text-slate-400">NIP {{ $g->nip }}</span>@endif
                                    @if (! $g->aktif)<span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-[9px] font-black text-slate-500">Nonaktif</span>@endif
                                </p>
                                <p class="text-[10px] text-slate-400">{{ $g->jenis_kelamin === 'P' ? 'Perempuan' : ($g->jenis_kelamin === 'L' ? 'Laki-laki' : '-') }}</p>
                            </div>
                            @if ($bolehGuru)
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="edit = !edit" class="text-[11px] font-bold text-amber-600 hover:underline"><i class="fa-solid fa-pen"></i></button>
                                    <form method="POST" action="{{ route('admin.guru.destroy', $g) }}" onsubmit="return confirm('Hapus data guru {{ $g->nama }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-[11px] font-bold text-rose-600 hover:underline"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        @if ($bolehGuru)
                            <form x-show="edit" x-transition method="POST" action="{{ route('admin.guru.update', $g) }}" class="mt-3 grid grid-cols-1 gap-3 border-t border-slate-100 pt-3 dark:border-slate-800 sm:grid-cols-4">
                                @csrf
                                @method('PUT')
                                <input type="text" name="nama" value="{{ $g->nama }}" required class="{{ $in }}">
                                <input type="text" name="nip" value="{{ $g->nip }}" class="{{ $in }}" placeholder="NIP">
                                <select name="jenis_kelamin" class="{{ $in }}">
                                    <option value="">—</option>
                                    <option value="L" @selected($g->jenis_kelamin === 'L')>Laki-laki</option>
                                    <option value="P" @selected($g->jenis_kelamin === 'P')>Perempuan</option>
                                </select>
                                <div class="flex items-center gap-2">
                                    <input type="hidden" name="aktif" value="0">
                                    <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-600 dark:text-slate-300">
                                        <input type="checkbox" name="aktif" value="1" @checked($g->aktif) class="rounded border-slate-300 text-emerald-600"> Aktif
                                    </label>
                                    <button class="ml-auto rounded-xl bg-emerald-600 px-4 py-2 text-[11px] font-black text-white hover:bg-emerald-700">Simpan</button>
                                </div>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="py-8 text-center text-[11px] text-slate-400">Belum ada data guru. Tambahkan guru untuk bisa memilih wali kelas.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
