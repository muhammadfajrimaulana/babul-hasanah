@extends('layouts.admin')

@section('title', 'Rapor Santri - Akademik')

@use('App\Support\Akademik')

@section('content')
    @php $slugAdmin = auth()->user()->jenjangSlug(); @endphp

    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white">Rapor Santri</h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                Rata-rata dan peringkat dihitung dari nilai berstatus terbit. Catatan wali kelas diisi manual, lalu rapor diterbitkan ke portal wali.
            </p>
        </div>

        @include('dashboard.akademik._tabs')
        @include('dashboard.akademik._peringatan-jenjang')

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10 px-4 py-3 text-xs font-bold text-emerald-800 dark:text-emerald-300">
                <i class="fa-solid fa-circle-check mr-1.5"></i>{{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 dark:border-rose-500/30 bg-rose-50 dark:bg-rose-500/10 px-4 py-3 text-xs font-bold text-rose-800 dark:text-rose-300">
                <i class="fa-solid fa-triangle-exclamation mr-1.5"></i>{{ session('error') }}
            </div>
        @endif

        <form method="GET" action="{{ route('admin.akademik.rapor.index') }}"
            class="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                @if ($slugAdmin)
                    <input type="hidden" name="jenjang" value="{{ $slugAdmin }}">
                @else
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Jenjang</label>
                        <select name="jenjang"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            <option value="">Semua jenjang</option>
                            @foreach ($daftarJenjang as $j)
                                <option value="{{ $j['slug'] }}" @selected($filter['jenjang'] === $j['slug'])>
                                    {{ $j['kode'] }} — {{ $j['nama'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Kelas</label>
                    <input type="text" name="kelas" maxlength="50" value="{{ $filter['kelas'] }}" placeholder="Contoh: 7A"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Tahun Ajaran</label>
                    <select name="tahun_ajaran"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                        @foreach (Akademik::tahunAjaranPilihan() as $tahun)
                            <option value="{{ $tahun }}" @selected($filter['tahun'] === $tahun)>{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Semester</label>
                    <select name="semester"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                        @foreach (Akademik::SEMESTER as $nilai => $label)
                            <option value="{{ $nilai }}" @selected($filter['semester'] === $nilai)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-2 border-t border-slate-100 pt-4 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-[10px] text-slate-400">
                    Perhitungan ulang tidak menimpa catatan wali kelas dan tidak mengubah status rapor.
                </p>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="submit"
                        class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-600 transition hover:border-emerald-400 hover:text-emerald-700 dark:border-slate-700 dark:text-slate-300">
                        <i class="fa-solid fa-filter mr-1"></i> Tampilkan
                    </button>
                    @if ($bisaUbah)
                        <form method="POST" action="{{ route('admin.akademik.rapor.hitung-ulang') }}"
                            onsubmit="return confirm('Hitung ulang rata-rata dan peringkat rapor pada konteks ini?')">
                            @csrf
                            <input type="hidden" name="jenjang" value="{{ $filter['jenjang'] }}">
                            <input type="hidden" name="kelas" value="{{ $filter['kelas'] }}">
                            <input type="hidden" name="tahun_ajaran" value="{{ $filter['tahun'] }}">
                            <input type="hidden" name="semester" value="{{ $filter['semester'] }}">
                            <button type="submit"
                                class="rounded-xl bg-emerald-700 px-4 py-2.5 text-xs font-black text-white transition hover:bg-emerald-800">
                                <i class="fa-solid fa-rotate mr-1"></i> Hitung Ulang
                            </button>
                        </form>
                    @endif
                    @if ($bisaTerbit)
                        <form method="POST" action="{{ route('admin.akademik.rapor.terbitkan-semua') }}"
                            onsubmit="return confirm('Terbitkan semua rapor draft pada konteks ini?')">
                            @csrf
                            <input type="hidden" name="jenjang" value="{{ $filter['jenjang'] }}">
                            <input type="hidden" name="kelas" value="{{ $filter['kelas'] }}">
                            <input type="hidden" name="tahun_ajaran" value="{{ $filter['tahun'] }}">
                            <input type="hidden" name="semester" value="{{ $filter['semester'] }}">
                            <button type="submit"
                                class="rounded-xl bg-amber-500 px-4 py-2.5 text-xs font-black text-slate-900 transition hover:bg-amber-600">
                                <i class="fa-solid fa-paper-plane mr-1"></i> Terbitkan Semua Draft
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </form>

        <div class="overflow-hidden rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
            @if ($rapor->isEmpty())
                <div class="px-6 py-12 text-center space-y-2">
                    <i class="fa-solid fa-file-lines text-2xl text-slate-300 dark:text-slate-600"></i>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400">Belum ada rapor. Past nilai yang ingin diraporkan sudah diterbitkan, lalu klik Hitung Ulang.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[820px] text-left">
                        <thead class="bg-slate-50 dark:bg-slate-800/60">
                            <tr>
                                <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Santri</th>
                                <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Kelas</th>
                                <th class="px-4 py-3 text-center text-[10px] font-black uppercase tracking-wider text-slate-400">Rata-rata</th>
                                <th class="px-4 py-3 text-center text-[10px] font-black uppercase tracking-wider text-slate-400">Peringkat</th>
                                <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-slate-400">Catatan Wali Kelas</th>
                                <th class="px-4 py-3 text-center text-[10px] font-black uppercase tracking-wider text-slate-400">Status</th>
                                @if ($bisaUbah || $bisaTerbit)
                                    <th class="px-4 py-3 text-right text-[10px] font-black uppercase tracking-wider text-slate-400">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($rapor as $row)
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                                    <td class="px-4 py-3">
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ $row->santri?->nama_lengkap ?? '—' }}</p>
                                        <p class="text-[10px] font-mono text-slate-400">{{ $row->santri?->nomor_induk }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-600 dark:text-slate-300">{{ $row->kelas }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-sm font-black text-emerald-700 dark:text-emerald-400">{{ Akademik::formatNilai($row->rata_rata) }}</span>
                                        <p class="text-[10px] text-slate-400">Predikat {{ Akademik::predikat($row->rata_rata) }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-xs font-black text-slate-800 dark:text-slate-100">{{ $row->peringkat ?? '—' }}</span>
                                        <p class="text-[10px] text-slate-400">dari {{ $row->jumlah_santri }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="max-w-[260px] truncate text-[11px] text-slate-600 dark:text-slate-300" title="{{ $row->catatan_wali_kelas }}">
                                            {{ $row->catatan_wali_kelas ?: 'Belum ada catatan' }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($row->status === 'published')
                                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-black text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">Terbit</span>
                                        @else
                                            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-black text-amber-700 dark:bg-amber-500/20 dark:text-amber-300">Draft</span>
                                        @endif
                                    </td>
                                    @if ($bisaUbah || $bisaTerbit)
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-end gap-2">
                                                @if ($bisaUbah)
                                                    <a href="{{ route('admin.akademik.rapor.edit', $row) }}"
                                                        class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-[10px] font-bold text-slate-600 transition hover:border-emerald-400 hover:text-emerald-700 dark:border-slate-700 dark:text-slate-300">
                                                        <i class="fa-solid fa-pen"></i> Catatan
                                                    </a>
                                                @endif
                                                @if ($bisaTerbit && $row->status !== 'published')
                                                    <form method="POST" action="{{ route('admin.akademik.rapor.terbitkan', $row) }}"
                                                        onsubmit="return confirm('Terbitkan rapor {{ $row->santri?->nama_lengkap }}?')">
                                                        @csrf
                                                        <button type="submit"
                                                            class="rounded-lg bg-amber-500 px-2.5 py-1.5 text-[10px] font-black text-slate-900 transition hover:bg-amber-600">
                                                            <i class="fa-solid fa-paper-plane"></i> Terbitkan
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
