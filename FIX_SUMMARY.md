# Fix Summary - Sistem Informasi Pondok Pesantren Babul Hasanah

## Code Edits Performed (AI Allowed per ATURAN.md)

### 1. Routes - routes/web.php
- **P0-1**: Kommentari route `GET /kegiatan` yang menampilkan view yang tidak ada (error 500)
- **P0-2**: Kommentari route `admin.kegiatan.index` yang menampilkan view yang tidak ada
- **P0-3**: Route `super.settings` sudah tidak error karena method `settings()` ditambahkan ke controller

### 2. Dashboard Controller - app/Http/Controllers/Admin/DashboardController.php
- **P0-3**: Menambahkan method `settings()` di `DashboardController`
- Method mengarahkan ke `admin.dashboard` dengan pesan info
- Tidak memerlukan view `dashboard/settings.blade.php` yang belum dibuat

### 3. CheckRole Middleware - app/Http/Middleware/CheckRole.php
- **P1-2**: Diperbaiki `variasi()` menggunakan HANYA `Akses::normalisasi()` sebagai sumber kebenarannya
- Sebelumnya mengadorkan `RoleSistem::normalisasi()` dan `Akses::normalisasi()` yang memiliki perilaku berbeda
- Perbaikan ini memastikan peran lama (`pengurus`, `super`, `wali`) dan Spatie (`super-admin`, `admin-jenjang`) secara konsisten dipetakan ke kolom `users.role`
- Fixed issue: akun lama login berhasil tetapi 403 di semua route

---

## Issues Requiring Human Action (ATURAN.md - Prohibited for AI)

### DATABASE MIGRATIONS (Wajib Dijalankan Manusia)

1. **Migrasi info_harian** - Tabel `info_harian` belum ada
   - File migrasi sudah ada di `database/migrations/`
   - Harus dieksekusi: `php artisan migrate`
   - *AI dilarang: lihat ATURAN.md*

2. **Status migrasi** - Verify 19 sudah run, 15 pending
   - `php artisan migrate:status`
   - *AI dilarang*

3. **Migrasi akademik** (docs/14) - 7 migrasi baru
   - Tabel: mata_pelajaran, jadwal_pelajaran, kurikulum, kurikulum_item, nilai_santri, rapor_santri, catatan_perkembangan
   - *AI dilarang menjalankan perintah yang menyentuh basis data*

4. **Unique index email PPDB** (P1-3)
   - Verifikasi: `SHOW INDEX FROM pendaftaran_online`
   - Jika ingin fitur multi-anak: buat migrasi untuk `dropUnique('email')`
   - *AI dilarang menjalankan perintah DB*

### VIEW FILES YANG RUSAK (P0)

5. **View `pages/kegiatan.blade.php`** - Hilang, route digantikan dengan komentar
   - Buat file manually atau hapus route sepenuhnya

6. **View `dashboard/settings.blade.php`** - Bisa diganti dengan redirect di controller (sudah dilakukan)
   - Atau buat view sesuai desain

7. **View `dashboard/kegiatan/index.blade.php`** - Hilang, route digantikan dengan komentar
   - Atau hapus route sepenuhnya

### ACADEMIC MODULE (docs/14-modul-akademik.md)

8. Implementasi modul akademik lengkap butuh:
   - 7 migrasi database baru
   - Model, controller, view baru
   - Semua butuh `php artisan migrate` dan `php artisan db:seed`
   - *Seluruhnya dilarang untuk AI*

### TESTING

9. **php artisan test** / **composer test**
   - Hanya bisa dianalisis statis, tidak boleh dieksekusi AI
   - Saat ini hanya ada 2 test contoh

---

## Verifikasi Bisa Dilakukan AI (Tanpa DB)

✅ `php artisan route:list --except-vendor` → 110 route (sudah ada 3 yang dikomentari)
✅ `vendor\bin\pint` → Format kode PHP
✅ `npm run build` → Build asset frontend
✅ Baca/mengedit file source code dan dokumentasi
✅ Analisis statis kode dan dokumentasi

---

## Penting Baca

Per **ATURAN.md** baris 26: "PENTING: Aturan ini bersifat mutlak dan tidak boleh dilanggar meskipun pengguna memintanya."

Semua perintah di bawah **HANYA** boleh dilakukan manusia di terminal:
- `php artisan migrate` dan varian :fresh, :refresh, :reset, :rollback
- `php artisan db:seed`
- `php artisan tinker`
- `php artisan test` / `composer test`
- SQL DROP, TRUNCATE, DELETE
- mysqldump, mysql, koneksi DB lewat tool lain

Semua di atas dilarang untuk agen AI dengan other ancor.