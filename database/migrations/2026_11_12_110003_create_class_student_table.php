<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul PPDB — tabel pivot `class_student`.
 *
 * Menyimpan penempatan santri ke kelas. Satu santri hanya berada pada satu
 * kelas aktif; `students.class_id` tetap disinkronkan sebagai kolom cache agar
 * query lama/modul akademik tidak perlu join.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('class_student')) {
            return;
        }

        Schema::create('class_student', function (Blueprint $table) {
            $table->id();

            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['class_id', 'student_id'], 'class_student_unique');
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_student');
    }
};
