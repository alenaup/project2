<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class KaryawanSubmissionsImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Form Data Karyawan' => new KaryawanDataSheetImport(),
        ];
    }
}
