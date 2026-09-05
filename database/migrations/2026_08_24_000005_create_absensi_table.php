<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->onDelete('cascade');
            $table->string('jenis_kegiatan'); // Contoh: Sholat Berjamaah, Tahfidz, Sekolah Formal
            $table->text('keterangan')->nullable();
            $table->enum('status_kehadiran', ['hadir', 'izin', 'sakit', 'alpa'])->default('hadir');
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};