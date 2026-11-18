<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen_nilai', function (Blueprint $table) {
            $table->id();

            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();

            // Snapshot jenjang & kelas saat dokumen diunggah, supaya riwayat
            // tetap sesuai kondisi saat rapor diterbitkan.
            $table->string('jenjang', 100);
            $table->string('kelas', 50)->nullable();
            $table->string('tahun_ajaran', 20); // contoh: 2026/2027
            $table->string('semester', 10);     // ganjil | genap

            $table->string('judul', 150)->default('Rapor');
            $table->string('file_path');
            $table->string('file_name', 200);
            $table->unsignedBigInteger('file_size')->default(0);

            // draft = belum terlihat wali, published = sudah terbit.
            $table->string('status', 20)->default('published');

            $table->foreignId('diunggah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            // Satu rapor PDF per santri per semester; unggah ulang menimpa.
            $table->unique(
                ['santri_id', 'tahun_ajaran', 'semester'],
                'dokumen_nilai_unik'
            );
            $table->index(
                ['jenjang', 'tahun_ajaran', 'semester', 'status'],
                'dokumen_nilai_scope_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_nilai');
    }
};
