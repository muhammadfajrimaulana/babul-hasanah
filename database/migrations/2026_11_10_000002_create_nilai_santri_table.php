<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nilai_santri', function (Blueprint $table) {
            $table->id();

            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->cascadeOnDelete();

            // Snapshot jenjang & kelas saat nilai diinput, supaya riwayat tidak
            // berubah ketika santri naik kelas.
            $table->string('jenjang', 100);
            $table->string('kelas', 50);
            $table->string('tahun_ajaran', 20); // contoh: 2026/2027
            $table->string('semester', 10);     // ganjil | genap

            $table->string('kategori', 20);     // tugas | uts | uas | hafalan
            $table->decimal('nilai', 5, 2);
            $table->string('catatan', 255)->nullable();

            // draft = belum terlihat wali, published = sudah terbit.
            $table->string('status', 20)->default('draft');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('diperbarui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('diterbitkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['santri_id', 'mata_pelajaran_id', 'tahun_ajaran', 'semester', 'kategori'],
                'nilai_santri_unik'
            );
            $table->index(
                ['jenjang', 'kelas', 'tahun_ajaran', 'semester', 'status'],
                'nilai_scope_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_santri');
    }
};
