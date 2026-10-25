<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('albums', function (Blueprint $t) {
            $t->id();
            $t->string('judul');
            $t->string('kategori');
            $t->date('tanggal')->nullable();
            $t->text('deskripsi')->nullable();
            $t->string('sampul')->nullable();            // path di disk public
            $t->timestamps();
        });

        Schema::create('album_media', function (Blueprint $t) {
            $t->id();
            $t->foreignId('album_id')->constrained()->cascadeOnDelete();
            $t->enum('tipe', ['foto', 'video', 'link']);  // foto | video upload | link medsos
            $t->string('path')->nullable();               // file foto / video di disk public
            $t->string('url')->nullable();                // link YouTube / TikTok / Instagram
            $t->string('poster')->nullable();             // sampul video (path di disk public)
            $t->string('keterangan')->nullable();
            $t->unsignedInteger('urutan')->default(0);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('album_media');
        Schema::dropIfExists('albums');
    }
};
