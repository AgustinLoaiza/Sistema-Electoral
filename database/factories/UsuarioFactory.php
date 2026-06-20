<?php

namespace Database\Factories;

use App\Models\Campamento;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Usuario>
 */
class UsuarioFactory extends Factory
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
            'nombres' => fake()->firstName(),
            'apellidos' => fake()->lastName(),
            'nombre_usuario' => fake()->unique()->userName(),
            'password' => 'password',
            'rol' => Usuario::ROL_VOTANTE,
            'activo' => true,
            'debe_cambiar_password' => true,
            'ultimo_acceso' => null,
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => Usuario::ROL_SUPER_ADMIN,
            'debe_cambiar_password' => false,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => Usuario::ROL_ADMIN,
            'debe_cambiar_password' => false,
        ]);
    }

    public function votante(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => Usuario::ROL_VOTANTE,
        ]);
    }
}
