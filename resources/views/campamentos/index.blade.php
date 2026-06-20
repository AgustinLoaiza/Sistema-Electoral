@extends('layouts.app')

@section('title', 'Campamentos')
@section('page_title', 'Campamentos')
@section('page_subtitle', 'Gestion institucional para crear elecciones por campamento')

@section('content')
    <div class="page-header">
        <div>
            <p class="page-kicker">Institucion</p>
            <h2 class="page-title">Campamentos registrados</h2>
        </div>
        <a class="btn btn-primary" href="{{ route('campamentos.create') }}">Nuevo campamento</a>
    </div>

    <div class="card table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Numero</th>
                    <th>Ciudad</th>
                    <th>Usuarios</th>
                    <th>Elecciones</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($campamentos as $campamento)
                    <tr>
                        <td>{{ $campamento->nombre }}</td>
                        <td>{{ $campamento->numero ?? 'Sin numero' }}</td>
                        <td>{{ $campamento->ciudad ?? 'Sin ciudad' }}</td>
                        <td>{{ $campamento->usuarios_count }}</td>
                        <td>{{ $campamento->elecciones_count }}</td>
                        <td>{{ $campamento->activo ? 'Activo' : 'Inactivo' }}</td>
                        <td>
                            <div class="btn-row">
                                <a class="btn btn-outline" href="{{ route('campamentos.show', $campamento) }}">Ver</a>
                                <a class="btn btn-outline" href="{{ route('campamentos.edit', $campamento) }}">Editar</a>
                                <form method="POST" action="{{ route('campamentos.destroy', $campamento) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Inhabilitar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">No hay campamentos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">{{ $campamentos->links() }}</div>
@endsection
