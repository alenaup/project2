<?php

namespace Database\Factories;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RekapKehadiran;
class RekapKehadiranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'total_lembur' => $this->faker->numberBetween(0, 10),
            'total_jam_kerja' => $this->faker->numberBetween(0, 40),
            'total_terlambat' => $this->faker->numberBetween(0, 5),
            'tanggal_validasi' => $this->faker->date(),
            'status_validasi' => $this->faker->randomElement(['Valid', 'Tidak_Valid']),
            'status' => $this->faker->randomElement([Status::Active->value, Status::Inactive->value]),
            'pemvalidasi_id' => rand(1, 4),
            'tanggal' => $this->faker->date(),
        ];
    }
}
