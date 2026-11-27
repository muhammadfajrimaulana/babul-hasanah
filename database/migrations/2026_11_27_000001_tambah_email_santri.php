<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Email pribadi santri dipakai sebagai username akun alumni saat kelulusan.
     *
     * Sebelum kolom ini ada, satu-satunya email yang tersedia adalah email
     * orang tua/wali sehingga akun alumni jatuh ke alamat @alumni....
     */
    public function up(): void
    {
        if (Schema::hasTable('pendaftaran_online') && ! Schema::hasColumn('pendaftaran_online', 'email_santri')) {
            Schema::table('pendaftaran_online', function (Blueprint $table) {
                $table->string('email_santri', 255)->nullable()->after('email');
            });
        }

        if (Schema::hasTable('santri') && ! Schema::hasColumn('santri', 'email_santri')) {
            Schema::table('santri', function (Blueprint $table) {
                $table->string('email_santri', 255)->nullable()->after('nama_wali');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('santri') && Schema::hasColumn('santri', 'email_santri')) {
            Schema::table('santri', function (Blueprint $table) {
                $table->dropColumn('email_santri');
            });
        }

        if (Schema::hasTable('pendaftaran_online') && Schema::hasColumn('pendaftaran_online', 'email_santri')) {
            Schema::table('pendaftaran_online', function (Blueprint $table) {
                $table->dropColumn('email_santri');
            });
        }
    }
};
