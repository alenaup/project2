<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;


class VendorSeeder extends Seeder
{
    /**
     * Seed data Vendor.
     * Vendor adalah perusahaan outsourcing yang bekerja sama.
     */
    public function run(): void
    {
        Vendor::create([
            'nama_vendor' => 'PT. EcoGreen Jaya',
            'status' => 'active',
            'nomor_tlp' => '081234567890',
            'email' => 'ecogreen@vendor.com',
            'alamat' => 'Jl. Industri No. 10, Jakarta Selatan',
        ]);

        Vendor::factory()->count(3)->create();
    }
}
