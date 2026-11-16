<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul 1 — tabel `grades` (nilai santri).
 *
 * Satu baris = satu nilai untuk kombinasi student + subject + tahun ajaran +
 * semester + kategori (tugas/uts/uas/hafalan).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('grades')) {
            return;
        }

        Schema::create('grades', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();

            $table->string('tahun_ajaran', 9);
            $table->string('semester', 10);
            $table->string('kategori', 20);
            $table->decimal('nilai', 5, 2);
            $table->text('catatan')->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamp('published_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                ['student_id', 'subject_id', 'tahun_ajaran', 'semester', 'kategori'],
                'grades_unik_siswa_mapel_periode_kategori'
            );
            $table->index(['class_id', 'tahun_ajaran', 'semester']);
            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
