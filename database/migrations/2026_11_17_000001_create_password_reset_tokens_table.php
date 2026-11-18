<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel penyimpan token reset kata sandi.
     *
     * `token` berisi HASH dari token acak yang dikirim lewat email
     * (token asli tidak pernah disimpan di database). `created_at`
     * dipakai untuk mengecek masa berlaku 30 menit.
     *
     * Dibuat dengan pengecekan agar aman dijalankan berulang.
     */
    public function up(): void
    {
        if (Schema::hasTable('password_reset_tokens')) {
            return;
        }

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
