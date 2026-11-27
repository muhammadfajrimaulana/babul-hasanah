@extends('layouts.admin')

@section('title', 'Detail Kurikulum - Akademik')

@use('App\Support\Akademik')

@section('content')
    @php $inputCls = 'w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20'; @endphp

    <div class="space-y-6">
        <div>
            <a href="{{ route('admin.akademik.kurikulum.index') }}"
                class="inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-500 hover:text-emerald-700 transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar kurikulum
            </a>
            <div class="mt-1 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-black text-slate-900 dark:text-white">{{ $kurikulum->nama }}</h2>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        {{ $kurikulum->jenjang }} · {{ $kurikulum->tahun_ajaran }} · {{ Akademik::labelSemester($kurikulum->semester) }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($kurikulum->status === 'published')
                        <span class="rounded-full bg-emerald-100 px-3 py-1.5 text-[10px] font-black text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">Terbit</span>
                    @else
                        <span class="rounded-full bg-amber-100 px-3 py-1.5 text-[10px] font-black text-amber-700 dark:bg-amber-500/20 dark:text-amber-300">Draft</span>
                    @endif
                    @if ($bisaUbah)
                        <a href="{{ route('admin.akademik.kurikulum.edit', $kurikulum) }}"
                            class="rounded-xl border border-slate-200 px-3 py-2 text-[11px] font-bold text-slate-600 transition hover:border-emerald-400 hover:text-emerald-700 dark:border-slate-700 dark:text-slate-300">
                            <i class="fa-solid fa-pen mr-1"></i> Edit
                        </a>
                    @endif
                    @if ($bisaUbah && auth()->user()->bisa('akademik.terbit') && $kurikulum->status !== 'published')
                        <form method="POST" action="{{ route('admin.akademik.kurikulum.terbitkan', $kurikulum) }}"
                            onsubmit="return confirm('Terbitkan kurikulum ini ke portal wali?')">
                            @csrf
                            <button type="submit"
                                class="rounded-xl bg-amber-500 px-4 py-2 text-[11px] font-black text-slate-900 transition hover:bg-amber-600">
                                <i class="fa-solid fa-paper-plane mr-1"></i> Terbitkan
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        @include('dashboard.akademik._tabs')

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-bold text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
                <i class="fa-solid fa-circle-check mr-1.5"></i>{{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-bold text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
                <i class="fa-solid fa-triangle-exclamation mr-1.5"></i>{{ session('error') }}
            </div>
        @endif

        @if ($kurikulum->deskripsi)
            <div class="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 text-xs leading-relaxed text-slate-600 dark:text-slate-300 shadow-sm">
                {{ $kurikulum->deskripsi }}
            </div>
        @endif

        <div class="overflow-hidden rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                <h3 class="text-sm font-black text-slate-900 dark:text-white">
                    <i class="fa-solid fa-list-check mr-1.5 text-emerald-600"></i> Item Kurikulum
                </h3>
                <p class="mt-0.5 text-[10px] text-slate-400">Jumlah: {{ $kurikulum->item->count() }} mata pelajaran</p>
            </div>

            @if ($kurikulum->item->isEmpty())
                <div class="px-6 py-10 text-center">
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400">Belum ada item. Tambahkan mata pelajaran di bawah.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($kurikulum->item as $row)
                        <div class="grid grid-cols-1 gap-3 px-5 py-4 lg:grid-cols-[1.2fr_1fr_1fr_auto] lg:items-center">
                            <div>
                                <p class="text-xs font-black text-slate-800 dark:text-slate-100">{{ $row->mataPelajaran?->nama ?? '—' }}</p>
                                <p class="text-[10px] text-slate-400">KKM {{ $row->kkm }} · Urutan {{ $row->urutan }}</p>
                            </div>
                            <div class="lg:col-span-2">
                                <p class="text-[11px] leading-relaxed text-slate-600 dark:text-slate-300">
                                    {{ $row->target_kd ?: 'Target kompetensi belum diisi.' }}
                                </p>
                            </div>
                            @if ($bisaUbah)
                                <div class="flex flex-wrap items-center gap-2">
                                    <details class="relative">
                                        <summary class="cursor-pointer rounded-lg border border-slate-200 px-2.5 py-1.5 text-[10px] font-bold text-slate-600 list-none dark:border-slate-700 dark:text-slate-300">
                                            Edit
                                        </summary>
                                        <form method="POST" action="{{ route('admin.akademik.kurikulum.item.update', [$kurikulum, $row]) }}"
                                            class="absolute right-0 z-10 mt-2 w-72 space-y-2 rounded-2xl border border-slate-200 bg-white p-3 shadow-xl dark:border-slate-700 dark:bg-slate-900">
                                            @csrf
                                            @method('PUT')
                                            <input type="number" name="kkm" min="0" max="100" required value="{{ $row->kkm }}" class="{{ $inputCls }}" placeholder="KKM">
                                            <textarea name="target_kd" rows="2" maxlength="1000" class="{{ $inputCls }}" placeholder="Target kompetensi dasar">{{ $row->target_kd }}</textarea>
                                            <input type="number" name="urutan" min="0" max="999" value="{{ $row->urutan }}" class="{{ $inputCls }}" placeholder="Urutan">
                                            <button type="submit" class="w-full rounded-lg bg-emerald-700 px-3 py-2 text-[10px] font-black text-white">Simpan</button>
                                        </form>
                                    </details>
                                    <form method="POST" action="{{ route('admin.akademik.kurikulum.item.destroy', [$kurikulum, $row]) }}"
                                        onsubmit="return confirm('Hapus item ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="rounded-lg border border-rose-200 px-2.5 py-1.5 text-[10px] font-bold text-rose-600 hover:bg-rose-50 dark:border-rose-500/30 dark:text-rose-300">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        @if ($bisaUbah)
            <form method="POST" action="{{ route('admin.akademik.kurikulum.item.store', $kurikulum) }}"
                class="space-y-4 rounded-3xl border border-sky-200 dark:border-sky-500/30 bg-white dark:bg-slate-900 p-5 shadow-sm">
                @csrf
                <h3 class="text-sm font-black text-slate-900 dark:text-white">
                    <i class="fa-solid fa-plus mr-1.5 text-sky-600"></i> Tambah Item
                </h3>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-4">
                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Mata Pelajaran</label>
                        <select name="mata_pelajaran_id" required class="{{ $inputCls }}">
                            <option value="">Pilih mapel</option>
                            @foreach ($daftarMapel as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">KKM</label>
                        <input type="number" name="kkm" min="0" max="100" value="70" required class="{{ $inputCls }}">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Urutan</label>
                        <input type="number" name="urutan" min="0" max="999" value="{{ $kurikulum->item->count() + 1 }}" class="{{ $inputCls }}">
                    </div>
                    <div class="space-y-1.5 sm:col-span-4">
                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Target Kompetensi Dasar <span class="font-normal text-slate-400">(opsional)</span></label>
                        <textarea name="target_kd" rows="2" maxlength="1000" class="{{ $inputCls }}" placeholder="Contoh: Menghafal dan memahami nahwu dasar"></textarea>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-5 py-2.5 text-xs font-black text-white transition hover:bg-sky-700">
                        <i class="fa-solid fa-plus"></i> Tambah ke Kurikulum
                    </button>
                </div>
            </form>
        @endif
    </div>
@endsection
