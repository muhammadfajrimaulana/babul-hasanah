<?php

use App\Http\Controllers\Admin\AlumniController as AdminAlumniController;
use App\Http\Controllers\Admin\BeritaController as AdminBeritaController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\GaleriController as AdminGaleriController;
use App\Http\Controllers\Admin\InfoHarianController as AdminInfoHarianController;
use App\Http\Controllers\Admin\KegiatanController as AdminKegiatanController;
use App\Http\Controllers\Admin\KontenController as AdminKontenController;
use App\Http\Controllers\Admin\PendaftaranController as AdminPendaftaranController;
use App\Http\Controllers\Admin\PenggunaController as AdminPenggunaController;
use App\Http\Controllers\Admin\PrestasiAdminController;
use App\Http\Controllers\Admin\ProgramController as AdminProgramController;
use App\Http\Controllers\Admin\SantriController as AdminDataSantriController;
use App\Http\Controllers\Admin\SantriFormController as AdminSantriFormController;
use App\Http\Controllers\Admin\StrukturOrganisasiController as AdminStrukturController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Portal\PortalDashboardController as PortalDashboard;
use App\Http\Controllers\Public\AlumniController as PublicAlumniController;
use App\Http\Controllers\Public\BeritaController as PublicBeritaController;
use App\Http\Controllers\Public\GaleriController as PublicGaleriController;
use App\Http\Controllers\Public\InfoHarianController as PublicInfoHarianController;
use App\Http\Controllers\Public\PendaftaranOnlineController;
use App\Http\Controllers\Public\PrestasiController;
use App\Http\Controllers\Public\StrukturOrganisasiController;
use App\Http\Middleware\CheckRole; // Hanya pakai CheckRole tunggal
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
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');

// ==========================================
// 3. SUPER ADMIN ROUTES (Protected)
// ==========================================
Route::middleware(['auth', 'role:super_admin'])->prefix('super')->name('super.')->group(function () {
    Route::get('/pengaturan-sistem', [AdminDashboard::class, 'settings'])->name('settings');

    // Pengguna dashboard (CRUD)
    Route::resource('pengguna', AdminPenggunaController::class)
        ->parameters(['pengguna' => 'pengguna'])
        ->except('show')
        ->names('pengguna');
});

