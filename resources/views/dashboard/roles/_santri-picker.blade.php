@php
    $daftarSantri = $santriList
        ->map(fn ($s) => [
            'id' => (string) $s->id,
            'nama' => $s->nama_lengkap,
            'kelas' => $s->kelas ?: '-',
            'induk' => $s->nomor_induk ?: '',
        ])
        ->values();

    $daftarKelas = $santriList
        ->pluck('kelas')
        ->filter(fn ($k) => filled($k))
        ->unique()
        ->sort()
        ->values();

    $terpilih = (string) old('santri_id', '');

    $hint = $hint ?? 'Data jenjang dan kelas mengikuti santri yang dipilih.';
@endphp

<div data-santri-picker>
    <label for="{{ $id }}" class="{{ $labelCls }}">Santri <span class="text-rose-500">*</span></label>

    {{-- Pencarian nama/NIS + filter kelas --}}
    <div class="mb-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[10px] text-slate-400"></i>
            <input type="search" data-santri-search placeholder="Cari nama atau NIS santri..."
                autocomplete="off" class="{{ $inputCls }} pl-9">
        </div>
        <div class="relative">
            <i class="fa-solid fa-users-rectangle pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[10px] text-slate-400"></i>
            <select data-santri-kelas class="{{ $inputCls }} pl-9" aria-label="Saring berdasarkan kelas">
                <option value="">Semua kelas</option>
                @foreach ($daftarKelas as $kelas)
                    <option value="{{ $kelas }}">Kelas {{ $kelas }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <select id="{{ $id }}" name="santri_id" required class="{{ $inputCls }}" data-santri-select>
        <option value="" disabled @selected($terpilih === '')>Pilih santri</option>
        @foreach ($santriList as $s)
            <option value="{{ $s->id }}" @selected($terpilih === (string) $s->id)>
                {{ $s->nama_lengkap }} — Kelas {{ $s->kelas ?: '-' }} ({{ $s->nomor_induk ?: 'tanpa induk' }})
            </option>
        @endforeach
    </select>

    <div class="mt-1.5 flex flex-wrap items-center justify-between gap-2">
        <p class="text-[10px] text-slate-400">{{ $hint }}</p>
        <p class="text-[10px] font-bold text-slate-400" data-santri-info>{{ $santriList->count() }} santri</p>
    </div>

    {{-- Sumber data untuk pencarian di sisi klien --}}
    <script type="application/json" data-santri-data>{!! json_encode($daftarSantri, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!}</script>
</div>
