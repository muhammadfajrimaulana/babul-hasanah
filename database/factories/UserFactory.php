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
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'wali_santri',
            'jenjang' => null,
            'is_active' => true,
            'must_change_password' => false,
        ];
    }

    /** Akun Superadmin. */
    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'super_admin', 'jenjang' => null]);
    }

    /** Akun Pengurus. */
    public function pengurus(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'pengurus', 'jenjang' => null]);
    }

    /** Akun Pembina. */
    public function pembina(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'pembina', 'jenjang' => null]);
    }

    /** Akun Admin Jenjang (wajib terikat satu jenjang). */
    public function adminJenjang(string $jenjang = 'mts'): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'admin', 'jenjang' => $jenjang]);
    }

    /** Akun Wali Santri. */
    public function waliSantri(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'wali_santri', 'jenjang' => null]);
    }

    /** Akun Alumni. */
    public function alumni(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'alumni', 'jenjang' => null]);
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
}
