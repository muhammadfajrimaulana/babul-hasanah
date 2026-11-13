<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Pastikan tabel `grades` memiliki kolom status publikasi.
 *
 * Migrasi awal Modul 1 sudah membuat kolom ini. Migrasi ini menjadi pengaman
 * untuk basis data lama yang tabel `grades`-nya dibuat sebelum kolom status
 * ditambahkan. Idempoten: aman dijalankan berulang.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('grades')) {
            return;
        }

        Schema::table('grades', function (Blueprint $table) {
            if (! Schema::hasColumn('grades', 'status')) {
                $table->string('status', 20)->default('draft');
            }

            if (! Schema::hasColumn('grades', 'published_at')) {
                $table->timestamp('published_at')->nullable();
            }
        });

        if (Schema::hasColumn('grades', 'status')) {
            DB::table('grades')->whereNull('status')->update(['status' => 'draft']);
        }
    }

    public function down(): void
    {
        // Kolom status adalah bagian inti modul; tidak dihapus saat rollback.
    }
};
