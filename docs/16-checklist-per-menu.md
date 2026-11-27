# 16 — Checklist Perbaikan per Menu (Bekerja ke Depan)

Dokumen ini dimaksudkan sebagai **daftar kerja siap pakai** untuk sesi berikutnya.
Tujuannya: tidak perlu mengulang audit dari nol; tinggal pilih menu, lalu kerjakan item yang
ditandai.

- **Disusun:** 27 November 2026
- **Sumber:** pembacaan kode, `docs/07-fitur.md`, `docs/09-audit-dan-rekomendasi.md`,
  `docs/12-halaman-per-role.md`, `docs/14-modul-akademik.md`, dan perubahan terakhir.
- **Catatan status:** `docs/07-fitur.md` masih berisi beberapa status lama (misalnya "Kegiatan ❌",
  "import stub"). **Dokumen ini yang lebih baru** untuk item yang disebut di sini.
- **Aturan:** agen AI hanya boleh menulis kode/migrasi. Menjalankan `migrate`, `tinker`, `test`,
  atau query database tetap dilakukan manusia. Lihat [ATURAN.md](ATURAN.md).

## Legend

| Tanda | Arti |
|:--:|---|
| ✅ | Berjalan end-to-end |
| ⚠️ | Berjalan, tapi ada yang kurang/perlu dirapikan |
| 🚧 | Belum dibuat / masih rencana |
| ❌ | Rusak, tidak ada, atau sengaja dinonaktifkan |

---

## 0. Ringkasan yang baru selesai (27 Nov 2026)

| Item | Status | Catatan |
|---|---|---|
| `/kegiatan` error 500 | ✅ | Dialihkan ke `/info-harian`, nama route `kegiatan` dipertahankan |
| `admin.kegiatan.index`, `super.settings` | ✅ | Ternyata sudah ada view/method; audit lama usang |
| Import santri | ✅ | CSV native via `App\Support\ImportSantri`, template CSV dinamis |
| Policy | ✅ sebagian | `SantriPolicy` + `NilaiPolicy` dipakai di modul santri & nilai |
| Email PPDB | ✅ | Email wali boleh multi-anak; `email_santri` unik via migrasi |
| Anti-spam PPDB | ✅ | `throttle:5,10`, honeypot, halaman 429 |
| Kelulusan & akun alumni | ✅ | Lulus massal, buat akun massal, reset password + email 24 jam |
| Migrasi | ✅ | Pengguna melaporkan sudah menjalankan `php artisan migrate` |
| Menu mati `Program` & `Kegiatan` admin | ✅ | `Program` tetap alias redirect; `Kegiatan` diisi dari Info Harian + catatan kalender + absensi santri (28 Nov 2026) |
| Scoping prestasi & widget gender | ✅ | Prestasi sudah difilter per jenjang; `santri.jenis_kelamin` ditambahkan (migrasi 28 Nov 2026) dan diisi dari PPDB/form/import (28 Nov 2026) |
| Audit log aksi sensitif | ✅ | Import CSV, lulus massal, buat akun massal, reset password, dan perubahan peran tercatat di `log_aktivitas` (28 Nov 2026) |
| Perluasan policy per-record | ✅ | `PembinaanPolicy` (hafalan/pelanggaran/absensi), `IzinPolicy`, `LegalisirPolicy`, dan `PortalPolicy` dipasang aditif di admin & portal (28 Nov 2026) |
| Error 500 `/admin/pembinaan` | ✅ | `translatedFormat()` dipanggil pada string; `Absensi.tanggal` dan `Alumni.nilai_diunggah_pada` kini di-cast ke Carbon (28 Nov 2026) |
| Import nilai massal CSV | ✅ | `App\Support\ImportNilai` + template + pratinjau error + audit log `import_nilai_csv` (28 Nov 2026) |
| Jadwal, kurikulum, rapor, buka nilai, Google Login | 🚧 kode siap | Keputusan produk item #6 sudah diimplementasikan di kode: CRUD jadwal (anti-bentrok), CRUD kurikulum + item + terbit, tabel `rapor_santri` (rata-rata/peringkat/catatan wali kelas), buka kembali nilai `published` dengan alasan wajib, dan OAuth Google. **Menunggu migrasi + kredensial + `composer install` oleh manusia.** Lihat [17-google-oauth.md](17-google-oauth.md). |

