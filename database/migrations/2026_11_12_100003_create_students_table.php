<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul 1 — tabel `students`.
 *
 * Kolom wajib modul: id, nisn, name, parent_email, status.
 * Kolom tambahan dipakai untuk menautkan akun wali (user_id), kelas (class_id),
 * dan scoping jenjang (jenjang) agar portal wali bisa memakai data ini.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('students')) {
            return;
        }

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nisn', 30)->unique();
            $table->string('name');
            $table->string('parent_email')->nullable();
            $table->string('status', 20)->default('aktif');

            $table->foreignId('user_id')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->foreignId('class_id')->nullable()
                ->constrained('classes')->nullOnDelete();
            $table->string('jenjang', 100)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['jenjang', 'status']);
            $table->index('parent_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
