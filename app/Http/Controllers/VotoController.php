<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Eleccion;
use App\Models\VotanteEleccion;
use App\Models\Voto;

class VotoController extends Controller
{
    public function create(Eleccion $eleccion)
    {
        $usuario = Auth::user();

        if ($eleccion->estado !== Eleccion::ESTADO_ABIERTA) {
            return back()->withErrors('La elección no está abierta.');
        }

        $votante = VotanteEleccion::query()->where('eleccion_id', $eleccion->id)->where('usuario_id', $usuario->id)->first();

        if (!$votante || !$votante->habilitado) {
            return back()->withErrors('No estás habilitado para votar en esta elección.');
        }

        if ($votante->voto_emitido) {
            return back()->withErrors('Ya has emitido tu voto en esta elección.');
        }

        $eleccion->load('puestos', 'candidatos.usuario', 'candidatos.puesto');

        return view('votos.create', compact('eleccion'));
    }

    public function store(Request $request, Eleccion $eleccion)
    {
        $usuario = Auth::user();

        $data = $request->validate([
            'votos' => ['required', 'array'],
            'votos.*.puesto_id' => ['required', 'exists:puestos,id'],
            'votos.*.tipo_voto' => ['required', 'in:candidato,blanco,nulo'],
            'votos.*.candidato_id' => ['nullable', 'exists:candidatos,id'],
        ]);

        DB::transaction(function () use ($eleccion, $usuario, $data) {
            $votante = VotanteEleccion::query()->where('eleccion_id', $eleccion->id)
                ->where('usuario_id', $usuario->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($eleccion->estado !== Eleccion::ESTADO_ABIERTA) {
                abort(403, 'La elección no está abierta.');
            }

            if (! $votante->habilitado || $votante->voto_emitido) {
                abort(403, 'No puedes votar en esta elección.');
            }

            foreach ($data['votos'] as $voto) {
                Voto::create([
                    'eleccion_id' => $eleccion->id,
                    'puesto_id' => $voto['puesto_id'],
                    'candidato_id' => $voto['tipo_voto'] === Voto::TIPO_CANDIDATO
                        ? $voto['candidato_id']
                        : null,
                    'tipo_voto' => $voto['tipo_voto'],
                ]);
            }

            $votante->update([
                'voto_emitido' => true,
                'fecha_voto' => now(),
            ]);
        });

        return redirect()->route('votante.dashboard')->with('success', 'Tu voto fue registrado correctamente.');
    }
}
