<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Eleccion;
use App\Models\Voto;

class ResultadoController extends Controller
{
    public function show(Eleccion $eleccion)
    {
        $usuario = Auth::user();

        if (! $eleccion->resultados_publicados && ! in_array($usuario->rol, ['admin', 'super_admin'], true)) {
            abort(403);
        }

        $resultados = Voto::query()
            ->selectRaw('puesto_id, candidato_id, tipo_voto, COUNT(*) as total', [])
            ->where('eleccion_id', $eleccion->id)
            ->groupBy('puesto_id', 'candidato_id', 'tipo_voto')
            ->with('puesto', 'candidato.usuario')
            ->get();

        return view('resultados.show', compact('eleccion', 'resultados'));
    }
}
