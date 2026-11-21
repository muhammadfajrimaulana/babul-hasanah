@extends('layouts.alumni')

@section('title', 'Profil Alumni')

@section('content')
    @php
        $in = 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500';
        $lb = 'mb-1.5 block text-[11px] font-bold text-slate-600';
        $card = 'rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm';
        $lock = 'rounded-2xl border border-slate-100 bg-slate-50/70 p-3.5';
        $label = 'text-[10px] font-black uppercase tracking-wider text-slate-400';
        $santri = $alumni->santri;
        $riwayat = $santri?->riwayatKelas()->latest()->first();
    @endphp

    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-black text-slate-900">Profil Alumni</h2>
            <p class="text-xs text-slate-500">
                Perbarui data kontak dan karier Anda. Data riwayat akademik dan pelanggaran bersifat terkunci.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">
            <div class="{{ $card }} lg:col-span-3">
                <h3 class="mb-4 text-sm font-black text-slate-900">Data yang Bisa Diperbarui</h3>
                <form method="POST" action="{{ route('alumni.profil.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="{{ $lb }}">No. HP</label>
                            <input type="text" name="no_hp" value="{{ old('no_hp', $alumni->no_hp) }}" maxlength="30" class="{{ $in }}" placeholder="08xx">
                            @error('no_hp') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $lb }}">Email</label>
                            <input type="email" name="email" value="{{ old('email', $alumni->email ?: Auth::user()->email) }}" maxlength="255" class="{{ $in }}">
                            @error('email') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-[10px] text-slate-400">Email ini juga dipakai untuk login.</p>
                        </div>
                        <div>
                            <label class="{{ $lb }}">Kegiatan Saat Ini</label>
                            <select name="kegiatan" class="{{ $in }}">
                                <option value="">— Pilih —</option>
                                @foreach ($kegiatan as $keg)
                                    <option value="{{ $keg }}" @selected(old('kegiatan', $alumni->kegiatan) === $keg)>{{ $keg }}</option>
                                @endforeach
                            </select>
                            @error('kegiatan') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $lb }}">Instansi / Kampus / Tempat Kerja</label>
                            <input type="text" name="instansi" value="{{ old('instansi', $alumni->instansi) }}" maxlength="255" class="{{ $in }}">
                            @error('instansi') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="{{ $lb }}">Alamat Sekarang</label>
                        <textarea name="alamat" rows="3" maxlength="1000" class="{{ $in }}">{{ old('alamat', $alumni->alamat) }}</textarea>
                        @error('alamat') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-black text-white hover:bg-indigo-700">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Data
                    </button>
                </form>
            </div>

            <div class="{{ $card }} lg:col-span-2">
                <div class="mb-4 flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-500"><i class="fa-solid fa-lock"></i></span>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">Riwayat Terkunci</h3>
                        <p class="text-[10px] text-slate-400">Tidak dapat diubah oleh alumni.</p>
                    </div>
                </div>

                <dl class="space-y-2.5">
                    <div class="{{ $lock }}"><dt class="{{ $label }}">Nama</dt><dd class="mt-0.5 text-xs font-bold text-slate-800">{{ $alumni->nama ?: $santri?->nama_lengkap }}</dd></div>
                    <div class="{{ $lock }}"><dt class="{{ $label }}">Jenjang Terakhir</dt><dd class="mt-0.5 text-xs font-bold text-slate-800">{{ \App\Support\Jenjang::label($riwayat?->jenjang ?: $alumni->jenjang) ?: '-' }}</dd></div>
                    <div class="{{ $lock }}"><dt class="{{ $label }}">Kelas Terakhir</dt><dd class="mt-0.5 text-xs font-bold text-slate-800">{{ $riwayat?->kelas ?: $santri?->kelas ?: '-' }}</dd></div>
                    <div class="{{ $lock }}"><dt class="{{ $label }}">Nomor Ijazah</dt><dd class="mt-0.5 font-mono text-xs font-bold text-slate-800">{{ $alumni->nomor_ijazah ?: '-' }}</dd></div>
                    <div class="{{ $lock }}"><dt class="{{ $label }}">Tahun Lulus</dt><dd class="mt-0.5 text-xs font-bold text-slate-800">{{ $alumni->tahun_lulus ?: '-' }}</dd></div>
                    <div class="{{ $lock }}"><dt class="{{ $label }}">NISN</dt><dd class="mt-0.5 font-mono text-xs font-bold text-slate-800">{{ $alumni->nisn ?: $santri?->nomor_induk ?: '-' }}</dd></div>
                </dl>

                <p class="mt-3 rounded-xl bg-amber-50 px-3 py-2 text-[10px] leading-relaxed text-amber-700">
                    <i class="fa-solid fa-circle-info mr-1"></i>
                    History nilai, hafalan, presensi, pelanggaran, dan prestasi tidak dapat diubah. Hubungi Admin Jenjang bila ada kekeliruan data.
                </p>
            </div>
        </div>
    </div>
@endsection
