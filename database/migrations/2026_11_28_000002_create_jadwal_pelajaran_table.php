<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('jadwal_pelajaran')) {
            return;
        }

        Schema::create('jadwal_pelajaran', function (Blueprint $table) {
            $table->id();

            $table->string('jenjang', 100);
            $table->string('kelas', 50);
            $table->string('tahun_ajaran', 20);
            $table->string('semester', 10);
            $table->string('hari', 20);
            $table->time('jam_mulai');
            $table->time('jam_selesai');

            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->cascadeOnDelete();
            $table->foreignId('guru_id')->nullable()->constrained('guru')->nullOnDelete();

            $table->string('ruang', 50)->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(
                ['jenjang', 'kelas', 'tahun_ajaran', 'semester', 'hari'],
                'jadwal_scope_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelajaran');
    }
};
