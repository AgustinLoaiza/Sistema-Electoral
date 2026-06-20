<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Campamento;
use App\Models\Usuario;
use Tests\TestCase;

class CampamentoManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_campamento(): void
    {
        $superAdmin = Usuario::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin)->post(route('campamentos.store'), [
            'nombre' => 'Capitulo de Prueba',
            'numero' => '9001',
            'ciudad' => 'La Paz',
            'activo' => '1',
        ]);

        $response->assertRedirect(route('campamentos.index'));
        $this->assertDatabaseHas('campamentos', [
            'nombre' => 'Capitulo de Prueba',
            'numero' => '9001',
            'ciudad' => 'La Paz',
            'activo' => true,
        ]);
    }

    public function test_super_admin_can_update_campamento(): void
    {
        $superAdmin = Usuario::factory()->superAdmin()->create();
        $campamento = Campamento::factory()->create();

        $response = $this->actingAs($superAdmin)->put(route('campamentos.update', $campamento), [
            'nombre' => 'Capitulo Actualizado',
            'numero' => '9002',
            'ciudad' => 'Cochabamba',
            'activo' => '1',
        ]);

        $response->assertRedirect(route('campamentos.index'));
        $this->assertDatabaseHas('campamentos', [
            'id' => $campamento->id,
            'nombre' => 'Capitulo Actualizado',
            'numero' => '9002',
            'ciudad' => 'Cochabamba',
            'activo' => true,
        ]);
    }

    public function test_super_admin_can_disable_campamento_without_deleting_it(): void
    {
        $superAdmin = Usuario::factory()->superAdmin()->create();
        $campamento = Campamento::factory()->create(['activo' => true]);

        $response = $this->actingAs($superAdmin)->delete(route('campamentos.destroy', $campamento));

        $response->assertRedirect(route('campamentos.index'));
        $this->assertDatabaseHas('campamentos', [
            'id' => $campamento->id,
            'activo' => false,
        ]);
    }

    public function test_admin_cannot_manage_campamentos(): void
    {
        $admin = Usuario::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('campamentos.index'));

        $response->assertForbidden();
    }
}
