<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Template unggah nilai satu kelas + satu mata pelajaran.
 *
 * Kolom nilai yang sudah ada diisi otomatis agar admin dapat memperbarui
 * nilai lama tanpa kehilangan data.
 */
class GradesTemplateExport implements FromArray, ShouldAutoSize, WithColumnWidths, WithHeadings
{
    /**
     * @param  array<int, array<int, string|float|null>>  $baris
     */
    public function __construct(private array $baris) {}

    public function headings(): array
    {
        return ['nisn', 'nama', 'tugas', 'uts', 'uas', 'hafalan', 'catatan'];
    }

    public function array(): array
    {
        return $this->baris;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 35,
            'C' => 10,
            'D' => 10,
            'E' => 10,
            'F' => 10,
            'G' => 40,
        ];
    }
}
