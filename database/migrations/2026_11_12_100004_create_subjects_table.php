<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul 1 — tabel `subjects` (mata pelajaran per jenjang).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('subjects')) {
            return;
        }

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('jenjang', 100);
            $table->string('kode', 20);
            $table->string('nama');
            $table->string('kelompok', 50)->nullable();
            $table->unsignedTinyInteger('kkm')->default(75);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['jenjang', 'kode'], 'subjects_jenjang_kode_unique');
            $table->index('jenjang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
