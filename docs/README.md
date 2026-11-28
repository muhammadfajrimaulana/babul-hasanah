# Dokumentasi Ekosistem — Pondok Pesantren Babul Hasanah

Folder ini berisi hasil analisis menyeluruh aplikasi **Sistem Informasi Pondok Pesantren Babul Hasanah**.
Dokumentasi disusun berdasarkan pembacaan langsung kode, konfigurasi, migrasi, dan tampilan
yang ada di repositori (bukan asumsi dari README bawaan Laravel).

> [!IMPORTANT]
> **Baca [ATURAN.md](ATURAN.md) lebih dulu.** Aturan tersebut melarang agen AI menjalankan perintah
> yang menyentuh database (`migrate`, `db:seed`, `tinker`, `migrate:status`, `php artisan test`, SQL
> destruktif, dan sejenisnya). Semua contoh perintah database di dokumentasi ini ditujukan **untuk
> dijalankan manusia**, bukan oleh AI.

- **Tanggal analisis awal:** 31 Oktober 2026
- **Pembaruan terakhir:** 28 November 2026 (checklist per menu, jadwal/kurikulum/rapor, Google OAuth, test scoping, peta tampilan per menu, query beranda/profil dipindah ke controller, data 7 jenjang dipusatkan, fallback gambar publik, tautan Blade memakai named route, pembersihan 16 file 0 byte, halaman error 403/404/500, feature test login/reset password, verifikasi PPDB, nilai/izin & soft delete santri, CRUD kepengurusan, aset placeholder `public/images/`, alt gambar deskriptif, panduan verifikasi manusia, satukan styling Vite, kompresi aset, panduan praktis role dan jalur akses, pembersihan view/controller mati)
- **Status working tree:** ada perubahan yang belum di-commit; folder `docs/` di-*ignore* Git sehingga perubahan dokumentasi tidak muncul di `git status`
- **Framework:** Laravel 13.29 (requirement `^13.17`), PHP 8.3+ (runtime terpasang PHP 8.5.5)
- **Basis data:** MySQL `babul_hasanah` (XAMPP)
- **Skala kode saat ini:** 232 route aplikasi, 57 file controller, 34 model, 50 file migrasi, 144 file Blade

## Daftar Dokumen

| No | Dokumen | Isi |
|----|---------|-----|
| — | [ATURAN.md](ATURAN.md) | **Aturan wajib**: larangan akses/mengubah database oleh AI |
| 1 | [01-gambaran-umum.md](01-gambaran-umum.md) | Profil proyek, tech stack, 6 peran pengguna, peta direktori |
| 2 | [02-arsitektur.md](02-arsitektur.md) | Arsitektur aplikasi, alur request, lapisan kode, diagram |
| 3 | [03-database.md](03-database.md) | ERD, seluruh tabel, relasi model, seeder, status migrasi |
| 4 | [04-routing-dan-otorisasi.md](04-routing-dan-otorisasi.md) | Daftar route aplikasi, middleware `role`, matriks hak akses |
| 5 | [05-backend.md](05-backend.md) | Controller, model, middleware, validasi, upload file |
| 6 | [06-frontend.md](06-frontend.md) | Layout, halaman publik/admin/portal, desain, aset, build |
| 7 | [07-fitur.md](07-fitur.md) | Alur setiap fitur + status implementasi |
| 8 | [08-instalasi-dan-operasional.md](08-instalasi-dan-operasional.md) | Setup lokal, perintah artisan, seeding, deployment |
| 9 | [09-audit-dan-rekomendasi.md](09-audit-dan-rekomendasi.md) | Temuan bug, celah keamanan, utang teknis, roadmap |
| 10 | [10-panduan-kontribusi.md](10-panduan-kontribusi.md) | Konvensi kode & langkah menambah modul baru |
| 11 | [11-role-dan-login.md](11-role-dan-login.md) | **6 peran**, alur login, redirect per role, middleware, matriks hak akses |
| 12 | [12-halaman-per-role.md](12-halaman-per-role.md) | **Dashboard per role**, menu sidebar/portal, scoping jenjang, checklist uji |
| 13 | [role-pengguna.md](role-pengguna.md) | **Panduan role pengguna**: 6 peran, 3 akun inti, alur login, dashboard, pengelolaan akun & wali, troubleshooting |
| 14 | [14-modul-akademik.md](14-modul-akademik.md) | **Modul akademik**: nilai, transkrip, mapel, jadwal (anti-bentrok), kurikulum + item, rapor rekap (rata-rata/peringkat/catatan wali), portal wali; kode siap, tabel baru menunggu migrasi manusia |
| 15 | [15-email-santri-akun-alumni.md](15-email-santri-akun-alumni.md) | **Email santri → username akun alumni**: kolom baru, alur PPDB, sinkronisasi kelulusan, dan pengiriman kredensial |
| 16 | [16-checklist-per-menu.md](16-checklist-per-menu.md) | **Checklist kerja per menu**: kondisi terkini, yang masih kurang, prioritas, dan urutan perbaikan untuk sesi berikutnya |
| 17 | [17-google-oauth.md](17-google-oauth.md) | **Google Login**: langkah Google Cloud Console, `GOOGLE_CLIENT_ID`/`GOOGLE_CLIENT_SECRET`, callback, `composer install`, dan batasan akun harus sudah terdaftar |
| 18 | [18-peta-tampilan-per-menu.md](18-peta-tampilan-per-menu.md) | **Peta tampilan per menu**: tabel menu → route → controller → file Blade untuk publik, auth, admin, portal wali, alumni, email/error, plus alias & view tanpa route |
| 19 | [19-panduan-verifikasi-manusia.md](19-panduan-verifikasi-manusia.md) | **Panduan verifikasi manusia**: urutan `migrate`/test/build, jumlah method test, checklist UI per role, dan format lapor hasil |
| 20 | [20-panduan-role-dan-jalur.md](20-panduan-role-dan-jalur.md) | **Panduan praktis role & jalur akses**: kenapa `pengasuh` masih ada di migrasi, 6 role final, asal akun, redirect login, matriks modul, dan batas data |

