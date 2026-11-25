<?php

use App\Http\Controllers\Admin\Akademik\MataPelajaranController as AdminMapelController;
use App\Http\Controllers\Admin\Akademik\NilaiController as AdminNilaiController;
use App\Http\Controllers\Admin\Akademik\TranskripController as AdminTranskripController;
use App\Http\Controllers\Admin\AlumniController as AdminAlumniController;
use App\Http\Controllers\Admin\BeritaController as AdminBeritaController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\DokumenAlumniController;
use App\Http\Controllers\Admin\GaleriController as AdminGaleriController;
use App\Http\Controllers\Admin\InfoHarianController as AdminInfoHarianController;
use App\Http\Controllers\Admin\IzinController as AdminIzinController;
use App\Http\Controllers\Admin\JenjangController as AdminJenjangController;
use App\Http\Controllers\Admin\KegiatanController as AdminKegiatanController;
use App\Http\Controllers\Admin\KelasController as AdminKelasController;
use App\Http\Controllers\Admin\KontenController as AdminKontenController;
use App\Http\Controllers\Admin\LegalisirController as AdminLegalisirController;
use App\Http\Controllers\Admin\LogAktivitasController;
use App\Http\Controllers\Admin\PembinaanController;
use App\Http\Controllers\Admin\PendaftaranController as AdminPendaftaranController;
use App\Http\Controllers\Admin\PengajuanPerubahanController as AdminPengajuanController;
use App\Http\Controllers\Admin\PenggunaController as AdminPenggunaController;
use App\Http\Controllers\Admin\PengumumanController as AdminPengumumanController;
use App\Http\Controllers\Admin\PrestasiAdminController;
use App\Http\Controllers\Admin\RoleDashboardController as AdminRoleDashboard;
use App\Http\Controllers\Admin\SantriAksiController;
use App\Http\Controllers\Admin\SantriController as AdminDataSantriController;
use App\Http\Controllers\Admin\SantriFormController as AdminSantriFormController;
use App\Http\Controllers\Admin\StrukturOrganisasiController as AdminStrukturController;
use App\Http\Controllers\Alumni\AlumniPortalController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GantiPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Portal\PortalAkademikController;
use App\Http\Controllers\Portal\PortalDashboardController as PortalDashboard;
use App\Http\Controllers\Portal\PortalDokumenController;
use App\Http\Controllers\Portal\PortalIzinController;
use App\Http\Controllers\Portal\PortalPengajuanController;
use App\Http\Controllers\Portal\PortalPengumumanController;
use App\Http\Controllers\Portal\PortalPrestasiController;
use App\Http\Controllers\Portal\PortalSantriController;
use App\Http\Controllers\Public\AlumniController as PublicAlumniController;
use App\Http\Controllers\Public\BeritaController as PublicBeritaController;
use App\Http\Controllers\Public\GaleriController as PublicGaleriController;
use App\Http\Controllers\Public\InfoHarianController as PublicInfoHarianController;
use App\Http\Controllers\Public\PendaftaranOnlineController;
use App\Http\Controllers\Public\PrestasiController;
use App\Http\Controllers\Public\StrukturOrganisasiController;
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
    Route::get('/fasilitas', fn () => view('pages.fasilitas'))->name('profil.fasilitas');
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

// Wajib ganti password sementara (24 jam). Tidak bisa dilewati.
Route::middleware('auth')->group(function () {
    Route::get('/ganti-password', [GantiPasswordController::class, 'show'])->name('password.change');
    Route::put('/ganti-password', [GantiPasswordController::class, 'update'])->name('password.change.update');
});

// ==========================================
// 3. ROUTES PENGGUNA — hanya Superadmin
// ==========================================
Route::middleware(['auth'])->prefix('super')->name('super.')->group(function () {
    Route::get('/pengaturan-sistem', [AdminDashboard::class, 'settings'])
        ->middleware('role:super_admin')->name('settings');

    Route::middleware('role:'.Akses::roles('pengguna'))->group(function () {
        Route::resource('pengguna', AdminPenggunaController::class)
            ->parameters(['pengguna' => 'pengguna'])
            ->except('show')
            ->names('pengguna');
    });
});

