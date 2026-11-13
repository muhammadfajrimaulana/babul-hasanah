@extends('layouts.admin')

@section('title', 'Manajemen Pengguna - Pondok Pesantren Babul Hasanah')

@php
    $warnaPeran = [
        'super_admin' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-900/50',
        'admin' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-900/50',
        'pengasuh' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-900/50',
        'pembina' => 'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-950/40 dark:text-orange-300 dark:border-orange-900/50',
        'wali_santri' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/40 dark:text-sky-300 dark:border-sky-900/50',
        'alumni' => 'bg-violet-50 text-violet-700 border-violet-200 dark:bg-violet-950/40 dark:text-violet-300 dark:border-violet-900/50',
    ];

    $ikonJenjang = fn ($jenjang) => \App\Support\Jenjang::label($jenjang) ?: 'Semua jenjang';

    // Nilai awal modal Alpine: buka kembali form bila validasi gagal.
    $modalAwal = old('_form', '');
    $alpineAwal = [
        'modal' => $modalAwal,
        'tambah' => [
            'id' => null,
            'name' => old('_form') === 'tambah' ? old('name', '') : '',
            'email' => old('_form') === 'tambah' ? old('email', '') : '',
            'role' => old('_form') === 'tambah' ? old('role', '') : '',
            'jenjang' => old('_form') === 'tambah' ? old('jenjang', '') : '',
            'phone' => old('_form') === 'tambah' ? old('phone', '') : '',
            'status' => 'active',
            'aktif' => true,
        ],
        'edit' => [
            'id' => old('_id'),
            'name' => old('name', ''),
            'email' => old('email', ''),
            'role' => old('role', ''),
            'jenjang' => old('jenjang', ''),
            'phone' => old('phone', ''),
            'status' => '',
            'aktif' => true,
        ],
    ];
@endphp

