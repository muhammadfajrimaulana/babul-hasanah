# 09 — Audit, Temuan, dan Rekomendasi

**Pembaruan:** 22 November 2026 (finalisasi 6 role, alur pesantren, portal alumni)
**Metode:** pembacaan langsung kode, migrasi, route, dan view; verifikasi daftar route dengan
`php artisan route:list --except-vendor` (**189 route aplikasi**).
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
| Otorisasi per-record | ⚠️ Lemah | Belum ada Gate/Policy; pengecekan manual tersebar |
| Scoping jenjang | ✅ Berjalan | Santri, pendaftaran, alumni, nilai, kelas, izin, pengumuman |
| Dashboard per peran | ✅ Berjalan | 4 dashboard panel + portal wali + portal alumni |
| CRUD inti | ✅ Berjalan | Berita, galeri, prestasi, alumni, struktur, info harian |
| PPDB → akun wali | ✅ Baik | Password acak + email kredensial + wajib ganti 24 jam |
| Import Excel/CSV | ❌ Stub | Tidak memproses file; belum ada package |
| Modul akademik (nilai/transkrip/mapel) | ✅ UI / 🚧 tabel | Kode selesai; tabel menunggu migrasi manusia |
| Halaman publik | ✅ Sebagian | Beberapa view/aset masih hilang |
| Test otomatis | ❌ Minim | Hanya 2 test contoh |
| Dokumentasi | ⚠️ Tidak seragam | Beberapa dokumen masih memuat kondisi lama |

**Skala kode aktual:** 189 route aplikasi, 55 controller, 31 model, 43 file migrasi, 133 file Blade,
7 seeder, 2 test.

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
`Str::password(12)`, mengisi `must_change_password = true` dan `temp_password_expires_at = now()->addDay()`,
lalu mengirim email kredensial (`KredensialMail`). Login pertama dipaksa ke `/ganti-password`;
password sementara yang kedaluwarsa ditolak dan harus direset admin.

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

### 3.4 Belum ada otorisasi per-record (Gate/Policy) — ✅ Selesai sebagian (27 Nov 2026)

**Temuan lama:** tidak ada folder `app/Policies`; pengecekan memakai `Akses::cocokJenjang()` dan
`abort_unless()` manual di controller.

**Implementasi sekarang:**

- `App\Policies\SantriPolicy` dan `App\Policies\NilaiPolicy` dibuat dan didaftarkan eksplisit lewat
  `Gate::policy()` di `AppServiceProvider`. Keduanya memanggil `Akses` sebagai satu-satunya sumber
  keputusan, sehingga hasilnya identik dengan middleware `role:` yang sudah ada.
- Sudah dipakai di `SantriFormController` (create/store/edit/update) dan `NilaiController`
  (store/terbitkan/konteks jenjang). Pemeriksaan lama sengaja **dipertahankan** sebagai lapisan
  kedua sekaligus menjaga pesan error spesifik (misalnya "Jenjang di luar wewenang").
