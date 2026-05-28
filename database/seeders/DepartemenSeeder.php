<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Departemen;
use Illuminate\Database\Seeder;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Departemen::create([
            'nama_departemen' => 'IT',
            'status' => Status::Active->value,
            'lokasi_id' => 1,
        ]);

        Departemen::create([
            'nama_departemen' => 'Manajemen',
            'status' => Status::Active->value,
            'lokasi_id' => 2,
        ]);
        Departemen::create([
            'nama_departemen' => 'HRD',
            'status' => Status::Active->value,
            'lokasi_id' => 3,
        ]);
    }
}
