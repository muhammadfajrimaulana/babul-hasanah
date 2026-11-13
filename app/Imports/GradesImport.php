<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

/**
 * Pembaca mentah file Excel nilai.
 *
 * Controller memakai `Excel::toArray()` agar dapat memvalidasi seluruh baris
 * terlebih dahulu dan menampilkan kesalahan per baris tanpa menyimpan data
 * setengah jadi.
 */
class GradesImport implements ToArray
{
    public function array(array $array): void
    {
        // Tidak ada pemrosesan di sini; data dibaca oleh controller.
    }
}
