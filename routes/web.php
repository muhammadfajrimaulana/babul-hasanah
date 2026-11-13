<?php

use App\Http\Controllers\Admin\Akademik\GradeController as AdminNilaiController;
use App\Http\Controllers\Admin\Akademik\KenaikanKelasController as AdminKenaikanKelasController;
use App\Http\Controllers\Admin\Akademik\MataPelajaranController as AdminMapelController;
use App\Http\Controllers\Admin\Akademik\TranskripController as AdminTranskripController;
use App\Http\Controllers\Admin\AlumniController as AdminAlumniController;
use App\Http\Controllers\Admin\BeritaController as AdminBeritaController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\GaleriController as AdminGaleriController;
use App\Http\Controllers\Admin\ImportSantriController as AdminImportSantriController;
use App\Http\Controllers\Admin\InfoHarianController as AdminInfoHarianController;
use App\Http\Controllers\Admin\JenjangController as AdminJenjangController;
use App\Http\Controllers\Admin\KegiatanController as AdminKegiatanController;
use App\Http\Controllers\Admin\KontenController as AdminKontenController;
use App\Http\Controllers\Admin\PendaftaranController as AdminPendaftaranController;
use App\Http\Controllers\Admin\PenggunaController as AdminPenggunaController;
use App\Http\Controllers\Admin\PpdbController as AdminPpdbController;
use App\Http\Controllers\Admin\PrestasiAdminController;
use App\Http\Controllers\Admin\RoleDashboardController as AdminRoleDashboard;
use App\Http\Controllers\Admin\SantriController as AdminDataSantriController;
use App\Http\Controllers\Admin\SantriFormController as AdminSantriFormController;
use App\Http\Controllers\Admin\StrukturOrganisasiController as AdminStrukturController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Portal\PortalAkademikController;
use App\Http\Controllers\Portal\PortalAlumniController;
use App\Http\Controllers\Portal\PortalAnakController;
use App\Http\Controllers\Portal\PortalDashboardController as PortalDashboard;
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

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (Halaman Depan & Pendaftaran)
|--------------------------------------------------------------------------
| Rute ini diakses oleh publik & semua role.
*/

// ######## HALAMAN DEPAN ########### //
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

// Route::get('/kegiatan', function () {
//     return view('pages.kegiatan');
// })->name('kegiatan'); // Dihapus sementara sampai view dibuat (audit P0-1)

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

// ######## KONTAK ########### //
Route::get('/kontak', function () {
    return view('pages.kontak');
})->name('kontak');

/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
| Rute login & logout untuk semua user
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    // Batasi 6 percobaan per menit per IP untuk mencegah brute force.
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:6,1');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');

// ==========================================
// 3. ROUTES PENGGUNA (Super Admin + pengelola wali)
// ==========================================
// Super Admin: semua akun. Admin Jenjang/Pengasuh/Pembina: hanya akun wali
// santri (Admin Jenjang dibatasi ke jenjangnya oleh controller).
// ==========================================
Route::middleware(['auth'])->prefix('super')->name('super.')->group(function () {

    // 3.1 Dashboard per role
    Route::get('/dashboard', [AdminDashboard::class, 'settings'])
        ->middleware('role:super_admin')->name('settings');

    Route::middleware('role:'.Akses::roles('pengguna'))->group(function () {
        // Pengguna dashboard (CRUD)
        Route::resource('pengguna', AdminPenggunaController::class)
            ->parameters(['pengguna' => 'pengguna'])
            ->except('show')
            ->names('pengguna');

        // Reset password akun (khusus Super Admin)
        Route::post('/pengguna/{pengguna}/reset-password', [AdminPenggunaController::class, 'resetPassword'])
            ->middleware('role:super_admin')
            ->name('pengguna.reset-password');

        // Aktifkan akun yang masih pending (membuat & mengirim kata sandi baru)
        Route::post('/pengguna/{pengguna}/aktifkan', [AdminPenggunaController::class, 'aktifkan'])
            ->name('pengguna.aktifkan');
    });
});

