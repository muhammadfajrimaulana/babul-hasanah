<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambah kolom profil pada tabel users:
 * - avatar  : path foto profil (disk "public")
 * - jenjang : jenjang pendidikan yang ditangani seorang admin (opsional)
 *
 * Idempoten: aman dijalankan meskipun kolom sudah ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'jenjang')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('jenjang', 100)->nullable()->after('role');
            });
        }

        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'avatar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('avatar')->nullable()->after('phone');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'avatar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('avatar');
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'jenjang')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('jenjang');
            });
        }
    }
};
