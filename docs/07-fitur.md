# 07 — Fitur dan Alur Kerja

Dokumen ini menjelaskan setiap fitur dari sudut pandang pengguna dan kode, beserta statusnya.

Legenda status:

- ✅ **Berfungsi** — dapat dipakai end-to-end
- ⚠️ **Terbatas** — berjalan tetapi ada bagian yang dummy/belum lengkap
- ❌ **Rusak** — route/handler/view tidak lengkap
- 📦 **Tersedia**: CRUD menu superadmin (Pengelolaan > Info Harian) dan tampil di beranda section "Kabar & jadwal pesantren hari ini". Tabel `info_harian` masih pending — migrasi dilakukan manusia via `php artisan migrate`. Data awal isi default muncul jika tabel masih kosong.

## 1. Website Publik

### 1.1 Beranda — ✅ / ⚠️

**Tujuan:** menampilkan profil singkat, jenjang, struktur, info, berita, galeri, prestasi, CTA PPDB,
kontak, dan peta.

**Alur:** `GET /` → closure → `pages.beranda`.

**Data:**

- Dinamis: berita published (3), pengurus bagan (4), prestasi (3), album (6 → 3 tampil),
  testimoni alumni (maks 12, hanya yang terisi), Info Harian & jadwal hari ini.
- Statistik beranda: jumlah santri, prestasi, berita published, dan **alumni** (bukan info harian).
- Statis: teks profil, kebutuhan kontak. Section **Kehidupan Santri** sudah dihapus dan diganti
  section **Info Harian** berbentuk kotak hijau dengan panel “Jadwal Hari Ini” berwarna putih.

**Catatan:** redesign besar sedang berjalan (uncommitted); library Swiper sudah dihapus dari view
tetapi sisa CSS/JS handler-nya masih ada.

### 1.2 Profil — ✅ (halaman) / ⚠️ (konten)

**Tujuan:** halaman profil yang mengambil teks dari tabel `konten` + dewan pengasuh.

**Alur:** `GET /profil` → closure → `pages.profil`.

**Data:** `Konten::getVal()` untuk 7 key, `Kepengurusan` untuk daftar pengasuh.

**Catatan:** hanya halaman `/profil` yang memakai `konten`; sub-halaman sejarah/visi-misi statis.
`sejarah_foto` tidak bisa diunggah dari dashboard.

### 1.3 Sejarah — ✅ statis

**Alur:** `GET /profil/sejarah` → `pages.sejarah`.

**Data:** array `$tonggak`, `$pengasuh`, `$budaya`, `$kajian`, `$jadwal` di file. Gambar mengarah ke
`public/images/sejarah/*` yang **tidak tersedia**.

### 1.4 Visi & Misi — ✅ statis

**Alur:** `GET /profil/visi-misi` → `pages.visi-misi`.

**Data:** `$visi`, `$misi`, `$nilai` hardcoded. Gambar dari `public/images/profil/*` tidak tersedia.

### 1.5 Pembelajaran — ✅ statis

**Alur:** `GET /profil/pembelajaran` → `pages.pembelajaran`.

**Data:** `$jalur`, `$kajian`, `$jalurDiniyah`, `$jalurMadrasah`, `$metode`, `$jadwal`.
Gambar dari `public/images/pembelajaran/*` tidak tersedia.

### 1.6 Struktur Organisasi Publik — ✅

**Alur:** `GET /profil/struktur-organisasi` → `Public\StrukturOrganisasiController@index`.

**Data:**

- Bagan: tree aktif kategori `bagan` (rekursif)
- Ketua jenjang: kategori `jenjang` aktif
- Bidang: kategori `bidang` aktif
- Jumlah pengurus

**View:** `pages/struktur-organisasi` + `partials/struktur-node` (rekursif, 1 query).

### 1.7 Halaman Jenjang — ✅ statis

**Alur:** `GET /jenjang/{slug}` dengan 7 slug valid.

**Data:** setiap file mendefinisikan array `$p` (nama, deskripsi, kurikulum, fasilitas, foto, dll.)
lalu meng-include `pages/jenjang/_detail`.

**Catatan:** seluruh konten hardcoded; tidak terhubung ke tabel `program`. Gambar
`public/images/program/*` tidak tersedia.

