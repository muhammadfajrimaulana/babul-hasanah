<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menjamin seluruh kolom yang dipakai form PPDB benar-benar ada di database.
 *
 * Migrasi ini sengaja dibuat idempoten agar tetap aman dijalankan pada database
 * yang riwayat migrasinya sudah tidak sinkron (mis. migrasi lama tercatat "run"
 * padahal isinya berbeda).
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->pastikanKolomPendaftaran();
        $this->pastikanKolomSantri();
    }

    public function down(): void
    {
        // Sengaja tidak menghapus kolom: data PPDB & Data Santri memakainya.
    }

    private function pastikanKolomPendaftaran(): void
    {
        if (! Schema::hasTable('pendaftaran_online')) {
            return;
        }

        // 1. Tambah kolom form PPDB yang belum ada.
        $definisi = [
            'no_hp_aktif'     => fn (Blueprint $table) => $table->string('no_hp_aktif', 30)->nullable(),
            'alamat_domisili' => fn (Blueprint $table) => $table->text('alamat_domisili')->nullable(),
            'alamat_sekolah'  => fn (Blueprint $table) => $table->text('alamat_sekolah')->nullable(),
            'tahun_lulus'     => fn (Blueprint $table) => $table->string('tahun_lulus', 4)->nullable(),
            'nama_ortu_wali'  => fn (Blueprint $table) => $table->string('nama_ortu_wali')->nullable(),
            'jenjang_tujuan'  => fn (Blueprint $table) => $table->string('jenjang_tujuan', 100)->nullable(),
            'email'           => fn (Blueprint $table) => $table->string('email')->nullable(),
            'nisn'            => fn (Blueprint $table) => $table->string('nisn', 20)->nullable(),
        ];

        foreach ($definisi as $nama => $buat) {
            if (! Schema::hasColumn('pendaftaran_online', $nama)) {
                Schema::table('pendaftaran_online', $buat);
            }
        }

        // 2. Kolom lama tidak lagi wajib diisi (form baru tidak mengirimnya).
        $longgarkan = [
            'nik'            => 255,
            'nama_ortu_wali' => 255,
            'jenjang_tujuan' => 100,
        ];

        foreach ($longgarkan as $nama => $panjang) {
            if (Schema::hasColumn('pendaftaran_online', $nama)) {
                Schema::table('pendaftaran_online', function (Blueprint $table) use ($nama, $panjang) {
                    $table->string($nama, $panjang)->nullable()->change();
                });
            }
        }
    }

    private function pastikanKolomSantri(): void
    {
        if (! Schema::hasTable('santri')) {
            return;
        }

        // 1. Relasi ke arsip pendaftaran.
        if (! Schema::hasColumn('santri', 'pendaftaran_online_id')) {
            Schema::table('santri', function (Blueprint $table) {
                $table->foreignId('pendaftaran_online_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('pendaftaran_online')
                    ->nullOnDelete();
            });
        }

        // 2. Longgarkan kolom agar 7 jenjang & data opsional aman disimpan.
        if (Schema::hasColumn('santri', 'jenjang_pendidikan')) {
            Schema::table('santri', function (Blueprint $table) {
                $table->string('jenjang_pendidikan', 100)->nullable()->change();
            });
        }

        if (Schema::hasColumn('santri', 'kelas')) {
            Schema::table('santri', function (Blueprint $table) {
                $table->string('kelas', 50)->nullable()->change();
            });
        }

        if (Schema::hasColumn('santri', 'alamat')) {
            Schema::table('santri', function (Blueprint $table) {
                $table->text('alamat')->nullable()->change();
            });
        }

        if (Schema::hasColumn('santri', 'nama_wali')) {
            Schema::table('santri', function (Blueprint $table) {
                $table->string('nama_wali')->nullable()->change();
            });
        }

        // 3. user_id boleh kosong (santri tanpa akun portal).
        if (Schema::hasColumn('santri', 'user_id') && DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE santri MODIFY user_id BIGINT UNSIGNED NULL');
        }
    }
};
