<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Candidato;
use App\Models\Eleccion;
use App\Models\VotanteEleccion;
use App\Models\Voto;

class VotoController extends Controller
{
    public function create(Eleccion $eleccion)
    {
        $usuario = Auth::user();

        if ($eleccion->estado !== Eleccion::ESTADO_ABIERTA) {
            return back()->withErrors('La eleccion no esta abierta.');
        }

        $votante = VotanteEleccion::query()
            ->where('eleccion_id', $eleccion->id)
            ->where('usuario_id', $usuario->id)
            ->first();

        if (! $votante || ! $votante->habilitado) {
            return back()->withErrors('No estas habilitado para votar en esta eleccion.');
        }

        if ($votante->voto_emitido) {
            return back()->withErrors('Ya has emitido tu voto en esta eleccion.');
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
            $votante = VotanteEleccion::query()
                ->where('eleccion_id', $eleccion->id)
                ->where('usuario_id', $usuario->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($eleccion->estado !== Eleccion::ESTADO_ABIERTA) {
                abort(403, 'La eleccion no esta abierta.');
            }

            if (! $votante->habilitado || $votante->voto_emitido) {
                abort(403, 'No puedes votar en esta eleccion.');
            }

            $puestosHabilitados = $eleccion->puestos()
                ->wherePivot('activo', true)
                ->pluck('puestos.id')
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values();

            $puestosEnviados = collect($data['votos'])
                ->pluck('puesto_id')
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values();

            if ($puestosEnviados->duplicates()->isNotEmpty() || $puestosHabilitados->all() !== $puestosEnviados->all()) {
                abort(422, 'Debes emitir exactamente un voto por cada puesto habilitado.');
            }

            foreach ($data['votos'] as $voto) {
                $tipoVoto = $voto['seleccion'];
                $candidatoId = null;
                $puestoId = (int) $voto['puesto_id'];

                if (str_starts_with($voto['seleccion'], 'candidato:')) {
                    $tipoVoto = Voto::TIPO_CANDIDATO;
                    $candidatoId = (int) str_replace('candidato:', '', $voto['seleccion']);
                }

                if (! in_array($tipoVoto, [Voto::TIPO_CANDIDATO, Voto::TIPO_BLANCO, Voto::TIPO_NULO], true)) {
                    abort(422, 'Seleccion de voto no valida.');
                }

                if ($tipoVoto === Voto::TIPO_CANDIDATO) {
                    $candidatoValido = Candidato::query()
                        ->whereKey($candidatoId)
                        ->where('eleccion_id', $eleccion->id)
                        ->where('puesto_id', $puestoId)
                        ->where('estado', Candidato::ESTADO_APROBADO)
                        ->exists();

                    if (! $candidatoValido) {
                        abort(422, 'El candidato seleccionado no corresponde al puesto o eleccion.');
                    }
                }

                Voto::create([
                    'eleccion_id' => $eleccion->id,
                    'puesto_id' => $puestoId,
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
