<?php

use App\Http\Controllers\Admin\Akademik\MataPelajaranController as AdminMapelController;
use App\Http\Controllers\Admin\Akademik\NilaiController as AdminNilaiController;
use App\Http\Controllers\Admin\Akademik\TranskripController as AdminTranskripController;
use App\Http\Controllers\Admin\AlumniController as AdminAlumniController;
use App\Http\Controllers\Admin\BeritaController as AdminBeritaController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\GaleriController as AdminGaleriController;
use App\Http\Controllers\Admin\InfoHarianController as AdminInfoHarianController;
use App\Http\Controllers\Admin\JenjangController as AdminJenjangController;
use App\Http\Controllers\Admin\KegiatanController as AdminKegiatanController;
use App\Http\Controllers\Admin\KontenController as AdminKontenController;
use App\Http\Controllers\Admin\PendaftaranController as AdminPendaftaranController;
use App\Http\Controllers\Admin\PenggunaController as AdminPenggunaController;
use App\Http\Controllers\Admin\PrestasiAdminController;
use App\Http\Controllers\Admin\RoleDashboardController as AdminRoleDashboard;
use App\Http\Controllers\Admin\SantriController as AdminDataSantriController;
use App\Http\Controllers\Admin\SantriFormController as AdminSantriFormController;
use App\Http\Controllers\Admin\StrukturOrganisasiController as AdminStrukturController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Portal\PortalAkademikController;
use App\Http\Controllers\Portal\PortalDashboardController as PortalDashboard;
use App\Http\Controllers\Portal\PortalPrestasiController;
use App\Http\Controllers\Public\AlumniController as PublicAlumniController;
use App\Http\Controllers\Public\BeritaController as PublicBeritaController;
use App\Http\Controllers\Public\GaleriController as PublicGaleriController;
use App\Http\Controllers\Public\InfoHarianController as PublicInfoHarianController;
use App\Http\Controllers\Public\PendaftaranOnlineController;
use App\Http\Controllers\Public\PrestasiController;
use App\Http\Controllers\Public\StrukturOrganisasiController;
use App\Http\Middleware\CheckRole; // Hanya pakai CheckRole tunggal
use App\Support\Akses;
use Illuminate\Support\Facades\Route;

// ==========================================
// 1. PUBLIC ROUTES (Halaman Depan & Pendaftaran)
// ==========================================
Route::get('/', function () {
    return view('pages.beranda');
})->name('home');

// ######## PROFIL ########### //
Route::get('/profil', function () {
    return view('pages.profil');
})->name('profil');

Route::prefix('profil')->name('profil.')->group(function () {
    Route::get('/sejarah', fn () => view('pages.sejarah'))->name('sejarah');
    Route::get('/visi-misi', fn () => view('pages.visi-misi'))->name('visi-misi');
    Route::get('/struktur-organisasi', [StrukturOrganisasiController::class, 'index'])->name('struktur-organisasi');
    Route::get('/pembelajaran', fn () => view('pages.pembelajaran'))->name('pembelajaran');
});

// ######## JENJANG ########### //
Route::get('/jenjang/{slug}', fn (string $slug) => view('pages.jenjang.'.$slug))
    ->whereIn('slug', ['mdta', 'pps-wustha', 'mts', 'ma', 'pdf-wustha', 'pdf-ulya', 'mahad-aly'])
    ->name('jenjang.show');

// ######## BERITA, GALERI, KEGIATAN ########### //
Route::get('/berita', [PublicBeritaController::class, 'index'])->name('berita.index');
Route::get('/berita/{berita:slug}', [PublicBeritaController::class, 'show'])->name('berita.show');

Route::get('/galeri', [PublicGaleriController::class, 'index'])->name('galeri');

Route::get('/kegiatan', function () {
    return view('pages.kegiatan');
})->name('kegiatan');

// ######## PRESTASI SANTRI ########### //
Route::get('/prestasi', [PrestasiController::class, 'index'])->name('prestasi.index');

