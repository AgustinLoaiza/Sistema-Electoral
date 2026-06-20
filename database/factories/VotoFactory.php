<?php

namespace Database\Factories;

use App\Models\Candidato;
use App\Models\Eleccion;
use App\Models\Puesto;
use App\Models\Voto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Voto>
 */
class VotoFactory extends Factory
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
            'candidato_id' => Candidato::factory(),
            'tipo_voto' => Voto::TIPO_CANDIDATO,
        ];
    }

    public function blanco(): static
    {
        return $this->state(fn (array $attributes) => [
            'candidato_id' => null,
            'tipo_voto' => Voto::TIPO_BLANCO,
        ]);
    }

    public function nulo(): static
    {
        return $this->state(fn (array $attributes) => [
            'candidato_id' => null,
            'tipo_voto' => Voto::TIPO_NULO,
        ]);
    }
}
