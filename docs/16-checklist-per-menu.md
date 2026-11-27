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

---

## 1. Website Publik

| Menu | Kondisi | Yang kurang / rekomendasi | Prioritas |
|---|---|---|---|
| Beranda | ⚠️ | Widget `santriPutra`/`santriPutri` selalu 0 karena `santri` belum punya `jenis_kelamin`. Pilih: tambah kolom gender **atau** hapus widget. Query DB masih ada di Blade. | P2 |
| Profil (Sejarah, Visi-Misi, Pembelajaran, Fasilitas) | ⚠️ | Sebagian besar konten hardcode di Blade dengan fallback `Konten`. Rapikan ke tabel `konten` agar bisa diedit Superadmin. Gambar `public/images/` tidak ada. | P2/P3 |
| Struktur Organisasi | ✅ | Publik + CRUD admin. Kepengurusan tidak punya CRUD terpisah; cukup dikelola lewat Struktur atau dibuat modul baru. | P2 |
| 7 Halaman Jenjang | ✅ | Blade per slug (`mdta` … `mahad-aly`). Konten masih default di Blade; belum lewat `Konten`. | P3 |
| Berita | ✅ | Slug tidak ikut berubah saat judul diedit → perlu kebijakan redirect 301 bila diinginkan. | P3 |
| Galeri | ⚠️ | CRUD create/delete berjalan. Penyimpanan masih campur disk `public` dan `public/upload`; standarkan ke `Storage::disk('public')`. | P2 |
| Prestasi | ⚠️ | Tampil publik. Data admin jenjang belum menyimpan `jenjang`, sehingga prestasi tampak global. Tambah kolom jenjang atau beri label "global". | P2 |
| Alumni (publik) | ✅ | Filter kegiatan & instansi berjalan. Daftar kegiatan masih konstanta statis. | P3 |
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
| Google Login | ❌ | Tombol masih `href="#"`. Putuskan: hapus tombol atau implementasikan Socialite. | P2 |
| Profil saya | ✅ | Nama, email, password, avatar. Role tidak bisa diubah di sini (by design). | — |
| Pengguna (Superadmin) | ✅ | CRUD + normalisasi role + jaga "superadmin terakhir". Perluas log untuk perubahan role. | P2 |
| Pengaturan sistem | ✅ | Hanya informasi aplikasi dari `config()`. Perubahan `.env` sengaja lewat server. | — |

---

## 3. Panel Admin

| Menu | Kondisi | Yang kurang / rekomendasi | Prioritas |
|---|---|---|---|
| Dashboard | ⚠️ | Widget gender salah (lihat Beranda). Banyak query di Blade; pindahkan ke controller/view composer. | P2 |
| Dashboard Jenjang | ⚠️ | Ringkasan prestasi belum discoping per jenjang. | P2 |
| Pendaftaran | ✅ | Verifikasi → santri + akun wali + email. Email wali kini multi-anak. Pastikan migrasi `drop_unique_email` aktif. | P1 |
| Data Santri | ✅ | CRUD, naik kelas + rollback, pindah jenjang, arsip, lulus tunggal/massal, import CSV, detail. Masih ada hapus permanen untuk Superadmin → pertimbangkan hapus/soft delete. XLSX belum didukung. | P2 |
| Data Alumni | ✅ | Tarik santri lulus, buat akun massal, reset password, detail, kartu kredensial. Perlu catatan audit untuk reset password & pembuatan akun massal. | P2 |
| Akademik — Mata Pelajaran | ✅ | CRUD + guru pengampu. Jadwal pelajaran & kurikulum belum ada. | P1/P2 |
| Akademik — Nilai | ✅ | Input `draft` → `published`, transkrip, cetak/unggah rapor. **Import nilai massal belum ada** — bisa memakai pola `ImportSantri`. Belum ada kebijakan siapa yang boleh membuka nilai `published`. | P1 |
| Akademik — Transkrip | ✅ | Cetak per santri. Perlu ekspor PDF resmi bila dibutuhkan. | P2 |
| Pembinaan | ✅ | Hafalan, pelanggaran, presensi kegiatan. Policy belum dipakai di sini (masih manual). | P2 |
| Izin Pulang | ✅ | Status + kode + auto terlambat. Policy belum dipakai. | P2 |
| Pengumuman | ✅ | Target per jenjang; Pengurus menulis semua jenjang (by design). Pastikan sesuai kebijakan. | P2 |
| Kelas & Guru | ✅ | Master kelas, guru tanpa akun login, rekap wali kelas. | — |
| Pengajuan Perubahan | ✅ | Antrean dari wali + verifikasi. Policy belum dipakai. | P2 |
| Legalisir | ✅ | Antrean alumni. Policy belum dipakai. | P2 |
| Prestasi | ⚠️ | CRUD. Tambah `jenjang` agar Admin Jenjang tidak melihat data global. | P2 |
| Jenjang | ✅ | 7 submenu, detail per jenjang. Konten sebagian statis. | P3 |
| Berita & Galeri | ✅ | Seragamkan penyimpanan file. | P2 |
| Konten / Struktur / Info Harian | ✅ | Khusus Superadmin. Lengkapi aset gambar publik. | P2 |
| Log Aktivitas | ✅ | Sudah ada. **Belum mencatat** import CSV, lulus massal, buat akun massal, reset password. | P2 |
| Program | ✅ | Route `admin.program.index` sengaja dipertahankan sebagai alias/redirect ke `admin.jenjang.index` (keputusan: tidak dibuat CRUD). Tidak ada menu sidebar. | P3 |
| Kegiatan (admin) | ✅ | Halaman monitoring baca-saja: jadwal aktif dari Info Harian, catatan kalender pribadi, dan absensi santri sesuai batas jenjang. Tidak ada tabel `kegiatan` baru; tidak ada menu sidebar. | P3 |

