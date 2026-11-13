<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul 1 — tabel `classes` (rombel/kelas per jenjang).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('classes')) {
            return;
        }

        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('jenjang', 100);
            $table->string('name', 50);
            $table->string('tingkat', 20)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['jenjang', 'name'], 'classes_jenjang_name_unique');
            $table->index('jenjang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
