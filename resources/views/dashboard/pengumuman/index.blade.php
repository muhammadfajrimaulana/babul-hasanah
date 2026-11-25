@extends('layouts.admin')

@section('title', 'Pengumuman - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Pengumuman</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Superadmin &amp; Pengurus dapat mengirim ke semua jenjang. Admin Jenjang hanya bisa mengirim ke jenjangnya sendiri.
                </p>
            </div>
            @if ($jenjangAdmin)
                <span class="inline-flex items-center gap-2 self-start rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2 text-[11px] font-bold text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300">
                    <i class="fa-solid fa-lock"></i> Terkunci: {{ \App\Support\Jenjang::label($jenjangAdmin) }}
                </span>
            @endif
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
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900" x-data="{ buka: false }">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                            <i class="fa-solid fa-bullhorn"></i>
                        </span>
                        <div>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white">Buat Pengumuman Baru</h3>
                            <p class="text-[10px] text-slate-400">Pengumuman langsung tampil di portal wali/alumni sesuai target jenjang.</p>
                        </div>
                    </div>
                    <button type="button" @click="buka = !buka"
                        class="rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                        <i class="fa-solid fa-plus mr-1"></i> <span x-text="buka ? 'Tutup' : 'Tulis'"></span>
                    </button>
                </div>

                <form x-show="buka" x-transition method="POST" action="{{ route('admin.pengumuman.store') }}" class="mt-5 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-[11px] font-bold text-slate-600 dark:text-slate-300">Judul</label>
                            <input type="text" name="judul" value="{{ old('judul') }}" required maxlength="180"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @error('judul') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-[11px] font-bold text-slate-600 dark:text-slate-300">Tanggal</label>
                            <input type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" required
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @error('tanggal') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[11px] font-bold text-slate-600 dark:text-slate-300">Target Jenjang</label>
                        @if ($jenjangAdmin)
                            <input type="hidden" name="target_jenjang" value="{{ $jenjangAdmin }}">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-600 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-300">
                                {{ \App\Support\Jenjang::label($jenjangAdmin) }} (dikunci sistem)
                            </div>
                        @else
                            <select name="target_jenjang"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="">Semua Jenjang (Yayasan)</option>
                                @foreach ($daftarJenjang as $j)
                                    <option value="{{ $j['slug'] }}" @selected(old('target_jenjang') === $j['slug'])>{{ $j['kode'] }} — {{ $j['nama'] }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[11px] font-bold text-slate-600 dark:text-slate-300">Isi Pengumuman</label>
                        <textarea name="isi" rows="4" required maxlength="5000"
                            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('isi') }}</textarea>
                        @error('isi') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-black text-white transition hover:bg-emerald-700">
                        <i class="fa-solid fa-paper-plane"></i> Publikasikan
                    </button>
                </form>
            </div>
        @endif

        @if (! $jenjangAdmin)
            <form method="GET" action="{{ route('admin.pengumuman.index') }}" class="flex flex-col gap-2 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:flex-row sm:items-center">
                <label class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Filter jenjang:</label>
                <select name="jenjang" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 sm:w-64">
                    <option value="">Semua</option>
                    @foreach ($daftarJenjang as $j)
                        <option value="{{ $j['slug'] }}" @selected(\App\Support\Jenjang::normalisasi($targetTerpilih) === $j['slug'])>{{ $j['kode'] }}</option>
                    @endforeach
                </select>
                <button class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white">Terapkan</button>
            </form>
        @endif

        <div class="space-y-4">
            @forelse ($pengumuman as $item)
                <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900" x-data="{ edit: false }">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300">
                                    <i class="fa-solid fa-layer-group"></i> {{ $item->labelTarget() }}
                                </span>
                                <span class="text-[10px] font-semibold text-slate-400">{{ $item->tanggal?->translatedFormat('d M Y') }}</span>
                                <span class="text-[10px] text-slate-400">· {{ $item->pembuat?->name ?? 'Sistem' }}</span>
                            </div>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white">{{ $item->judul }}</h3>
                            <p class="mt-1 whitespace-pre-line text-[11px] leading-relaxed text-slate-600 dark:text-slate-300">{{ $item->isi }}</p>
                        </div>

                        @if ($bolehUbah && (! $jenjangAdmin || $item->target_jenjang === $jenjangAdmin))
                            <div class="flex shrink-0 items-center gap-2">
                                <button type="button" @click="edit = !edit"
                                    class="rounded-xl bg-amber-50 px-3 py-2 text-[11px] font-bold text-amber-700 transition hover:bg-amber-100 dark:bg-amber-500/10 dark:text-amber-300">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('admin.pengumuman.destroy', $item) }}"
                                    onsubmit="return confirm('Hapus pengumuman ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="rounded-xl bg-rose-50 px-3 py-2 text-[11px] font-bold text-rose-700 transition hover:bg-rose-100 dark:bg-rose-500/10 dark:text-rose-300">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    @if ($bolehUbah && (! $jenjangAdmin || $item->target_jenjang === $jenjangAdmin))
                        <form x-show="edit" x-transition method="POST" action="{{ route('admin.pengumuman.update', $item) }}"
                            class="mt-4 space-y-3 rounded-2xl border border-slate-100 bg-slate-50/60 p-4 dark:border-slate-800 dark:bg-slate-800/40">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <input type="text" name="judul" value="{{ $item->judul }}" required maxlength="180"
                                    class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                <input type="date" name="tanggal" value="{{ $item->tanggal?->toDateString() }}" required
                                    class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            </div>
                            <div>
                                @if ($jenjangAdmin)
                                    <input type="hidden" name="target_jenjang" value="{{ $jenjangAdmin }}">
                                    <div class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                                        {{ \App\Support\Jenjang::label($jenjangAdmin) }} (dikunci sistem)
                                    </div>
                                @else
                                    <select name="target_jenjang"
                                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                        <option value="">Semua Jenjang</option>
                                        @foreach ($daftarJenjang as $j)
                                            <option value="{{ $j['slug'] }}" @selected($item->target_jenjang === $j['slug'])>{{ $j['kode'] }} — {{ $j['nama'] }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <textarea name="isi" rows="4" required maxlength="5000"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-white">{{ $item->isi }}</textarea>
                            <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2.5 text-[11px] font-black text-white hover:bg-emerald-700">
                                <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Perubahan
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-6 py-12 text-center text-xs text-slate-400 dark:border-slate-700 dark:bg-slate-900">
                    <i class="fa-solid fa-bullhorn mb-2 block text-2xl text-slate-300"></i>
                    Belum ada pengumuman. Pengumuman yang dibuat akan tampil di portal sesuai jenjang target.
                </div>
            @endforelse
        </div>

        <div>{{ $pengumuman->links() }}</div>
    </div>
@endsection
