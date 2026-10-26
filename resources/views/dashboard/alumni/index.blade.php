@extends('layouts.admin')

@section('title', 'Data Alumni - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Data Alumni</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Kelola data lulusan Pondok Pesantren Babul Hasanah beserta kegiatan mereka setelah lulus.</p>
            </div>
            <a href="{{ route('admin.alumni.create') }}"
                class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-plus"></i> Tambah Alumni
            </a>
        </div>

        @if (session('success'))
            <div class="px-4 py-3 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="px-4 py-3 rounded-xl bg-red-50 text-red-700 text-xs font-bold border border-red-100">{{ session('error') }}</div>
        @endif

        <form method="GET" class="flex flex-wrap gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau instansi..."
                class="w-full sm:w-72 text-xs px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <select name="angkatan"
                class="text-xs px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua angkatan</option>
                @foreach ($daftarAngkatan as $th)
                    <option value="{{ $th }}" @selected(request('angkatan') == $th)>{{ $th }}</option>
                @endforeach
            </select>
            <button class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold">Cari</button>
            @if (request()->filled('q') || request()->filled('angkatan'))
                <a href="{{ route('admin.alumni.index') }}"
                    class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold">Reset</a>
            @endif
        </form>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-100 dark:border-slate-800">
                            <th class="py-3 px-6 font-extrabold">Alumni</th>
                            <th class="py-3 px-6 font-extrabold">Angkatan</th>
                            <th class="py-3 px-6 font-extrabold">Jenjang</th>
                            <th class="py-3 px-6 font-extrabold">Kegiatan Sekarang</th>
                            <th class="py-3 px-6 font-extrabold">Kontak</th>
                            <th class="py-3 px-6 font-extrabold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse ($alumnis as $a)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        @if ($a->foto_url)
                                            <img src="{{ $a->foto_url }}" alt="" class="w-10 h-10 object-cover rounded-full border border-slate-100 dark:border-slate-700">
                                        @else
                                            <span class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 flex items-center justify-center text-[11px] font-black">
                                                {{ mb_strtoupper(mb_substr($a->nama, 0, 1)) }}
                                            </span>
                                        @endif
                                        <div>
                                            <div class="font-black text-slate-900 dark:text-white">{{ $a->nama }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $a->jenis_kelamin === 'L' ? 'Laki-laki' : ($a->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">{{ $a->tahun_lulus }}</td>
                                <td class="py-4 px-6">{{ $a->jenjang ?? '-' }}</td>
                                <td class="py-4 px-6">
                                    @if ($a->kegiatan)
                                        <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 rounded-lg text-[10px] font-bold">{{ $a->kegiatan }}</span>
                                    @endif
                                    <div class="text-[10px] text-slate-400 mt-1">{{ $a->instansi }}</div>
                                </td>
                                <td class="py-4 px-6 text-slate-500 dark:text-slate-400">
                                    <div>{{ $a->no_hp ?? '-' }}</div>
                                    <div class="text-[10px]">{{ $a->email }}</div>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.alumni.edit', $a) }}"
                                            class="px-2.5 py-1 bg-amber-50 dark:bg-amber-950/50 text-amber-600 hover:bg-amber-100 rounded-lg text-[10px] font-bold transition">Edit</a>
                                        <form action="{{ route('admin.alumni.destroy', $a) }}" method="POST" onsubmit="return confirm('Hapus data alumni ini?')">
                                            @csrf @method('DELETE')
                                            <button class="px-2.5 py-1 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-[10px] font-bold transition">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-8 text-center text-slate-400 text-xs">Belum ada data alumni.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($alumnis->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">{{ $alumnis->links() }}</div>
            @endif
        </div>
    </div>
@endsection