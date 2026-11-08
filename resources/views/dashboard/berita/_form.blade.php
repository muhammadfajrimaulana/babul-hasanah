{{--
    Form editor berita (dipakai oleh create & edit).
    Variabel: $berita (null saat create), $action, $method ('POST' | 'PUT')
--}}
@php
    $berita = $berita ?? null;
    $kategoriList = ['Kegiatan', 'Pengumuman', 'Prestasi', 'Artikel', 'Informasi PSB'];
    $publishedAt = old('published_at', optional($berita?->published_at)->format('Y-m-d\TH:i'));
    $statusNow = old('status', $berita?->status ?? 'draft');
    $isPublished = ($berita?->status ?? 'draft') === 'published';

    // Unggah gambar isi berita ke server (opsional). Jika route belum dibuat, gambar disisipkan sebagai base64.
    $uploadUrl = \Illuminate\Support\Facades\Route::has('admin.berita.upload-gambar') ? route('admin.berita.upload-gambar') : null;

    $card = 'bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5';
    $label = 'block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2';
    $hint = 'text-[11px] text-slate-400 mt-1.5';
    $errCls = 'text-[11px] font-semibold text-red-600 mt-1.5';
    $inputClass = 'w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition';
    $btnGhost = 'px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition';
    $btnPrimary = 'px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition flex items-center justify-center gap-2';
    $lanjutanOpen = $errors->has('slug') || $errors->has('ringkasan');
