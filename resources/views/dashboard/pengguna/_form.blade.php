@php
    $in  = 'w-full text-xs px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500';
    $lb  = 'block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1.5';
    $err = 'text-[11px] text-red-600 mt-1';
    $v   = fn ($f) => old($f, $pengguna->$f ?? '');
    $edit = !empty($pengguna);
@endphp

@if ($errors->any())
    <div class="px-4 py-3 rounded-xl bg-red-50 text-red-700 text-xs font-bold border border-red-100">Periksa kembali isian yang bertanda merah.</div>
@endif

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <label class="{{ $lb }}">Nama lengkap *</label>
        <input type="text" name="name" value="{{ $v('name') }}" class="{{ $in }}" required>
        @error('name') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $lb }}">Email *</label>
        <input type="email" name="email" value="{{ $v('email') }}" class="{{ $in }}" required>
        @error('email') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $lb }}">Peran *</label>
        <select name="role" class="{{ $in }}" required>
            <option value="">- Pilih peran -</option>
            @foreach ($roles as $key => $label)
                <option value="{{ $key }}" @selected($v('role') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        @error('role') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $lb }}">Kata sandi {{ $edit ? '(kosongkan jika tidak diubah)' : '*' }}</label>
        <input type="password" name="password" class="{{ $in }}" autocomplete="new-password" @required(!$edit)>
        @error('password') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $lb }}">Konfirmasi kata sandi</label>
        <input type="password" name="password_confirmation" class="{{ $in }}" autocomplete="new-password" @required(!$edit)>
    </div>
</div>