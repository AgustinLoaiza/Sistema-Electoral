<?php

namespace Database\Factories;

use App\Models\Campamento;
use App\Models\Eleccion;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Eleccion>
 */
class EleccionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'campamento_id' => Campamento::factory(),
            'creado_por' => Usuario::factory()->admin(),
            'titulo' => 'Eleccion capitular '.$this->faker->monthName(),
            'descripcion' => $this->faker->sentence(),
            'estado' => Eleccion::ESTADO_BORRADOR,
            'fecha_inicio' => null,
            'fecha_fin' => null,
            'resultados_publicados' => false,
        ];
    }
}
