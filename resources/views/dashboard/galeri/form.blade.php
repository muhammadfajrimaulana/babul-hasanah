@extends('layouts.admin')

@section('title', 'Tambah Album Galeri - Pondok Pesantren Babul Hasanah')

@section('content')
    @php
        $card = 'bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm p-6 space-y-4';
        $input = 'w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500';
        $label = 'block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1.5';
        $file = 'block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:text-emerald-700 file:px-3 file:py-2 file:text-xs file:font-bold hover:file:bg-emerald-100 dark:file:bg-emerald-950/50 dark:file:text-emerald-400';
        $note = 'text-[11px] text-slate-400 dark:text-slate-500 leading-relaxed';
    @endphp

    <div class="space-y-6 max-w-4xl">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Tambah Album Galeri</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Satu album bisa berisi foto, video yang diunggah, dan
                    link video dari YouTube, TikTok, atau Instagram.</p>
            </div>
            <a href="{{ route('admin.galeri.index') }}"
                class="text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white whitespace-nowrap pt-1">Kembali</a>
        </div>

        @if ($errors->any())
            <div
                class="bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 rounded-2xl px-5 py-4">
                <p class="text-xs font-black text-rose-700 dark:text-rose-400">Album belum tersimpan. Periksa isian berikut:
                </p>
                <ul class="mt-2 ml-4 list-disc text-xs text-rose-700 dark:text-rose-400 space-y-0.5">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.galeri.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- ========== Informasi album ========== --}}
            <div class="{{ $card }}">
                <h3 class="text-sm font-black text-slate-900 dark:text-white">Informasi Album</h3>

                <div>
                    <label for="judul" class="{{ $label }}">Judul album</label>
                    <input type="text" id="judul" name="judul" value="{{ old('judul') }}" maxlength="150" required
                        class="{{ $input }}">
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="kategori" class="{{ $label }}">Kategori</label>
                        <input type="text" id="kategori" name="kategori" value="{{ old('kategori') }}" list="daftar-kategori"
                            maxlength="60" required class="{{ $input }}" placeholder="Contoh: Kegiatan Santri">
                        <datalist id="daftar-kategori">
                            @foreach ($kategori ?? [] as $k)
                                <option value="{{ $k }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label for="tanggal" class="{{ $label }}">Tanggal kegiatan</label>
                        <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal') }}" class="{{ $input }}">
                    </div>
                </div>

                <div>
                    <label for="deskripsi" class="{{ $label }}">Deskripsi singkat</label>
                    <textarea id="deskripsi" name="deskripsi" rows="3" maxlength="300" class="{{ $input }}">{{ old('deskripsi') }}</textarea>
                </div>

                <div>
                    <label for="sampul" class="{{ $label }}">Gambar sampul (opsional)</label>
                    <input type="file" id="sampul" name="sampul" accept="image/*" class="{{ $file }}">
                    <p class="{{ $note }} mt-1.5">Jika kosong, sampul diambil dari foto pertama, atau dari gambar sampul
                        video.</p>
                </div>
            </div>

            {{-- ========== Foto ========== --}}
            <div class="{{ $card }}">
                <h3 class="text-sm font-black text-slate-900 dark:text-white">Foto</h3>
                <div>
                    <label for="foto" class="{{ $label }}">Pilih satu atau beberapa foto</label>
                    <input type="file" id="foto" name="foto[]" accept="image/*" multiple class="{{ $file }}">
                    <p class="{{ $note }} mt-1.5">Maksimal 4 MB per foto.</p>
                </div>
                <div id="foto-preview" class="flex flex-wrap gap-2"></div>
            </div>

            {{-- ========== Video ========== --}}
            <div class="{{ $card }}">
                <h3 class="text-sm font-black text-slate-900 dark:text-white">Video</h3>

                <div id="video-rows" class="space-y-3"></div>
                <p id="video-empty" class="{{ $note }}">Belum ada video. Tambahkan lewat tombol di bawah.</p>

                <div class="flex flex-wrap gap-2 pt-1">
                    <button type="button" data-add="upload"
                        class="text-xs font-bold px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:border-emerald-500 hover:text-emerald-600 transition flex items-center gap-2">
                        <i class="fa-solid fa-upload"></i> Unggah video
                    </button>
                    <button type="button" data-add="link"
                        class="text-xs font-bold px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:border-emerald-500 hover:text-emerald-600 transition flex items-center gap-2">
                        <i class="fa-solid fa-link"></i> Tambah link YouTube / TikTok / Instagram
                    </button>
                </div>
                <p class="{{ $note }}">Video unggahan maksimal 50 MB. Untuk video yang lebih besar, unggah ke YouTube lalu
                    tempel linknya di sini.</p>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-6 py-3 rounded-xl transition shadow-sm">Simpan
                    Album</button>
                <a href="{{ route('admin.galeri.index') }}"
                    class="text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white">Batal</a>
            </div>
        </form>
    </div>

    {{-- ===== Templat baris video (dikloning oleh skrip di bawah) ===== --}}
    <template id="tpl-upload">
        <div data-row
            class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/40 p-4 space-y-3">
            <div class="flex items-center justify-between">
                <span
                    class="px-2 py-0.5 bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400 rounded text-[9px] font-bold"><i
                        class="fa-solid fa-upload"></i> Unggah video</span>
                <button type="button" data-del aria-label="Hapus video ini"
                    class="text-slate-400 hover:text-rose-600 text-xs"><i class="fa-solid fa-trash"></i></button>
            </div>
            <input type="hidden" name="media[__i__][tipe]" value="upload">
            <div>
                <label class="{{ $label }}">File video (mp4, webm, mov)</label>
                <input type="file" name="media[__i__][file]" accept="video/mp4,video/webm,video/quicktime" required
                    class="{{ $file }}">
            </div>
            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <label class="{{ $label }}">Gambar sampul video (opsional)</label>
                    <input type="file" name="media[__i__][poster]" accept="image/*" class="{{ $file }}">
                </div>
                <div>
                    <label class="{{ $label }}">Keterangan (opsional)</label>
                    <input type="text" name="media[__i__][keterangan]" maxlength="120" class="{{ $input }}">
                </div>
            </div>
        </div>
    </template>

    <template id="tpl-link">
        <div data-row
            class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/40 p-4 space-y-3">
            <div class="flex items-center justify-between">
                <span
                    class="px-2 py-0.5 bg-sky-50 text-sky-600 dark:bg-sky-950/50 dark:text-sky-400 rounded text-[9px] font-bold"><i
                        class="fa-solid fa-link"></i> Link medsos</span>
                <button type="button" data-del aria-label="Hapus video ini"
                    class="text-slate-400 hover:text-rose-600 text-xs"><i class="fa-solid fa-trash"></i></button>
            </div>
            <input type="hidden" name="media[__i__][tipe]" value="link">
            <div>
                <label class="{{ $label }}">Link video</label>
                <input type="url" name="media[__i__][url]" data-url required placeholder="https://www.youtube.com/watch?v=…"
                    class="{{ $input }}">
                <p data-hint class="hidden text-[11px] font-bold mt-1.5"></p>
            </div>
            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <label class="{{ $label }}">Gambar sampul video (opsional)</label>
                    <input type="file" name="media[__i__][poster]" accept="image/*" class="{{ $file }}">
                    <p class="{{ $note }} mt-1.5">YouTube otomatis memakai gambar bawaannya. TikTok dan Instagram
                        tidak punya, jadi sebaiknya unggah sampul.</p>
                </div>
                <div>
                    <label class="{{ $label }}">Keterangan (opsional)</label>
                    <input type="text" name="media[__i__][keterangan]" maxlength="120" class="{{ $input }}">
                </div>
            </div>
        </div>
    </template>

    <script>
        (function () {
            const rows = document.getElementById('video-rows');
            const empty = document.getElementById('video-empty');
            let n = 0;

            // ----- tambah / hapus baris video -----
            document.querySelectorAll('[data-add]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const html = document.getElementById('tpl-' + btn.dataset.add).innerHTML.replaceAll('__i__', n++);
                    rows.insertAdjacentHTML('beforeend', html);
                    empty.classList.add('hidden');
                });
            });

            rows.addEventListener('click', e => {
                const del = e.target.closest('[data-del]');
                if (!del) return;
                del.closest('[data-row]').remove();
                if (!rows.children.length) empty.classList.remove('hidden');
            });

            // ----- deteksi platform dari link -----
            function deteksi(u) {
                u = (u || '').trim();
                if (!u) return null;
                if (/(youtu\.be\/|youtube\.com\/(watch\?|shorts\/|embed\/|live\/))/i.test(u)) return ['ok', 'YouTube terdeteksi'];
                if (/tiktok\.com\/.*\/video\/\d+/i.test(u)) return ['ok', 'TikTok terdeteksi'];
                if (/(vm|vt)\.tiktok\.com|tiktok\.com\/t\//i.test(u)) return ['warn', 'Link pendek TikTok tidak bisa diputar di halaman. Buka link di browser, lalu salin alamat lengkapnya (…/video/angka).'];
                if (/instagram\.com\/(p|reel|reels|tv)\//i.test(u)) return ['ok', 'Instagram terdeteksi'];
                return ['bad', 'Link belum dikenali. Pengunjung hanya akan melihat tombol “Buka video”.'];
            }

            const warna = {
                ok: 'text-emerald-600 dark:text-emerald-400',
                warn: 'text-amber-600 dark:text-amber-400',
                bad: 'text-rose-600 dark:text-rose-400',
            };

            rows.addEventListener('input', e => {
                if (!e.target.matches('[data-url]')) return;
                const hint = e.target.closest('[data-row]').querySelector('[data-hint]');
                const r = deteksi(e.target.value);
                hint.className = 'text-[11px] font-bold mt-1.5 ' + (r ? warna[r[0]] : 'hidden');
                hint.textContent = r ? r[1] : '';
            });

            // ----- pratinjau foto -----
            const foto = document.getElementById('foto');
            const prev = document.getElementById('foto-preview');
            let urls = [];

            foto.addEventListener('change', () => {
                urls.forEach(u => URL.revokeObjectURL(u));
                prev.innerHTML = '';
                urls = [...foto.files].map(f => URL.createObjectURL(f));
                urls.forEach(u => {
                    const img = document.createElement('img');
                    img.src = u;
                    img.alt = '';
                    img.className = 'w-20 h-16 object-cover rounded-xl border border-slate-200 dark:border-slate-700';
                    prev.appendChild(img);
                });
            });
        })();
    </script>
@endsection
