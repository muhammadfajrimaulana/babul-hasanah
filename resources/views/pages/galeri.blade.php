@extends('layouts.app')

@section('title', 'Galeri Pondok Pesantren Babul Hasanah')

@section('content')

@php
    /*
     * DATA ALBUM
     * Jika controller mengirim variabel $galeri, data itu yang dipakai.
     * Format satu album:
     * [
     *   'judul' => '...', 'kategori' => '...', 'tanggal' => '2026-06-20' (opsional),
     *   'deskripsi' => '...' (opsional), 'sampul' => URL (opsional, default foto pertama),
     *   'foto' => [ URL, atau ['gambar' => URL, 'keterangan' => '...' (opsional)] ],
     * ]
     * Selama belum ada, dipakai data CONTOH di bawah ini.
     */
    $img = asset('assets/img/banner/banner.png');

    $album = $galeri ?? [
        [
            'judul' => 'Wisuda Tahfidz & Haflah Akhirussanah', 'kategori' => 'Wisuda & Haflah', 'tanggal' => '2026-06-20',
            'deskripsi' => 'Momen haru dan bahagia pelepasan santri angkatan kelima bersama keluarga dan para asatidz.',
            'foto' => [$img, ['gambar' => $img, 'keterangan' => 'Penyerahan piagam oleh pengasuh'], $img, $img, ['gambar' => $img, 'keterangan' => 'Foto bersama wisudawan'], $img],
        ],
        [
            'judul' => 'Kajian Kitab Kuning Bersama Asatidz', 'kategori' => 'Kajian Kitab', 'tanggal' => '2026-08-12',
            'deskripsi' => 'Suasana bandongan dan sorogan santri di aula utama pondok pesantren.',
            'foto' => [$img, $img, $img, $img],
        ],
        [
            'judul' => 'Lomba Muhadharah 3 Bahasa', 'kategori' => 'Kegiatan Santri', 'tanggal' => '2026-08-20',
            'deskripsi' => 'Melatih mental dan retorika dakwah santri tingkat MA dalam bahasa Arab, Inggris, dan Indonesia.',
            'foto' => [$img, $img, ['gambar' => $img, 'keterangan' => 'Penampilan juara pertama'], $img, $img],
        ],
        [
            'judul' => 'Gedung Asrama Putra Tahap II', 'kategori' => 'Fasilitas Asrama', 'tanggal' => '2026-07-05',
            'deskripsi' => 'Asrama yang bersih, nyaman, dan kondusif untuk belajar.',
            'foto' => [$img, $img, $img],
        ],
        [
            'judul' => 'Penampilan Tim Hadroh', 'kategori' => 'Kegiatan Santri', 'tanggal' => '2026-08-20',
            'deskripsi' => 'Penyaluran bakat seni Islami santri pada acara Desa Manggis.',
            'foto' => [$img, $img, $img, $img],
        ],
        [
            'judul' => 'Setoran Hafalan Al-Qur’an', 'kategori' => 'Kajian Kitab', 'tanggal' => '2026-05-14',
            'deskripsi' => 'Program pembinaan hafalan intensif harian.',
            'foto' => [$img],
        ],
        [
            'judul' => 'Gotong Royong Bersama Santri', 'kategori' => 'Kegiatan Santri', 'tanggal' => '2026-04-02',
            'deskripsi' => 'Menumbuhkan kebersamaan dan kepedulian terhadap lingkungan pesantren.',
            'foto' => [$img, $img, $img],
        ],
    ];

    $albums = collect($album)->map(function ($a) use ($img) {
        $fotos = collect($a['foto'] ?? [])->map(fn ($f) => [
            'gambar'     => is_array($f) ? $f['gambar'] : $f,
            'keterangan' => is_array($f) ? ($f['keterangan'] ?? '') : '',
        ])->values()->all();

        return [
            'judul'     => $a['judul'],
            'kategori'  => $a['kategori'],
            'tanggal'   => ! empty($a['tanggal']) ? \Illuminate\Support\Carbon::parse($a['tanggal'])->locale('id')->translatedFormat('d F Y') : '',
            'deskripsi' => $a['deskripsi'] ?? '',
            'sampul'    => $a['sampul'] ?? ($fotos[0]['gambar'] ?? $img),
            'foto'      => $fotos,
        ];
    })->filter(fn ($a) => count($a['foto']) > 0)->values()->all();
