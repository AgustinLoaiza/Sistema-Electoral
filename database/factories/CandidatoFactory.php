<?php

namespace Database\Factories;

use App\Models\Candidato;
use App\Models\Eleccion;
use App\Models\Puesto;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Candidato>
 */
class CandidatoFactory extends Factory
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
            'usuario_id' => Usuario::factory()->votante(),
            'estado' => Candidato::ESTADO_APROBADO,
        ];
    }
}
