<?php

namespace Database\Seeders;

use App\Models\Lembur;
use Illuminate\Database\Seeder;

class LemburSeeder extends Seeder
{
    public function run(): void
    {

        Lembur::create([
            'mulai_lembur' => '2026-04-21 17:00:00',
            'selesai_lembur' => '2026-04-21 20:00:00',
            'tanggal_dibuat' => '2026-04-21 10:00:00',
            'status' => 'Lembur',
            'karyawan_id' => 34,
            'pemvalidasi' => 'Manager HR',
            'keterangan' => 'Penyelesaian proyek akhir bulan',
        ]);
        Lembur::create([
            'mulai_lembur' => '2026-04-22 18:00:00',
            'selesai_lembur' => '2026-04-22 21:00:00',
            'tanggal_dibuat' => '2026-04-22 11:00:00',
            'status' => 'Lembur',
            'karyawan_id' => 34,
            'pemvalidasi' => 'Manager HR',
            'keterangan' => 'Penyelesaian laporan keuangan',
        ]);
        Lembur::create([
            'mulai_lembur' => '2026-04-23 19:00:00',
            'selesai_lembur' => '2026-04-23 22:00:00',
            'tanggal_dibuat' => '2026-04-23 12:00:00',
            'status' => 'Lembur',
            'karyawan_id' => 35,
            'pemvalidasi' => 'Manager HR',
            'keterangan' => 'Penyelesaian tugas mendesak',
        ]);
    }
}
