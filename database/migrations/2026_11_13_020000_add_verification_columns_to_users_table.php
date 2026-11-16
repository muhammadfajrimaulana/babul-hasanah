<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom pendukung alur verifikasi akun wali santri.
 *
 * - `verified_at`          : waktu akun diverifikasi Admin Jenjang.
 * - `password_generated_at`: waktu kata sandi otomatis dibuat/dikirim.
 *
 * Migrasi ini juga memastikan `status` tersedia (bila migrasi sebelumnya
 * belum dijalankan) dan mengaktifkan akun lama yang bukan wali santri agar
 * tidak ikut terkunci oleh aturan login "pending".
 *
 * Idempoten: aman dijalankan berulang.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'pending'])->default('pending')->after('role');
            }

            if (! Schema::hasColumn('users', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('email_verified_at');
            }

            if (! Schema::hasColumn('users', 'password_generated_at')) {
                $table->timestamp('password_generated_at')->nullable()->after('verified_at');
            }
        });

        // Akun internal (bukan wali santri) yang sudah ada sebelumnya tetap
        // aktif. Akun wali santri lama juga diaktifkan karena konsep "pending"
        // baru berlaku untuk pendaftaran PPDB setelah migrasi ini.
        DB::table('users')
            ->whereIn('role', ['super_admin', 'admin', 'pengasuh', 'pembina', 'alumni'])
            ->update(['status' => 'active']);

        DB::table('users')
            ->where('role', 'wali_santri')
            ->update(['status' => 'active']);
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'password_generated_at')) {
                $table->dropColumn('password_generated_at');
            }

            if (Schema::hasColumn('users', 'verified_at')) {
                $table->dropColumn('verified_at');
            }
        });
    }
};
