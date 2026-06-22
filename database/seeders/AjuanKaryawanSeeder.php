<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder untuk membuat data karyawan outsourcing yang menunggu
 * persetujuan HR (status = inactive).
 *
 * Data ini akan tampil di halaman "Ajuan Data Karyawan" pada panel HR.
 */
class AjuanKaryawanSeeder extends Seeder
{
    /**
     * Jalankan seeder.
     */
    public function run(): void
    {
        /* ──────────────────────────────────────────────────────
         |  Data manual — karyawan pending dengan data realistis
         * ────────────────────────────────────────────────────── */
        $karyawanPending = [
            [
                'nama_lengkap'   => 'Fairuz Kamala',
                'email'          => 'fairuz.k@chemistry.id',
                'nomor_tlp'      => '081233445566',
                'nip'            => '240029222',
                'alamat'         => 'Jl. Sudirman No. 10, Jakarta Selatan',
                'outsourcing_id' => 1, // PT. EcoGreen Jaya
                'departemen_id'  => 1,
            ],
            [
                'nama_lengkap'   => 'Ahmad Syauqi',
                'email'          => 'ahmad.s@syauqi.com',
                'nomor_tlp'      => '085711223344',
                'nip'            => '240029223',
                'alamat'         => 'Perum Harapan Indah, Bekasi',
                'outsourcing_id' => 2, // PT. Global Solutions
                'departemen_id'  => 1,
            ],
            [
                'nama_lengkap'   => 'Nabila Putri',
                'email'          => 'nabila.p@gmail.com',
                'nomor_tlp'      => '081988776655',
                'nip'            => '240029224',
                'alamat'         => 'Apartemen Menteng Atas Lt. 12, Jakarta Pusat',
                'outsourcing_id' => 3, // PT. Cepat Prima
                'departemen_id'  => 2,
            ],
            [
                'nama_lengkap'   => 'Dimas Anggara',
                'email'          => 'dimas.an@outlook.com',
                'nomor_tlp'      => '082155667788',
                'nip'            => '240029225',
                'alamat'         => 'Jl. Melati No. 45, Batam',
                'outsourcing_id' => 4, // PT. Jaya Mandiri
                'departemen_id'  => 2,
            ],
            [
                'nama_lengkap'   => 'Rina Melati',
                'email'          => 'rina.m@perusahaan.com',
                'nomor_tlp'      => '089944332211',
                'nip'            => '240029226',
                'alamat'         => 'Cluster Pinang Mas Blok C7, Tangerang',
                'outsourcing_id' => 1, // PT. EcoGreen Jaya
                'departemen_id'  => 3,
            ],
            [
                'nama_lengkap'   => 'Bima Sakti',
                'email'          => 'bima.s@space.id',
                'nomor_tlp'      => '081233440099',
                'nip'            => '240029227',
                'alamat'         => 'Jl. Antariksa No. 1, Batam',
                'outsourcing_id' => 2, // PT. Global Solutions
                'departemen_id'  => 3,
            ],
            [
                'nama_lengkap'   => 'Thoiriq Muchlisqism',
                'email'          => 'thoiriq.m@racing.com',
                'nomor_tlp'      => '087766554433',
                'nip'            => '240029228',
                'alamat'         => 'Kavling Sirkuit No. 7, Sentul',
                'outsourcing_id' => 3, // PT. Cepat Prima
                'departemen_id'  => 1,
            ],
        ];

        foreach ($karyawanPending as $data) {
            DB::table('user')->insert(array_merge($data, [
                'password'   => Hash::make('password'),
                'role'       => UserRole::Karyawan->value,
                'status'     => Status::Inactive->value,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        /* ──────────────────────────────────────────────────────
         |  Data dummy — tambahan karyawan pending via factory
         * ────────────────────────────────────────────────────── */
        User::factory()
            ->count(8)
            ->pendingApproval()
            ->create();
    }
}
