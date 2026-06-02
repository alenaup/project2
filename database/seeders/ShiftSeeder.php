<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Seed data Shift.
     * Data master untuk 3 tipe shift kerja: Pagi, Sore, Malam.
     */
    public function run(): void
    {
        Shift::create([
            'jam_masuk' => '07:00:00',
            'jam_keluar' => '15:00:00',
            'nama_shift' => 'Pagi',
        ]);

        Shift::create([
            'jam_masuk' => '15:00:00',
            'jam_keluar' => '23:00:00',
            'nama_shift' => 'Sore',
        ]);

        Shift::create([
            'jam_masuk' => '23:00:00',
            'jam_keluar' => '07:00:00',
            'nama_shift' => 'Malam',
        ]);
    }
}
