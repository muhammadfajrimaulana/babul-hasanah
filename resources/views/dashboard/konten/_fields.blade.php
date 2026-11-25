@php
    $listKeys = [
        'sejarah_pilar',
        'sejarah_budaya',
        'sejarah_motivasi',
        'visi_page_misi',
        'visi_page_nilai',
        'pembelajaran_jalur',
        'pembelajaran_kajian',
        'pembelajaran_diniyah',
        'pembelajaran_madrasah',
        'pembelajaran_metode',
    ];

    $partsFor = function (string $key): array {
        return match ($key) {
            'pembelajaran_kajian' => ['Nama bidang', 'Keterangan singkat', 'Daftar kitab (pisahkan koma)'],
            'pembelajaran_diniyah', 'pembelajaran_madrasah' => ['Kode', 'Nama lengkap', 'Deskripsi'],
            default => ['Judul', 'Keterangan'],
        };
    };
@endphp

<div class="space-y-5">
    @foreach ($fields as $row)
        @php
            $value = (string) old('items.' . $row['key'], $row['value']);
            $isList = in_array($row['key'], $listKeys, true) || str_ends_with($row['key'], '_items');
            $parts = $isList ? $partsFor($row['key']) : [];
            $lineItems = [];

            if ($isList) {
                foreach (preg_split('/\r\n|\r|\n/', $value) as $line) {
                    $line = trim($line);
                    if ($line === '') {
                        continue;
                    }
                    $cells = array_map('trim', explode('|', $line));
                    $cells = array_pad($cells, count($parts), '');
                    $lineItems[] = array_slice($cells, 0, count($parts));
                }
            }

            $isLong = ! $isList && (str_contains($value, "\n") || mb_strlen($value) > 80);
            $isUrl = str_ends_with($row['key'], '_url');
            $help = match (true) {
                $row['key'] === 'sejarah_jenjang' => 'Pisahkan dengan koma. Contoh: MDTA, MTs, MA',
                $isUrl => 'Tulis alamat tujuan, misalnya /pendaftaran',
                $isList => 'Klik "Tambah baris" bila perlu. Urutan item mengikuti tampilan di halaman.',
                default => null,
            };
        @endphp

        <div
            class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-900/30 p-4 space-y-2">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <label class="block text-sm font-semibold leading-snug text-slate-800 dark:text-slate-100">
                        {{ $row['label'] }}
                        @if (!$row['is_default'])
                            <span
                                class="ml-1 inline-flex items-center rounded-full bg-emerald-50 dark:bg-emerald-950/50 px-1.5 py-0.5 text-[9px] font-bold text-emerald-700 dark:text-emerald-300">tambahan</span>
                        @endif
                    </label>
                    @if ($help)
                        <p class="mt-0.5 text-[11px] leading-relaxed text-slate-500 dark:text-slate-400">{{ $help }}</p>
                    @endif
                </div>

                <button type="submit" name="delete_key" value="{{ $row['key'] }}"
                    onclick="return confirm({{ $row['is_default'] ? "'Kembalikan konten ini ke nilai bawaan?'" : "'Hapus konten tambahan ini secara permanen?'" }})"
                    title="{{ $row['is_default'] ? 'Kembalikan ke nilai bawaan' : 'Hapus permanen' }}"
                    class="w-7 h-7 shrink-0 rounded-lg inline-flex items-center justify-center text-[10px] transition
                        {{ $row['is_default'] ? 'text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/40' : 'text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40' }}">
                    <i class="fa-solid {{ $row['is_default'] ? 'fa-rotate-left' : 'fa-trash' }}"></i>
                </button>
            </div>

            @if ($isList)
                <div x-data="{
                    parts: @js($parts),
                    items: @js($lineItems),
                    add() { this.items.push(this.parts.map(() => '')); },
                    remove(i) { this.items.splice(i, 1); },
                    serialize() {
                        return this.items
                            .map(row => row.map(cell => (cell || '').trim()).join(' | '))
                            .filter(row => row.replace(/\s*\|\s*/g, '') !== '')
                            .join('\n');
                    }
                }" class="space-y-2">
                    <div x-show="items.length === 0"
                        class="rounded-lg border border-dashed border-slate-300 dark:border-slate-600 px-4 py-5 text-center text-[11px] text-slate-400">
                        Belum ada item. Klik tombol di bawah untuk menambah.
                    </div>

                    <template x-for="(item, index) in items" :key="index">
                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-3">
                            <div class="mb-2 flex items-center justify-between gap-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400"
                                    x-text="'Item ' + (index + 1)"></span>
                                <button type="button" @click="remove(index)" title="Hapus item"
                                    class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-[10px] font-semibold text-red-500 hover:bg-red-50 dark:hover:bg-red-950/40 transition">
                                    <i class="fa-solid fa-xmark"></i> Hapus
                                </button>
                            </div>

                            <div class="grid grid-cols-1 gap-2"
                                :class="parts.length > 2 ? 'sm:grid-cols-3' : 'sm:grid-cols-2'">
                                <template x-for="(part, pIndex) in parts" :key="pIndex">
                                    <label class="block">
                                        <span
                                            class="mb-1 block text-[10px] font-semibold text-slate-500 dark:text-slate-400"
                                            x-text="part"></span>
                                        <input type="text" x-model="items[index][pIndex]"
                                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-xs text-slate-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                    </label>
                                </template>
                            </div>
                        </div>
                    </template>

                    <button type="button" @click="add()"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 px-3 py-2 text-[11px] font-bold text-emerald-700 dark:text-emerald-300 transition hover:bg-emerald-100 dark:hover:bg-emerald-900/50">
                        <i class="fa-solid fa-plus text-[9px]"></i> Tambah baris
                    </button>

                    <input type="hidden" name="items[{{ $row['key'] }}]" value="{{ $value }}"
                        :value="serialize()">
                </div>
            @elseif ($isLong)
                <textarea name="items[{{ $row['key'] }}]" rows="4"
                    class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3.5 py-2.5 text-xs leading-relaxed text-slate-900 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">{{ $value }}</textarea>
            @else
                <input type="text" name="items[{{ $row['key'] }}]" value="{{ $value }}"
                    class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3.5 py-2.5 text-xs text-slate-900 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
            @endif
        </div>
    @endforeach
</div>
