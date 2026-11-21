@extends('layouts.portal')

@section('content')
    @php
        $in = 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500';
        $lb = 'mb-1.5 block text-[11px] font-bold text-slate-600';
    @endphp

    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-black text-slate-900">Ajukan Perubahan Data</h2>
            <p class="text-xs text-slate-500">
                Portal wali bersifat hanya-baca. Jika ada data yang salah, ajukan perubahan ke Admin Jenjang untuk diperiksa.
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
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><i class="fa-solid fa-pen-to-square"></i></span>
                            <div>
                                <h3 class="text-sm font-black text-slate-900">Form Pengajuan</h3>
                                <p class="text-[10px] text-slate-400">Admin Jenjang akan menyetujui atau menolak.</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('portal.pengajuan.store') }}" class="space-y-3">
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
                            <div>
                                <label class="{{ $lb }}">Bagian Data yang Diubah</label>
                                <select name="bidang" required class="{{ $in }}">
                                    <option value="">— Pilih —</option>
                                    @foreach ($bidang as $kunci => $label)
                                        <option value="{{ $kunci }}" @selected(old('bidang') === $kunci)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('bidang') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="{{ $lb }}">Data yang Benar</label>
                                <input type="text" name="nilai_baru" value="{{ old('nilai_baru') }}" required maxlength="1000" class="{{ $in }}" placeholder="Tulis data baru">
                                @error('nilai_baru') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="{{ $lb }}">Alasan / Catatan (opsional)</label>
                                <textarea name="alasan" rows="3" maxlength="1000" class="{{ $in }}" placeholder="Contoh: nomor HP lama sudah tidak aktif">{{ old('alasan') }}</textarea>
                            </div>
                            <button type="submit" class="w-full rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-black text-white hover:bg-emerald-700">
                                <i class="fa-solid fa-paper-plane mr-1"></i> Kirim Pengajuan
                            </button>
                        </form>
                    </div>
                </div>

                <div class="space-y-4 lg:col-span-3">
                    <h3 class="text-sm font-black text-slate-900">Riwayat Pengajuan</h3>

                    @forelse ($pengajuan as $item)
                        @php
                            $badge = match ($item->status) {
                                'disetujui' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                'ditolak' => 'bg-rose-100 text-rose-700 border-rose-200',
                                default => 'bg-amber-100 text-amber-700 border-amber-200',
                            };
                        @endphp
                        <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-sm">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-lg border px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider {{ $badge }}">{{ $item->labelStatus() }}</span>
                                <span class="text-[10px] font-semibold text-slate-400">{{ $item->created_at?->translatedFormat('d M Y H:i') }}</span>
                            </div>
                            <h4 class="mt-1.5 text-sm font-black text-slate-900">{{ $item->santri?->nama_lengkap ?? 'Santri' }}</h4>
                            <p class="mt-0.5 text-[11px] text-slate-500">
                                <b>{{ $bidang[$item->bidang] ?? $item->bidang }}</b>:
                                <span class="text-slate-400 line-through">{{ $item->nilai_lama ?: '-' }}</span>
                                → <span class="font-bold text-emerald-700">{{ $item->nilai_baru }}</span>
                            </p>
                            @if ($item->alasan)
                                <p class="mt-1 text-[11px] text-slate-500"><b>Alasan:</b> {{ $item->alasan }}</p>
                            @endif
                            @if ($item->catatan_admin)
                                <p class="mt-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[11px] text-slate-600">
                                    <b>Catatan Admin:</b> {{ $item->catatan_admin }}
                                    @if ($item->pemroses) <span class="text-slate-400">· {{ $item->pemroses->name }}</span> @endif
                                </p>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-3xl border border-dashed border-slate-200 bg-white px-6 py-12 text-center text-sm text-slate-400">
                            <i class="fa-solid fa-inbox mb-2 block text-2xl text-slate-300"></i>
                            Belum ada pengajuan perubahan data.
                        </div>
                    @endforelse

                    <div>{{ $pengajuan->links() }}</div>
                </div>
            </div>
        @endif
    </div>
@endsection