---

## 1. Website Publik

| Menu | Kondisi | Yang kurang / rekomendasi | Prioritas |
|---|---|---|---|
| Beranda | ✅ | Query DB sudah dipindah dari Blade ke `Public\HomeController@index` (28 Nov 2026); Blade hanya menampilkan variabel. Guard `Schema::hasTable` tetap dipertahankan. Statistik memakai data nyata. | — |
| Profil (Sejarah, Visi-Misi, Pembelajaran, Fasilitas) | ✅ | Konten sudah lewat tabel `konten` (bisa diedit Superadmin). Query dipindah dari Blade ke `Public\ProfilController` (28 Nov 2026). Sejarah & fasilitas sudah punya placeholder bila foto belum diunggah. | — |
| Struktur Organisasi | ✅ | Publik + CRUD admin. Foto kini divalidasi di `StrukturOrganisasi::foto_url`; berkas hilang ⇒ ikon ditampilkan, bukan `<img>` rusak (28 Nov 2026). Kartu Kepengurusan di `/profil` memakai tabel tersendiri dan kini punya CRUD admin (`admin.kepengurusan.*`). | — |
| 7 Halaman Jenjang | ✅ sebagian | Konten dipusatkan di `App\Support\DataJenjang` dan disajikan lewat `Public\JenjangController@show` (28 Nov 2026). Bug copy-paste `pdf-wustha`/`mahad-aly` yang menampilkan konten PPS Wustha sudah diperbaiki; tautan "jenjang lainnya" kini memakai `route('jenjang.show')`. Banner & foto ketua melewati `App\Support\GambarPublik`; 14 placeholder sudah tersedia di `public/images/program/` dan `public/images/pengurus/`, fallback bawaan tetap aktif. Masih perlu: nama ketua & foto resmi pesantren menggantikan placeholder, dan bila ingin diedit admin, sambungkan ke tabel `konten`. | P3 |
| Berita | ✅ | `Berita::gambar_url` kini memvalidasi berkas `storage` lewat `GambarPublik`; bila hilang, fallback ke gambar di dalam isi lalu banner bawaan (28 Nov 2026). Permalink/slug bersifat **permanen**: dibuat otomatis sekali saat berita dibuat, tidak ikut berubah saat judul diedit (form edit readonly). Tautan lama tetap berfungsi, jadi tidak perlu redirect 301. Dijaga `tests/Feature/SlugBeritaTest.php`. | — |
| Galeri | ✅ | CRUD create/delete berjalan. Upload sudah memakai `Storage::disk('public')`; pembacaan path disatukan lewat `GambarPublik::dariStorage()` (menerima path baru `galeri/...`, `storage/`, URL `/storage/...`, dan path publik lama `upload/galeri/...`), sehingga admin & beranda memakai resolver yang sama. Penghapusan album lewat `GambarPublik::hapus()` hanya menyentuh berkas di disk `public`; path legacy sengaja dibiarkan. Dijaga `PathGambarStorageTest` dan `GaleriStorageTest`. | — |
| Prestasi | ✅ | Tampil publik (_global_ by design). Data sudah punya kolom `jenjang`; panel admin dan dashboard jenjang sudah memfilternya. | P2 |
| Alumni (publik) | ✅ | `Alumni::foto_url` kini mengembalikan `null` bila berkas tidak ada, sehingga kartu menampilkan inisial (28 Nov 2026). Filter kegiatan & instansi berjalan. Daftar kegiatan masih konstanta statis. | P3 |
| Info Harian | ✅ | CRUD Superadmin + tampil di beranda. Cek hasil migrasi tabel bila ada keraguan. | P1 |
| PPDB | ✅ | Email wali + email santri, verifikasi, akun wali, email kredensial, throttle, honeypot. Perlu pastikan mailer SMTP produksi; fallback reset password sudah ada. | P2 |
| Kontak | ✅ | Halaman statis. | P3 |
| Kegiatan (publik) | ✅ | Sekarang redirect ke `/info-harian`. Buat halaman khusus hanya jika ingin agenda terpisah dari info harian. | P3 |

---

## 2. Autentikasi & Akun

