<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Modul 1 — penyesuaian tabel users.
 *
 * - `role` diubah dari ENUM menjadi VARCHAR agar bisa menampung peran baru
 *   (`alumni`) dan nilai hasil sinkronisasi Spatie Permission.
 * - Menambah `softDeletes` (akun tidak benar-benar hilang saat dihapus).
 * - `jenjang` & `avatar` sudah ditambahkan pada migrasi 2026_11_08.
 *
 * Idempoten: aman dijalankan ulang.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (! Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Ubah ENUM lama menjadi VARCHAR(50) supaya fleksibel.
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 50)->default('wali_santri')->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        // ENUM lama tidak mengenal 'alumni'; amankan dulu sebelum rollback.
        if (Schema::hasColumn('users', 'role')) {
            DB::table('users')
                ->whereNotIn('role', ['super_admin', 'admin', 'pengasuh', 'pembina', 'wali_santri'])
                ->update(['role' => 'wali_santri']);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'admin', 'pengasuh', 'pembina', 'wali_santri'])
                ->default('wali_santri')
                ->change();
        });

        if (Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
