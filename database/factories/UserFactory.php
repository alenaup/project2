<?php

namespace Database\Factories;

/*  */
use App\Enums\Status;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * nama password yang digunakan untuk membuat user baru dengan factory.
     */
    protected static ?string $password;

    /**
     * definisi dari model default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            /* bagian semua role punya */
            'nama_lengkap' => fake()->name(),
            'password' => Hash::make('password'),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'nomor_tlp' => fake()->phoneNumber(),
            'status' => $this->faker->randomElement([Status::Active->value, Status::Inactive->value]),
            'created_by' => 0,

            /* tidak semua role punya */
            'alamat' => fake()->address(),
            'NIP' => fake()->numerify('NIP-########'),
            'nama_departemen' => fake()->randomElement(['IT', 'HR', 'Finance', 'Marketing']),
            'tanggal_masuk' => fake()->date(),
            'tanggal_keluar' => null,
            'role' => UserRole::Karyawan->value, // default
            'remember_token' => Str::random(10),
            'vendor_id' => rand(1, 4),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function superAdmin()
    {
        return $this->state(fn () => [
            'role' => UserRole::SuperAdmin->value,
            'alamat' => null,
            'NIP' => (000),
            'nama_departemen' => null,
            'tanggal_masuk' => null,
            'tanggal_keluar' => null,
            'vendor_id' => null,
        ]);
    }

    public function adminVendor()
    {
        return $this->state(fn () => [
            'role' => UserRole::AdminVendor->value,
            'alamat' => null,
            'NIP' => (000),
            'nama_departemen' => null,
            'tanggal_masuk' => null,
            'tanggal_keluar' => null,
            'vendor_id' => null,
        ]);
    }

    public function hr()
    {
        return $this->state(fn () => [
            'role' => UserRole::Hr->value,
            'alamat' => null,
            'NIP' => (000),
            'nama_departemen' => null,
            'tanggal_masuk' => null,
            'tanggal_keluar' => null,
            'vendor_id' => null,
        ]);
    }

    public function kepalaDepartemen()
    {
        return $this->state(fn () => [
            'role' => UserRole::KepalaDepartemen->value,
            'alamat' => null,
            'NIP' => (000),
            'nama_departemen' => null,
            'tanggal_masuk' => null,
            'tanggal_keluar' => null,
            'vendor_id' => null,
        ]);
    }
}
