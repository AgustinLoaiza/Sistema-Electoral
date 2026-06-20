<?php

namespace Database\Factories;

use App\Models\Campamento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Campamento>
 */
class CampamentoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => 'Capitulo '.$this->faker->city(),
            'numero' => (string) $this->faker->unique()->numberBetween(10000, 99999),
            'ciudad' => $this->faker->city(),
            'activo' => true,
        ];
    }
}
