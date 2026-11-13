@extends('layouts.admin')

@section('title', 'Kelulusan PPDB - Pondok Pesantren Babul Hasanah')

@section('content')
    @php
        $badge = fn ($status) => match (strtolower((string) ($status ?: 'pending'))) {
            'diterima' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300',
            'ditolak' => 'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-300',
            default => 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300',
        };
        $label = fn ($status) => match (strtolower((string) ($status ?: 'pending'))) {
            'diterima' => 'Diterima',
            'ditolak' => 'Ditolak',
            default => 'Menunggu',
        };
    @endphp

    <div class="space-y-5 pb-8">
        <div class="flex flex-col gap-3 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:flex-row sm:items-center sm:justify-between sm:rounded-3xl sm:p-6">
            <div>
                <span class="inline-block rounded-full border border-emerald-500/20 bg-emerald-50 px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                    Modul PPDB
                </span>
                <h1 class="mt-1 text-lg font-black tracking-tight text-slate-900 dark:text-white sm:text-2xl">Verifikasi Kelulusan</h1>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                    Terima pendaftar terpilih. Akun wali, data santri, dan data plotting dibuat otomatis.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.ppdb.plotting') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-3.5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700">
                    <i class="fa-solid fa-layer-group text-emerald-400"></i> Plotting Kelas
                </a>
                <span class="rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs font-bold text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    {{ $statistik['total'] }} Pendaftar
                </span>
            </div>
        </div>

        @if (session('success'))
            <div class="flex flex-col gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-xs font-bold text-emerald-800 shadow-sm dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300 sm:flex-row sm:items-center sm:justify-between">
                <span class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-sm text-emerald-600"></i>
                    {{ session('success') }}
                </span>
                @if (session('wa_link'))
                    <a href="{{ session('wa_link') }}" target="_blank" rel="noopener"
                        class="inline-flex items-center gap-1.5 self-start rounded-xl bg-emerald-600 px-3.5 py-2 text-[11px] font-bold text-white transition hover:bg-emerald-700 sm:self-center">
                        <i class="fa-brands fa-whatsapp"></i> Kirim akun ke wali {{ session('nama_santri') }}
                    </a>
                @endif
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center gap-2.5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-xs font-bold text-rose-800 shadow-sm dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-300">
                <i class="fa-solid fa-circle-exclamation text-sm text-rose-600"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            @foreach ([
                ['label' => 'Total', 'nilai' => $statistik['total'], 'ikon' => 'fa-users', 'warna' => 'slate'],
                ['label' => 'Menunggu', 'nilai' => $statistik['pending'], 'ikon' => 'fa-hourglass-half', 'warna' => 'amber'],
                ['label' => 'Diterima', 'nilai' => $statistik['diterima'], 'ikon' => 'fa-circle-check', 'warna' => 'emerald'],
                ['label' => 'Ditolak', 'nilai' => $statistik['ditolak'], 'ikon' => 'fa-circle-xmark', 'warna' => 'rose'],
            ] as $kartu)
                @php
                    $warna = match ($kartu['warna']) {
                        'amber' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-300',
                        'emerald' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300',
                        'rose' => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-300',
                        default => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300',
                    };
                @endphp
                <div class="flex items-center gap-3 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $warna }}">
                        <i class="fa-solid {{ $kartu['ikon'] }}"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">{{ $kartu['label'] }}</p>
                        <p class="text-xl font-black text-slate-900 dark:text-white">{{ $kartu['nilai'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.ppdb.kelulusan') }}"
            class="grid grid-cols-1 gap-2 rounded-2xl border border-slate-200/80 bg-white p-3.5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:grid-cols-2 sm:rounded-3xl lg:grid-cols-4">
            <div class="relative sm:col-span-2 lg:col-span-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="q" value="{{ $filter['q'] ?? '' }}" placeholder="Cari nama, NISN, no. pendaftaran..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-xs text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-100">
            </div>
            <select name="status"
                class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:outline-none dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-200">
                <option value="">Semua status</option>
                @foreach (['pending' => 'Menunggu', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak'] as $nilai => $teks)
                    <option value="{{ $nilai }}" @selected(($filter['status'] ?? '') === $nilai)>{{ $teks }}</option>
                @endforeach
            </select>
            @if (! $jenjangSlug)
                <select name="jenjang"
                    class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:outline-none dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-200">
                    <option value="">Semua jenjang</option>
                    @foreach ($jenjangList as $slug => $teks)
                        <option value="{{ $slug }}" @selected(($filter['jenjang'] ?? '') === $slug)>{{ $teks }}</option>
                    @endforeach
                </select>
            @endif
            <div class="flex gap-2">
                <button type="submit"
                    class="flex-1 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                    <i class="fa-solid fa-filter mr-1"></i> Terapkan
                </button>
                <a href="{{ route('admin.ppdb.kelulusan') }}"
                    class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    Reset
                </a>
            </div>
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:rounded-3xl">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-[10px] font-extrabold uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-400">
                            <th class="px-4 py-3">Pendaftar</th>
                            <th class="px-4 py-3">NISN</th>
                            <th class="px-4 py-3">Jenjang</th>
                            <th class="px-4 py-3">Email Wali</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700 dark:divide-slate-800 dark:text-slate-300">
                        @forelse ($pendaftar as $item)
                            @php
                                $status = strtolower((string) ($item->status_pendaftaran ?: 'pending'));
                                $email = $item->parent_email ?: $item->email;
                            @endphp
                            <tr class="align-top transition hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                <td class="px-4 py-3.5">
                                    <p class="font-black text-slate-900 dark:text-white">{{ $item->nama_lengkap }}</p>
                                    <p class="mt-0.5 font-mono text-[10px] text-emerald-700 dark:text-emerald-400">{{ $item->no_pendaftaran }}</p>
                                    <p class="mt-0.5 text-[10px] text-slate-400">{{ $item->asal_sekolah ?: 'Asal sekolah belum diisi' }}</p>
                                </td>
                                <td class="px-4 py-3.5 font-mono text-slate-500">{{ $item->nisn ?: '-' }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="rounded-lg bg-slate-100 px-2 py-1 text-[10px] font-bold dark:bg-slate-800">
                                        {{ \App\Support\Jenjang::label($item->jenjang_tujuan) ?: ($item->jenjang_tujuan ?: '-') }}
                                    </span>
                                </td>
                                <td class="max-w-[180px] break-all px-4 py-3.5 text-slate-500">{{ $email ?: '-' }}</td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="rounded-full px-2.5 py-1 text-[10px] font-bold {{ $badge($status) }}">{{ $label($status) }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex flex-wrap items-center justify-end gap-1.5">
                                        @if ($status === 'pending')
                                            <form method="POST" action="{{ route('admin.ppdb.verifikasi', $item) }}" class="inline-flex gap-1">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" name="status_pendaftaran" value="diterima"
                                                    class="rounded-lg bg-emerald-600 px-2.5 py-1.5 text-[10px] font-bold text-white transition hover:bg-emerald-700">
                                                    <i class="fa-solid fa-check mr-0.5"></i> Terima
                                                </button>
                                                <button type="submit" name="status_pendaftaran" value="ditolak"
                                                    class="rounded-lg bg-rose-50 px-2.5 py-1.5 text-[10px] font-bold text-rose-700 transition hover:bg-rose-100 dark:bg-rose-500/10 dark:text-rose-300">
                                                    <i class="fa-solid fa-xmark mr-0.5"></i> Tolak
                                                </button>
                                            </form>
                                        @elseif ($status === 'diterima')
                                            <a href="{{ route('admin.ppdb.plotting', ['q' => $item->nisn ?: $item->nama_lengkap]) }}"
                                                class="rounded-lg bg-sky-50 px-2.5 py-1.5 text-[10px] font-bold text-sky-700 transition hover:bg-sky-100 dark:bg-sky-500/10 dark:text-sky-300">
                                                <i class="fa-solid fa-layer-group mr-0.5"></i> Plotting
                                            </a>
                                        @else
                                            <span class="rounded-lg bg-slate-100 px-2.5 py-1.5 text-[10px] font-bold text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                                Selesai
                                            </span>
                                        @endif
                                        <a href="{{ route('admin.pendaftaran.edit', $item->id) }}"
                                            class="rounded-lg bg-amber-50 px-2.5 py-1.5 text-[10px] font-bold text-amber-700 transition hover:bg-amber-100 dark:bg-amber-500/10 dark:text-amber-300">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-xs italic text-slate-400">
                                    Tidak ada pendaftar yang sesuai filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($pendaftar, 'links'))
                <div class="border-t border-slate-100 p-4 dark:border-slate-800">
                    {{ $pendaftar->links() }}
                </div>
            @endif
        </div>

        <p class="text-[11px] leading-relaxed text-slate-400">
            <i class="fa-solid fa-circle-info mr-1"></i>
            Menerima pendaftar otomatis membuat akun Portal Wali Santri dengan
            <b>kata sandi acak unik</b> (ditampilkan sekali ke admin) serta data santri pada tabel
            <code>students</code>. Status awal santri adalah <b>calon</b> sampai diplot ke kelas.
        </p>
    </div>
@endsection
