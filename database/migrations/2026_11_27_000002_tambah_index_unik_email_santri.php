<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Email santri adalah username akun alumni, jadi harus unik.
     *
     * Migrasi ini aman terhadap data lama: bila ada duplikat, indeks sengaja
     * tidak dipasang agar `php artisan migrate` tidak gagal. Duplikat harus
     * dibersihkan oleh manusia sebelum migrasi dijalankan ulang.
     */
    private const TARGET = [
        ['tabel' => 'pendaftaran_online', 'kolom' => 'email_santri'],
        ['tabel' => 'santri', 'kolom' => 'email_santri'],
    ];

    public function up(): void
    {
        foreach (self::TARGET as $target) {
            $tabel = $target['tabel'];
            $kolom = $target['kolom'];

            if (! Schema::hasTable($tabel) || ! Schema::hasColumn($tabel, $kolom)) {
                continue;
            }

            if (Schema::hasIndex($tabel, [$kolom], 'unique')) {
                continue;
            }

            $adaDuplikat = DB::table($tabel)
                ->select($kolom)
                ->whereNotNull($kolom)
                ->where($kolom, '<>', '')
                ->groupBy($kolom)
                ->havingRaw('COUNT(*) > 1')
                ->exists();

            if ($adaDuplikat) {
                continue;
            }

            Schema::table($tabel, function (Blueprint $table) use ($kolom) {
                $table->unique($kolom);
            });
        }
    }

    public function down(): void
    {
        foreach (self::TARGET as $target) {
            $tabel = $target['tabel'];
            $kolom = $target['kolom'];

            if (! Schema::hasTable($tabel) || ! Schema::hasColumn($tabel, $kolom)) {
                continue;
            }

            if (! Schema::hasIndex($tabel, [$kolom], 'unique')) {
                continue;
            }

            Schema::table($tabel, function (Blueprint $table) use ($kolom) {
                $table->dropUnique([$kolom]);
            });
        }
    }
};
