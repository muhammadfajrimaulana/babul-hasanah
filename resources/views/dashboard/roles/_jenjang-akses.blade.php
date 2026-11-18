@php
    $pengguna = auth()->user();
    $bolehNilai = $pengguna->bisa('akademik.ubah');
    $bolehPrestasi = $pengguna->bisa('prestasi.ubah');
    $statistik = $jenjangStats ?? \App\Support\RekapJenjang::ringkasan();
    $judul = $judul ?? 'Akses Cepat per Jenjang';
    $deskripsi = $deskripsi ?? 'Input nilai & prestasi serta lihat data santri dan alumni pada tiap jenjang.';
@endphp

<div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
        <div>
            <h2 class="text-sm font-black text-slate-900 dark:text-white">{{ $judul }}</h2>
            <p class="text-[10px] text-slate-400">{{ $deskripsi }}</p>
        </div>
        @unless ($bolehNilai)
            <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                <i class="fa-solid fa-eye mr-1"></i> Hanya baca
            </span>
        @endunless
    </div>

    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($statistik as $j)
            <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-4 dark:border-slate-800 dark:bg-slate-800/40">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex min-w-0 items-center gap-2">
                        <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg text-white"
                            style="background: {{ $j['warna'] }}">
                            <i class="fa-solid {{ $j['ikon'] }} text-[11px]"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-[11px] font-black text-slate-800 dark:text-slate-100">{{ $j['kode'] }}</p>
                            <p class="truncate text-[9px] text-slate-400" title="{{ $j['nama'] }}">{{ $j['nama'] }}</p>
                        </div>
                    </div>
                    <span class="flex-shrink-0 rounded-lg bg-white px-2 py-1 text-[9px] font-bold text-slate-500 dark:bg-slate-900 dark:text-slate-400">
                        {{ number_format($j['santri'], 0, ',', '.') }} santri
                    </span>
                </div>

                <p class="mt-2 text-[9px] font-semibold text-slate-400">
                    {{ number_format($j['pendaftar'], 0, ',', '.') }} pendaftar ·
                    {{ number_format($j['alumni'], 0, ',', '.') }} alumni
                </p>

                <div class="mt-3 grid grid-cols-2 gap-1.5">
                    <a href="{{ route('admin.dashboard.jenjang.nilai.form', ['jenjang' => $j['slug']]) }}"
                        class="flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-2 text-[10px] font-black text-emerald-800 transition hover:bg-emerald-100 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                        <i class="fa-solid {{ $bolehNilai ? 'fa-pen-to-square' : 'fa-book-open-reader' }}"></i>
                        {{ $bolehNilai ? 'Input Nilai' : 'Nilai' }}
                    </a>
                    <a href="{{ route('admin.dashboard.jenjang.prestasi.form', ['jenjang' => $j['slug']]) }}"
                        class="flex items-center gap-1.5 rounded-lg border border-amber-200 bg-amber-50 px-2 py-2 text-[10px] font-black text-amber-800 transition hover:bg-amber-100 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-200">
                        <i class="fa-solid {{ $bolehPrestasi ? 'fa-trophy' : 'fa-medal' }}"></i>
                        {{ $bolehPrestasi ? 'Input Prestasi' : 'Prestasi' }}
                    </a>
                    <a href="{{ route('admin.jenjang.show', $j['slug']) }}"
                        class="flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-2 py-2 text-[10px] font-bold text-slate-600 transition hover:border-emerald-200 hover:text-emerald-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                        <i class="fa-solid fa-people-group"></i> Data Santri
                    </a>
                    <a href="{{ route('admin.alumni.index', ['jenjang' => $j['slug']]) }}"
                        class="flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-2 py-2 text-[10px] font-bold text-slate-600 transition hover:border-emerald-200 hover:text-emerald-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                        <i class="fa-solid fa-user-graduate"></i> Data Alumni
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
