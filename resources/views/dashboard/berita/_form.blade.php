@php
    $input = 'w-full text-xs px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500';
    $label = 'block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1.5';
    $b = $berita ?? null;
@endphp

@if ($errors->any())
    <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 text-red-600 text-xs border border-red-100">
        <ul class="list-disc pl-4 space-y-0.5">
            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
@endif

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6">
        <div>
            <label class="{{ $label }}">Judul Berita *</label>
            <input type="text" name="judul" value="{{ old('judul', $b?->judul) }}" class="{{ $input }}" required>
        </div>
        <div>
            <label class="{{ $label }}">Ringkasan (maks. 300 karakter)</label>
            <textarea name="ringkasan" rows="3" maxlength="300" class="{{ $input }}">{{ old('ringkasan', $b?->ringkasan) }}</textarea>
        </div>
        <div>
            <label class="{{ $label }}">Isi Berita *</label>
            <textarea name="isi" rows="14" class="{{ $input }}" required>{{ old('isi', $b?->isi) }}</textarea>
        </div>
    </div>

    <div class="space-y-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 h-fit">
        <div>
            <label class="{{ $label }}">Kategori *</label>
            <select name="kategori" class="{{ $input }}">
                @foreach (\App\Models\Berita::KATEGORI as $k)
                    <option value="{{ $k }}" @selected(old('kategori', $b?->kategori) === $k)>{{ $k }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $label }}">Status *</label>
            <select name="status" class="{{ $input }}">
                <option value="draft" @selected(old('status', $b?->status) === 'draft')>Draft</option>
                <option value="published" @selected(old('status', $b?->status) === 'published')>Terbitkan</option>
            </select>
        </div>
        <div>
            <label class="{{ $label }}">Tanggal Terbit (opsional)</label>
            <input type="datetime-local" name="published_at"
                value="{{ old('published_at', $b?->published_at?->format('Y-m-d\TH:i')) }}" class="{{ $input }}">
            <p class="text-[10px] text-slate-400 mt-1">Kosongkan untuk terbit sekarang.</p>
        </div>
        <div>
            <label class="{{ $label }}">Gambar Utama (maks. 2 MB)</label>
            <img id="preview" src="{{ $b?->gambar ? $b->gambar_url : '' }}"
                class="{{ $b?->gambar ? '' : 'hidden' }} w-full h-36 object-cover rounded-xl mb-2 border border-slate-100">
            <input type="file" name="gambar" accept="image/*" class="text-xs w-full"
                onchange="const f=this.files[0]; if(f){const p=document.getElementById('preview'); p.src=URL.createObjectURL(f); p.classList.remove('hidden');}">
        </div>
        <div class="flex gap-2 pt-2">
            <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition">Simpan</button>
            <a href="{{ route('admin.berita.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold">Batal</a>
        </div>
    </div>
</div>
