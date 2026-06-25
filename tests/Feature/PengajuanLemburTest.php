<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\Validasi;
use App\Livewire\KaryawanOutsourcing\PengajuanLembur;
use App\Models\Departemen;
use App\Models\Lembur;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PengajuanLemburTest extends TestCase
{
    use RefreshDatabase;

    public function test_karyawan_can_view_and_submit_overtime_request(): void
    {
        // 1. Setup department
        $dept = Departemen::create([
            'nama_departemen' => 'IT Department',
            'status' => 'active',
        ]);

        // 2. Setup user (karyawan)
        $karyawan = new User([
            'nama_lengkap' => 'Ahmad Karyawan',
            'email' => 'ahmad@ecogreen.id',
            'password' => bcrypt('password'),
            'role' => UserRole::Karyawan,
            'status' => 'active',
            'nomor_tlp' => '08123456789',
            'nip' => '12345678',
            'alamat' => 'Batam Center',
        ]);
        $karyawan->departemen_id = $dept->id_departemen;
        $karyawan->save();

        // Acting as karyawan
        $this->actingAs($karyawan);

        // 3. Test component rendering and dynamic properties
        Livewire::test(PengajuanLembur::class)
            ->assertViewHas('user', function ($u) use ($karyawan) {
                return $u->id_user === $karyawan->id_user;
            })
            ->set('tanggal_lembur', '2026-06-20')
            ->set('jam_mulai', '17:00')
            ->set('jam_selesai', '21:00')
            ->set('keterangan', 'Pekerjaan migrasi database server')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('keterangan', null);

        // 4. Assert that database has the created lembur request
        $this->assertDatabaseHas('lembur', [
            'keterangan' => 'Pekerjaan migrasi database server',
            'status_validasi' => Validasi::Pending->value,
            'karyawan_id' => $karyawan->id_user,
        ]);
    }
}