### 1.8 Berita — ✅

**Daftar:**

- `GET /berita` → `Public\BeritaController@index`
- Headline 1, sub-headline 2, populer 4, indeks 6/halaman
- Hanya status `published` dan `published_at <= now()`

**Detail:**

- `GET /berita/{slug}` → route model binding `{berita:slug}`
- 404 jika belum published
- `views` bertambah setiap kunjungan
- 3 berita terkait

**Admin:** CRUD lengkap dengan upload gambar, slug otomatis, draft/publish, penjadwalan.

### 1.9 Galeri — ✅

**Publik:**

- `GET /galeri` → `Public\GaleriController@index`
- Album diurut tanggal terbaru
- Media: foto, video upload, link YouTube/TikTok/Instagram
- Deteksi platform otomatis di view + embed
- Fallback album demo jika data kosong

**Admin:**

- `GET /admin/pengelolaan-galeri` — daftar album + jumlah foto/video
- `GET .../tambah` — form album + multi upload
- `POST ...` — simpan album & media
- `DELETE .../{galeri}` — hapus album + semua file

**Belum ada:** edit album, hapus media satuan, ubah urutan, ganti sampul.

### 1.10 Prestasi — ✅

**Publik:** `GET /prestasi` → daftar semua prestasi, filter kategori/tahun di klien, urut tahun
terbaru + juara 1–3 dulu, penghargaan di akhir.

**Admin:** CRUD lengkap (`PrestasiAdminController`), label otomatis.

### 1.11 Kontak — ✅ statis

`GET /kontak` → `pages.kontak` dengan alamat, telepon, WA, email, FAQ, dan peta Google Maps embed.
Data kontak hardcoded di view (tidak dari database).

### 1.12 Kegiatan Publik — ❌

`GET /kegiatan` → closure `view('pages.kegiatan')`, tetapi **file view tidak ada** → error 500.
Tidak ada model/tabel kegiatan yang aktif.

### 1.13 Alumni — ✅

`GET /alumni` → `Public\AlumniController@index`.

**Data:** seluruh baris tabel `alumni`, urut tahun lulus terbaru lalu nama. Setiap item dipetakan ke
nama, foto (`foto_url`), inisial, jenis kelamin, tahun lulus, jenjang, kegiatan, instansi, dan testimoni
(no. HP/email tidak ditampilkan di publik).

**Tampilan:** hero, 4 kartu statistik (total alumni, angkatan, kuliah, bekerja/wirausaha), filter
pencarian + angkatan + chip kegiatan di sisi klien, kartu alumni dengan foto atau inisial, testimoni,
serta empty state. Gaya visual mengikuti halaman Prestasi/Galeri.

**Navigasi:** item **Alumni** di dropdown **Profil** navbar (aktif saat `request()->is('alumni*')`)
dan berlaku juga di drawer mobile karena memakai markup yang sama.

**Testimoni di beranda:** alumni yang kolom `testimoni`-nya terisi otomatis tampil pada slider
**“Kata Alumni”** di halaman beranda (maks. 12 terbaru, auto-geser 5,5 detik, 3 kartu desktop /
2 tablet / 1 mobile, ada panah, dot, dan usap). Section disembunyikan bila belum ada testimoni.

## 2. PPDB Online

### 2.1 Form Pendaftaran — ✅

**Alur:**

1. `GET /pendaftaran` → form.
2. `POST /pendaftaran` → `Public\PendaftaranOnlineController@store`.
3. Validasi 12 field:

   | Field | Kolom DB | Aturan |
   |-------|----------|--------|
   | Nama Lengkap | `nama_lengkap` | required, max 255 |
   | Tempat Tanggal Lahir | `tempat_tanggal_lahir` | required, max 255 |
   | Nomor Handphone Aktif (WA) | `no_hp_aktif` | required, max 30 |
   | Alamat Tinggal (Domisili) | `alamat_domisili` | required, max 1000 |
   | Email Orang Tua / Wali | `email` | required, email, unik (dipakai login Portal Wali) |
   | Nama Orang Tua (Wali) | `nama_ortu_wali` | required, max 255 |
   | Nomor Handphone Orang Tua | `no_hp_ortu` | required, max 30 |
   | Sekolah Asal | `asal_sekolah` | required, max 255 |
   | Alamat Sekolah Asal | `alamat_sekolah` | required, max 1000 |
   | Tahun Lulus | `tahun_lulus` | required, 4 digit |
   | NISN | `nisn` | opsional, max 20 |
   | Pilihan Jenjang | `jenjang_tujuan` | required, salah satu dari 7 jenjang |

