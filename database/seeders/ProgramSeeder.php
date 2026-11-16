<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Program;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
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
            Program::create([
                'nama_program' => $prog,
                'slug' => Str::slug($prog),
                'status' => 'Aktif',
            ]);
        }
    }
}