## Ringkasan Cepat

Aplikasi ini adalah **website profil pesantren + PPDB online + panel operasional + portal wali + portal alumni**
dalam satu proyek Laravel monolitik.

### Enam role final

| Role | Label | Area |
|------|-------|------|
| `super_admin` | Superadmin | `/admin/*` (semua modul), `/super/pengguna`, log aktivitas |
| `pengurus` | Pengurus | `/admin/dashboard-pengurus` + monitoring & pengumuman |
| `pembina` | Pembina | `/admin/dashboard-pembina` + pembinaan, izin, monitoring |
| `admin` | Admin Jenjang | `/admin/dashboard-jenjang` + operasional **satu jenjang** |
| `wali_santri` | Wali Santri | `/portal/*` (hanya anak sendiri) |
| `alumni` | Alumni | `/alumni/*` (hanya data sendiri) |

Tidak ada role **Bendahara** dan tidak ada modul keuangan. Role lama `pengasuh` dinormalkan menjadi
`pengurus`. Guru/Wali Kelas **tidak memiliki akun login**; data guru dikelola Admin Jenjang dan
namanya tampil pada halaman Nilai.

### Fitur utama

- **Publik:** beranda, profil (sejarah, visi-misi, struktur, pembelajaran), 7 halaman jenjang,
  berita, galeri album, prestasi, alumni, info harian, pendaftaran online, kontak.
- **PPDB:** wali wajib mengisi jenjang + email/WA. Pendaftar diterima otomatis menjadi santri,
  dibuatkan akun wali, password sementara 24 jam, dan email kredensial. Login pertama wajib ganti password.
- **Admin Jenjang:** verifikasi PPDB, data santri, naik kelas/rollback, pindah jenjang, arsip,
  kelulusan, alumni, nilai/hafalan/presensi, izin, pengumuman jenjangnya, kelas & guru, rekap wali kelas.
- **Pengurus:** monitoring lintas jenjang dan pengumuman; **Pembina:** pembinaan harian (hafalan,
  pelanggaran, presensi, izin) tanpa akses tulis data induk.
- **Superadmin:** semua modul + akun pengguna + log aktivitas + rollback kenaikan kelas.
- **Wali Santri:** portal multi-anak (satu akun bisa banyak anak), nilai/rapor, prestasi, izin pulang,
  pengumuman, pengajuan perubahan data, dan dokumen kelulusan.
- **Alumni:** dashboard profil, nomor ijazah, kelas/kamar terakhir, history nilai & pelanggaran,
  presensi, dokumen, tracer study, pengumuman, dan pengajuan legalisir. History terkunci.

