@extends('layouts.app')

@section('title', 'Campamento')
@section('page_title', $campamento->nombre)
@section('page_subtitle', 'Detalle institucional del campamento')

@section('content')
    <div class="page-header">
        <div>
            <p class="page-kicker">Campamento</p>
            <h2 class="page-title">{{ $campamento->nombre }}</h2>
            <p class="muted">{{ $campamento->ciudad ?? 'Sin ciudad' }} · {{ $campamento->numero ?? 'Sin numero' }}</p>
        </div>
        <div class="btn-row">
            <a class="btn btn-outline" href="{{ route('campamentos.edit', $campamento) }}">Editar</a>
            <a class="btn btn-outline" href="{{ route('campamentos.index') }}">Volver</a>
        </div>
    </div>

    <div class="grid grid-2 mb-2">
        <div class="card card-pad">
            <p class="page-kicker">Resumen</p>
            <div class="stat-row">
                <div class="stat">
                    <strong>{{ $campamento->usuarios_count }}</strong>
                    <span>Usuarios</span>
                </div>
                <div class="stat">
                    <strong>{{ $campamento->elecciones_count }}</strong>
                    <span>Elecciones</span>
                </div>
                <div class="stat">
                    <strong>{{ $campamento->activo ? 'Si' : 'No' }}</strong>
                    <span>Activo</span>
                </div>
            </div>
        </div>

        <div class="card card-pad">
            <p class="page-kicker">Uso</p>
            <p class="muted">Este campamento estara disponible al crear usuarios y nuevas elecciones mientras se mantenga activo.</p>
        </div>
    </div>

    <div class="grid grid-2">
        <div class="card table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ultimas elecciones</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campamento->elecciones as $eleccion)
                        <tr>
                            <td>{{ $eleccion->titulo }}</td>
                            <td><span class="status {{ $eleccion->estado }}">{{ $eleccion->estado }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="2">No hay elecciones registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Usuarios recientes</th>
                        <th>Rol</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campamento->usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->apellidos }} {{ $usuario->nombres }}</td>
                            <td><span class="status">{{ $usuario->rol }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="2">No hay usuarios registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
