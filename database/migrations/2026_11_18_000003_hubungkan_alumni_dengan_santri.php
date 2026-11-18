<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menghubungkan Data Alumni dengan santri yang sudah lulus.
     *
     * - santri.status_kelulusan : aktif / lulus
     * - santri.tahun_lulus      : angkatan kelulusan santri
     * - alumni.santri_id        : relasi satu-satu ke santri asal, dipakai
     *                             untuk mencegah data alumni ganda
     */
    public function up(): void
    {
        if (Schema::hasTable('santri')) {
            Schema::table('santri', function (Blueprint $table) {
                if (! Schema::hasColumn('santri', 'status_kelulusan')) {
                    $table->string('status_kelulusan', 20)
                        ->default('aktif')
                        ->after('kelas')
                        ->index();
                }

                if (! Schema::hasColumn('santri', 'tahun_lulus')) {
                    $table->unsignedSmallInteger('tahun_lulus')
                        ->nullable()
                        ->after('status_kelulusan');
                }
            });
        }

        if (Schema::hasTable('alumni')) {
            Schema::table('alumni', function (Blueprint $table) {
                if (! Schema::hasColumn('alumni', 'santri_id')) {
                    $table->foreignId('santri_id')
                        ->nullable()
                        ->after('id')
                        ->unique()
                        ->constrained('santri')
                        ->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('alumni') && Schema::hasColumn('alumni', 'santri_id')) {
            Schema::table('alumni', function (Blueprint $table) {
                $table->dropConstrainedForeignId('santri_id');
            });
        }

        if (Schema::hasTable('santri')) {
            Schema::table('santri', function (Blueprint $table) {
                if (Schema::hasColumn('santri', 'tahun_lulus')) {
                    $table->dropColumn('tahun_lulus');
                }

                if (Schema::hasColumn('santri', 'status_kelulusan')) {
                    $table->dropColumn('status_kelulusan');
                }
            });
        }
    }
};
