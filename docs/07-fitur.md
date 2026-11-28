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

**Tujuan:** halaman profil yang mengambil teks dari tabel `konten`.

**Alur:** `GET /profil` → `Public\ProfilController@index` → `pages.profil`.

**Data:** `Konten::getVal()` untuk 7 key. Kartu pimpinan/`$kepengurusans` dihapus 28 Nov 2026
(tabel `kepengurusan` dihapus via migrasi `2026_11_28_000008_hapus_tabel_kepengurusan`); menu
**Struktur Organisasi** tetap ada dan bagan lengkap di `/struktur-organisasi` tidak berubah.

**Catatan:** halaman `/profil` memakai `konten` untuk teks; sub-halaman sejarah, visi-misi,
pembelajaran, dan fasilitas juga memakai `konten` (bisa diedit Superadmin lewat menu Konten).
`sejarah_foto` dapat diunggah dari dashboard Konten.

### 1.3 Sejarah — ✅

**Alur:** `GET /profil/sejarah` → `ProfilController@sejarah` → `pages.sejarah`.

**Data:** `$konten` + `$layout` dari tabel `konten` (default tetap tersedia). Foto memakai
`konten.sejarah_foto` dengan placeholder bila belum diunggah; tidak memakai `public/images/sejarah/*`.

### 1.4 Visi & Misi — ✅

**Alur:** `GET /profil/visi-misi` → `ProfilController@visiMisi` → `pages.visi-misi`.

**Data:** `$konten` + `$layout` dari tabel `konten`. Tidak ada referensi `public/images/profil/*`.

### 1.5 Pembelajaran — ✅

**Alur:** `GET /profil/pembelajaran` → `ProfilController@pembelajaran` → `pages.pembelajaran`.

**Data:** `$konten` + `$layout` dari tabel `konten`. Tidak ada referensi `public/images/pembelajaran/*`.

### 1.6 Struktur Organisasi Publik — ✅

**Alur:** `GET /profil/struktur-organisasi` (halaman Struktur Organisasi, sering disebut
`/struktur-organisasi`) → `Public\StrukturOrganisasiController@index`. Menu ini tetap ada dan bagan lengkapnya tidak berubah setelah Kepengurusan dihapus 28 Nov 2026.

**Data:**

- Bagan: tree aktif kategori `bagan` (rekursif)
- Ketua jenjang: kategori `jenjang` aktif
- Bidang: kategori `bidang` aktif
- Jumlah pengurus

**View:** `pages/struktur-organisasi` + `partials/struktur-node` (rekursif, 1 query).

### 1.7 Halaman Jenjang — ✅ sebagian statis

**Alur:** `GET /jenjang/{slug}` dengan 7 slug valid → `Public\JenjangController@show` →
`pages/jenjang/_detail`. (Ini halaman publik; `Admin\JenjangController` sudah dihapus 28 Nov 2026.)

**Data:** satu sumber `App\Support\DataJenjang` untuk 7 jenjang (nama, deskripsi, kurikulum,
fasilitas, ketua, dll.); tautan jenjang lain memakai `route('jenjang.show')`.

**Catatan:** konten masih hardcoded di `DataJenjang`, belum terhubung ke tabel `konten`. Sejak
28 Nov 2026, `public/images/program/*` dan `public/images/pengurus/*` berisi placeholder yang
dipakai `GambarPublik`; nama ketua/foto masih contoh dan perlu data resmi.

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
3. Validasi 13 field (12 wajib + NISN opsional) + honeypot `website` (`prohibited`):

   | Field | Kolom DB | Aturan |
   |-------|----------|--------|
   | Nama Lengkap | `nama_lengkap` | required, max 255 |
   | Tempat Tanggal Lahir | `tempat_tanggal_lahir` | required, max 255 |
   | Nomor Handphone Aktif (WA) | `no_hp_aktif` | required, max 16, hanya angka/`+`/`-`/spasi |
   | Alamat Tinggal (Domisili) | `alamat_domisili` | required, max 1000 |
   | Email Orang Tua / Wali | `email` | required, email, **tidak unik** (satu wali boleh mendaftarkan beberapa anak; dipakai login Portal Wali) |
   | Email Santri | `email_santri` | required, email, beda dari email wali, unik (calon username akun alumni) |
   | Nama Orang Tua (Wali) | `nama_ortu_wali` | required, max 255 |
   | Nomor Handphone Orang Tua | `no_hp_ortu` | required, max 16, hanya angka/`+`/`-`/spasi |
   | Sekolah Asal | `asal_sekolah` | required, max 255 |
   | Alamat Sekolah Asal | `alamat_sekolah` | required, max 1000 |
   | Tahun Lulus | `tahun_lulus` | required, 4 digit |
   | NISN | `nisn` | opsional, max 20 |
   | Pilihan Jenjang | `jenjang_tujuan` | required, salah satu dari 7 jenjang |

