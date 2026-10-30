<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pendaftaran_online', function (Blueprint $table) {
            // Hapus kolom 'nama_ortu_wali' yang lama karena sudah diganti nama_ayah + nama_ibu
            $table->dropColumn('nama_ortu_wali');
        });
    }

    public function down(): void
    {
        // Kembalikan jika rollback (opsional)
        Schema::table('pendaftaran_online', function (Blueprint $table) {
            $table->string('nama_ortu_wali');
        });
    }
};