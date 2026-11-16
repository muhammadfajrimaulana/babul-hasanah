<?php

use App\Support\Jenjang;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Salin mata pelajaran lama (`mata_pelajaran`) ke skema baru (`subjects`).
 *
 * Manajemen Nilai Akademik membaca `subjects`, sedangkan halaman Mata
 * Pelajaran yang sudah ada mengelola `mata_pelajaran`. Migrasi ini membuat
 * daftar mapel lama langsung tersedia di halaman nilai tanpa duplikasi.
 *
 * Idempoten: baris dengan jenjang + kode yang sama diperbarui, bukan
 * diduplikasi. Mapel nonaktif tidak disalin.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('mata_pelajaran') || ! Schema::hasTable('subjects')) {
            return;
        }

        $query = DB::table('mata_pelajaran');

        if (Schema::hasColumn('mata_pelajaran', 'aktif')) {
            $query->where('aktif', true);
        }

        $query->orderBy('id')->chunk(200, function ($baris) {
            foreach ($baris as $row) {
                $kode = trim((string) ($row->kode ?? ''));
                $nama = trim((string) ($row->nama ?? ''));

                if ($kode === '' || $nama === '') {
                    continue;
                }

                $jenjang = Jenjang::normalisasi($row->jenjang ?? null) ?: (string) $row->jenjang;
                $kunci = ['jenjang' => $jenjang, 'kode' => $kode];
                $nilai = [
                    'nama' => $nama,
                    'kelompok' => $row->kelompok ?? null,
                    'kkm' => (int) ($row->kkm ?? 70),
                    'updated_at' => now(),
                    'deleted_at' => null,
                ];

                $ada = DB::table('subjects')->where($kunci)->exists();

                if ($ada) {
                    DB::table('subjects')->where($kunci)->update($nilai);
                } else {
                    DB::table('subjects')->insert($kunci + $nilai + [
                        'created_at' => now(),
                    ]);
                }
            }
        });
    }

    public function down(): void
    {
        // Data hasil salinan tidak dihapus agar nilai yang sudah memakainya aman.
    }
};
