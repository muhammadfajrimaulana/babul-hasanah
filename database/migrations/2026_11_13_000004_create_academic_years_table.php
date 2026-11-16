<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Modul Kenaikan Kelas — tabel `academic_years`.
 *
 * Satu baris = satu tahun ajaran (contoh `2026/2027`). Relasi ini dipakai
 * `grades.academic_year_id` dan `class_student.academic_year_id` agar nilai
 * lama tetap menempel pada tahun ajaran saat nilai tersebut dibuat, meskipun
 * santri sudah dipindahkan ke kelas baru.
 *
 * Idempoten: aman dijalankan ulang.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('academic_years')) {
            Schema::create('academic_years', function (Blueprint $table) {
                $table->id();
                $table->string('nama', 20)->unique(); // 2026/2027
                $table->date('mulai')->nullable();
                $table->date('selesai')->nullable();
                $table->boolean('aktif')->default(false);

                $table->timestamps();
            });
        }

        // Pastikan tahun ajaran berjalan tersedia agar halaman kenaikan kelas
        // langsung bisa dipakai setelah migrasi.
        $mulai = now()->month >= 7 ? now()->year : now()->year - 1;
        $nama = $mulai.'/'.($mulai + 1);

        $ada = DB::table('academic_years')->where('nama', $nama)->exists();

        if (! $ada) {
            DB::table('academic_years')->insert([
                'nama' => $nama,
                'mulai' => $mulai.'-07-01',
                'selesai' => ($mulai + 1).'-06-30',
                'aktif' => DB::table('academic_years')->where('aktif', true)->doesntExist(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