4. Generate `no_pendaftaran` `PPDB-{tahun}-{5 huruf}` (dicek unik).
5. Simpan status `pending`.
6. Redirect back + pesan nomor pendaftaran.

**Catatan:** tidak ada email konfirmasi, tidak ada upload berkas, tidak ada captcha/rate limit.
Kolom `email` **milik orang tua/wali** (santri tidak punya email) dan menjadi akun login Portal Wali
saat pendaftaran diverifikasi; tabel admin `/admin/pendaftaran` serta `/admin/santri` menampilkannya
sebagai "Email Orang Tua / Wali"; setelah diverifikasi, data masuk ke tabel `santri` dan tertaut
lewat `santri.pendaftaran_online_id`.

### 2.2 Verifikasi oleh Admin — ✅ dengan risiko

**Alur:**

1. Admin membuka `/admin/pendaftaran`.
2. Menekan **Terima** atau **Tolak** (PATCH `/admin/pendaftaran/{id}` → `admin.pendaftaran.verifikasi`).
3. Jika diterima:
   - Cek belum pernah diterima, email wali tidak kosong, dan email belum dipakai akun non-wali.
   - Buat/pakai ulang `User` role `wali_santri`; akun baru mendapat password acak
     `Str::password(12)`, `must_change_password`, dan masa berlaku 24 jam.
   - Buat `Santri` dengan nomor induk acak, jenjang dari pilihan PPDB, `pendaftaran_online_id`
     sebagai arsip, serta tautkan wali ke pivot `santri_wali`.
   - Catat `RiwayatKelas` awal dan Log Aktivitas.
   - Kirim **email kredensial**; bila gagal, admin memakai tombol **Reset Password Wali**.
4. Redirect + pesan sukses. Link WhatsApp tetap disediakan sebagai kanal bantu, bukan pengiriman
   password utama.

**Edit & hapus:**

- **Edit** (`/admin/pendaftaran/{id}/edit`, PUT `admin.pendaftaran.update`): mengubah 12 field
  pendaftaran. Jika pendaftar sudah `diterima`, perubahan ikut disinkronkan ke `users`
  (nama/email/telepon) dan `santri` (nama, jenjang, wali, alamat) dalam satu transaksi.
- **Hapus** (`DELETE admin.pendaftaran.destroy`): hanya untuk pendaftar yang belum pernah
  diverifikasi. Pendaftar yang sudah tertaut `santri`/alumni **tidak bisa dihapus** karena
  dipertahankan sebagai arsip PPDB (tombol berubah menjadi label **Arsip**). Akun portal wali
  **tidak** dihapus.
- **Hapus santri** (`DELETE admin.santri.destroy`): menghapus data santri tanpa menghapus arsip
  pendaftarannya. Status arsip kembali `pending` bila santri belum lulus; tetap `diterima` bila
  santri sudah lulus/alumni agar riwayat PPDB utuh.
- **Edit santri** (`GET/PUT admin.santri.edit/update`): mengubah nomor induk, nama, jenjang,
  kelas, wali, dan alamat pada tabel `santri` langsung.

**Risiko:**

- Email kredensial bergantung pada konfigurasi mailer; di lokal biasanya hanya masuk `log`.
- Tidak ada validasi kuota/jenjang.
- Tidak ada status "diproses" atau catatan penolakan pada PPDB.
- Menghapus pendaftaran yang belum diverifikasi bersifat permanen dan tidak memakai soft delete;
  pendaftar yang sudah menjadi santri/alumni dilindungi sebagai arsip.
- Password sementara 24 jam harus benar-benar diganti; bila kedaluwarsa, wali perlu reset admin.

## 3. Autentikasi

### 3.1 Login — ✅

