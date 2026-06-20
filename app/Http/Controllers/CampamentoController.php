<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Campamento;

class CampamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $campamentos = Campamento::query()
            ->withCount(['usuarios', 'elecciones'])
            ->orderByDesc('activo')
            ->orderBy('nombre')
            ->paginate(15);

        return view('campamentos.index', compact('campamentos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('campamentos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:50', 'unique:campamentos,numero'],
            'ciudad' => ['nullable', 'string', 'max:255'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $data['activo'] = $request->boolean('activo');

        Campamento::create($data);

        return redirect()->route('campamentos.index')->with('success', 'Campamento creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Campamento $campamento)
    {
        $campamento->loadCount(['usuarios', 'elecciones']);
        $campamento->load([
            'elecciones' => fn ($query) => $query->latest()->limit(10),
            'usuarios' => fn ($query) => $query->orderBy('apellidos')->orderBy('nombres')->limit(10),
        ]);

        return view('campamentos.show', compact('campamento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Campamento $campamento)
    {
        return view('campamentos.edit', compact('campamento'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Campamento $campamento)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:50', Rule::unique('campamentos', 'numero')->ignore($campamento->id)],
            'ciudad' => ['nullable', 'string', 'max:255'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $data['activo'] = $request->boolean('activo');

        $campamento->update($data);

        return redirect()->route('campamentos.index')->with('success', 'Campamento actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campamento $campamento)
    {
        $campamento->update(['activo' => false]);

        return redirect()->route('campamentos.index')->with('success', 'Campamento inhabilitado correctamente.');
    }
}
