@php
    $in  = 'w-full text-xs px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500';
    $lb  = 'block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1.5';
    $err = 'text-[11px] text-red-600 mt-1';
    $v   = fn ($f) => old($f, $pengguna?->$f ?? '');
    $edit = ! empty($pengguna);

    // Pengelola selain Super Admin hanya boleh membuat/mengubah akun wali santri.
    $hanyaWali = count($roles) === 1 && array_key_exists('walisantri', $roles);
    $roleAwal = old('role', $pengguna?->peranSistem() ?: ($hanyaWali ? 'walisantri' : ''));

    $labelJenjang = \App\Http\Controllers\Admin\PenggunaController::jenjangOptions();
    $jenjangAwal = old('jenjang', \App\Support\Jenjang::normalisasi($jenjangTertaut ?? null) ?: '');

    $santriAwal = old('santri_id', $santriTertaut[0] ?? '');
    $sudahTertaut = ! empty($santriTertaut);
    $wajibSantri = ($wajibSantri ?? false) && ! $sudahTertaut;
@endphp

@if ($errors->any())
    <div class="px-4 py-3 rounded-xl bg-red-50 text-red-700 text-xs font-bold border border-red-100">Periksa kembali isian yang bertanda merah.</div>
@endif

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4"
     x-data='{ role: @json($roleAwal) }'>
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
        <label class="{{ $lb }}">Nomor HP</label>
        <input type="text" name="phone" value="{{ $v('phone') }}" class="{{ $in }}" placeholder="08xxxxxxxxxx">
        @error('phone') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $lb }}">Peran *</label>

        @if ($hanyaWali)
            {{-- Pengelola terbatas: peran dikunci ke Wali Santri. --}}
            <input type="hidden" name="role" value="walisantri">
            <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-600 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-300">
                <i class="fa-solid fa-lock text-[10px] text-slate-400"></i>
                Wali Santri
            </div>
            <p class="text-[10px] text-slate-400 mt-1">Anda hanya dapat mengelola akun wali santri.</p>
        @else
            <select name="role" class="{{ $in }}" required x-model="role">
                <option value="">- Pilih peran -</option>
                @foreach ($roles as $key => $label)
                    <option value="{{ $key }}" @selected($roleAwal === $key)>{{ $label }}</option>
                @endforeach
            </select>
        @endif

        @error('role') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>

    <div x-show="role === 'admin-jenjang'" x-cloak>
        <label class="{{ $lb }}">Jenjang yang ditangani *</label>
        <select name="jenjang" class="{{ $in }}" x-bind:required="role === 'admin-jenjang'">
            <option value="">- Pilih jenjang -</option>
            @foreach ($labelJenjang as $slug => $label)
                <option value="{{ $slug }}" @selected($jenjangAwal === $slug)>{{ $label }}</option>
            @endforeach
        </select>
        @error('jenjang') <p class="{{ $err }}">{{ $message }}</p> @enderror
        <p class="text-[10px] text-slate-400 mt-1">Data Admin Jenjang dibatasi ke jenjang yang dipilih.</p>
    </div>

    {{-- Tautan anak untuk akun wali santri --}}
    <div class="sm:col-span-2" x-show="role === 'walisantri'" x-cloak>
        <label class="{{ $lb }}">
            Santri / Anak Tertaut
            @if ($wajibSantri)
                *
            @endif
        </label>

        @if ($sudahTertaut && empty($santriList))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-[11px] font-bold text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300">
                <i class="fa-solid fa-link mr-1"></i>
                Akun ini sudah tertaut ke santri. Tidak ada santri lain yang bisa ditautkan saat ini.
            </div>
        @elseif (empty($santriList))
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-[11px] font-bold text-amber-700 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-300">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                Belum ada santri yang tersedia untuk ditautkan. Tambahkan data santri terlebih dahulu.
            </div>
        @else
            <select name="santri_id" class="{{ $in }}" @required($wajibSantri)>
                <option value="">- Pilih santri -</option>
                @foreach ($santriList as $id => $label)
                    <option value="{{ $id }}" @selected((string) $santriAwal === (string) $id)>{{ $label }}</option>
                @endforeach
            </select>
            <p class="text-[10px] text-slate-400 mt-1">
                Akun wali akan memantau data santri yang dipilih di portal wali.
                @if ($sudahTertaut)
                    Kosongkan bila tidak ingin mengubah tautan saat ini.
                @endif
            </p>
        @endif

        @error('santri_id') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $lb }}">Kata sandi {{ $edit ? '(kosongkan jika tidak diubah)' : '(kosongkan = babul2026)' }}</label>
        <input type="password" name="password" class="{{ $in }}" autocomplete="new-password">
        @error('password') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $lb }}">Konfirmasi kata sandi</label>
        <input type="password" name="password_confirmation" class="{{ $in }}" autocomplete="new-password">
    </div>

    <div class="sm:col-span-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 px-4 py-3">
        <p class="text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Catatan peran</p>

        @if ($hanyaWali)
            <ul class="text-[11px] text-slate-500 dark:text-slate-400 space-y-0.5 list-disc pl-4">
                <li><b>Wali Santri</b> — hanya masuk ke portal wali dan memantau data anaknya.</li>
                <li>Data santri hanya bisa diubah oleh Super Admin atau Admin Jenjang.</li>
                @if (\App\Support\Akses::dibatasiJenjang(auth()->user()))
                    <li>Anda hanya dapat mengelola wali santri pada jenjang <b>{{ auth()->user()->namaJenjang() }}</b>.</li>
                @endif
            </ul>
        @else
            <ul class="text-[11px] text-slate-500 dark:text-slate-400 space-y-0.5 list-disc pl-4">
                <li><b>Super Admin</b> — akses penuh + kelola pengguna & konten institusional.</li>
                <li><b>Admin Jenjang</b> — data operasional, dibatasi ke satu jenjang yang dipilih.</li>
                <li><b>Pengasuh</b> — monitoring seluruh pesantren (hanya baca).</li>
                <li><b>Pembina</b> — monitoring santri & kehadiran (hanya baca).</li>
                <li><b>Wali Santri</b> — hanya portal wali, tidak bisa masuk panel admin.</li>
                <li><b>Alumni</b> — hanya portal alumni.</li>
            </ul>
        @endif
    </div>
</div>