// ==========================================
// 4. PANEL ADMIN — SATU DASHBOARD PER ROLE
// ==========================================
// Setiap role hanya bisa membuka dashboard-nya sendiri. Percobaan membuka
// dashboard role lain akan ditolak middleware `role` (403).
// ==========================================
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // 4.1 Dashboard per role
    Route::get('/dashboard', [AdminDashboard::class, 'index'])
        ->middleware('role:super_admin')->name('dashboard');

    Route::get('/dashboard-jenjang', [AdminRoleDashboard::class, 'adminJenjang'])
        ->middleware('role:admin')->name('dashboard.jenjang');

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
        // Import Excel & template (modul PPDB).
        Route::get('/santri/template', [AdminImportSantriController::class, 'template'])->name('santri.template');
        Route::get('/santri/import', [AdminImportSantriController::class, 'index'])->name('santri.import.index');
        Route::post('/santri/import', [AdminImportSantriController::class, 'import'])->name('santri.import');

        // Santri (aksi yang mengubah data) — didaftarkan lebih dulu agar
        // /santri/template tidak tertangkap oleh /santri/{id}.
        Route::get('/santri/tambah', [AdminSantriFormController::class, 'create'])->name('santri.create');
        Route::post('/santri', [AdminSantriFormController::class, 'store'])->name('santri.store');
        Route::get('/santri/{id}/edit', [AdminSantriFormController::class, 'edit'])->name('santri.edit');
        Route::put('/santri/{id}', [AdminSantriFormController::class, 'update'])->name('santri.update');
        Route::delete('/santri/{id}', [AdminDataSantriController::class, 'destroy'])->name('santri.destroy');

        // Pendaftaran (verifikasi & ubah)
        Route::patch('/pendaftaran/{id}', [AdminPendaftaranController::class, 'updateStatus'])->name('pendaftaran.verifikasi');
        Route::get('/pendaftaran/{id}/edit', [AdminPendaftaranController::class, 'edit'])->name('pendaftaran.edit');
        Route::put('/pendaftaran/{id}', [AdminPendaftaranController::class, 'update'])->name('pendaftaran.update');
        Route::delete('/pendaftaran/{id}', [AdminDataSantriController::class, 'destroy'])->name('pendaftaran.destroy');

        // Modul PPDB: verifikasi kelulusan + plotting ke kelas.
        Route::prefix('ppdb')->name('ppdb.')->group(function () {
            Route::get('/kelulusan', [AdminPpdbController::class, 'kelulusan'])->name('kelulusan');
            Route::patch('/kelulusan/{pendaftaran}', [AdminPpdbController::class, 'verifikasi'])->name('verifikasi');
            Route::get('/plotting', [AdminPpdbController::class, 'plotting'])->name('plotting');
            Route::post('/plotting', [AdminPpdbController::class, 'plot'])->name('plot');
            Route::post('/kelas', [AdminPpdbController::class, 'storeKelas'])->name('kelas.store');
        });

        // Alumni (CRUD, tanpa index yang sudah ada di grup baca)
        Route::get('/data-alumni/create', [AdminAlumniController::class, 'create'])->name('alumni.create');
        Route::post('/data-alumni', [AdminAlumniController::class, 'store'])->name('alumni.store');
        Route::get('/data-alumni/{alumni}/edit', [AdminAlumniController::class, 'edit'])->name('alumni.edit');
        Route::put('/data-alumni/{alumni}', [AdminAlumniController::class, 'update'])->name('alumni.update');
        Route::delete('/data-alumni/{alumni}', [AdminAlumniController::class, 'destroy'])->name('alumni.destroy');

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

        Route::get('/pengelolaan-prestasi', [PrestasiAdminController::class, 'index'])->name('prestasi.index');

        Route::get('/pengelolaan-jenjang', [AdminJenjangController::class, 'index'])->name('jenjang.index');
        Route::get('/pengelolaan-jenjang/{jenjang}', [AdminJenjangController::class, 'show'])->name('jenjang.show');

        // Nama lama dipertahankan agar tautan/bookmark lama tetap berfungsi.
        Route::get('/pengelolaan-program', fn () => redirect()->route('admin.jenjang.index'))->name('program.index');

        Route::get('/pengelolaan-kegiatan', [AdminKegiatanController::class, 'index'])->name('kegiatan.index'); // Dihapus sementara sampai view dibuat (audit P0-2)
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
            Route::get('/nilai/template', [AdminNilaiController::class, 'template'])->name('nilai.template');
            Route::get('/nilai/upload', [AdminNilaiController::class, 'upload'])->name('nilai.upload');
            Route::get('/transkrip', [AdminTranskripController::class, 'index'])->name('transkrip.index');
            Route::get('/transkrip/{santri}/cetak', [AdminTranskripController::class, 'cetak'])->name('transkrip.cetak');
            Route::get('/transkrip/{santri}', [AdminTranskripController::class, 'show'])->name('transkrip.show');

            // Kenaikan kelas & kelulusan santri (skema students + grades).
            Route::get('/kenaikan-kelas', [AdminKenaikanKelasController::class, 'index'])
                ->name('kenaikan-kelas.index');
        });

        // Tulis: hanya Super Admin & Admin Jenjang.
        Route::middleware('role:'.Akses::roles('akademik.ubah'))->group(function () {
            Route::post('/nilai', [AdminNilaiController::class, 'store'])->name('nilai.store');
            Route::post('/nilai/upload', [AdminNilaiController::class, 'preview'])->name('nilai.preview');
            Route::post('/nilai/import', [AdminNilaiController::class, 'import'])->name('nilai.import');

            Route::post('/kenaikan-kelas/proses', [AdminKenaikanKelasController::class, 'proses'])
                ->name('kenaikan-kelas.proses');
            Route::post('/kenaikan-kelas/tahun-ajaran', [AdminKenaikanKelasController::class, 'simpanTahun'])
                ->name('kenaikan-kelas.tahun');
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
    Route::post('/anak', [PortalAnakController::class, 'store'])->name('anak.pilih');
    Route::get('/nilai', [PortalAkademikController::class, 'index'])->name('nilai');
    Route::get('/nilai/pdf', [PortalAkademikController::class, 'pdf'])->name('nilai.pdf');
    Route::get('/nilai/cetak', [PortalAkademikController::class, 'cetak'])->name('nilai.cetak');
});

