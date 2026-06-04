<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Kehadiran;
use App\Models\User;
use Illuminate\Database\Seeder;

class KehadiranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        // Ambil data semua user role karyawan
        $karyawans = User::query()->where('role', UserRole::Karyawan->value)->get();

        // Ambil 5 karyawan pertama untuk dibiarkan kosong kehadirannya
        $karyawanDenganKehadiran = $karyawans->skip(5);

        // Tipe kehadiran yang tersedia (1 s.d. 6):
        // 1: hadir, 2: sakit, 3: mankir, 4: cuti, 5: izin, 6: terlambat
        $tipeKehadiranList = [1, 2, 3, 4, 5, 6];

        $dates = [
            '2026-06-21',
            '2026-07-22',
            '2026-06-23'
        ];

        foreach ($karyawanDenganKehadiran as $index => $karyawan) {
            // Ambil jadwal_id yang valid dari jadwal yang di-attach ke karyawan ini
            $jadwalIds = $karyawan->jadwal()->pluck('id_jadwal')->toArray();
            if (empty($jadwalIds)) {
                $jadwalIds = [1, 2, 3];
            }

            foreach ($dates as $dateIndex => $date) {
                // Pilih tipe kehadiran secara bergilir agar bervariasi untuk setiap karyawan
                $tipeKehadiranId = $tipeKehadiranList[($index + $dateIndex) % count($tipeKehadiranList)];
                $jadwalId = $jadwalIds[($index + $dateIndex) % count($jadwalIds)];

                // Tentukan waktu masuk/keluar dan lokasi berdasarkan tipe kehadiran
                $waktuMasuk = null;
                $waktuKeluar = null;
                $lokasiMasuk = 'Kantor Pusat';
                $lokasiKeluar = 'Kantor Pusat';

                if ($tipeKehadiranId === 1) { // hadir
                    $waktuMasuk = $date . ' 07:00:00';
                    $waktuKeluar = $date . ' 15:00:00';
                } elseif ($tipeKehadiranId === 6) { // terlambat
                    $waktuMasuk = $date . ' 07:45:00'; // Terlambat
                    $waktuKeluar = $date . ' 15:00:00';
                } elseif ($tipeKehadiranId === 2) { // sakit
                    $lokasiMasuk = 'Sakit';
                    $lokasiKeluar = null;
                } elseif ($tipeKehadiranId === 3) { // mankir
                    $lokasiMasuk = 'Mankir';
                    $lokasiKeluar = null;
                } elseif ($tipeKehadiranId === 4) { // cuti
                    $lokasiMasuk = 'Cuti';
                    $lokasiKeluar = null;
                } elseif ($tipeKehadiranId === 5) { // izin
                    $lokasiMasuk = 'Izin';
                    $lokasiKeluar = null;
                }

                // Menggunakan rekapan_kehadiran_id acak antara 1 sampai 4
                $rekapanKehadiranId = ($index % 4) + 1;

                Kehadiran::create([
                    'waktu_masuk' => $waktuMasuk,
                    'waktu_keluar' => $waktuKeluar,
                    'tanggal' => $date,
                    'lokasi_masuk' => $lokasiMasuk,
                    'lokasi_keluar' => $lokasiKeluar,
                    'rekapan_kehadiran_id' => $rekapanKehadiranId,
                    'jadwal_id' => $jadwalId,
                    'tipe_kehadiran_id' => $tipeKehadiranId,
                    'karyawan_id' => $karyawan->id_user,
                ]);
            }
        }
    }
}
