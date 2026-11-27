@extends('layouts.admin')

@section('title', 'Kurikulum - Akademik')

@use('App\Support\Akademik')

@section('content')
    @php $slugAdmin = auth()->user()->jenjangSlug(); @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Kurikulum</h2>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                    Susun kurikulum per jenjang dan semester. Kurikulum berstatus draf belum tampil di portal wali.
                </p>
            </div>
            @if ($bisaUbah)
                <a href="{{ route('admin.akademik.kurikulum.create', ['jenjang' => $filter['jenjang']]) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-xs font-black text-white shadow-sm transition hover:bg-emerald-800">
                    <i class="fa-solid fa-plus"></i> Tambah Kurikulum
                </a>
            @endif
        </div>

        @include('dashboard.akademik._tabs')
        @include('dashboard.akademik._peringatan-jenjang')

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10 px-4 py-3 text-xs font-bold text-emerald-800 dark:text-emerald-300">
                <i class="fa-solid fa-circle-check mr-1.5"></i>{{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 dark:border-rose-500/30 bg-rose-50 dark:bg-rose-500/10 px-4 py-3 text-xs font-bold text-rose-800 dark:text-rose-300">
                <i class="fa-solid fa-triangle-exclamation mr-1.5"></i>{{ session('error') }}
            </div>
        @endif

        <form method="GET" action="{{ route('admin.akademik.kurikulum.index') }}"
            class="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                @if ($slugAdmin)
                    <input type="hidden" name="jenjang" value="{{ $slugAdmin }}">
                @else
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Jenjang</label>
                        <select name="jenjang"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            <option value="">Semua jenjang</option>
                            @foreach ($daftarJenjang as $j)
                                <option value="{{ $j['slug'] }}" @selected($filter['jenjang'] === $j['slug'])>
                                    {{ $j['kode'] }} — {{ $j['nama'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Tahun Ajaran</label>
                    <select name="tahun_ajaran"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                        @foreach (Akademik::tahunAjaranPilihan() as $tahun)
                            <option value="{{ $tahun }}" @selected($filter['tahun'] === $tahun)>{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Semester</label>
                    <select name="semester"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                        @foreach (Akademik::SEMESTER as $nilai => $label)
                            <option value="{{ $nilai }}" @selected($filter['semester'] === $nilai)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($kurikulum as $row)
                <div class="flex flex-col rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white">{{ $row->nama }}</h3>
                            <p class="mt-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                {{ $row->jenjang }} · {{ $row->tahun_ajaran }} · {{ Akademik::labelSemester($row->semester) }}
                            </p>
                        </div>
                        @if ($row->status === 'published')
                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-black text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">Terbit</span>
                        @else
                            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-black text-amber-700 dark:bg-amber-500/20 dark:text-amber-300">Draft</span>
                        @endif
                    </div>
                    <p class="mt-3 line-clamp-2 text-[11px] leading-relaxed text-slate-500 dark:text-slate-400">
                        {{ $row->deskripsi ?: 'Belum ada deskripsi.' }}
                    </p>
                    <p class="mt-3 text-[11px] font-bold text-slate-600 dark:text-slate-300">
                        <i class="fa-solid fa-list-check mr-1 text-emerald-600"></i>{{ $row->item_count }} item mata pelajaran
                    </p>
                    <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4 dark:border-slate-800">
                        <a href="{{ route('admin.akademik.kurikulum.show', $row) }}"
                            class="rounded-lg border border-emerald-200 px-3 py-1.5 text-[10px] font-bold text-emerald-700 transition hover:bg-emerald-50 dark:border-emerald-500/30 dark:text-emerald-300">
                            Kelola Item
                        </a>
                        @if ($bisaUbah)
                            <a href="{{ route('admin.akademik.kurikulum.edit', $row) }}"
                                class="rounded-lg border border-slate-200 px-3 py-1.5 text-[10px] font-bold text-slate-600 transition hover:border-emerald-400 hover:text-emerald-700 dark:border-slate-700 dark:text-slate-300">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.akademik.kurikulum.destroy', $row) }}"
                                onsubmit="return confirm('Hapus kurikulum ini beserta seluruh itemnya?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="rounded-lg border border-rose-200 px-3 py-1.5 text-[10px] font-bold text-rose-600 transition hover:bg-rose-50 dark:border-rose-500/30 dark:text-rose-300">
                                    Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="md:col-span-2 xl:col-span-3 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-6 py-12 text-center space-y-2">
                    <i class="fa-solid fa-book-open text-2xl text-slate-300 dark:text-slate-600"></i>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400">Belum ada kurikulum untuk konteks ini.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
