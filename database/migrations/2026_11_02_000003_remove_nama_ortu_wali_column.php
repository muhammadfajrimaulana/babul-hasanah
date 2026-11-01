<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Dibatalkan: kolom `nama_ortu_wali` dipakai kembali oleh form PPDB
     * (satu field "Nama Orang Tua / Wali").
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
