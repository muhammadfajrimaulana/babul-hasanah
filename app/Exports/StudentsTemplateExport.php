<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Template import santri.
 *
 * Empat kolom wajib sesuai spesifikasi Modul PPDB. Baris contoh memakai
 * penulisan jenjang yang diterima (boleh kode, nama, atau slug).
 */
class StudentsTemplateExport implements FromArray, ShouldAutoSize, WithColumnWidths, WithHeadings
{
    public function headings(): array
    {
        return ['nisn', 'nama_santri', 'jenjang', 'parent_email'];
    }

    public function array(): array
    {
        return [
            ['0012345678', 'Ahmad Fauzi', 'MTs', 'wali.ahmad@gmail.com'],
            ['0012345679', 'Siti Aminah', 'MA', 'wali.siti@gmail.com'],
            ['0012345680', 'Muhammad Rizki', 'MDTA', 'wali.rizki@gmail.com'],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22,
            'B' => 35,
            'C' => 22,
            'D' => 38,
        ];
    }
}
