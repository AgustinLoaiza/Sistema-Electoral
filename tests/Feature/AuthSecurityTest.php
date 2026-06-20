<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Campamento;
use App\Models\Usuario;
use Tests\TestCase;

class AuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_username_and_is_redirected_to_dashboard(): void
    {
        $usuario = Usuario::factory()->create([
            'password' => 'secret123',
            'activo' => true,
            'debe_cambiar_password' => false,
        ]);

        $response = $this->post(route('login.store'), [
            'nombre_usuario' => $usuario->nombre_usuario,
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($usuario);
        $this->assertDatabaseHas('audit_logs', [
            'usuario_id' => $usuario->id,
            'accion' => 'login_exitoso',
        ]);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $usuario = Usuario::factory()->create([
            'password' => 'secret123',
            'activo' => false,
            'debe_cambiar_password' => false,
        ]);

        $response = $this->from(route('login'))->post(route('login.store'), [
            'nombre_usuario' => $usuario->nombre_usuario,
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors('nombre_usuario');
        $this->assertGuest();
    }

    public function test_user_that_must_change_password_is_redirected_to_change_password_screen(): void
    {
        $usuario = Usuario::factory()->create([
            'password' => 'secret123',
            'activo' => true,
            'debe_cambiar_password' => true,
        ]);

        $response = $this->post(route('login.store'), [
            'nombre_usuario' => $usuario->nombre_usuario,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('password.change'));
    }

    public function test_voter_cannot_access_user_administration(): void
    {
        $votante = Usuario::factory()->votante()->create([
            'debe_cambiar_password' => false,
        ]);

        $response = $this->actingAs($votante)->get(route('usuarios.index'));

        $response->assertForbidden();
    }

    public function test_inactive_authenticated_user_is_returned_to_login(): void
    {
        $usuario = Usuario::factory()->create([
            'activo' => false,
            'debe_cambiar_password' => false,
        ]);

        $response = $this->actingAs($usuario)->get(route('dashboard'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('nombre_usuario');
    }
}