// ==========================================
// 4. PANEL ADMIN — SATU DASHBOARD PER ROLE
// ==========================================
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // 4.1 Dashboard per role
    Route::get('/dashboard', [AdminDashboard::class, 'index'])
        ->middleware('role:super_admin')->name('dashboard');

    Route::get('/dashboard-jenjang', [AdminRoleDashboard::class, 'adminJenjang'])
        ->middleware('role:admin,super_admin')->name('dashboard.jenjang');

    Route::get('/dashboard-pengurus', [AdminRoleDashboard::class, 'pengurus'])
        ->middleware('role:pengurus')->name('dashboard.pengurus');

    // Alias lama agar tautan lama tidak rusak.
    Route::get('/dashboard-pengasuh', fn () => redirect()->route('admin.dashboard.pengurus'))
        ->middleware('role:pengurus')->name('dashboard.pengasuh');

    Route::get('/dashboard-pembina', [AdminRoleDashboard::class, 'pembina'])
        ->middleware('role:pembina')->name('dashboard.pembina');

    // Halaman input nilai lama dialihkan ke menu Akademik → Nilai Santri.
    Route::get('/dashboard-jenjang/input-nilai', [AdminRoleDashboard::class, 'formNilai'])
        ->middleware('role:admin,super_admin,pengurus')->name('dashboard.jenjang.nilai.form');

    Route::get('/dashboard-jenjang/input-prestasi', [AdminRoleDashboard::class, 'formPrestasi'])
        ->middleware('role:admin,super_admin,pengurus')->name('dashboard.jenjang.prestasi.form');

    Route::post('/dashboard-jenjang/nilai', [AdminRoleDashboard::class, 'simpanNilai'])
        ->middleware('role:admin,super_admin')->name('dashboard.jenjang.nilai');

    Route::post('/dashboard-jenjang/prestasi', [AdminRoleDashboard::class, 'simpanPrestasi'])
        ->middleware('role:admin,super_admin')->name('dashboard.jenjang.prestasi');

    Route::post('/dashboard-jenjang/nilai/pdf', [AdminRoleDashboard::class, 'simpanDokumenNilai'])
        ->middleware('role:admin,super_admin')->name('dashboard.jenjang.nilai.pdf');

    Route::post('/dashboard-jenjang/nilai/pdf/massal', [AdminRoleDashboard::class, 'pdfMassal'])
        ->middleware('role:admin,super_admin')->name('dashboard.jenjang.nilai.pdf.massal');

    Route::get('/dashboard-jenjang/nilai/pdf/{dokumen}/unduh', [AdminRoleDashboard::class, 'unduhDokumenNilai'])
        ->middleware('role:admin,super_admin,pengurus,pembina')->name('dashboard.jenjang.nilai.pdf.unduh');

    Route::delete('/dashboard-jenjang/nilai/pdf/{dokumen}', [AdminRoleDashboard::class, 'hapusDokumenNilai'])
        ->middleware('role:admin,super_admin')->name('dashboard.jenjang.nilai.pdf.hapus');

    // 4.2 Profil akun sendiri (semua peran panel)
    Route::middleware('role:'.Akses::roles('profil'))->group(function () {
        Route::get('/profil-saya', [AdminDashboard::class, 'profil'])->name('profil');
        Route::put('/profil-saya', [AdminDashboard::class, 'updateProfil'])->name('profil.update');
        Route::put('/profil-saya/password', [AdminDashboard::class, 'updatePassword'])->name('profil.password');
        Route::post('/profil-saya/avatar', [AdminDashboard::class, 'updateAvatar'])->name('profil.avatar');
    });

    // 4.3 Operasional (tulis) — hanya Superadmin & Admin Jenjang
    Route::middleware('role:'.Akses::roles('santri.ubah'))->group(function () {
        // Santri (aksi yang mengubah data) — didaftarkan lebih dulu agar
        // /santri/template tidak tertangkap oleh /santri/{id}.
        Route::get('/santri/template', [AdminDataSantriController::class, 'downloadTemplate'])->name('santri.template');
        Route::post('/santri/import', [AdminDataSantriController::class, 'import'])->name('santri.import');
        Route::get('/santri/naik-kelas', [SantriAksiController::class, 'naikKelasForm'])->name('santri.naik-kelas');
        Route::post('/santri/naik-kelas', [SantriAksiController::class, 'naikKelasProses'])->name('santri.naik-kelas.proses');
        Route::get('/santri/tambah', [AdminSantriFormController::class, 'create'])->name('santri.create');
        Route::post('/santri', [AdminSantriFormController::class, 'store'])->name('santri.store');
        Route::get('/santri/{id}/edit', [AdminSantriFormController::class, 'edit'])->name('santri.edit');
        Route::put('/santri/{id}', [AdminSantriFormController::class, 'update'])->name('santri.update');
        Route::delete('/santri/{id}', [AdminDataSantriController::class, 'destroy'])->name('santri.destroy');

        // Aksi santri: reset wali, pindah jenjang, arsip, kelulusan.
        Route::post('/santri/{id}/reset-password-wali', [SantriAksiController::class, 'resetPasswordWali'])->name('santri.reset-wali');
        Route::post('/santri/{id}/pindah-jenjang', [SantriAksiController::class, 'pindahJenjang'])->name('santri.pindah-jenjang');
        Route::post('/santri/{id}/arsipkan', [SantriAksiController::class, 'arsipkan'])->name('santri.arsipkan');
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

        // Berkas nilai/transkrip alumni.
        Route::post('/data-alumni/{alumni}/nilai', [AdminAlumniController::class, 'uploadNilai'])->name('alumni.nilai.upload');
        Route::delete('/data-alumni/{alumni}/nilai', [AdminAlumniController::class, 'hapusNilai'])->name('alumni.nilai.hapus');

        // Prestasi (CRUD, tanpa index)
        Route::get('/pengelolaan-prestasi/tambah', [PrestasiAdminController::class, 'create'])->name('prestasi.create');
        Route::post('/pengelolaan-prestasi', [PrestasiAdminController::class, 'store'])->name('prestasi.store');
        Route::get('/pengelolaan-prestasi/{prestasi}/edit', [PrestasiAdminController::class, 'edit'])->name('prestasi.edit');
        Route::put('/pengelolaan-prestasi/{prestasi}', [PrestasiAdminController::class, 'update'])->name('prestasi.update');
        Route::delete('/pengelolaan-prestasi/{prestasi}', [PrestasiAdminController::class, 'destroy'])->name('prestasi.destroy');

        // Dokumen kelulusan (ijazah, SKL, transkrip, sertifikat) — diunggah admin.
        Route::post('/santri/{id}/dokumen', [DokumenAlumniController::class, 'store'])->name('santri.dokumen.store');
        Route::delete('/dokumen/{dokumen}', [DokumenAlumniController::class, 'destroy'])->name('santri.dokumen.hapus');

        // Pengajuan perubahan data dari wali: setujui / tolak.
        Route::patch('/pengajuan-perubahan/{pengajuan}/setujui', [AdminPengajuanController::class, 'setujui'])->name('pengajuan.setujui');
        Route::patch('/pengajuan-perubahan/{pengajuan}/tolak', [AdminPengajuanController::class, 'tolak'])->name('pengajuan.tolak');

        // Proses legalisir alumni (unggah hasil).
        Route::patch('/legalisir/{legalisir}', [AdminLegalisirController::class, 'proses'])->name('legalisir.proses');
    });

    // Rollback kenaikan kelas — khusus Superadmin, wajib alasan.
    Route::post('/riwayat-kelas/{riwayat}/rollback', [SantriAksiController::class, 'rollbackNaikKelas'])
        ->middleware('role:super_admin')->name('riwayat.rollback');

    Route::post('/santri/{id}/kembalikan', [SantriAksiController::class, 'kembalikan'])
        ->middleware('role:super_admin')->name('santri.kembalikan');

    // 4.4 Konten website — Superadmin & Admin Jenjang
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
    Route::middleware('role:'.Akses::roles('pendaftaran.lihat'))->group(function () {
        Route::get('/pendaftaran', [AdminPendaftaranController::class, 'index'])->name('pendaftaran.index');
    });

    Route::middleware('role:'.Akses::roles('santri.lihat'))->group(function () {
        Route::get('/santri', [AdminDataSantriController::class, 'index'])->name('santri.index');
        Route::get('/santri/{id}', [AdminDataSantriController::class, 'detail'])->name('santri.detail');

        Route::get('/pengelolaan-prestasi', [PrestasiAdminController::class, 'index'])->name('prestasi.index');

        Route::get('/pengelolaan-jenjang', [AdminJenjangController::class, 'index'])->name('jenjang.index');
        Route::get('/pengelolaan-jenjang/{jenjang}', [AdminJenjangController::class, 'show'])->name('jenjang.show');

        Route::get('/pengelolaan-program', fn () => redirect()->route('admin.jenjang.index'))->name('program.index');

        Route::get('/pengelolaan-kegiatan', [AdminKegiatanController::class, 'index'])->name('kegiatan.index');

        // Pengajuan perubahan data dari wali.
        Route::get('/pengajuan-perubahan', [AdminPengajuanController::class, 'index'])->name('pengajuan.index');
    });

    Route::middleware('role:'.Akses::roles('alumni.lihat'))->group(function () {
        Route::get('/data-alumni', [AdminAlumniController::class, 'index'])->name('alumni.index');
        Route::get('/data-alumni/{alumni}/nilai', [AdminAlumniController::class, 'unduhNilai'])->name('alumni.nilai.unduh');
        Route::get('/data-alumni/{alumni}', [AdminAlumniController::class, 'show'])->name('alumni.show');

        // Antrean legalisir alumni.
        Route::get('/legalisir', [AdminLegalisirController::class, 'index'])->name('legalisir.index');
    });

    // 4.6 Pembinaan, izin, pengumuman, kelas & guru
    Route::middleware('role:'.Akses::roles('pembinaan.lihat'))->group(function () {
        Route::get('/pembinaan', [PembinaanController::class, 'index'])->name('pembinaan.index');
    });

    Route::middleware('role:'.Akses::roles('pembinaan.ubah'))->group(function () {
        Route::post('/pembinaan/hafalan', [PembinaanController::class, 'simpanHafalan'])->name('pembinaan.hafalan.store');
        Route::delete('/pembinaan/hafalan/{hafalan}', [PembinaanController::class, 'hapusHafalan'])->name('pembinaan.hafalan.hapus');
        Route::post('/pembinaan/pelanggaran', [PembinaanController::class, 'simpanPelanggaran'])->name('pembinaan.pelanggaran.store');
        Route::delete('/pembinaan/pelanggaran/{pelanggaran}', [PembinaanController::class, 'hapusPelanggaran'])->name('pembinaan.pelanggaran.hapus');
        Route::post('/pembinaan/absensi', [PembinaanController::class, 'simpanAbsensi'])->name('pembinaan.absensi.store');
    });

    Route::middleware('role:'.Akses::roles('izin.lihat'))->group(function () {
        Route::get('/izin', [AdminIzinController::class, 'index'])->name('izin.index');
    });

    Route::middleware('role:'.Akses::roles('izin.verifikasi'))->group(function () {
        Route::patch('/izin/{izin}/setujui', [AdminIzinController::class, 'setujui'])->name('izin.setujui');
        Route::patch('/izin/{izin}/tolak', [AdminIzinController::class, 'tolak'])->name('izin.tolak');
        Route::patch('/izin/{izin}/kembali', [AdminIzinController::class, 'kembali'])->name('izin.kembali');
    });

    Route::middleware('role:'.Akses::roles('pengumuman.lihat'))->group(function () {
        Route::get('/pengumuman', [AdminPengumumanController::class, 'index'])->name('pengumuman.index');
    });

    Route::middleware('role:'.Akses::roles('pengumuman.ubah'))->group(function () {
        Route::post('/pengumuman', [AdminPengumumanController::class, 'store'])->name('pengumuman.store');
        Route::put('/pengumuman/{pengumuman}', [AdminPengumumanController::class, 'update'])->name('pengumuman.update');
        Route::delete('/pengumuman/{pengumuman}', [AdminPengumumanController::class, 'destroy'])->name('pengumuman.destroy');
    });

    Route::middleware('role:'.Akses::roles('kelas.lihat'))->group(function () {
        Route::get('/kelas', [AdminKelasController::class, 'index'])->name('kelas.index');
        Route::get('/kelas/{kelas}/rekap', [AdminKelasController::class, 'rekap'])->name('kelas.rekap');
    });

    Route::middleware('role:'.Akses::roles('kelas.ubah'))->group(function () {
        Route::post('/kelas', [AdminKelasController::class, 'store'])->name('kelas.store');
        Route::put('/kelas/{kelas}', [AdminKelasController::class, 'update'])->name('kelas.update');
        Route::delete('/kelas/{kelas}', [AdminKelasController::class, 'destroy'])->name('kelas.destroy');
    });

    Route::middleware('role:'.Akses::roles('guru.ubah'))->group(function () {
        Route::post('/guru', [AdminKelasController::class, 'storeGuru'])->name('guru.store');
        Route::put('/guru/{guru}', [AdminKelasController::class, 'updateGuru'])->name('guru.update');
        Route::delete('/guru/{guru}', [AdminKelasController::class, 'destroyGuru'])->name('guru.destroy');
    });

    // 4.7 Pengelolaan institusional — hanya Superadmin
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

    Route::middleware('role:'.Akses::roles('info'))->group(function () {
        Route::resource('info-harian', AdminInfoHarianController::class)
            ->parameters(['info-harian' => 'info'])
            ->except('show')
            ->names('info');
    });

    // 4.8 Log aktivitas — hanya Superadmin
    Route::get('/log-aktivitas', [LogAktivitasController::class, 'index'])
        ->middleware('role:'.Akses::roles('log'))->name('log.index');

    // 4.9 Akademik — nilai, transkrip, dan master mata pelajaran.
    Route::prefix('akademik')->name('akademik.')->group(function () {
        Route::middleware('role:'.Akses::roles('akademik.lihat'))->group(function () {
            Route::get('/nilai', [AdminNilaiController::class, 'index'])->name('nilai.index');
            Route::get('/transkrip', [AdminTranskripController::class, 'index'])->name('transkrip.index');
            Route::get('/transkrip/{santri}/cetak', [AdminTranskripController::class, 'cetak'])->name('transkrip.cetak');
            Route::get('/transkrip/{santri}', [AdminTranskripController::class, 'show'])->name('transkrip.show');
        });

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
// 5. PORTAL WALI SANTRI
// ==========================================
Route::middleware(['auth', 'role:wali_santri'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/dashboard', [PortalDashboard::class, 'index'])->name('dashboard');
    Route::get('/nilai', [PortalAkademikController::class, 'index'])->name('nilai');
    Route::get('/nilai/cetak', [PortalAkademikController::class, 'cetak'])->name('nilai.cetak');

    Route::get('/nilai/dokumen/{dokumen}', [PortalAkademikController::class, 'dokumenLihat'])->name('nilai.dokumen');
    Route::get('/nilai/dokumen/{dokumen}/unduh', [PortalAkademikController::class, 'dokumenUnduh'])->name('nilai.dokumen.unduh');
    Route::get('/prestasi', [PortalPrestasiController::class, 'index'])->name('prestasi');

    // Detail anak (hanya baca) + seluruh riwayat.
    Route::get('/anak/{santri}', [PortalSantriController::class, 'show'])->name('anak.show');

    // Pengumuman sesuai jenjang anak.
    Route::get('/pengumuman', [PortalPengumumanController::class, 'index'])->name('pengumuman.index');

    // Izin pulang.
    Route::get('/izin', [PortalIzinController::class, 'index'])->name('izin.index');
    Route::post('/izin', [PortalIzinController::class, 'store'])->name('izin.store');

    // Ajukan perubahan data anak.
    Route::get('/pengajuan', [PortalPengajuanController::class, 'index'])->name('pengajuan.index');
    Route::post('/pengajuan', [PortalPengajuanController::class, 'store'])->name('pengajuan.store');

    // Dokumen kelulusan anak (ijazah, SKL, transkrip, sertifikat).
    Route::get('/dokumen', [PortalDokumenController::class, 'index'])->name('dokumen.index');
    Route::get('/dokumen/{dokumen}/unduh', [PortalDokumenController::class, 'unduh'])->name('dokumen.unduh');
});

// ==========================================
// 6. PORTAL ALUMNI
// ==========================================
Route::middleware(['auth', 'role:alumni'])->prefix('alumni')->name('alumni.')->group(function () {
    Route::get('/dashboard', [AlumniPortalController::class, 'dashboard'])->name('dashboard');

    Route::get('/profil', [AlumniPortalController::class, 'profil'])->name('profil');
    Route::put('/profil', [AlumniPortalController::class, 'updateProfil'])->name('profil.update');

    // History nilai terkunci (hanya baca).
    Route::get('/nilai', [AlumniPortalController::class, 'nilai'])->name('nilai');

    Route::get('/dokumen', [AlumniPortalController::class, 'dokumen'])->name('dokumen');
    Route::get('/dokumen/{dokumen}/unduh', [AlumniPortalController::class, 'unduhDokumen'])->name('dokumen.unduh');

    Route::get('/tracer-study', [AlumniPortalController::class, 'tracer'])->name('tracer');
    Route::put('/tracer-study', [AlumniPortalController::class, 'updateTracer'])->name('tracer.update');

    Route::get('/legalisir', [AlumniPortalController::class, 'legalisir'])->name('legalisir');
    Route::post('/legalisir', [AlumniPortalController::class, 'ajukanLegalisir'])->name('legalisir.store');
    Route::get('/legalisir/{legalisir}/unduh', [AlumniPortalController::class, 'unduhLegalisir'])->name('legalisir.unduh');

    Route::get('/pengumuman', [AlumniPortalController::class, 'pengumuman'])->name('pengumuman');
});
