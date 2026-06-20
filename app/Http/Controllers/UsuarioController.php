<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campamento;
use App\Models\Usuario;
use App\Support\PdfReport;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $usuarios = $this->usuariosQuery($request)->paginate(15)->withQueryString();
        $roles = [
            Usuario::ROL_SUPER_ADMIN => 'Super admin',
            Usuario::ROL_ADMIN => 'Admin',
            Usuario::ROL_VOTANTE => 'Votante',
        ];

        return view('usuarios.index', compact('usuarios', 'roles'));
    }

    public function exportPdf(Request $request)
    {
        $usuarios = $this->usuariosQuery($request)->get();

        $rows = $usuarios->map(fn (Usuario $usuario) => [
            trim($usuario->apellidos . ' ' . $usuario->nombres),
            $usuario->nombre_usuario,
            $usuario->campamento->nombre ?? 'Sin campamento',
            $usuario->rol,
            $usuario->activo ? 'Activo' : 'Inactivo',
        ]);

        $pdf = PdfReport::make(
            'Reporte de usuarios',
            'Filtros: busqueda=' . ($request->query('q') ?: 'todos') . ', rol=' . ($request->query('rol') ?: 'todos'),
            ['Nombre', 'Usuario', 'Campamento', 'Rol', 'Estado'],
            $rows
        );

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="usuarios.pdf"',
            'Cache-Control' => 'no-store',
        ]);
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
            'nombre_usuario' => ['required', 'string', 'max:255', 'unique:usuarios,nombre_usuario,' . $usuario->id],
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

        return redirect()->route('usuarios.index')->with('success', 'Password restablecida correctamente.');
    }

    private function usuariosQuery(Request $request)
    {
        $query = Usuario::with('campamento')
            ->orderBy('apellidos')
            ->orderBy('nombres');

        $search = trim((string) $request->query('q', ''));

        if ($search !== '') {
            $term = '%' . strtolower($search) . '%';

            $query->where(function ($subquery) use ($term) {
                $subquery
                    ->whereRaw('LOWER(nombres) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(apellidos) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(nombre_usuario) LIKE ?', [$term]);
            });
        }

        $rol = $request->query('rol');

        if (in_array($rol, [Usuario::ROL_SUPER_ADMIN, Usuario::ROL_ADMIN, Usuario::ROL_VOTANTE], true)) {
            $query->where('rol', $rol);
        }

        return $query;
    }
}
