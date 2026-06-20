<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Eleccion;
use App\Models\Usuario;
use App\Models\VotanteEleccion;

class VotanteEleccionController extends Controller
{
    public function index(Eleccion $eleccion)
    {
        $votantes = VotanteEleccion::with('usuario')
            ->where('eleccion_id', $eleccion->id)
            ->paginate(20);

        $usuarios = Usuario::query()
            ->where('activo', true)
            ->where('rol', 'votante')
            ->orderBy('apellidos', 'asc')
            ->orderBy('nombres', 'asc')
            ->get();

        return view('votantes.index', compact('eleccion', 'votantes', 'usuarios'));
    }

    public function store(Request $request, Eleccion $eleccion)
    {
        if ($eleccion->estado !== Eleccion::ESTADO_BORRADOR) {
            return back()->withErrors('Solo puedes habilitar votantes en elecciones en borrador.');
        }

        $data = $request->validate([
            'usuario_id' => ['required', 'exists:usuarios,id'],
        ]);

        VotanteEleccion::firstOrCreate(
            [
                'eleccion_id' => $eleccion->id,
                'usuario_id' => $data['usuario_id'],
            ],
            [
                'habilitado' => true,
                'voto_emitido' => false,
            ]
        );

        return back()->with('success', 'Votante habilitado correctamente.');
    }

    public function habilitar(VotanteEleccion $votanteEleccion)
    {
        $votanteEleccion->update(['habilitado' => true]);

        return back()->with('success', 'Votante habilitado.');
    }

    public function inhabilitar(VotanteEleccion $votanteEleccion)
    {
        if ($votanteEleccion->voto_emitido) {
            return back()->withErrors('No puedes inhabilitar a un usuario que ya votó.');
        }

        $votanteEleccion->update(['habilitado' => false]);

        return back()->with('success', 'Votante inhabilitado.');
    }
}
