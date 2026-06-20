<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Eleccion;
use App\Models\VotanteEleccion;

class MonitorController extends Controller
{
    public function show(Eleccion $eleccion)
    {
        return view('monitor.show', compact('eleccion'));
    }

    public function data(Eleccion $eleccion)
    {
        $habilitados = VotanteEleccion::query()->where('eleccion_id', $eleccion->id)
            ->where('habilitado', true)
            ->count();

        $votaron = VotanteEleccion::query()->where('eleccion_id', $eleccion->id)
            ->where('habilitado', true)
            ->where('voto_emitido', true)
            ->count();

        return response()->json([
            'eleccion_id' => $eleccion->id,
            'estado' => $eleccion->estado,
            'habilitados' => $habilitados,
            'votaron' => $votaron,
            'faltan' => max($habilitados - $votaron, 0),
            'porcentaje' => $habilitados > 0 ? round(($votaron / $habilitados) * 100, 2) : 0,
        ]);
    }
}
