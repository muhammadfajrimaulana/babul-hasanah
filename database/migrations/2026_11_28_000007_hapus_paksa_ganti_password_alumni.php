<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Alumni mendapat kredensial lewat email saat dinyatakan lulus oleh admin.
     * Kredensial tersebut langsung dipakai, jadi flag password sementara pada
     * akun alumni lama perlu dibersihkan.
     */
    public function up(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'must_change_password')) {
            return;
        }

        DB::table('users')
            ->whereIn('role', ['alumni', 'alumnus'])
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
            ->whereIn('role', ['alumni', 'alumnus'])
            ->update([
                'must_change_password' => true,
                'temp_password_expires_at' => now()->addDay(),
            ]);
    }
};