---

## 4. Portal Wali Santri

| Menu | Kondisi | Yang kurang / rekomendasi | Prioritas |
|---|---|---|---|
| Beranda | ✅ | Multi-anak + indikator notifikasi nilai. Policy portal belum dipakai. | P2 |
| Nilai Santri | ✅ | Hanya nilai `published`. Perlu test scoping "anak sendiri". | P1 |
| Prestasi | ✅ | Per anak. | — |
| Pengumuman | ✅ | Per jenjang. | — |
| Izin Pulang | ✅ | Ajukan + lihat status. | — |
| Ajukan Perubahan | ✅ | Form + riwayat. | — |
| Dokumen | ✅ | Unduh dokumen anak. | — |
| Mode Riwayat | ✅ | Anak lulus tetap bisa dilihat wali. | — |
| Jadwal & Kurikulum | 🚧 | Belum ada di portal (lihat docs/14 Fase 3). Putuskan dulu apakah dibangun. | P2 |

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
| Policy | ✅ sebagian | Baru `SantriPolicy` & `NilaiPolicy`. Menyusul: portal wali/alumni, pembinaan, izin, legalisir, prestasi. | P2 |
| Import | ✅ sebagian | Santri CSV sudah nyata. Nilai massal belum; XLSX tidak didukung (ekspor ke CSV dulu). | P1 |
| Email / kredensial | ⚠️ | Mailer `log` di lokal. Produksi wajib SMTP. Sediakan fallback admin bila email gagal. | P2 |
| Audit log | ⚠️ | Perluas ke aksi sensitif: import, lulus massal, buat akun massal, reset password, ubah role. | P2 |
| Test | 🚧 | Baru 2 test bawaan. Tambah feature test: login per role, scoping jenjang, kepemilikan anak, verifikasi PPDB, reset password. (AI tidak boleh menjalankannya.) | P1 |
| Styling | ⚠️ | Publik/auth via Vite; admin/portal via Tailwind CDN. Satukan bertahap. | P2 |
| Halaman error | ⚠️ | `errors/429.blade.php` sudah ada. 403/404/500 masih bawaan Laravel. | P3 |
| Aset | ⚠️ | `logo.png` ~3,8 MB perlu dikompresi; `public/images/` hilang; beberapa file controller/view 0 byte bisa dibersihkan. | P3 |
| Dokumentasi 01–08, 10 | ⚠️ | Masih banyak klaim lama (jumlah route, status modul). Doc 09/12/14/15/16 yang terbaru. | P3 |

---

## 7. Urutan Disarankan untuk Sesi Berikutnya

1. ~~**Rapikan menu mati:** `Program` (redirect) dan `Kegiatan` admin (placeholder).~~ ✅ **Selesai 28 Nov 2026** — `Program` tetap alias redirect; `Kegiatan` kini menampilkan data nyata. Berikutnya: nomor 2.
2. **Scoping data:** prestasi per jenjang + widget gender di dashboard.
3. **Audit log** untuk aksi sensitif yang baru ditambahkan (import, lulus massal, akun massal, reset password).
4. **Perluas Policy** ke portal wali/alumni, pembinaan, izin, legalisir.
5. **Import nilai massal CSV** memakai pola `App\Support\ImportSantri` + pratinjau error.
6. **Keputusan produk yang masih terbuka** (dari docs/14 §9):
   - Jadwal pelajaran & kurikulum dibangun atau cukup statis?
   - Perlu tabel `rapor_santri` (rata-rata/peringkat/catatan wali kelas)?
   - Siapa boleh membuka nilai `published`?
   - Google Login dipasang atau tombolnya dihapus?
7. **Pengerasan akhir:** SMTP produksi, test feature, satukan styling, kompresi aset, error 403/404/500.

## 8. Cara Pakai Checklist Ini

- Awali sesi dengan: "Buka `docs/16-checklist-per-menu.md`, kerjakan item nomor X."
- Agen cukup membaca file terkait di kolom konteks/docs, lalu mengubah kode.
- **Jangan** menjalankan perintah database/test; serahkan ke manusia.
- Setelah selesai, perbarui tanda status di dokumen ini dan di `docs/09-audit-dan-rekomendasi.md`.
