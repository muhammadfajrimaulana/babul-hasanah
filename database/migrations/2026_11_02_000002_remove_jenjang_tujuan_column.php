<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pendaftaran_online', function (Blueprint $table) {
            // Hapus kolom 'jenjang_tujuan' yang lama karena sudah diganti field alamat
            $table->dropColumn('jenjang_tujuan');
        });
    }

    public function down(): void
    {
        // Kembalikan jika rollback (opsional)
        Schema::table('pendaftaran_online', function (Blueprint $table) {
            $table->string('jenjang_tujuan')->nullable();
        });
    }
};