- Modul lain (portal wali/alumni, prestasi, pembinaan) masih memakai pengecekan manual. Adopsi
  policy ke modul tersebut bisa dilakukan bertahap tanpa mengubah perilaku. Lihat
  [14-modul-akademik.md](14-modul-akademik.md) §7.

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
- Jika `email_wali` belum memiliki akun, akun `wali_santri` dibuat dengan password sementara 24 jam
  dan email kredensial dikirim. Kegagalan email tidak membatalkan import dan dilaporkan terpisah.
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
| P2-1 | `santriPutra`/`santriPutri` selalu 0 | `DashboardController.php:132-133` mengirim 0; tabel `santri` tidak punya `jenis_kelamin` | Tambah kolom gender atau hapus widget |
| P2-2 | Prestasi di dashboard Admin Jenjang bersifat global | `RoleDashboardController.php:67` tidak difilter jenjang | Beri label “global” atau tambah kolom jenjang |
| P2-3 | Pengelolaan kepengurusan hilang | Tidak ada controller/route CRUD `kepengurusan`; hanya ditampilkan di `/profil` | Buat CRUD atau pindahkan ke Struktur Organisasi |
| P2-4 | `program` tidak dikelola | `admin.program.index` hanya closure redirect ke `admin.jenjang.index`; view `dashboard/program` tidak terpakai | ✅ Selesai 28 Nov 2026: diputuskan tetap sebagai alias redirect; `Kegiatan` admin diisi dari data nyata (Info Harian + catatan kalender + absensi) |
| P2-5 | Aset gambar publik hilang | Folder `public/images/` untuk sejarah, profil, pembelajaran, program, dan pengurus tidak ada | Tambahkan aset atau ganti path |
| P2-6 | Dua sistem styling | Publik/auth via Vite; admin/portal via Tailwind CDN | Satukan ke Vite |
| P2-7 | Query database di Blade | `beranda`, `profil` | Pindahkan ke controller/view composer |
| P2-8 | Penyimpanan file tidak seragam | Mayoritas disk `public`; sisa `public/upload` | Standarkan disk `public` + `Storage::delete` |
| P2-9 | Audit log baru mencakup aksi penting | `log_aktivitas` + `LogAktivitasController` sudah ada; belum semua perubahan kecil tercatat | Perluas pencatatan ke CRUD sensitif |
| P2-10 | PPDB tanpa throttle/captcha | Form publik bisa spam | ✅ Selesai 27 Nov 2026: `throttle:5,10` pada `POST /pendaftaran`, honeypot `website` (aturan `prohibited`), dan halaman 429 berbahasa Indonesia |
| P2-11 | Email PPDB bergantung mailer | Mailer `log` di lokal | Pastikan SMTP produksi + fallback reset admin |
| P2-12 | `APP_NAME`/locale belum branding | `APP_NAME=Laravel`, locale `en` | Set nama pesantren + locale `id` |
| P2-13 | Tidak ada halaman error kustom | 403/404/500 memakai bawaan | ✅ Sebagian: `errors/429.blade.php` sudah ditambahkan; 403/404/500 menyusul |
| P2-14 | Arsip santri sudah ada, hapus permanen masih mungkin | `santri.arsipkan` + `kembalikan`; `destroy` masih tersedia untuk Superadmin/Admin | Pertimbangkan soft delete & batasi hapus permanen |
| P2-15 | Test minim | Hanya 2 contoh | Tambah feature test untuk login, scoping, izin, nilai, portal |

---

## 5. P3 — Rendah

- `resources/views/welcome.blade.php` (72 KB) dan beberapa controller/import/service 0 byte tidak dipakai.
- `portal/kegiatan.blade.php` kosong.
- `layouts/admin-menu-struktur.blade.php` tidak di-include.
- `resources/views/components/alert.blade.php` dan `card.blade.php` kosong.
- Gambar `logo.png` 3,8 MB perlu dikompresi.
- Sebagian pesan sukses salah konteks (sisa modul lama).
- Penggunaan `rand()`/`Str::random` untuk identifier perlu ditinjau.
- Format tanggal campur `translatedFormat` dan hardcode.
- Sebagian gambar tidak punya `alt` deskriptif.
- Slug berita tidak berubah saat judul diedit (perlu kebijakan redirect).

---

## 6. Ketidaksinkronan Dokumentasi (Doc Drift)

Kode adalah sumber kebenaran. Tabel ini merangkum klaim dokumen yang sudah tidak sesuai.

