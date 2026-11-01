<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Menyelaraskan skema PPDB dengan form terbaru:
     * Nama Lengkap, Tempat Tanggal Lahir, No. HP Aktif (WA),
     * Alamat Domisili, Email, Nama Orang Tua/Wali, No. HP Orang Tua,
     * Sekolah Asal, Alamat Sekolah, Tahun Lulus, NISN, Pilihan Jenjang.
     */
    public function up(): void
    {
        if (! Schema::hasTable('pendaftaran_online')) {
            return;
        }

        // 1. Tambah kolom baru hanya jika belum ada.
        foreach (['no_hp_aktif' => 'string', 'tahun_lulus' => 'string'] as $nama => $tipe) {
            if (! Schema::hasColumn('pendaftaran_online', $nama)) {
                Schema::table('pendaftaran_online', function (Blueprint $table) use ($nama, $tipe) {
                    if ($tipe === 'string') {
                        $table->string($nama, $nama === 'tahun_lulus' ? 4 : 30)->nullable();
                    }
                });
            }
        }

        foreach (['alamat_domisili', 'alamat_sekolah'] as $nama) {
            if (! Schema::hasColumn('pendaftaran_online', $nama)) {
                Schema::table('pendaftaran_online', function (Blueprint $table) use ($nama) {
                    $table->text($nama)->nullable();
                });
            }
        }

        // 2. Pastikan kolom yang dipakai form/admin/verifikasi tetap ada.
        if (! Schema::hasColumn('pendaftaran_online', 'nama_ortu_wali')) {
            Schema::table('pendaftaran_online', function (Blueprint $table) {
                $table->string('nama_ortu_wali')->nullable();
            });
        }

        if (! Schema::hasColumn('pendaftaran_online', 'jenjang_tujuan')) {
            Schema::table('pendaftaran_online', function (Blueprint $table) {
                $table->string('jenjang_tujuan', 100)->nullable();
            });
        }

        // 3. Kolom lama yang tidak dipakai lagi harus boleh kosong.
        foreach (['nik', 'nama_ortu_wali', 'jenjang_tujuan'] as $nama) {
            if (Schema::hasColumn('pendaftaran_online', $nama)) {
                Schema::table('pendaftaran_online', function (Blueprint $table) use ($nama) {
                    $table->string($nama, $nama === 'jenjang_tujuan' ? 100 : 255)
                        ->nullable()
                        ->change();
                });
            }
        }

        // 4. Tabel santri: longgarkan kolom agar 7 jenjang & data opsional aman.
        if (Schema::hasTable('santri')) {
            Schema::table('santri', function (Blueprint $table) {
                if (Schema::hasColumn('santri', 'jenjang_pendidikan')) {
                    $table->string('jenjang_pendidikan', 100)->nullable()->change();
                }

                if (Schema::hasColumn('santri', 'kelas')) {
                    $table->string('kelas', 50)->nullable()->change();
                }

                if (Schema::hasColumn('santri', 'alamat')) {
                    $table->text('alamat')->nullable()->change();
                }

                if (Schema::hasColumn('santri', 'nama_wali')) {
                    $table->string('nama_wali')->nullable()->change();
                }
            });
        }
    }

    public function down(): void
    {
        // Sengaja tidak mengembalikan perubahan: kolom-kolom ini dipakai
        // oleh fitur PPDB dan Data Santri yang sedang berjalan.
    }
};
