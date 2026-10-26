<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->unsignedSmallInteger('tahun_lulus');
            $table->string('jenjang', 50)->nullable();
            $table->string('kegiatan', 30)->nullable();   // Kuliah / Bekerja / Wirausaha / Mengabdi / Lainnya
            $table->string('instansi')->nullable();       // kampus / tempat kerja
            $table->string('no_hp', 30)->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->text('testimoni')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni');
    }
};