// 5b. PORTAL ALUMNI (Protected)
// ==========================================
Route::middleware(['auth', 'role:alumni'])->prefix('alumni')->name('alumni.')->group(function () {
    Route::get('/dashboard', [PortalAlumniController::class, 'index'])->name('dashboard');
    Route::get('/transkrip/pdf', [PortalAlumniController::class, 'transkripPdf'])->name('transkrip.pdf');
    Route::get('/ijazah/pdf', [PortalAlumniController::class, 'ijazahPdf'])->name('ijazah.pdf');
    Route::post('/tracer-study', [PortalAlumniController::class, 'simpanTracer'])->name('tracer.simpan');
});

// Kompatibilitas tautan lama sebelum Portal Alumni dipindahkan ke /alumni.
Route::middleware(['auth', 'role:alumni'])
    ->get('/alumni-area/dashboard', fn () => redirect()->route('alumni.dashboard'))
    ->name('alumni-area.dashboard');

// 6. Wali Santri Beranda (tanpa prefix portal, hanya role wali_santri)
// Diakses dari: http://127.0.0.1:8000/wali/dashboard
Route::middleware(['auth', 'role:wali_santri'])->prefix('wali')->name('wali.')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Admin\WaliSantriController::class, 'index'])->name('dashboard');
    Route::get('profil', [\App\Http\Controllers\Admin\WaliSantriController::class, 'profil'])->name('profil');
    Route::get('nilai', [\App\Http\Controllers\Admin\WaliSantriController::class, 'nilai'])->name('nilai');
    Route::get('anak', [\App\Http\Controllers\Admin\WaliSantriController::class, 'anak'])->name('anak');
    Route::get('pendaftaran', [\App\Http\Controllers\Admin\WaliSantriController::class, 'pendaftaran'])->name('pendaftaran');
});