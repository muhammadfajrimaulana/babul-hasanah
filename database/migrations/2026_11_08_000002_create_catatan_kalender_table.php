<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catatan pada kalender dashboard admin.
 * Satu tanggal bisa punya banyak catatan dari beberapa admin.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('catatan_kalender')) {
            return;
        }

        Schema::create('catatan_kalender', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal');
            $table->string('catatan', 500);
            $table->string('warna', 20)->nullable();
            $table->timestamps();

            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_kalender');
    }
};