4. Generate `no_pendaftaran` lewat `KodeUnik::pendaftaran()` dengan format
   `PPDB-{tahun}-{5 huruf}` dan pengecekan tabrakan.
5. Simpan status `pending`.
6. Kirim `PendaftaranMasukMail` ke email wali. Bila pengiriman gagal, error dicatat dan pesan
   tetap memberitahu pengguna agar menyimpan nomor pendaftaran.
7. Redirect back + pesan nomor pendaftaran, jenjang tujuan, dan status email konfirmasi.

**Catatan:** tidak ada upload berkas, tidak ada captcha/rate limit; honeypot `website` hanya
dipakai untuk menolak bot. Kolom `email` **milik orang tua/wali** dan menjadi akun login Portal
Wali saat pendaftaran diverifikasi; indeks UNIQUE lama sudah dicabut agar satu wali bisa
mendaftarkan beberapa anak. Kolom `email_santri` dipakai sebagai calon username akun alumni saat
kelulusan dan dicek terhadap akun serta pendaftaran lain. Tabel admin `/admin/pendaftaran` serta
`/admin/santri` menampilkan keduanya dengan jelas; setelah diverifikasi, data masuk ke tabel
`santri` dan tertaut lewat `santri.pendaftaran_online_id`.

### 2.2 Verifikasi oleh Admin — ✅ dengan risiko

**Alur:**

1. Admin membuka `/admin/pendaftaran`.
2. Menekan **Terima** atau **Tolak** (PATCH `/admin/pendaftaran/{id}` → `admin.pendaftaran.verifikasi`).
3. Jika diterima:
   - Cek belum pernah diterima, email wali tidak kosong, dan email belum dipakai akun non-wali;
     email santri juga dicek agar tidak bentrok dengan akun/pendaftaran lain.
   - Buat/pakai ulang `User` role `wali_santri`; akun baru mendapat password acak
     `Str::password(12)`, `must_change_password`, dan masa berlaku 24 jam.
   - Buat `Santri` dengan nomor induk dari `KodeUnik::nomorInduk()` (pengecekan tabrakan
     termasuk santri soft-deleted), jenjang dari pilihan PPDB, `pendaftaran_online_id`
     sebagai arsip, serta tautkan wali ke pivot `santri_wali`.
   - Catat `RiwayatKelas` awal dan Log Aktivitas.
   - Kirim **email kredensial**; bila gagal, admin memakai tombol **Reset Password Wali**.
4. Redirect + pesan sukses. Link WhatsApp tetap disediakan sebagai kanal bantu, bukan pengiriman
   password utama.

**Edit & hapus:**

- **Edit** (`/admin/pendaftaran/{id}/edit`, PUT `admin.pendaftaran.update`): mengubah 13 field
  pendaftaran (termasuk `email_santri`). Email wali tidak unik; email santri dicek agar tidak
  bentrok dengan akun/pendaftaran lain. Jika pendaftar sudah `diterima`, perubahan ikut
  disinkronkan ke `users` (nama/email/telepon) dan `santri` (nama, jenjang, wali, alamat)
  dalam satu transaksi.
- **Hapus** (`DELETE admin.pendaftaran.destroy`): hanya untuk pendaftar yang belum pernah
  diverifikasi. Pendaftar yang sudah tertaut `santri`/alumni **tidak bisa dihapus** karena
  dipertahankan sebagai arsip PPDB (tombol berubah menjadi label **Arsip**). Akun portal wali
  **tidak** dihapus.