// ==========================================
// 4. ADMIN & PENGASUH ROUTES (Protected)
// ==========================================
// Mengizinkan super_admin, admin, dan pengasuh masuk ke dashboard
Route::middleware(['auth', 'role:super_admin,admin,pengasuh,pembina'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Profil & Pengaturan Akun
    Route::get('/profil-saya', [AdminDashboard::class, 'profil'])->name('profil');
    Route::put('/profil-saya', [AdminDashboard::class, 'updateProfil'])->name('profil.update');
    Route::put('/profil-saya/password', [AdminDashboard::class, 'updatePassword'])->name('profil.password');
    Route::post('/profil-saya/avatar', [AdminDashboard::class, 'updateAvatar'])->name('profil.avatar');

    // Pendaftaran
    Route::get('/pendaftaran', [AdminPendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::patch('/pendaftaran/{id}', [AdminPendaftaranController::class, 'updateStatus'])->name('pendaftaran.verifikasi');
    Route::get('/pendaftaran/{id}/edit', [AdminPendaftaranController::class, 'edit'])->name('pendaftaran.edit');
    Route::put('/pendaftaran/{id}', [AdminPendaftaranController::class, 'update'])->name('pendaftaran.update');
    Route::delete('/pendaftaran/{id}', [AdminPendaftaranController::class, 'destroy'])->name('pendaftaran.destroy');

    // Santri
    Route::get('/santri', [AdminDataSantriController::class, 'index'])->name('santri.index');
    Route::get('/santri/template', [AdminDataSantriController::class, 'downloadTemplate'])->name('santri.template');
    Route::post('/santri/import', [AdminDataSantriController::class, 'import'])->name('santri.import');
    Route::get('/santri/tambah', [AdminSantriFormController::class, 'create'])->name('santri.create');
    Route::post('/santri', [AdminSantriFormController::class, 'store'])->name('santri.store');
    Route::get('/santri/{id}', [AdminDataSantriController::class, 'detail'])->name('santri.detail');
    Route::get('/santri/{id}/edit', [AdminSantriFormController::class, 'edit'])->name('santri.edit');
    Route::put('/santri/{id}', [AdminSantriFormController::class, 'update'])->name('santri.update');
    Route::delete('/santri/{id}', [AdminDataSantriController::class, 'destroy'])->name('santri.destroy');

    // Alumni (CRUD)
    Route::resource('data-alumni', AdminAlumniController::class)
        ->parameters(['data-alumni' => 'alumni'])
        ->except('show')
        ->names('alumni');

    // Konten
    Route::get('/pengelolaan-konten', [AdminKontenController::class, 'index'])->name('konten.index');
    Route::post('/konten', [AdminKontenController::class, 'store'])->name('konten.store');
    Route::put('/konten/update', [AdminKontenController::class, 'updateProfil'])->name('konten.update');

    // Pengelolaan lainnya
    Route::get('/pengelolaan-program', [AdminProgramController::class, 'index'])->name('program.index');
    Route::get('/pengelolaan-kegiatan', [AdminKegiatanController::class, 'index'])->name('kegiatan.index');

    // Berita (CRUD)
    Route::resource('pengelolaan-berita', AdminBeritaController::class)
        ->parameters(['pengelolaan-berita' => 'berita'])
        ->except('show')
        ->names('berita');

    Route::post('/pengelolaan-berita/upload-gambar', [AdminBeritaController::class, 'uploadGambar'])
        ->name('berita.upload-gambar');

    // Galeri
    Route::get('/pengelolaan-galeri', [AdminGaleriController::class, 'index'])->name('galeri.index');
    Route::get('/pengelolaan-galeri/tambah', [AdminGaleriController::class, 'create'])->name('galeri.create');
    Route::post('/pengelolaan-galeri', [AdminGaleriController::class, 'store'])->name('galeri.store');
    Route::delete('/pengelolaan-galeri/{galeri}', [AdminGaleriController::class, 'destroy'])->name('galeri.destroy');

    // Prestasi santri
    Route::get('/pengelolaan-prestasi', [PrestasiAdminController::class, 'index'])->name('prestasi.index');
    Route::get('/pengelolaan-prestasi/tambah', [PrestasiAdminController::class, 'create'])->name('prestasi.create');
    Route::post('/pengelolaan-prestasi', [PrestasiAdminController::class, 'store'])->name('prestasi.store');
    Route::get('/pengelolaan-prestasi/{prestasi}/edit', [PrestasiAdminController::class, 'edit'])->name('prestasi.edit');
    Route::put('/pengelolaan-prestasi/{prestasi}', [PrestasiAdminController::class, 'update'])->name('prestasi.update');
    Route::delete('/pengelolaan-prestasi/{prestasi}', [PrestasiAdminController::class, 'destroy'])->name('prestasi.destroy');

    // Struktur organisasi (CRUD)
    Route::resource('struktur-organisasi', AdminStrukturController::class)
        ->parameters(['struktur-organisasi' => 'struktur'])
        ->except('show')
        ->names('struktur');

    // Info harian beranda (Khusus Super Admin di dalam grup admin)
    Route::middleware('role:super_admin')->group(function () {
        Route::resource('info-harian', AdminInfoHarianController::class)
            ->parameters(['info-harian' => 'info'])
            ->except('show')
            ->names('info');
    });
});

// ==========================================
// 5. PORTAL WALI SANTRI ROUTES (Protected)
// ==========================================
Route::middleware(['auth', 'role:wali_santri'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/dashboard', [PortalDashboard::class, 'index'])->name('dashboard');
});