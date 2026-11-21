<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Finalisasi enam role resmi:
 * super_admin, pengurus, pembina, admin, wali_santri, alumni.
 *
 * Perubahan penting:
 * - role lama `pengasuh` diganti menjadi `pengurus`
 * - role `alumni` ditambahkan
 * - akun memiliki status aktif & kewajiban ganti password sementara
 * - akun alumni ditautkan ke tabel alumni
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        // Rename nilai lama sebelum ENUM diubah agar data tidak hilang.
        if (Schema::hasColumn('users', 'role')) {
            DB::table('users')->where('role', 'pengasuh')->update(['role' => 'pengurus']);
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->enum('role', [
                    'super_admin',
                    'pengurus',
                    'pembina',
                    'admin',
                    'wali_santri',
                    'alumni',
                ])->default('wali_santri')->change();
            }

            if (! Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }

            if (! Schema::hasColumn('users', 'must_change_password')) {
                $table->boolean('must_change_password')->default(false)->after('is_active');
            }

            if (! Schema::hasColumn('users', 'temp_password_expires_at')) {
                $table->timestamp('temp_password_expires_at')->nullable()->after('must_change_password');
            }

            if (! Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('temp_password_expires_at');
            }
        });

        if (Schema::hasTable('alumni') && ! Schema::hasColumn('alumni', 'user_id')) {
            Schema::table('alumni', function (Blueprint $table) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->unique()
                    ->after('santri_id')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (Schema::hasColumn('users', 'last_login_at')) {
            Schema::table('users', fn (Blueprint $table) => $table->dropColumn('last_login_at'));
        }

        if (Schema::hasColumn('users', 'temp_password_expires_at')) {
            Schema::table('users', fn (Blueprint $table) => $table->dropColumn('temp_password_expires_at'));
        }

        if (Schema::hasColumn('users', 'must_change_password')) {
            Schema::table('users', fn (Blueprint $table) => $table->dropColumn('must_change_password'));
        }

        if (Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_active'));
        }

        if (Schema::hasColumn('alumni', 'user_id')) {
            Schema::table('alumni', fn (Blueprint $table) => $table->dropConstrainedForeignId('user_id'));
        }

        DB::table('users')->where('role', 'pengurus')->update(['role' => 'pengasuh']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'admin', 'pengasuh', 'pembina', 'wali_santri'])
                ->default('wali_santri')
                ->change();
        });
    }
};