- Form email + password + remember.
- Redirect sesuai role (Superadmin, Pengurus, Pembina, Admin Jenjang, Wali, Alumni).
- Akun nonaktif (`is_active = false`) dan password sementara kedaluwarsa ditolak.
- Akun dengan `must_change_password` dipaksa ke `/ganti-password`.
- Pesan error Bahasa Indonesia.
- Toggle password & state loading (JS).
- Tombol "Masuk dengan Google" **belum berfungsi** (`href="#"`), meski Socialite terpasang dan
  `.env` memiliki kredensial Google.

### 3.2 Logout — ✅

POST `/logout`, invalidate session, regenerate token, redirect `/`.

### 3.3 Lupa Password — ✅

Route `/lupa-kata-sandi` + `/reset-kata-sandi/{token}` tersedia, mengirim tautan reset ke email akun.
Throttle 6/menit. Pengiriman aktual bergantung konfigurasi mailer.

### 3.4 Register — ❌

Tidak ada route dan controller kosong. Pendaftaran akun hanya lewat PPDB (wali), kelulusan (alumni),
atau menu Pengguna (Superadmin).

## 4. Dashboard Admin

### 4.1 Dashboard — ⚠️

**Data:** statistik, tren pendaftar, komposisi santri, agenda, pendaftar terbaru, shortcut fitur.

**Masalah:**

- `santriPutra/Putri` masih **hardcode 0** karena kolom `jenis_kelamin` tidak ada di `santri`.
- "Asatidz" menghitung tabel `kepengurusan`, bukan data asatidz.
- `komposisi` dan `agenda` **sudah berjalan** (`jenjang_pendidikan` dan `info_harian`).

### 4.1b Dashboard per Jenjang (multi-role) — ✅

- `/admin/dashboard-jenjang` dapat dibuka **Superadmin** dan **Admin Jenjang**.
- Admin Jenjang otomatis dibatasi ke jenjangnya; Superadmin memilih jenjang lewat `?jenjang=slug`
  atau melihat gabungan seluruh jenjang bila tidak memilih.
- Komponen: kartu statistik (santri, pendaftar, menunggu, alumni, prestasi), aksi cepat, form input
  nilai & prestasi (hanya Superadmin/Admin Jenjang), tabel pendaftar/santri terbaru, ringkasan
  alumni + status nilai/transkrip, dan panel **Akses Cepat per Jenjang**.
- Halaman input nilai/prestasi dan unggah rapor PDF mengikuti pilihan jenjang yang sama.
- Dashboard **Pengurus** (`/admin/dashboard-pengurus`) dan **Pembina** (`/admin/dashboard-pembina`)
  memiliki halaman sendiri. Pengurus memantau seluruh jenjang; Pembina fokus pada pembinaan harian.
- Dashboard Superadmin, Pengurus, dan Pembina menampilkan panel **Akses Cepat per Jenjang**.

### 4.2 Pendaftaran — ✅

Lihat bagian 2.2. Tampilan responsif: kartu di mobile, tabel di desktop, pencarian klien.

### 4.3 Data Santri — ✅ / ⚠️

- Daftar santri dengan filter pencarian, jenjang, kelas, dan status; Admin Jenjang dibatasi ke jenjangnya.
- Tambah/edit/hapus, detail lengkap (identitas, wali, riwayat kelas, nilai, hafalan, pelanggaran, izin, dokumen).
- **Naik kelas:** pilih tahun ajaran/semester, kelas asal/tujuan, status **naik/tinggal**; nilai lama dikunci.
- **Rollback** kenaikan kelas: khusus Superadmin, wajib alasan, tercatat di log aktivitas.
- **Pindah jenjang:** data lama ikut (nilai, hafalan, pelanggaran, izin, dokumen) + riwayat kelas.
- **Arsipkan:** santri keluar/berhenti diarsipkan, bukan dihapus; akun wali tetap aktif.
- **Kelulusan:** menandai lulus otomatis membuat alumni + akun alumni + password sementara + email.
- **Reset Password Wali:** membuat password sementara baru 24 jam + email kredensial.
- Tombol **Import Excel** masih stub dan **Template** mengarah ke file yang tidak ada.

### 4.4 Data Alumni — ✅

