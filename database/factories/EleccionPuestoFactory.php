<?php

namespace Database\Factories;

use App\Models\EleccionPuesto;
use App\Models\Eleccion;
use App\Models\Puesto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EleccionPuesto>
 */
class EleccionPuestoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'eleccion_id' => Eleccion::factory(),
            'puesto_id' => Puesto::factory(),
            'orden' => $this->faker->numberBetween(1, 20),
            'activo' => true,
        ];
    }
}
