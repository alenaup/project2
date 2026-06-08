<?php

namespace Database\Factories;

/*  */
use App\Enums\Status;
use App\Enums\UserRole;
use App\Models\Departemen;
use App\Models\Outsourcing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

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
            'nama_lengkap' => $this->faker->name(),
            'password' => Hash::make('password'),
            'nomor_tlp' => $this->faker->numerify('############'),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'role' => UserRole::Karyawan->value, // default
            'status' => Status::Active->value,
            'user_id' => null,

            /* tidak semua role punya */
            'alamat' => $this->faker->address(),
            'nip' => $this->faker->numerify('NIP-########'),
            'tanggal_masuk' => $this->faker->date(),
            'tanggal_keluar' => null,
            
            'remember_token' => Str::random(10),
            'outsourcing_id' => $this->outsourcingId(),
            'departemen_id' => $this->departemenId(),
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

    public function superAdmin(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::SuperAdmin->value,
            'alamat' => null,
            'nip' => '0',
            'tanggal_masuk' => null,
            'tanggal_keluar' => null,
            'outsourcing_id' => null,
            'departemen_id' => null,
        ]);
    }

    public function adminVendor(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::AdminVendor->value,
            'alamat' => null,
            'nip' => '0',
            'tanggal_masuk' => null,
            'tanggal_keluar' => null,
            'outsourcing_id' => $this->outsourcingId(),
            'departemen_id' => null,
        ]);
    }

    public function hr(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::Hr->value,
            'alamat' => null,
            'nip' => '0',
            'tanggal_masuk' => null,
            'tanggal_keluar' => null,
            'outsourcing_id' => null,
            'departemen_id' => null,
        ]);
    }

    public function kepalaDepartemen(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::KepalaDepartemen->value,
            'alamat' => null,
            'nip' => '0',
            'tanggal_masuk' => null,
            'tanggal_keluar' => null,
            'outsourcing_id' => null,
            'departemen_id' => $this->departemenId(),
        ]);
    }

    /**
     * State untuk karyawan yang menunggu persetujuan HR (pending approval).
     * Status diatur ke inactive agar muncul di halaman ajuan data karyawan.
     */
    public function pendingApproval(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::Karyawan->value,
            'status' => Status::Inactive->value,
            'alamat' => $this->faker->address(),
            'nip' => $this->faker->numerify('NIP-########'),
            'tanggal_masuk' => null,
            'tanggal_keluar' => null,
            'outsourcing_id' => $this->outsourcingId(),
            'departemen_id' => $this->departemenId(),
        ]);
    }

    private function outsourcingId(): ?int
    {
        return Outsourcing::query()->inRandomOrder()->value('id_outsourcing')
            ?? Outsourcing::factory()->create()?->id_outsourcing;
    }

    private function departemenId(): ?int
    {
        return Departemen::query()->inRandomOrder()->value('id_departemen')
            ?? Departemen::query()->create([
                'nama_departemen' => $this->faker->unique()->word(),
                'status' => Status::Active->value,
            ])?->id_departemen;
    }
}
