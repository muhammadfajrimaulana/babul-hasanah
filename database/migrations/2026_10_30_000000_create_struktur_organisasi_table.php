<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Lewati jika tabel sudah dibuat sebelumnya.
        if (Schema::hasTable('struktur_organisasi')) {
            return;
        }

        Schema::create('struktur_organisasi', function (Blueprint $table) {
            $table->id();
            $table->string('kategori', 20)->index();            // bagan | jenjang | bidang
            $table->foreignId('parent_id')->nullable()
                ->constrained('struktur_organisasi')->nullOnDelete();
            $table->string('nama', 150);
            $table->string('jabatan', 150)->nullable();
            $table->string('kode', 30)->nullable();
            $table->string('keterangan')->nullable();
            $table->string('ikon', 50)->nullable();
            $table->string('foto')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('struktur_organisasi');
    }
};