- Tabel alumni mengikuti gaya tabel **Data Santri** (padat, badge, aksi Detail/Edit/Hapus)
  dengan kolom: No. Induk, Nama, NISN, Jenis Kelamin, Jenjang, Tahun Masuk, Tahun Lulus,
  Kegiatan, Instansi, Testimoni, No. HP, Email, Alamat, Nilai, Status, Aksi.
- Ringkasan di atas tabel sama dengan statistik halaman publik `/alumni`: **Total alumni**,
  **Angkatan lulus**, **Melanjutkan kuliah**, dan **Bekerja & wirausaha** (untuk Admin
  Jenjang dihitung hanya pada jenjangnya).
- Filter angkatan + jenjang (jenjang hanya muncul bila user boleh lebih dari satu) +
  **kegiatan** (mengikuti chip kegiatan pada `/alumni`), serta pencarian
  nama/NIS/NISN/jenjang/instansi/testimoni/email/no. HP. Kolom **Testimoni** menandai
  alumni yang testimoninya sudah terisi — data inilah yang tampil di direktori publik dan
  slider "Kata Alumni" di beranda.
- Alumni tertaut ke santri: menandai santri **Lulus** di `/admin/santri` otomatis membuat
  baris alumni (`App\Support\Kelulusan`), membatalkan kelulusan menghapusnya. Santri lulus
  yang belum punya data alumni bisa ditarik lewat banner **Tarik Santri Lulus**. Kolom
  **Status** menampilkan **Alumni** untuk baris hasil kelulusan (santri berstatus lulus),
  **Santri** bila tertaut ke santri yang masih aktif, dan **Manual** untuk entri tanpa tautan.
- Halaman detail alumni menampilkan data pribadi/akademik, kontak, kegiatan, testimoni,
  serta unggah/pratinjau/unduh/hapus berkas **nilai/transkrip PDF** (maks. 10 MB, satu
  berkas per alumni, unggah ulang menimpa berkas lama).
- Create/edit/hapus dengan foto + NISN + tahun masuk.
- Admin Jenjang mendapat tampilan & aksi yang sama, tetapi data dibatasi ke jenjangnya
  (daftar jenjang pada form ikut dibatasi).
- Data alumni tampil di halaman publik `/alumni` (lihat §1.13).

### 4.5 Konten Profil — ✅ / ⚠️

- Form teks per halaman (sejarah, visi, pembelajaran) memakai `items[]` + `layout[]`.
- Unggah `sejarah_foto` (jpg/jpeg/png/webp maks 2 MB) tersedia.
- Validasi key dan layout memakai whitelist; key di luar daftar diabaikan.
- Tidak ada CRUD pengurus/kepengurusan di panel; data itu hanya dibaca dari tabel `kepengurusan`.

### 4.6 Program — ✅ (alias)

- `admin.program.index` sengaja dipertahankan sebagai redirect ke `admin.jenjang.index`
  untuk menjaga tautan lama; tidak dibuat CRUD tersendiri.
- `ProgramSeeder` tetap dipanggil oleh `DatabaseSeeder` pada instalasi baru.
- `ProgramController` lama tidak dipakai route mana pun (dead code, bisa dibersihkan).

### 4.7 Kegiatan — ✅

- `admin.kegiatan.index` → `KegiatanController@index`, view `dashboard/kegiatan/index`.
- Menampilkan jadwal aktif dari `info_harian`, catatan kalender milik akun, dan
  absensi/kegiatan santri yang dibatasi menurut jenjang pengguna.
- Bersifat **hanya baca**; tidak ada tabel/model `kegiatan` baru dan tidak ada
  tautan di sidebar (akses lewat URL `/admin/pengelolaan-kegiatan`).

### 4.8 Berita — ✅

CRUD lengkap: judul, kategori, ringkasan, isi, gambar, status draft/published, tanggal terbit.
Slug otomatis, penulis dari user login, pencarian, paginasi.

### 4.9 Galeri — ✅ / ⚠️

Lihat 1.9. Kuat untuk create + delete, belum ada edit.

### 4.10 Prestasi — ✅

CRUD lengkap: judul, nama, kelas, kategori, tingkat, wilayah, tahun, peringkat, label, deskripsi.

### 4.11 Struktur Organisasi — ✅

CRUD 3 kategori dengan validasi pohon:

