<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('santri') || Schema::hasColumn('santri', 'pendaftaran_online_id')) {
            return;
        }

        Schema::table('santri', function (Blueprint $table) {
            $table->foreignId('pendaftaran_online_id')
                ->nullable()
                ->after('user_id')
                ->constrained('pendaftaran_online')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('santri', 'pendaftaran_online_id')) {
            Schema::table('santri', function (Blueprint $table) {
                $table->dropConstrainedForeignId('pendaftaran_online_id');
            });
        }
    }
};
