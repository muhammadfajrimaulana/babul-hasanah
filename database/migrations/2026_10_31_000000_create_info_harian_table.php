<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('info_harian', function (Blueprint $table) {
            $table->id();
            $table->string('jenis', 10)->default('info');   // info | jadwal
            $table->string('kategori', 30)->nullable();     // khusus jenis info
            $table->string('judul', 150);                   // judul info / nama kegiatan jadwal
            $table->text('isi')->nullable();
            $table->string('jam', 10)->nullable();          // khusus jenis jadwal, contoh 04.30
            $table->date('tanggal')->nullable();            // mulai tayang
            $table->date('berlaku_sampai')->nullable();     // kosong = tidak kedaluwarsa
            $table->boolean('penting')->default(false);     // tampil di bar INFO berjalan
            $table->boolean('aktif')->default(true);
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();

            $table->index(['jenis', 'aktif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('info_harian');
    }
};