- Bagan: atasan opsional, tidak boleh siklus, foto opsional
- Jenjang: kode wajib, foto opsional
- Bidang: keterangan + ikon
- Urutan otomatis, aktif/nonaktif
- Hapus ditolak jika masih punya bawahan

### 4.12 Pengguna Dashboard — ✅

- Hanya **Superadmin**.
- 7 tab: Semua Pengguna, Superadmin, Pengurus, Pembina, Admin Jenjang, Wali Santri, Alumni.
- Daftar + filter role + pencarian; tambah, edit, hapus.
- Admin Jenjang wajib memilih tepat satu jenjang.
- Proteksi: tidak bisa hapus diri sendiri / Superadmin terakhir.
- Akun wali/alumni yang masih memiliki relasi data tidak dihapus permanen (dinonaktifkan/ditolak).

### 4.13 Pengaturan Sistem — ❌

Route `super.settings` mengarah ke `DashboardController@settings` yang tidak ada.

### 4.14 Info Harian — ✅ (sudah terhubung) / 🚧 (tabel pending)

Sudah ada:

- Model + scope + accessor (`info`, `jadwal`, `aktif`, `berlaku`, `sudahTerbit`, `histori`)
- Controller CRUD lengkap (`Admin\InfoHarianController`)
- Route `admin.info.*` (6 method, dibungkus `role:super_admin`)
- View index & form di dashboard
- Menu sidebar di `Pengelolaan > Info Harian` (khusus superadmin)
- Guard `Schema::hasTable('info_harian')` di beranda (jika tabel belum di-migrate, tampil data dummy; jika sudah di-migrate, tampil data nyata dari DB)

**Halaman publik `/info-harian`** (`Public\InfoHarianController@index`, menu navbar "Info Harian"):

- Hero + statistik + panel jadwal hari ini (timeline, penanda jadwal berikutnya)
- Grid kartu info aktif dengan pencarian & filter kategori
- **Histori kedaluwarsa**: kartu info yang `berlaku_sampai < hari ini` ditampilkan dalam grid dengan label
  "Kedaluwarsa", hanya sampai **3 hari** setelah tanggal berakhir (`InfoHarian::HISTORI_HARI`).
  Lewat dari itu info otomatis hilang dari halaman (data tetap ada di DB).

Belum ada (butuh migrasi manusia):

- Jalankan `php artisan migrate` (lihat [ATURAN.md](ATURAN.md)) → tabel `info_harian` terbentuk, data seeder masuk
- Isi data di tabel via `InfoHarianSeeder` atau form admin
- Google OAuth aktif (masih dummy)

### 4.15 Pembinaan — ✅

- Halaman `/admin/pembinaan` menampilkan hafalan, pelanggaran, dan presensi per santri.
- **Hafalan:** setoran/ziyadah/murojaah, surat/ayat/juz, nilai, predikat, catatan.
- **Pelanggaran:** jenis, deskripsi, poin, tindakan.
- **Presensi:** hadir/izin/sakit/alpa + keterangan.
- Superadmin, Admin Jenjang, dan Pembina boleh menulis; Pengurus hanya membaca.
- Admin Jenjang dibatasi ke jenjangnya. Setiap input mencatat `dicatat_oleh`.

### 4.16 Izin Pulang — ✅

- Wali mengajukan izin dari portal: alasan, tanggal pulang/kembali, penjemput.
- Status: **MENUNGGU → DISETUJUI / DITOLAK → SELESAI / TERLAMBAT**.
- Penolakan **wajib alasan** (`alasan_penolakan`).
- Izin disetujui mendapat `kode_izin` unik (representasi kode/QR izin).
- Tombol **Sudah Kembali** menandai selesai (`kembali_pada`).
- `Izin::tandaiTerlambat()` dipanggil saat halaman admin/portal dibuka; izin yang melewati
  `tanggal_kembali` otomatis menjadi **TERLAMBAT**.
- Superadmin, Admin Jenjang, dan Pembina boleh memverifikasi; Pengurus hanya membaca.

### 4.17 Pengumuman — ✅

- Superadmin & Pengurus dapat membuat pengumuman untuk **semua jenjang**.
- Admin Jenjang hanya untuk jenjangnya (dipaksa di controller).
- Wali Santri & Alumni hanya membaca.
- Pengumuman tampil di portal wali (sesuai jenjang anak) dan portal alumni.
- Target `null` berarti semua jenjang.

