<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('santri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nomor_induk')->unique();
            $table->string('nama_lengkap');
            $table->enum('jenjang_pendidikan', ['MTs', 'MA', 'Diniyah']);
            $table->string('kelas');
            $table->text('alamat');
            $table->string('nama_wali');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('santri');
    }
};