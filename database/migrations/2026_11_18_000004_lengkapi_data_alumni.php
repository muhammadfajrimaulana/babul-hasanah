<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Melengkapi data alumni:
     *
     * - nisn              : nomor induk siswa nasional
     * - tahun_masuk       : tahun mulai mondok
     * - nilai_path        : berkas PDF nilai/transkrip alumni
     * - nilai_nama        : nama asli berkas untuk diunduh
     * - nilai_diunggah_pada : waktu unggah terakhir
     */
    public function up(): void
    {
        if (! Schema::hasTable('alumni')) {
            return;
        }

        Schema::table('alumni', function (Blueprint $table) {
            if (! Schema::hasColumn('alumni', 'nisn')) {
                $table->string('nisn', 30)->nullable()->after('nama');
            }

            if (! Schema::hasColumn('alumni', 'tahun_masuk')) {
                $table->unsignedSmallInteger('tahun_masuk')->nullable()->after('tahun_lulus');
            }

            if (! Schema::hasColumn('alumni', 'nilai_path')) {
                $table->string('nilai_path')->nullable()->after('foto');
            }

            if (! Schema::hasColumn('alumni', 'nilai_nama')) {
                $table->string('nilai_nama')->nullable()->after('nilai_path');
            }

            if (! Schema::hasColumn('alumni', 'nilai_diunggah_pada')) {
                $table->timestamp('nilai_diunggah_pada')->nullable()->after('nilai_nama');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('alumni')) {
            return;
        }

        Schema::table('alumni', function (Blueprint $table) {
            foreach (['nilai_diunggah_pada', 'nilai_nama', 'nilai_path', 'tahun_masuk', 'nisn'] as $kolom) {
                if (Schema::hasColumn('alumni', $kolom)) {
                    $table->dropColumn($kolom);
                }
            }
        });
    }
};
