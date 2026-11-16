<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->id();
            // Slug jenjang resmi (mdta, mts, ma, dst.) agar konsisten dengan App\Support\Jenjang.
            $table->string('jenjang', 100);
            $table->string('kode', 30);
            $table->string('nama', 150);
            $table->string('kelompok', 50)->nullable(); // Umum / Diniyah / Tahfidz
            $table->unsignedTinyInteger('kkm')->default(70);
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->unique(['jenjang', 'kode'], 'mapel_jenjang_kode_unik');
            $table->index(['jenjang', 'aktif'], 'mapel_jenjang_aktif_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_pelajaran');
    }
};