@endphp

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
<style>
    /* Diisi otomatis oleh JS: jarak padding area konten admin */
    :root { --sticky-pt: 0px; }

    /* Bilah aksi menempel rapat di bawah navbar saat halaman digulir */
    #editor-bar { top: calc(var(--sticky-pt) * -1); }

    /* Editor: toolbar selalu terlihat, isi tulisan digulir di dalam kartu */
    #editor-wrap { overflow: hidden; }
    #editor-wrap .ql-toolbar.ql-snow { border: 0; border-bottom: 1px solid #e2e8f0; padding: 10px 14px; background: #f8fafc; box-sizing: border-box; height: auto; }
    #editor-wrap .ql-container.ql-snow { border: 0; font-family: inherit; height: max(640px, 85vh); }
    #editor-wrap .ql-editor { padding: 32px 44px; font-size: 16px; line-height: 1.75; color: #0f172a; }
    #editor-wrap .ql-editor.ql-blank::before { left: 44px; right: 44px; font-style: normal; color: #94a3b8; }
    #editor-wrap .ql-editor img { cursor: pointer; }
    #editor-wrap:focus-within { box-shadow: 0 0 0 2px rgb(16 185 129 / .35); border-color: rgb(16 185 129 / .6); }
    #editor-wrap.has-error { border-color: #f87171; box-shadow: 0 0 0 2px rgb(248 113 113 / .25); }

    .dark #editor-wrap .ql-toolbar.ql-snow { background: #0b1325; border-color: #1e293b; }
    .dark #editor-wrap .ql-editor { color: #e2e8f0; }
    .dark #editor-wrap .ql-snow .ql-stroke { stroke: #cbd5e1; }
    .dark #editor-wrap .ql-snow .ql-fill { fill: #cbd5e1; }
    .dark #editor-wrap .ql-snow .ql-picker { color: #cbd5e1; }

    /* Bilah pengaturan gambar (muncul saat gambar diklik) */
    #img-bar { position: absolute; z-index: 25; display: none; align-items: center; gap: 2px; padding: 4px; border-radius: 12px; background: #0f172a; box-shadow: 0 8px 24px rgb(15 23 42 / .35); }
    #img-bar.show { display: flex; }
    #img-frame { position: absolute; z-index: 24; display: none; pointer-events: none; border: 2px solid #10b981; border-radius: 12px; }
    #img-frame.show { display: block; }
    .img-btn { display: inline-flex; align-items: center; justify-content: center; min-width: 30px; height: 30px; padding: 0 8px; border-radius: 8px; font-size: 11px; font-weight: 700; color: #e2e8f0; transition: background .15s; }
    .img-btn:hover { background: rgba(255,255,255,.14); }
    .img-btn.active { background: #10b981; color: #fff; }
    .img-btn.danger:hover { background: #dc2626; }
    .img-sep { width: 1px; height: 18px; background: rgba(255,255,255,.22); margin: 0 4px; }

    /* Modal potong gambar */
    #crop-img { display: block; max-width: 100%; }
    .crop-chip { padding: 6px 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 11px; font-weight: 700; color: #475569; transition: all .15s; }
    .dark .crop-chip { border-color: #334155; color: #cbd5e1; }
    .crop-chip:hover { border-color: #10b981; }
    .crop-chip.active { background: #10b981; border-color: #10b981; color: #fff; }

    #judul-input:focus { outline: none; }
    details.panel > summary { list-style: none; cursor: pointer; }
    details.panel > summary::-webkit-details-marker { display: none; }
    details.panel[open] .chev { transform: rotate(180deg); }

    @media (max-width: 640px) {
        #editor-wrap .ql-editor { padding: 20px 18px; }
        #editor-wrap .ql-editor.ql-blank::before { left: 18px; right: 18px; }
    }
</style>

<form id="berita-form" action="{{ $action }}" method="POST" novalidate>
    @csrf
    @if ($method !== 'POST') @method($method) @endif
    <input type="hidden" name="status" id="status-input" value="{{ $statusNow }}">
    <textarea name="isi" id="isi-input" class="hidden">{{ old('isi', $berita?->isi ?? '') }}</textarea>

    {{-- ===== Bilah aksi ===== --}}
    <div id="editor-bar" class="sticky z-30 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-3 bg-white/95 dark:bg-slate-900/95 backdrop-blur border-b border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('admin.berita.index') }}" title="Kembali ke daftar berita"
                class="w-9 h-9 shrink-0 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 flex items-center justify-center transition">
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </a>
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <p class="text-sm font-black text-slate-900 dark:text-white truncate">{{ $berita ? 'Edit Berita' : 'Berita Baru' }}</p>
                    @if ($isPublished)
                        <span class="px-2 py-0.5 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 rounded-md text-[10px] font-bold">Terbit</span>
                    @else
                        <span class="px-2 py-0.5 bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 rounded-md text-[10px] font-bold">Draft</span>
                    @endif
                </div>
                <p id="save-state" class="text-[10px] text-slate-400 truncate">
                    @if ($berita) Terakhir diubah {{ $berita->updated_at->diffForHumans() }} @else Belum disimpan @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <button type="button" id="btn-preview" class="{{ $btnGhost }} flex items-center gap-2">
                <i class="fa-solid fa-eye"></i> <span class="hidden sm:inline">Pratinjau</span>
            </button>
            <button type="submit" onclick="document.getElementById('status-input').value='draft'" class="{{ $btnGhost }}">
                Simpan draft
            </button>
            <button type="submit" onclick="document.getElementById('status-input').value='published'" class="{{ $btnPrimary }}">
                <i class="fa-solid fa-paper-plane"></i> {{ $isPublished ? 'Perbarui' : 'Publikasikan' }}
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="mt-5 px-4 py-3 rounded-xl bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-300 text-xs border border-red-100 dark:border-red-900/50 flex gap-3">
            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
            <div>
                <p class="font-bold mb-1">Berita belum bisa disimpan. Periksa isian berikut:</p>
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="mt-6 grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_340px] gap-6 items-start">

        {{-- ===== Kolom editor ===== --}}
        <div class="min-w-0 space-y-4">
            <div>
                <input type="text" name="judul" id="judul-input" value="{{ old('judul', $berita?->judul ?? '') }}"
                    placeholder="Tulis judul berita di sini…" autocomplete="off" maxlength="255"
                    class="w-full bg-transparent text-2xl sm:text-3xl font-black leading-tight text-slate-900 dark:text-white placeholder-slate-300 dark:placeholder-slate-600 pb-3 border-0 border-b-2 border-slate-200 dark:border-slate-700 focus:border-emerald-500 transition">
                <div class="flex items-center justify-between mt-1.5">
                    @error('judul') <p class="{{ $errCls }}">{{ $message }}</p> @else <span></span> @enderror
                    <span id="judul-count" class="text-[10px] text-slate-400">0/255</span>
                </div>
            </div>

            <div id="editor-wrap" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition">
                <div id="toolbar">
                    <span class="ql-formats">
                        <select class="ql-header">
                            <option value="1">Judul besar</option>
                            <option value="2">Sub judul</option>
                            <option value="3">Judul kecil</option>
                            <option selected>Normal</option>
                        </select>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-bold" title="Tebal"></button>
                        <button class="ql-italic" title="Miring"></button>
                        <button class="ql-underline" title="Garis bawah"></button>
                        <button class="ql-strike" title="Coret"></button>
                    </span>
                    <span class="ql-formats">
                        <select class="ql-color"></select>
                        <select class="ql-background"></select>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-list" value="ordered" title="Daftar nomor"></button>
                        <button class="ql-list" value="bullet" title="Daftar poin"></button>
                        <button class="ql-indent" value="-1"></button>
                        <button class="ql-indent" value="+1"></button>
                        <select class="ql-align"></select>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-blockquote" title="Kutipan"></button>
                        <button class="ql-link" title="Tautan"></button>
                        <button class="ql-image" title="Sisipkan gambar"></button>
                        <button class="ql-video" title="Sisipkan video"></button>
                    </span>
                    <span class="ql-formats"><button class="ql-clean" title="Hapus format"></button></span>
                </div>
                <div id="editor"></div>
            </div>

            {{-- Bilah pengaturan gambar: dipindahkan ke dalam editor oleh JS --}}
            <div id="img-frame"></div>
            <div id="img-bar" role="toolbar" aria-label="Pengaturan gambar">
                <button type="button" class="img-btn" data-a="left" title="Rata kiri, teks mengalir di kanan"><i class="fa-solid fa-align-left"></i></button>
                <button type="button" class="img-btn" data-a="center" title="Di tengah"><i class="fa-solid fa-align-center"></i></button>
                <button type="button" class="img-btn" data-a="right" title="Rata kanan, teks mengalir di kiri"><i class="fa-solid fa-align-right"></i></button>
                <button type="button" class="img-btn" data-a="full" title="Lebar penuh"><i class="fa-solid fa-expand"></i></button>
                <span class="img-sep"></span>
                <button type="button" class="img-btn" data-s="25" title="Ukuran 25%">25%</button>
                <button type="button" class="img-btn" data-s="50" title="Ukuran 50%">50%</button>
                <button type="button" class="img-btn" data-s="75" title="Ukuran 75%">75%</button>
                <button type="button" class="img-btn" data-s="100" title="Ukuran 100%">100%</button>
                <span class="img-sep"></span>
                <button type="button" class="img-btn" data-do="crop" title="Potong gambar"><i class="fa-solid fa-crop-simple"></i></button>
                <button type="button" class="img-btn danger" data-do="del" title="Hapus gambar"><i class="fa-solid fa-trash"></i></button>
            </div>
            <input type="file" id="img-file" accept="image/*" class="hidden">

            <div class="flex flex-wrap items-center justify-between gap-2 text-[11px] text-slate-400 px-1">
                <span>
                    <span id="word-count" class="font-semibold">0 / 1.000 kata</span> · <span id="read-time">0 menit baca</span>
                    <span id="img-status" class="ml-2 text-emerald-600 font-semibold"></span>
                </span>
                <span class="hidden sm:inline">Klik gambar di dalam tulisan untuk mengatur posisi, ukuran, atau memotongnya.</span>
            </div>
            <p id="limit-msg" class="{{ $errCls }} hidden">Batas 1.000 kata tercapai. Teks yang melebihi batas tidak ditambahkan.</p>
            <p id="isi-error" class="{{ $errCls }} {{ $errors->has('isi') ? '' : 'hidden' }}">Isi berita belum diisi.</p>
        </div>

        {{-- ===== Sidebar ===== --}}
        <aside class="space-y-4">

            {{-- Publikasi --}}
            <div class="{{ $card }}">
                <span class="{{ $label }}">Publikasi</span>
                <div class="flex items-center justify-between text-xs mb-4">
                    <span class="text-slate-500">Status</span>
                    @if ($isPublished)
                        <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 rounded-lg font-bold">Terbit</span>
                    @else
                        <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-500 rounded-lg font-bold">Draft</span>
                    @endif
                </div>

                <label for="published-at" class="text-xs font-semibold text-slate-600 dark:text-slate-300 block mb-1.5">Tanggal terbit</label>
                <input type="datetime-local" name="published_at" id="published-at" value="{{ $publishedAt }}" class="{{ $inputClass }}">
                <p class="{{ $hint }}">Kosongkan untuk memakai waktu saat dipublikasikan.</p>
                @error('published_at') <p class="{{ $errCls }}">{{ $message }}</p> @enderror

                <div class="grid grid-cols-2 gap-2 mt-4">
                    <button type="submit" onclick="document.getElementById('status-input').value='draft'" class="{{ $btnGhost }}">Simpan draft</button>
                    <button type="submit" onclick="document.getElementById('status-input').value='published'" class="{{ $btnPrimary }}">
                        {{ $isPublished ? 'Perbarui' : 'Publikasikan' }}
                    </button>
                </div>
            </div>

            {{-- Gambar utama --}}
            <div class="{{ $card }}">
                <span class="{{ $label }}">Gambar Utama <span class="text-[10px] text-slate-500">(maks. 2 MB)</span></span>
                <div class="relative group">
                    <img id="preview-gambar" src="{{ $berita?->gambar_url ?? '' }}"
                        class="{{ $berita?->gambar ? '' : 'hidden' }} w-full h-32 object-cover rounded-xl mb-2 border border-slate-100 dark:border-slate-800">
                    <label class="block w-full h-32 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800/50 flex flex-col items-center justify-center gap-1 cursor-pointer hover:border-emerald-500 dark:hover:border-emerald-700 transition"
                        onclick="document.getElementById('gambar-input').click()">
                        <i class="fa-solid fa-cloud-arrow-up text-emerald-500 dark:text-emerald-400 text-lg"></i>
                        <span class="text-[11px] font-medium text-slate-600 dark:text-slate-400">
                            {{ $berita?->gambar ? 'Ganti gambar' : 'Pilih gambar' }}
                        </span>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500">JPG, PNG, WebP</span>
                    </label>
                    <input type="file" name="gambar" id="gambar-input" accept="image/*" class="hidden"
                        onchange="previewGambar(this)">
                    @error('gambar') <p class="{{ $errCls }}">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Detail --}}
            <div class="{{ $card }} space-y-4">
                <div>
                    <label for="kategori-input" class="{{ $label }}">Kategori</label>
                    <select name="kategori" id="kategori-input" class="{{ $inputClass }}">
                        <option value="">Pilih kategori…</option>
                        @foreach ($kategoriList as $k)
                            <option value="{{ $k }}" @selected(old('kategori', $berita?->kategori ?? 'Kegiatan') === $k)>{{ $k }}</option>
                        @endforeach
                    </select>
                    @error('kategori') <p class="{{ $errCls }}">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="penulis-input" class="{{ $label }}">Penulis</label>
                    <input type="text" name="penulis" id="penulis-input" maxlength="100"
                        value="{{ old('penulis', $berita?->penulis ?? auth()->user()->name ?? 'Admin') }}"
                        class="{{ $inputClass }}">
                    <p class="{{ $hint }}">Tampil sebagai "Oleh …" di halaman berita.</p>
                    @error('penulis') <p class="{{ $errCls }}">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Lanjutan --}}
            <details class="panel {{ $card }}" @if ($lanjutanOpen) open @endif>
                <summary class="flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    <span>Pengaturan lanjutan</span>
                    <i class="fa-solid fa-chevron-down chev text-[10px] transition-transform"></i>
                </summary>
                <div class="space-y-4 mt-4">
                    <div>
                        <label for="slug-input" class="text-xs font-semibold text-slate-600 dark:text-slate-300 block mb-1.5">Permalink</label>
                        <input type="text" name="slug" id="slug-input" value="{{ old('slug', $berita?->slug ?? '') }}"
                            placeholder="otomatis-dari-judul" class="{{ $inputClass }} font-mono">
                        <p class="{{ $hint }} break-all">{{ url('/berita') }}/<span id="slug-preview">{{ old('slug', $berita?->slug ?? '…') }}</span></p>
                        @error('slug') <p class="{{ $errCls }}">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="ringkasan-input" class="text-xs font-semibold text-slate-600 dark:text-slate-300 block mb-1.5">Ringkasan</label>
                        <textarea name="ringkasan" id="ringkasan-input" rows="3" maxlength="300"
                            placeholder="Opsional. Tampil di kartu daftar berita."
                            class="{{ $inputClass }} resize-none">{{ old('ringkasan', $berita?->ringkasan ?? '') }}</textarea>
                        <div class="flex justify-between">
                            @error('ringkasan') <p class="{{ $errCls }}">{{ $message }}</p> @else <span></span> @enderror
                            <span id="ringkasan-count" class="text-[10px] text-slate-400 mt-1.5">0/300</span>
                        </div>
                    </div>
                </div>
            </details>
        </aside>
    </div>
</form>

{{-- ===== Modal potong gambar ===== --}}
<div id="crop-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-900/70 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-3xl rounded-3xl shadow-xl overflow-hidden flex flex-col max-h-[94vh]">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <div>
                <p class="text-sm font-black text-slate-900 dark:text-white">Potong &amp; atur gambar</p>
                <p class="text-[11px] text-slate-400">Geser gambar atau tarik kotak untuk memilih bagian yang dipakai.</p>
            </div>
            <button type="button" id="crop-close" class="w-8 h-8 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="bg-slate-900" style="height: min(55vh, 480px);">
            <img id="crop-img" alt="">
        </div>

        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 space-y-3">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-1">Rasio</span>
                <button type="button" class="crop-chip active" data-r="free">Bebas</button>
                <button type="button" class="crop-chip" data-r="1.7778">16:9</button>
                <button type="button" class="crop-chip" data-r="1.3333">4:3</button>
                <button type="button" class="crop-chip" data-r="1">1:1</button>
                <button type="button" class="crop-chip" data-r="0.75">3:4</button>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-1">Alat</span>
                <button type="button" class="crop-chip" data-t="zoom-in" title="Perbesar"><i class="fa-solid fa-magnifying-glass-plus"></i></button>
                <button type="button" class="crop-chip" data-t="zoom-out" title="Perkecil"><i class="fa-solid fa-magnifying-glass-minus"></i></button>
                <button type="button" class="crop-chip" data-t="rot-l" title="Putar kiri"><i class="fa-solid fa-rotate-left"></i></button>
                <button type="button" class="crop-chip" data-t="rot-r" title="Putar kanan"><i class="fa-solid fa-rotate-right"></i></button>
                <button type="button" class="crop-chip" data-t="flip" title="Balik horizontal"><i class="fa-solid fa-left-right"></i></button>
                <button type="button" class="crop-chip" data-t="reset" title="Atur ulang">Atur ulang</button>
            </div>
        </div>

        <div class="flex justify-end gap-2 px-6 py-4 border-t border-slate-100 dark:border-slate-800">
            <button type="button" id="crop-cancel" class="{{ $btnGhost }}">Batal</button>
            <button type="button" id="crop-ok" class="{{ $btnPrimary }}"><i class="fa-solid fa-check"></i> Gunakan gambar</button>
        </div>
    </div>
</div>

{{-- ===== Modal pratinjau ===== --}}
<div id="preview-modal" class="fixed inset-0 z-50 hidden items-start justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 w-full max-w-3xl rounded-3xl shadow-xl my-8 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <p class="text-xs font-black text-slate-900 dark:text-white">Pratinjau</p>
            <button type="button" id="preview-close" class="w-8 h-8 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <article class="px-6 sm:px-10 py-8">
            <p id="pv-cat" class="text-[11px] font-bold tracking-wider uppercase text-amber-600 mb-2"></p>
            <h1 id="pv-title" class="text-3xl font-black text-slate-900 dark:text-white mb-2"></h1>
            <p id="pv-meta" class="text-xs text-slate-400 mb-6"></p>
            <div id="pv-body" class="ql-editor flow-root !p-0 !min-h-0 !h-auto"></div>
        </article>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const $ = id => document.getElementById(id);
    const MAX_WORDS = 1000;
    const uploadUrl = @json($uploadUrl);
    const csrf = @json(csrf_token());

    const form = $('berita-form');
    const isiInput = $('isi-input');
    const judul = $('judul-input');
    const slugInput = $('slug-input');
    const slugPreview = $('slug-preview');
    const saveState = $('save-state');
    const editorWrap = $('editor-wrap');
    const isiError = $('isi-error');
    const limitMsg = $('limit-msg');
    let dirty = false;

    // ---- Posisi bilah aksi: menyesuaikan padding area konten admin ----
    const bar = $('editor-bar');
    const findScroller = el => {
        for (let p = el.parentElement; p && p !== document.body; p = p.parentElement) {
            const oy = getComputedStyle(p).overflowY;
            if (oy === 'auto' || oy === 'scroll' || oy === 'overlay') return p;
        }
        return null;
    };
    const scroller = findScroller(bar);
    const syncSticky = () => {
        const pt = scroller ? (parseFloat(getComputedStyle(scroller).paddingTop) || 0) : 0;
        document.documentElement.style.setProperty('--sticky-pt', pt + 'px');
    };
    syncSticky();
    window.addEventListener('resize', syncSticky);

    // =====================================================================
    //  Format gambar kustom: menyimpan posisi (align) dan ukuran di HTML
    // =====================================================================
    const BaseImage = Quill.import('formats/image');
    const IMG_ATTRS = ['alt', 'height', 'width', 'style', 'data-align', 'data-size'];
    class LayoutImage extends BaseImage {
        static formats(node) {
            return IMG_ATTRS.reduce((f, a) => { if (node.hasAttribute(a)) f[a] = node.getAttribute(a); return f; }, {});
        }
        format(name, value) {
            if (IMG_ATTRS.includes(name)) {
                if (value) this.domNode.setAttribute(name, value); else this.domNode.removeAttribute(name);
            } else {
                super.format(name, value);
            }
        }
    }
    Quill.register(LayoutImage, true);

    const buildStyle = (align, size) => {
        const base = 'height:auto;max-width:100%;border-radius:12px;';
        if (align === 'left')  return base + 'float:left;width:' + size + '%;margin:4px 20px 12px 0;';
        if (align === 'right') return base + 'float:right;width:' + size + '%;margin:4px 0 12px 20px;';
        if (align === 'full')  return base + 'display:block;width:100%;margin:16px 0;';
        return base + 'display:block;width:' + size + '%;margin:16px auto;';
    };
    const layoutFormats = (align, size) => ({ 'data-align': align, 'data-size': String(size), style: buildStyle(align, size) });

    // ---- Editor ----
    let openPicker = () => {};
    const quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Mulai menulis berita di sini…',
        modules: { toolbar: { container: '#toolbar', handlers: { image: () => openPicker() } } },
    });
    if (isiInput.value.trim()) quill.clipboard.dangerouslyPasteHTML(isiInput.value);

    // ---- Hitung kata & batas 1.000 kata ----
    const wordsOf = text => { const t = text.trim(); return t ? t.split(/\s+/).length : 0; };
    const wordCount = () => wordsOf(quill.getText());
    const fmt = n => n.toLocaleString('id-ID');

    const refreshCounter = () => {
        const n = wordCount();
        const el = $('word-count');
        el.textContent = fmt(n) + ' / ' + fmt(MAX_WORDS) + ' kata';
        el.className = 'font-semibold ' + (n >= MAX_WORDS ? 'text-red-600' : n >= MAX_WORDS * 0.9 ? 'text-amber-600' : '');
        $('read-time').textContent = Math.max(n ? 1 : 0, Math.round(n / 200)) + ' menit baca';
    };
    refreshCounter();

    const isEmpty = () => quill.getText().trim() === '' && !quill.root.querySelector('img, iframe');
    const markDirty = () => { dirty = true; saveState.textContent = 'Ada perubahan yang belum disimpan'; };

    let lastRange = null, limitTimer = null;
    quill.on('selection-change', r => { if (r) lastRange = r; });
    quill.on('text-change', (delta, oldDelta, source) => {
        if (source === 'user') {
            const oldText = oldDelta.ops.map(o => typeof o.insert === 'string' ? o.insert : ' ').join('');
            if (wordCount() > MAX_WORDS && wordCount() > wordsOf(oldText)) {
                quill.updateContents(delta.invert(oldDelta), 'silent'); // batalkan perubahan yang melewati batas
                if (lastRange) quill.setSelection(lastRange.index, 0, 'silent');
                limitMsg.classList.remove('hidden');
                clearTimeout(limitTimer);
                limitTimer = setTimeout(() => limitMsg.classList.add('hidden'), 4000);
                refreshCounter();
                return;
            }
        }
        refreshCounter(); markDirty();
        if (!isEmpty()) { isiError.classList.add('hidden'); editorWrap.classList.remove('has-error'); }
        requestAnimationFrame(placeImgBar);
    });
    form.addEventListener('input', markDirty);

    // =====================================================================
    //  Pengaturan gambar: klik gambar -> atur posisi, ukuran, potong, hapus
    // =====================================================================
    const imgBar = $('img-bar'), imgFrame = $('img-frame');
    quill.container.appendChild(imgFrame);
    quill.container.appendChild(imgBar);
    let selImg = null;

    const hideImgBar = () => { selImg = null; imgBar.classList.remove('show'); imgFrame.classList.remove('show'); };

    function placeImgBar() {
        if (!selImg) return;
        if (!selImg.isConnected) return hideImgBar();
        const c = quill.container.getBoundingClientRect();
        const r = selImg.getBoundingClientRect();
        if (r.bottom < c.top + 8 || r.top > c.bottom - 8) {
            imgBar.classList.remove('show'); imgFrame.classList.remove('show');
            return;
        }
        imgFrame.style.top = (r.top - c.top) + 'px';
        imgFrame.style.left = (r.left - c.left) + 'px';
        imgFrame.style.width = r.width + 'px';
        imgFrame.style.height = r.height + 'px';
        imgFrame.classList.add('show');
        imgBar.classList.add('show');
        const left = Math.max(8, Math.min(r.left - c.left + 8, c.width - imgBar.offsetWidth - 8));
        imgBar.style.left = left + 'px';
        imgBar.style.top = Math.max(8, r.top - c.top + 8) + 'px';
    }
    function refreshImgBar() {
        const al = selImg.dataset.align || 'center';
        const sz = selImg.dataset.size || '';
        imgBar.querySelectorAll('[data-a]').forEach(b => b.classList.toggle('active', b.dataset.a === al));
        imgBar.querySelectorAll('[data-s]').forEach(b => b.classList.toggle('active', al !== 'full' && b.dataset.s === sz));
    }
    const selectImg = img => { selImg = img; refreshImgBar(); placeImgBar(); };

    quill.root.addEventListener('click', e => { if (e.target.tagName === 'IMG') selectImg(e.target); else hideImgBar(); });
    quill.root.addEventListener('scroll', () => requestAnimationFrame(placeImgBar));
    window.addEventListener('resize', () => requestAnimationFrame(placeImgBar));

    const applyLayout = patch => {
        const blot = selImg && Quill.find(selImg);
        if (!blot) return;
        const idx = quill.getIndex(blot);
        let align = patch.align || selImg.dataset.align || 'center';
        let size = patch.size || parseInt(selImg.dataset.size || '75', 10);
        if (patch.size && align === 'full') align = 'center';
        if (align === 'full') size = 100;
        quill.formatText(idx, 1, layoutFormats(align, size), 'user');
        refreshImgBar();
        requestAnimationFrame(placeImgBar);
    };

    imgBar.addEventListener('mousedown', e => e.preventDefault());
    imgBar.addEventListener('click', e => {
        const b = e.target.closest('button');
        if (!b || !selImg) return;
        if (b.dataset.a) applyLayout({ align: b.dataset.a });
        else if (b.dataset.s) applyLayout({ size: parseInt(b.dataset.s, 10) });
        else if (b.dataset.do === 'crop') openCrop(selImg.currentSrc || selImg.src, { replace: selImg });
        else if (b.dataset.do === 'del') {
            const blot = Quill.find(selImg);
            if (blot) quill.deleteText(quill.getIndex(blot), 1, 'user');
            hideImgBar();
        }
    });

    // =====================================================================
    //  Potong gambar (Cropper.js) + sisipkan ke editor
    // =====================================================================
    const cropModal = $('crop-modal'), cropImg = $('crop-img');
    let cropper = null, cropCtx = {}, cropObjUrl = null;

    const setStatus = (msg, ms) => {
        $('img-status').textContent = msg || '';
        if (msg && ms) setTimeout(() => { if ($('img-status').textContent === msg) $('img-status').textContent = ''; }, ms);
    };
    const setRatio = r => {
        cropModal.querySelectorAll('[data-r]').forEach(b => b.classList.toggle('active', b.dataset.r === r));
        if (cropper) cropper.setAspectRatio(r === 'free' ? NaN : parseFloat(r));
    };

    function openCrop(src, ctx) {
        cropCtx = ctx || {};
        closeCrop(true);
        if (src instanceof Blob) { cropObjUrl = URL.createObjectURL(src); cropImg.src = cropObjUrl; }
        else { cropImg.src = src; }
        cropModal.classList.remove('hidden'); cropModal.classList.add('flex');
        cropper = new Cropper(cropImg, { viewMode: 1, dragMode: 'move', autoCropArea: 1, background: false, responsive: true, checkOrientation: true });
        cropModal.querySelectorAll('[data-r]').forEach(b => b.classList.toggle('active', b.dataset.r === 'free'));
    }
    function closeCrop(keepOpen) {
        if (cropper) { cropper.destroy(); cropper = null; }
        if (cropObjUrl) { URL.revokeObjectURL(cropObjUrl); cropObjUrl = null; }
        if (!keepOpen) { cropModal.classList.add('hidden'); cropModal.classList.remove('flex'); cropImg.removeAttribute('src'); }
    }

    cropModal.querySelectorAll('[data-r]').forEach(b => b.addEventListener('click', () => setRatio(b.dataset.r)));
    cropModal.querySelectorAll('[data-t]').forEach(b => b.addEventListener('click', () => {
        if (!cropper) return;
        const t = b.dataset.t;
        if (t === 'zoom-in') cropper.zoom(0.1);
        else if (t === 'zoom-out') cropper.zoom(-0.1);
        else if (t === 'rot-l') cropper.rotate(-90);
        else if (t === 'rot-r') cropper.rotate(90);
        else if (t === 'flip') cropper.scaleX(-(cropper.getData().scaleX || 1));
        else if (t === 'reset') { cropper.reset(); setRatio('free'); }
    }));
    $('crop-close').addEventListener('click', () => closeCrop());
    $('crop-cancel').addEventListener('click', () => closeCrop());

    $('crop-ok').addEventListener('click', () => {
        if (!cropper) return;
        const ctx = cropCtx;
        let canvas;
        try {
            canvas = cropper.getCroppedCanvas({ maxWidth: 1600, maxHeight: 1600, fillColor: '#fff', imageSmoothingQuality: 'high' });
            if (!canvas) throw new Error('Area potong tidak valid.');
            canvas.toBlob(blob => {
                if (!blob) { alert('Gagal memproses gambar.'); return; }
                closeCrop();
                insertImage(blob, ctx);
            }, 'image/jpeg', 0.88);
        } catch (err) {
            alert('Gambar tidak bisa dipotong: ' + (err.message || 'gambar berasal dari domain lain.'));
        }
    });

    const toDataURL = blob => new Promise((res, rej) => {
        const fr = new FileReader();
        fr.onload = () => res(fr.result); fr.onerror = () => rej(new Error('Gagal membaca gambar.'));
        fr.readAsDataURL(blob);
    });
    const uploadBlob = async blob => {
        const fd = new FormData();
        fd.append('gambar', new File([blob], 'gambar.jpg', { type: 'image/jpeg' }));
        const res = await fetch(uploadUrl, {
            method: 'POST', body: fd, credentials: 'same-origin',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });
        if (!res.ok) throw new Error('Server menolak unggahan (' + res.status + ').');
        const j = await res.json();
        if (!j.url) throw new Error('Respons server tidak valid.');
        return j.url;
    };

    async function insertImage(blob, ctx) {
        setStatus('Memproses gambar…');
        try {
            const url = uploadUrl ? await uploadBlob(blob) : await toDataURL(blob);
            const old = ctx.replace;
            let idx, keep = layoutFormats('center', 75);
            if (old && old.isConnected && Quill.find(old)) {
                idx = quill.getIndex(Quill.find(old));
                keep = {
                    'data-align': old.dataset.align || 'center',
                    'data-size': old.dataset.size || '75',
                    style: old.getAttribute('style') || buildStyle('center', 75),
                };
                quill.deleteText(idx, 1, 'user');
            } else {
                idx = typeof ctx.index === 'number' ? ctx.index : Math.max(0, quill.getLength() - 1);
            }
            quill.insertEmbed(idx, 'image', url, 'user');
            quill.formatText(idx, 1, keep, 'user');
            quill.setSelection(idx + 1, 0, 'silent');
            hideImgBar();
            setStatus('Gambar ditambahkan.', 2500);
        } catch (err) {
            setStatus('');
            alert('Gagal menambahkan gambar. ' + (err.message || ''));
        }
    }

    // ---- Pilih file / tempel / seret gambar -> selalu lewat jendela potong ----
    const fileInput = $('img-file');
    const rememberIndex = () => { const r = quill.getSelection(); return r ? r.index : Math.max(0, quill.getLength() - 1); };
    const handleFile = (f, index) => {
        if (!f || !f.type.startsWith('image/')) { alert('File harus berupa gambar.'); return; }
        if (f.size > 15 * 1024 * 1024) { alert('Ukuran gambar terlalu besar (maks. 15 MB).'); return; }
        openCrop(f, { index });
    };
    openPicker = () => { fileInput._index = rememberIndex(); fileInput.click(); };
    fileInput.addEventListener('change', e => {
        const f = e.target.files[0]; const idx = fileInput._index; e.target.value = '';
        if (f) handleFile(f, idx);
    });
    const imageFrom = dt => dt && [...(dt.files || [])].find(f => f.type.startsWith('image/'));
    quill.root.addEventListener('paste', e => {
        const f = imageFrom(e.clipboardData);
        if (f && !e.clipboardData.getData('text/plain')) { e.preventDefault(); e.stopPropagation(); handleFile(f, rememberIndex()); }
    }, true);
    quill.root.addEventListener('drop', e => {
        const f = imageFrom(e.dataTransfer);
        if (f) { e.preventDefault(); e.stopPropagation(); handleFile(f, rememberIndex()); }
    }, true);

    // ---- Penghitung karakter ----
    const bindCounter = (input, out, max) => {
        const upd = () => { out.textContent = input.value.length + '/' + max; };
        input.addEventListener('input', upd); upd();
    };
    bindCounter(judul, $('judul-count'), 255);
    bindCounter($('ringkasan-input'), $('ringkasan-count'), 300);

    // ---- Kirim form: cek dulu isian wajib ----
    const flag = (el, msg) => { el.focus(); el.scrollIntoView({ block: 'center', behavior: 'smooth' }); alert(msg); };
    form.addEventListener('submit', e => {
        if (!judul.value.trim()) { e.preventDefault(); return flag(judul, 'Judul berita belum diisi.'); }
        if (isEmpty()) {
            e.preventDefault();
            isiError.classList.remove('hidden');
            editorWrap.classList.add('has-error');
            editorWrap.scrollIntoView({ block: 'center', behavior: 'smooth' });
            quill.focus();
            return;
        }
        if (wordCount() > MAX_WORDS) { e.preventDefault(); return alert('Isi berita melebihi batas ' + fmt(MAX_WORDS) + ' kata.'); }
        if (!$('kategori-input').value) { e.preventDefault(); return flag($('kategori-input'), 'Pilih kategori terlebih dahulu.'); }
        if (!$('penulis-input').value.trim()) { e.preventDefault(); return flag($('penulis-input'), 'Nama penulis belum diisi.'); }
        isiInput.value = quill.root.innerHTML;
        dirty = false;
    });
    window.addEventListener('beforeunload', e => { if (dirty) { e.preventDefault(); e.returnValue = ''; } });

    // ---- Ctrl+S = simpan draft ----
    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
            e.preventDefault();
            $('status-input').value = 'draft';
            form.requestSubmit();
        }
    });

    // ---- Preview gambar utama ----
    function previewGambar(input) {
        const file = input.files[0];
        const preview = $('preview-gambar');
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    // ---- Slug otomatis ----
    const slugify = s => s.toLowerCase().trim().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
    let slugManual = slugInput.value !== '';
    slugInput.addEventListener('input', () => { slugManual = true; slugPreview.textContent = slugInput.value || '…'; });
    judul.addEventListener('input', () => {
        if (slugManual) return;
        slugInput.value = slugify(judul.value);
        slugPreview.textContent = slugInput.value || '…';
    });

    // ---- Pratinjau ----
    const modal = $('preview-modal');
    const closeModal = () => { modal.classList.add('hidden'); modal.classList.remove('flex'); };
    $('btn-preview').addEventListener('click', () => {
        $('pv-title').textContent = judul.value || 'Tanpa judul';
        $('pv-body').innerHTML = quill.root.innerHTML;
        $('pv-cat').textContent = $('kategori-input').value || 'Tanpa kategori';
        const tgl = $('published-at').value;
        const d = tgl ? new Date(tgl) : new Date();
        $('pv-meta').textContent =
            d.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) +
            ' • Oleh ' + ($('penulis-input').value || 'Admin');
        modal.classList.remove('hidden'); modal.classList.add('flex');
    });
    $('preview-close').addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', e => {
        if (e.key !== 'Escape') return;
        if (!cropModal.classList.contains('hidden')) closeCrop(); else closeModal();
    });
});
</script>