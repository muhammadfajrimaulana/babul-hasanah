@extends('layouts.admin')

@section('title', 'Pengelolaan Konten')

@section('content')
    @php
        $pages = [
            'sejarah' => [
                'label' => 'Sejarah',
                'group' => 'Sejarah',
                'icon' => 'fa-landmark',
                'desc' => 'Riwayat pendirian dan perkembangan pesantren (BAB I–III).',
                'url' => url('/profil/sejarah'),
            ],
            'visi' => [
                'label' => 'Visi & Misi',
                'group' => 'Visi & Misi',
                'icon' => 'fa-bullseye',
                'desc' => 'Visi, misi, nilai, dan harapan pesantren (BAB V).',
                'url' => url('/profil/visi-misi'),
            ],
            'pembelajaran' => [
                'label' => 'Pembelajaran',
                'group' => 'Pembelajaran',
                'icon' => 'fa-graduation-cap',
                'desc' => 'Jalur belajar, kajian, jenjang, dan peran pesantren (BAB IV).',
                'url' => url('/profil/pembelajaran'),
            ],
            'fasilitas' => [
                'label' => 'Fasilitas',
                'group' => 'Fasilitas',
                'icon' => 'fa-building',
                'desc' => 'Empat fasilitas utama beserta foto dan keterangannya.',
                'url' => url('/profil/fasilitas'),
            ],
        ];

        $rowMap = collect($rows)->keyBy('key');
        $pageRows = collect($rows)->groupBy('group');

        $fotoUrl = function (?string $path): ?string {
            $path = trim((string) $path);

            if ($path === '') {
                return null;
            }

            $relative = preg_replace('#^storage/#', '', $path);

            if (! is_file(public_path('storage/' . $relative))) {
                return null;
            }

            return asset('storage/' . $relative);
        };

        $sectionIcons = [
            'Hero' => 'fa-panorama',
            'BAB I' => 'fa-book-open',
            'BAB II' => 'fa-seedling',
            'BAB III' => 'fa-people-group',
            'BAB IV' => 'fa-hands-holding-circle',
            'BAB V' => 'fa-seedling',
            'Sejarah' => 'fa-book-open',
            'Budaya' => 'fa-people-group',
            'Kajian' => 'fa-book-open-reader',
            'Motivasi' => 'fa-quote-left',
            'Visi' => 'fa-eye',
            'Misi' => 'fa-list-check',
            'Nilai' => 'fa-gem',
            'Motto' => 'fa-star-and-crescent',
            'Jalur Belajar' => 'fa-route',
            'Jenjang' => 'fa-graduation-cap',
            'Metode' => 'fa-chalkboard-user',
            'CTA' => 'fa-bullhorn',
            'Fasilitas' => 'fa-building',
        ];

        $sectionNotes = [
            'Hero' => 'Bagian paling atas halaman: judul dan deskripsi pembuka.',
            'BAB I' => 'Latar belakang, penggagas, serta alasan dan tujuan pendirian.',
            'BAB II' => 'Perintisan tahun 1997, dukungan masyarakat, dan makna nama.',
            'BAB III' => 'Kepemimpinan, pengembangan pendidikan, dan kegiatan santri.',
            'BAB IV' => 'Peran pesantren dalam pendidikan, akhlak, dan masyarakat.',
            'BAB V' => 'Visi, harapan, dan keberlanjutan pesantren.',
            'Sejarah' => 'Cerita berdirinya pesantren, pilar, dan arti nama.',
            'Budaya' => 'Kegiatan dan budaya keseharian santri.',
            'Kajian' => 'Kitab dan jadwal kajian yang dipelajari.',
            'Motivasi' => 'Kutipan penyemangat di bagian bawah halaman.',
            'Visi' => 'Pernyataan visi pesantren.',
            'Misi' => 'Langkah-langkah untuk mewujudkan visi.',
            'Nilai' => 'Nilai utama yang ditanamkan kepada santri.',
            'Motto' => 'Semboyan singkat pesantren.',
            'Jalur Belajar' => 'Penjelasan sistem pembelajaran pesantren.',
            'Jenjang' => 'Daftar jenjang dan program pendidikan.',
            'Metode' => 'Cara santri belajar di pesantren.',
            'CTA' => 'Ajakan dan tombol menuju pendaftaran.',
            'Fasilitas' => 'Judul, deskripsi, dan empat fasilitas utama.',
            'Konten Tambahan' => 'Konten khusus yang ditambahkan admin.',
        ];
    @endphp

    <div class="max-w-6xl mx-auto pb-24 space-y-5"
        x-data="{
            tab: ['sejarah', 'visi', 'pembelajaran', 'fasilitas'].includes(localStorage.getItem('kontenTab')) ? localStorage.getItem('kontenTab') : 'sejarah',
            setTab(name) {
                this.tab = name;
                localStorage.setItem('kontenTab', name);
            }
        }">

        <!-- Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm transition-colors">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center text-xl shrink-0 shadow-inner">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Pengelolaan Konten</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pilih halaman, ubah teks atau foto, lalu
                        klik Simpan.</p>
                </div>
            </div>

            <div
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 px-3.5 py-2 text-[11px] font-semibold text-emerald-700 dark:text-emerald-300">
                <i class="fa-solid fa-lightbulb"></i>
                Tidak perlu mengetik kode.
            </div>
        </div>

        @if (session('success'))
            <div
                class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 rounded-2xl text-xs font-semibold flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div
                class="p-4 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-2xl text-xs shadow-sm">
                <div class="flex items-center gap-2 font-bold mb-1.5">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Ada yang perlu diperbaiki:</span>
                </div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Tab Navigasi -->
        <div class="flex flex-wrap items-center gap-2">
            @foreach ($pages as $pageKey => $page)
                @php $count = ($pageRows[$page['group']] ?? collect())->count(); @endphp
                <button type="button" @click="setTab('{{ $pageKey }}')"
                    :class="tab === '{{ $pageKey }}' ?
                        'bg-emerald-600 text-white border-emerald-600 shadow-md shadow-emerald-900/10' :
                        'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700'"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border text-xs font-bold transition">
                    <i class="fa-solid {{ $page['icon'] }}"></i>
                    {{ $page['label'] }}
                    <span
                        class="inline-flex items-center rounded-full px-1.5 py-0.5 text-[9px] bg-black/10 dark:bg-white/10">{{ $count }}</span>
                </button>
            @endforeach
        </div>

        <!-- Form Utama -->
        <form action="{{ route('admin.konten.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            @foreach ($pages as $pageKey => $page)
                @php
                    $groupRows = $pageRows[$page['group']] ?? collect();
                    $sections = $groupRows->groupBy('section');
                @endphp

                <div x-show="tab === '{{ $pageKey }}'" x-cloak class="space-y-5">
                    <!-- Panel Header -->
                    <div
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                <i class="fa-solid {{ $page['icon'] }}"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Halaman
                                    {{ $page['label'] }}</h2>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ $page['desc'] }}</p>
                            </div>
                        </div>
                        <a href="{{ $page['url'] }}" target="_blank"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] font-bold hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                            Lihat halaman <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                        </a>
                    </div>

                    {{-- ============ FOTO SEJARAH ============ --}}
                    @if ($pageKey === 'sejarah')
                        @php $fotoPreview = $fotoUrl($foto); @endphp
                        <div x-data="{ preview: null }"
                            class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm p-5 space-y-4">
                            <div class="flex flex-col sm:flex-row gap-5">
                                <div
                                    class="relative w-full sm:w-44 h-32 shrink-0 overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-900">
                                    @if ($fotoPreview)
                                        <img x-show="!preview" src="{{ $fotoPreview }}" alt="Foto sejarah"
                                            class="h-full w-full object-cover">
                                    @else
                                        <div x-show="!preview"
                                            class="flex h-full w-full flex-col items-center justify-center gap-1.5 text-slate-400">
                                            <i class="fa-solid fa-image text-2xl"></i>
                                            <span class="text-[10px] font-semibold">Belum ada foto</span>
                                        </div>
                                    @endif
                                    <template x-if="preview">
                                        <img :src="preview" alt="Pratinjau foto" class="h-full w-full object-cover">
                                    </template>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Foto halaman sejarah</h3>
                                    <p class="mt-1 text-[11px] leading-relaxed text-slate-500 dark:text-slate-400">
                                        Foto akan tampil di tengah halaman, dengan keterangan di bawahnya. Gunakan foto
                                        landscape (mendatar) agar hasilnya rapi.
                                    </p>

                                    <div class="mt-3 flex flex-wrap items-center gap-3">
                                        <label for="sejarah_foto"
                                            class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                                            <i class="fa-solid fa-cloud-arrow-up"></i> Pilih foto
                                        </label>
                                        <input id="sejarah_foto" type="file" name="sejarah_foto" accept="image/*"
                                            class="sr-only"
                                            @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                                        <span class="text-[10px] text-slate-400">JPG, PNG, atau WebP. Maksimal 2 MB.</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="sejarah_foto_caption"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 dark:text-slate-300">Keterangan
                                    foto</label>
                                <input id="sejarah_foto_caption" type="text" name="items[sejarah_foto_caption]"
                                    value="{{ old('items.sejarah_foto_caption', $caption) }}"
                                    placeholder="Contoh: Komplek Pondok Pesantren Babul Hasanah"
                                    class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3.5 py-2.5 text-xs text-slate-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                <p class="mt-1 text-[10px] text-slate-400">Teks ini muncul tepat di bawah foto.</p>
                            </div>
                        </div>
                    @endif

                    {{-- ============ FASILITAS ============ --}}
                    @if ($pageKey === 'fasilitas')
                        @php
                            $fasilitasHeader = collect([
                                $rowMap['fasilitas_heading'] ?? null,
                                $rowMap['fasilitas_desc'] ?? null,
                            ])->filter();
                        @endphp

                        <div
                            class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm p-5">
                            <h3 class="mb-3 text-sm font-bold text-slate-900 dark:text-white">Judul dan deskripsi halaman
                            </h3>
                            @include('dashboard.konten._fields', ['fields' => $fasilitasHeader])
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @for ($i = 1; $i <= 4; $i++)
                                @php
                                    $facilityPhoto = $fotoUrl($fasilitasFoto[$i] ?? null);
                                    $titleRow = $rowMap["fasilitas_{$i}_title"] ?? null;
                                    $descRow = $rowMap["fasilitas_{$i}_desc"] ?? null;
                                @endphp

                                <div x-data="{ preview: null }"
                                    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm overflow-hidden">
                                    <div class="relative h-40 bg-slate-100 dark:bg-slate-900">
                                        @if ($facilityPhoto)
                                            <img x-show="!preview" src="{{ $facilityPhoto }}"
                                                alt="Fasilitas {{ $i }}" class="h-full w-full object-cover">
                                        @else
                                            <div x-show="!preview"
                                                class="flex h-full w-full flex-col items-center justify-center gap-1.5 text-slate-400">
                                                <i class="fa-solid fa-image text-2xl"></i>
                                                <span class="text-[10px] font-semibold">Foto belum ada</span>
                                            </div>
                                        @endif
                                        <template x-if="preview">
                                            <img :src="preview" alt="Pratinjau fasilitas"
                                                class="h-full w-full object-cover">
                                        </template>

                                        <span
                                            class="absolute left-3 top-3 inline-flex items-center rounded-full bg-black/60 px-2.5 py-1 text-[10px] font-bold text-white">Fasilitas
                                            {{ $i }}</span>
                                    </div>

                                    <div class="p-4 space-y-3">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <label for="fasilitas_foto_{{ $i }}"
                                                class="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 px-3 py-2 text-[11px] font-bold text-emerald-700 dark:text-emerald-300 transition hover:bg-emerald-100 dark:hover:bg-emerald-900/50">
                                                <i class="fa-solid fa-cloud-arrow-up"></i> Pilih foto
                                            </label>
                                            <input id="fasilitas_foto_{{ $i }}" type="file"
                                                name="fasilitas_{{ $i }}_foto" accept="image/*" class="sr-only"
                                                @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                                            <span class="text-[10px] text-slate-400">Maks. 2 MB</span>
                                        </div>

                                        <div>
                                            <label
                                                class="mb-1 block text-[11px] font-semibold text-slate-500 dark:text-slate-400">Nama
                                                fasilitas</label>
                                            <input type="text" name="items[fasilitas_{{ $i }}_title]"
                                                value="{{ old('items.fasilitas_'.$i.'_title', $titleRow['value'] ?? '') }}"
                                                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-xs text-slate-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                        </div>

                                        <div>
                                            <label
                                                class="mb-1 block text-[11px] font-semibold text-slate-500 dark:text-slate-400">Keterangan
                                                fasilitas</label>
                                            <textarea name="items[fasilitas_{{ $i }}_desc]" rows="3"
                                                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-xs leading-relaxed text-slate-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">{{ old('items.fasilitas_'.$i.'_desc', $descRow['value'] ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    @endif

                    {{-- ============ SECTION KONTEN (selain fasilitas) ============ --}}
                    @if ($pageKey !== 'fasilitas')
                        @foreach ($sections as $sectionName => $fields)
                            <details
                                class="group bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm overflow-hidden"
                                {{ $loop->first ? 'open' : '' }}>
                                <summary
                                    class="cursor-pointer list-none p-4 sm:p-5 flex items-center justify-between gap-3 hover:bg-slate-50 dark:hover:bg-slate-700/20 transition">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div
                                            class="w-9 h-9 shrink-0 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300 flex items-center justify-center">
                                            <i
                                                class="fa-solid {{ $sectionIcons[$sectionName] ?? 'fa-folder-open' }} text-xs"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $sectionName }}
                                            </h3>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                                {{ $sectionNotes[$sectionName] ?? 'Konten halaman ini.' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex shrink-0 items-center gap-3">
                                        <span
                                            class="hidden sm:inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-700 px-2 py-0.5 text-[10px] font-semibold text-slate-500 dark:text-slate-300">{{ $fields->count() }}
                                            kolom</span>
                                        <i
                                            class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform group-open:rotate-180"></i>
                                    </div>
                                </summary>

                                <div class="p-4 sm:p-5 border-t border-slate-100 dark:border-slate-700/60">
                                    @include('dashboard.konten._fields', ['fields' => $fields])
                                </div>
                            </details>
                        @endforeach
                    @endif
                </div>
            @endforeach

            <!-- Save Bar -->
            <div
                class="sticky bottom-0 z-20 p-4 sm:p-5 bg-white/95 dark:bg-slate-800/95 backdrop-blur rounded-2xl border border-slate-200/60 dark:border-slate-700/60 shadow-lg flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <p class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info"></i>
                    Perubahan di semua tab akan disimpan sekaligus.
                </p>
                <button type="submit"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-lg shadow-emerald-900/10 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan perubahan
                </button>
            </div>
        </form>
    </div>
@endsection
