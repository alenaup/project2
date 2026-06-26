<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Enums\UserRole;
use App\Enums\Validasi;
use App\Models\Departemen;
use App\Models\Lembur;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DummyLemburSeeder extends Seeder
{
    public function run(): void
    {
        // Cari atau buat departemen
        $departemen = Departemen::first();
        if (!$departemen) {
            $departemen = Departemen::create([
                'nama_departemen' => 'IT Department',
                'status' => 'active'
            ]);
        }

        // Pastikan ada kepala departemen
        $kepala = User::where('role', UserRole::KepalaDepartemen->value)
            ->where('departemen_id', $departemen->id_departemen)
            ->first();
            
        if (!$kepala) {
            $kepala = User::create([
                'nama_lengkap' => 'Kepala Dept Dummy',
                'email' => 'kepala_dummy@example.com',
                'password' => Hash::make('password'),
                'role' => UserRole::KepalaDepartemen->value,
                'departemen_id' => $departemen->id_departemen,
                'status' => Status::Active->value,
            ]);
        }

        // Buat beberapa karyawan dan data lembur
        for ($i = 1; $i <= 3; $i++) {
            $karyawan = User::firstOrCreate(
                ['email' => "karyawan_dummy{$i}@example.com"],
                [
                    'nama_lengkap' => "Karyawan Dummy $i",
                    'password' => Hash::make('password'),
                    'role' => UserRole::Karyawan->value,
                    'departemen_id' => $departemen->id_departemen,
                    'status' => Status::Active->value,
                ]
            );

            // Buat 2 pengajuan lembur pending untuk masing-masing karyawan
            for ($j = 1; $j <= 2; $j++) {
                Lembur::create([
                    'mulai_lembur' => Carbon::now()->subDays($j)->setHour(17)->setMinute(0),
                    'selesai_lembur' => Carbon::now()->subDays($j)->setHour(21)->setMinute(0),
                    'tanggal_dibuat' => Carbon::now()->subDays($j),
                    'status' => Status::Active->value,
                    'status_validasi' => Validasi::Pending->value,
                    'keterangan' => "Pekerjaan server lembur part $j (Dummy)",
                    'karyawan_id' => $karyawan->id_user,
                ]);
            }
        }
    }
}