| Menu | Kondisi | Yang kurang / rekomendasi | Prioritas |
|---|---|---|---|
| Login | ✅ | 6 role, redirect sesuai `Akses::DASHBOARD_ROUTE`, status aktif dicek. | — |
| Lupa kata sandi | ✅ | Self-service lewat email. Pastikan mailer produksi aktif. | P2 |
| Ganti password wajib | ✅ | Password sementara 24 jam tidak bisa dilewati. | — |
| Register publik | ❌ | **Sengaja tidak ada.** Akun dibuat PPDB/admin. Jangan ditambahkan tanpa kebijakan. | — |
| Google Login | 🚧 kode siap | Socialite terpasang di kode; tombol muncul otomatis setelah `GOOGLE_CLIENT_ID`/`GOOGLE_CLIENT_SECRET` diisi. Akun Google harus sudah terdaftar & aktif (tanpa registrasi otomatis). Butuh `composer install` + env oleh manusia — lihat [17-google-oauth.md](17-google-oauth.md). | P2 |
| Profil saya | ✅ | Nama, email, password, avatar. Role tidak bisa diubah di sini (by design). | — |
| Pengguna (Superadmin) | ✅ | CRUD + normalisasi role + jaga "superadmin terakhir". Perluas log untuk perubahan role. | P2 |
| Pengaturan sistem | ✅ | Hanya informasi aplikasi dari `config()`; `app.name` kini "Pondok Pesantren Babul Hasanah" (28 Nov 2026). Perubahan `.env` sengaja lewat server. | — |

---

## 3. Panel Admin

