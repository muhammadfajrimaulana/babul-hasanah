<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pendaftaran_online', function (Blueprint $table) {
            // Hapus kolom 'nik' yang lama karena diganti menjadi nik_santri
            $table->dropColumn('nik');
        });
    }

    public function down(): void
    {
        // Kembalikan jika perlu (opsional, hanya jika rollback)
        Schema::table('pendaftaran_online', function (Blueprint $table) {
            $table->string('nik')->unique();
        });
    }
};