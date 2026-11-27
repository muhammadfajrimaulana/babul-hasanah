<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Menandai santri sebagai terhapus tanpa menghilangkan baris database.
     *
     * Riwayat nilai, izin, absensi, dokumen, alumni, dan tautan PPDB tetap
     * tersimpan sehingga Superadmin bisa memulihkan data bila terjadi salah
     * hapus. Kolom ini sengaja nullable dan diberi indeks oleh softDeletes().
     */
    public function up(): void
    {
        if (! Schema::hasTable('santri') || Schema::hasColumn('santri', 'deleted_at')) {
            return;
        }

        Schema::table('santri', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('santri', 'deleted_at')) {
            Schema::table('santri', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
