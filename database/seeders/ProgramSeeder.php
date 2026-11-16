<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Daftar program/jenjang pendidikan resmi pesantren (data master).
 * Idempoten: aman dijalankan berulang karena memakai kunci slug.
 */
class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            'MDTA (Madrasah Diniyah Takmiliyah)',
            'PPS WUSTHA',
            'MTS',
            'MAS',
            'PDF (Diniyah Formal) Wustha',
            'PDF ULYA',
            "MA'HAD 'ALY",
        ];

        foreach ($programs as $prog) {
            Program::updateOrCreate(
                ['slug' => Str::slug($prog)],
                [
                    'nama_program' => $prog,
                    'status' => 'Aktif',
                ]
            );
        }
    }
}
