<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Database\Seeder;

class KaryawanJadwalSeeder extends Seeder
{

    public function run()
    {
        $users = User::query()->where('role', UserRole::Karyawan->value)->get();
        $jadwals = Jadwal::all();

        foreach ($users as $user) {
            // random ambil 1-3 jadwal
            $randomJadwal = $jadwals->random(rand(1, 3))->pluck('id_jadwal')->toArray();

            // attach ke pivot
            $user->jadwal()->attach($randomJadwal);
        }
    }
}
