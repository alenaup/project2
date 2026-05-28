<?php

namespace Database\Factories;

use App\Models\Outsourcing;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\Status;

/**
 * @extends Factory<Outsourcing>
 */
class OutsourcingFactory extends Factory
{
    protected $model = Outsourcing::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_outsourcing' => $this->faker->company(),
            'status' => $this->faker->randomElement([Status::Active->value, Status::Inactive->value]),
            'nomor_tlp' => $this->faker->numerify('08##########'),
            'email' => $this->faker->unique()->safeEmail(),
            'alamat' => $this->faker->address(),
        ];
    }
}
