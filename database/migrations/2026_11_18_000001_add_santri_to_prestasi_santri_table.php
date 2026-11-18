<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Prestasi kini dapat ditautkan ke data santri.
     *
     * - santri_id : relasi ke tabel santri (untuk mengetahui wali pemilik akun)
     * - jenjang    : snapshot jenjang saat prestasi dicatat, dipakai untuk
     *                membatasi data Admin Jenjang
     * - dicatat_oleh : admin yang menginput prestasi
     */
    public function up(): void
    {
        if (! Schema::hasTable('prestasi_santri')) {
            return;
        }

        Schema::table('prestasi_santri', function (Blueprint $table) {
            if (! Schema::hasColumn('prestasi_santri', 'santri_id')) {
                $table->foreignId('santri_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('santri')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('prestasi_santri', 'jenjang')) {
                $table->string('jenjang', 100)->nullable()->after('santri_id')->index();
            }

            if (! Schema::hasColumn('prestasi_santri', 'dicatat_oleh')) {
                $table->foreignId('dicatat_oleh')
                    ->nullable()
                    ->after('deskripsi')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('prestasi_santri')) {
            return;
        }

        Schema::table('prestasi_santri', function (Blueprint $table) {
            if (Schema::hasColumn('prestasi_santri', 'dicatat_oleh')) {
                $table->dropConstrainedForeignId('dicatat_oleh');
            }

            if (Schema::hasColumn('prestasi_santri', 'jenjang')) {
                $table->dropColumn('jenjang');
            }

            if (Schema::hasColumn('prestasi_santri', 'santri_id')) {
                $table->dropConstrainedForeignId('santri_id');
            }
        });
    }
};
