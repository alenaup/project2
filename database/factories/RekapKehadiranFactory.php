<?php

namespace Database\Factories;

use App\Enums\Status;
use App\Enums\UserRole;
use App\Enums\Validasi;
use App\Models\RekapKehadiran;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RekapKehadiranFactory extends Factory
{
    protected $model = RekapKehadiran::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'total_mankir'    => $this->faker->numberBetween(0, 2),
            'total_cuti'      => $this->faker->numberBetween(0, 3),
            'total_lembur'    => $this->faker->numberBetween(0, 10),
            'total_izin'      => $this->faker->numberBetween(0, 2),
            'total_sakit'     => $this->faker->numberBetween(0, 3),
            'total_hadir'     => $this->faker->numberBetween(15, 22),
            'total_terlambat' => $this->faker->numberBetween(0, 5),
            'total_jam_kerja' => $this->faker->numberBetween(120, 160),
            'tanggal_validasi' => $this->faker->date(),
            'status_validasi' => $this->faker->randomElement([
                Validasi::Valid->value,
                Validasi::Invalid->value,
                Validasi::Pending->value,
            ]),
            'status'    => $this->faker->randomElement([Status::Active->value, Status::Inactive->value]),
            'pengaju'   => $this->getUserIdByRole(UserRole::AdminVendor),
            'pevalidasi' => $this->getUserIdByRole(UserRole::Hr),
        ];
    }

    /**
     * Mengambil id_user secara aman berdasarkan role.
     * Jika belum ada user dengan role tersebut, buat satu terlebih dahulu.
     */
    private function getUserIdByRole(UserRole $role): int
    {
        $user = User::where('role', $role->value)->inRandomOrder()->first();

        if ($user) {
            return $user->id_user;
        }

        // Buat user baru sesuai role jika belum ada
        return match ($role) {
            UserRole::AdminVendor => User::factory()->adminVendor()->create()->id_user,
            UserRole::Hr          => User::factory()->hr()->create()->id_user,
            default               => User::factory()->create()->id_user,
        };
    }
}
