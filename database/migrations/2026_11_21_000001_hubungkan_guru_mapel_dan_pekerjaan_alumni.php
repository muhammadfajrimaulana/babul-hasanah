<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Melengkapi data akademik & alumni:
     *
     * - mata_pelajaran.guru_id : guru pengampu yang namanya tampil di halaman
     *   Nilai per mata pelajaran (guru tetap tanpa akun login).
     * - alumni.pekerjaan       : pekerjaan/jabatan alumni untuk tracer study.
     *
     * Ditulis idempoten agar aman dijalankan pada database lama.
     */
    public function up(): void
    {
        if (
            Schema::hasTable('mata_pelajaran')
            && Schema::hasTable('guru')
            && ! Schema::hasColumn('mata_pelajaran', 'guru_id')
        ) {
            Schema::table('mata_pelajaran', function (Blueprint $table) {
                $table->foreignId('guru_id')->nullable()
                    ->constrained('guru')->nullOnDelete();
            });
        }

        if (Schema::hasTable('alumni') && ! Schema::hasColumn('alumni', 'pekerjaan')) {
            Schema::table('alumni', function (Blueprint $table) {
                $table->string('pekerjaan', 150)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('mata_pelajaran') && Schema::hasColumn('mata_pelajaran', 'guru_id')) {
            Schema::table('mata_pelajaran', function (Blueprint $table) {
                $table->dropConstrainedForeignId('guru_id');
            });
        }

        if (Schema::hasTable('alumni') && Schema::hasColumn('alumni', 'pekerjaan')) {
            Schema::table('alumni', function (Blueprint $table) {
                $table->dropColumn('pekerjaan');
            });
        }
    }
};
