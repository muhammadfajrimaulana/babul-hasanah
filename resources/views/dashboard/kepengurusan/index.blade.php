@extends('layouts.admin')

@section('title', 'Kepengurusan - Panel Admin')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Kepengurusan</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Kelola kartu pimpinan yang tampil pada bagian Kepengurusan halaman Profil.
                Untuk bagan lengkap, gunakan menu Struktur.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('profil') }}" target="_blank"
                class="px-4 py-2.5 rounded-xl text-xs font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                <i class="fa-solid fa-arrow-up-right-from-square mr-1"></i> Lihat halaman
            </a>
            <a href="{{ route('admin.kepengurusan.create') }}"
                class="px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg shadow-emerald-600/20 transition">
                <i class="fa-solid fa-plus mr-1"></i> Tambah
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm">
            <i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 px-4 py-3 text-sm">
            <i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
        <table class="w-full min-w-[640px] text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/60 text-[10px] uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="px-4 py-3 font-bold">Urutan</th>
                    <th class="px-4 py-3 font-bold">Pengurus</th>
                    <th class="px-4 py-3 font-bold">Jabatan</th>
                    <th class="px-4 py-3 text-right font-bold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($items as $item)
                    <tr>
                        <td class="px-4 py-3 text-slate-500">{{ $item->urutan }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 shrink-0 rounded-full overflow-hidden bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 grid place-items-center font-bold">
                                    @if ($item->foto_url)
                                        <img src="{{ $item->foto_url }}" alt="Foto {{ $item->nama }}" class="w-full h-full object-cover">
                                    @else
                                        {{ substr($item->nama, 0, 1) }}
                                    @endif
                                </div>
                                <span class="font-bold text-slate-800 dark:text-slate-100">{{ $item->nama }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $item->jabatan }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center gap-1">
                                <a href="{{ route('admin.kepengurusan.edit', $item) }}"
                                    class="px-2.5 py-1 bg-amber-50 dark:bg-amber-950/50 text-amber-600 hover:bg-amber-100 rounded-lg text-[10px] font-bold transition">
                                    Edit
                                </a>
                                <form action="{{ route('admin.kepengurusan.destroy', $item) }}" method="POST" class="inline-block"
                                    onsubmit="return confirm(@js('Hapus data '.$item->nama.' dari halaman Profil?'));">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-2.5 py-1 bg-rose-50 dark:bg-rose-950/50 text-rose-600 hover:bg-rose-100 rounded-lg text-[10px] font-bold transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-10 text-center text-slate-400 text-xs">
                            Belum ada data pengurus. Tambahkan data pertama untuk menampilkan kartu di halaman Profil.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="text-[11px] text-slate-400">
        Urutan kecil tampil lebih awal. Foto baru disimpan di disk <code>public</code>; berkas lama di
        <code>public/upload/kepengurusan/</code> tetap ditampilkan tanpa diubah.
    </p>
</div>
@endsection
