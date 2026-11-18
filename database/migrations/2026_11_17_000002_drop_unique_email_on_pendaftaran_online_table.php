<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Satu email orang tua/wali boleh dipakai untuk mendaftarkan beberapa anak.
     *
     * Migrasi awal membuat kolom `email` dengan indeks UNIQUE. Indeks itu
     * dihapus di sini agar pendaftaran anak kedua/ketiga dengan email yang
     * sama tidak gagal karena constraint database.
     */
    public function up(): void
    {
        if (! Schema::hasTable('pendaftaran_online') || ! Schema::hasColumn('pendaftaran_online', 'email')) {
            return;
        }

        $indeksUnik = collect(Schema::getIndexes('pendaftaran_online'))
            ->first(fn (array $indeks) => ($indeks['unique'] ?? false)
                && ($indeks['columns'] ?? []) === ['email']);

        if (! $indeksUnik) {
            return;
        }

        Schema::table('pendaftaran_online', function (Blueprint $table) use ($indeksUnik) {
            $table->dropUnique($indeksUnik['name']);
        });
    }

    /**
     * Kembalikan indeks UNIQUE hanya bila datanya masih memungkinkan.
     * Kalau sudah ada email ganda (multi-anak), indeks tidak dipasang agar
     * migrasi rollback tidak gagal.
     */
    public function down(): void
    {
        if (! Schema::hasTable('pendaftaran_online') || ! Schema::hasColumn('pendaftaran_online', 'email')) {
            return;
        }

        $adaDuplikat = DB::table('pendaftaran_online')
            ->select('email')
            ->whereNotNull('email')
            ->groupBy('email')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($adaDuplikat || Schema::hasIndex('pendaftaran_online', ['email'], 'unique')) {
            return;
        }

        Schema::table('pendaftaran_online', function (Blueprint $table) {
            $table->unique('email');
        });
    }
};
