<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tagihan administrasi santri untuk Tab Keuangan Portal Wali.
 *
 * Satu baris = satu tagihan (SPP, daftar ulang, kitab, dan lain-lain).
 * `student_id` dipakai skema baru; `santri_id` menjadi jembatan untuk data
 * lama sehingga portal tetap bisa menampilkan tagihan santri lama.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tagihan_santri')) {
            return;
        }

        Schema::create('tagihan_santri', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')->nullable()
                ->constrained('students')->nullOnDelete();
            $table->foreignId('santri_id')->nullable()
                ->constrained('santri')->nullOnDelete();

            $table->string('jenis', 50);
            $table->string('periode', 30)->nullable();
            $table->decimal('nominal', 12, 2)->default(0);
            $table->decimal('dibayar', 12, 2)->default(0);
            $table->date('jatuh_tempo')->nullable();
            $table->string('status', 20)->default('belum');
            $table->timestamp('dibayar_at')->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->index(['student_id', 'status']);
            $table->index(['santri_id', 'status']);
            $table->index('jatuh_tempo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan_santri');
    }
};
