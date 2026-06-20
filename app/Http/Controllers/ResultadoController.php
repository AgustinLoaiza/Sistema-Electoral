<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Candidato;
use App\Models\Eleccion;
use App\Models\Voto;
use App\Support\PdfReport;

class ResultadoController extends Controller
{
    public function show(Eleccion $eleccion)
    {
        $this->authorizeResults($eleccion);

        [$resultados, $resultadosPorPuesto] = $this->buildResults($eleccion);

        return view('resultados.show', compact('eleccion', 'resultados', 'resultadosPorPuesto'));
    }

    public function exportPdf(Eleccion $eleccion)
    {
        $this->authorizeResults($eleccion);

        [, $resultadosPorPuesto] = $this->buildResults($eleccion);

        $rows = collect();

        foreach ($resultadosPorPuesto as $grupo) {
            foreach ($grupo['opciones'] as $opcion) {
                $rows->push([
                    $grupo['puesto']->nombre,
                    $opcion['nombre'],
                    $opcion['tipo'],
                    (string) $opcion['total'],
                    number_format($opcion['porcentaje'], 1, ',', '.') . '%',
                ]);
            }
        }

        $pdf = PdfReport::make(
            'Resultados electorales',
            $eleccion->titulo . ' - ' . ($eleccion->campamento->nombre ?? 'Campamento'),
            ['Puesto', 'Opcion', 'Tipo', 'Votos', 'Porcentaje'],
            $rows
        );

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="resultados-eleccion-' . $eleccion->id . '.pdf"',
            'Cache-Control' => 'no-store',
        ]);
    }

    private function authorizeResults(Eleccion $eleccion): void
    {
        $usuario = Auth::user();

        if (! $eleccion->resultados_publicados && ! in_array($usuario->rol, ['admin', 'super_admin'], true)) {
            abort(403);
        }
    }

    private function buildResults(Eleccion $eleccion): array
    {
        $eleccion->load([
            'campamento',
            'puestos' => fn ($query) => $query
                ->wherePivot('activo', true)
                ->orderBy('eleccion_puestos.orden')
                ->orderBy('puestos.orden'),
            'candidatos' => fn ($query) => $query
                ->with('usuario')
                ->where('estado', Candidato::ESTADO_APROBADO)
                ->orderBy('puesto_id')
                ->orderBy('id'),
        ]);

        $resultados = Voto::query()
            ->selectRaw('puesto_id, candidato_id, tipo_voto, COUNT(*) as total', [])
            ->where('eleccion_id', $eleccion->id)
            ->groupBy('puesto_id', 'candidato_id', 'tipo_voto')
            ->with('puesto', 'candidato.usuario')
            ->get();

        $conteosPorPuesto = $resultados->groupBy('puesto_id');

        $resultadosPorPuesto = $eleccion->puestos->map(function ($puesto) use ($eleccion, $conteosPorPuesto) {
            $conteos = $conteosPorPuesto->get($puesto->id, collect())->keyBy(function ($resultado) {
                return $resultado->tipo_voto === Voto::TIPO_CANDIDATO
                    ? 'candidato:' . $resultado->candidato_id
                    : $resultado->tipo_voto;
            });

            $opciones = collect();

            foreach ($eleccion->candidatos->where('puesto_id', $puesto->id) as $candidato) {
                $conteo = $conteos->get('candidato:' . $candidato->id);

                $opciones->push([
                    'nombre' => trim($candidato->usuario->nombres . ' ' . $candidato->usuario->apellidos),
                    'tipo' => 'candidato',
                    'total' => (int) ($conteo->total ?? 0),
                ]);
            }

            foreach ([Voto::TIPO_BLANCO => 'Voto blanco', Voto::TIPO_NULO => 'Voto nulo'] as $tipo => $nombre) {
                $conteo = $conteos->get($tipo);

                $opciones->push([
                    'nombre' => $nombre,
                    'tipo' => $tipo,
                    'total' => (int) ($conteo->total ?? 0),
                ]);
            }

            $totalPuesto = $opciones->sum('total');

            return [
                'puesto' => $puesto,
                'total' => $totalPuesto,
                'opciones' => $opciones->map(function ($opcion) use ($totalPuesto) {
                    $opcion['porcentaje'] = $totalPuesto > 0
                        ? round(($opcion['total'] / $totalPuesto) * 100, 1)
                        : 0;

                    return $opcion;
                }),
            ];
        });

        return [$resultados, $resultadosPorPuesto];
    }
}
