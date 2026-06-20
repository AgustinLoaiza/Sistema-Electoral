<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Campamento;
use App\Models\Candidato;
use App\Models\Eleccion;
use App\Models\Puesto;
use App\Models\Usuario;
use App\Models\Voto;
use Tests\TestCase;

class ReportsAndFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_be_filtered_by_search_text_and_role(): void
    {
        $admin = Usuario::factory()->admin()->create(['debe_cambiar_password' => false]);
        $campamento = Campamento::factory()->create();

        Usuario::factory()->votante()->create([
            'campamento_id' => $campamento->id,
            'nombres' => 'Ana Maria',
            'apellidos' => 'Sucre',
            'nombre_usuario' => 'ana.sucre',
            'debe_cambiar_password' => false,
        ]);

        Usuario::factory()->admin()->create([
            'campamento_id' => $campamento->id,
            'nombres' => 'Ana Admin',
            'apellidos' => 'Bolivia',
            'nombre_usuario' => 'ana.admin',
            'debe_cambiar_password' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('usuarios.index', [
            'q' => 'ana',
            'rol' => Usuario::ROL_VOTANTE,
        ]));

        $response->assertOk();
        $response->assertSee('ana.sucre');
        $response->assertDontSee('ana.admin');
    }

    public function test_users_pdf_export_respects_current_filters(): void
    {
        $admin = Usuario::factory()->admin()->create(['debe_cambiar_password' => false]);

        Usuario::factory()->votante()->create([
            'nombres' => 'Filtro',
            'apellidos' => 'Votante',
            'nombre_usuario' => 'filtro.votante',
            'debe_cambiar_password' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('usuarios.export-pdf', [
            'q' => 'filtro',
            'rol' => Usuario::ROL_VOTANTE,
        ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-1.4', $response->getContent());
    }

    public function test_elections_pdf_export_is_available_for_admins(): void
    {
        $admin = Usuario::factory()->admin()->create(['debe_cambiar_password' => false]);
        Eleccion::factory()->create(['creado_por' => $admin->id]);

        $response = $this->actingAs($admin)->get(route('elecciones.export-pdf'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-1.4', $response->getContent());
    }

    public function test_published_results_pdf_can_be_downloaded_by_voters(): void
    {
        $campamento = Campamento::factory()->create();
        $admin = Usuario::factory()->admin()->create(['campamento_id' => $campamento->id]);
        $votante = Usuario::factory()->votante()->create([
            'campamento_id' => $campamento->id,
            'debe_cambiar_password' => false,
        ]);

        $eleccion = Eleccion::factory()->create([
            'campamento_id' => $campamento->id,
            'creado_por' => $admin->id,
            'estado' => Eleccion::ESTADO_PUBLICADA,
            'resultados_publicados' => true,
        ]);

        $puesto = Puesto::factory()->create(['nombre' => 'Maestro Consejero', 'orden' => 1]);
        $eleccion->puestos()->attach($puesto->id, ['orden' => 1, 'activo' => true]);

        $candidatoUsuario = Usuario::factory()->votante()->create(['campamento_id' => $campamento->id]);
        $candidato = Candidato::factory()->create([
            'eleccion_id' => $eleccion->id,
            'puesto_id' => $puesto->id,
            'usuario_id' => $candidatoUsuario->id,
        ]);

        Voto::factory()->create([
            'eleccion_id' => $eleccion->id,
            'puesto_id' => $puesto->id,
            'candidato_id' => $candidato->id,
            'tipo_voto' => Voto::TIPO_CANDIDATO,
        ]);

        $response = $this->actingAs($votante)->get(route('resultados.export-pdf', $eleccion));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-1.4', $response->getContent());
    }
}