| Menu | Kondisi | Yang kurang / rekomendasi | Prioritas |
|---|---|---|---|
| Dashboard | ✅ | Widget gender dihitung dari `santri.jenis_kelamin` lewat `DashboardController::komposisiJenisKelamin()`. Banyak query di Blade; pindahkan ke controller/view composer. | P2 |
| Dashboard Jenjang | ⚠️ | Ringkasan prestasi belum discoping per jenjang. | P2 |
| Pendaftaran | ✅ | Verifikasi → santri + akun wali + email. Email wali kini multi-anak. Pastikan migrasi `drop_unique_email` aktif. | P1 |
| Data Santri | ✅ | CRUD, naik kelas + rollback, pindah jenjang, arsip, lulus tunggal/massal, import CSV, detail, pencarian `?q=` (nama, No. Induk, NISN, kelas, wali, email/akun user, data PPDB) yang bergabung dengan filter `?jenjang=` dan pembatasan akses Admin Jenjang. Hapus kini soft delete: data masuk `admin.santri.terhapus`, dapat dipulihkan Superadmin lewat `admin.santri.pulihkan`; status PPDB tidak diubah saat hapus. Tidak ada tombol hapus permanen/force delete di UI. Nomor induk otomatis kini aman dari tabrakan dengan santri yang sudah dihapus lunak (`KodeUnik`). XLSX belum didukung. | — |
| Data Alumni | ✅ | Tarik santri lulus, buat akun massal, reset password, detail, kartu kredensial. Perlu catatan audit untuk reset password & pembuatan akun massal. | P2 |
| Akademik — Mata Pelajaran | ✅ | CRUD + guru pengampu jadi sumber untuk jadwal & item kurikulum. | — |
| Akademik — Nilai | ✅ | Input `draft` → `published`, transkrip, cetak/unggah rapor, import nilai massal CSV via `App\Support\ImportNilai` + template dinamis. Nilai `published` kini bisa dibuka kembali jadi `draft` oleh Superadmin/Admin Jenjang lewat form alasan wajib; aksi `nilai_dibuka_kembali` tercatat di log. Portal wali hanya menampilkan nilai `published` (termasuk tab Detail Anak). | P1 |
| Akademik — Transkrip | ✅ | Cetak per santri. Perlu ekspor PDF resmi bila dibutuhkan. | P2 |
| Akademik — Jadwal Pelajaran | 🚧 kode siap | CRUD per jenjang/kelas/tahun/semester, validasi bentrok jam kelas & guru, audit `jadwal_*`. Tabel `jadwal_pelajaran` menunggu migrasi manusia. | P1 |
| Akademik — Kurikulum | 🚧 kode siap | CRUD kurikulum + item (mapel, KKM, target KD, urutan), status `draft` → `published` lewat `akademik.terbit`, audit `kurikulum_*`. Tabel `kurikulum`/`kurikulum_item` menunggu migrasi. | P1 |
| Akademik — Rapor Santri | 🚧 kode siap | Tabel `rapor_santri`: rata-rata & peringkat dihitung otomatis dari nilai `published` via `App\Support\Rapor::hitungUlang()`, catatan wali kelas diisi manual, terbit satuan/massal, tampil di portal wali. Menunggu migrasi. | P1 |
| Pembinaan | ✅ | Hafalan, pelanggaran, presensi kegiatan. Sudah melewati `PembinaanPolicy` + pengecekan manual. `tanggal` absensi di-cast agar tidak error 500 di Blade. | P2 |
| Izin Pulang | ✅ | Status + kode + auto terlambat. Sudah melewati `IzinPolicy` + pengecekan manual. | P2 |
| Pengumuman | ✅ | Target per jenjang; Pengurus menulis semua jenjang (by design). Pastikan sesuai kebijakan. | P2 |
| Kelas & Guru | ✅ | Master kelas, guru tanpa akun login, rekap wali kelas. | — |
| Pengajuan Perubahan | ✅ | Antrean dari wali + verifikasi. Endpoint portal memakai `PortalPolicy`. | P2 |
| Legalisir | ✅ | Antrean alumni diproses lewat `LegalisirPolicy` + pengecekan jenjang lama. | P2 |
| Prestasi | ✅ | CRUD + kolom `jenjang`. Admin Jenjang terkunci pada jenjangnya; role global bisa memilih satu jenjang atau melihat agregat. | P2 |
| Jenjang | ✅ | 7 submenu, detail per jenjang. Konten publik sebagian statis (sumber: `App\Support\DataJenjang`), gambar sudah punya fallback lewat `App\Support\GambarPublik`. | P3 |
| Berita & Galeri | ✅ | Validasi berkas sudah terpusat di `GambarPublik` (28 Nov 2026). Penyimpanan file masih perlu diseragamkan. | P2 |
| Konten / Struktur / Info Harian | ✅ | Khusus Superadmin. Lengkapi aset gambar publik. | P2 |
| Kepengurusan (`/profil`) | ✅ | CRUD Superadmin di `admin.kepengurusan.*` (nama, jabatan, urutan, foto). Query dipindah ke `ProfilController@index`; foto lama di `public/upload/kepengurusan/` tetap terbaca lewat accessor `foto_url`, foto baru masuk disk `public`. Tidak menggantikan menu Struktur (bagan lengkap tetap terpisah). | — |
| Log Aktivitas | ✅ | Mencatat import CSV (sukses/gagal), lulus massal, buat akun alumni massal, reset password, perubahan role/jenjang pengguna, `hapus_santri`/`pulihkan_santri`, dan `kepengurusan_*`. | P2 |
| Program | ✅ | Route `admin.program.index` sengaja dipertahankan sebagai alias/redirect ke `admin.jenjang.index` (keputusan: tidak dibuat CRUD). Tidak ada menu sidebar. | P3 |
| Kegiatan (admin) | ✅ | Halaman monitoring baca-saja: jadwal aktif dari Info Harian, catatan kalender pribadi, dan absensi santri sesuai batas jenjang. Tidak ada tabel `kegiatan` baru; tidak ada menu sidebar. | P3 |

---

## 4. Portal Wali Santri

| Menu | Kondisi | Yang kurang / rekomendasi | Prioritas |
|---|---|---|---|
| Beranda | ✅ | Multi-anak + indikator notifikasi nilai. Endpoint per-record kini memakai `PortalPolicy`. | P2 |
| Nilai Santri | ✅ | Halaman `/portal/nilai` dan tab **Nilai** pada Detail Anak sama-sama hanya menampilkan nilai `published`. Feature test scoping "anak sendiri" tersedia di `tests/Feature/PortalKepemilikanAnakTest.php` (dijalankan manusia). | P1 |
| Prestasi | ✅ | Per anak. | — |
| Pengumuman | ✅ | Per jenjang. | — |
| Izin Pulang | ✅ | Ajukan + lihat status. | — |
| Ajukan Perubahan | ✅ | Form + riwayat. | — |
| Dokumen | ✅ | Unduh dokumen anak. | — |
| Mode Riwayat | ✅ | Anak lulus tetap bisa dilihat wali. | — |
| Jadwal & Kurikulum | 🚧 kode siap | Portal akademik kini menampilkan jadwal jenjang/kelas anak, kurikulum `published`, dan rapor `published`. Menunggu migrasi tabel baru agar data bisa diisi. | P2 |