@endphp

<style>
    /* ===== Halaman Galeri Album (memakai variabel warna dari layouts/app) ===== */
    .gl-hero { background: var(--cream); padding: 56px 0 64px; border-radius: 0 0 36px 36px; text-align: center; }
    .gl-eyebrow { font-size: 11px; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: var(--gold); }
    .gl-title { font-size: clamp(28px, 4vw, 40px); font-weight: 700; color: var(--green); line-height: 1.2; margin-top: 8px; }
    .gl-sub { font-size: 14px; color: var(--muted); max-width: 580px; margin: 10px auto 0; line-height: 1.7; }

    .gl-body { padding: 40px 0 72px; }

    .gl-filter { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; margin-bottom: 14px; }
    .gl-chip {
        font-family: inherit; font-size: 13px; font-weight: 500; padding: 9px 18px; border-radius: 999px; cursor: pointer;
        background: #fff; color: #4b5563; border: 1px solid var(--line); transition: all .2s ease;
    }
    .gl-chip:hover { border-color: var(--emerald); color: var(--emerald-d); }
    .gl-chip.on { background: var(--green); border-color: var(--green); color: #fff; font-weight: 600; }
    .gl-count { text-align: center; font-size: 12.5px; color: var(--muted); margin-bottom: 30px; }

    /* Kartu album */
    .gl-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; }
    .gl-card {
        background: #fff; border: 1px solid var(--line); border-radius: 22px; overflow: hidden; cursor: pointer;
        text-align: left; font-family: inherit; padding: 0; display: block; width: 100%;
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .gl-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }
    .gl-card:focus-visible { outline: 2px solid var(--emerald); outline-offset: 3px; }
    .gl-img { position: relative; aspect-ratio: 4 / 3; overflow: hidden; background: var(--cream); }
    .gl-img img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .5s ease; }
    .gl-card:hover .gl-img img { transform: scale(1.06); }
    .gl-badge {
        position: absolute; right: 14px; bottom: 14px; display: inline-flex; align-items: center; gap: 7px;
        background: rgba(23, 64, 47, .88); color: #fff; font-size: 11.5px; font-weight: 500;
        padding: 6px 12px; border-radius: 999px; backdrop-filter: blur(4px);
    }
    .gl-badge i { color: var(--emerald-l); font-size: 11px; }
    .gl-info { padding: 16px 20px 20px; }
    .gl-cat { font-size: 10.5px; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--gold); }
    .gl-date { font-size: 11.5px; color: var(--muted); font-weight: 400; letter-spacing: 0; text-transform: none; }
    .gl-h { font-size: 15px; font-weight: 600; color: var(--green); line-height: 1.4; margin: 6px 0; transition: color .2s ease; }
    .gl-card:hover .gl-h { color: var(--emerald-d); }
    .gl-p { font-size: 12.5px; color: var(--muted); line-height: 1.7; }
    .clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    .gl-more { text-align: center; margin-top: 40px; }
    .gl-more button {
        font-family: inherit; font-size: 13.5px; font-weight: 600; color: var(--green); background: #fff; cursor: pointer;
        border: 1px solid var(--line); padding: 12px 28px; border-radius: 12px; transition: all .25s ease;
    }
    .gl-more button:hover { background: var(--emerald); border-color: var(--emerald); color: #fff; transform: translateY(-2px); }
    .gl-empty { text-align: center; color: var(--muted); font-size: 14px; padding: 60px 0; }
    .gl-empty i { display: block; font-size: 32px; color: var(--line); margin-bottom: 12px; }

    /* Lightbox album */
    .gl-lb {
        position: fixed; inset: 0; z-index: 10000; background: rgba(15, 40, 30, .95);
        display: flex; align-items: center; justify-content: center; padding: 24px;
    }
    .gl-lb-box { max-width: 960px; width: 100%; max-height: 100%; overflow-y: auto; }
    .gl-stage { position: relative; }
    .gl-lb-img { width: 100%; height: min(58vh, 560px); object-fit: contain; border-radius: 18px; background: rgba(0, 0, 0, .25); display: block; }
    .gl-lb-cap { text-align: center; color: #cfe0d6; margin-top: 16px; }
    .gl-lb-cap .gl-cat { color: var(--emerald-l); }
    .gl-lb-cap h3 { color: #fff; font-size: 18px; font-weight: 600; margin: 6px 0; }
    .gl-lb-cap p { font-size: 13px; line-height: 1.7; max-width: 600px; margin: 0 auto; }
    .gl-lb-note { display: inline-block; margin-top: 10px; background: rgba(255, 255, 255, .1); color: #fff; font-size: 12.5px; padding: 6px 14px; border-radius: 999px; }
    .gl-lb-num { font-size: 11.5px; margin-top: 10px; opacity: .7; }

    .gl-thumbs { display: flex; gap: 8px; overflow-x: auto; padding: 14px 2px 4px; justify-content: safe center; }
    .gl-th {
        flex: none; width: 66px; height: 50px; border-radius: 10px; overflow: hidden; border: 2px solid transparent;
        padding: 0; cursor: pointer; opacity: .55; background: none; transition: all .2s ease;
    }
    .gl-th img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .gl-th:hover { opacity: .9; }
    .gl-th.on { border-color: var(--emerald-l); opacity: 1; }

    .gl-btn {
        position: absolute; width: 44px; height: 44px; border-radius: 50%; border: 0; cursor: pointer; color: #fff;
        background: rgba(255, 255, 255, .12); display: grid; place-items: center; font-size: 15px; transition: background .2s ease;
    }
    .gl-btn:hover { background: var(--emerald); }
    .gl-close { top: 20px; right: 20px; z-index: 2; }
    .gl-prev { left: 20px; top: 50%; transform: translateY(-50%); }
    .gl-next { right: 20px; top: 50%; transform: translateY(-50%); }

    @media (max-width: 1024px) { .gl-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 640px) {
        .gl-hero { padding: 40px 0 48px; border-radius: 0 0 28px 28px; }
        .gl-body { padding: 30px 0 56px; }
        .gl-grid { grid-template-columns: 1fr; }
        .gl-lb { padding: 64px 14px 14px; align-items: flex-start; }
        .gl-lb-img { height: 42vh; }
        .gl-prev { left: 8px; } .gl-next { right: 8px; }
        .gl-prev, .gl-next { width: 38px; height: 38px; }
    }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('galeri', (albums) => ({
            albums,
            aktif: 'Semua',
            batas: 6,
            langkah: 6,
            a: null,   // indeks album yang dibuka (pada daftar tersaring)
            f: 0,      // indeks foto di dalam album

            get kategori() { return ['Semua', ...new Set(this.albums.map(x => x.kategori))]; },
            get tersaring() { return this.aktif === 'Semua' ? this.albums : this.albums.filter(x => x.kategori === this.aktif); },
            get tampil() { return this.tersaring.slice(0, this.batas); },
            get sisa() { return this.tersaring.length - this.batas; },
            get album() { return this.a === null ? null : this.tersaring[this.a]; },
            get foto() { return this.album ? this.album.foto[this.f] : null; },
            get jumlah() { return this.album ? this.album.foto.length : 0; },

            pilih(k) { this.aktif = k; this.batas = this.langkah; },
            lagi() { this.batas += this.langkah; },

            buka(i) { this.a = i; this.f = 0; document.body.style.overflow = 'hidden'; },
            tutup() { this.a = null; document.body.style.overflow = ''; },
            ke(i) {
                this.f = i;
                this.$nextTick(() => document.getElementById('gl-th-' + i)?.scrollIntoView({ inline: 'center', block: 'nearest', behavior: 'smooth' }));
            },
            maju() { if (this.jumlah > 1) this.ke((this.f + 1) % this.jumlah); },
            mundur() { if (this.jumlah > 1) this.ke((this.f - 1 + this.jumlah) % this.jumlah); },
        }));
    });
</script>

{{-- ===================== HERO ===================== --}}
<section class="gl-hero">
    <div class="site-container">
        <span class="gl-eyebrow">Dokumentasi Visual</span>
        <h1 class="gl-title">Galeri Kegiatan &amp; Fasilitas Pesantren</h1>
        <p class="gl-sub">Album dokumentasi momen kebersamaan, kegiatan belajar mengajar, ekstrakurikuler, serta suasana asrama di Pondok Pesantren Babul Hasanah.</p>
    </div>
</section>

{{-- ===================== ISI ===================== --}}
<section class="gl-body">
    <div class="site-container"
         x-data="galeri(@js($albums))"
         @keydown.window.escape="tutup()"
         @keydown.window.arrow-right="album && maju()"
         @keydown.window.arrow-left="album && mundur()">

        {{-- Filter kategori --}}
        <div class="gl-filter">
            <template x-for="k in kategori" :key="k">
                <button type="button" class="gl-chip" :class="{ 'on': aktif === k }" @click="pilih(k)" x-text="k"></button>
            </template>
        </div>
        <p class="gl-count">
            Menampilkan <b x-text="tampil.length"></b> dari <b x-text="tersaring.length"></b> album
        </p>

        {{-- Grid album --}}
        <div class="gl-grid">
            <template x-for="(item, i) in tampil" :key="item.judul + i">
                <button type="button" class="gl-card" @click="buka(i)"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="gl-img">
                        <img :src="item.sampul" :alt="item.judul" loading="lazy">
                        <span class="gl-badge"><i class="fa-regular fa-images"></i><span x-text="item.foto.length + ' foto'"></span></span>
                    </div>
                    <div class="gl-info">
                        <span class="gl-cat">
                            <span x-text="item.kategori"></span>
                            <span class="gl-date" x-show="item.tanggal" x-text="'• ' + item.tanggal"></span>
                        </span>
                        <h3 class="gl-h" x-text="item.judul"></h3>
                        <p class="gl-p clamp-2" x-text="item.deskripsi"></p>
                    </div>
                </button>
            </template>
        </div>

        {{-- Kosong --}}
        <div class="gl-empty" x-show="tersaring.length === 0" x-cloak>
            <i class="fa-regular fa-images"></i>
            Belum ada album pada kategori ini.
        </div>

        {{-- Muat lebih banyak --}}
        <div class="gl-more" x-show="sisa > 0" x-cloak>
            <button type="button" @click="lagi()">
                Muat Lebih Banyak Album <span x-text="'(' + sisa + ' lagi)'"></span>
            </button>
        </div>

        {{-- Lightbox album --}}
        <div class="gl-lb" x-show="album" x-cloak @click.self="tutup()"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             role="dialog" aria-modal="true" aria-label="Album foto">

            <button type="button" class="gl-btn gl-close" @click="tutup()" aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>

            <div class="gl-lb-box" x-show="album" @click.self="tutup()">
                <div class="gl-stage">
                    <img class="gl-lb-img" :src="foto && foto.gambar" :alt="album && album.judul">
                    <button type="button" class="gl-btn gl-prev" x-show="jumlah > 1" @click="mundur()" aria-label="Foto sebelumnya"><i class="fa-solid fa-chevron-left"></i></button>
                    <button type="button" class="gl-btn gl-next" x-show="jumlah > 1" @click="maju()" aria-label="Foto berikutnya"><i class="fa-solid fa-chevron-right"></i></button>
                </div>

                <div class="gl-lb-cap">
                    <span class="gl-cat">
                        <span x-text="album && album.kategori"></span>
                        <span class="gl-date" style="color:#a9c4b6" x-show="album && album.tanggal" x-text="'• ' + (album ? album.tanggal : '')"></span>
                    </span>
                    <h3 x-text="album && album.judul"></h3>
                    <p x-show="album && album.deskripsi" x-text="album && album.deskripsi"></p>
                    <span class="gl-lb-note" x-show="foto && foto.keterangan" x-text="foto && foto.keterangan"></span>
                    <div class="gl-lb-num" x-text="(f + 1) + ' / ' + jumlah"></div>
                </div>

                <div class="gl-thumbs" x-show="jumlah > 1">
                    <template x-for="(t, ti) in (album ? album.foto : [])" :key="ti">
                        <button type="button" class="gl-th" :id="'gl-th-' + ti" :class="{ 'on': f === ti }" @click="ke(ti)" :aria-label="'Foto ' + (ti + 1)">
                            <img :src="t.gambar" alt="" loading="lazy">
                        </button>
                    </template>
                </div>
            </div>
        </div>

    </div>
</section>

@endsection