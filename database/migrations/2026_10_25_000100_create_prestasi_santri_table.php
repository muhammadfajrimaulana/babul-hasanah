<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestasi_santri', function (Blueprint $t) {
            $t->id();
            $t->string('judul');                              // nama lomba / capaian
            $t->string('nama');                               // nama santri
            $t->string('kelas');                              // contoh: XI MA
            $t->string('kategori');                           // bidang lomba, contoh: Tahfidz
            $t->string('tingkat');                            // Kecamatan | Kabupaten | Provinsi | Nasional | Internasional
            $t->string('wilayah');                            // lokasi lomba
            $t->unsignedSmallInteger('tahun');
            $t->unsignedTinyInteger('peringkat')->default(0); // 1, 2, 3 = juara; 0 = penghargaan non-juara
            $t->string('label', 40);                          // teks di kartu, contoh: Juara 1
            $t->text('deskripsi')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasi_santri');
    }
};
