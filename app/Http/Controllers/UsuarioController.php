<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campamento;
use App\Models\Usuario;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuarios = Usuario::with('campamento')
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->paginate(15);

        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $campamentos = Campamento::query()->where('activo', true)->orderBy('nombre')->get();

        return view('usuarios.create', compact('campamentos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'campamento_id' => ['required', 'exists:campamentos,id'],
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'nombre_usuario' => ['required', 'string', 'max:255', 'unique:usuarios,nombre_usuario'],
            'password' => ['required', 'string', 'min:8'],
            'rol' => ['required', 'in:super_admin,admin,votante'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $data['activo'] = $request->boolean('activo');
        $data['debe_cambiar_password'] = true;

        Usuario::create($data);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Usuario $usuario)
    {
        return view('usuarios.show', compact('usuario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Usuario $usuario)
    {
        $campamentos = Campamento::query()->where('activo', true)->orderBy('nombre')->get();

        return view('usuarios.edit', compact('usuario', 'campamentos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Usuario $usuario)
    {
        $data = $request->validate([
            'campamento_id' => ['required', 'exists:campamentos,id'],
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'nombre_usuario' => ['required', 'string', 'max:255', 'unique:usuarios,nombre_usuario,'.$usuario->id],
            'rol' => ['required', 'in:super_admin,admin,votante'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $data['activo'] = $request->boolean('activo');

        $usuario->update($data);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Usuario $usuario)
    {
        $usuario->update(['activo' => false]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario inhabilitado correctamente.');
    }

    public function resetPassword(Usuario $usuario)
    {
        $usuario->update([
            'password' => '12345678',
            'debe_cambiar_password' => true,
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Contraseña restablecida correctamente.');
    }
}
