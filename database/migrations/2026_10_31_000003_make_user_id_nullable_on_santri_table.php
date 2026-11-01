<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Santri yang diinput manual boleh belum punya akun wali,
     * jadi kolom user_id harus boleh kosong.
     */
    public function up(): void
    {
        if (Schema::hasColumn('santri', 'user_id') && DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE santri MODIFY user_id BIGINT UNSIGNED NULL');
        }
    }

    public function down(): void
    {
        // Sengaja dikosongkan: mengembalikan ke NOT NULL akan gagal
        // jika sudah ada santri tanpa akun wali.
    }
};
