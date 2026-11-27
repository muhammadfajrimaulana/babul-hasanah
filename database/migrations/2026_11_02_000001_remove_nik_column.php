<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('pendaftaran_online', 'nik')) {
            return;
        }

        // SQLite menolak drop kolom yang masih dipakai indeks UNIQUE.
        // MySQL menghapus indeks itu otomatis, jadi pemeriksaan ini aman
        // untuk kedua driver dan membuat RefreshDatabase bisa dijalankan.
        if (Schema::hasIndex('pendaftaran_online', ['nik'], 'unique')) {
            Schema::table('pendaftaran_online', function (Blueprint $table) {
                $table->dropUnique(['nik']);
            });
        }

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