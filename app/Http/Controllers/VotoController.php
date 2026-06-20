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
            'votos.*.seleccion' => ['required', 'string'],
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
                $tipoVoto = $voto['seleccion'];
                $candidatoId = null;

                if (str_starts_with($voto['seleccion'], 'candidato:')) {
                    $tipoVoto = Voto::TIPO_CANDIDATO;
                    $candidatoId = (int) str_replace('candidato:', '', $voto['seleccion']);
                }

                if (! in_array($tipoVoto, [Voto::TIPO_CANDIDATO, Voto::TIPO_BLANCO, Voto::TIPO_NULO], true)) {
                    abort(422, 'Seleccion de voto no valida.');
                }

                Voto::create([
                    'eleccion_id' => $eleccion->id,
                    'puesto_id' => $voto['puesto_id'],
                    'candidato_id' => $candidatoId,
                    'tipo_voto' => $tipoVoto,
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
