<?php

namespace Database\Seeders;

use App\Models\Lembur;
use App\Models\User;
use App\Enums\UserRole;
use App\Enums\Status;
use App\Enums\Validasi;
use Illuminate\Database\Seeder;

class LemburSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua kepala departemen yang memiliki departemen_id
        $kepalaDepartemens = User::where('role', UserRole::KepalaDepartemen)
            ->whereNotNull('departemen_id')
            ->get();

        foreach ($kepalaDepartemens as $kd) {
            // Dapatkan karyawan di departemen yang sama
            $karyawans = User::where('role', UserRole::Karyawan)
                ->where('departemen_id', $kd->departemen_id)
                ->get();


            // Jika tidak ada karyawan di departemen ini, buat 1 dummy karyawan menggunakan factory
            if ($karyawans->isEmpty()) {
                $karyawan = User::factory()->create([
                    'role' => UserRole::Karyawan->value,
                    'departemen_id' => $kd->departemen_id,
                ]);
                $karyawans = collect([$karyawan]);
            }

            // Buat beberapa lembur dummy untuk karyawan di departemen ini
            foreach ($karyawans as $index => $karyawan) {
                // Tentukan status validasi secara bergantian (Pending, Valid, Invalid)
                // index 0 -> Pending, index 1 -> Valid, index 2 -> Invalid, dst.
                $statusValidasi = ($index % 3 === 0) 
                    ? Validasi::Pending->value 
                    : (($index % 3 === 1) ? Validasi::Valid->value : Validasi::Invalid->value);
                
                // Variasi tanggal untuk data dummy agar lebih realistis
                $dayOffset = $index * 2;
                $tanggalDibuat = now()->subDays($dayOffset + 1)->setTime(10, 0, 0);
                $mulaiLembur = now()->subDays($dayOffset)->setTime(17, 0, 0);
                $selesaiLembur = now()->subDays($dayOffset)->setTime(20, 0, 0);

                Lembur::create([
                    'mulai_lembur' => $mulaiLembur,
                    'selesai_lembur' => $selesaiLembur,
                    'tanggal_dibuat' => $tanggalDibuat,
                    'status' => Status::Active->value,
                    'status_validasi' => $statusValidasi,
                    'karyawan_id' => $karyawan->id_user,
                    'pemvalidasi_id' => $statusValidasi !== Validasi::Pending->value ? $kd->id_user : null,
                    'keterangan' => 'Pekerjaan lembur untuk penyelesaian tugas di departemen ' . ($kd->departemen?->nama_departemen ?? 'IT'),
                ]);
            }
        }
    }
}
