<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul Kelulusan — tanggal kelulusan santri.
 *
 * Status kelulusan disimpan di `students.status` (`graduated`). Kolom
 * `graduated_at` dipakai untuk mencetak ijazah digital dan tracer study.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('students') || Schema::hasColumn('students', 'graduated_at')) {
            return;
        }

        Schema::table('students', function (Blueprint $table) {
            $table->timestamp('graduated_at')->nullable();
        });
    }

    public function down(): void
    {
        // Data kelulusan tidak dihapus saat rollback.
    }
};
