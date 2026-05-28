<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Outsourcing;

class OutsourcingSeeder extends Seeder
{
    /**
     * Seed data Outsourcing.
     * Outsourcing adalah perusahaan penyedia jasa tenaga kerja (vendor).
     */
    public function run(): void
    {
        Outsourcing::create([
            'nama_outsourcing' => 'PT. EcoGreen Jaya',
            'status' => 'active',
            'nomor_tlp' => '081234567890',
            'email' => 'ecogreen@vendor.com',
            'alamat' => 'Jl. Industri No. 10, Jakarta Selatan',
        ]);

        Outsourcing::create([
            'nama_outsourcing' => 'PT. Global Solutions',
            'status' => 'active',
            'nomor_tlp' => '089876543210',
            'email' => 'globalsol@vendor.com',
            'alamat' => 'Jl. Bisnis Raya No. 45, Jakarta Pusat',
        ]);

        Outsourcing::create([
            'nama_outsourcing' => 'PT. Cepat Prima',
            'status' => 'active',
            'nomor_tlp' => '087788990011',
            'email' => 'cepatprima@vendor.com',
            'alamat' => 'Jl. Sukses Makmur No. 8, Batam',
        ]);

        Outsourcing::create([
            'nama_outsourcing' => 'PT. Jaya Mandiri',
            'status' => 'active',
            'nomor_tlp' => '082122232425',
            'email' => 'jayamandiri@vendor.com',
            'alamat' => 'Jl. Sudirman No. 102, Batam',
        ]);
    }
}
