@php
    $in  = 'w-full text-xs px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500';
    $lb  = 'block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1.5';
    $err = 'text-[11px] text-red-600 mt-1';
    $v   = fn ($f) => old($f, $alumni->$f ?? '');
@endphp

@if ($errors->any())
    <div class="px-4 py-3 rounded-xl bg-red-50 text-red-700 text-xs font-bold border border-red-100">Periksa kembali isian yang bertanda merah.</div>
@endif

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <label class="{{ $lb }}">Nama lengkap *</label>
        <input type="text" name="nama" value="{{ $v('nama') }}" class="{{ $in }}" required>
        @error('nama') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $lb }}">Jenis kelamin</label>
        <select name="jenis_kelamin" class="{{ $in }}">
            <option value="">-</option>
            <option value="L" @selected($v('jenis_kelamin') === 'L')>Laki-laki</option>
            <option value="P" @selected($v('jenis_kelamin') === 'P')>Perempuan</option>
        </select>
        @error('jenis_kelamin') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $lb }}">Tahun lulus (angkatan) *</label>
        <input type="number" name="tahun_lulus" value="{{ $v('tahun_lulus') }}" min="1998" max="{{ now()->year + 1 }}" class="{{ $in }}" required>
        @error('tahun_lulus') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $lb }}">Jenjang terakhir</label>
        <select name="jenjang" class="{{ $in }}">
            <option value="">-</option>
            @foreach (\App\Models\Alumni::JENJANG as $j)
                <option value="{{ $j }}" @selected($v('jenjang') === $j)>{{ $j }}</option>
            @endforeach
        </select>
        @error('jenjang') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $lb }}">Kegiatan sekarang</label>
        <select name="kegiatan" class="{{ $in }}">
            <option value="">-</option>
            @foreach (\App\Models\Alumni::KEGIATAN as $k)
                <option value="{{ $k }}" @selected($v('kegiatan') === $k)>{{ $k }}</option>
            @endforeach
        </select>
        @error('kegiatan') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="{{ $lb }}">Kampus / tempat kerja / usaha</label>
        <input type="text" name="instansi" value="{{ $v('instansi') }}" class="{{ $in }}">
        @error('instansi') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $lb }}">No. HP / WhatsApp</label>
        <input type="text" name="no_hp" value="{{ $v('no_hp') }}" class="{{ $in }}">
        @error('no_hp') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $lb }}">Email</label>
        <input type="email" name="email" value="{{ $v('email') }}" class="{{ $in }}">
        @error('email') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="{{ $lb }}">Alamat</label>
        <textarea name="alamat" rows="2" class="{{ $in }}">{{ $v('alamat') }}</textarea>
        @error('alamat') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="{{ $lb }}">Testimoni / pesan untuk pesantren</label>
        <textarea name="testimoni" rows="3" class="{{ $in }}">{{ $v('testimoni') }}</textarea>
        @error('testimoni') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="{{ $lb }}">Foto (maks. 2 MB)</label>
        @if (!empty($alumni?->foto_url))
            <img src="{{ $alumni->foto_url }}" alt="" class="w-16 h-16 object-cover rounded-full mb-2 border border-slate-200 dark:border-slate-700">
        @endif
        <input type="file" name="foto" accept=".jpg,.jpeg,.png,.webp" class="{{ $in }}">
        @error('foto') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>
</div>
