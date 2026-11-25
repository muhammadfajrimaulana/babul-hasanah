@extends('layouts.portal')

@section('content')
    @php
        $in = 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500';
        $lb = 'mb-1.5 block text-[11px] font-bold text-slate-600';
    @endphp

    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-black text-slate-900">Izin Pulang</h2>
            <p class="text-xs text-slate-500">
                Ajukan izin pulang tanpa perlu bertanya lewat WhatsApp. Status berubah real-time setelah diperiksa Pembina / Admin Jenjang.
            </p>
        </div>

        @if ($anak->isEmpty())
            <div class="rounded-3xl border border-amber-200 bg-amber-50 px-6 py-10 text-center text-sm text-amber-800">
                Akun Anda belum tertaut ke data santri. Hubungi Admin Jenjang untuk menautkan anak Anda.
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">
                <div class="lg:col-span-2">
                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm">
                        <div class="mb-4 flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><i class="fa-solid fa-plane-departure"></i></span>
                            <div>
                                <h3 class="text-sm font-black text-slate-900">Ajukan Izin Pulang</h3>
                                <p class="text-[10px] text-slate-400">Status awal otomatis MENUNGGU.</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('portal.izin.store') }}" class="space-y-3">
                            @csrf
                            <div>
                                <label class="{{ $lb }}">Pilih Anak</label>
                                <select name="santri_id" required class="{{ $in }}">
                                    <option value="">— Pilih anak —</option>
                                    @foreach ($anak as $a)
                                        <option value="{{ $a->id }}" @selected((string) old('santri_id') === (string) $a->id)>
                                            {{ $a->nama_lengkap }} — {{ \App\Support\Jenjang::label($a->jenjang_pendidikan) }} {{ $a->kelas }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('santri_id') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="{{ $lb }}">Tanggal Pulang</label>
                                    <input type="date" name="tanggal_pulang" value="{{ old('tanggal_pulang') }}" required min="{{ now()->toDateString() }}" class="{{ $in }}">
                                    @error('tanggal_pulang') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="{{ $lb }}">Tanggal Kembali</label>
                                    <input type="date" name="tanggal_kembali" value="{{ old('tanggal_kembali') }}" required min="{{ now()->toDateString() }}" class="{{ $in }}">
                                    @error('tanggal_kembali') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div>
                                <label class="{{ $lb }}">Yang Menjemput</label>
                                <input type="text" name="penjemput" value="{{ old('penjemput') }}" required maxlength="150" class="{{ $in }}" placeholder="Nama ayah / ibu / wali">
                                @error('penjemput') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="{{ $lb }}">Alasan</label>
                                <textarea name="alasan" rows="3" required maxlength="1000" class="{{ $in }}" placeholder="Contoh: menghadiri acara keluarga">{{ old('alasan') }}</textarea>
                                @error('alasan') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <button type="submit" class="w-full rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-black text-white hover:bg-emerald-700">
                                <i class="fa-solid fa-paper-plane mr-1"></i> Kirim Pengajuan
                            </button>
                        </form>
                    </div>
                </div>

                <div class="space-y-4 lg:col-span-3">
                    <h3 class="text-sm font-black text-slate-900">Riwayat &amp; Status Izin</h3>

                    @forelse ($izin as $item)
                        <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-sm">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-lg border px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider {{ $item->kelasStatus() }}">
                                            {{ $item->labelStatus() }}
                                        </span>
                                        <span class="text-[10px] font-semibold text-slate-400">{{ $item->created_at?->translatedFormat('d M Y H:i') }}</span>
                                    </div>
                                    <h4 class="mt-1.5 text-sm font-black text-slate-900">{{ $item->santri?->nama_lengkap ?? 'Santri' }}</h4>
                                    <p class="mt-1 text-[11px] text-slate-500">
                                        Pulang <b>{{ $item->tanggal_pulang?->translatedFormat('d M Y') }}</b>
                                        · Kembali <b>{{ $item->tanggal_kembali?->translatedFormat('d M Y') }}</b>
                                        · Penjemput {{ $item->penjemput }}
                                    </p>
                                    <p class="mt-1 text-[11px] text-slate-500">{{ $item->alasan }}</p>

                                    @if ($item->status === \App\Models\Izin::DISETUJUI && $item->kode_izin)
                                        <div class="mt-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-3">
                                            <p class="text-[10px] font-black uppercase tracking-wider text-emerald-600">Kode Izin (tunjukkan di gerbang)</p>
                                            <p class="mt-0.5 font-mono text-lg font-black tracking-widest text-emerald-800">{{ $item->kode_izin }}</p>
                                        </div>
                                    @endif

                                    @if ($item->status === \App\Models\Izin::TERLAMBAT)
                                        <div class="mt-3 rounded-2xl border border-red-300 bg-red-50 p-3 text-[11px] font-bold text-red-700">
                                            <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                                            Santri belum kembali melewati tanggal yang dijanjikan. Segera hubungi Pembina.
                                        </div>
                                    @endif

                                    @if ($item->alasan_penolakan)
                                        <div class="mt-3 rounded-2xl border border-rose-200 bg-rose-50 p-3 text-[11px] text-rose-700">
                                            <b>Alasan penolakan:</b> {{ $item->alasan_penolakan }}
                                        </div>
                                    @endif

                                    @if ($item->kembali_pada)
                                        <p class="mt-2 text-[10px] text-emerald-600"><i class="fa-solid fa-check mr-1"></i> Kembali tercatat {{ $item->kembali_pada->translatedFormat('d M Y H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-3xl border border-dashed border-slate-200 bg-white px-6 py-12 text-center text-sm text-slate-400">
                            <i class="fa-solid fa-calendar-days mb-2 block text-2xl text-slate-300"></i>
                            Belum ada pengajuan izin.
                        </div>
                    @endforelse

                    <div>{{ $izin->links() }}</div>
                </div>
            </div>
        @endif
    </div>
@endsection
