<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\PendaftaranController as AdminPendaftaranController;
use App\Http\Controllers\Admin\SantriController as AdminDataSantriController;
use App\Http\Controllers\Admin\KontenController as AdminKontenController;
use App\Http\Controllers\Admin\ProgramController as AdminProgramController;
use App\Http\Controllers\Admin\KegiatanController as AdminKegiatanController;
use App\Http\Controllers\Admin\BeritaController as AdminBeritaController;
use App\Http\Controllers\Admin\GaleriController as AdminGaleriController;
use App\Http\Controllers\Portal\PortalDashboardController as PortalDashboard;
use App\Http\Controllers\Public\PendaftaranOnlineController;
use App\Http\Controllers\Public\PrestasiController;

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
    Route::get('/struktur-organisasi', fn () => view('pages.struktur-organisasi'))->name('struktur-organisasi');
    Route::get('/pembelajaran', fn () => view('pages.pembelajaran'))->name('pembelajaran');
});

// ######## JENJANG ########### //
// Halaman tiap jenjang ada di resources/views/pages/jenjang/{slug}.blade.php
Route::get('/jenjang/{slug}', fn (string $slug) => view('pages.jenjang.' . $slug))
    ->whereIn('slug', ['mdta', 'pps-wustha', 'mts', 'ma', 'pdf-wustha', 'pdf-ulya', 'mahad-aly'])
    ->name('jenjang.show');

// ######## BERITA, GALERI, KEGIATAN ########### //
Route::get('/berita', function () {
    return view('pages.berita');
})->name('berita.index');

Route::get('/galeri', function () {
    return view('pages.galeri');
})->name('galeri');

Route::get('/kegiatan', function () {
    return view('pages.kegiatan');
})->name('kegiatan');

// ######## PRESTASI SANTRI ########### //
Route::get('/prestasi', [PrestasiController::class, 'index'])->name('prestasi.index');

// ######## PENDAFTARAN ONLINE ########### //
Route::get('/pendaftaran', function () {
    return view('pages.pendaftaran');
})->name('pendaftaran.index');

Route::post('/pendaftaran', [PendaftaranOnlineController::class, 'store'])->name('pendaftaran.store');

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
Route::middleware(['auth', \App\Http\Middleware\RoleSuperAdmin::class])->prefix('super')->name('super.')->group(function () {
    Route::get('/pengaturan-sistem', [AdminDashboard::class, 'settings'])->name('settings');
});

// ==========================================
// 4. ADMIN ROUTES (Protected)
// ==========================================
Route::middleware(['auth', \App\Http\Middleware\RoleAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Pendaftaran
    Route::get('/pendaftaran', [AdminPendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::patch('/pendaftaran/{id}', [AdminPendaftaranController::class, 'updateStatus'])->name('pendaftaran.update');

    // Santri
    Route::get('/santri', [AdminDataSantriController::class, 'index'])->name('santri.index');
    Route::get('/santri/template', [AdminDataSantriController::class, 'downloadTemplate'])->name('santri.template');
    Route::post('/santri/import', [AdminDataSantriController::class, 'import'])->name('santri.import');
    Route::get('/santri/{id}', [AdminDataSantriController::class, 'detail'])->name('santri.detail');

    // Konten
    Route::get('/pengelolaan-konten', [AdminKontenController::class, 'index'])->name('konten.index');
    Route::put('/konten/update', [AdminKontenController::class, 'updateProfil'])->name('konten.update');

    // Pengurus
    Route::post('/pengurus', [AdminKontenController::class, 'storeKepengurusan'])->name('pengurus.store');
    Route::delete('/pengurus/{id}', [AdminKontenController::class, 'destroyKepengurusan'])->name('pengurus.destroy');

    // Pengelolaan lainnya
    Route::get('/pengelolaan-program', [AdminProgramController::class, 'index'])->name('program.index');
    Route::get('/pengelolaan-kegiatan', [AdminKegiatanController::class, 'index'])->name('kegiatan.index');
    Route::get('/pengelolaan-berita', [AdminBeritaController::class, 'index'])->name('berita.index');
    Route::get('/pengelolaan-galeri', [AdminGaleriController::class, 'index'])->name('galeri.index');
});

// ==========================================
// 5. PORTAL WALI SANTRI ROUTES (Protected)
// ==========================================
Route::middleware(['auth', \App\Http\Middleware\RoleWaliSantri::class])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/dashboard', [PortalDashboard::class, 'index'])->name('dashboard');
});

Route::get('/kontak', function () {
    return view('pages.kontak');
})->name('kontak');