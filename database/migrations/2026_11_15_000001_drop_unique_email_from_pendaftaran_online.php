<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Satu akun wali boleh mendaftarkan lebih dari satu anak.
 *
 * Migrasi lama membuat kolom `pendaftaran_online.email` unik sehingga
 * pendaftaran anak kedua dengan email wali yang sama selalu gagal. Email wali
 * kini disimpan pada `parent_email` (tidak unik) dan `email` hanya menjadi
 * kolom kompatibilitas. Migrasi ini menghapus indeks unik lama bila masih ada.
 *
 * Idempoten: aman dijalankan berulang dan pada database yang indeksnya sudah
 * dihapus manual.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pendaftaran_online')) {
            return;
        }

        foreach (Schema::getIndexes('pendaftaran_online') as $index) {
            $kolom = array_map('strtolower', $index['columns'] ?? []);

            // Hanya indeks unik yang benar-benar hanya berisi kolom `email`.
            if (empty($index['unique']) || $kolom !== ['email']) {
                continue;
            }

            Schema::table('pendaftaran_online', function (Blueprint $table) use ($index) {
                $table->dropUnique($index['name']);
            });

            break;
        }
    }

    public function down(): void
    {
        // Sengaja tidak mengembalikan indeks unik: email wali memang boleh
        // dipakai untuk beberapa pendaftaran (kakak/adik).
    }
};
