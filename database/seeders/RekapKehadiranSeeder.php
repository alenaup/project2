<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RekapKehadiran;


class RekapKehadiranSeeder extends Seeder
{
    public function run(): void
    {
        RekapKehadiran::factory()->count(4)->create();
    }
}
