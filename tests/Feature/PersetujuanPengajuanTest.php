<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\Validasi;
use App\Livewire\KepalaDepartement\PersetujuanPengajuan;
use App\Models\Departemen;
use App\Models\Lembur;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PersetujuanPengajuanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test kepala departemen can view, approve, and reject department employees' overtime.
     */
    public function test_kepala_departemen_can_manage_overtime_requests(): void
    {
        // 1. Setup departments
        $dept1 = Departemen::create([
            'nama_departemen' => 'IT Department',
            'status' => 'active',
        ]);

        $dept2 = Departemen::create([
            'nama_departemen' => 'Finance Department',
            'status' => 'active',
        ]);

        // 2. Setup users
        $kepalaDept = new User([
            'nama_lengkap' => 'Kepala Dept IT',
            'email' => 'kepala.it@ecogreen.id',
            'password' => bcrypt('password'),
            'role' => UserRole::KepalaDepartemen,
            'status' => 'active',
        ]);
        $kepalaDept->departemen_id = $dept1->id_departemen;
        $kepalaDept->save();

        $karyawanIT = new User([
            'nama_lengkap' => 'Karyawan IT',
            'email' => 'karyawan.it@ecogreen.id',
            'password' => bcrypt('password'),
            'role' => UserRole::Karyawan,
            'status' => 'active',
        ]);
        $karyawanIT->departemen_id = $dept1->id_departemen;
        $karyawanIT->save();

        $karyawanFinance = new User([
            'nama_lengkap' => 'Karyawan Finance',
            'email' => 'karyawan.finance@ecogreen.id',
            'password' => bcrypt('password'),
            'role' => UserRole::Karyawan,
            'status' => 'active',
        ]);
        $karyawanFinance->departemen_id = $dept2->id_departemen;
        $karyawanFinance->save();

        // 3. Setup overtime records
        $lemburIT = Lembur::create([
            'mulai_lembur' => now()->addHours(2),
            'selesai_lembur' => now()->addHours(5),
            'tanggal_dibuat' => now(),
            'status' => 'active',
            'status_validasi' => Validasi::Pending->value,
            'keterangan' => 'Lembur IT kerjaan server',
            'karyawan_id' => $karyawanIT->id_user,
        ]);

        $lemburFinance = Lembur::create([
            'mulai_lembur' => now()->addHours(1),
            'selesai_lembur' => now()->addHours(3),
            'tanggal_dibuat' => now(),
            'status' => 'active',
            'status_validasi' => Validasi::Pending->value,
            'keterangan' => 'Lembur Finance tutup buku',
            'karyawan_id' => $karyawanFinance->id_user,
        ]);

        // 4. Test livewire component behavior acting as Kepala Departemen
        $this->actingAs($kepalaDept);

        Livewire::test(PersetujuanPengajuan::class)
            // Verify lemburList only contains IT department lembur and not Finance department lembur
            ->assertViewHas('lemburList', function ($list) use ($lemburIT, $lemburFinance) {
                return $list->contains('id_lembur', $lemburIT->id_lembur) && 
                       !$list->contains('id_lembur', $lemburFinance->id_lembur);
            })
            // Select the IT lembur request
            ->call('selectLembur', $lemburIT->id_lembur)
            ->assertSet('selectedLemburId', $lemburIT->id_lembur)
            // Approve the request
            ->call('approve')
            ->assertSet('selectedLemburId', null);

        // Assert that database has updated the lembur record status to valid and set validation user
        $this->assertDatabaseHas('lembur', [
            'id_lembur' => $lemburIT->id_lembur,
            'status_validasi' => Validasi::Valid->value,
            'pemvalidasi_id' => $kepalaDept->id_user,
        ]);

        // Reset state and test rejection
        $lemburIT2 = Lembur::create([
            'mulai_lembur' => now()->addHours(2),
            'selesai_lembur' => now()->addHours(5),
            'tanggal_dibuat' => now(),
            'status' => 'active',
            'status_validasi' => Validasi::Pending->value,
            'keterangan' => 'Lembur IT kerjaan server kedua',
            'karyawan_id' => $karyawanIT->id_user,
        ]);

        Livewire::test(PersetujuanPengajuan::class)
            ->call('selectLembur', $lemburIT2->id_lembur)
            ->call('reject');

        $this->assertDatabaseHas('lembur', [
            'id_lembur' => $lemburIT2->id_lembur,
            'status_validasi' => Validasi::Invalid->value,
            'pemvalidasi_id' => $kepalaDept->id_user,
        ]);
    }
}