---

## 5. Portal Alumni

| Menu | Kondisi | Yang kurang / rekomendasi | Prioritas |
|---|---|---|---|
| Beranda | ✅ | Profil, nomor ijazah, kelas/kamar terakhir, riwayat terkunci. | — |
| Profil | ✅ | Bisa diubah alumni. Email dipakai sebagai username akun. | — |
| Nilai & Riwayat | ✅ | Riwayat nilai, pelanggaran, presensi. | — |
| Dokumen | ✅ | Arsip dokumen. | — |
| Tracer Study | ✅ | Kegiatan/instansi/kontak; memperbarui profil. | — |
| Legalisir | ✅ | Pengajuan + antrean. | — |
| Pengumuman | ✅ | Per jenjang. | — |
| Akun Login | ✅ | Dibuat saat lulus, email = `email_santri`, password 24 jam, reset oleh admin. Jika email kredensial gagal, admin harus reset manual. | P2 |

---

## 6. Lintas Modul / Teknis

| Item | Kondisi | Yang kurang / rekomendasi | Prioritas |
|---|---|---|---|
| Migrasi & indeks | ✅ | Pengguna sudah menjalankan `migrate`. Verifikasi manual indeks `email_santri` hanya bila ada data lama duplikat. | P1 |
| Policy | ✅ | `SantriPolicy`, `NilaiPolicy`, `PembinaanPolicy`, `IzinPolicy`, `LegalisirPolicy`, dan `PortalPolicy` (ability `portal.*`) terdaftar di `AppServiceProvider`. Pemeriksaan manual lama tetap dipertahankan. Prestasi belum perlu policy khusus (sudah difilter per jenjang). | P2 |
| Import | ✅ | Santri CSV dan nilai CSV sudah nyata (native, tanpa package). XLSX tidak didukung (ekspor ke CSV dulu). | P1 |
| Email / kredensial | ⚠️ | Mailer `log` di lokal. Produksi wajib SMTP. Sediakan fallback admin bila email gagal. | P2 |
| Audit log | ✅ | Aksi sensitif tercatat: import CSV santri/nilai, lulus/buat akun massal, reset password, ubah role & jenjang, tambah/hapus pengguna, buka nilai `published`, CRUD jadwal/kurikulum/rapor, dan `login_google`. | P2 |
| Login Google | 🚧 kode siap | Socialite terpasang di `composer.lock` tapi `vendor/` belum ada; kredensial `GOOGLE_CLIENT_ID`/`GOOGLE_CLIENT_SECRET` belum diisi. Tombol otomatis muncul setelah env terisi. Panduan lengkap: [17-google-oauth.md](17-google-oauth.md). | P2 |
| Test | ✅ sebagian | Feature test scoping jenjang & kepemilikan anak ditambahkan: `tests/Feature/ScopingJenjangTest.php` dan `tests/Feature/PortalKepemilikanAnakTest.php` (7 test method). Dua migrasi disesuaikan agar `RefreshDatabase` SQLite lolos: `2026_11_02_000001_remove_nik_column.php` (index UNIQUE `nik` dibuang sebelum drop kolom) dan `2026_11_28_000001_tambah_jenis_kelamin_santri.php` (backfill pakai subquery, bukan `UPDATE ... JOIN`). `LoginDanResetPasswordTest.php` juga ditambahkan (login 6 role, akun nonaktif, password sementara, ganti password, reset via token email). `VerifikasiPPDBTest.php` juga ditambahkan (form & honeypot, email santri duplikat, verifikasi diterima/ditolak, scoping jenjang). `NilaiDanIzinTest.php` ditambahkan 28 Nov 2026: 9 test nilai (draft, pengembalian ke draft, hapus draft, admin tanpa jenjang, mapel beda jenjang, terbitkan, pengurus ditolak, buka-kembali + alasan, scoping jenjang) dan 7 test izin (ajukan anak sendiri, anak orang lain ditolak, izin aktif, setujui + kode, tolak wajib alasan, tanda kembali, scoping jenjang). `SoftDeleteSantriTest.php` ditambahkan 28 Nov 2026: 6 method (soft delete oleh Superadmin, penolakan Admin Jenjang, hilang dari daftar/detail, pemulihan dari tempat sampah, pembatasan akses sampah/pemulihan, serta nilai & izin tetap utuh). `KepengurusanCrudTest.php` ditambahkan 28 Nov 2026: 7 method (tambah & tampil di profil, validasi wajib, ubah urutan, hapus, penolakan non-superadmin, fallback foto legacy, unggah foto ke disk `public`). `GambarPublikTest.php` ditambahkan 28 Nov 2026: 3 method (ketersediaan 7 banner, 7 foto ketua, dan fallback bila berkas tidak ada). `tests/Unit/AltGambarTest.php` ditambahkan 28 Nov 2026: 2 method pemeriksa source Blade (tanpa database). `PencarianSantriTest.php` ditambahkan 28 Nov 2026: 5 method (nama, No. Induk, email wali, `q` + jenjang, Admin Jenjang fail-closed). `SlugBeritaTest.php` ditambahkan 28 Nov 2026: 4 method (slug otomatis, duplikat ditolak, permalink tetap saat judul diedit, edit tanpa kirim slug). `LocaleTanggalTest.php` ditambahkan 28 Nov 2026: 3 method (locale `id`, nama bulan Indonesia, dan pemeriksa source Blade agar tidak ada `format('d/m/Y')`/`format('d M Y')` tampilan). `PathGambarStorageTest.php` (4 method), `GaleriStorageTest.php` (1 method), `NamaAplikasiTest.php` (1 method), `EnvExampleTest.php` (2 method), dan `IdentifierUnikTest.php` (3 method), dan `ViewMatTest.php` (1 method) ditambahkan 28 Nov 2026 untuk normalisasi path, upload/hapus galeri di disk `public`, branding aplikasi, keamanan template `.env`, identifier unik terhadap soft delete, dan view mati. `PesanFlashTest.php` (2 method) dan `DokumenSinkronTest.php` (2 method) ditambahkan 28 Nov 2026 untuk memastikan flash Arsipkan mengikuti status `keluar`/`arsip` serta doc 03/05 tidak berisi klaim lama. AI hanya menulis/memeriksa sintaks; manusia yang menjalankan test. | P1 |
| Styling | ✅ | Selesai 28 Nov 2026: kelima layout (publik, auth, admin, portal, alumni) memakai `@vite`; Tailwind CDN dihapus; `@custom-variant dark` di `app.css` untuk mode gelap admin; `.nav-scroll` dan font Instrument Sans disatukan. `npm run build` sukses. | P2 |
| Halaman error | ✅ | `errors/403`, `404`, `429`, `500` berbahasa Indonesia dengan gaya seragam (28 Nov 2026). Tombol kembali memakai `route('home')`. | — |
| Aset | ✅ | Selesai dikompresi 28 Nov 2026: `logo.png` 3,81 MB → 141 KB (512x512), `logo-teks.png` 179 KB → 39 KB, `banner.png`/`slide-1.png` masing-masing 1,20 MB → 303 KB, `pimpinan.jpg` 605 KB → 127 KB; total 6,98 MB → 0,91 MB (hemat 86,9%). Path tidak berubah. `public/images/` sudah berisi 14 placeholder (7 banner 1200×400 ±28 KB dan 7 foto ketua 320×320 ±4–5 KB, total 230 KB); `GambarPublik` menemukan berkas nyata, fallback tetap aktif. Placeholder bukan foto resmi — ganti saat tersedia. 16 file 0 byte sudah dihapus; `UserFactory.php` tetap dipakai test. | P3 |
| Dokumentasi 01–08, 10 | ✅ | Doc 03/05 diselaraskan 28 Nov 2026: migrasi 50 file, import CSV nyata + template on-the-fly, soft delete, email santri, jenis kelamin, jadwal/kurikulum/rapor, PPDB multi-anak + email, slug permanen, kepengurusan disk `public`, controller akademik/Google. Doc 01/02/06/07/08/09/16/18 juga sudah diselaraskan (route, test, aset, sumber data halaman). | P3 |
| Tautan Blade | ✅ | Audit 28 Nov 2026: seluruh nama `route()` yang dipakai Blade/controller terdaftar (dua temuan awal ternyata `request()->route('jenjang')` dan `route('register')` yang dijaga `Route::has`). 32 `url('/...')` hardcoded diganti named route; `welcome.blade.php` tidak lagi mengarah ke `/dashboard` yang tak punya route. | — |
| Alt gambar | ✅ | 28 Nov 2026: 6 `alt=""` diganti teks deskriptif (foto alumni, gambar berita, thumbnail galeri, alat potong), `<img>` pratinjau berita diberi `alt`, avatar admin memakai nama pengguna. Dijaga `tests/Unit/AltGambarTest.php`. | — |

