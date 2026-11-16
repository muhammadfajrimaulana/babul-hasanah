<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul PPDB — tautan tabel `students` ke arsip PPDB dan data santri lama.
 *
 * - `pendaftaran_online_id`: santri yang lahir dari verifikasi PPDB.
 * - `santri_id`: pasangan baris di tabel lama `santri` (dipakai portal wali &
 *   modul yang belum dimigrasi). Boleh kosong untuk data hasil import Excel.
 *
 * Idempoten: aman dijalankan ulang.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('students')) {
            return;
        }

        if (! Schema::hasColumn('students', 'pendaftaran_online_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->foreignId('pendaftaran_online_id')
                    ->nullable()
                    ->after('jenjang')
                    ->constrained('pendaftaran_online')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('students', 'santri_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->foreignId('santri_id')
                    ->nullable()
                    ->after('pendaftaran_online_id')
                    ->constrained('santri')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('students')) {
            return;
        }

        if (Schema::hasColumn('students', 'santri_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropConstrainedForeignId('santri_id');
            });
        }

        if (Schema::hasColumn('students', 'pendaftaran_online_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropConstrainedForeignId('pendaftaran_online_id');
            });
        }
    }
};
