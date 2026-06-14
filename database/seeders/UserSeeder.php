<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* KEPALA DEPARTEMEN START */
        DB::table('user')->insert([
            'nama_lengkap' => 'Muhammad Rangga',
            'email' => 'Rangga@gmail.com',
            'nomor_tlp' => '081275796452',
            'status' => 'active',
            'role' => UserRole::KepalaDepartemen->value,
            'password' => Hash::make('userRangga'),
            'departemen_id' => 1, // IT
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user')->insert([
            'nama_lengkap' => 'Muhammad Thoriq Ali Aljundi',
            'email' => 'thoriq@gmail.com',
            'nomor_tlp' => '081275796452',
            'status' => 'active',
            'role' => UserRole::KepalaDepartemen->value,
            'password' => Hash::make('userThoriq'),
            'departemen_id' => 2, // Manajemen
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /* Membuat 10 data dummy kepala departemen */
        User::factory()->count(5)->kepalaDepartemen()->create();

        /* KEPALA DEPARTEMENT END */
        /* =================================== */

        /* HR START */
        DB::table('user')->insert([
            'nama_lengkap' => 'Jason Devito',
            'email' => 'jason@gmail.com',
            'nomor_tlp' => '081275796452',
            'status' => 'active',
            'role' => UserRole::Hr->value,
            'password' => Hash::make('userJason'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /* Membuat 5 data dummy HR */
        User::factory()->count(5)->hr()->create();
        /* HR END */
        /* =================================== */

        /* SUPER ADMIN START */
        DB::table('user')->insert([
            'nama_lengkap' => 'Atma Fauzilla',
            'email' => 'atma@gmail.com',
            'nomor_tlp' => '081275796452',
            'status' => 'active',
            'role' => UserRole::SuperAdmin->value,
            'password' => Hash::make('userAtma'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /* Membuat 2 data dummy super admin */
        User::factory()->count(2)->superAdmin()->create();

        /* SUPER ADMIN END */
        /* ===================================== */

        /* ADMIN VENDOR START */
        DB::table('user')->insert([
            'nama_lengkap' => 'Zahrah Faradila',
            'email' => 'zahrah@gmail.com',
            'nomor_tlp' => '081275796452',
            'status' => 'active',
            'role' => UserRole::AdminVendor->value,
            'password' => Hash::make('userZahrah'),
            'outsourcing_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /* Membuat 5 data dummy admin vendor */
        User::factory()->count(5)->adminVendor()->create();
        /* ADMIN VENDOR END */

        /* ================================ */

        /* KARYAWAN START */
        DB::table('user')->insert([
            'nama_lengkap' => 'Atma Karyawan',
            'email' => '2atma@gmail.com',
            'nomor_tlp' => '081275796452',
            'status' => 'active',
            'role' => UserRole::Karyawan->value,
            'password' => Hash::make('userAtma'),
            'nip' => 'NIP-' . rand(100000, 999999),
            'outsourcing_id' => 1,
            'departemen_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user')->insert([
            'nama_lengkap' => 'Rangga Karyawan',
            'email' => '2rangga@gmail.com',
            'nomor_tlp' => '081275796452',
            'status' => 'active',
            'role' => UserRole::Karyawan->value,
            'password' => Hash::make('userRangga'),
            'nip' => 'NIP-' . rand(100000, 999999),
            'outsourcing_id' => 2,
            'departemen_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user')->insert([
            'nama_lengkap' => 'JasonKaryawan',
            'email' => '2jason@gmail.com',
            'nomor_tlp' => '081275796452',
            'status' => 'active',
            'role' => UserRole::Karyawan->value,
            'password' => Hash::make('userJason'),
            'nip' => 'NIP-' . rand(100000, 999999),
            'outsourcing_id' => 3,
            'departemen_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user')->insert([
            'nama_lengkap' => 'ZahraKaryawan',
            'email' => '3zahra@gmail.com',
            'nomor_tlp' => '081275796452',
            'status' => 'active',
            'role' => UserRole::Karyawan->value,
            'password' => Hash::make('userZahra'),
            'nip' => 'NIP-' . rand(100000, 999999),
            'outsourcing_id' => 4,
            'departemen_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user')->insert([
            'nama_lengkap' => 'ThoriqKaryawan',
            'email' => '2Thoriq@gmail.com',
            'nomor_tlp' => '081275796452',
            'status' => 'active',
            'role' => UserRole::Karyawan->value,
            'password' => Hash::make('userThoriq'),
            'nip' => 'NIP-' . rand(100000, 999999),
            'outsourcing_id' => 4,
            'departemen_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        /* membuat data dummy 20 karyawan */
        User::factory()->count(20)->create();
        /* KARYAWAN END */
    }
}
