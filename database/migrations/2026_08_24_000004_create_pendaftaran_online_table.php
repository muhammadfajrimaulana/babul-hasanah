<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pendaftaran_online', function (Blueprint $table) {
            $table->id();
            $table->string('no_pendaftaran')->unique();
            $table->string('nama_lengkap');
            $table->string('nik')->unique();
            $table->string('tempat_tanggal_lahir');
            $table->string('jenjang_tujuan');
            $table->string('asal_sekolah');
            $table->string('nama_ortu_wali');
            $table->string('no_hp_ortu');
            $table->enum('status_pendaftaran', ['pending', 'diterima', 'ditolak'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_online');
    }
};