- **Hapus santri** (`DELETE admin.santri.destroy`): sejak 28 Nov 2026 bersifat **soft delete**
  (`santri.deleted_at`), bukan penghapusan permanen. Baris dan arsip PPDB tetap tersimpan; status
  pendaftaran **sengaja tidak diubah** agar pemulihan tidak perlu verifikasi ulang. Data masuk
  `/admin/santri/terhapus` (`admin.santri.terhapus`) dan hanya Superadmin yang bisa memulihkannya
  lewat `POST admin.santri.pulihkan` (`restore()` + audit `pulihkan_santri`). Aksi hapus tercatat
  sebagai `hapus_santri`. Tidak ada UI `forceDelete`; untuk santri keluar/berhenti tetap pakai
  **Arsipkan**, bukan hapus.
- **Edit santri** (`GET/PUT admin.santri.edit/update`): mengubah nomor induk, nama, jenjang,
  kelas, wali, dan alamat pada tabel `santri` langsung.

**Risiko:**

- Email kredensial bergantung pada konfigurasi mailer; di lokal biasanya hanya masuk `log`.
- Tidak ada validasi kuota/jenjang.
- Tidak ada status "diproses" atau catatan penolakan pada PPDB.
- Menghapus pendaftaran yang belum diverifikasi bersifat permanen dan tidak memakai soft delete;
  pendaftar yang sudah menjadi santri/alumni dilindungi sebagai arsip.
- Password wali dari PPDB dan alumni dari kelulusan langsung dipakai dari email; tidak ada
  kewajiban ganti password pada alur otomatis.

## 3. Autentikasi

### 3.1 Login — ✅

- Form email + password + remember.
- Redirect sesuai role (Superadmin, Pengurus, Pembina, Admin Jenjang, Wali, Alumni).
- Akun nonaktif (`is_active = false`) dan password sementara kedaluwarsa ditolak.
- Akun dengan `must_change_password` dipaksa ke `/ganti-password` (akun manual berflag; wali PPDB
  dan alumni kelulusan memakai `false`).
- Pesan error Bahasa Indonesia.
- Toggle password & state loading (JS).
- Tombol **"Masuk dengan Google"** sudah memakai Socialite (`/auth/google` → callback
  `/auth/google/callback`). Tombol hanya tampil bila `GOOGLE_CLIENT_ID` dan
  `GOOGLE_CLIENT_SECRET` terisi. Email Google wajib sudah terdaftar & akun aktif; tidak ada
  auto-registrasi. Login dicatat sebagai `login_google`. Panduan: [17-google-oauth.md](17-google-oauth.md).

### 3.2 Logout — ✅

POST `/logout`, invalidate session, regenerate token, redirect `/`.

### 3.3 Lupa Password — ✅

Route `/lupa-kata-sandi` + `/reset-kata-sandi/{token}` tersedia, mengirim tautan reset ke email akun.
Throttle 6/menit. Pengiriman aktual bergantung konfigurasi mailer.

### 3.4 Register — ❌

Tidak ada route dan controller kosong. Pendaftaran akun hanya lewat PPDB (wali), kelulusan (alumni),
atau menu Pengguna (Superadmin).

## 4. Dashboard Admin

### 4.1 Dashboard — ✅ / ⚠️

**Data:** statistik, tren pendaftar, komposisi santri, agenda, pendaftar terbaru, shortcut fitur.

- ✅ `santriPutra/Putri` dihitung dari `santri.jenis_kelamin` (migrasi 28 Nov 2026).
  Bila belum diisi, labelnya "Jenis kelamin belum diisi", bukan "0".
- ✅ Stat "Asatidz"/`totalAsatidz` dihapus 28 Nov 2026 (tidak ada lagi kartu yang menghitung `kepengurusan`).
- ✅ `komposisi` dan `agenda` berjalan (`jenjang_pendidikan` dan `info_harian`).

### 4.1b Dashboard per Jenjang — halaman kanonik tunggal ✅

