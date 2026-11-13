{{--
    Modal Edit Pengguna.
    Dipakai oleh dashboard/pengguna/index.blade.php (Alpine: modalEdit, data).
--}}
@php
    $in = 'w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500';
    $lb = 'block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1.5';
    $err = 'text-[11px] text-rose-600 mt-1';
@endphp

<div x-show="modalEdit" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
    @keydown.escape.window="modalEdit = false">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-800 w-full max-w-lg max-h-[92vh] overflow-y-auto custom-scrollbar"
        @click.away="modalEdit = false">

        <div class="px-6 pt-6 pb-4 border-b border-slate-100 dark:border-slate-800 flex items-start justify-between gap-4">
            <div>
                <h3 class="text-base font-black text-slate-900 dark:text-white">Edit Pengguna</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                    Ubah nama, email, dan peran akun.
                </p>
            </div>
            <button type="button" @click="modalEdit = false"
                class="w-8 h-8 shrink-0 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 hover:bg-slate-200 transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form x-bind:action="'{{ url('super/pengguna') }}/' + data.id" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="_form" value="edit">
            <input type="hidden" name="_id" x-bind:value="data.id">

            <div
                class="rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 px-4 py-3 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Akun</p>
                    <p class="text-xs font-black text-slate-700 dark:text-slate-200 truncate" x-text="data.email"></p>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-black border"
                    x-bind:class="data.aktif
                        ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-900/50'
                        : 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-900/50'">
                    <span class="w-1.5 h-1.5 rounded-full"
                        x-bind:class="data.aktif ? 'bg-emerald-500' : 'bg-amber-500 animate-pulse'"></span>
                    <span x-text="data.aktif ? 'Aktif' : 'Pending'"></span>
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="{{ $lb }}">Nama lengkap *</label>
                    <input type="text" name="name" x-model="data.name" class="{{ $in }}" required>
                    @error('name')
                        <p class="{{ $err }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $lb }}">Email *</label>
                    <input type="email" name="email" x-model="data.email" class="{{ $in }}" required>
                    @error('email')
                        <p class="{{ $err }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $lb }}">Nomor HP</label>
                    <input type="text" name="phone" x-model="data.phone" class="{{ $in }}"
                        placeholder="08xxxxxxxxxx">
                    @error('phone')
                        <p class="{{ $err }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $lb }}">Peran *</label>
                    <select name="role" x-model="data.role" class="{{ $in }}" required>
                        <option value="">— Pilih peran —</option>
                        @foreach ($rolesBolehDibuat as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="{{ $err }}">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="data.role === 'admin-jenjang'" x-cloak>
                    <label class="{{ $lb }}">Jenjang yang ditangani *</label>
                    <select name="jenjang" x-model="data.jenjang" class="{{ $in }}"
                        x-bind:required="data.role === 'admin-jenjang'">
                        <option value="">— Pilih jenjang —</option>
                        @foreach ($jenjangList as $slug => $label)
                            <option value="{{ $slug }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('jenjang')
                        <p class="{{ $err }}">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="{{ $lb }}">Kata sandi baru</label>
                    <input type="password" name="password" class="{{ $in }}" autocomplete="new-password"
                        placeholder="Kosongkan jika tidak diubah">
                    @error('password')
                        <p class="{{ $err }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $lb }}">Konfirmasi kata sandi</label>
                    <input type="password" name="password_confirmation" class="{{ $in }}"
                        autocomplete="new-password" placeholder="Ulangi kata sandi baru">
                </div>
            </div>

            <div
                class="rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 px-4 py-3 text-[11px] text-slate-500 dark:text-slate-400">
                <i class="fa-solid fa-shield-halved text-emerald-500 mr-1"></i>
                Super Admin terakhir tidak dapat diturunkan perannya. Untuk mereset kata sandi ke <b>babul2026</b>,
                gunakan tombol <b>Reset</b> pada tabel.
            </div>

            <div class="flex gap-2 pt-1">
                <button type="submit"
                    class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black px-5 py-3 rounded-xl transition shadow-lg shadow-emerald-600/20">
                    <i class="fa-solid fa-floppy-disk mr-1.5"></i> Simpan Perubahan
                </button>
                <button type="button" @click="modalEdit = false"
                    class="px-5 py-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