| Dokumen | Klaim lama | Kondisi kode sekarang |
|---|---|---|
| 01 | 83 route, 30 controller, 15 model, 29 migrasi, 65 Blade, 5 seeder | 189 route, 55 controller, 31 model, 43 migrasi, 133 Blade, 7 seeder |
| 01 | Folder middleware berisi “3 middleware role” | `CheckRole` + `WajibGantiPassword` |
| 02 | 67 route; 20 migrasi; tanpa alias middleware; PPDB tanpa transaksi | 189 route; 43 migrasi; alias `role` ada; verifikasi PPDB memakai `DB::transaction` |
| 03 | `DatabaseSeeder` hanya `RoleAndUserSeeder` + konten; 3 akun | Memanggil 4 seeder + konten; tetap **3 akun inti**, bukan 11 akun dummy |
| 03 | `User::santri()` tidak ada | Ada (`hasMany`) + pivot `santri_wali` |
| 03 | Migrasi 20 file | 43 file |
| 04 | `/admin/pengurus` + `storeKepengurusan` ada | Route dan method tidak ada |
| 04 | `admin.program.index` → `ProgramController@index` | Closure redirect ke `admin.jenjang.index` |
| 05 | Konten menerima semua input tanpa whitelist | Sudah whitelist `items[]`, validasi gambar, layout JSON |
| 05 | Edit pengguna view tidak ada | `dashboard/pengguna/edit.blade.php` sudah ada |
| 05 | CRUD kepengurusan di `KontenController` | Tidak ada |
| 06 | 59 Blade; menu Info Harian tidak ada | 133 Blade; menu Info Harian ada |
| 07 | Portal wali statis; password wali acak `babul####` | Portal dinamis multi-anak; password acak + email + wajib ganti 24 jam |
| 07 | Edit pengguna/detail santri/tambah santri tidak ada | Sudah tersedia |
| 08 | Seeder tambahan tidak dipanggil otomatis; akun `admin@…` | 4 seeder otomatis; 3 akun inti (Superadmin, Pengurus, Pembina) |
| 09 (lama) | Tidak ada rate limit login; PPDB tidak atomik | Login `throttle:6,1`; PPDB transaksi |
| 10 | Info Harian WIP, route belum ada | Route + menu + seeder sudah ada |

**Tindakan:** dokumen 01–08, 10, dan 14 diperbarui bertahap pada 22 November 2026. Dokumen peran
(`role-pengguna.md`, 11, 12) adalah sumber utama untuk 6 role final. Sisa risiko utama ada di
§3.3 (index email PPDB) dan §3.4 (policy per-record).

---

## 7. Matriks Risiko Keamanan

| Risiko | Level | Keterangan |
|---|---|---|
| Nilai/rapor/portal belum punya policy per-record | **Tinggi** | Pemeriksaan manual ada, tetapi wajib diaudit setiap endpoint baru |
| Index unik email PPDB vs fitur multi-anak | **Tinggi** | Perlu verifikasi DB; migrasi drop unique sudah ada |
| `.env` menyimpan kredensial & secret | Tinggi | Rotasi bila pernah dibagikan |
| `APP_DEBUG=true` di produksi | Tinggi | Stack trace terbuka |
| Email kredensial gagal terkirim | Sedang | Fallback reset password admin |
| Mass assignment konten | Sedang | Sudah dibatasi whitelist, tetap audit |
| Upload tanpa validasi MIME penuh | Sedang | Konten sudah divalidasi; modul baru wajib ikut |
| PPDB tanpa captcha/throttle | Sedang | Spam pendaftaran |
| Otorisasi hanya level route + manual | Sedang | Belum ada policy |
| Audit log belum mencakup semua CRUD | Sedang | `log_aktivitas` sudah ada untuk aksi penting |
| XSS pada konten `{!! !!}` | Rendah–Sedang | Hanya admin |
| Tidak ada HTTPS wajib | Sedang | Hanya host ngrok |

---

## 8. Kebutuhan Baru — Modul Akademik

Kebutuhan yang diajukan:

1. Admin Jenjang mengunggah **nilai siswa per kelas**.
2. **Transkrip nilai**.
3. **Jadwal pelajaran**.
4. **Kurikulum**.
5. **Pengumuman** per jenjang.
6. Data tersebut tampil di **akun Wali Santri**.
7. Wali dapat melihat **kegiatan** dan **progres pembelajaran** anak.

Status: **UI dan validasi sudah diimplementasikan**; tabel `mata_pelajaran`, `nilai_santri`, dan
`dokumen_nilai` sudah ditulis sebagai migrasi tetapi **belum dijalankan oleh agen AI**. Rancangan dan
catatan implementasi ada di **[14-modul-akademik.md](14-modul-akademik.md)**.