- Sejak 28 Nov 2026 satu halaman kanonik: `/admin/dashboard-jenjang` (`admin.dashboard.jenjang`,
  `RoleDashboardController@adminJenjang`, view `dashboard/roles/admin-jenjang`).
  `Admin\JenjangController` dan view `dashboard/jenjang/index`/`show` dihapus; sidebar tidak lagi
  memuat submenu "Jenjang", hanya satu menu **Dashboard Jenjang** (untuk Admin Jenjang, menu
  **Dashboard** sudah mengarah ke halaman ini).
- URL lama hanya redirect: `/admin/pengelolaan-jenjang` → `admin.dashboard.jenjang`,
  `/admin/pengelolaan-jenjang/{jenjang}` → `admin.dashboard.jenjang?jenjang=slug`, dan
  `/admin/pengelolaan-program` → langsung ke `admin.dashboard.jenjang`.
- Route dibuka oleh `jenjang.lihat` = **Superadmin, Admin Jenjang, Pengurus, Pembina** (sebelumnya
  hanya admin/super_admin). Role tanpa hak ubah melihat mode **baca-saja** otomatis.
- Admin Jenjang otomatis dibatasi ke jenjangnya; Superadmin/Pengurus/Pembina memilih jenjang lewat
  `?jenjang=slug` atau melihat agregat bila tidak memilih.
- Mode agregat: kartu ringkasan 7 jenjang + grafik perbandingan (`RekapJenjang::nilaiDbSemua()`
  mengambil semua nilai jenjang). Mode satu jenjang: kartu identitas jenjang + tautan publik,
  grafik **Santri per Kelas** dan **Status Pendaftar** (theme-aware dark mode), kartu statistik,
  aksi cepat, form nilai/prestasi (Superadmin/Admin Jenjang), tabel terbaru, dan ringkasan alumni.
- Halaman input nilai/prestasi dan unggah rapor PDF mengikuti pilihan jenjang yang sama.
- Dashboard **Pengurus** (`/admin/dashboard-pengurus`) dan **Pembina** (`/admin/dashboard-pembina`)
  tetap memiliki halaman sendiri; keduanya kini juga boleh membuka dashboard jenjang (baca-saja).

### 4.2 Pendaftaran — ✅

Lihat bagian 2.2. Tampilan responsif: kartu di mobile, tabel di desktop, pencarian klien.

### 4.3 Data Santri — ✅ / ⚠️

- Daftar santri dengan filter pencarian, jenjang, kelas, dan status; Admin Jenjang dibatasi ke jenjangnya.
- Tambah/edit/hapus, detail lengkap (identitas, wali, riwayat kelas, nilai, hafalan, pelanggaran, izin, dokumen).
- **Naik kelas:** pilih tahun ajaran/semester, kelas asal/tujuan, status **naik/tinggal**; nilai lama dikunci.
- **Rollback** kenaikan kelas: khusus Superadmin, wajib alasan, tercatat di log aktivitas.
- **Pindah jenjang:** data lama ikut (nilai, hafalan, pelanggaran, izin, dokumen) + riwayat kelas.
- **Arsipkan:** santri keluar/berhenti diarsipkan, bukan dihapus; akun wali tetap aktif.
- **Kelulusan:** menandai lulus otomatis membuat alumni + akun alumni + email berisi kredensial
  yang langsung bisa dipakai.
- **Reset Password Wali/Alumni:** membuat password baru + email kredensial yang langsung bisa dipakai.
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
- Kartu pimpinan dan CRUD `admin.kepengurusan.*` **dihapus 28 Nov 2026** bersama controller, model,
  view, dan tabel `kepengurusan` (migrasi `2026_11_28_000008_hapus_tabel_kepengurusan`). Menu
  **Struktur Organisasi** tetap ada; bagan lengkap di `/struktur-organisasi` tidak berubah.

### 4.6 Program — ✅ (alias)

- `admin.program.index` sengaja dipertahankan sebagai redirect langsung ke
  `admin.dashboard.jenjang` (`/admin/dashboard-jenjang`) untuk menjaga tautan lama; tidak dibuat
  CRUD tersendiri.
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
Kolom `jenjang` membatasi Admin Jenjang; role global bisa memilih satu jenjang atau melihat agregat.
Halaman prestasi publik sengaja menampilkan seluruh jenjang.

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
- Google OAuth: kode siap, menunggu `composer install` + kredensial (lihat doc 17)

