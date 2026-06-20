@extends('layouts.app')

@section('title', 'Usuarios')
@section('page_title', 'Gestion de usuarios')
@section('page_subtitle', 'Administracion de accesos, roles y estado de votantes')

@section('content')
    <div class="page-header">
        <div>
            <p class="page-kicker">Usuarios</p>
            <h2 class="page-title">Listado general</h2>
        </div>
        <div class="btn-row">
            <a class="btn btn-outline" href="{{ route('usuarios.export-pdf', request()->query()) }}">Exportar PDF</a>
            <a class="btn btn-primary" href="{{ route('usuarios.create') }}">Nuevo usuario</a>
        </div>
    </div>

    <form method="GET" action="{{ route('usuarios.index') }}" class="card card-pad filter-card mb-2">
        <div class="form-grid">
            <div class="field">
                <label for="q">Buscar usuario</label>
                <input id="q" type="search" name="q" value="{{ request('q') }}" placeholder="Nombre, apellido o usuario">
            </div>

            <div class="field">
                <label for="rol">Filtrar por rol</label>
                <select id="rol" name="rol">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $value => $label)
                        <option value="{{ $value }}" @selected(request('rol') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field align-end">
                <div class="btn-row">
                    <button class="btn btn-primary" type="submit">Filtrar</button>
                    <a class="btn btn-outline" href="{{ route('usuarios.index') }}">Limpiar</a>
                </div>
            </div>
        </div>
    </form>

    <div class="card table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Campamento</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->apellidos }} {{ $usuario->nombres }}</td>
                        <td>{{ $usuario->nombre_usuario }}</td>
                        <td>{{ $usuario->campamento->nombre ?? 'Sin campamento' }}</td>
                        <td><span class="status">{{ $usuario->rol }}</span></td>
                        <td>{{ $usuario->activo ? 'Activo' : 'Inactivo' }}</td>
                        <td>
                            <div class="btn-row">
                                <a class="btn btn-outline" href="{{ route('usuarios.show', $usuario) }}">Ver</a>
                                <a class="btn btn-outline" href="{{ route('usuarios.edit', $usuario) }}">Editar</a>
                                <form method="POST" action="{{ route('usuarios.reset-password', $usuario) }}">
                                    @csrf
                                    <button class="btn btn-outline" type="submit">Reset</button>
                                </form>
                                <form method="POST" action="{{ route('usuarios.destroy', $usuario) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Inhabilitar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">No hay usuarios registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">{{ $usuarios->links() }}</div>
@endsection
