<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Dibatalkan: kolom `jenjang_tujuan` masih dipakai oleh form PPDB,
     * tabel admin, dan proses verifikasi menjadi data santri.
     */
    public function up(): void
    {
        // sengaja dikosongkan
    }

    public function down(): void
    {
        // sengaja dikosongkan
    }
};
