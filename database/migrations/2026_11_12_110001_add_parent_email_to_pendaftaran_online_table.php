<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul PPDB — kolom `parent_email` pada tabel pendaftaran_online.
 *
 * Sebelumnya email orang tua/wali disimpan pada kolom `email`. Kolom baru ini
 * membuat maksudnya eksplisit. Keduanya tetap disinkronkan oleh aplikasi agar
 * data & tampilan lama tidak rusak.
 *
 * Idempoten: aman dijalankan ulang.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pendaftaran_online')) {
            return;
        }

        if (! Schema::hasColumn('pendaftaran_online', 'parent_email')) {
            Schema::table('pendaftaran_online', function (Blueprint $table) {
                $table->string('parent_email', 255)->nullable()->after('email');
                $table->index('parent_email');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('pendaftaran_online')) {
            return;
        }

        if (Schema::hasColumn('pendaftaran_online', 'parent_email')) {
            Schema::table('pendaftaran_online', function (Blueprint $table) {
                $table->dropIndex(['parent_email']);
                $table->dropColumn('parent_email');
            });
        }
    }
};
