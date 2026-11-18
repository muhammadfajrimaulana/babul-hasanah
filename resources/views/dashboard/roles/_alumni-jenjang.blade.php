{{--
    Tabel Data Alumni untuk satu jenjang (atau gabungan seluruh jenjang).

    Parameter: $alumniTerbaru, $totalAlumni, $alumniPunyaNilai,
    $alumniBelumNilai, $nilaiSiap, $bolehAlumni, $jenjang, $jenjangPilihan.
--}}
@php
    $jenjang = $jenjang ?? null;
    $jenjangPilihan = $jenjangPilihan ?? null;
    $totalAlumni = $totalAlumni ?? 0;
    $alumniPunyaNilai = $alumniPunyaNilai ?? 0;
    $alumniBelumNilai = $alumniBelumNilai ?? max(0, $totalAlumni - $alumniPunyaNilai);
    $alumniTerbaru = $alumniTerbaru ?? collect();
    $nilaiSiap = $nilaiSiap ?? \App\Models\Alumni::siapNilai();
    $bolehAlumni = $bolehAlumni ?? auth()->user()->bisa('alumni.ubah');
@endphp

<div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-800">
        <div>
            <h2 class="text-sm font-black text-slate-900 dark:text-white">Data Alumni</h2>
            <p class="text-[10px] text-slate-400">
                {{ $jenjang ? 'Lulusan jenjang '.$jenjang['kode'] : 'Lulusan seluruh jenjang' }} ·
                {{ number_format($totalAlumni, 0, ',', '.') }} alumni
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('alumni.index') }}" target="_blank" rel="noopener"
                class="text-[10px] font-bold text-slate-400 hover:underline">Lihat publik</a>
            <a href="{{ route('admin.alumni.index', $jenjangPilihan ? ['jenjang' => $jenjangPilihan] : []) }}"
                class="text-[10px] font-bold text-emerald-600 hover:underline">Kelola alumni</a>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-2 px-5 pt-4">
        <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2.5 text-center dark:border-slate-800 dark:bg-slate-800/40">
            <p class="text-base font-black text-slate-900 dark:text-white">{{ number_format($totalAlumni, 0, ',', '.') }}</p>
            <p class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Total alumni</p>
        </div>
        <div class="rounded-xl border border-emerald-100 bg-emerald-50/60 px-3 py-2.5 text-center dark:border-emerald-500/20 dark:bg-emerald-500/10">
            <p class="text-base font-black text-emerald-700 dark:text-emerald-300">{{ number_format($alumniPunyaNilai, 0, ',', '.') }}</p>
            <p class="text-[9px] font-bold uppercase tracking-wide text-emerald-600/80 dark:text-emerald-400/80">Punya nilai</p>
        </div>
        <div class="rounded-xl border border-amber-100 bg-amber-50/60 px-3 py-2.5 text-center dark:border-amber-500/20 dark:bg-amber-500/10">
            <p class="text-base font-black text-amber-700 dark:text-amber-300">{{ number_format($alumniBelumNilai, 0, ',', '.') }}</p>
            <p class="text-[9px] font-bold uppercase tracking-wide text-amber-600/80 dark:text-amber-400/80">Belum ada nilai</p>
        </div>
    </div>

    <div class="overflow-x-auto px-1 pb-1 pt-3">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-400 dark:bg-slate-800/50">
                    <th class="px-4 py-3 font-extrabold">Nama</th>
                    <th class="px-4 py-3 font-extrabold">Jenjang</th>
                    <th class="px-4 py-3 font-extrabold">Lulus</th>
                    <th class="px-4 py-3 font-extrabold">Kegiatan</th>
                    <th class="px-4 py-3 font-extrabold text-center">Status</th>
                    <th class="px-4 py-3 font-extrabold text-center">Nilai</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($alumniTerbaru as $a)
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.alumni.show', $a) }}"
                                class="font-bold text-slate-700 hover:text-emerald-600 dark:text-slate-200">
                                {{ $a->nama }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ \App\Support\Jenjang::label($a->jenjang) ?: '-' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $a->tahun_lulus ?: '-' }}</td>
                        <td class="px-4 py-3">
                            @if (filled($a->kegiatan))
                                <span class="rounded-lg bg-slate-100 px-2 py-1 text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                    {{ $a->kegiatan }}
                                </span>
                            @else
                                <span class="text-[10px] text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @include('dashboard.alumni._status', ['alumni' => $a])
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if ($nilaiSiap && $a->punyaNilai())
                                <a href="{{ route('admin.alumni.show', $a) }}"
                                    class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2 py-1 text-[10px] font-bold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                                    <i class="fa-solid fa-file-pdf"></i> Ada
                                </a>
                            @elseif ($nilaiSiap && $bolehAlumni)
                                <a href="{{ route('admin.alumni.show', $a) }}"
                                    class="inline-flex items-center gap-1 rounded-lg bg-amber-50 px-2 py-1 text-[10px] font-bold text-amber-700 hover:bg-amber-100 dark:bg-amber-500/10 dark:text-amber-300">
                                    <i class="fa-solid fa-upload"></i> Unggah
                                </a>
                            @else
                                <span class="text-[10px] text-slate-400">Belum</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-7 text-center text-[11px] text-slate-400">
                            Belum ada data alumni{{ $jenjang ? ' pada jenjang ini' : '' }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