// ######## ALUMNI ########### //
Route::get('/alumni', [PublicAlumniController::class, 'index'])->name('alumni.index');

// ######## INFO HARIAN ########### //
Route::get('/info-harian', [PublicInfoHarianController::class, 'index'])->name('info-harian.index');

// ######## PENDAFTARAN ONLINE ########### //
Route::get('/pendaftaran', function () {
    return view('pages.pendaftaran');
})->name('pendaftaran.index');

Route::post('/pendaftaran', [PendaftaranOnlineController::class, 'store'])->name('pendaftaran.store');

Route::get('/kontak', function () {
    return view('pages.kontak');
})->name('kontak');

// ==========================================
// 2. AUTHENTICATION ROUTES
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    // Batasi 6 percobaan per menit per IP untuk mencegah brute force.
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:6,1');

    // Lupa kata sandi: tautan reset dikirim ke email akun yang terdaftar.
    // Token hanya disimpan sebagai hash dan berlaku 30 menit (single-use).
    Route::get('/lupa-kata-sandi', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/lupa-kata-sandi', [ForgotPasswordController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('password.email');

    Route::get('/reset-kata-sandi/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-kata-sandi', [ResetPasswordController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('password.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');
// Route::get('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');

// ==========================================
// 3. ROUTES PENGGUNA (Super Admin + pengelola wali)
// ==========================================
// Super Admin: semua akun. Admin Jenjang/Pengasuh/Pembina: hanya akun wali
// santri (Admin Jenjang dibatasi ke jenjangnya oleh controller).
Route::middleware(['auth'])->prefix('super')->name('super.')->group(function () {
    Route::get('/pengaturan-sistem', [AdminDashboard::class, 'settings'])
        ->middleware('role:super_admin')->name('settings');

    Route::middleware('role:'.Akses::roles('pengguna'))->group(function () {
        // Pengguna dashboard (CRUD)
        Route::resource('pengguna', AdminPenggunaController::class)
            ->parameters(['pengguna' => 'pengguna'])
            ->except('show')
            ->names('pengguna');
    });
});

// ==========================================
// 4. PANEL ADMIN — SATU DASHBOARD PER ROLE
// ==========================================
// Setiap role hanya bisa membuka dashboard-nya sendiri. Percobaan membuka
// dashboard role lain akan ditolak middleware `role` (403).
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // 4.1 Dashboard per role
    Route::get('/dashboard', [AdminDashboard::class, 'index'])
        ->middleware('role:super_admin')->name('dashboard');

    Route::get('/dashboard-jenjang', [AdminRoleDashboard::class, 'adminJenjang'])
        ->middleware('role:admin,super_admin,pengasuh,pembina')->name('dashboard.jenjang');

    // Halaman input nilai sudah digabung ke Akademik → Nilai Santri.
    // Route lama tetap ada sebagai pengalihan agar tautan lama tidak rusak.
    Route::get('/dashboard-jenjang/input-nilai', [AdminRoleDashboard::class, 'formNilai'])
        ->middleware('role:admin,super_admin,pengasuh,pembina')->name('dashboard.jenjang.nilai.form');

    Route::get('/dashboard-jenjang/input-prestasi', [AdminRoleDashboard::class, 'formPrestasi'])
        ->middleware('role:admin,super_admin,pengasuh,pembina')->name('dashboard.jenjang.prestasi.form');

    // Input cepat nilai & prestasi dari dashboard jenjang (Super Admin & Admin).
    Route::post('/dashboard-jenjang/nilai', [AdminRoleDashboard::class, 'simpanNilai'])
        ->middleware('role:admin,super_admin')->name('dashboard.jenjang.nilai');

    Route::post('/dashboard-jenjang/prestasi', [AdminRoleDashboard::class, 'simpanPrestasi'])
        ->middleware('role:admin,super_admin')->name('dashboard.jenjang.prestasi');

    // Unggah & kelola rapor PDF nilai santri.
    Route::post('/dashboard-jenjang/nilai/pdf', [AdminRoleDashboard::class, 'simpanDokumenNilai'])
        ->middleware('role:admin,super_admin')->name('dashboard.jenjang.nilai.pdf');

    Route::post('/dashboard-jenjang/nilai/pdf/massal', [AdminRoleDashboard::class, 'pdfMassal'])
        ->middleware('role:admin,super_admin')->name('dashboard.jenjang.nilai.pdf.massal');

    Route::get('/dashboard-jenjang/nilai/pdf/{dokumen}/unduh', [AdminRoleDashboard::class, 'unduhDokumenNilai'])
        ->middleware('role:admin,super_admin,pengasuh,pembina')->name('dashboard.jenjang.nilai.pdf.unduh');

    Route::delete('/dashboard-jenjang/nilai/pdf/{dokumen}', [AdminRoleDashboard::class, 'hapusDokumenNilai'])
        ->middleware('role:admin,super_admin')->name('dashboard.jenjang.nilai.pdf.hapus');

    Route::get('/dashboard-pengasuh', [AdminRoleDashboard::class, 'pengasuh'])
        ->middleware('role:pengasuh')->name('dashboard.pengasuh');

    Route::get('/dashboard-pembina', [AdminRoleDashboard::class, 'pembina'])
        ->middleware('role:pembina')->name('dashboard.pembina');

    // 4.2 Profil akun sendiri (semua peran panel)
    Route::middleware('role:'.Akses::roles('profil'))->group(function () {
        Route::get('/profil-saya', [AdminDashboard::class, 'profil'])->name('profil');
        Route::put('/profil-saya', [AdminDashboard::class, 'updateProfil'])->name('profil.update');
        Route::put('/profil-saya/password', [AdminDashboard::class, 'updatePassword'])->name('profil.password');
        Route::post('/profil-saya/avatar', [AdminDashboard::class, 'updateAvatar'])->name('profil.avatar');
    });

    // 4.3 Operasional (tulis) — hanya Super Admin & Admin Jenjang
    Route::middleware('role:'.Akses::roles('santri.ubah'))->group(function () {
        // Santri (aksi yang mengubah data) — didaftarkan lebih dulu agar
        // /santri/template tidak tertangkap oleh /santri/{id}.
        Route::get('/santri/template', [AdminDataSantriController::class, 'downloadTemplate'])->name('santri.template');
        Route::post('/santri/import', [AdminDataSantriController::class, 'import'])->name('santri.import');
        Route::get('/santri/tambah', [AdminSantriFormController::class, 'create'])->name('santri.create');
        Route::post('/santri', [AdminSantriFormController::class, 'store'])->name('santri.store');
        Route::get('/santri/{id}/edit', [AdminSantriFormController::class, 'edit'])->name('santri.edit');
        Route::put('/santri/{id}', [AdminSantriFormController::class, 'update'])->name('santri.update');
        Route::delete('/santri/{id}', [AdminDataSantriController::class, 'destroy'])->name('santri.destroy');

        // Kelulusan santri: otomatis membuat/menghapus data alumni terkait.
        Route::post('/santri/{id}/luluskan', [AdminDataSantriController::class, 'luluskan'])->name('santri.luluskan');
        Route::post('/santri/{id}/batalkan-kelulusan', [AdminDataSantriController::class, 'batalkanKelulusan'])->name('santri.batalkan-kelulusan');

        // Pendaftaran (verifikasi & ubah)
        Route::patch('/pendaftaran/{id}', [AdminPendaftaranController::class, 'updateStatus'])->name('pendaftaran.verifikasi');
        Route::get('/pendaftaran/{id}/edit', [AdminPendaftaranController::class, 'edit'])->name('pendaftaran.edit');
        Route::put('/pendaftaran/{id}', [AdminPendaftaranController::class, 'update'])->name('pendaftaran.update');
        Route::delete('/pendaftaran/{id}', [AdminPendaftaranController::class, 'destroy'])->name('pendaftaran.destroy');

        // Alumni (CRUD, tanpa index yang sudah ada di grup baca)
        Route::post('/data-alumni/tarik-santri-lulus', [AdminAlumniController::class, 'tarikSantriLulus'])->name('alumni.tarik-santri-lulus');
        Route::get('/data-alumni/create', [AdminAlumniController::class, 'create'])->name('alumni.create');
        Route::post('/data-alumni', [AdminAlumniController::class, 'store'])->name('alumni.store');
        Route::get('/data-alumni/{alumni}/edit', [AdminAlumniController::class, 'edit'])->name('alumni.edit');
        Route::put('/data-alumni/{alumni}', [AdminAlumniController::class, 'update'])->name('alumni.update');
        Route::delete('/data-alumni/{alumni}', [AdminAlumniController::class, 'destroy'])->name('alumni.destroy');

        // Unggah & hapus berkas nilai/transkrip alumni.
        Route::post('/data-alumni/{alumni}/nilai', [AdminAlumniController::class, 'uploadNilai'])->name('alumni.nilai.upload');
        Route::delete('/data-alumni/{alumni}/nilai', [AdminAlumniController::class, 'hapusNilai'])->name('alumni.nilai.hapus');

        // Prestasi (CRUD, tanpa index)
        Route::get('/pengelolaan-prestasi/tambah', [PrestasiAdminController::class, 'create'])->name('prestasi.create');
        Route::post('/pengelolaan-prestasi', [PrestasiAdminController::class, 'store'])->name('prestasi.store');
        Route::get('/pengelolaan-prestasi/{prestasi}/edit', [PrestasiAdminController::class, 'edit'])->name('prestasi.edit');
        Route::put('/pengelolaan-prestasi/{prestasi}', [PrestasiAdminController::class, 'update'])->name('prestasi.update');
        Route::delete('/pengelolaan-prestasi/{prestasi}', [PrestasiAdminController::class, 'destroy'])->name('prestasi.destroy');
    });

    // 4.4 Konten website — Super Admin & Admin Jenjang
    Route::middleware('role:'.Akses::roles('berita'))->group(function () {
        Route::resource('pengelolaan-berita', AdminBeritaController::class)
            ->parameters(['pengelolaan-berita' => 'berita'])
            ->except('show')
            ->names('berita');

        Route::post('/pengelolaan-berita/upload-gambar', [AdminBeritaController::class, 'uploadGambar'])
            ->name('berita.upload-gambar');

        Route::get('/pengelolaan-galeri', [AdminGaleriController::class, 'index'])->name('galeri.index');
        Route::get('/pengelolaan-galeri/tambah', [AdminGaleriController::class, 'create'])->name('galeri.create');
        Route::post('/pengelolaan-galeri', [AdminGaleriController::class, 'store'])->name('galeri.store');
        Route::delete('/pengelolaan-galeri/{galeri}', [AdminGaleriController::class, 'destroy'])->name('galeri.destroy');
    });

    // 4.5 Baca / monitoring — semua peran panel (tanpa aksi ubah)
    Route::middleware('role:'.Akses::roles('santri.lihat'))->group(function () {
        Route::get('/pendaftaran', [AdminPendaftaranController::class, 'index'])->name('pendaftaran.index');

        Route::get('/santri', [AdminDataSantriController::class, 'index'])->name('santri.index');
        Route::get('/santri/{id}', [AdminDataSantriController::class, 'detail'])->name('santri.detail');

        Route::get('/data-alumni', [AdminAlumniController::class, 'index'])->name('alumni.index');
        Route::get('/data-alumni/{alumni}/nilai', [AdminAlumniController::class, 'unduhNilai'])->name('alumni.nilai.unduh');
        Route::get('/data-alumni/{alumni}', [AdminAlumniController::class, 'show'])->name('alumni.show');

        Route::get('/pengelolaan-prestasi', [PrestasiAdminController::class, 'index'])->name('prestasi.index');

        Route::get('/pengelolaan-jenjang', [AdminJenjangController::class, 'index'])->name('jenjang.index');
        Route::get('/pengelolaan-jenjang/{jenjang}', [AdminJenjangController::class, 'show'])->name('jenjang.show');

        // Nama lama dipertahankan agar tautan/bookmark lama tetap berfungsi.
        Route::get('/pengelolaan-program', fn () => redirect()->route('admin.jenjang.index'))->name('program.index');

        Route::get('/pengelolaan-kegiatan', [AdminKegiatanController::class, 'index'])->name('kegiatan.index');
    });

    // 4.6 Pengelolaan institusional — hanya Super Admin
    Route::middleware('role:'.Akses::roles('konten'))->group(function () {
        Route::get('/pengelolaan-konten', [AdminKontenController::class, 'index'])->name('konten.index');
        Route::post('/konten', [AdminKontenController::class, 'store'])->name('konten.store');
        Route::put('/konten/update', [AdminKontenController::class, 'updateProfil'])->name('konten.update');
    });

    Route::middleware('role:'.Akses::roles('struktur'))->group(function () {
        Route::resource('struktur-organisasi', AdminStrukturController::class)
            ->parameters(['struktur-organisasi' => 'struktur'])
            ->except('show')
            ->names('struktur');
    });

    // Info harian beranda (khusus Super Admin)
    Route::middleware('role:'.Akses::roles('info'))->group(function () {
        Route::resource('info-harian', AdminInfoHarianController::class)
            ->parameters(['info-harian' => 'info'])
            ->except('show')
            ->names('info');
    });

    // 4.7 Akademik — nilai, transkrip, dan master mata pelajaran.
    // Baca: Super Admin, Admin Jenjang (jenjangnya), Pengasuh (semua, hanya baca).
    Route::prefix('akademik')->name('akademik.')->group(function () {
        Route::middleware('role:'.Akses::roles('akademik.lihat'))->group(function () {
            Route::get('/nilai', [AdminNilaiController::class, 'index'])->name('nilai.index');
            Route::get('/transkrip', [AdminTranskripController::class, 'index'])->name('transkrip.index');
            Route::get('/transkrip/{santri}/cetak', [AdminTranskripController::class, 'cetak'])->name('transkrip.cetak');
            Route::get('/transkrip/{santri}', [AdminTranskripController::class, 'show'])->name('transkrip.show');
        });

        // Tulis: hanya Super Admin & Admin Jenjang.
        Route::middleware('role:'.Akses::roles('akademik.ubah'))->group(function () {
            Route::post('/nilai', [AdminNilaiController::class, 'store'])->name('nilai.store');
        });

        Route::post('/nilai/terbitkan', [AdminNilaiController::class, 'terbitkan'])
            ->middleware('role:'.Akses::roles('akademik.terbit'))
            ->name('nilai.terbitkan');

        Route::middleware('role:'.Akses::roles('akademik.mapel'))->group(function () {
            Route::resource('mata-pelajaran', AdminMapelController::class)
                ->parameters(['mata-pelajaran' => 'mapel'])
                ->except('show')
                ->names('mapel');
        });
    });
});

// ==========================================
// 5. PORTAL WALI SANTRI ROUTES (Protected)
// ==========================================
Route::middleware(['auth', 'role:wali_santri'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/dashboard', [PortalDashboard::class, 'index'])->name('dashboard');
    Route::get('/nilai', [PortalAkademikController::class, 'index'])->name('nilai');
    Route::get('/nilai/cetak', [PortalAkademikController::class, 'cetak'])->name('nilai.cetak');

    // Rapor PDF: pratinjau (inline) dan unduh, hanya untuk wali pemilik data.
    Route::get('/nilai/dokumen/{dokumen}', [PortalAkademikController::class, 'dokumenLihat'])->name('nilai.dokumen');
    Route::get('/nilai/dokumen/{dokumen}/unduh', [PortalAkademikController::class, 'dokumenUnduh'])->name('nilai.dokumen.unduh');
    Route::get('/prestasi', [PortalPrestasiController::class, 'index'])->name('prestasi');
});
