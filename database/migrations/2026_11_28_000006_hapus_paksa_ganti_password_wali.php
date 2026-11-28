<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Wali santri login memakai kredensial yang dikirim ke email setelah
     * pendaftaran diterima, jadi tidak dipaksa mengganti password sementara.
     *
     * Migrasi ini membersihkan flag pada akun wali yang sudah dibuat sebelumnya.
     */
    public function up(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'must_change_password')) {
            return;
        }

        DB::table('users')
            ->whereIn('role', ['wali_santri', 'wali', 'walisantri', 'orangtua'])
            ->update([
                'must_change_password' => false,
                'temp_password_expires_at' => null,
            ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'must_change_password')) {
            return;
        }

        DB::table('users')
            ->whereIn('role', ['wali_santri', 'wali', 'walisantri', 'orangtua'])
            ->update([
                'must_change_password' => true,
                'temp_password_expires_at' => now()->addDay(),
            ]);
    }
};