### 4.18 Kelas, Guru & Rekap Wali Kelas — ✅

- **Guru/Wali Kelas tidak punya akun login.**
- Master `guru` menyimpan nama, NIP, jenis kelamin, status aktif.
- Master `kelas` menyimpan jenjang, nama, tingkat, tahun ajaran, dan **satu wali kelas**
  (`wali_kelas_id`).
- Admin Jenjang mengelola kelas/guru pada jenjangnya; Superadmin semua jenjang.
- **Rekap Wali Kelas** (`admin.kelas.rekap`) adalah halaman cetak berisi nilai, hafalan,
  pelanggaran, dan presensi kelas untuk diserahkan ke Wali Kelas. Wali Kelas boleh merekap/
  mengajukan, tetapi input/edit sistem tetap dilakukan Admin Jenjang dan tercatat di Log Aktivitas
  atas nama Admin Jenjang.
- Nama guru pengampu tampil di halaman **Nilai** per mata pelajaran (`mata_pelajaran.guru_id`),
  transkrip, dan portal nilai wali.

### 4.19 Akademik — ✅ (UI) / 🚧 (tabel)

- **Nilai:** filter jenjang/kelas/mapel/tahun ajaran/semester/kategori; input & edit oleh
  Superadmin/Admin Jenjang; status `draft` → `published`; nilai `draft` tidak terlihat wali.
- **Transkrip:** tampilan & cetak per santri, menampilkan guru pengampu.
- **Mata Pelajaran:** CRUD per jenjang + pilih guru pengampu.
- **Rapor PDF:** unggah per santri/semester (`dokumen_nilai`), bisa diunduh wali.
- Tabel `mata_pelajaran`, `nilai_santri`, dan `dokumen_nilai` sudah ditulis sebagai migrasi;
  status pembuatan tabel perlu diverifikasi manusia.

### 4.20 Log Aktivitas — ✅

- Khusus Superadmin (`/admin/log-aktivitas`).
- Mencatat aksi penting: verifikasi PPDB, pembuatan akun, kelulusan, naik kelas, rollback,
  pindah jenjang, arsip, perubahan nilai, dan pengajuan wali.
- Menyimpan `user_id`, `aksi`, `model_type/model_id`, `data` (JSON), `alasan`, `ip`, `user_agent`.
- Rollback dan aksi sensitif lain **wajib alasan** dan tercatat.

### 4.21 Portal Alumni — ✅

Lihat §5.3.

## 5. Portal Wali Santri

### 5.1 Dashboard — ✅

- Route: `GET /portal/dashboard`, middleware `role:wali_santri`.
- Menampilkan sapaan, daftar anak (bisa lebih dari satu), status anak, dan ringkasan:
  kehadiran, tahfidz, nilai/rapor, prestasi, izin, serta pengumuman.
- Satu akun wali dapat memiliki banyak anak lewat `santri.user_id` dan pivot `santri_wali`.
- Jika anak sudah lulus/non-aktif, kartu berubah menjadi **Mode Riwayat**: tombol pengajuan/izin
  disembunyikan, nilai & pelanggaran tetap dapat dilihat, dan dokumen kelulusan tersedia.

### 5.2 Menu Portal Wali

| Halaman | Isi |
|---------|-----|
| Dashboard | Ringkasan semua anak |
| Nilai | Nilai & rapor per anak (hanya yang sudah terbit) + cetak |
| Prestasi | Prestasi anak |
| Detail/Riwayat Anak | Identitas, kelas/kamar, nilai, hafalan, pelanggaran, presensi, izin, dokumen |
| Pengumuman | Pengumuman sesuai jenjang anak |
| Izin | Ajukan izin pulang + status/kode izin |
| Pengajuan | Ajukan perubahan data anak (disetujui/ditolak admin) |
| Dokumen | Unduh ijazah/SKL/transkrip/sertifikat |

Aturan:

- Wali **hanya** dapat membuka anak miliknya (`User::memilikiSantri()`), bukan hanya tautan disembunyikan.
- Wali tidak dapat mengubah nilai, presensi, atau data akademik.
- Setelah anak lulus, akun wali **tetap aktif** dan tampilan berubah menjadi history anak.

