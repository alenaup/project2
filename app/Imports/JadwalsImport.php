<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class JadwalsImport implements WithMultipleSheets
{
    /**
     * Specify which sheets to import.
     *
     * @return array
     */
    public function sheets(): array
    {
        return [
            // Indeks 1 mengacu pada sheet kedua ("Atur Jadwal")
            1 => new JadwalSheetImport(),
        ];
    }
}
