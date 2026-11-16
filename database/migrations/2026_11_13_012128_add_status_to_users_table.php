<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambah kolom `status` pada tabel users.
 *
 * Nilai:
 * - `active`  : akun sudah diverifikasi dan boleh masuk.
 * - `pending` : akun menunggu verifikasi Admin Jenjang (khusus wali santri
 *               yang mendaftar mandiri melalui PPDB).
 *
 * Idempoten: aman bila kolom sudah ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users') || Schema::hasColumn('users', 'status')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['active', 'pending'])->default('pending')->after('role');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'status')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
