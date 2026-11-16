@extends('layouts.admin')

@section('title', 'Detail Alumni - Pondok Pesantren Babul Hasanah')

@section('content')
    @php
        $baris = function (string $label, $nilai) {
            return [$label, filled($nilai) ? $nilai : '—'];
        };

        $identitas = [
            $baris('Nama Lengkap', $alumni->nama),
            $baris('Jenis Kelamin', $alumni->jenis_kelamin === 'L' ? 'Laki-laki' : ($alumni->jenis_kelamin === 'P' ? 'Perempuan' : null)),
            $baris('Tahun Lulus', $alumni->tahun_lulus),
            $baris('Jenjang', $alumni->jenjang),
            $baris('No. HP', $alumni->no_hp),
            $baris('Email', $alumni->email),
        ];

        $pasca = [
            $baris('Kegiatan', $alumni->kegiatan),
            $baris('Instansi / Kampus', $alumni->instansi),
            $baris('Alamat', $alumni->alamat),
        ];
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2 text-[11px] font-bold text-slate-400">
                    <a href="{{ route('admin.alumni.index') }}" class="hover:text-emerald-600 transition">Data Alumni</a>
                    <i class="fa-solid fa-chevron-right text-[8px]"></i>
                    <span>Detail</span>
                </div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white mt-1">{{ $alumni->nama }}</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Angkatan {{ $alumni->tahun_lulus ?? '—' }}
                    @if ($alumni->jenjang)
                        &middot; {{ $alumni->jenjang }}
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.alumni.index') }}"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 transition">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
                </a>
                @if (auth()->user()->bisa('alumni.ubah'))
                    <a href="{{ route('admin.alumni.edit', $alumni) }}"
                        class="rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-emerald-700 transition">
                        <i class="fa-solid fa-pen mr-1"></i> Edit
                    </a>
                @endif
            </div>
        </div>

        @if (session('success'))
            <div class="px-4 py-3 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Profil --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center gap-4">
                        @if ($alumni->foto_url)
                            <img src="{{ $alumni->foto_url }}" alt="{{ $alumni->nama }}"
                                class="h-20 w-20 rounded-2xl object-cover border border-slate-100 dark:border-slate-700">
                        @else
                            <span
                                class="flex h-20 w-20 items-center justify-center rounded-2xl bg-emerald-50 text-2xl font-black text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400">
                                {{ mb_strtoupper(mb_substr($alumni->nama, 0, 1)) }}
                            </span>
                        @endif
                        <div class="min-w-0">
                            <div class="text-sm font-black text-slate-900 dark:text-white">{{ $alumni->nama }}</div>
                            <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                                {{ $alumni->kegiatan ?: 'Kegiatan belum diisi' }}
                            </div>
                            @if ($alumni->instansi)
                                <div class="text-[11px] text-slate-400">{{ $alumni->instansi }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-5 space-y-3 border-t border-slate-100 pt-5 dark:border-slate-800">
                        @foreach ($identitas as [$label, $nilai])
                            <div class="flex items-start justify-between gap-4">
                                <span class="text-[11px] font-bold uppercase tracking-wide text-slate-400">{{ $label }}</span>
                                <span class="text-right text-xs font-bold text-slate-700 dark:text-slate-200">{{ $nilai }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Data Tertaut</h3>
                    <div class="mt-4 space-y-3 text-xs">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-slate-500 dark:text-slate-400">Data Santri</span>
                            @if ($alumni->student)
                                <a href="{{ route('admin.santri.detail', $alumni->student->id) }}"
                                    class="font-bold text-emerald-600 hover:underline">
                                    {{ $alumni->student->name }} ({{ $alumni->student->nisn }})
                                </a>
                            @else
                                <span class="font-bold text-slate-400">Tidak tertaut</span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-slate-500 dark:text-slate-400">Akun Portal</span>
                            @if ($alumni->user)
                                <span class="font-bold text-slate-700 dark:text-slate-200">{{ $alumni->user->email }}</span>
                            @else
                                <span class="font-bold text-slate-400">Belum ada</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rincian --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Setelah Lulus</h3>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        @foreach ($pasca as [$label, $nilai])
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800/60">
                                <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400">{{ $label }}</div>
                                <div class="mt-1 text-xs font-bold text-slate-700 dark:text-slate-200">{{ $nilai }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Testimoni</h3>
                    <p class="mt-3 text-xs leading-relaxed text-slate-600 dark:text-slate-300">
                        {{ $alumni->testimoni ?: 'Belum ada testimoni dari alumni ini.' }}
                    </p>
                </div>

                @if (auth()->user()->bisa('alumni.ubah'))
                    <div class="rounded-3xl border border-rose-100 bg-rose-50/60 p-6 dark:border-rose-900/40 dark:bg-rose-950/20">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-sm font-black text-rose-700 dark:text-rose-300">Hapus Data Alumni</h3>
                                <p class="text-[11px] text-rose-600/80 dark:text-rose-300/70">
                                    Tindakan ini menghapus data alumni ini secara permanen.
                                </p>
                            </div>
                            <form action="{{ route('admin.alumni.destroy', $alumni) }}" method="POST"
                                onsubmit="return confirm('Hapus data alumni {{ $alumni->nama }}?')">
                                @csrf @method('DELETE')
                                <button
                                    class="rounded-xl bg-rose-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-rose-700 transition">
                                    <i class="fa-solid fa-trash mr-1"></i> Hapus Alumni
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