---

## 7. Urutan Disarankan untuk Sesi Berikutnya

1. ~~**Rapikan menu mati:** `Program` (redirect) dan `Kegiatan` admin (placeholder).~~ ✅ **Selesai 28 Nov 2026** — `Program` tetap alias redirect; `Kegiatan` kini menampilkan data nyata. Berikutnya: nomor 2.
2. ~~**Scoping data:** prestasi per jenjang + widget gender di dashboard.~~ ✅ **Selesai 28 Nov 2026** — prestasi sudah ter-scoping; kolom `jenis_kelamin` santri ditambahkan dan widget dihitung nyata. Berikutnya: nomor 3.
3. ~~**Audit log** untuk aksi sensitif yang baru ditambahkan (import, lulus massal, akun massal, reset password).~~ ✅ **Selesai 28 Nov 2026** — ditambah juga perubahan peran/jenjang & hapus pengguna. Berikutnya: nomor 4.
4. ~~**Perluas Policy** ke portal wali/alumni, pembinaan, izin, legalisir.~~ ✅ **Selesai 28 Nov 2026** — empat policy baru ditambahkan secara aditif; pengecekan manual tetap jadi lapisan kedua.
5. ~~**Import nilai massal CSV** memakai pola `App\Support\ImportSantri` + pratinjau error.~~ ✅ **Selesai 28 Nov 2026** — `App\Support\ImportNilai`, template dinamis, baris error dilewati dan ditampilkan, audit log ditambahkan. Berikutnya: nomor 6.
6. ~~**Keputusan produk yang masih terbuka** (dari docs/14 §9).~~ 🚧 **Kode selesai 28 Nov 2026**, tinggal eksekusi oleh manusia:
   - ✅ Jadwal pelajaran & kurikulum dibangun lengkap (CRUD + validasi bentrok + terbit).
   - ✅ Tabel `rapor_santri` ditambahkan (rata-rata/peringkat otomatis + catatan wali kelas).
   - ✅ Nilai `published` bisa dibuka kembali oleh Superadmin/Admin Jenjang dengan alasan wajib + audit.
   - ✅ Google Login dipasang lewat Socialite; tombol muncul setelah kredensial terisi (lihat doc 17).
   - **Manusia:** jalankan `php artisan migrate`, `composer install`, isi `.env` Google, `php artisan config:clear`, lalu verifikasi UI.
