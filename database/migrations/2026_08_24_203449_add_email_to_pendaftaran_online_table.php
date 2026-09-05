<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pendaftaran_online', function (Blueprint $table) {
            $table->string('email')->unique()->after('nama_ortu_wali');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran_online', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};
