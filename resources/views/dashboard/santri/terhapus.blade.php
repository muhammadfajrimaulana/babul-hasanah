@extends('layouts.admin')

@section('title', 'Tempat Sampah Santri - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Tempat Sampah Santri</h2>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                    Data yang dihapus ditandai, bukan dihilangkan. Nilai, izin, absensi, dokumen, alumni, dan riwayat PPDB tetap tersimpan.
                </p>
            </div>
            <a href="{{ route('admin.santri.index') }}"
                class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Data Santri
            </a>
        </div>

        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-[11px] text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
            <i class="fa-solid fa-circle-info mr-1.5"></i>
            Hanya Superadmin yang dapat memulihkan data. Penghapusan permanen sengaja tidak disediakan di halaman ini.
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <table class="w-full min-w-[720px] text-left text-xs">
                <thead class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-500 dark:bg-slate-800/60 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3 font-bold">Santri</th>
                        <th class="px-4 py-3 font-bold">Jenjang</th>
                        <th class="px-4 py-3 font-bold">Kelas</th>
                        <th class="px-4 py-3 font-bold">Dihapus Pada</th>
                        <th class="px-4 py-3 text-right font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($santris as $santri)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-bold text-slate-800 dark:text-slate-100">{{ $santri->nama_lengkap }}</p>
                                <p class="text-[10px] text-slate-500">NIS {{ $santri->nomor_induk }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $santri->jenjang_pendidikan ?: '-' }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $santri->kelas ?: '-' }}</td>
                            <td class="px-4 py-3 text-slate-500">
                                {{ $santri->deleted_at?->translatedFormat('d M Y H:i') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form action="{{ route('admin.santri.pulihkan', $santri->id) }}" method="POST" class="inline-block"
                                    onsubmit="return confirm(@js('Pulihkan '.$santri->nama_lengkap.'? Seluruh riwayatnya akan tersedia lagi.'));">
                                    @csrf
                                    <button type="submit"
                                        class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 hover:bg-emerald-100 rounded-lg text-[10px] font-bold transition">
                                        <i class="fa-solid fa-trash-arrow-up mr-1"></i> Pulihkan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 text-xs">Tempat sampah kosong.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $santris->links() }}
    </div>
@endsection
