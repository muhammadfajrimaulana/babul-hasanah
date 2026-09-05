@extends('layouts.admin')

@section('title', 'Data Santri - Pondok Pesantren Babul Hasanah')

@section('content')
    <div class="space-y-6" x-data="{ importModalOpen: false }">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Manajemen Data Santri</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Kelola informasi data santri aktif Pondok Pesantren
                    Babul Hasanah dari semua jenjang.</p>
            </div>
            <div class="flex items-center gap-2">
                <!-- Tombol Trigger Modal Import -->
                <button @click="importModalOpen = true"
                    class="bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-file-excel"></i> Import Excel
                </button>

                <a href="#"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-plus"></i> Tambah Santri Baru
                </a>
            </div>
        </div>

        <!-- MODAL IMPORT EXCEL -->
        <div x-show="importModalOpen" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-fade-in"
            @keydown.escape.window="importModalOpen = false">

            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-800 max-w-md w-full p-6 sm:p-8 space-y-5 relative"
                @click.away="importModalOpen = false">

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-xl flex items-center justify-center text-lg">
                            <i class="fa-solid fa-file-excel"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white">Import Data Santri</h3>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">Unggah file berformat .xlsx atau .csv
                            </p>
                        </div>
                    </div>
                    <button @click="importModalOpen = false"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Form Upload -->
                <form action="{{ route('admin.santri.import') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Pilih Berkas Excel</label>
                        <input type="file" name="file_excel" accept=".xlsx, .xls, .csv" required
                            class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 dark:file:bg-emerald-950/50 dark:file:text-emerald-400 hover:file:bg-emerald-100 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer">
                        <p class="text-[10px] text-slate-400">Pastikan struktur kolom sesuai dengan template.</p>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/50 p-3 rounded-2xl flex items-center justify-between text-xs">
                        <span class="text-slate-600 dark:text-slate-400">Butuh format acuannya?</span>
                        <a href="{{ route('admin.santri.template') }}"
                            class="text-emerald-600 dark:text-emerald-400 font-bold hover:underline flex items-center gap-1.5">
                            <i class="fa-solid fa-download"></i> Download Template
                        </a>
                    </div>

                    <div class="pt-2 flex gap-2">
                        <button type="button" @click="importModalOpen = false"
                            class="flex-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold px-4 py-2.5 rounded-xl text-xs transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-upload"></i> Proses Import
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <!-- TABEL DATA SANTRI (Sama seperti sebelumnya) -->
        <div
            class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-300">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr
                            class="bg-slate-50 dark:bg-slate-800/50 text-slate-400 dark:text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-100 dark:border-slate-800">
                            <th class="py-3 px-6 font-extrabold">No. Induk / Nama</th>
                            <th class="py-3 px-6 font-extrabold">Jenjang & Kelas</th>
                            <th class="py-3 px-6 font-extrabold">Nama Wali</th>
                            <th class="py-3 px-6 font-extrabold">Alamat</th>
                            <th class="py-3 px-6 font-extrabold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse($santris as $santri)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                <td class="py-4 px-6">
                                    <div class="font-black text-slate-900 dark:text-white">{{ $santri->nama_lengkap }}</div>
                                    <div class="text-[10px] text-slate-400">NI: {{ $santri->nomor_induk ?? '-' }}</div>
                                </td>
                                <td class="py-4 px-6">
                                    <span
                                        class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 rounded-lg text-[10px] font-bold">
                                        {{ $santri->jenjang_pendidikan }} - {{ $santri->kelas ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-slate-600 dark:text-slate-400">
                                    {{ $santri->nama_wali ?? '-' }}
                                </td>
                                <td class="py-4 px-6 text-slate-600 dark:text-slate-400 max-w-xs truncate">
                                    {{ $santri->alamat ?? '-' }}
                                </td>
                                <td class="py-4 px-6 text-right space-x-1">
                                    <a href="{{ route('admin.santri.detail', $santri->id) }}"
                                        class="px-2.5 py-1 bg-sky-50 dark:bg-sky-950/50 text-sky-600 hover:bg-sky-100 rounded-lg text-[10px] font-bold transition">Detail</a>
                                    <button
                                        class="px-2.5 py-1 bg-amber-50 dark:bg-amber-950/50 text-amber-600 hover:bg-amber-100 rounded-lg text-[10px] font-bold transition">Edit</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400 text-xs">Belum ada data santri
                                    yang tersimpan di database.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($santris, 'links'))
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $santris->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