Implikasi audit:

- Modul ini menambah data pribadi sensitif → **policy per-record wajib** (P1-3.4).
- Unggah massal masih bergantung pada fondasi import yang **stub** (P1-3.5).
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
4. Tambah policy per-record untuk nilai, izin, dokumen, dan portal.
5. Bangun fondasi import CSV/XLSX yang benar.
6. Audit ulang scoping jenjang (termasuk prestasi di dashboard).

### Sprint 2 — Modul Akademik (bertahap)

Mengikuti fase di [14-modul-akademik.md](14-modul-akademik.md) §8:

1. Fase 0: jalankan migrasi (manusia), verifikasi model/relasi, policy.
2. Fase 1: mapel + guru pengampu (sudah ada UI).
3. Fase 2: nilai, terbitkan, transkrip (sudah ada UI); unggah massal masih rencana.
4. Fase 3: portal wali nilai/rapor (sudah ada); jadwal/kurikulum masih rencana.
5. Fase 4: perluas audit log, PDF, test.

### Sprint 3 — Konsolidasi & Kualitas (P2/P3)

1. Sinkronkan dokumentasi 01–08 & 10.
2. Satukan styling ke Vite, pindahkan query dari Blade.
3. Hapus file/tabel/view legacy.
4. Tambah rate limit PPDB, captcha, email konfirmasi.
5. Tambah halaman error kustom, SEO, audit aksesibilitas.
6. Tambah feature test untuk login, scoping jenjang, dan CRUD.

---

## 10. Checklist Verifikasi

### Boleh dijalankan AI (tidak menyentuh database)

- [ ] `php artisan route:list --except-vendor` → 189 route; satu-satunya handler hilang adalah `DashboardController@settings` (P0-3)
- [ ] `vendor\bin\pint`
- [ ] `npm run build`
- [ ] `php artisan view:clear` / `config:clear` / `route:clear`

### Hanya manusia — AI dilarang ([ATURAN.md](ATURAN.md))

- [ ] `php artisan migrate:status` → semua migrasi `Ran`
- [ ] Verifikasi index unik `pendaftaran_online.email`
- [ ] `php artisan migrate` (termasuk `info_harian`, tabel alur pesantren, dan akademik)
- [ ] `php artisan db:seed --class=RoleAndUserSeeder` (bila perlu akun uji)
- [ ] `php artisan test`
- [ ] Uji manual P0: `/kegiatan`, `admin.kegiatan.index`, `super.settings`
- [ ] Uji P1: login dengan role lama `pengasuh`/`super` → harus dinormalkan
- [ ] Uji P1: pendaftaran dua anak dengan satu email
- [ ] Uji alur: PPDB diterima → email kredensial → wajib ganti password
- [ ] Uji alur: izin pulang (setujui/tolak/terlambat/selesai)
- [ ] Uji alur: naik kelas + rollback Superadmin
- [ ] Uji alur: kelulusan → akun alumni → portal alumni

---

## 11. Lampiran Bukti

| Temuan | Lokasi |
|---|---|
| 189 route aplikasi & 3 route rusak | `routes/web.php`; `php artisan route:list --except-vendor` |
| Password sementara + email kredensial | `app/Http/Controllers/Admin/PendaftaranController.php`; `app/Mail/KredensialMail.php` |
| Normalisasi role | `app/Support/Akses.php`; `app/Http/Middleware/CheckRole.php` |
| Index unik email | `database/migrations/2026_08_24_203449_add_email_to_pendaftaran_online_table.php`; `2026_11_17_000002_drop_unique_email_on_pendaftaran_online_table.php` |
| Validasi email publik | `app/Http/Controllers/Public/PendaftaranOnlineController.php` |
| Import stub | `app/Http/Controllers/Admin/SantriController.php` |
| Kepengurusan tanpa CRUD | `grep` controller; hanya model + tampilan `/profil` |
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

Catatan: perbaikan di atas **tidak menghapus** temuan P1 yang masih terbuka (index email PPDB,
policy per-record, import stub). Lihat §3.

