<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jadwal;
use App\Models\Shift;
use App\Enums\Status;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ShiftPagi = Shift::query()->where('nama_shift', 'Pagi')->first();
        $ShiftSore = Shift::query()->where('nama_shift', 'Sore')->first();
        $ShiftMalam = Shift::query()->where('nama_shift', 'Malam')->first();
        Jadwal::create([
            'status' => Status::Active->value,
            'toleransi_telat' => '00:15:00',
            'tanggal_mulai' => '2026-04-01',
            'tanggal_akhir' => '2026-04-30',
            'nama_periode' => 'Periode 1', 
            'dibuat_oleh' => 1,
            'shift_id' => $ShiftPagi->id_shift,
        ]);
        Jadwal::create([
            'status' => Status::Active->value,
            'toleransi_telat' => '00:15:00',
            'tanggal_mulai' => '2026-04-21',
            'tanggal_akhir' => '2026-04-21',
            'nama_periode' => 'Periode 2',
            'dibuat_oleh' => 1,
            'shift_id' => $ShiftSore->id_shift,
        ]);
        Jadwal::create([
            'status' => Status::Active->value,
            'toleransi_telat' => '00:15:00',
            'tanggal_mulai' => '2026-04-21',
            'tanggal_akhir' => '2026-04-21',
            'nama_periode' => 'Periode 3',
            'dibuat_oleh' => 2,
            'shift_id' => $ShiftMalam->id_shift,
        ]);

        Jadwal::create([
            'status' => Status::Active->value,
            'toleransi_telat' => '00:15:00',
            'tanggal_mulai' => '2026-05-01',
            'tanggal_akhir' => '2026-05-31',
            'nama_periode' => 'Periode 4',
            'dibuat_oleh' => 1,
            'shift_id' => $ShiftPagi->id_shift,
        ]);

        Jadwal::create([
            'status' => Status::Active->value,
            'toleransi_telat' => '00:15:00',
            'tanggal_mulai' => '2026-05-01',
            'tanggal_akhir' => '2026-05-31',
            'nama_periode' => 'Periode 5',
            'dibuat_oleh' => 1,
            'shift_id' => $ShiftSore->id_shift,
        ]);

        Jadwal::create([
            'status' => Status::Active->value,
            'toleransi_telat' => '00:15:00',
            'tanggal_mulai' => '2026-05-01',
            'tanggal_akhir' => '2026-05-31',
            'nama_periode' => 'Periode 6',
            'dibuat_oleh' => 2,
            'shift_id' => $ShiftMalam->id_shift,
        ]);

        Jadwal::create([
            'status' => Status::Active->value,
            'toleransi_telat' => '00:15:00',
            'tanggal_mulai' => '2026-06-01',
            'tanggal_akhir' => '2026-06-30',
            'nama_periode' => 'Periode 7',
            'dibuat_oleh' => 1,
            'shift_id' => $ShiftPagi->id_shift,
        ]);

        Jadwal::create([
            'status' => Status::Active->value,
            'toleransi_telat' => '00:15:00',
            'tanggal_mulai' => '2026-06-01',
            'tanggal_akhir' => '2026-06-30',
            'nama_periode' => 'Periode 8',
            'dibuat_oleh' => 2,
            'shift_id' => $ShiftSore->id_shift,
        ]);
    }
}
