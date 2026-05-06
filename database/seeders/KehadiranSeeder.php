<?php

namespace Database\Seeders;

use App\Models\Kehadiran;
use Illuminate\Database\Seeder;
class KehadiranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data referensi
        Kehadiran::create([
            'waktu_masuk' => '2026-04-21 06:50:00',
            'waktu_keluar' => '2026-04-21 15:00:00',
            'waktu_telat' => null,
            'tanggal' => '2026-04-21',
            'lokasi_masuk' => 'Kantor Pusat',
            'lokasi_keluar' => 'Kantor Pusat',
            'status_rekapan' => 'valid',
            'bukti' => 'default.png',
            'keterangan' => 'hadir',
            'rekapan_kehadiran_id' => 1,
            'jadwal_id' => 1,
            'tipe_kehadiran_id' => 1,
        ]);
        Kehadiran::create([
            'waktu_masuk' => '2026-04-21 07:10:00',
            'waktu_keluar' => '2026-04-21 16:00:00',
            'waktu_telat' => '00:10:00',
            'tanggal' => '2026-04-21',
            'lokasi_masuk' => 'Kantor Pusat',
            'lokasi_keluar' => 'Kantor Pusat',
            'bukti' => 'default.png',
            'keterangan' => 'hadir dengan keterlambatan',
            'rekapan_kehadiran_id' => 1,
            'jadwal_id' => 1,
            'tipe_kehadiran_id' => 2,
        ]);
    }
}
