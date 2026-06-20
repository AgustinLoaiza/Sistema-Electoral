<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Puesto;

class PuestoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $puestos = Puesto::query()->orderBy('orden', 'asc')->paginate(15);

        return view('puestos.index', compact('puestos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('puestos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'orden' => ['required', 'integer', 'min:0'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $data['activo'] = $request->boolean('activo');

        Puesto::create($data);

        return redirect()->route('puestos.index')->with('success', 'Puesto creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Puesto $puesto)
    {
        return view('puestos.edit', compact('puesto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Puesto $puesto)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'orden' => ['required', 'integer', 'min:0'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $data['activo'] = $request->boolean('activo');

        $puesto->update($data);

        return redirect()->route('puestos.index')->with('success', 'Puesto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Puesto $puesto)
    {
        $puesto->update(['activo' => false]);

        return redirect()->route('puestos.index')->with('success', 'Puesto inhabilitado correctamente.');
    }
}
