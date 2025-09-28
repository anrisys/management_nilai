<?php

namespace Database\Factories;

use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Nilai>
 */
class NilaiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mapel = ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'IPA', 'IPS', 'PKN', 'Seni Budaya', 'PJOK'];
        
        return [
            'siswa_id' => Siswa::factory(),
            'kelas' => $this->faker->randomElement(['7A', '7B', '8A', '8B', '9A', '9B']),
            'mapel' => $this->faker->randomElement($mapel),
            'nilai' => $this->faker->numberBetween(50, 100),
        ];
    }

    /**
     * State untuk nilai excellent (85-100)
     */
    public function excellent(): static
    {
        return $this->state(fn (array $attributes) => [
            'nilai' => $this->faker->numberBetween(85, 100),
        ]);
    }

    /**
     * State untuk nilai good (75-84)
     */
    public function good(): static
    {
        return $this->state(fn (array $attributes) => [
            'nilai' => $this->faker->numberBetween(75, 84),
        ]);
    }

    /**
     * State untuk nilai specific mapel
     */
    public function mapel(string $mapel): static
    {
        return $this->state(fn (array $attributes) => [
            'mapel' => $mapel,
        ]);
    }

    /**
     * State untuk nilai specific kelas
     */
    public function kelas(string $kelas): static
    {
        return $this->state(fn (array $attributes) => [
            'kelas' => $kelas,
        ]);
    }
}
