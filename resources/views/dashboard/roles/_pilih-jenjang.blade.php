@php
    $penggunaJenjang = auth()->user();
    $batasiJenjang = \App\Support\Akses::dibatasiJenjang($penggunaJenjang);
    $pilihanJenjang = $jenjangPilihan ?? null;
@endphp

@unless ($batasiJenjang)
    <form method="GET"
        class="flex flex-col gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-start gap-3">
            <span
                class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                <i class="fa-solid fa-layer-group"></i>
            </span>
            <div>
                <p class="text-[11px] font-black uppercase tracking-wider text-slate-400">Pilih Jenjang</p>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    Menampilkan satu jenjang, atau gabungan seluruh jenjang bila tidak dipilih.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <select name="jenjang" onchange="this.form.submit()"
                class="min-w-[220px] rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                <option value="">Semua jenjang (gabungan)</option>
                @foreach ($daftarJenjang ?? \App\Support\Jenjang::semua() as $j)
                    <option value="{{ $j['slug'] }}" @selected($pilihanJenjang === $j['slug'])>
                        {{ $j['kode'] }} — {{ $j['nama'] }}
                    </option>
                @endforeach
            </select>
            <noscript>
                <button type="submit"
                    class="rounded-xl bg-emerald-600 px-3.5 py-2.5 text-xs font-black text-white">Terapkan</button>
            </noscript>
        </div>
    </form>
@endunless
