<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * URUTAN PENTING! Parent table harus di-seed sebelum child table
     * karena ada relasi foreign key antar tabel.
     *
     * Urutan dependensi:
     * 1. Lokasi (Independen)
     * 2. Outsourcing/Vendor (Independen)
     * 3. Departemen (Butuh: Lokasi)
     * 4. User (Butuh: Outsourcing, Departemen)
     * 5. RekapKehadiran (Butuh: User/Admin, User/HR)
     * 6. Shift (Independen)
     * 7. Jadwal (Butuh: Shift, User/KepalaDepartemen)
     * 8. KaryawanJadwal Pivot (Butuh: User, Jadwal)
     * 9. TipeKehadiran (Independen)
     * 10. Kehadiran (Butuh: Jadwal, TipeKehadiran, RekapKehadiran)
     * 11. Lembur (Butuh: User/Karyawan, User/KepalaDepartemen)
     */
    public function run(): void
    {
        // --- Tabel Independen & Master ---
        $this->call([
            LokasiSeeder::class,
            OutsourcingSeeder::class,
            DepartemenSeeder::class,
            UserSeeder::class,
            RekapKehadiranSeeder::class,
            ShiftSeeder::class,
            JadwalSeeder::class,
            KaryawanJadwalSeeder::class,
            TipeKehadiranSeeder::class,
        ]);

        // --- Tabel Transaksional/Detail ---
        $this->call([
            KehadiranSeeder::class,
            LemburSeeder::class,
        ]);
    }
}
