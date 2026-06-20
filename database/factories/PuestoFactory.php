<?php

namespace Database\Factories;

use App\Models\Puesto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Puesto>
 */
class PuestoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->randomElement([
                'Maestro Consejero',
                'Primer Consejero',
                'Segundo Consejero',
                'Escriba',
                'Tesorero',
                'Hospitalario',
                'Orador',
            ]),
            'descripcion' => null,
            'orden' => $this->faker->numberBetween(1, 20),
            'activo' => true,
        ];
    }
}
