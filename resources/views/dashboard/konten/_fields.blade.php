@php
    $formatFor = function (string $key): string {
        return match ($key) {
            'pembelajaran_kajian' => 'Judul | Keterangan | Kitab1, Kitab2',
            'pembelajaran_diniyah', 'pembelajaran_madrasah' => 'Kode | Nama | Deskripsi',
            default => 'Judul | Keterangan',
        };
    };
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach ($fields as $row)
        @php
            $value = (string) $row['value'];
            $isList = str_contains($row['label'], '|');
            $isLong = ! $isList && (str_contains($value, "\n") || mb_strlen($value) > 80);
            $lineItems = $isList
                ? array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $value)), fn ($v) => $v !== ''))
                : [];
            $format = $formatFor($row['key']);
        @endphp

        <div class="space-y-1.5 {{ $isList || $isLong ? 'md:col-span-2' : '' }}">
            <div class="flex items-center justify-between gap-2">
                <label class="text-xs font-bold text-slate-700 dark:text-slate-300 leading-snug">
                    {{ $row['label'] }}
                    @if (!$row['is_default'])
                        <span
                            class="ml-1 inline-flex items-center rounded-full bg-emerald-50 dark:bg-emerald-950/50 px-1.5 py-0.5 text-[9px] font-bold text-emerald-700 dark:text-emerald-300">tambahan</span>
                    @endif
                </label>

                <button type="submit" name="delete_key" value="{{ $row['key'] }}"
                    onclick="return confirm({{ $row['is_default'] ? "'Kembalikan konten ini ke nilai bawaan?'" : "'Hapus konten tambahan ini secara permanen?'" }})"
                    title="{{ $row['is_default'] ? 'Kembalikan ke nilai bawaan' : 'Hapus permanen' }}"
                    class="w-6 h-6 shrink-0 rounded-md inline-flex items-center justify-center text-[9px] transition
                        {{ $row['is_default']
                            ? 'text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/40'
                            : 'text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40' }}">
                    <i class="fa-solid {{ $row['is_default'] ? 'fa-rotate-left' : 'fa-trash' }}"></i>
                </button>
            </div>

            @if ($isList)
                <div x-data="{ items: @js($lineItems) }" class="space-y-2">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex items-center gap-2">
                            <span class="w-6 text-center text-[10px] font-bold text-slate-400 shrink-0"
                                x-text="index + 1"></span>
                            <input type="text" x-model="items[index]" placeholder="{{ $format }}"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-sm">
                            <button type="button" @click="items.splice(index, 1)" title="Hapus item"
                                class="w-8 h-8 shrink-0 rounded-lg bg-red-50 dark:bg-red-950/50 text-red-500 hover:bg-red-100 dark:hover:bg-red-900/50 inline-flex items-center justify-center transition">
                                <i class="fa-solid fa-trash text-[10px]"></i>
                            </button>
                        </div>
                    </template>

                    <div class="flex items-center justify-between gap-2">
                        <button type="button" @click="items.push('')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition">
                            <i class="fa-solid fa-plus text-[9px]"></i> Tambah item
                        </button>
                        <span class="text-[10px] text-slate-400"><span x-text="items.length"></span> item</span>
                    </div>

                    <input type="hidden" name="items[{{ $row['key'] }}]" value="{{ $value }}"
                        :value="items.join('\n')">

                    <p class="text-[10px] text-slate-400 dark:text-slate-500">Satu baris satu item. Format:
                        <code>{{ $format }}</code>. Ikon mengikuti urutan.</p>
                </div>
            @elseif ($isLong)
                <textarea name="items[{{ $row['key'] }}]" rows="3"
                    class="w-full text-xs leading-relaxed rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-sm transition">{{ $value }}</textarea>
            @else
                <input type="text" name="items[{{ $row['key'] }}]" value="{{ $value }}"
                    class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-sm transition">
            @endif

            @if ($row['key'] === 'sejarah_jenjang')
                <p class="text-[10px] text-slate-400 dark:text-slate-500">Pisahkan dengan koma, contoh:
                    <code>MDTA, MTs, MA</code>
                </p>
            @elseif ($row['key'] === 'pembelajaran_cta_url')
                <p class="text-[10px] text-slate-400 dark:text-slate-500">Contoh: <code>/pendaftaran</code> atau
                    <code>https://…</code>
                </p>
            @endif
        </div>
    @endforeach
</div>
