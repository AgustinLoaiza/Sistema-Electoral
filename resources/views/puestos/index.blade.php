@extends('layouts.app')

@section('title', 'Puestos')
@section('page_title', 'Puestos electorales')
@section('page_subtitle', 'Cargos disponibles para configurar elecciones')

@section('content')
    <div class="page-header">
        <div>
            <p class="page-kicker">Puestos</p>
            <h2 class="page-title">Cargos registrados</h2>
        </div>
        <a class="btn btn-primary" href="{{ route('puestos.create') }}">Nuevo puesto</a>
    </div>

    <div class="card table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Orden</th>
                    <th>Nombre</th>
                    <th>Descripcion</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($puestos as $puesto)
                    <tr>
                        <td>{{ $puesto->orden }}</td>
                        <td>{{ $puesto->nombre }}</td>
                        <td>{{ $puesto->descripcion ?? 'Sin descripcion' }}</td>
                        <td>{{ $puesto->activo ? 'Activo' : 'Inactivo' }}</td>
                        <td>
                            <div class="btn-row">
                                <a class="btn btn-outline" href="{{ route('puestos.edit', $puesto) }}">Editar</a>
                                <form method="POST" action="{{ route('puestos.destroy', $puesto) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Inhabilitar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">No hay puestos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $puestos->links() }}</div>
@endsection