@section('content')
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="space-y-6" x-data="manajemenPengguna({{ \Illuminate\Support\Js::from($alpineAwal) }})">

        {{-- ============================ HEADER ============================ --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                    Manajemen Pengguna
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-2xl leading-relaxed">
                    Kelola akun panel dan portal. Super Admin dapat mengatur semua akun; Pengasuh/Pembina dapat
                    mendaftarkan Admin Jenjang &amp; Wali Santri; Admin Jenjang hanya mengelola wali santri pada
                    jenjangnya.
                </p>
            </div>

            @if (! empty($rolesBolehDibuat))
                <button type="button" @click="bukaTambah()"
                    class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white text-xs font-black px-5 py-3 rounded-xl transition shadow-lg shadow-emerald-600/20">
                    <i class="fa-solid fa-plus text-[11px]"></i>
                    Tambah Pengguna
                </button>
            @endif
        </div>

        {{-- ============================ FLASH ============================ --}}
        @if (session('success'))
            <div
                class="flex items-start gap-2.5 px-4 py-3 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-900/50">
                <i class="fa-solid fa-circle-check mt-0.5"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div
                class="flex items-start gap-2.5 px-4 py-3 rounded-xl bg-rose-50 text-rose-700 text-xs font-bold border border-rose-100 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-900/50">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div
                class="px-4 py-3 rounded-xl bg-rose-50 text-rose-700 text-xs font-bold border border-rose-100 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-900/50">
                <p class="flex items-center gap-2"><i class="fa-solid fa-circle-exclamation"></i> Periksa kembali isian
                    formulir.</p>
                <ul class="list-disc pl-5 mt-1.5 space-y-0.5 font-medium">
                    @foreach ($errors->all() as $pesan)
                        <li>{{ $pesan }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ============================ STATISTIK ============================ --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-5 shadow-sm flex items-center gap-4">
                <div
                    class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center text-base">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Akun</p>
                    <p class="text-xl font-black text-slate-900 dark:text-white">{{ $statistik['total'] }}</p>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-5 shadow-sm flex items-center gap-4">
                <div
                    class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-300 flex items-center justify-center text-base">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Aktif</p>
                    <p class="text-xl font-black text-slate-900 dark:text-white">{{ $statistik['aktif'] }}</p>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-5 shadow-sm flex items-center gap-4">
                <div
                    class="w-11 h-11 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-300 flex items-center justify-center text-base">
                    <i class="fa-solid fa-user-clock"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Pending</p>
                    <p class="text-xl font-black text-slate-900 dark:text-white">{{ $statistik['pending'] }}</p>
                </div>
            </div>
        </div>

        {{-- ============================ TAB KATEGORI ============================ --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm p-2">
            <div class="flex gap-1 overflow-x-auto custom-scrollbar">
                @foreach ($tabs as $key => $tab)
                    @php
                        $aktif = $kategoriAktif === $key;
                    @endphp
                    <a href="{{ route('super.pengguna.index', array_filter(['kategori' => $key, 'q' => $filter['q'] ?? null, 'status' => $filter['status'] ?? null, 'jenjang' => $filter['jenjang'] ?? null])) }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-[11px] font-bold whitespace-nowrap transition
                            {{ $aktif
                                ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20'
                                : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60' }}">
                        <i class="fa-solid {{ $tab['ikon'] }} text-[10px]"></i>
                        {{ $tab['label'] }}
                        <span
                            class="px-1.5 py-0.5 rounded-md text-[9px] font-black {{ $aktif ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400' }}">
                            {{ $hitung[$key] ?? 0 }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ============================ FILTER ============================ --}}
        <form method="GET" action="{{ route('super.pengguna.index') }}"
            class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm p-4">
            <input type="hidden" name="kategori" value="{{ $kategoriAktif }}">

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-5 relative">
                    <i
                        class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-[11px] text-slate-400"></i>
                    <input type="text" name="q" value="{{ $filter['q'] ?? '' }}" placeholder="Cari nama atau email..."
                        class="w-full pl-10 pr-4 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                @if ($punyaStatus)
                    <div class="sm:col-span-3">
                        <select name="status"
                            class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Semua status</option>
                            <option value="active" @selected(($filter['status'] ?? '') === 'active')>Aktif</option>
                            <option value="pending" @selected(($filter['status'] ?? '') === 'pending')>Pending</option>
                        </select>
                    </div>
                @endif

                @if ($bolehSemua)
                    <div class="sm:col-span-2">
                        <select name="jenjang"
                            class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Semua jenjang</option>
                            @foreach ($jenjangList as $slug => $label)
                                <option value="{{ $slug }}" @selected(($filter['jenjang'] ?? '') === $slug)>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-2 bg-slate-900 dark:bg-white dark:text-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition">
                        <i class="fa-solid fa-filter text-[10px]"></i> Filter
                    </button>
                    @if (array_filter([$filter['q'] ?? null, $filter['status'] ?? null, $filter['jenjang'] ?? null]))
                        <a href="{{ route('super.pengguna.index', ['kategori' => $kategoriAktif]) }}"
                            class="inline-flex items-center justify-center w-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-300 hover:bg-slate-200 transition"
                            title="Reset filter">
                            <i class="fa-solid fa-rotate-left text-[11px]"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>

        {{-- ============================ TABEL ============================ --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full min-w-[760px] text-left">
                    <thead
                        class="bg-slate-50 dark:bg-slate-800/60 text-[10px] uppercase tracking-wider text-slate-400 font-black">
                        <tr>
                            <th class="px-5 py-3.5">Pengguna</th>
                            <th class="px-4 py-3.5">Peran</th>
                            <th class="px-4 py-3.5">Jenjang / Santri</th>
                            <th class="px-4 py-3.5">Status</th>
                            <th class="px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($penggunas as $p)
                            @php
                                $warna = $warnaPeran[$p->role] ?? 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700';
                                $inisial = strtoupper(mb_substr($p->name, 0, 1));
                                $aktif = $p->sudahAktif();
                            @endphp
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 shrink-0 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center text-xs font-black shadow-sm">
                                            {{ $inisial }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-black text-slate-800 dark:text-white truncate max-w-[220px]">
                                                {{ $p->name }}
                                                @if ($p->id === auth()->id())
                                                    <span
                                                        class="ml-1 text-[9px] font-bold text-emerald-600 dark:text-emerald-400">(Anda)</span>
                                                @endif
                                            </p>
                                            <p class="text-[11px] text-slate-400 truncate max-w-[240px]">{{ $p->email }}</p>
                                            @if ($p->phone)
                                                <p class="text-[10px] text-slate-400 mt-0.5">
                                                    <i class="fa-solid fa-phone text-[8px] mr-1"></i>{{ $p->phone }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border text-[10px] font-black {{ $warna }}">
                                        {{ $p->labelPeran() }}
                                    </span>
                                </td>

                                <td class="px-4 py-4">
                                    @if ($p->peranSistem() === 'admin-jenjang')
                                        <span class="text-[11px] font-bold text-slate-600 dark:text-slate-300">
                                            <i class="fa-solid fa-layer-group text-[9px] text-emerald-500 mr-1"></i>
                                            {{ $ikonJenjang($p->jenjang) }}
                                        </span>
                                    @elseif ($p->peranSistem() === 'walisantri')
                                        <span class="text-[11px] font-bold text-slate-600 dark:text-slate-300">
                                            <i class="fa-solid fa-child-reaching text-[9px] text-sky-500 mr-1"></i>
                                            {{ $p->santri_count }} santri tertaut
                                        </span>
                                    @else
                                        <span class="text-[10px] text-slate-400">—</span>
                                    @endif
                                </td>

                                <td class="px-4 py-4">
                                    @if ($aktif)
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-black dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-900/50">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-black dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-900/50">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Pending
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button"
                                            @click="bukaEdit({{ \Illuminate\Support\Js::from([
                                                'id' => $p->id,
                                                'name' => $p->name,
                                                'email' => $p->email,
                                                'role' => $p->peranSistem(),
                                                'jenjang' => \App\Support\Jenjang::normalisasi($p->jenjang),
                                                'phone' => $p->phone,
                                                'status' => $p->statusAkun(),
                                                'aktif' => $aktif,
                                            ]) }})"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-100 hover:text-emerald-700 dark:hover:bg-emerald-950/40 dark:hover:text-emerald-300 text-[10px] font-bold transition"
                                            title="Edit pengguna">
                                            <i class="fa-solid fa-pen text-[10px]"></i>
                                            <span class="hidden lg:inline">Edit</span>
                                        </button>

                                        @if (! $aktif)
                                            <form action="{{ route('super.pengguna.aktifkan', $p) }}" method="POST"
                                                onsubmit="return confirm('Aktifkan akun {{ addslashes($p->name) }}? Sistem akan membuat dan mengirim kata sandi baru ke email pengguna.')">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold transition"
                                                    title="Aktifkan akun">
                                                    <i class="fa-solid fa-user-check text-[10px]"></i>
                                                    <span class="hidden lg:inline">Aktifkan</span>
                                                </button>
                                            </form>
                                        @endif

                                        @if ($bisaReset)
                                            <form action="{{ route('super.pengguna.reset-password', $p) }}" method="POST"
                                                onsubmit="return confirm('Reset kata sandi {{ addslashes($p->name) }} ke babul2026?')">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-2 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 hover:bg-amber-100 text-[10px] font-bold transition"
                                                    title="Reset kata sandi ke babul2026">
                                                    <i class="fa-solid fa-key text-[10px]"></i>
                                                    <span class="hidden xl:inline">Reset</span>
                                                </button>
                                            </form>
                                        @endif

                                        <button type="button"
                                            @click="konfirmasiHapus({{ \Illuminate\Support\Js::from(['id' => $p->id, 'name' => $p->name, 'peran' => $p->labelPeran()]) }})"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-2 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-300 hover:bg-rose-100 text-[10px] font-bold transition"
                                            title="Hapus akun">
                                            <i class="fa-solid fa-trash-can text-[10px]"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-14 text-center">
                                    <div
                                        class="mx-auto w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-xl mb-3">
                                        <i class="fa-solid fa-users-slash"></i>
                                    </div>
                                    <p class="text-sm font-black text-slate-700 dark:text-slate-200">Belum ada pengguna</p>
                                    <p class="text-xs text-slate-400 mt-1">Tambahkan akun baru atau ubah kata kunci
                                        pencarian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($penggunas->hasPages())
                <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $penggunas->links() }}
                </div>
            @endif
        </div>

        {{-- ============================ MODAL TAMBAH ============================ --}}
        @include('dashboard.pengguna._modal-tambah')

        {{-- ============================ MODAL EDIT ============================ --}}
        @include('dashboard.pengguna._modal-edit')

        {{-- ============================ MODAL HAPUS ============================ --}}
        <div x-show="modalHapus" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
            @keydown.escape.window="modalHapus = false">
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-800 max-w-sm w-full p-6 sm:p-8 space-y-5 text-center"
                @click.away="modalHapus = false">
                <div
                    class="mx-auto w-14 h-14 bg-rose-100 dark:bg-rose-900/30 text-rose-600 rounded-2xl flex items-center justify-center text-xl">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <div class="space-y-1.5">
                    <h3 class="text-base font-black text-slate-900 dark:text-white">Hapus Akun?</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Akun <b x-text="hapus.name"></b> (<span x-text="hapus.peran"></span>) akan dihapus. Tautan data
                        santri akan dilepas, tetapi data santri tidak ikut terhapus.
                    </p>
                    <p class="text-[10px] text-rose-500 font-bold">Super Admin terakhir tidak dapat dihapus.</p>
                </div>

                <form x-bind:action="'{{ url('super/pengguna') }}/' + hapus.id" method="POST" class="space-y-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full bg-rose-600 hover:bg-rose-700 text-white font-black px-4 py-3 rounded-xl text-xs transition shadow-lg shadow-rose-600/20">
                        Ya, Hapus Akun
                    </button>
                    <button type="button" @click="modalHapus = false"
                        class="w-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold px-4 py-2.5 rounded-xl text-xs transition">
                        Batal
                    </button>
                </form>
            </div>
        </div>

    </div>

    <script>
        function manajemenPengguna(awal) {
            return {
                modalTambah: awal.modal === 'tambah',
                modalEdit: awal.modal === 'edit',
                modalHapus: false,
                data: {
                    id: null,
                    name: '',
                    email: '',
                    role: '',
                    jenjang: '',
                    phone: '',
                    status: '',
                    aktif: true,
                },
                hapus: {
                    id: null,
                    name: '',
                    peran: ''
                },

                init() {
                    if (this.modalEdit) {
                        this.data = Object.assign(this.data, awal.edit || {});
                    } else if (this.modalTambah) {
                        this.data = Object.assign(this.data, awal.tambah || {});
                    }
                },

                bukaTambah() {
                    this.data = {
                        id: null,
                        name: '',
                        email: '',
                        role: '',
                        jenjang: '',
                        phone: '',
                        status: 'active',
                        aktif: true,
                    };
                    this.modalTambah = true;
                },

                bukaEdit(row) {
                    this.data = Object.assign({}, row);
                    this.modalEdit = true;
                },

                konfirmasiHapus(row) {
                    this.hapus = row;
                    this.modalHapus = true;
                },
            };
        }
    </script>
@endsection
