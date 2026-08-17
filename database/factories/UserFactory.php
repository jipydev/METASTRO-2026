<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nim'                        => fake()->unique()->numerify('#######'), // 7 digit
            'nama'                       => fake()->name(),
            'email'                      => fake()->unique()->safeEmail(),
            'email_verified_at'          => now(),
            'password'                   => static::$password ??= Hash::make('metastro2026'),
            'nomor_hp'                   => fake()->unique()->numerify('08##########'),
            'tanggal_lahir'              => fake()->date('Y-m-d', '-18 years'),
            'jenis_kelamin'              => fake()->randomElement(['laki-laki', 'perempuan']),
            'status'                     => true,
            'is_initial_setup_completed' => true,
            'remember_token'             => Str::random(10),
        ];
    }

    /**
     * State untuk Admin
     */
    public function admin(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('admin');
        });
    }

    /**
     * State untuk Panitia
     */
    public function panitia(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('panitia');
        });
    }

    /**
     * State untuk Peserta
     */
    public function peserta(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('peserta');
        });
    }

    /**
     * Unverified Email State
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
