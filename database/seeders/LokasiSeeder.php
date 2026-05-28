<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Lokasi;
use Illuminate\Database\Seeder;

class LokasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Lokasi::create([
            'nama_lokasi' => 'Nongsa Digital Park',
            'latitude' => 1.185641456138759,
            'longitude' => 104.10166559444662,
            'radius' => 100,
            'status' => Status::Active->value,
        ]);

        Lokasi::create([
            'nama_lokasi' => 'Politeknik Negeri Batam',
            'latitude' => 1.118322,
            'longitude' => 104.048767,
            'radius' => 100,
            'status' => Status::Active->value,
        ]);

        Lokasi::create([
            'nama_lokasi' => 'Eco Green Batam',
            'latitude' => 1.083481,
            'longitude' => 104.030512,
            'radius' => 100,
            'status' => Status::Active->value,
        ]);
    }
}
