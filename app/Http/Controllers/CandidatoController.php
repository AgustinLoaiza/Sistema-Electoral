<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidato;
use App\Models\Eleccion;
use App\Models\Usuario;

class CandidatoController extends Controller
{
    public function create(Eleccion $eleccion)
    {
        if ($eleccion->estado !== Eleccion::ESTADO_BORRADOR) {
            return back()->withErrors('Solo puedes agregar candidatos en elecciones en borrador.');
        }

        $eleccion->load('puestos');
        $usuarios = Usuario::query()->where('activo', true)
            ->where('rol', 'votante')
            ->orderBy('apellidos', 'asc')
            ->orderBy('nombres', 'asc')
            ->get();

        return view('candidatos.create', compact('eleccion', 'usuarios'));
    }

    public function store(Request $request, Eleccion $eleccion)
    {
        if ($eleccion->estado !== Eleccion::ESTADO_BORRADOR) {
            return back()->withErrors('Solo puedes agregar candidatos en elecciones en borrador.');
        }

        $data = $request->validate([
            'puesto_id' => ['required', 'exists:puestos,id'],
            'usuario_id' => ['required', 'exists:usuarios,id'],
        ]);

        Candidato::create([
            'eleccion_id' => $eleccion->id,
            'puesto_id' => $data['puesto_id'],
            'usuario_id' => $data['usuario_id'],
            'estado' => Candidato::ESTADO_APROBADO,
        ]);

        return redirect()->route('elecciones.show', $eleccion)->with('success', 'Candidato agregado correctamente.');
    }

    public function destroy(Candidato $candidato)
    {
        if ($candidato->eleccion->estado !== Eleccion::ESTADO_BORRADOR) {
            return back()->withErrors('Solo puedes retirar candidatos en elecciones en borrador.');
        }

        $candidato->update([
            'estado' => Candidato::ESTADO_RETIRADO,
        ]);

        return back()->with('success', 'Candidato retirado correctamente.');
    }
}