### 4.15 Pembinaan — ✅

- Halaman `/admin/pembinaan` menampilkan hafalan, pelanggaran, dan presensi per santri.
- **Hafalan:** setoran/ziyadah/murojaah, surat/ayat/juz, nilai, predikat, catatan.
- **Pelanggaran:** jenis, deskripsi, poin, tindakan.
- **Presensi:** hadir/izin/sakit/alpa + keterangan.
- Superadmin, Admin Jenjang, dan Pembina boleh menulis; Pengurus hanya membaca.
- Admin Jenjang dibatasi ke jenjangnya. Setiap input mencatat `dicatat_oleh`.
- Sejak 28 Nov 2026, aksi tulis melewati `PembinaanPolicy` (hak `pembinaan.ubah` + cakupan santri).
  Pengecekan manual lama tetap dipertahankan.

### 4.16 Izin Pulang — ✅

- Wali mengajukan izin dari portal: alasan, tanggal pulang/kembali, penjemput.
- Status: **MENUNGGU → DISETUJUI / DITOLAK → SELESAI / TERLAMBAT**.
- Penolakan **wajib alasan** (`alasan_penolakan`).
- Izin disetujui mendapat `kode_izin` unik (representasi kode/QR izin).
- Tombol **Sudah Kembali** menandai selesai (`kembali_pada`).
- `Izin::tandaiTerlambat()` dipanggil saat halaman admin/portal dibuka; izin yang melewati
  `tanggal_kembali` otomatis menjadi **TERLAMBAT**.
- Superadmin, Admin Jenjang, dan Pembina boleh memverifikasi; Pengurus hanya membaca.
- Verifikasi melewati `IzinPolicy` (hak `izin.verifikasi` + cakupan jenjang); pengajuan wali
  melewati `portal.ajukanIzin`. Pengecekan manual tetap jadi lapisan kedua.

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
- **Import nilai CSV:** template dinamis, konteks dari filter bisa ditimpa per baris, baris error
  dilewati dan ditampilkan sebagai daftar error; hasil import berstatus `draft`.
- **Transkrip:** tampilan & cetak per santri, menampilkan guru pengampu.
- **Mata Pelajaran:** CRUD per jenjang + pilih guru pengampu.
- **Buka kembali nilai:** Superadmin/Admin Jenjang mengubah nilai `published` jadi `draft`
  lewat form alasan wajib (min. 5 karakter); aksi `nilai_dibuka_kembali` tercatat di log.
- **Jadwal Pelajaran (kode siap 28 Nov 2026):** CRUD per jenjang/kelas/tahun/semester;
  bentrok jam kelas dan jam guru ditolak; tampil di portal wali.
- **Kurikulum (kode siap 28 Nov 2026):** CRUD kurikulum + item (mapel, KKM, target KD, urutan);
  status `draft` → `published` oleh pemegang `akademik.terbit`; hanya kurikulum terbit yang
  tampil di portal wali.
- **Rapor Santri (kode siap 28 Nov 2026):** tabel `rapor_santri` berisi rata-rata & peringkat
  yang dihitung ulang dari nilai `published` (`App\Support\Rapor`), catatan wali kelas diisi
  manual, terbit satuan/massal, dan tampil di portal wali.
- **Rapor PDF:** unggah per santri/semester (`dokumen_nilai`), bisa diunduh wali.
- Tabel `mata_pelajaran`, `nilai_santri`, `dokumen_nilai`, `jadwal_pelajaran`, `kurikulum`,
  `kurikulum_item`, dan `rapor_santri` sudah ditulis sebagai migrasi; status pembuatan tabel
  perlu diverifikasi manusia (`php artisan migrate` dijalankan manusia).

### 4.20 Log Aktivitas — ✅

- Khusus Superadmin (`/admin/log-aktivitas`).
- Mencatat aksi penting: verifikasi PPDB, pembuatan akun, kelulusan, naik kelas, rollback,
  pindah jenjang, arsip, perubahan nilai, dan pengajuan wali.
