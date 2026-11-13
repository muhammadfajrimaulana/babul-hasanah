<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Modul Kenaikan Kelas — relasi tahun ajaran.
 *
 * - `grades.academic_year_id`: nilai lama terikat pada tahun ajaran asalnya
 *   sehingga tidak hilang/tercampur ketika santri naik kelas.
 * - `class_student.academic_year_id`: penempatan kelas aktif dicatat pada
 *   tahun ajaran berjalan.
 *
 * Nilai `tahun_ajaran` yang sudah ada di `grades` disalin ke `academic_years`
 * lalu dihubungkan lewat id. Idempoten dan aman untuk basis data lama.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('academic_years')) {
            return;
        }

        if (Schema::hasTable('grades') && ! Schema::hasColumn('grades', 'academic_year_id')) {
            Schema::table('grades', function (Blueprint $table) {
                $table->foreignId('academic_year_id')
                    ->nullable()
                    ->constrained('academic_years')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('class_student') && ! Schema::hasColumn('class_student', 'academic_year_id')) {
            Schema::table('class_student', function (Blueprint $table) {
                $table->foreignId('academic_year_id')
                    ->nullable()
                    ->constrained('academic_years')
                    ->nullOnDelete();
            });
        }

        $this->isiTahunAjaranLama();
    }

    public function down(): void
    {
        // Lepas relasi lebih dulu agar tabel academic_years dapat dihapus oleh
        // migrasi sebelumnya. Data nilai tetap utuh pada kolom `tahun_ajaran`.
        foreach (['grades', 'class_student'] as $tabel) {
            if (Schema::hasTable($tabel) && Schema::hasColumn($tabel, 'academic_year_id')) {
                Schema::table($tabel, function (Blueprint $table) {
                    $table->dropConstrainedForeignId('academic_year_id');
                });
            }
        }
    }

    /** Buat baris academic_years dari nilai `grades.tahun_ajaran` lalu tautkan. */
    private function isiTahunAjaranLama(): void
    {
        if (! Schema::hasTable('grades')
            || ! Schema::hasColumn('grades', 'academic_year_id')
            || ! Schema::hasColumn('grades', 'tahun_ajaran')) {
            return;
        }

        $daftar = DB::table('grades')
            ->whereNotNull('tahun_ajaran')
            ->where('tahun_ajaran', '<>', '')
            ->distinct()
            ->orderBy('tahun_ajaran')
            ->pluck('tahun_ajaran');

        foreach ($daftar as $nama) {
            $nama = trim((string) $nama);

            if ($nama === '') {
                continue;
            }

            $id = DB::table('academic_years')->where('nama', $nama)->value('id');

            if (! $id) {
                $mulai = (int) strtok($nama, '/');
                $mulai = $mulai > 0 ? $mulai : (int) now()->year;

                $id = DB::table('academic_years')->insertGetId([
                    'nama' => $nama,
                    'mulai' => $mulai.'-07-01',
                    'selesai' => ($mulai + 1).'-06-30',
                    'aktif' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('grades')
                ->where('tahun_ajaran', $nama)
                ->whereNull('academic_year_id')
                ->update(['academic_year_id' => $id]);
        }

        // Penempatan kelas yang belum punya tahun ajaran ditautkan ke tahun
        // ajaran aktif agar relasi tetap konsisten.
        if (Schema::hasTable('class_student') && Schema::hasColumn('class_student', 'academic_year_id')) {
            $aktif = DB::table('academic_years')->where('aktif', true)->value('id')
                ?: DB::table('academic_years')->orderByDesc('nama')->value('id');

            if ($aktif) {
                DB::table('class_student')
                    ->whereNull('academic_year_id')
                    ->update(['academic_year_id' => $aktif]);
            }
        }
    }
};