7. **Pengerasan akhir:** SMTP produksi, ~~test feature scoping jenjang & kepemilikan anak~~ ✅ **Selesai ditulis 28 Nov 2026** (tinggal dijalankan manusia), test login/reset password ✅ **Selesai ditulis 28 Nov 2026** (tinggal dijalankan manusia), test verifikasi PPDB ✅ **Selesai ditulis 28 Nov 2026** (tinggal dijalankan manusia), test nilai CRUD & izin ✅ **Selesai ditulis 28 Nov 2026** (tinggal dijalankan manusia), test soft delete santri ✅ **Selesai ditulis 28 Nov 2026** (tinggal dijalankan manusia), test CRUD kepengurusan ✅ **Selesai ditulis 28 Nov 2026**, test aset `GambarPublik` ✅ **Selesai ditulis 28 Nov 2026**, ~~satukan styling~~ ✅ **Selesai 28 Nov 2026**, ~~kompresi aset~~ ✅ **Selesai 28 Nov 2026**, ~~error 403/404/500~~ ✅ **Selesai 28 Nov 2026**.
8. ~~**CRUD kepengurusan** (P2-3).~~ ✅ **Selesai 28 Nov 2026** — tabel `kepengurusan` kini dikelola dari `/admin/kepengurusan`; query di `/profil` dipindah ke controller.
9. ~~**Aset `public/images/`** (P2-5).~~ ✅ **Selesai 28 Nov 2026** — 14 placeholder dibuat di `public/images/program/` dan `public/images/pengurus/`; tidak ada path baru di Blade.
10. ~~**Alt gambar deskriptif** (P3).~~ ✅ **Selesai 28 Nov 2026** — `alt=""` dan alt generik pada gambar data/admin diganti, dijaga `AltGambarTest`.
11. **Verifikasi UI + test oleh manusia** — panduan lengkap ada di [19-panduan-verifikasi-manusia.md](19-panduan-verifikasi-manusia.md). AI tidak menjalankan migrate/test/UI. Setelah hasil diterima, lanjut ke P3 lain (slug berita/redirect, format tanggal) atau ganti placeholder dengan data/foto resmi pesantren.
12. ~~**Slug berita/redirect** (P3).~~ ✅ **Selesai 28 Nov 2026** — kebijakan permalink permanen dipilih: slug tidak berubah saat judul diedit, form edit readonly, `SlugBeritaTest` ditambahkan.
13. ~~**Format tanggal** (P3).~~ ✅ **Selesai 28 Nov 2026** — locale default `id` (`.env` + `config/app.php`), 9 tanggal tampilan perlu `translatedFormat`, format teknis dipertahankan, `LocaleTanggalTest` ditambahkan.
14. ~~**Branding aplikasi** (P2-12).~~ ✅ **Selesai 28 Nov 2026** — `APP_NAME="Pondok Pesantren Babul Hasanah"` di `.env` + default `config/app.php`; `SESSION_COOKIE=laravel-session` dipertahankan agar sesi tidak terputus, `NamaAplikasiTest` ditambahkan.
15. ~~**Template `.env.example`**.~~ ✅ **Selesai 28 Nov 2026** — template lengkap dibuat (locale `id`, branding, DB/queue/cache, placeholder Google & SMTP) tanpa secret asli; `EnvExampleTest` menjaga isi dan memastikan tidak ada kredensial tersalin dari `.env` lokal.
16. ~~**Pengerasan identifier** (P3).~~ ✅ **Selesai 28 Nov 2026** — `App\Support\KodeUnik` memusatkan pembuatan `PPDB-…`, `SANTRI-…`, `IZIN-…` dengan pengecekan duplikat; nomor induk kini memeriksa santri yang dihapus lunak (`withTrashed()`) agar tidak melanggar unique index. Format kode tetap. `IdentifierUnikTest` ditambahkan.
17. ~~**Bersihkan view mati** (P3).~~ ✅ **Selesai 28 Nov 2026** — `welcome.blade.php` (72 KB, tanpa route) dan `layouts/admin-menu-struktur.blade.php` (duplikat menu yang sudah ada inline di sidebar) dihapus. `ViewMatTest` mencegah keduanya muncul lagi.
18. ~~**Perbaiki pesan sukses salah konteks** (P3).~~ ✅ **Selesai 28 Nov 2026** — flash aksi Arsipkan kini membedakan status `keluar` ("Keluar / Berhenti") dan `arsip` ("berhasil diarsipkan"); sebelumnya keduanya selalu berkata "diarsipkan". `PesanFlashTest` ditambahkan (2 method).
19. ~~**Sinkron doc 03/05/07 + doc peran** (P3).~~ ✅ **Selesai 28 Nov 2026** — `03-database.md`, `05-backend.md`, `07-fitur.md`, dan `role-pengguna.md` disesuaikan dengan kode (50 migrasi, 34 model, 57 controller, 144 Blade; import CSV nyata; soft delete; email santri; jenis kelamin; jadwal/kurikulum/rapor; PPDB 13 field + multi-anak + email konfirmasi + `KodeUnik`; slug permanen; kepengurusan disk `public`; controller akademik/Google). `docs/09`, `docs/16`, `docs/19`, dan `docs/README.md` juga diperbarui. `DokumenSinkronTest` (2 method, tanpa DB) menjaga keempat doc itu. Berikutnya: ganti placeholder dengan data/foto resmi pesantren (butuh aset dari pengguna).

## 8. Cara Pakai Checklist Ini

- Awali sesi dengan: "Buka `docs/16-checklist-per-menu.md`, kerjakan item nomor X."
- Agen cukup membaca file terkait di kolom konteks/docs, lalu mengubah kode.
- **Jangan** menjalankan perintah database/test; serahkan ke manusia.
- Setelah selesai, perbarui tanda status di dokumen ini dan di `docs/09-audit-dan-rekomendasi.md`.
