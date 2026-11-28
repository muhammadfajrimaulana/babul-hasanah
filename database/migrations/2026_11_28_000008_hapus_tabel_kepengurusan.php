<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Fitur kartu pimpinan di /profil dihapus. Tabel lama tidak dipakai lagi.
     */
    public function up(): void
    {
        Schema::dropIfExists('kepengurusan');
    }

    /**
     * Kembalikan struktur lama bila migrasi dibatalkan. Data tidak dipulihkan.
     */
    public function down(): void
    {
        Schema::create('kepengurusan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jabatan');
            $table->string('foto')->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }
};