### 5.3 Portal Alumni — ✅

- Route prefix `/alumni`, middleware `role:alumni`.
- **Dashboard:** profil, nomor ijazah, kelas/kamar terakhir, status, ringkasan nilai & pelanggaran.
- **Nilai & Riwayat:** history nilai terkunci (hanya baca), termasuk nilai lama.
- **Dokumen:** hanya dapat **mengunduh** ijazah/SKL/transkrip/sertifikat.
- **Tracer Study:** alumni dapat memperbarui kegiatan, instansi, pekerjaan, alamat, kontak,
  tahun mulai, dan catatan.
- **Legalisir:** ajukan legalisir (jenis dokumen + jumlah) dan unduh hasil bila sudah selesai.
- **Pengumuman:** membaca pengumuman untuk alumni.
- History nilai dan pelanggaran **tidak dapat diubah** oleh alumni.

### 5.4 Rencana yang Terlihat dari Kode

- `LaporanSantriController` masih kosong.
- `portal/kegiatan.blade.php` masih kosong.
- Tabel `absensi` sudah dipakai halaman pembinaan dan portal.

## 6. Fitur Pendukung

### 6.1 Notifikasi WhatsApp — ⚠️

Bukan integrasi API; hanya pembuatan link `https://wa.me/...` setelah pendaftaran diterima.
Nomor `0...` dikonversi ke `62...`.

### 6.2 Upload File — ✅ / ⚠️

Berjalan untuk berita, alumni, galeri, struktur, konten, kepengurusan. Namun lokasi penyimpanan
tidak seragam (`storage` vs `public/upload`).

### 6.3 Pencarian & Filter — ✅

- Berita admin: judul
- Alumni: nama/instansi + angkatan
- Pengguna: nama/email + role
- Prestasi publik: kategori + tahun (klien)
- Pendaftaran: pencarian klien

### 6.4 Paginasi — ✅

Berita admin, santri, alumni, pengguna, galeri, prestasi admin, berita publik.

### 6.5 Dark Mode — ✅

Hanya area admin; disimpan di `localStorage`.

## 7. Matriks Status Fitur

| Fitur | Publik | Admin | Status |
|-------|--------|-------|--------|
| Beranda | ✅ | — | ⚠️ info dummy bila tabel kosong |
| Profil | ✅ | ✅ | ⚠️ sebagian statis |
| Sejarah/Visi/Pembelajaran | ✅ | ❌ | ✅ statis |
| Jenjang | ✅ | ❌ | ✅ statis |
| Struktur | ✅ | ✅ | ✅ |
| Berita | ✅ | ✅ | ✅ |
| Galeri | ✅ | ✅ | ✅ create/delete |
| Prestasi | ✅ | ✅ | ✅ |
| Kontak | ✅ | — | ✅ statis |
| Kegiatan | ❌ | ❌ | ❌ |
| PPDB | ✅ | ✅ | ✅ santri + akun wali + email |
| Santri | — | ✅ | ✅ detail/naik kelas/pindah/arsip/lulus; import stub |
| Alumni | ✅ | ✅ | ✅ CRUD + portal alumni |
| Pengguna | — | ✅ | ✅ 7 tab, khusus Superadmin |
| Info Harian | ✅ | ✅ | ✅ CRUD + menu; tabel perlu verifikasi |
| Portal wali | — | — | ✅ dinamis multi-anak + mode riwayat |
| Portal alumni | — | — | ✅ profil, nilai, dokumen, legalisir, tracer |
| Pembinaan | — | ✅ | ✅ hafalan/pelanggaran/presensi |
| Izin pulang | — | ✅ | ✅ status + kode + auto terlambat |
| Pengumuman | — | ✅ | ✅ per jenjang |
| Naik kelas | — | ✅ | ✅ + rollback Superadmin |
| Kelas & guru | — | ✅ | ✅ + rekap wali kelas |
| Akademik | — | ✅ | ✅ UI; tabel menunggu migrasi manusia |
| Log aktivitas | — | ✅ | ✅ khusus Superadmin |
| Google Login | ❌ | — | ❌ |
| Reset password | ✅ | — | ✅ route + email |
