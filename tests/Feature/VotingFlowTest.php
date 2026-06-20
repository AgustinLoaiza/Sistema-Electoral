<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Campamento;
use App\Models\Candidato;
use App\Models\Eleccion;
use App\Models\Puesto;
use App\Models\Usuario;
use App\Models\VotanteEleccion;
use App\Models\Voto;
use Tests\TestCase;

class VotingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_enabled_voter_can_vote_once_for_all_positions(): void
    {
        $context = $this->electionContext();
        $votante = $this->enabledVoter($context['eleccion'], $context['campamento']);

        $response = $this->actingAs($votante)->post(route('votos.store', $context['eleccion']), [
            'votos' => [
                [
                    'puesto_id' => $context['puestos'][0]->id,
                    'seleccion' => 'candidato:' . $context['candidatos'][0]->id,
                ],
                [
                    'puesto_id' => $context['puestos'][1]->id,
                    'seleccion' => Voto::TIPO_BLANCO,
                ],
            ],
        ]);

        $response->assertRedirect(route('votante.dashboard'));
        $this->assertDatabaseCount('votos', 2);
        $this->assertDatabaseHas('votos', [
            'eleccion_id' => $context['eleccion']->id,
            'puesto_id' => $context['puestos'][0]->id,
            'candidato_id' => $context['candidatos'][0]->id,
            'tipo_voto' => Voto::TIPO_CANDIDATO,
        ]);
        $this->assertDatabaseHas('votantes_elecciones', [
            'eleccion_id' => $context['eleccion']->id,
            'usuario_id' => $votante->id,
            'voto_emitido' => true,
        ]);
    }

    public function test_voter_cannot_vote_twice(): void
    {
        $context = $this->electionContext();
        $votante = $this->enabledVoter($context['eleccion'], $context['campamento']);
        $payload = $this->validVotePayload($context);

        $this->actingAs($votante)->post(route('votos.store', $context['eleccion']), $payload)
            ->assertRedirect(route('votante.dashboard'));

        $this->actingAs($votante)->post(route('votos.store', $context['eleccion']), $payload)
            ->assertForbidden();

        $this->assertDatabaseCount('votos', 2);
    }

    public function test_vote_rejects_candidate_from_another_election(): void
    {
        $context = $this->electionContext();
        $otherContext = $this->electionContext();
        $votante = $this->enabledVoter($context['eleccion'], $context['campamento']);

        $response = $this->actingAs($votante)->post(route('votos.store', $context['eleccion']), [
            'votos' => [
                [
                    'puesto_id' => $context['puestos'][0]->id,
                    'seleccion' => 'candidato:' . $otherContext['candidatos'][0]->id,
                ],
                [
                    'puesto_id' => $context['puestos'][1]->id,
                    'seleccion' => Voto::TIPO_BLANCO,
                ],
            ],
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('votos', 0);
        $this->assertDatabaseHas('votantes_elecciones', [
            'eleccion_id' => $context['eleccion']->id,
            'usuario_id' => $votante->id,
            'voto_emitido' => false,
        ]);
    }

    public function test_vote_requires_exactly_one_selection_per_enabled_position(): void
    {
        $context = $this->electionContext();
        $votante = $this->enabledVoter($context['eleccion'], $context['campamento']);

        $response = $this->actingAs($votante)->post(route('votos.store', $context['eleccion']), [
            'votos' => [
                [
                    'puesto_id' => $context['puestos'][0]->id,
                    'seleccion' => 'candidato:' . $context['candidatos'][0]->id,
                ],
                [
                    'puesto_id' => $context['puestos'][0]->id,
                    'seleccion' => Voto::TIPO_BLANCO,
                ],
            ],
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('votos', 0);
    }

    public function test_fifty_enabled_voters_can_cast_votes_without_losing_records(): void
    {
        $context = $this->electionContext();

        $votantes = Usuario::factory()
            ->count(50)
            ->votante()
            ->create([
                'campamento_id' => $context['campamento']->id,
                'debe_cambiar_password' => false,
            ]);

        foreach ($votantes as $votante) {
            VotanteEleccion::factory()->create([
                'eleccion_id' => $context['eleccion']->id,
                'usuario_id' => $votante->id,
                'habilitado' => true,
                'voto_emitido' => false,
            ]);

            $this->actingAs($votante)->post(route('votos.store', $context['eleccion']), $this->validVotePayload($context))
                ->assertRedirect(route('votante.dashboard'));
        }

        $this->assertDatabaseCount('votos', 100);
        $this->assertSame(50, VotanteEleccion::query()
            ->where('eleccion_id', $context['eleccion']->id)
            ->where('voto_emitido', true)
            ->count());
    }

    private function electionContext(): array
    {
        $campamento = Campamento::factory()->create();
        $admin = Usuario::factory()->admin()->create(['campamento_id' => $campamento->id]);

        $eleccion = Eleccion::factory()->create([
            'campamento_id' => $campamento->id,
            'creado_por' => $admin->id,
            'estado' => Eleccion::ESTADO_ABIERTA,
            'fecha_inicio' => now(),
        ]);

        $puestos = collect([
            Puesto::factory()->create(['nombre' => 'Maestro Consejero', 'orden' => 1]),
            Puesto::factory()->create(['nombre' => 'Primer Consejero', 'orden' => 2]),
        ]);

        foreach ($puestos as $index => $puesto) {
            $eleccion->puestos()->attach($puesto->id, [
                'orden' => $index + 1,
                'activo' => true,
            ]);
        }

        $candidatos = $puestos->map(function (Puesto $puesto) use ($campamento, $eleccion) {
            $usuario = Usuario::factory()->votante()->create([
                'campamento_id' => $campamento->id,
                'debe_cambiar_password' => false,
            ]);

            return Candidato::factory()->create([
                'eleccion_id' => $eleccion->id,
                'puesto_id' => $puesto->id,
                'usuario_id' => $usuario->id,
                'estado' => Candidato::ESTADO_APROBADO,
            ]);
        });

        return compact('campamento', 'eleccion', 'puestos', 'candidatos');
    }

    private function enabledVoter(Eleccion $eleccion, Campamento $campamento): Usuario
    {
        $votante = Usuario::factory()->votante()->create([
            'campamento_id' => $campamento->id,
            'debe_cambiar_password' => false,
        ]);

        VotanteEleccion::factory()->create([
            'eleccion_id' => $eleccion->id,
            'usuario_id' => $votante->id,
            'habilitado' => true,
            'voto_emitido' => false,
        ]);

        return $votante;
    }

    private function validVotePayload(array $context): array
    {
        return [
            'votos' => [
                [
                    'puesto_id' => $context['puestos'][0]->id,
                    'seleccion' => 'candidato:' . $context['candidatos'][0]->id,
                ],
                [
                    'puesto_id' => $context['puestos'][1]->id,
                    'seleccion' => Voto::TIPO_BLANCO,
                ],
            ],
        ];
    }
}
