<?php

namespace Database\Factories;

use App\Models\Eleccion;
use App\Models\Usuario;
use App\Models\VotanteEleccion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VotanteEleccion>
 */
class VotanteEleccionFactory extends Factory
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
            'usuario_id' => Usuario::factory()->votante(),
            'habilitado' => true,
            'voto_emitido' => false,
            'fecha_voto' => null,
        ];
    }
}
