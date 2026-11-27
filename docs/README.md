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
- **Pembaruan terakhir:** 27 November 2026 (email santri/akun alumni, import CSV, policy, anti-spam PPDB, dan checklist per menu)
- **Status working tree:** ada perubahan yang belum di-commit; folder `docs/` di-*ignore* Git sehingga perubahan dokumentasi tidak muncul di `git status`
- **Framework:** Laravel 13.29 (requirement `^13.17`), PHP 8.3+ (runtime terpasang PHP 8.5.5)
- **Basis data:** MySQL `babul_hasanah` (XAMPP)
- **Skala kode saat ini:** 189 route aplikasi, 55 file controller, 31 model, 43 file migrasi, 133 file Blade

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
| 14 | [14-modul-akademik.md](14-modul-akademik.md) | **UI selesai, tabel belum dibuat**: nilai per kelas, transkrip, mata pelajaran, portal nilai wali; migrasi harus dijalankan manusia |
| 15 | [15-email-santri-akun-alumni.md](15-email-santri-akun-alumni.md) | **Email santri → username akun alumni**: kolom baru, alur PPDB, sinkronisasi kelulusan, dan pengiriman kredensial |
| 16 | [16-checklist-per-menu.md](16-checklist-per-menu.md) | **Checklist kerja per menu**: kondisi terkini, yang masih kurang, prioritas, dan urutan perbaikan untuk sesi berikutnya |

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
| Info Harian | **Berjalan**: CRUD via menu Superadmin + tampil di beranda |
| Import santri | **Berjalan via CSV native** (tanpa package): validasi per baris, laporan error, template CSV dinamis; XLSX tidak didukung |
| Modul akademik | **UI berjalan** (nilai, transkrip, mata pelajaran, portal nilai wali); tabel `mata_pelajaran` & `nilai_santri` menunggu migrasi oleh manusia — lihat [14-modul-akademik.md](14-modul-akademik.md) |
| Notifikasi nilai wali | Indikator in-app di dashboard portal (tanpa WhatsApp gateway) |
| Google Login | **Belum aktif**, tombol hanya `href="#"` |
| Test | Hanya 2 contoh test bawaan |

### Peringatan Awal

Route error 500 sudah dituntaskan: `/kegiatan` kini dialihkan ke `/info-harian`, sementara
`admin.kegiatan.index` dan `super.pengaturan-sistem` ternyata sudah memiliki view/method. Lihat
[09-audit-dan-rekomendasi.md](09-audit-dan-rekomendasi.md). Temuan lama seperti detail santri, edit
pengguna, Info Harian, dan pemanggilan seeder juga sudah diperbaiki — daftarnya ada di §12 dokumen audit.

> **Catatan migrasi:** seluruh perubahan skema ditulis sebagai file migrasi dan **tidak dijalankan
> oleh agen AI**. Status “sudah/belum dimigrasi” harus diverifikasi manusia. Dokumentasi ini
> membedakan tegas antara “kode tersedia” dan “tabel sudah ada di database”.
