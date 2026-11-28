# 09 — Audit, Temuan, dan Rekomendasi

**Pembaruan:** 28 November 2026 (finalisasi 6 role, alur pesantren, portal alumni, soft delete santri,
penghapusan fitur Kepengurusan)
**Metode:** pembacaan langsung kode, migrasi, route, dan view; verifikasi daftar route dengan
`php artisan route:list --except-vendor` (**227 route aplikasi**).
Tidak ada perintah yang menyentuh database.
**Batasan:** status migrasi/tabel/isi database **tidak diverifikasi** oleh agen AI sesuai
[ATURAN.md](ATURAN.md). Semua hal yang bergantung pada kondisi database ditandai
“perlu verifikasi manusia”.

> Dokumen ini menggantikan versi sebelumnya. Temuan yang sudah selesai dikumpulkan di
> [§12 Riwayat Perbaikan](#12-riwayat-perbaikan) agar daftar kerja yang terbuka tidak tercampur
> dengan yang sudah beres.

---

## 1. Ringkasan Kesehatan

| Area | Status | Catatan |
|---|---|---|
| Autentikasi & redirect per peran | ✅ Baik | 6 peran, satu sumber `Akses`, throttle login 6/menit, akun nonaktif & password sementara |
| Otorisasi route | ✅ Baik | Middleware `role` di setiap grup; 403 lintas peran |
| Otorisasi per-record | ✅ Berjalan | Gate/Policy terpasang di santri, nilai, pembinaan, izin, legalisir, dan portal; pengecekan manual jadi lapisan kedua |
| Scoping jenjang | ✅ Berjalan | Santri, pendaftaran, alumni, nilai, kelas, izin, pengumuman |
| Dashboard per peran | ✅ Berjalan | 4 dashboard panel + portal wali + portal alumni |
| CRUD inti | ✅ Berjalan | Berita, galeri, prestasi, alumni, struktur, info harian |
| PPDB → akun wali | ✅ Baik | Password acak + email kredensial + wajib ganti 24 jam |
| Import Excel/CSV | ✅ CSV native | `ImportSantri` + `ImportNilai` memproses CSV, template CSV dinamis, baris error dilaporkan; XLSX tidak didukung |
| Modul akademik (nilai/transkrip/mapel) | ✅ UI / 🚧 tabel | Kode selesai; tabel menunggu migrasi manusia |
| Halaman publik | ✅ Sebagian | Banner/foto jenjang memakai placeholder `public/images/`; konten statis lain sudah memakai tabel `konten` dengan fallback |
| Test otomatis | ⚠️ Berkembang | 2 test contoh + 12 feature test (scoping, portal, login/reset, PPDB, nilai/izin, soft delete santri, aset GambarPublik, pencarian, slug berita, galeri, identifier, pesan flash) + 7 unit test; ditulis/diperiksa AI, dijalankan manusia |
| Dokumentasi | ⚠️ Tidak seragam | Beberapa dokumen masih memuat kondisi lama |

**Skala kode aktual:** 227 route aplikasi, 54 controller, 33 model, 53 file migrasi, 136 file Blade,
7 seeder, 21 file test (2 contoh + 12 feature + 7 unit, 87 method).

---

## 2. P0 — Harus Segera Diperbaiki

| # | Temuan | Bukti | Dampak | Rekomendasi | Status |
|:--:|---|---|---|---|---|
| P0-1 | `/kegiatan` mengarah ke view yang tidak ada | `routes/web.php` → `view('pages.kegiatan')`; file `resources/views/pages/kegiatan.blade.php` tidak ada | Error 500 saat URL dibuka | Route lama dialihkan ke `info-harian.index` yang berisi agenda & jadwal; nama route tetap `kegiatan` | ✅ Selesai (27 Nov 2026) |
| P0-2 | `admin.kegiatan.index` mengarah ke view yang tidak ada (temuan lama) | `KegiatanController@index` → `dashboard.kegiatan.index`; file dan folder view ternyata **sudah ada** | Tidak ada error; halaman monitoring (baca saja) | Tidak perlu tindakan | ✅ Selesai |
| P0-3 | `super.settings` memanggil method yang tidak ada (temuan lama) | `DashboardController@settings()` dan `resources/views/dashboard/settings.blade.php` ternyata **sudah ada** | Tidak ada error bila URL diketik | Tidak perlu tindakan | ✅ Selesai |

Catatan: P0-2 memakai middleware baca (`super_admin,admin,pengurus,pembina`) dan P0-3 dibatasi
`role:super_admin`. Keduanya sudah diverifikasi ulang dari kode pada 27 November 2026. Route
`kegiatan` tidak memiliki tautan menu aktif; redirect menjaga bookmark lama tetap berfungsi.

---

## 3. P1 — Prioritas Tinggi

### 3.1 Password akun wali dari PPDB — ✅ Selesai

**Kondisi sekarang:** `PendaftaranController@updateStatus` membuat password acak
`Str::password(12)`, mengisi `must_change_password = false` dan `temp_password_expires_at = null`,
lalu mengirim email kredensial (`KredensialMail`) berisi username (email wali) + password.
Kredensial langsung dipakai tanpa ganti password. Akun wali lama dibersihkan oleh migrasi
`2026_11_28_000006_hapus_paksa_ganti_password_wali.php`. Perlakuan yang sama berlaku untuk alumni
(migrasi `2026_11_28_000007_hapus_paksa_ganti_password_alumni.php`).

**Sisa risiko:** keberhasilan pengiriman email bergantung konfigurasi mailer. Bila gagal, admin
mendapat pesan untuk memakai tombol **Reset Password Wali**. Pastikan mailer produksi benar dan
rotasi kredensial bila pernah dibagikan.

### 3.2 Normalisasi role — ✅ Selesai

**Kondisi sekarang:** `CheckRole` memakai `Akses::normalisasi()` untuk role user maupun daftar role
route. Role lama `pengasuh`, `superadmin`, `wali`, `admin_jenjang`, dan `alumnus` dikenali. Migrasi
`2026_11_20_000001_finalisasi_role_dan_akun` juga mengubah data `pengasuh` → `pengurus`.

**Sisa pekerjaan:** verifikasi data lama oleh manusia setelah migrasi dijalankan, lalu pastikan
tidak ada nilai role di luar enam role resmi.

### 3.3 Index unik email PPDB vs email santri — ✅ Selesai (27 Nov 2026)

**Keputusan:** email **wali** boleh dipakai banyak anak, email **santri** wajib unik karena menjadi
username akun alumni.

- Migrasi `2026_11_17_000002_drop_unique_email_on_pendaftaran_online_table.php` sudah menghapus
  indeks unik `pendaftaran_online.email` secara aman (cek `getIndexes()` + `dropUnique()` by nama).
- Validasi admin `PendaftaranController@update` sudah diselaraskan: aturan
  `unique:pendaftaran_online,email` dihapus. Pemeriksaan bentrok dengan **akun pengguna** tetap ada.
- Migrasi baru `2026_11_27_000002_tambah_index_unik_email_santri.php` memasang indeks unik pada
  `pendaftaran_online.email_santri` dan `santri.email_santri`. Jika data lama masih mengandung
  duplikat, indeks **sengaja dilewati** agar migrasi tidak gagal; duplikat perlu dibersihkan manusia.

**Yang masih perlu manusia:** jalankan `php artisan migrate`, lalu verifikasi dengan
`SHOW INDEX FROM pendaftaran_online` dan `SHOW INDEX FROM santri` bila ingin memastikan indeks benar-benar aktif.

### 3.4 Otorisasi per-record (Gate/Policy) — ✅ Selesai (28 Nov 2026)

**Temuan lama:** tidak ada folder `app/Policies`; pengecekan memakai `Akses::cocokJenjang()` dan
`abort_unless()` manual di controller.

**Implementasi sekarang:**

- `SantriPolicy` dan `NilaiPolicy` (27 Nov) dipakai di `SantriFormController` dan `NilaiController`.
- Perluasan 28 Nov 2026:
  - `PembinaanPolicy` — satu policy untuk `Hafalan`, `Pelanggaran`, dan `Absensi`; hak tulis
    `pembinaan.ubah` + cakupan santri via `Akademik::santriQuery()`.
  - `IzinPolicy` — verifikasi oleh Pembina/Admin Jenjang versus pengajuan oleh wali pemilik anak.
  - `LegalisirPolicy` — proses oleh Admin Jenjang versus ajukan/unduh oleh alumni pemilik data.
  - `PortalPolicy` — ability `portal.*` lewat `Gate::define()` untuk kepemilikan anak, rapor,
    dokumen, dan data alumni.
- Semua policy memanggil `Akses`/relasi yang sama dengan middleware `role:` dan `abort_unless()` lama.
  Pemeriksaan manual sengaja **dipertahankan** sebagai lapisan kedua sekaligus menjaga pesan error
  spesifik. Tidak ada policy baru untuk prestasi karena datanya sudah difilter per jenjang.
  Detail rencana: [14-modul-akademik.md](14-modul-akademik.md) §7.

### 3.5 Import Excel/CSV masih stub — ✅ Selesai (CSV native, 27 Nov 2026)

**Temuan lama:** `SantriController@import()` hanya memvalidasi file lalu mengembalikan pesan sukses
tanpa memproses data. Package `maatwebsite/excel` tidak ada dan template XLSX tidak tersedia.

**Implementasi sekarang:** jalur CSV native tanpa dependency baru.

- `App\Support\ImportSantri` membaca CSV (koma atau titik koma), menerima header kunci/`snake_case`
  maupun label Indonesia, serta menyeragamkan jenjang memakai `Jenjang::normalisasi()`.
- Validasi per baris memakai aturan yang sama dengan Tambah Santri manual, termasuk batas jenjang
  Admin Jenjang (`Akses::cocokJenjang()`), email santri, dan duplikasi nomor induk.
- Baris gagal dilewati dan dilaporkan di halaman index (maksimal 20 pesan pertama); baris valid
  tetap tersimpan. Setiap penyimpanan dibungkus `DB::transaction()`.
- Jika `email_wali` belum memiliki akun, akun `wali_santri` dibuat dengan kredensial yang langsung
  bisa dipakai (tanpa ganti password) dan email kredensial dikirim. Kegagalan email tidak
  membatalkan import dan dilaporkan terpisah.
- `downloadTemplate()` kini menghasilkan `template_data_santri.csv` secara dinamis, sehingga tidak
  bergantung pada berkas statis di `public/assets/template/`.

**Catatan:** format XLSX/XLS sengaja tidak didukung. Admin diminta mengekspor ke CSV terlebih dahulu.

### 3.6 Status tabel `info_harian` tidak dapat dipastikan

**Bukti:** route `admin.info.*` dan tampilan beranda sudah memakai `Schema::hasTable()`; file migrasi
ada. Dokumen 03/07/08 saling bertentangan soal status migrasi.

**Dampak:** fitur bisa tampil kosong/dummy tanpa error, sehingga sulit dibedakan dari “belum
dimigrasi”.

**Rekomendasi:** manusia menjalankan `php artisan migrate:status` dan `php artisan migrate`, lalu
memperbarui dokumentasi. Setelah tabel ada, hapus fallback dummy di beranda agar sumber data jelas.

---

## 4. P2 — Sedang

| # | Temuan | Bukti / Dampak | Rekomendasi |
|:--:|---|---|---|
| P2-1 | `santriPutra`/`santriPutri` selalu 0 | `DashboardController.php` mengirim 0; tabel `santri` tidak punya `jenis_kelamin` | ✅ Selesai 28 Nov 2026: kolom `jenis_kelamin` ditambahkan, diisi dari PPDB/form/import, dan widget dihitung di controller |
| P2-2 | Prestasi di dashboard Admin Jenjang bersifat global | Temuan lama: filter jenjang belum ada | ✅ Selesai: `prestasi_santri.jenjang` sudah dipakai di `PrestasiAdminController`, `RoleDashboardController`, dan form input |
| P2-3 | Pengelolaan kepengurusan hilang (temuan lama) | Dulu tidak ada controller/route CRUD `kepengurusan`; hanya ditampilkan di `/profil` | ❌ **Fitur dihapus 28 Nov 2026:** controller, model, dua view, 6 route `admin.kepengurusan.*`, tautan sidebar, kartu `/profil`, dan stat "Asatidz" dihapus; tabel `kepengurusan` dihapus via migrasi `2026_11_28_000008_hapus_tabel_kepengurusan`. Menu **Struktur Organisasi** tetap ada; `admin.struktur.*` (6 route) dan bagan lengkap di `/struktur-organisasi` tidak berubah |
| P2-4 | `program` tidak dikelola | `admin.program.index` hanya closure redirect; view `dashboard/program` tidak terpakai | ✅ Selesai 28 Nov 2026: tetap alias redirect, kini langsung ke halaman kanonik `admin.dashboard.jenjang`; `Kegiatan` admin diisi dari data nyata (Info Harian + catatan kalender + absensi) |
| P2-5 | Aset gambar publik hilang | Folder `public/images/` untuk program dan pengurus tidak ada | ✅ Selesai 28 Nov 2026: 14 placeholder dibuat (7 `images/program/{slug}.jpg` 1200×400 dan 7 `images/pengurus/ketua-{slug}.jpg` 320×320, total 230 KB). `GambarPublik` kini menemukan file nyata; fallback bawaan tetap aktif. Bukan foto resmi — ganti dengan aset pesantren bila tersedia; halaman sejarah/profil/pembelajaran memakai `konten`/`StrukturOrganisasi`, bukan folder ini |
| P2-6 | Dua sistem styling | Publik/auth via Vite; admin/portal via Tailwind CDN | ✅ Selesai 28 Nov 2026: 5 layout memakai `@vite`; CDN dihapus; `dark:` memakai `@custom-variant` kelas `.dark`; `.nav-scroll` disatukan di `app.css` |
| P2-7 | Query database di Blade | `beranda`, `profil` | Pindahkan ke controller/view composer |
| P2-8 | Penyimpanan file tidak seragam | Mayoritas disk `public`; sisa `public/upload` | Standarkan disk `public` + `Storage::delete` |
| P2-9 | Audit log baru mencakup aksi penting | `log_aktivitas` + `LogAktivitasController` sudah ada | ✅ Selesai 28 Nov 2026: dicatat pula import CSV santri/nilai, lulus massal, buat akun massal, reset password, ubah role/jenjang pengguna, dan `hapus_santri`/`pulihkan_santri` (audit `kepengurusan_*` ikut dihapus bersama fiturnya 28 Nov 2026) |
| P2-10 | PPDB tanpa throttle/captcha | Form publik bisa spam | ✅ Selesai 27 Nov 2026: `throttle:5,10` pada `POST /pendaftaran`, honeypot `website` (aturan `prohibited`), dan halaman 429 berbahasa Indonesia |
| P2-11 | Email PPDB bergantung mailer | Mailer `log` di lokal | Pastikan SMTP produksi + fallback reset admin |
| P2-12 | `APP_NAME`/locale belum branding | `APP_NAME=Laravel`, locale `en` | ✅ Selesai 28 Nov 2026: `APP_NAME="Pondok Pesantren Babul Hasanah"` di `.env` dan default `config/app.php` ikut diganti; locale sudah `id` sejak item format tanggal. `SESSION_COOKIE=laravel-session` ditambahkan secara eksplisit agar perubahan `APP_NAME` tidak mengganti nama cookie dan memaksa pengguna logout. Dijaga `NamaAplikasiTest`. |
| P2-13 | Tidak ada halaman error kustom | 403/404/500 memakai bawaan | ✅ Selesai 28 Nov 2026: `errors/403`, `404`, `429`, `500` berbahasa Indonesia, gaya seragam, tombol `route('home')` |
| P2-14 | Arsip santri sudah ada, hapus permanen masih mungkin | `santri.arsipkan` + `kembalikan`; `destroy` masih hard delete untuk Superadmin | ✅ Selesai 28 Nov 2026: `santri.deleted_at` ditambahkan, model memakai `SoftDeletes`, `destroy()` jadi soft delete + audit `hapus_santri`, halaman `admin.santri.terhapus` (`onlyTrashed`) dan aksi `admin.santri.pulihkan` (`restore` + audit `pulihkan_santri`) hanya untuk Superadmin. Status PPDB sengaja tidak diubah saat hapus agar pemulihan tidak perlu verifikasi ulang. `forceDelete` tidak diekspos ke UI; unique index NIS/email tetap dipakai baris terhapus |
| P2-15 | Test minim | 2 contoh + 2 feature scoping | ✅ Sebagian 28 Nov 2026: `ScopingJenjangTest`, `PortalKepemilikanAnakTest`, `LoginDanResetPasswordTest`, `VerifikasiPPDBTest`, `NilaiDanIzinTest` (16 method), `SoftDeleteSantriTest` (6 method), dan `GambarPublikTest` (3 method) ditulis; `KepengurusanCrudTest` dihapus bersama fiturnya; manusia yang menjalankan, portal lain masih menyusul |

---

## 5. P3 — Rendah

- ~~`resources/views/welcome.blade.php` (72 KB) masih ada, tapi tanpa route aktif.~~ ✅ Selesai
  28 Nov 2026: dihapus setelah audit tidak ada route maupun `@include` yang merujuknya.
- ~~Template `.env.example` belum ada.~~ ✅ Selesai 28 Nov 2026: template dibuat dengan
  `APP_LOCALE=id`, `APP_NAME` pesantren, `SESSION_COOKIE`, DB/queue/cache, placeholder Google
  & SMTP, tanpa kredensial asli. Dijaga `EnvExampleTest`.
- ~~`layouts/admin-menu-struktur.blade.php` tidak di-include.~~ ✅ Selesai 28 Nov 2026:
  duplikat lama dihapus; menu Struktur sudah langsung ada di `layouts/admin-sidebar.blade.php`.
  Dijaga `ViewMatTest`.
- File 0 byte sudah dibersihkan 28 Nov 2026 (16 file: `app/`, `resources/views/`, dan dua factory di `database/factories/`); `UserFactory.php` tetap dipakai test.
- ~~Gambar `logo.png` 3,8 MB perlu dikompresi.~~ ✅ Selesai 28 Nov 2026: 6 aset statis dikompresi (6,98 MB → 0,91 MB, hemat 86,9%), path tidak berubah.
- ~~Sebagian pesan sukses salah konteks (sisa modul lama).~~ ✅ Selesai 28 Nov 2026:
  aksi **Arsipkan** di `SantriAksiController` melayani dua status (`keluar` dan `arsip`) tetapi
  selalu mengirim pesan "diarsipkan". Flash kini mengikuti status yang dipilih: "Keluar /
  Berhenti" atau "berhasil diarsipkan". Dijaga `PesanFlashTest`.
- ~~Penggunaan `rand()`/`Str::random` untuk identifier perlu ditinjau.~~ ✅ Selesai 28 Nov 2026:
  pembuatan `PPDB-…`, `SANTRI-…`, dan `IZIN-…` dipusatkan di `App\Support\KodeUnik` dengan
  pengecekan tabrakan sebelum pakai. Nomor induk **juga memeriksa santri soft-deleted** lewat
  `withTrashed()`, karena unique index tetap dipakai baris terhapus; import CSV memakai peta
  nomor yang sama. Format kode tidak berubah. Dijaga `IdentifierUnikTest`.
- ~~Format tanggal campur `translatedFormat` dan hardcode.~~ ✅ Selesai 28 Nov 2026: `APP_LOCALE`/default `config('app.locale')` jadi `id`, sehingga seluruh `translatedFormat()` memakai nama bulan/hari Indonesia. Sembilan tanggal tampilan yang masih memakai `format('d/m/Y')` / `format('d M Y')` diganti `translatedFormat('d M Y')`. Format teknis (`Y-m-d` untuk input, `date('Y')` untuk tahun, `H:i:s` untuk jam) sengaja tidak diubah. Dijaga `tests/Unit/LocaleTanggalTest.php`.
- ~~Sebagian gambar tidak punya `alt` deskriptif.~~ ✅ Selesai 28 Nov 2026: 6 `alt=""` diganti teks deskriptif (alumni, berita, galeri, alat potong), `<img>` pratinjau diberi `alt`, dan avatar admin memakai nama pengguna. Dijaga oleh `tests/Unit/AltGambarTest.php`.
- ~~Slug berita tidak berubah saat judul diedit (perlu kebijakan redirect).~~ ✅ Selesai 28 Nov 2026: diputuskan **permalink bersifat permanen**. Slug dibuat sekali saat berita dibuat dan sengaja tidak diubah saat judul diedit, sehingga tautan lama tidak rusak dan tabel alias/redirect 301 tidak diperlukan. Form edit menampilkan permalink readonly; `SlugBeritaTest` menjaga perilaku ini.

---

## 6. Ketidaksinkronan Dokumentasi (Doc Drift)

Kode adalah sumber kebenaran. Tabel ini merangkum klaim dokumen yang sudah tidak sesuai.

| Dokumen | Klaim lama | Kondisi kode sekarang |
|---|---|---|
| 01 | 83 route, 30 controller, 15 model, 29 migrasi, 65 Blade, 5 seeder | 227 route, 54 controller, 33 model, 53 migrasi, 136 Blade, 7 seeder |
| 01 | Folder middleware berisi “3 middleware role” | `CheckRole` + `WajibGantiPassword` |
| 02 | 67 route; 20 migrasi; tanpa alias middleware; PPDB tanpa transaksi | 227 route; 53 migrasi; alias `role` ada; verifikasi PPDB memakai `DB::transaction` |
| 03 | `DatabaseSeeder` hanya `RoleAndUserSeeder` + konten; 3 akun | Memanggil 4 seeder + konten; tetap **3 akun inti**, bukan 11 akun dummy |
| 03 | `User::santri()` tidak ada | Ada (`hasMany`) + pivot `santri_wali` |
| 03 | Migrasi 20 file | 53 file (termasuk `2026_11_28_000008_hapus_tabel_kepengurusan`) |
| 04 | `/admin/pengurus` + `storeKepengurusan` ada | Route dan method tidak ada; fitur Kepengurusan dihapus 28 Nov 2026 (`admin.kepengurusan.*` = 0), Struktur Organisasi tetap ada |
| 04/05/18 | Halaman `/admin/pengelolaan-jenjang` tersendiri + `Admin\JenjangController` | Disatukan 28 Nov 2026 ke `/admin/dashboard-jenjang` (`admin.dashboard.jenjang`); controller/view lama dihapus, URL lama hanya redirect, sidebar hanya satu menu Dashboard Jenjang |
| 04 | `admin.program.index` → `ProgramController@index` | Closure redirect langsung ke `admin.dashboard.jenjang` |
| 05 | Konten menerima semua input tanpa whitelist | Sudah whitelist `items[]`, validasi gambar, layout JSON |
| 05 | Edit pengguna view tidak ada | `dashboard/pengguna/edit.blade.php` sudah ada |
| 05 | CRUD kepengurusan di `KontenController` | Tidak ada; fitur dihapus 28 Nov 2026 (tabel via migrasi `000008`), `admin.struktur.*` tetap 6 route |
| 06 | 59 Blade; menu Info Harian tidak ada | 136 Blade; menu Info Harian ada |
| 07 | Portal wali statis; password wali acak `babul####` | Portal dinamis multi-anak; password acak + email + wajib ganti 24 jam |
| 07 | Edit pengguna/detail santri/tambah santri tidak ada | Sudah tersedia |
| 08 | Seeder tambahan tidak dipanggil otomatis; akun `admin@…` | 4 seeder otomatis; 3 akun inti (Superadmin, Pengurus, Pembina) |
| 09 (lama) | Tidak ada rate limit login; PPDB tidak atomik | Login `throttle:6,1`; PPDB transaksi |
| 10 | Info Harian WIP, route belum ada | Route + menu + seeder sudah ada |

**✅ 28 Nov 2026:** seluruh dokumentasi dalam `docs/` telah diselaraskan dengan kode: 227 route
aplikasi, 54 controller, 33 model, 53 file migrasi (termasuk email santri, jenis kelamin,
jadwal/kurikulum/rapor, soft delete, dan `2026_11_28_000008_hapus_tabel_kepengurusan`), 136 file
Blade, 21 file test / 87 method, import santri CSV nyata, template CSV on-the-fly, `settings()`
tersedia, soft delete + pemulihan, PPDB 13 field (12 wajib + NISN opsional) dengan `email_santri`,
multi-anak, email konfirmasi, dan nomor induk lewat `KodeUnik`, slug berita permanen, serta
controller akademik/Google login. **Fitur Kepengurusan dihapus 28 Nov 2026** (controller, model,
view, 6 route `admin.kepengurusan.*`, kartu `/profil`, tabel); menu **Struktur Organisasi** tetap ada
dengan 6 route `admin.struktur.*` dan bagan lengkap di `/struktur-organisasi` tidak berubah.
**Halaman Jenjang disatukan 28 Nov 2026** ke `/admin/dashboard-jenjang`: `Admin\JenjangController`
dan dua view dihapus, URL lama (`/admin/pengelolaan-jenjang`, `/admin/pengelolaan-jenjang/{jenjang}`,
`/admin/pengelolaan-program`) hanya redirect, sidebar hanya satu menu Dashboard Jenjang, dan
`jenjang.lihat` kini juga mencakup `pengurus`/`pembina` (baca-saja). Tabel di atas tetap
dipertahankan sebagai catatan historis temuan awal.

**Tindakan:** dokumen 01–08, 10, dan 14 diperbarui bertahap pada 22 November 2026; 03/05/07 dan
`role-pengguna.md` menyusul 28 November 2026, diikuti 01/02/06/08/09/16/18/19 dan `README.md`.
Dokumen peran (`role-pengguna.md`, 11, 12) adalah sumber utama untuk 6 role final. Sisa risiko utama
ada di verifikasi migrasi oleh manusia.

---

## 7. Matriks Risiko Keamanan

| Risiko | Level | Keterangan |
|---|---|---|
| Nilai/rapor/portal tanpa policy per-record | Dimitigasi | `NilaiPolicy` + `PortalPolicy` + `LegalisirPolicy` terpasang; manual tetap jadi lapisan kedua |
| Index unik email PPDB vs fitur multi-anak | **Tinggi** | Perlu verifikasi DB; migrasi drop unique sudah ada |
| `.env` menyimpan kredensial & secret | Tinggi | Rotasi bila pernah dibagikan |
| `APP_DEBUG=true` di produksi | Tinggi | Stack trace terbuka |
| Email kredensial gagal terkirim | Sedang | Fallback reset password admin |
| Mass assignment konten | Sedang | Sudah dibatasi whitelist, tetap audit |
| Upload tanpa validasi MIME penuh | Sedang | Konten sudah divalidasi; modul baru wajib ikut |
| PPDB tanpa captcha/throttle | Sedang | Spam pendaftaran |
| Otorisasi hanya level route + manual | Dimitigasi | Gate/Policy aktif pada modul sensitif; endpoint baru tetap wajib diaudit |
| Audit log belum mencakup semua CRUD | Dimitigasi | `log_aktivitas` mencatat aksi sensitif (lihat P2-9) |
| XSS pada konten `{!! !!}` | Rendah–Sedang | Hanya admin |
| Tidak ada HTTPS wajib | Sedang | Hanya host ngrok |

---

## 8. Kebutuhan Baru — Modul Akademik

Kebutuhan yang diajukan:

1. Admin Jenjang mengunggah **nilai siswa per kelas**.
2. **Transkrip nilai**.
3. **Jadwal pelajaran** — ✅ kode siap 28 Nov 2026 (CRUD + anti-bentrok jam kelas/guru).
4. **Kurikulum** — ✅ kode siap 28 Nov 2026 (CRUD + item + terbit).
5. **Pengumuman** per jenjang.
6. Data tersebut tampil di **akun Wali Santri**.
7. Wali dapat melihat **kegiatan** dan **progres pembelajaran** anak.

Status: **UI dan validasi sudah diimplementasikan**; tabel `mata_pelajaran`, `nilai_santri`,
`dokumen_nilai`, `jadwal_pelajaran`, `kurikulum`, `kurikulum_item`, dan `rapor_santri` sudah ditulis
sebagai migrasi tetapi **belum dijalankan oleh agen AI**. Rancangan dan catatan implementasi ada di
**[14-modul-akademik.md](14-modul-akademik.md)**.

Implikasi audit:

- Modul ini menambah data pribadi sensitif → **policy per-record wajib** (P1-3.4).
- Unggah massal nilai sudah memakai `App\Support\ImportNilai` (CSV native); XLSX masih belum didukung.
- Perluasan `Akses::MODUL`, sidebar, dan scoping jenjang **sudah dilakukan**.
- Status `draft → terbit` sudah ada agar nilai belum final tidak bocor ke wali.
- Tabel baru + migrasi harus dijalankan **manusia**.
- Nama guru pengampu tampil lewat `mata_pelajaran.guru_id`; guru tetap tanpa akun login.

---

## 9. Roadmap

### Sprint 0 — Stabilisasi (P0)

1. ✅ Selesaikan/hapus `/kegiatan` dan `admin.kegiatan.index` (publik → `/info-harian`; admin → data nyata).
2. Implementasi atau hapus `super.settings`.
3. Verifikasi status migrasi & tabel (manusia), lalu sinkronkan dokumen.

### Sprint 1 — Keamanan & Data (P1)

1. ✅ Ganti password wali PPDB menjadi acak + paksa ganti saat login pertama.
2. ✅ Satukan normalisasi role di `CheckRole` lewat `Akses::normalisasi()`.
3. Verifikasi & perbaiki index unik email PPDB.
4. ✅ Tambah policy per-record untuk nilai, izin, dokumen, dan portal — selesai 28 Nov 2026.
5. ✅ Fondasi import CSV sudah nyata untuk santri dan nilai; XLSX masih opsional (lihat P1-3.5).
6. ✅ Audit ulang scoping jenjang (termasuk prestasi di dashboard) — selesai 28 Nov 2026.

### Sprint 2 — Modul Akademik (bertahap)

Mengikuti fase di [14-modul-akademik.md](14-modul-akademik.md) §8:

1. Fase 0: jalankan migrasi (manusia), verifikasi model/relasi, policy.
2. Fase 1: mapel + guru pengampu (sudah ada UI).
3. Fase 2: nilai, terbitkan, transkrip, dan unggah massal CSV (sudah ada UI).
4. Fase 3: portal wali nilai/rapor/jadwal/kurikulum (kode siap 28 Nov 2026, menunggu migrasi).
5. Fase 4: perluas audit log (sudah: buka nilai, jadwal/kurikulum/rapor, login Google), PDF, test.

### Sprint 3 — Konsolidasi & Kualitas (P2/P3)

1. Sinkronkan dokumentasi 01–08 & 10.
2. Satukan styling ke Vite, pindahkan query dari Blade.
3. Hapus file/tabel/view legacy.
4. Tambah rate limit PPDB, captcha, email konfirmasi.
5. ~~Tambah halaman error kustom~~ ✅ (403/404/429/500 selesai 28 Nov 2026); SEO, audit aksesibilitas.
6. ~~Tambah feature test untuk login, verifikasi PPDB, nilai CRUD, dan izin~~ ✅ (28 Nov 2026).

---

## 10. Checklist Verifikasi

### Boleh dijalankan AI (tidak menyentuh database)

- [ ] `php artisan route:list --except-vendor` → 227 route aplikasi (`admin.kepengurusan.*` = 0, `admin.struktur.*` = 6); handler `DashboardController@settings` ternyata sudah ada (P0-3 selesai)
- [ ] `vendor\bin\pint`
- [ ] `npm run build`
- [ ] `php artisan view:clear` / `config:clear` / `route:clear`

### Hanya manusia — AI dilarang ([ATURAN.md](ATURAN.md))

- [ ] `php artisan migrate:status` → semua migrasi `Ran`
- [ ] Verifikasi index unik `pendaftaran_online.email`
- [ ] `php artisan migrate` (termasuk `info_harian`, tabel alur pesantren, akademik, dan `2026_11_28_000008_hapus_tabel_kepengurusan` — data lama tidak dipulihkan `down()`)
- [ ] `php artisan db:seed --class=RoleAndUserSeeder` (bila perlu akun uji)
- [ ] `php artisan test`
- [ ] Uji manual P0: `/kegiatan`, `admin.kegiatan.index`, `super.settings`
- [ ] Uji P1: login dengan role lama `pengasuh`/`super` → harus dinormalkan
- [ ] Uji P1: pendaftaran dua anak dengan satu email
- [ ] Uji alur: PPDB diterima → email kredensial → wali langsung login ke portal
- [ ] Uji alur: santri LULUS → email kredensial → alumni langsung login ke portal
- [ ] Uji alur: izin pulang (setujui/tolak/terlambat/selesai)
- [ ] Uji alur: naik kelas + rollback Superadmin
- [ ] Uji alur: kelulusan → akun alumni → portal alumni

---

## 11. Lampiran Bukti

| Temuan | Lokasi |
|---|---|
| 232 route aplikasi & 3 route rusak (temuan awal; sekarang 227 setelah penghapusan 6 route `admin.kepengurusan.*` dan 2 route `alumni.tracer.*`) | `routes/web.php`; `php artisan route:list` |
| Password sementara + email kredensial | `app/Http/Controllers/Admin/PendaftaranController.php`; `app/Mail/KredensialMail.php` |
| Normalisasi role | `app/Support/Akses.php`; `app/Http/Middleware/CheckRole.php` |
| Index unik email | `database/migrations/2026_08_24_203449_add_email_to_pendaftaran_online_table.php`; `2026_11_17_000002_drop_unique_email_on_pendaftaran_online_table.php` |
| Validasi email publik | `app/Http/Controllers/Public/PendaftaranOnlineController.php` |
| Import stub | `app/Http/Controllers/Admin/SantriController.php` |
| Kepengurusan dihapus 28 Nov 2026 | `grep`: tanpa controller/model/view/`$kepengurusans`; `admin.kepengurusan.*` = 0; `admin.struktur.*` = 6; tabel via `2026_11_28_000008_hapus_tabel_kepengurusan` |
| Program redirect | `routes/web.php` (`admin.program.index`) |
| Dashboard gender 0 | `app/Http/Controllers/Admin/DashboardController.php` |
| Prestasi global | `app/Http/Controllers/Admin/RoleDashboardController.php` |
| Seeder otomatis | `database/seeders/DatabaseSeeder.php` |
| Relasi `User::santri()` + pivot | `app/Models/User.php`; migrasi `2026_11_20_000002_create_alur_pesantren_tables` |
| Akses modul | `app/Support/Akses.php` |
| Rancangan akademik | [14-modul-akademik.md](14-modul-akademik.md) |

---

## 12. Riwayat Perbaikan

Temuan dari audit sebelumnya yang **sudah selesai** dan tidak lagi masuk daftar kerja terbuka.
Status diverifikasi lewat pembacaan kode, bukan lewat eksekusi test.

| Temuan lama | Kondisi sekarang | Bukti |
|---|---|---|
| Detail santri belum ada | Halaman detail tersedia | `resources/views/dashboard/santri/detail.blade.php`; `SantriController@detail` |
| Edit pengguna belum ada | Halaman edit tersedia | `resources/views/dashboard/pengguna/edit.blade.php` |
| Info Harian belum punya route/menu | Route, menu, dan CRUD tersedia | `admin.info.*` di `routes/web.php`; `InfoHarianController` |
| Seeder tambahan tidak dipanggil otomatis | `DatabaseSeeder` memanggil 4 seeder | `database/seeders/DatabaseSeeder.php` |
| Login tanpa rate limit | `throttle:6,1` pada login | `routes/web.php:91`; `AuthenticatedSessionController` |
| PPDB tidak atomik | Verifikasi PPDB memakai `DB::transaction` | `Admin/PendaftaranController@updateStatus` |
| Konten menerima semua input | Whitelist `items[]` + validasi gambar | `Admin/KontenController` |
| Login/redirect per peran tersebar | Dipusatkan di `Akses` | `app/Support/Akses.php` |
| Scoping jenjang belum ada | Santri, pendaftaran, alumni, nilai, kelas, izin, pengumuman dibatasi | `Akses::jenjangSlug()` + `RekapJenjang` |
| Role pengasuh & modul keuangan | 6 role final tanpa keuangan; `pengasuh` → `pengurus` | `Akses`; migrasi `2026_11_20_000001` |
| Password wali seragam | Acak + email + wajib ganti 24 jam | `PendaftaranController`; `WajibGantiPassword` |
| Normalisasi role ganda | `CheckRole` memakai `Akses::normalisasi()` | `CheckRole.php` |
| Portal wali statis | Portal dinamis multi-anak + mode riwayat | `PortalDashboardController`; `PortalSantriController` |
| Alumni tanpa akun | Akun alumni otomatis saat lulus + portal | `SantriController@luluskan`; `AlumniPortalController` |
| Route baca PPDB/alumni memakai grup `santri.lihat` | Grup dipisah per modul (`pendaftaran.lihat`, `alumni.lihat`); Pembina tidak lagi bisa membuka PPDB/alumni/legalisir lewat URL | `routes/web.php` §4.5; tautan alumni disembunyikan di `santri/detail.blade.php` |

Catatan: perbaikan di atas **tidak menghapus** temuan P1 yang masih terbuka (verifikasi index email
PPDB oleh manusia dan status migrasi tabel baru). Import CSV sudah nyata, bukan stub. Lihat §3.

