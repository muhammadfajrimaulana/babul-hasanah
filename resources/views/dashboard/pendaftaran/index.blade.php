@extends('layouts.admin')

@section('content')
    <div class="space-y-5 pb-8">

        <!-- HEADER HALAMAN -->
        <div
            class="bg-white dark:bg-slate-900 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span
                    class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-extrabold uppercase tracking-wider border border-emerald-500/20 mb-1">
                    Modul Seleksi
                </span>
                <h1 class="text-lg sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Verifikasi PPDB</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola dan verifikasi status calon santri masuk.
                </p>
            </div>
            <div
                class="self-start sm:self-center bg-slate-900 dark:bg-slate-800 text-white px-3.5 py-2 rounded-2xl text-xs font-bold shadow-sm flex items-center gap-2 border border-slate-800 dark:border-slate-700">
                <i class="fa-solid fa-users text-emerald-400"></i>
                <span>Total: {{ count($pendaftar) }} Pendaftar</span>
            </div>
        </div>

        @if (session('success'))
            <div
                class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300 px-4 py-3.5 rounded-2xl text-xs font-bold flex items-center gap-2.5 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div
                class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-300 px-4 py-3.5 rounded-2xl text-xs font-bold flex items-center gap-2.5 shadow-sm">
                <i class="fa-solid fa-circle-exclamation text-rose-600 dark:text-rose-400 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- TOOLBAR LIVE SEARCH -->
        <div
            class="bg-white dark:bg-slate-900 p-3.5 sm:p-4 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="relative">
                <span
                    class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400 dark:text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" id="liveSearchInput" onkeyup="filterData()"
                    placeholder="Cari nama, nomor pendaftaran, NISN, atau sekolah asal..."
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
            </div>
        </div>

        <!-- KONTEN UTAMA: DAFTAR PENDAFTAR -->
        <div
            class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200/80 dark:border-slate-800 overflow-hidden p-4 sm:p-6 space-y-4">

            @php
                $statusBadge = function ($status) {
                    return match (strtolower($status ?? 'pending')) {
                        'diterima' => 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300',
                        'ditolak' => 'bg-rose-100 dark:bg-rose-500/20 text-rose-800 dark:text-rose-300',
                        default => 'bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300',
                    };
                };
                $statusText = fn ($status) => ucfirst(strtolower($status ?? 'pending'));
            @endphp

            <!-- TAMPILAN MOBILE: KARTU BERTUMPUK -->
            <div class="space-y-3 sm:hidden" id="mobileListContainer">
                @forelse($pendaftar as $item)
                    @php
                        $statusMobile = strtolower($item->status_pendaftaran ?? 'pending');
                        $keyword = $item->nama_lengkap . ' ' . $item->no_pendaftaran . ' ' . $item->nisn . ' ' . $item->asal_sekolah . ' ' . $item->email . ' ' . $item->nama_ortu_wali;
                    @endphp
                    <div class="p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-800/50 border border-slate-200/70 dark:border-slate-700/60 space-y-3.5 shadow-sm searchable-item"
                        data-keyword="{{ strtolower($keyword) }}">
                        <div class="flex items-center justify-between border-b border-slate-200/60 dark:border-slate-700/60 pb-2.5">
                            <span
                                class="font-mono font-bold text-emerald-700 dark:text-emerald-400 text-xs bg-emerald-50 dark:bg-emerald-500/10 px-2.5 py-1 rounded-lg border border-emerald-500/20">
                                {{ $item->no_pendaftaran }}
                            </span>
                            <span class="{{ $statusBadge($statusMobile) }} text-[10px] font-bold px-2.5 py-1 rounded-full">
                                {{ $statusText($statusMobile) }}
                            </span>
                        </div>

                        <h3 class="text-sm font-black text-slate-900 dark:text-white">{{ $item->nama_lengkap }}</h3>

                        <dl class="grid grid-cols-1 gap-2 text-[11px]">
                            <div><dt class="font-bold text-slate-500 dark:text-slate-400">Tempat Tanggal Lahir</dt><dd class="text-slate-800 dark:text-slate-200">{{ $item->tempat_tanggal_lahir ?? '-' }}</dd></div>
                            <div><dt class="font-bold text-slate-500 dark:text-slate-400">No. HP Aktif (WA)</dt><dd class="text-slate-800 dark:text-slate-200">{{ $item->no_hp_aktif ?? '-' }}</dd></div>
                            <div><dt class="font-bold text-slate-500 dark:text-slate-400">Alamat Tinggal (Domisili)</dt><dd class="text-slate-800 dark:text-slate-200">{{ $item->alamat_domisili ?? '-' }}</dd></div>
                            <div><dt class="font-bold text-slate-500 dark:text-slate-400">Nama Orang Tua (Wali)</dt><dd class="text-slate-800 dark:text-slate-200">{{ $item->nama_ortu_wali ?? '-' }}</dd></div>
                            <div><dt class="font-bold text-slate-500 dark:text-slate-400">No. HP Orang Tua</dt><dd class="text-slate-800 dark:text-slate-200">{{ $item->no_hp_ortu ?? '-' }}</dd></div>
                            <div><dt class="font-bold text-slate-500 dark:text-slate-400">Email Orang Tua / Wali <span class="font-normal normal-case">(login wali)</span></dt><dd class="text-slate-800 dark:text-slate-200 break-all">{{ $item->email ?? '-' }}</dd></div>
                            <div><dt class="font-bold text-slate-500 dark:text-slate-400">Sekolah Asal</dt><dd class="text-slate-800 dark:text-slate-200">{{ $item->asal_sekolah ?? '-' }}</dd></div>
                            <div><dt class="font-bold text-slate-500 dark:text-slate-400">Alamat Sekolah Asal</dt><dd class="text-slate-800 dark:text-slate-200">{{ $item->alamat_sekolah ?? '-' }}</dd></div>
                            <div class="grid grid-cols-2 gap-2">
                                <div><dt class="font-bold text-slate-500 dark:text-slate-400">Tahun Lulus</dt><dd class="text-slate-800 dark:text-slate-200">{{ $item->tahun_lulus ?? '-' }}</dd></div>
                                <div><dt class="font-bold text-slate-500 dark:text-slate-400">NISN</dt><dd class="text-slate-800 dark:text-slate-200">{{ $item->nisn ?? '-' }}</dd></div>
                            </div>
                            <div><dt class="font-bold text-slate-500 dark:text-slate-400">Pilihan Jenjang</dt><dd class="text-slate-800 dark:text-slate-200">{{ $item->jenjang_tujuan ?? '-' }}</dd></div>
                        </dl>

                        @if (auth()->user()->bisa('pendaftaran.ubah'))
                            <div class="pt-1">
                                @if ($statusMobile == 'pending' || ! $item->status_pendaftaran)
                                    <form action="{{ route('admin.pendaftaran.verifikasi', $item->id) }}" method="POST"
                                        class="grid grid-cols-2 gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" name="status_pendaftaran" value="diterima"
                                            class="bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white py-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                                            <i class="fa-solid fa-check"></i> Terima
                                        </button>
                                        <button type="submit" name="status_pendaftaran" value="ditolak"
                                            class="bg-rose-50 dark:bg-rose-500/10 hover:bg-rose-100 dark:hover:bg-rose-500/20 active:scale-95 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30 py-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                                            <i class="fa-solid fa-xmark"></i> Tolak
                                        </button>
                                    </form>
                                @else
                                    <div class="text-center py-2 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-400 dark:text-slate-500 text-xs font-medium italic">
                                        <i class="fa-solid fa-lock text-[10px] mr-1"></i> Selesai Diproses
                                    </div>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <a href="{{ route('admin.pendaftaran.edit', $item->id) }}"
                                    class="bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 dark:hover:bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30 py-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </a>
                                <form action="{{ route('admin.pendaftaran.destroy', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus data pendaftaran ini secara permanen? Data santri hasil verifikasi juga akan dihapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full bg-slate-50 dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-slate-200 dark:border-slate-700 py-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-center text-[11px] font-bold text-slate-500 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-300">
                                <i class="fa-solid fa-eye mr-1"></i> Mode monitoring — verifikasi hanya oleh Admin Jenjang.
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-12 text-slate-400 dark:text-slate-500 italic text-xs">
                        Belum ada data pendaftaran.
                    </div>
                @endforelse
            </div>

            <!-- TAMPILAN DESKTOP: TABEL LENGKAP -->
            <div class="hidden sm:block overflow-x-auto pb-4">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            <th class="py-3 px-3.5 rounded-l-xl">No. Pendaftaran</th>
                            <th class="py-3 px-3.5">Nama Lengkap</th>
                            <th class="py-3 px-3.5">Tempat Tanggal Lahir</th>
                            <th class="py-3 px-3.5">No. HP Aktif (WA)</th>
                            <th class="py-3 px-3.5">Alamat Tinggal (Domisili)</th>
                            <th class="py-3 px-3.5">Nama Orang Tua (Wali)</th>
                            <th class="py-3 px-3.5">No. HP Orang Tua</th>
                            <th class="py-3 px-3.5">Email Orang Tua / Wali</th>
                            <th class="py-3 px-3.5">Sekolah Asal</th>
                            <th class="py-3 px-3.5">Alamat Sekolah Asal</th>
                            <th class="py-3 px-3.5">Tahun Lulus</th>
                            <th class="py-3 px-3.5">NISN</th>
                            <th class="py-3 px-3.5">Pilihan Jenjang</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center rounded-r-xl">Aksi Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs text-slate-700 dark:text-slate-300 font-medium" id="desktopTableBody">
                        @forelse($pendaftar as $item)
                            @php
                                $statusDesktop = strtolower($item->status_pendaftaran ?? 'pending');
                                $keyword = $item->nama_lengkap . ' ' . $item->no_pendaftaran . ' ' . $item->nisn . ' ' . $item->asal_sekolah . ' ' . $item->email . ' ' . $item->nama_ortu_wali;
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition searchable-item"
                                data-keyword="{{ strtolower($keyword) }}">
                                <td class="py-3.5 px-3.5 font-mono font-bold text-emerald-700 dark:text-emerald-400">{{ $item->no_pendaftaran }}</td>
                                <td class="py-3.5 px-3.5 font-bold text-slate-900 dark:text-white">{{ $item->nama_lengkap }}</td>
                                <td class="py-3.5 px-3.5">{{ $item->tempat_tanggal_lahir ?? '-' }}</td>
                                <td class="py-3.5 px-3.5 font-mono">{{ $item->no_hp_aktif ?? '-' }}</td>
                                <td class="py-3.5 px-3.5 max-w-xs whitespace-normal">{{ $item->alamat_domisili ?? '-' }}</td>
                                <td class="py-3.5 px-3.5">{{ $item->nama_ortu_wali ?? '-' }}</td>
                                <td class="py-3.5 px-3.5 font-mono">{{ $item->no_hp_ortu ?? '-' }}</td>
                                <td class="py-3.5 px-3.5 text-slate-500">{{ $item->email ?? '-' }}</td>
                                <td class="py-3.5 px-3.5">{{ $item->asal_sekolah ?? '-' }}</td>
                                <td class="py-3.5 px-3.5 max-w-xs whitespace-normal">{{ $item->alamat_sekolah ?? '-' }}</td>
                                <td class="py-3.5 px-3.5 text-center">{{ $item->tahun_lulus ?? '-' }}</td>
                                <td class="py-3.5 px-3.5 font-mono text-slate-500">{{ $item->nisn ?? '-' }}</td>
                                <td class="py-3.5 px-3.5">
                                    <span class="bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded font-semibold">{{ $item->jenjang_tujuan ?? '-' }}</span>
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <span class="{{ $statusBadge($statusDesktop) }} text-[10px] font-bold px-2.5 py-0.5 rounded-full">
                                        {{ $statusText($statusDesktop) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <div class="inline-flex items-center gap-1">
                                        @if (auth()->user()->bisa('pendaftaran.ubah'))
                                            @if ($statusDesktop == 'pending' || ! $item->status_pendaftaran)
                                                <form action="{{ route('admin.pendaftaran.verifikasi', $item->id) }}" method="POST"
                                                    class="inline-flex gap-1">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" name="status_pendaftaran" value="diterima"
                                                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-2.5 py-1 rounded text-[11px] font-bold transition">
                                                        Terima
                                                    </button>
                                                    <button type="submit" name="status_pendaftaran" value="ditolak"
                                                        class="bg-rose-600 hover:bg-rose-700 text-white px-2.5 py-1 rounded text-[11px] font-bold transition">
                                                        Tolak
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-slate-400 dark:text-slate-500 text-[11px] italic">Selesai</span>
                                            @endif

                                            <a href="{{ route('admin.pendaftaran.edit', $item->id) }}"
                                                class="bg-amber-100 dark:bg-amber-500/20 hover:bg-amber-200 dark:hover:bg-amber-500/30 text-amber-800 dark:text-amber-300 px-2.5 py-1 rounded text-[11px] font-bold transition">
                                                Edit
                                            </a>

                                            <form action="{{ route('admin.pendaftaran.destroy', $item->id) }}" method="POST"
                                                class="inline-flex"
                                                onsubmit="return confirm('Hapus data pendaftaran ini secara permanen? Data santri hasil verifikasi juga akan dihapus.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="bg-slate-100 dark:bg-slate-800 hover:bg-rose-100 dark:hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 px-2.5 py-1 rounded text-[11px] font-bold transition">
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500 text-[11px] italic">
                                                <i class="fa-solid fa-eye mr-1"></i> Hanya baca
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="text-center py-10 text-slate-400 dark:text-slate-500 italic">Belum ada data pendaftaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pesan Kosong Jika Hasil Live Search Nihil -->
            <div id="noResultFound" class="hidden text-center py-12 text-slate-400 dark:text-slate-500 italic text-xs">
                <i class="fa-regular fa-face-frown text-2xl mb-2 block text-slate-300 dark:text-slate-600"></i>
                Data pendaftar yang dicari tidak ditemukan.
            </div>

        </div>
    </div>

    <script>
        function filterData() {
            let input = document.getElementById('liveSearchInput').value.toLowerCase();
            let items = document.querySelectorAll('.searchable-item');
            let foundCount = 0;

            items.forEach(item => {
                let keyword = (item.getAttribute('data-keyword') || '').toLowerCase();
                if (keyword.includes(input)) {
                    item.style.display = "";
                    foundCount++;
                } else {
                    item.style.display = "none";
                }
            });

            let noResult = document.getElementById('noResultFound');
            if (foundCount === 0 && items.length > 0) {
                noResult.classList.remove('hidden');
            } else {
                noResult.classList.add('hidden');
            }
        }
    </script>
@endsection
