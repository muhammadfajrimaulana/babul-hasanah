{{--
    Modal Tambah Pengguna.
    Dipakai oleh dashboard/pengguna/index.blade.php (Alpine: modalTambah, data, modalTambah).
--}}
@php
    $in = 'w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500';
    $lb = 'block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1.5';
    $err = 'text-[11px] text-rose-600 mt-1';
@endphp

<div x-show="modalTambah" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
    @keydown.escape.window="modalTambah = false">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-800 w-full max-w-lg max-h-[92vh] overflow-y-auto custom-scrollbar"
        @click.away="modalTambah = false">

        <div class="px-6 pt-6 pb-4 border-b border-slate-100 dark:border-slate-800 flex items-start justify-between gap-4">
            <div>
                <h3 class="text-base font-black text-slate-900 dark:text-white">Tambah Pengguna Baru</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                    Buat akun dan tentukan perannya. Akun langsung berstatus Aktif.
                </p>
            </div>
            <button type="button" @click="modalTambah = false"
                class="w-8 h-8 shrink-0 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 hover:bg-slate-200 transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('super.pengguna.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="_form" value="tambah">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="{{ $lb }}">Nama lengkap *</label>
                    <input type="text" name="name" x-model="data.name" class="{{ $in }}"
                        placeholder="Nama pengguna" required>
                    @error('name')
                        <p class="{{ $err }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $lb }}">Email *</label>
                    <input type="email" name="email" x-model="data.email" class="{{ $in }}"
                        placeholder="nama@babulhasanah.sch.id" required>
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

            {{-- Tautan santri untuk akun wali santri --}}
            <div x-show="data.role === 'walisantri'" x-cloak>
                <label class="{{ $lb }}">Santri / Anak Tertaut {{ $bolehSemua ? '' : '*' }}</label>

                @if (empty($santriList))
                    <div
                        class="rounded-xl border border-amber-200 bg-amber-50 dark:border-amber-900/50 dark:bg-amber-950/30 px-4 py-2.5 text-[11px] font-bold text-amber-700 dark:text-amber-300">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        Belum ada santri yang tersedia. Tambahkan data santri terlebih dahulu melalui menu Data Santri.
                    </div>
                @else
                    <select name="santri_id" x-model="data.santri_id" class="{{ $in }}"
                        x-bind:required="data.role === 'walisantri' && {{ $bolehSemua ? 'false' : 'true' }}">
                        <option value="">— Pilih santri —</option>
                        @foreach ($santriList as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1">
                        Akun wali akan memantau data santri yang dipilih di portal wali.
                    </p>
                @endif

                @error('santri_id')
                    <p class="{{ $err }}">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="{{ $lb }}">Kata sandi</label>
                    <input type="password" name="password" class="{{ $in }}" autocomplete="new-password"
                        placeholder="Kosongkan = babul2026">
                    @error('password')
                        <p class="{{ $err }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $lb }}">Konfirmasi kata sandi</label>
                    <input type="password" name="password_confirmation" class="{{ $in }}"
                        autocomplete="new-password" placeholder="Ulangi kata sandi">
                </div>
            </div>

            <div
                class="rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 px-4 py-3 text-[11px] text-slate-500 dark:text-slate-400">
                <p class="font-bold text-slate-600 dark:text-slate-300 mb-1">
                    <i class="fa-solid fa-circle-info text-emerald-500 mr-1"></i>Catatan
                </p>
                <ul class="list-disc pl-4 space-y-0.5">
                    <li>Bila kata sandi dikosongkan, sistem memakai kata sandi default <b>babul2026</b>.</li>
                    <li>Akun yang dibuat pengelola langsung berstatus <b>Aktif</b>.</li>
                    <li>Khusus Wali Santri, kata sandi otomatis dapat dikirim ulang lewat tombol <b>Reset</b>.</li>
                </ul>
            </div>

            <div class="flex gap-2 pt-1">
                <button type="submit"
                    class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black px-5 py-3 rounded-xl transition shadow-lg shadow-emerald-600/20">
                    <i class="fa-solid fa-floppy-disk mr-1.5"></i> Simpan Pengguna
                </button>
                <button type="button" @click="modalTambah = false"
                    class="px-5 py-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
