<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul Portal Alumni — tautan tabel `alumni` ke akun dan data santri.
 *
 * Dengan `student_id` dan `user_id`, portal alumni dapat menemukan transkrip
 * dan ijazah milik akun yang login tanpa mencocokkan nama secara manual.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('alumni')) {
            return;
        }

        if (! Schema::hasColumn('alumni', 'student_id')) {
            Schema::table('alumni', function (Blueprint $table) {
                $table->foreignId('student_id')
                    ->nullable()
                    ->constrained('students')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('alumni', 'user_id')) {
            Schema::table('alumni', function (Blueprint $table) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        // Tautan data alumni tidak dihapus saat rollback.
    }
};
