<?php

namespace Database\Seeders;

use App\Models\Campamento;
use App\Models\Candidato;
use App\Models\Eleccion;
use App\Models\EleccionPuesto;
use App\Models\Puesto;
use App\Models\Usuario;
use App\Models\VotanteEleccion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $campamento = Campamento::factory()->create([
            'nombre' => 'Capitulo Antonio Jose de Sucre',
            'numero' => '75019',
            'ciudad' => 'Sucre',
        ]);

        $superAdmin = Usuario::factory()->superAdmin()->create([
            'campamento_id' => $campamento->id,
            'nombres' => 'Oscar Agustin',
            'apellidos' => 'Loaiza Torres',
            'nombre_usuario' => 'agustin.loaiza',
            'password' => '12345678',
        ]);

        $administrador = Usuario::factory()->admin()->create([
            'campamento_id' => $campamento->id,
            'nombres' => 'Mauricio',
            'apellidos' => 'Dulon Castro',
            'nombre_usuario' => 'mauricio.dulon',
            'password' => '12345678',
        ]);

        $votantes = Usuario::factory()->count(20)->votante()->create([
            'campamento_id' => $campamento->id,
        ]);

        $puestosBase = [
            'Maestro Consejero',
            'Primer Consejero',
            'Segundo Consejero',
            'Escriba',
            'Tesorero',
        ];

        $puestos = collect($puestosBase)->map(function (string $nombre, int $index) {
            return Puesto::create([
                'nombre' => $nombre,
                'orden' => $index + 1,
                'activo' => true,
            ]);
        });

        $eleccion = Eleccion::factory()->create([
            'campamento_id' => $campamento->id,
            'creado_por' => $administrador->id,
            'titulo' => 'Eleccion Capitular Junio 2026',
            'descripcion' => 'Eleccion de prueba para renovacion de oficialidad.',
            'estado' => Eleccion::ESTADO_BORRADOR,
        ]);

        $puestos->each(function (Puesto $puesto, int $index) use ($eleccion) {
            EleccionPuesto::create([
                'eleccion_id' => $eleccion->id,
                'puesto_id' => $puesto->id,
                'orden' => $index + 1,
                'activo' => true,
            ]);
        });

        $puestos->take(3)->each(function (Puesto $puesto, int $index) use ($eleccion, $votantes) {
            Candidato::create([
                'eleccion_id' => $eleccion->id,
                'puesto_id' => $puesto->id,
                'usuario_id' => $votantes[$index]->id,
                'estado' => Candidato::ESTADO_APROBADO,
            ]);
        });

        $votantes->each(function (Usuario $usuario) use ($eleccion) {
            VotanteEleccion::create([
                'eleccion_id' => $eleccion->id,
                'usuario_id' => $usuario->id,
                'habilitado' => true,
                'voto_emitido' => false,
                'fecha_voto' => null,
            ]);
        });
    }
}
