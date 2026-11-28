@extends('layouts.alumni')

@section('title', 'Profil & Tracer Study')

@section('content')
    @php
        $in = 'w-full rounded-xl border border-[#e4e0d4] bg-white px-4 py-2.5 text-xs text-[#1f2a24] focus:outline-none focus:ring-2 focus:ring-[#0e9f73]';
        $lb = 'mb-1.5 block text-[11px] font-bold text-[#6b756f]';
        $card = 'rounded-[22px] border border-[#e4e0d4] bg-white p-6 shadow-sm';
        $lock = 'rounded-2xl border border-[#e4e0d4] bg-[#fbfaf6] p-3.5';
        $label = 'text-[10px] font-extrabold uppercase tracking-wider text-[#6b756f]';
        $santri = $alumni->santri;
        $riwayat = $santri?->riwayatKelas()->latest()->first();
    @endphp

    <div class="space-y-[22px]">
        <div>
            <h2 class="text-2xl font-extrabold text-[#17402f]">Profil &amp; Tracer Study</h2>
            <p class="text-[13px] text-[#6b756f]">
                Satu halaman untuk memperbarui data kontak, karier, tracer study, dan testimoni Anda. Riwayat akademik dan pelanggaran tetap terkunci.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-[22px] lg:grid-cols-5">
            <div class="{{ $card }} lg:col-span-3">
                <div class="mb-5 flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#f1eee6] text-[#17402f]"><i class="fa-solid fa-user-pen"></i></span>
                    <div>
                        <h3 class="text-base font-extrabold text-[#17402f]">Data yang Bisa Diperbarui</h3>
                        <p class="text-[10px] text-[#6b756f]">Disimpan bersamaan dalam satu tombol.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('alumni.profil.update') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <h4 class="mb-3 text-[11px] font-extrabold uppercase tracking-wider text-[#0e9f73]">Data Diri &amp; Karier</h4>
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
                                <p class="mt-1 text-[10px] text-[#6b756f]">Email ini juga dipakai untuk login.</p>
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
                            <div>
                                <label class="{{ $lb }}">Pekerjaan / Jabatan</label>
                                <input type="text" name="pekerjaan" value="{{ old('pekerjaan', $alumni->pekerjaan) }}" maxlength="150" class="{{ $in }}" placeholder="Contoh: Guru / Mahasiswa / Wirausaha">
                                @error('pekerjaan') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="{{ $lb }}">Alamat Sekarang</label>
                            <textarea name="alamat" rows="3" maxlength="1000" class="{{ $in }}">{{ old('alamat', $alumni->alamat) }}</textarea>
                            @error('alamat') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="border-t border-[#e4e0d4] pt-5">
                        <h4 class="mb-1 text-[11px] font-extrabold uppercase tracking-wider text-[#0e9f73]">Data Tracer Study</h4>
                        <p class="mb-3 text-[10px] leading-relaxed text-[#6b756f]">
                            <i class="fa-solid fa-circle-info mr-1"></i>
                            Kegiatan, instansi, dan alamat di atas ikut disimpan sebagai data tracer study untuk akreditasi yayasan.
                        </p>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="{{ $lb }}">Kontak Tracer</label>
                                <input type="text" name="kontak" value="{{ old('kontak', $tracer->kontak ?: $alumni->no_hp) }}" maxlength="100" class="{{ $in }}" placeholder="No. HP / email yang bisa dihubungi">
                                @error('kontak') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                                <p class="mt-1 text-[10px] text-[#6b756f]">Boleh sama dengan No. HP di atas.</p>
                            </div>
                            <div>
                                <label class="{{ $lb }}">Tahun Mulai</label>
                                <input type="number" name="tahun_mulai" min="1900" max="{{ date('Y') + 1 }}" value="{{ old('tahun_mulai', $tracer->tahun_mulai) }}" class="{{ $in }}" placeholder="Contoh: 2024">
                                @error('tahun_mulai') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="{{ $lb }}">Catatan / Pengalaman</label>
                            <textarea name="catatan" rows="4" maxlength="2000" class="{{ $in }}" placeholder="Pengalaman kuliah/kerja, prestasi, atau pesan untuk almamater">{{ old('catatan', $tracer->catatan) }}</textarea>
                            @error('catatan') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="border-t border-[#e4e0d4] pt-5">
                        <h4 class="mb-3 text-[11px] font-extrabold uppercase tracking-wider text-[#0e9f73]">Testimoni</h4>
                        <label class="{{ $lb }}">Testimoni untuk Pesantren</label>
                        <textarea name="testimoni" rows="4" maxlength="1000" class="{{ $in }}" placeholder="Ceritakan kesan dan pesan Anda tentang Pondok Pesantren Babul Hasanah…">{{ old('testimoni', $alumni->testimoni) }}</textarea>
                        <p class="mt-1 text-[10px] leading-relaxed text-[#6b756f]">
                            <i class="fa-solid fa-circle-info mr-1"></i>
                            Jika diisi, testimoni tampil di
                            <a href="{{ route('alumni.index') }}" target="_blank" rel="noopener" class="font-bold text-[#0e9f73] hover:underline">halaman Alumni</a>
                            dan slider “Kata Alumni” di beranda.
                        </p>
                        @error('testimoni') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="rounded-full bg-[#f1eee6] px-5 py-2.5 text-xs font-extrabold text-[#17402f] transition hover:bg-[#e4e0d4]">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Profil, Tracer &amp; Testimoni
                    </button>
                </form>
            </div>

            <div class="space-y-[22px] lg:col-span-2">
                <div class="{{ $card }}">
                    <div class="mb-4 flex items-center gap-2">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#f1eee6] text-[#17402f]"><i class="fa-solid fa-lock"></i></span>
                        <div>
                            <h3 class="text-base font-extrabold text-[#17402f]">Riwayat Terkunci</h3>
                            <p class="text-[10px] text-[#6b756f]">Tidak dapat diubah oleh alumni.</p>
                        </div>
                    </div>

                    <dl class="space-y-2.5">
                        <div class="{{ $lock }}"><dt class="{{ $label }}">Nama</dt><dd class="mt-0.5 text-xs font-bold text-[#1f2a24]">{{ $alumni->nama ?: $santri?->nama_lengkap }}</dd></div>
                        <div class="{{ $lock }}"><dt class="{{ $label }}">Jenjang Terakhir</dt><dd class="mt-0.5 text-xs font-bold text-[#1f2a24]">{{ \App\Support\Jenjang::label($riwayat?->jenjang ?: $alumni->jenjang) ?: '-' }}</dd></div>
                        <div class="{{ $lock }}"><dt class="{{ $label }}">Kelas Terakhir</dt><dd class="mt-0.5 text-xs font-bold text-[#1f2a24]">{{ $riwayat?->kelas ?: $santri?->kelas ?: '-' }}</dd></div>
                        <div class="{{ $lock }}"><dt class="{{ $label }}">Nomor Ijazah</dt><dd class="mt-0.5 font-mono text-xs font-bold text-[#1f2a24]">{{ $alumni->nomor_ijazah ?: '-' }}</dd></div>
                        <div class="{{ $lock }}"><dt class="{{ $label }}">Tahun Lulus</dt><dd class="mt-0.5 text-xs font-bold text-[#1f2a24]">{{ $alumni->tahun_lulus ?: '-' }}</dd></div>
                        <div class="{{ $lock }}"><dt class="{{ $label }}">NISN</dt><dd class="mt-0.5 font-mono text-xs font-bold text-[#1f2a24]">{{ $alumni->nisn ?: $santri?->nomor_induk ?: '-' }}</dd></div>
                    </dl>

                    <p class="mt-3 rounded-xl bg-amber-50 px-3 py-2 text-[10px] leading-relaxed text-amber-700">
                        <i class="fa-solid fa-circle-info mr-1"></i>
                        History nilai, hafalan, presensi, pelanggaran, dan prestasi tidak dapat diubah. Hubungi Admin Jenjang bila ada kekeliruan data.
                    </p>
                </div>

                <div class="{{ $card }}">
                    <div class="mb-4 flex items-center gap-2">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#f1eee6] text-[#17402f]"><i class="fa-solid fa-chart-simple"></i></span>
                        <div>
                            <h3 class="text-base font-extrabold text-[#17402f]">Status Tracer Study</h3>
                            <p class="text-[10px] text-[#6b756f]">Ringkasan data yang tersimpan.</p>
                        </div>
                    </div>

                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between rounded-xl bg-[#fbfaf6] px-3 py-2.5">
                            <span class="text-[11px] font-bold text-[#6b756f]">Kegiatan</span>
                            <span class="text-[11px] font-extrabold text-[#17402f]">{{ $tracer->kegiatan ?: 'Belum diisi' }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl bg-[#fbfaf6] px-3 py-2.5">
                            <span class="text-[11px] font-bold text-[#6b756f]">Instansi</span>
                            <span class="text-right text-[11px] font-extrabold text-[#17402f]">{{ $tracer->instansi ?: '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl bg-[#fbfaf6] px-3 py-2.5">
                            <span class="text-[11px] font-bold text-[#6b756f]">Tahun Mulai</span>
                            <span class="text-[11px] font-extrabold text-[#17402f]">{{ $tracer->tahun_mulai ?: '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl bg-[#fbfaf6] px-3 py-2.5">
                            <span class="text-[11px] font-bold text-[#6b756f]">Testimoni</span>
                            <span class="text-[11px] font-extrabold text-[#17402f]">{{ filled($alumni->testimoni) ? 'Aktif' : 'Belum diisi' }}</span>
                        </div>
                    </div>

                    <p class="mt-4 rounded-xl bg-[#f1eee6] px-3 py-2 text-[10px] leading-relaxed text-[#17402f]">
                        <i class="fa-solid fa-circle-info mr-1"></i>
                        Data tracer study ikut memperbarui kegiatan, instansi, dan alamat pada profil alumni.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
