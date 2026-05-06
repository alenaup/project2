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
        DB::table('users')->insert([
            'nama_lengkap' => 'Muhammad Rangga',
            'email' => 'Rangga@gmail.com',
            'nomor_tlp' => '081275796452',
            'status' => 'active',
            'role' => UserRole::KepalaDepartemen->value,
            'password' => Hash::make('userRangga'),
        ]);

        DB::table('users')->insert([
            'nama_lengkap' => 'Muhammad Thoriq Ali Aljundi',
            'email' => 'thoriq@gmail.com',
            'nomor_tlp' => '081275796452',
            'status' => 'active',
            'role' => UserRole::KepalaDepartemen->value,
            'password' => Hash::make('userThoriq'),
        ]);

        /* Membuat 10 data dummy kepala departemen */
        User::factory()->count(10)->kepalaDepartemen()->create();

        /* KEPALA DEPARTEMENT END */
        /* =================================== */

        /* HR START */

        DB::table('users')->insert([
            'nama_lengkap' => 'Jason Devito',
            'email' => 'jason@gmail.com',
            'nomor_tlp' => '081275796452',
            'status' => 'active',
            'role' => UserRole::Hr->value,
            'password' => Hash::make('userJason'),
        ]);

        /* Membuat 10 data dummy HR */
        User::factory()->count(10)->hr()->create();
        /* HR END */
        /* =================================== */

        /* SUPER ADMIN START */

        DB::table('users')->insert([
            'nama_lengkap' => 'Atma Fauzilla',
            'email' => 'atma@gmail.com',
            'nomor_tlp' => '081275796452',
            'status' => 'active',
            'role' => UserRole::SuperAdmin->value,
            'password' => Hash::make('userAtma'),
        ]);

        /* Membuat 2 data dummy super admin */
        User::factory()->count(2)->superAdmin()->create();

        /* SUPER ADMIN END */
        /* ===================================== */

        /* ADMINN VENDOR START */

        DB::table('users')->insert([
            'nama_lengkap' => 'Zahrah Faradila',
            'email' => 'zahrah@gmail.com',
            'nomor_tlp' => '081275796452',
            'status' => 'active',
            'role' => UserRole::AdminVendor->value,
            'password' => Hash::make('userZahrah'),
        ]);

        /* Membuat 5 data dummy admin vendor */
        User::factory()->count(5)->adminVendor()->create();
        /* ADMIN VENDOR END */

        /* ================================ */

        /* KARYAWAN START */
        DB::table('users')->insert([
            'nama_lengkap' => 'Atma Karyawan',
            'email' => '2atma@gmail.com',
            'nomor_tlp' => '081275796452',
            'status' => 'active',
            'role' => UserRole::Karyawan->value,
            'password' => Hash::make('userAtma'),
            'vendor_id' => 1
        ]);

        DB::table('users')->insert([
            'nama_lengkap' => 'Rangga Karyawan',
            'email' => '2rangga@gmail.com',
            'nomor_tlp' => '081275796452',
            'status' => 'active',
            'role' => UserRole::Karyawan->value,
            'password' => Hash::make('userRangga'),
            'vendor_id' => 2
        ]);

        DB::table('users')->insert([
            'nama_lengkap' => 'JasonKaryawan',
            'email' => '2jason@gmail.com',
            'nomor_tlp' => '081275796452',
            'status' => 'active',
            'role' => UserRole::Karyawan->value,
            'password' => Hash::make('userJason'),
            'vendor_id' => 3
        ]);

        DB::table('users')->insert([
            'nama_lengkap' => 'ZahraKaryawan',
            'email' => '3zahra@gmail.com',
            'nomor_tlp' => '081275796452',
            'status' => 'active',
            'role' => UserRole::Karyawan->value,
            'password' => Hash::make('userZahra'),
            'vendor_id' => 4
        ]);

        DB::table('users')->insert([
            'nama_lengkap' => 'ThoriqKaryawan',
            'email' => '2Thoriq@gmail.com',
            'nomor_tlp' => '081275796452',
            'status' => 'active',
            'role' => UserRole::Karyawan->value,
            'password' => Hash::make('userThoriq'),
            'vendor_id' => 4
        ]);

        /* membuat data dummy 10 karyawan */
        User::factory()->count(40)->create();
        /* KARYAWAN END */

    }
}
