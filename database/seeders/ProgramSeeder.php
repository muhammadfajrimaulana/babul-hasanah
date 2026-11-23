<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $daftarProgram = [
            'MDTA (Madrasah Diniyah Takmiliyah)',
            'PPS WUSTHA',
            'MTS',
            'MAS',
            'PDF (Diniyah Formal) Wustha',
            'PDF ULYA',
            "MA'HAD 'ALY",
        ];

        foreach ($daftarProgram as $prog) {
            $slug = Str::slug($prog);

            // CEK DULU: apakah slug ini sudah ada di tabel program?
            // Pakai DB::table langsung agar abadi dan tidak dependen model relation
            $sudahAda = DB::table('program')->where('slug', $slug)->exists();

            if (! $sudahAda) {
                // BELUM ADA -> baru insert via DB query murni
                DB::table('program')->insert([
                    'nama_program' => $prog,
                    'slug' => $slug,
                    'status' => 'Aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            // Jika sudah ada: lewati, tidak terjadi apa-apa, tidak error
        }
    }
}
