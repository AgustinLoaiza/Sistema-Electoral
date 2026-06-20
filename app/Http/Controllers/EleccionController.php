<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Campamento;
use App\Models\Eleccion;
use App\Models\Puesto;

class EleccionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $elecciones = Eleccion::with('campamento', 'creador')
            ->latest()
            ->paginate(15);

        return view('elecciones.index', compact('elecciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $campamentos = Campamento::query()->where('activo', true)->orderBy('nombre')->get();
        $puestos = Puesto::query()->where('activo', true)->orderBy('orden')->get();

        return view('elecciones.create', compact('campamentos', 'puestos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'campamento_id' => ['required', 'exists:campamentos,id'],
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'puestos' => ['required', 'array'],
            'puestos.*' => ['exists:puestos,id'],
        ]);

        $eleccion = Eleccion::create([
            'campamento_id' => $data['campamento_id'],
            'creado_por' => Auth::id(),
            'titulo' => $data['titulo'],
            'descripcion' => $data['descripcion'] ?? null,
            'estado' => Eleccion::ESTADO_BORRADOR,
            'resultados_publicados' => false,
        ]);

        foreach ($data['puestos'] as $index => $puestoId) {
            $eleccion->puestos()->attach($puestoId, [
                'orden' => $index + 1,
                'activo' => true,
            ]);
        }

        return redirect()->route('elecciones.index')->with('success', 'Elección creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Eleccion $eleccion)
    {
        $eleccion->load('campamento', 'puestos', 'candidatos.usuario', 'votantesEleccion.usuario');

        return view('elecciones.show', compact('eleccion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Eleccion $eleccion)
    {
        if ($eleccion->estado !== Eleccion::ESTADO_BORRADOR) {
            return back()->withErrors('Solo puedes editar elecciones en borrador.');
        }

        $campamentos = Campamento::query()->where('activo', true)->orderBy('nombre')->get();
        $puestos = Puesto::query()->where('activo', true)->orderBy('orden')->get();

        return view('elecciones.edit', compact('eleccion', 'campamentos', 'puestos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Eleccion $eleccion)
    {
        if ($eleccion->estado !== Eleccion::ESTADO_BORRADOR) {
            return back()->withErrors('Solo puedes editar elecciones en borrador.');
        }

        $data = $request->validate([
            'campamento_id' => ['required', 'exists:campamentos,id'],
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
        ]);

        $eleccion->update($data);

        return redirect()->route('elecciones.show', $eleccion)->with('success', 'Elección actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Eleccion $eleccion)
    {
        if ($eleccion->estado !== Eleccion::ESTADO_BORRADOR) {
            return back()->withErrors('Solo puedes eliminar elecciones en borrador.');
        }

        Eleccion::query()->whereKey($eleccion->id)->delete();

        return redirect()->route('elecciones.index')->with('success', 'Elección eliminada correctamente.');
    }

    public function abrir(Eleccion $eleccion)
    {
        if ($eleccion->estado !== Eleccion::ESTADO_BORRADOR) {
            return back()->withErrors('Solo puedes abrir elecciones en borrador.');
        }

        $eleccion->update([
            'estado' => Eleccion::ESTADO_ABIERTA,
            'fecha_inicio' => now(),
        ]);

        return back()->with('success', 'Elección abierta correctamente.');
    }

    public function cerrar(Eleccion $eleccion)
    {
        if ($eleccion->estado !== Eleccion::ESTADO_ABIERTA) {
            return back()->withErrors('Solo puedes cerrar elecciones abiertas.');
        }

        $eleccion->update([
            'estado' => Eleccion::ESTADO_CERRADA,
            'fecha_fin' => now(),
        ]);

        return back()->with('success', 'Elección cerrada correctamente.');
    }

    public function publicar(Eleccion $eleccion)
    {
        if ($eleccion->estado !== Eleccion::ESTADO_CERRADA) {
            return back()->withErrors('Solo puedes publicar resultados de elecciones cerradas.');
        }

        $eleccion->update([
            'estado' => Eleccion::ESTADO_PUBLICADA,
            'resultados_publicados' => true,
        ]);

        return back()->with('success', 'Resultados publicados correctamente.');
    }
}
