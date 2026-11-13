<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul Portal Alumni — tabel `tracer_study`.
 *
 * Satu akun alumni mengisi satu formulir tracer study. Data ini dipakai
 * pesantren untuk memantau kegiatan lulusan (kuliah, bekerja, wirausaha, dst.).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tracer_study')) {
            return;
        }

        Schema::create('tracer_study', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('alumni_id')->nullable()->constrained('alumni')->nullOnDelete();

            $table->string('nama');
            $table->unsignedSmallInteger('tahun_lulus')->nullable();
            $table->string('jenjang', 50)->nullable();

            $table->string('kegiatan', 50);
            $table->string('instansi')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('bidang')->nullable();
            $table->unsignedSmallInteger('mulai_tahun')->nullable();

            $table->string('no_hp', 30)->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->text('testimoni')->nullable();
            $table->boolean('bersedia_dihubungi')->default(false);

            $table->timestamps();

            $table->unique('user_id', 'tracer_study_user_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracer_study');
    }
};
