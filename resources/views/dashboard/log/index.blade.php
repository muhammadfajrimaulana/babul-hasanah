@extends('layouts.admin')

@section('title', 'Log Aktivitas - Pondok Pesantren Babul Hasanah')

@section('content')
    @php
        $in = 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500';
    @endphp

    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white">Log Aktivitas</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Jejak perubahan data penting: nilai, kenaikan kelas, izin, pengumuman, reset password, dan lainnya. Hanya Superadmin.
            </p>
        </div>

        <form method="GET" action="{{ route('admin.log.index') }}" class="grid grid-cols-1 gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:grid-cols-2 lg:grid-cols-5">
            <div>
                <label class="mb-1.5 block text-[11px] font-bold text-slate-600 dark:text-slate-300">Aksi</label>
                <input type="text" name="aksi" value="{{ $filter['aksi'] ?? '' }}" placeholder="mis. rollback" class="{{ $in }}">
            </div>
            <div>
                <label class="mb-1.5 block text-[11px] font-bold text-slate-600 dark:text-slate-300">Pengguna</label>
                <select name="user" class="{{ $in }}">
                    <option value="">Semua pengguna</option>
                    @foreach ($pengguna as $p)
                        <option value="{{ $p->id }}" @selected((string) ($filter['user'] ?? '') === (string) $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-[11px] font-bold text-slate-600 dark:text-slate-300">Dari tanggal</label>
                <input type="date" name="dari" value="{{ $filter['dari'] ?? '' }}" class="{{ $in }}">
            </div>
            <div>
                <label class="mb-1.5 block text-[11px] font-bold text-slate-600 dark:text-slate-300">Sampai tanggal</label>
                <input type="date" name="sampai" value="{{ $filter['sampai'] ?? '' }}" class="{{ $in }}">
            </div>
            <div class="flex items-end gap-2">
                <button class="flex-1 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-black text-white hover:bg-emerald-700">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('admin.log.index') }}" class="rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300">
                    <i class="fa-solid fa-rotate"></i>
                </a>
            </div>
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-400 dark:bg-slate-800/50">
                            <th class="px-4 py-3 font-extrabold">Waktu</th>
                            <th class="px-4 py-3 font-extrabold">Pengguna</th>
                            <th class="px-4 py-3 font-extrabold">Aksi</th>
                            <th class="px-4 py-3 font-extrabold">Objek</th>
                            <th class="px-4 py-3 font-extrabold">Data</th>
                            <th class="px-4 py-3 font-extrabold">Alasan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($log as $item)
                            <tr class="align-top hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                <td class="whitespace-nowrap px-4 py-3 text-[11px] font-semibold text-slate-500">
                                    {{ $item->created_at?->translatedFormat('d M Y') }}<br>
                                    <span class="text-[10px] text-slate-400">{{ $item->created_at?->format('H:i:s') }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-bold text-slate-700 dark:text-slate-200">{{ $item->user?->name ?? 'Sistem' }}</p>
                                    <p class="text-[10px] text-slate-400">{{ \App\Support\Akses::label($item->user?->role) }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-lg bg-emerald-50 px-2 py-0.5 font-mono text-[10px] font-black text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">{{ $item->aksi }}</span>
                                </td>
                                <td class="px-4 py-3 text-[11px] text-slate-500">
                                    @if ($item->model_type)
                                        {{ class_basename($item->model_type) }}<span class="text-slate-400">#{{ $item->model_id }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if (! empty($item->data))
                                        <pre class="max-w-xs whitespace-pre-wrap break-words rounded-lg bg-slate-50 p-2 text-[10px] leading-snug text-slate-600 dark:bg-slate-800/60 dark:text-slate-300">{{ json_encode($item->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="max-w-xs px-4 py-3 text-[11px] text-slate-500">{{ $item->alasan ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-12 text-center text-[11px] text-slate-400">Belum ada log aktivitas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $log->links() }}</div>
    </div>
@endsection