- Sejak 28 Nov 2026 juga mencatat: import CSV santri dan nilai (beserta jumlah baris/error),
  kelulusan massal, pembuatan akun alumni massal, reset password alumni/wali,
  serta tambah/ubah (role & jenjang)/hapus pengguna.
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

Dokumen tersebut berasal dari tabel `dokumen_santri`. Admin Jenjang/Superadmin mengunggahnya dari
detail santri `/admin/santri/{id}` atau lewat shortcut di detail alumni
`/admin/data-alumni/{alumni}#dokumen`; berkas yang sama juga tampil di `/alumni/dokumen`.

Aturan:

- Wali **hanya** dapat membuka anak miliknya (`User::memilikiSantri()`), bukan hanya tautan disembunyikan.
  Sejak 28 Nov 2026 kepemilikan itu juga ditegakkan via `PortalPolicy` (`portal.lihatAnak`,
  `portal.lihatDokumen`, `portal.lihatDokumenSantri`, `portal.ajukanIzin`, `portal.lihatAnakId`).
- Wali tidak dapat mengubah nilai, presensi, atau data akademik.
- Setelah anak lulus, akun wali **tetap aktif** dan tampilan berubah menjadi history anak.

### 5.3 Portal Alumni — ✅

- Route prefix `/alumni`, middleware `role:alumni`.
- **Dashboard:** profil, nomor ijazah, kelas/kamar terakhir, status, ringkasan nilai & pelanggaran.
- **Nilai & Riwayat:** history nilai terkunci (hanya baca), termasuk nilai lama.
- **Dokumen:** hanya dapat **mengunduh** ijazah/SKL/transkrip/sertifikat.
- **Profil & Tracer Study:** dalam satu form alumni dapat memperbarui kontak, karier, alamat,
  kegiatan, instansi, pekerjaan, kontak tracer, tahun mulai, catatan, dan testimoni. Testimoni
  tampil di `/alumni` dan slider “Kata Alumni” di beranda.
- **Legalisir:** ajukan legalisir (jenis dokumen + jumlah) dan unduh hasil bila sudah selesai.
- **Pengumuman:** membaca pengumuman untuk alumni.
- History nilai dan pelanggaran **tidak dapat diubah** oleh alumni.
- Sejak 28 Nov 2026 seluruh endpoint alumni melewati `portal.lihatAlumni`; unduh dokumen dan
  legalisir masing-masing melewati `portal.lihatDokumenAlumni` dan `portal.unduhLegalisir`.
  Proses admin memakai `LegalisirPolicy` (hak `alumni.ubah` + cakupan jenjang).

### 5.4 Rencana yang Terlihat dari Kode

- `LaporanSantriController` dan `portal/kegiatan.blade.php` yang kosong sudah dihapus 28 Nov 2026.
- Tabel `absensi` sudah dipakai halaman pembinaan dan portal.

## 6. Fitur Pendukung

### 6.1 Notifikasi WhatsApp — ⚠️

Bukan integrasi API; hanya pembuatan link `https://wa.me/...` setelah pendaftaran diterima.
Nomor `0...` dikonversi ke `62...`.

### 6.2 Upload File — ✅ / ⚠️

Berjalan untuk berita, alumni, galeri, struktur, dan konten. Namun lokasi penyimpanan tidak seragam
(`storage` vs `public/upload` lama). Upload Kepengurusan dihapus 28 Nov 2026 bersama fiturnya.

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
| Portal alumni | — | — | ✅ profil + tracer + testimoni, nilai, dokumen, legalisir |
| Pembinaan | — | ✅ | ✅ hafalan/pelanggaran/presensi |
| Izin pulang | — | ✅ | ✅ status + kode + auto terlambat |
| Pengumuman | — | ✅ | ✅ per jenjang |
| Naik kelas | — | ✅ | ✅ + rollback Superadmin |
| Kelas & guru | — | ✅ | ✅ + rekap wali kelas |
| Akademik | — | ✅ | ✅ UI; tabel menunggu migrasi manusia |
| Log aktivitas | — | ✅ | ✅ khusus Superadmin |
| Google Login | 🚧 kode siap | — | 🚧 menunggu kredensial + `composer install` (doc 17) |
| Reset password | ✅ | — | ✅ route + email |
