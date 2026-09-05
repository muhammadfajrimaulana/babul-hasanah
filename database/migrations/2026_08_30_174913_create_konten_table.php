<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('konten', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // Contoh: profil_hero_title, sejarah_teks_1, visi
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, textarea, image
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konten');
    }
};