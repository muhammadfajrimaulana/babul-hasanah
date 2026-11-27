@extends('layouts.admin')

@section('title', 'Pengelolaan Konten')

@section('content')
    @php
        $pages = [
            'sejarah' => [
                'label' => 'Sejarah',
                'group' => 'Sejarah',
                'icon' => 'fa-landmark',
                'desc' => 'Riwayat pendirian, perkembangan (BAB I–III), dan info pendaftaran.',
                'url' => route('profil.sejarah'),
            ],
            'visi' => [
                'label' => 'Visi & Misi',
                'group' => 'Visi & Misi',
                'icon' => 'fa-bullseye',
                'desc' => 'Visi, misi, nilai, dan harapan pesantren (BAB V).',
                'url' => route('profil.visi-misi'),
            ],
            'pembelajaran' => [
                'label' => 'Pembelajaran',
                'group' => 'Pembelajaran',
                'icon' => 'fa-graduation-cap',
                'desc' => 'Jalur belajar, kajian, jenjang, dan metode pembelajaran.',
                'url' => route('profil.pembelajaran'),
            ],
            'fasilitas' => [
                'label' => 'Fasilitas',
                'group' => 'Fasilitas',
                'icon' => 'fa-building',
                'desc' => 'Judul halaman dan kartu fasilitas (bisa ditambah atau dihapus).',
                'url' => route('profil.fasilitas'),
            ],
        ];

        $rowMap = collect($rows)->keyBy('key');
        $pageRows = collect($rows)->groupBy('group');

        // Field bertipe daftar (punya tombol "Tambah baris" + tombol Tambah di bagian).
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
            'Daftar Isi' => 'fa-list-ul',
            'BAB I' => 'fa-book-open',
            'BAB II' => 'fa-seedling',
            'BAB III' => 'fa-people-group',
            'BAB V' => 'fa-seedling',
            'Pendaftaran' => 'fa-bullhorn',
            'Kajian' => 'fa-book-open-reader',
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
            'Hero' => 'Bagian paling atas halaman: label, judul, deskripsi, dan info tahun.',
            'Daftar Isi' => 'Kalimat pengantar hijau yang tampil tepat di atas panel Daftar Isi.',
            'BAB I' => 'Latar belakang, penggagas, serta alasan dan tujuan pendirian.',
            'BAB II' => 'Perintisan tahun 1997, dukungan masyarakat, dan makna nama.',
            'BAB III' => 'Kepemimpinan, pengembangan pendidikan, dan kegiatan santri.',
            'BAB V' => 'Visi, harapan, dan keberlanjutan pesantren.',
            'Pendaftaran' => 'Kartu hijau di bagian bawah halaman sejarah beserta tombolnya.',
            'Kajian' => 'Bidang kajian, penjelasan, dan daftar kitab yang dipelajari.',
            'Visi' => 'Judul, deskripsi, dan pernyataan visi pesantren.',
            'Misi' => 'Langkah-langkah untuk mewujudkan visi.',
            'Nilai' => 'Nilai utama yang ditanamkan kepada santri.',
            'Motto' => 'Semboyan singkat pesantren di bagian bawah halaman.',
            'Jalur Belajar' => 'Penjelasan sistem pembelajaran pesantren.',
            'Jenjang' => 'Daftar jenjang dan program pendidikan (Diniyah & Madrasah).',
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
        <form id="konten-form" action="{{ route('admin.konten.update') }}" method="POST" enctype="multipart/form-data"
            class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Dipakai tombol "Hapus" per bagian dan tombol "Hapus foto". --}}
            <input type="hidden" name="delete_keys" id="kb-delete-keys" value="">
            <input type="hidden" name="delete_foto" id="kb-delete-foto" value="">
            <input type="hidden" name="empty_keys" id="kb-empty-keys" value="">

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
                        <div x-data="{ preview: null }" data-kb-box
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

                            {{-- Aksi bagian: Edit / Tambah / Hapus --}}
                            <div class="flex flex-wrap items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-700/60 pt-3">
                                <button type="button" onclick="kb.edit(event, this)" title="Ubah keterangan foto"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 px-2.5 py-1.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-300 transition hover:bg-emerald-100 dark:hover:bg-emerald-900/50">
                                    <i class="fa-solid fa-pen text-[9px]"></i> Edit
                                </button>
                                <button type="button" onclick="kb.pilihFoto(event, 'sejarah_foto')"
                                    title="Tambah atau ganti foto"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 px-2.5 py-1.5 text-[10px] font-bold text-slate-600 dark:text-slate-300 transition hover:bg-slate-200 dark:hover:bg-slate-600">
                                    <i class="fa-solid fa-plus text-[9px]"></i> Tambah
                                </button>
                                <button type="button" onclick="kb.hapusFoto(event, 'sejarah_foto')" title="Hapus foto ini"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 dark:bg-red-950/40 px-2.5 py-1.5 text-[10px] font-bold text-red-600 dark:text-red-300 transition hover:bg-red-100 dark:hover:bg-red-900/40">
                                    <i class="fa-solid fa-trash text-[9px]"></i> Hapus
                                </button>
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

                        <div data-kb-box
                            class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm p-5">
                            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Judul dan deskripsi halaman
                                </h3>

                                {{-- Aksi bagian: Edit / Tambah / Hapus --}}
                                <div class="flex flex-wrap items-center gap-2">
                                    <button type="button" onclick="kb.edit(event, this)" title="Ubah judul dan deskripsi"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 px-2.5 py-1.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-300 transition hover:bg-emerald-100 dark:hover:bg-emerald-900/50">
                                        <i class="fa-solid fa-pen text-[9px]"></i> Edit
                                    </button>
                                    <button type="button" onclick="kb.add(event, this)" data-add-type="line"
                                        data-add-target="items[fasilitas_desc]" title="Tambah paragraf baru"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 px-2.5 py-1.5 text-[10px] font-bold text-slate-600 dark:text-slate-300 transition hover:bg-slate-200 dark:hover:bg-slate-600">
                                        <i class="fa-solid fa-plus text-[9px]"></i> Tambah
                                    </button>
                                    <button type="button" onclick="kb.reset(event, this)"
                                        data-keys="fasilitas_heading,fasilitas_desc" data-label="Judul dan deskripsi"
                                        title="Kembalikan ke nilai bawaan"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 dark:bg-red-950/40 px-2.5 py-1.5 text-[10px] font-bold text-red-600 dark:text-red-300 transition hover:bg-red-100 dark:hover:bg-red-900/40">
                                        <i class="fa-solid fa-trash text-[9px]"></i> Hapus
                                    </button>
                                </div>
                            </div>
                            @include('dashboard.konten._fields', ['fields' => $fasilitasHeader, 'listKeys' => $listKeys])
                        </div>

                        <div id="kb-facility-grid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($fasilitasCards as $card)
                                @php
                                    $i = $card['index'];
                                    $facilityPhoto = $fotoUrl($card['foto']);
                                @endphp

                                <div x-data="{ preview: null }" data-kb-box data-kb-fac-card
                                    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm overflow-hidden">
                                    <div class="relative h-40 bg-slate-100 dark:bg-slate-900">
                                        @if ($facilityPhoto)
                                            <img x-show="!preview" src="{{ $facilityPhoto }}"
                                                alt="Fasilitas {{ $loop->iteration }}" class="h-full w-full object-cover">
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
                                            {{ $loop->iteration }}</span>

                                        @if ($facilityPhoto)
                                            <button type="button" onclick="kb.hapusFoto(event, 'fasilitas_{{ $i }}_foto')"
                                                title="Hapus foto fasilitas ini"
                                                class="absolute right-3 bottom-3 inline-flex items-center gap-1.5 rounded-lg bg-black/60 px-2.5 py-1.5 text-[10px] font-bold text-white transition hover:bg-red-600">
                                                <i class="fa-solid fa-trash text-[9px]"></i> Hapus foto
                                            </button>
                                        @endif
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
                                                value="{{ old('items.fasilitas_'.$i.'_title', $card['title']) }}"
                                                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-xs text-slate-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                        </div>

                                        <div>
                                            <label
                                                class="mb-1 block text-[11px] font-semibold text-slate-500 dark:text-slate-400">Keterangan
                                                fasilitas</label>
                                            <textarea name="items[fasilitas_{{ $i }}_desc]" rows="3"
                                                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-xs leading-relaxed text-slate-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">{{ old('items.fasilitas_'.$i.'_desc', $card['desc']) }}</textarea>
                                        </div>

                                        {{-- Aksi kartu: Edit / Tambah paragraf / Hapus kartu --}}
                                        <div class="flex flex-wrap items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-700/60 pt-3">
                                            <button type="button" onclick="kb.edit(event, this)" title="Ubah isi fasilitas ini"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 px-2.5 py-1.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-300 transition hover:bg-emerald-100 dark:hover:bg-emerald-900/50">
                                                <i class="fa-solid fa-pen text-[9px]"></i> Edit
                                            </button>
                                            <button type="button" onclick="kb.add(event, this)" data-add-type="line"
                                                data-add-target="items[fasilitas_{{ $i }}_desc]" title="Tambah paragraf baru"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 px-2.5 py-1.5 text-[10px] font-bold text-slate-600 dark:text-slate-300 transition hover:bg-slate-200 dark:hover:bg-slate-600">
                                                <i class="fa-solid fa-plus text-[9px]"></i> Tambah
                                            </button>
                                            <button type="button" onclick="kb.hapusKartu(event, this)"
                                                data-keys="fasilitas_{{ $i }}_title,fasilitas_{{ $i }}_desc,fasilitas_{{ $i }}_foto"
                                                title="Hapus kartu fasilitas ini"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 dark:bg-red-950/40 px-2.5 py-1.5 text-[10px] font-bold text-red-600 dark:text-red-300 transition hover:bg-red-100 dark:hover:bg-red-900/40">
                                                <i class="fa-solid fa-trash text-[9px]"></i> Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Tambah kartu fasilitas baru (tanpa reload). --}}
                        @if (count($fasilitasCards) === 0)
                            <p class="mb-1 text-center text-xs text-slate-400 dark:text-slate-500">
                                Belum ada kartu fasilitas. Klik tombol di bawah untuk menambah.
                            </p>
                        @endif
                        <button type="button" onclick="kb.tambahKartu(event, this)" data-next="{{ $fasilitasNext }}"
                            class="flex w-full items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-600 bg-white/60 dark:bg-slate-800/40 px-4 py-4 text-xs font-bold text-slate-500 dark:text-slate-300 transition hover:border-emerald-400 hover:bg-emerald-50/60 hover:text-emerald-700 dark:hover:bg-emerald-950/30">
                            <i class="fa-solid fa-circle-plus text-base"></i> Tambah fasilitas
                        </button>

                        <template id="kb-facility-template">
                            <div data-kb-box data-kb-fac-card
                                class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm overflow-hidden">
                                <div class="relative h-40 bg-slate-100 dark:bg-slate-900">
                                    <div
                                        class="flex h-full w-full flex-col items-center justify-center gap-1.5 text-slate-400">
                                        <i class="fa-solid fa-image text-2xl"></i>
                                        <span class="text-[10px] font-semibold">Foto belum ada</span>
                                    </div>
                                    <span
                                        class="absolute left-3 top-3 inline-flex items-center rounded-full bg-black/60 px-2.5 py-1 text-[10px] font-bold text-white">Fasilitas
                                        __NUM__</span>
                                </div>

                                <div class="p-4 space-y-3">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <label for="fasilitas_foto___INDEX__"
                                            class="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 px-3 py-2 text-[11px] font-bold text-emerald-700 dark:text-emerald-300 transition hover:bg-emerald-100 dark:hover:bg-emerald-900/50">
                                            <i class="fa-solid fa-cloud-arrow-up"></i> Pilih foto
                                        </label>
                                        <input id="fasilitas_foto___INDEX__" type="file"
                                            name="fasilitas___INDEX___foto" accept="image/*" class="sr-only">
                                        <span class="text-[10px] text-slate-400">Maks. 2 MB</span>
                                    </div>

                                    <div>
                                        <label
                                            class="mb-1 block text-[11px] font-semibold text-slate-500 dark:text-slate-400">Nama
                                            fasilitas</label>
                                        <input type="text" name="items[fasilitas___INDEX___title]"
                                            placeholder="Contoh: Aula Serbaguna"
                                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-xs text-slate-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                    </div>

                                    <div>
                                        <label
                                            class="mb-1 block text-[11px] font-semibold text-slate-500 dark:text-slate-400">Keterangan
                                            fasilitas</label>
                                        <textarea name="items[fasilitas___INDEX___desc]" rows="3"
                                            placeholder="Ceritakan singkat fungsi fasilitas ini."
                                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-xs leading-relaxed text-slate-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"></textarea>
                                    </div>

                                    <div
                                        class="flex flex-wrap items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-700/60 pt-3">
                                        <button type="button" onclick="kb.edit(event, this)" title="Ubah isi fasilitas ini"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 px-2.5 py-1.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-300 transition hover:bg-emerald-100 dark:hover:bg-emerald-900/50">
                                            <i class="fa-solid fa-pen text-[9px]"></i> Edit
                                        </button>
                                        <button type="button" onclick="kb.hapusKartu(event, this)"
                                            data-keys="fasilitas___INDEX___title,fasilitas___INDEX___desc,fasilitas___INDEX___foto"
                                            title="Batalkan kartu ini"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 dark:bg-red-950/40 px-2.5 py-1.5 text-[10px] font-bold text-red-600 dark:text-red-300 transition hover:bg-red-100 dark:hover:bg-red-900/40">
                                            <i class="fa-solid fa-trash text-[9px]"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    @endif

                    {{-- ============ SECTION KONTEN (selain fasilitas) ============ --}}
                    @if ($pageKey !== 'fasilitas')
                        @foreach ($sections as $sectionName => $fields)
                            @php
                                $sectionKeys = $fields->pluck('key')->all();

                                $listRow = $fields->first(fn ($f) => in_array($f['key'], $listKeys, true)
                                    || str_ends_with($f['key'], '_items'));
                                $textRow = $listRow ? null : $fields->first(fn ($f) => str_contains((string) $f['value'], "\n")
                                    || mb_strlen((string) $f['value']) > 80);

                                $addType = $listRow ? 'list' : ($textRow ? 'line' : null);
                                $addTarget = $listRow['key'] ?? ($textRow['key'] ?? null);
                            @endphp

                            <details data-kb-box
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

                                    <div class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                                        <span
                                            class="hidden sm:inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-700 px-2 py-0.5 text-[10px] font-semibold text-slate-500 dark:text-slate-300">{{ $fields->count() }}
                                            kolom</span>

                                        {{-- Aksi bagian: Edit / Tambah / Hapus --}}
                                        <button type="button" onclick="kb.edit(event, this)"
                                            title="Buka dan ubah isi bagian ini"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 px-2.5 py-1.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-300 transition hover:bg-emerald-100 dark:hover:bg-emerald-900/50">
                                            <i class="fa-solid fa-pen text-[9px]"></i>
                                            <span class="hidden md:inline">Edit</span>
                                        </button>

                                        @if ($addType)
                                            <button type="button" onclick="kb.add(event, this)"
                                                data-add-type="{{ $addType }}" data-add-target="{{ $addTarget }}"
                                                title="{{ $addType === 'list' ? 'Tambah item baru pada daftar ini' : 'Tambah paragraf baru' }}"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 px-2.5 py-1.5 text-[10px] font-bold text-slate-600 dark:text-slate-300 transition hover:bg-slate-200 dark:hover:bg-slate-600">
                                                <i class="fa-solid fa-plus text-[9px]"></i>
                                                <span class="hidden md:inline">Tambah</span>
                                            </button>
                                        @else
                                            <button type="button" disabled
                                                title="Bagian ini hanya berisi teks singkat"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 px-2.5 py-1.5 text-[10px] font-bold text-slate-400 dark:text-slate-500 opacity-60 cursor-not-allowed">
                                                <i class="fa-solid fa-plus text-[9px]"></i>
                                                <span class="hidden md:inline">Tambah</span>
                                            </button>
                                        @endif

                                        <button type="button" onclick="kb.reset(event, this)"
                                            data-keys="{{ implode(',', $sectionKeys) }}" data-label="{{ $sectionName }}"
                                            title="Kembalikan seluruh isi bagian ini ke nilai bawaan"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 dark:bg-red-950/40 px-2.5 py-1.5 text-[10px] font-bold text-red-600 dark:text-red-300 transition hover:bg-red-100 dark:hover:bg-red-900/40">
                                            <i class="fa-solid fa-trash text-[9px]"></i>
                                            <span class="hidden md:inline">Hapus</span>
                                        </button>

                                        <i
                                            class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform group-open:rotate-180 ml-1"></i>
                                    </div>
                                </summary>

                                <div class="p-4 sm:p-5 border-t border-slate-100 dark:border-slate-700/60">
                                    @include('dashboard.konten._fields', ['fields' => $fields, 'listKeys' => $listKeys])
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

    <script>
        // Aksi cepat per bagian: Edit, Tambah, Hapus.
        window.kb = {
            stop(e) {
                e.preventDefault();
                e.stopPropagation();
            },
            box(btn) {
                return btn.closest('[data-kb-box]') || btn.closest('details');
            },
            open(box) {
                if (box && box.tagName === 'DETAILS') {
                    box.open = true;
                }
            },
            edit(e, btn) {
                this.stop(e);
                const box = this.box(btn);
                if (!box) return;

                this.open(box);

                const field = box.querySelector('input:not([type="hidden"]):not([type="file"]), textarea');
                if (!field) return;

                field.focus({ preventScroll: true });
                field.scrollIntoView({ block: 'center', behavior: 'smooth' });
            },
            add(e, btn) {
                this.stop(e);
                const box = this.box(btn);
                const type = btn.dataset.addType;
                const target = btn.dataset.addTarget;

                this.open(box);

                if (type === 'list') {
                    window.dispatchEvent(new CustomEvent('kb-add-row', { detail: target }));
                    if (box) box.scrollIntoView({ block: 'start', behavior: 'smooth' });
                    return;
                }

                const field = document.getElementsByName(target)[0];
                if (!field) return;

                if (field.tagName === 'TEXTAREA') {
                    field.value = field.value.replace(/\s+$/, '') + (field.value.trim() ? '\n\n' : '');
                }

                field.dispatchEvent(new Event('input', { bubbles: true }));
                field.focus({ preventScroll: true });
                field.scrollIntoView({ block: 'center', behavior: 'smooth' });
            },
            reset(e, btn) {
                this.stop(e);

                const keys = (btn.dataset.keys || '').split(',').filter(Boolean);
                const label = btn.dataset.label || 'bagian ini';

                if (!keys.length) return;
                if (!confirm('Kembalikan seluruh isi "' + label + '" ke nilai bawaan?')) return;

                const input = document.getElementById('kb-delete-keys');
                if (!input) return;

                input.value = keys.join(',');
                document.getElementById('konten-form').submit();
            },
            hapusFoto(e, key) {
                this.stop(e);
                if (!confirm('Hapus foto ini? Foto akan dihapus dari server.')) return;

                const input = document.getElementById('kb-delete-foto');
                if (!input) return;

                input.value = key;
                document.getElementById('konten-form').submit();
            },
            tambahKartu(e, btn) {
                this.stop(e);

                const tpl = document.getElementById('kb-facility-template');
                const grid = document.getElementById('kb-facility-grid');
                if (!tpl || !grid) return;

                let index = parseInt(btn.dataset.next || '1', 10);
                if (!index || index < 1) index = 1;
                btn.dataset.next = String(index + 1);

                const number = grid.querySelectorAll('[data-kb-fac-card]').length + 1;
                const html = tpl.innerHTML
                    .replace(/__INDEX__/g, String(index))
                    .replace(/__NUM__/g, String(number));

                const wrap = document.createElement('div');
                wrap.innerHTML = html.trim();
                const node = wrap.firstElementChild;
                if (!node) return;

                grid.appendChild(node);

                if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                    window.Alpine.initTree(node);
                }

                node.scrollIntoView({ block: 'center', behavior: 'smooth' });

                const field = node.querySelector('input[type="text"]');
                if (field) field.focus({ preventScroll: true });
            },
            hapusKartu(e, btn) {
                this.stop(e);

                const keys = (btn.dataset.keys || '').split(',').filter(Boolean);
                if (!keys.length) return;
                if (!confirm('Hapus kartu fasilitas ini? Foto yang terpasang juga ikut dihapus.')) return;

                const input = document.getElementById('kb-empty-keys');
                if (!input) return;

                input.value = keys.join(',');
                document.getElementById('konten-form').submit();
            },
            pilihFoto(e, id) {
                this.stop(e);
                const el = document.getElementById(id);
                if (el) el.click();
            }
        };
    </script>
@endsection
