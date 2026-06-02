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
        // Ambil data karyawan dan kepala departemen yang valid secara dinamis
        $karyawans = User::query()->where('role', UserRole::Karyawan->value)->limit(3)->get();
        $kepalaDepartemen = User::query()->where('role', UserRole::KepalaDepartemen->value)->first();

        $karyawan1 = $karyawans->get(0) ?? User::factory()->create();
        $karyawan2 = $karyawans->get(1) ?? User::factory()->create();
        $validator = $kepalaDepartemen ?? User::factory()->kepalaDepartemen()->create();

        Lembur::create([
            'mulai_lembur' => '2026-04-21 17:00:00',
            'selesai_lembur' => '2026-04-21 20:00:00',
            'tanggal_divalidasi' => '2026-04-21 10:00:00',
            'status' => Status::Active->value,
            'status_validasi' => Validasi::Valid->value,
            'karyawan_id' => $karyawan1->id_user,
            'pemvalidasi_id' => $validator->id_user,
            'keterangan' => 'Penyelesaian proyek akhir bulan',
        ]);

        Lembur::create([
            'mulai_lembur' => '2026-04-22 18:00:00',
            'selesai_lembur' => '2026-04-22 21:00:00',
            'tanggal_divalidasi' => '2026-04-22 11:00:00',
            'status' => Status::Active->value,
            'status_validasi' => Validasi::Valid->value,
            'karyawan_id' => $karyawan1->id_user,
            'pemvalidasi_id' => $validator->id_user,
            'keterangan' => 'Penyelesaian laporan keuangan',
        ]);

        Lembur::create([
            'mulai_lembur' => '2026-04-23 19:00:00',
            'selesai_lembur' => '2026-04-23 22:00:00',
            'tanggal_divalidasi' => '2026-04-23 12:00:00',
            'status' => Status::Active->value,
            'status_validasi' => Validasi::Pending->value,
            'karyawan_id' => $karyawan2->id_user,
            'pemvalidasi_id' => $validator->id_user,
            'keterangan' => 'Penyelesaian tugas mendesak',
        ]);
    }
}
