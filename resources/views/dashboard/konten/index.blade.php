@extends('layouts.admin')

@section('title', 'Pengelolaan Konten')

@section('content')
    @php
        $pages = [
            'sejarah' => [
                'label' => 'Sejarah',
                'group' => 'Sejarah',
                'icon' => 'fa-landmark',
                'desc' => 'Kelola isi halaman /profil/sejarah.',
                'url' => url('/profil/sejarah'),
            ],
            'visi' => [
                'label' => 'Visi & Misi',
                'group' => 'Visi & Misi',
                'icon' => 'fa-bullseye',
                'desc' => 'Kelola isi halaman /profil/visi-misi.',
                'url' => url('/profil/visi-misi'),
            ],
            'pembelajaran' => [
                'label' => 'Pembelajaran',
                'group' => 'Pembelajaran',
                'icon' => 'fa-graduation-cap',
                'desc' => 'Kelola isi halaman /profil/pembelajaran.',
                'url' => url('/profil/pembelajaran'),
            ],
        ];

        $pageRows = collect($rows)->groupBy('group');

        $sectionIcons = [
            'Hero' => 'fa-panorama',
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
        ];
    @endphp

    <div class="max-w-7xl mx-auto pb-24 space-y-6"
        x-data="{
            tab: ['sejarah', 'visi', 'pembelajaran'].includes(localStorage.getItem('kontenTab')) ? localStorage.getItem('kontenTab') : 'sejarah',
            setTab(name) {
                this.tab = name;
                localStorage.setItem('kontenTab', name);
            }
        }">

        <!-- Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm transition-colors">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center text-xl shrink-0 shadow-inner">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Pengelolaan Konten</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola Sejarah, Visi &amp; Misi, dan
                        Pembelajaran dalam satu tempat.</p>
                </div>
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
        <form action="{{ route('admin.konten.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            @foreach ($pages as $pageKey => $page)
                @php
                    $groupRows = $pageRows[$page['group']] ?? collect();
                    $sections = $groupRows->groupBy('section');
                @endphp

                <div x-show="tab === '{{ $pageKey }}'" x-cloak class="space-y-6">
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
                                <p class="text-[10px] text-slate-500 dark:text-slate-400">{{ $page['desc'] }}</p>
                            </div>
                        </div>
                        <a href="{{ $page['url'] }}" target="_blank"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] font-bold hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                            Lihat halaman <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                        </a>
                    </div>

                    <!-- Foto Sejarah (hanya tab Sejarah) -->
                    @if ($pageKey === 'sejarah')
                        <div
                            class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm p-5 flex flex-col sm:flex-row sm:items-center gap-5">
                            <div class="flex items-center gap-3 shrink-0">
                                <div
                                    class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300 flex items-center justify-center">
                                    <i class="fa-solid fa-image"></i>
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white">
                                        Foto Sejarah</h3>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Tampil di halaman
                                        <code>/profil</code> dan <code>/profil/sejarah</code>.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 flex-1">
                                @if (!empty($foto))
                                    <img src="{{ asset('storage/' . $foto) }}" alt="Foto sejarah"
                                        class="h-14 w-20 object-cover rounded-lg border border-slate-200 dark:border-slate-700 shrink-0">
                                @endif
                                <input type="file" name="sejarah_foto" accept="image/*"
                                    class="w-full text-[10px] text-slate-500 dark:text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 dark:file:bg-emerald-950/60 file:text-emerald-700 dark:file:text-emerald-300 hover:file:bg-emerald-100 cursor-pointer">
                            </div>
                        </div>
                    @endif

                    <!-- Pengaturan Tata Letak -->
                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm overflow-hidden">
                        <div
                            class="p-5 border-b border-slate-100 dark:border-slate-700/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div class="flex items-center gap-2.5">
                                <div
                                    class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                                    <i class="fa-solid fa-arrows-up-down-left-right text-xs"></i>
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white">
                                        Tata Letak Section</h3>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Atur urutan dan
                                        tampil/sembunyikan section. Klik <strong>Simpan</strong> untuk menerapkan.</p>
                                </div>
                            </div>
                            <span
                                class="text-[10px] text-slate-400 dark:text-slate-500">{{ $sections->count() }} section</span>
                        </div>

                        <div class="p-4" x-data="{
                            sections: @js($layouts[$pageKey]),
                            moveUp(i) {
                                if (i > 0) this.sections.splice(i - 1, 0, this.sections.splice(i, 1)[0]);
                            },
                            moveDown(i) {
                                if (i < this.sections.length - 1) this.sections.splice(i + 1, 0, this.sections.splice(i, 1)[0]);
                            }
                        }">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                                <template x-for="(section, index) in sections" :key="section.id">
                                    <div class="flex items-center gap-2 rounded-xl border p-2.5 transition"
                                        :class="section.visible ?
                                            'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-600' :
                                            'bg-slate-100 dark:bg-slate-900/40 border-slate-200/60 dark:border-slate-700 opacity-60'">
                                        <span
                                            class="w-6 h-6 shrink-0 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300 text-[10px] font-extrabold flex items-center justify-center"
                                            x-text="index + 1"></span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[11px] font-bold text-slate-700 dark:text-slate-200 truncate"
                                                x-text="section.label"></p>
                                            <label class="inline-flex items-center gap-1 mt-0.5 cursor-pointer">
                                                <input type="checkbox" x-model="section.visible"
                                                    class="w-3 h-3 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                                <span class="text-[9px] font-semibold text-slate-500 dark:text-slate-400"
                                                    x-text="section.visible ? 'Tampil' : 'Disembunyikan'"></span>
                                            </label>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <button type="button" @click="moveUp(index)" :disabled="index === 0"
                                                class="w-6 h-6 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 disabled:opacity-30 disabled:cursor-not-allowed inline-flex items-center justify-center transition">
                                                <i class="fa-solid fa-arrow-up text-[9px]"></i>
                                            </button>
                                            <button type="button" @click="moveDown(index)"
                                                :disabled="index === sections.length - 1"
                                                class="w-6 h-6 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 disabled:opacity-30 disabled:cursor-not-allowed inline-flex items-center justify-center transition">
                                                <i class="fa-solid fa-arrow-down text-[9px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <input type="hidden" name="layout[{{ $pageKey }}]"
                                value="{{ json_encode($layouts[$pageKey]) }}" :value="JSON.stringify(sections)">
                        </div>
                    </div>

                    <!-- Section Konten -->
                    @foreach ($sections as $sectionName => $fields)
                        <details class="group bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm overflow-hidden"
                            {{ $loop->first ? 'open' : '' }}>
                            <summary
                                class="cursor-pointer list-none p-4 sm:p-5 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-700/20 transition">
                                <div class="flex items-center gap-2.5">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300 flex items-center justify-center">
                                        <i class="fa-solid {{ $sectionIcons[$sectionName] ?? 'fa-folder-open' }} text-xs"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white">
                                            {{ $sectionName }}</h3>
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400">{{ $fields->count() }}
                                            field</p>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform group-open:rotate-180"></i>
                            </summary>

                            <div class="p-5 border-t border-slate-100 dark:border-slate-700/60">
                                @include('dashboard.konten._fields', ['fields' => $fields])
                            </div>
                        </details>
                    @endforeach
                </div>
            @endforeach

            <!-- Save Bar -->
            <div
                class="sticky bottom-0 z-20 p-4 sm:p-5 bg-white/95 dark:bg-slate-800/95 backdrop-blur rounded-2xl border border-slate-200/60 dark:border-slate-700/60 shadow-lg flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <p class="text-[10px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info"></i>
                    Ikon <i class="fa-solid fa-rotate-left mx-0.5"></i> mengembalikan field ke nilai bawaan.
                </p>
                <button type="submit"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-lg shadow-emerald-900/10 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Semua Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection
