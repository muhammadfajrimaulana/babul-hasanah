@extends('layouts.alumni')

@section('title', 'Tracer Study')

@section('content')
    @php
        $in = 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500';
        $lb = 'mb-1.5 block text-[11px] font-bold text-slate-600';
    @endphp

    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-black text-slate-900">Tracer Study</h2>
            <p class="text-xs text-slate-500">
                Data ini membantu akreditasi yayasan. Isi kegiatan, instansi, dan kontak Anda saat ini.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm lg:col-span-2">
                <form method="POST" action="{{ route('alumni.tracer.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="{{ $lb }}">Kegiatan Saat Ini <span class="text-rose-500">*</span></label>
                            <select name="kegiatan" required class="{{ $in }}">
                                <option value="">— Pilih —</option>
                                @foreach ($kegiatan as $keg)
                                    <option value="{{ $keg }}" @selected(old('kegiatan', $tracer->kegiatan) === $keg)>{{ $keg }}</option>
                                @endforeach
                            </select>
                            @error('kegiatan') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $lb }}">Tahun Mulai</label>
                            <input type="number" name="tahun_mulai" min="1900" max="{{ date('Y') + 1 }}"
                                value="{{ old('tahun_mulai', $tracer->tahun_mulai) }}" class="{{ $in }}">
                            @error('tahun_mulai') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="{{ $lb }}">Instansi / Kampus / Tempat Kerja</label>
                            <input type="text" name="instansi" value="{{ old('instansi', $tracer->instansi) }}" maxlength="255" class="{{ $in }}">
                            @error('instansi') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $lb }}">Kontak</label>
                            <input type="text" name="kontak" value="{{ old('kontak', $tracer->kontak) }}" maxlength="100" class="{{ $in }}" placeholder="No. HP / email">
                            @error('kontak') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="{{ $lb }}">Alamat Sekarang</label>
                            <textarea name="alamat" rows="2" maxlength="1000" class="{{ $in }}">{{ old('alamat', $tracer->alamat ?: $alumni->alamat) }}</textarea>
                            @error('alamat') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="{{ $lb }}">Catatan / Pengalaman</label>
                            <textarea name="catatan" rows="4" maxlength="2000" class="{{ $in }}" placeholder="Pengalaman kuliah/kerja, prestasi, atau pesan untuk almamater">{{ old('catatan', $tracer->catatan) }}</textarea>
                            @error('catatan') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-black text-white hover:bg-indigo-700">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Tracer Study
                    </button>
                </form>
            </div>

            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-black text-slate-900">Status Pengisian</h3>
                <div class="mt-4 space-y-2.5">
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2.5">
                        <span class="text-[11px] font-bold text-slate-600">Kegiatan</span>
                        <span class="text-[11px] font-black text-slate-800">{{ $tracer->kegiatan ?: 'Belum diisi' }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2.5">
                        <span class="text-[11px] font-bold text-slate-600">Instansi</span>
                        <span class="text-[11px] font-black text-slate-800">{{ $tracer->instansi ?: '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2.5">
                        <span class="text-[11px] font-bold text-slate-600">Tahun Mulai</span>
                        <span class="text-[11px] font-black text-slate-800">{{ $tracer->tahun_mulai ?: '-' }}</span>
                    </div>
                </div>
                <p class="mt-4 rounded-xl bg-indigo-50 px-3 py-2 text-[10px] leading-relaxed text-indigo-700">
                    <i class="fa-solid fa-circle-info mr-1"></i>
                    Perubahan tracer study juga memperbarui data kegiatan pada profil alumni.
                </p>
            </div>
        </div>
    </div>
@endsection
