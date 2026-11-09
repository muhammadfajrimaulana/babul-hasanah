@extends('layouts.admin')

@section('title', 'Pengguna Dashboard - Pondok Pesantren Babul Hasanah')

@section('content')
    @php
        $warna = [
            'slate' => [
                'ikon' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300',
                'aktif' => 'border-slate-400 ring-2 ring-slate-400/20',
            ],
            'red' => [
                'ikon' => 'bg-red-50 text-red-600 dark:bg-red-950/40 dark:text-red-400',
                'aktif' => 'border-red-400 ring-2 ring-red-400/20',
            ],
            'emerald' => [
                'ikon' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400',
                'aktif' => 'border-emerald-400 ring-2 ring-emerald-400/20',
            ],
            'amber' => [
                'ikon' => 'bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400',
                'aktif' => 'border-amber-400 ring-2 ring-amber-400/20',
            ],
            'sky' => [
                'ikon' => 'bg-sky-50 text-sky-600 dark:bg-sky-950/40 dark:text-sky-400',
                'aktif' => 'border-sky-400 ring-2 ring-sky-400/20',
            ],
        ];

        $tautan = fn (string $key) => route('super.pengguna.index', array_filter([
            'kategori' => $key,
            'q' => request('q'),
            'jenjang' => request('jenjang'),
        ], fn ($v) => filled($v)));

        $tabAktif = $tabs[$kategoriAktif] ?? null;
        $filterAktif = request()->filled('q') || request()->filled('jenjang');
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">
                    {{ $bolehSemua ? 'Pengguna Dashboard' : 'Akun Wali Santri' }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    @if ($bolehSemua)
                        Kelola semua akun yang dapat masuk ke panel, dipisahkan per kategori.
                    @elseif (\App\Support\Akses::normalisasi(auth()->user()->role) === 'admin')
                        Kelola akun wali santri yang tertaut ke santri di jenjang
                        <b>{{ auth()->user()->namaJenjang() }}</b>.
                    @else
                        Kelola akun wali santri. Perubahan data santri tetap dilakukan Admin Jenjang.
                    @endif
                </p>
            </div>
            <a href="{{ route('super.pengguna.create') }}"
                class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-plus"></i>
                {{ $bolehSemua ? 'Tambah Pengguna' : 'Tambah Wali Santri' }}
            </a>
        </div>

        @if (session('success'))
            <div class="px-4 py-3 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="px-4 py-3 rounded-xl bg-red-50 text-red-700 text-xs font-bold border border-red-100">{{ session('error') }}</div>
        @endif

        {{-- TAB KATEGORI PENGGUNA --}}
        @if (count($tabs) > 1)
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-5">
                @foreach ($tabs as $key => $tab)
                    @php $w = $warna[$tab['warna']] ?? $warna['slate']; @endphp
                    <a href="{{ $tautan($key) }}"
                        class="group rounded-2xl border bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:bg-slate-900 {{ $kategoriAktif === $key ? $w['aktif'] : 'border-slate-100 dark:border-slate-800' }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-[10px] font-extrabold uppercase tracking-wider text-slate-400">{{ $tab['label'] }}</p>
                                <p class="mt-1 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($hitung[$key] ?? 0, 0, ',', '.') }}</p>
                            </div>
                            <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl {{ $w['ikon'] }}">
                                <i class="fa-solid {{ $tab['ikon'] }}"></i>
                            </span>
                        </div>
                        <p class="mt-2 line-clamp-2 text-[10px] leading-relaxed text-slate-400">{{ $tab['deskripsi'] }}</p>
                    </a>
                @endforeach
            </div>
        @endif

        @if ($tabAktif)
            <div class="flex flex-wrap items-center gap-2 rounded-2xl border border-slate-100 bg-slate-50/70 px-4 py-3 text-[11px] text-slate-600 dark:border-slate-800 dark:bg-slate-800/40 dark:text-slate-300">
                <i class="fa-solid {{ $tabAktif['ikon'] }} text-emerald-600"></i>
                <span class="font-black">{{ $tabAktif['label'] }}</span>
                <span class="text-slate-400">·</span>
                <span>{{ $tabAktif['deskripsi'] }}</span>
                <span class="ml-auto rounded-lg bg-white px-2.5 py-1 text-[10px] font-bold text-slate-500 shadow-sm dark:bg-slate-900 dark:text-slate-300">
                    {{ $hitung[$kategoriAktif] ?? 0 }} akun
                </span>
            </div>
        @endif

        {{-- PENCARIAN --}}
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="hidden" name="kategori" value="{{ $kategoriAktif }}">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau email..."
                class="w-full sm:w-72 text-xs px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
            @if ($bolehSemua)
                <select name="jenjang"
                    class="text-xs px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua jenjang</option>
                    @foreach (($jenjangList ?? []) as $slug => $nama)
                        <option value="{{ $slug }}" @selected(request('jenjang') === $slug)>{{ $nama }}</option>
                    @endforeach
                </select>
            @endif
            <button class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold">Cari</button>
            @if ($filterAktif)
                <a href="{{ $tautan($kategoriAktif) }}"
                    class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold">Reset</a>
            @endif
        </form>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-100 dark:border-slate-800">
                            <th class="py-3 px-6 font-extrabold">Pengguna</th>
                            <th class="py-3 px-6 font-extrabold">Peran</th>
                            <th class="py-3 px-6 font-extrabold">Jenjang</th>
                            <th class="py-3 px-6 font-extrabold">Anak Tertaut</th>
                            <th class="py-3 px-6 font-extrabold">Dibuat</th>
                            <th class="py-3 px-6 font-extrabold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse ($penggunas as $p)
                            @php
                                $badge = match ($p->role) {
                                    'super_admin' => 'bg-red-50 dark:bg-red-950/50 text-red-600',
                                    'admin'       => 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400',
                                    'pengasuh'    => 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400',
                                    'pembina'     => 'bg-cyan-50 dark:bg-cyan-950/50 text-cyan-700 dark:text-cyan-400',
                                    default       => 'bg-sky-50 dark:bg-sky-950/50 text-sky-600',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <span class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 flex items-center justify-center text-[11px] font-black">
                                            {{ mb_strtoupper(mb_substr($p->name, 0, 1)) }}
                                        </span>
                                        <div>
                                            <div class="font-black text-slate-900 dark:text-white">
                                                {{ $p->name }}
                                                @if ($p->id === auth()->id())
                                                    <span class="ml-1 text-[9px] font-bold text-slate-400">(Anda)</span>
                                                @endif
                                            </div>
                                            <div class="text-[10px] text-slate-400">{{ $p->email }}</div>
                                            @if ($p->phone)
                                                <div class="text-[10px] text-slate-400"><i class="fa-solid fa-phone mr-1"></i>{{ $p->phone }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $badge }}">{{ $roles[$p->role] ?? ($p->role ?: '-') }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    @if ($p->jenjang)
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">{{ $jenjangList[$p->jenjang] ?? \App\Support\Jenjang::label($p->jenjang) }}</span>
                                    @else
                                        <span class="text-[10px] text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    @if ($p->role === 'wali_santri')
                                        @if (($p->santri_count ?? 0) > 0)
                                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">
                                                {{ $p->santri_count }} santri
                                            </span>
                                        @else
                                            <span class="text-[10px] text-slate-400">Belum tertaut</span>
                                        @endif
                                    @else
                                        <span class="text-[10px] text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-slate-500 dark:text-slate-400">{{ $p->created_at?->format('d/m/Y') }}</td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('super.pengguna.edit', $p) }}"
                                            class="px-2.5 py-1 bg-amber-50 dark:bg-amber-950/50 text-amber-600 hover:bg-amber-100 rounded-lg text-[10px] font-bold transition">Edit</a>
                                        @if ($p->id !== auth()->id())
                                            <form action="{{ route('super.pengguna.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus pengguna ini? Akun wali akan dilepas dari santrinya.')">
                                                @csrf @method('DELETE')
                                                <button class="px-2.5 py-1 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-[10px] font-bold transition">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-users-slash mb-2 block text-2xl"></i>
                                    @if ($filterAktif)
                                        Tidak ada pengguna yang cocok dengan pencarian.
                                    @else
                                        Belum ada akun pada kategori {{ $tabAktif['label'] ?? 'ini' }}.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($penggunas->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">{{ $penggunas->links() }}</div>
            @endif
        </div>

        @unless ($bolehSemua)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-[11px] leading-relaxed text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-200">
                <i class="fa-solid fa-circle-info mr-1"></i>
                <b>Batas akses Anda:</b> hanya akun <b>wali santri</b> yang dapat dikelola.
                Akun Super Admin, Admin Jenjang, Pengasuh, dan Pembina hanya dapat dikelola oleh Super Admin.
            </div>
        @endunless
    </div>
@endsection