### Kondisi Singkat

| Aspek | Kondisi |
|-------|---------|
| Autentikasi | Berjalan (login email/password, **6 role**, status aktif, password sementara 24 jam) |
| Otorisasi | Middleware `CheckRole` + `App\Support\Akses`; Policy (`SantriPolicy`, `NilaiPolicy`) sudah terpasang dan dipakai di modul santri & nilai, modul lain menyusul |
| Dashboard per peran | **Berjalan**: Superadmin, Pengurus, Pembina, Admin Jenjang, Wali Santri, Alumni |
| Batas data per jenjang | **Berjalan** untuk santri, pendaftaran, alumni, nilai, kelas, pengumuman, dan izin (role `admin`) |
| PPDB | Berjalan sampai verifikasi + pembuatan santri + akun wali + email kredensial; email wali boleh multi-anak, email santri unik, form dilindungi throttle + honeypot |
| Portal wali | Dinamis, multi-anak, mendukung mode riwayat setelah anak lulus |
| Portal alumni | Berjalan: profil, nilai, dokumen, legalisir, tracer study, pengumuman |
| Naik kelas | Berjalan + rollback khusus Superadmin dengan alasan & log |
| Hapus santri | Soft delete + Tempat Sampah; pemulihan khusus Superadmin; `forceDelete` tidak diekspos ke UI |
| Kepengurusan | CRUD Superadmin di `/admin/kepengurusan`; kartu `/profil` dari tabel `kepengurusan`; foto lama tetap terbaca |
| Aset gambar publik | 14 placeholder di `public/images/program/` (banner 1200×400) dan `public/images/pengurus/` (foto ketua 320×320), total 230 KB; fallback `GambarPublik` tetap aktif. Belum foto resmi |
| Info Harian | **Berjalan**: CRUD via menu Superadmin + tampil di beranda |
| Import santri | **Berjalan via CSV native** (tanpa package): validasi per baris, laporan error, template CSV dinamis; XLSX tidak didukung |
| Modul akademik | **Kode siap** (nilai, transkrip, mapel, jadwal anti-bentrok, kurikulum + item, rapor rekap, portal wali); tabel baru menunggu migrasi oleh manusia — lihat [14-modul-akademik.md](14-modul-akademik.md) |
| Notifikasi nilai wali | Indikator in-app di dashboard portal (tanpa WhatsApp gateway) |
| Google Login | **Kode siap** via Socialite; tombol muncul setelah kredensial diisi. Butuh `composer install` + env — lihat [17-google-oauth.md](17-google-oauth.md) |
| Test | 2 contoh bawaan + 13 feature test (`ScopingJenjangTest`, `PortalKepemilikanAnakTest`, `LoginDanResetPasswordTest`, `VerifikasiPPDBTest`, `NilaiDanIzinTest`, `SoftDeleteSantriTest`, `KepengurusanCrudTest`, `GambarPublikTest`, `PencarianSantriTest`, `SlugBeritaTest`, `GaleriStorageTest`, `IdentifierUnikTest`, `PesanFlashTest`) + 7 unit test (`AltGambarTest`, `LocaleTanggalTest`, `PathGambarStorageTest`, `NamaAplikasiTest`, `EnvExampleTest`, `ViewMatTest`, `DokumenSinkronTest`); dijalankan manusia, lihat [16-checklist-per-menu.md](16-checklist-per-menu.md) |

### Peringatan Awal

Route error 500 sudah dituntaskan: `/kegiatan` kini dialihkan ke `/info-harian`, sementara
`admin.kegiatan.index` dan `super.pengaturan-sistem` ternyata sudah memiliki view/method. Lihat
[09-audit-dan-rekomendasi.md](09-audit-dan-rekomendasi.md). Temuan lama seperti detail santri, edit
pengguna, Info Harian, dan pemanggilan seeder juga sudah diperbaiki — daftarnya ada di §12 dokumen audit.

> **Catatan migrasi:** seluruh perubahan skema ditulis sebagai file migrasi dan **tidak dijalankan
> oleh agen AI**. Status “sudah/belum dimigrasi” harus diverifikasi manusia. Dokumentasi ini
> membedakan tegas antara “kode tersedia” dan “tabel sudah ada di database”.
