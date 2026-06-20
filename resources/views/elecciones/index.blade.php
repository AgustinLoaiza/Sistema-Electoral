@extends('layouts.app')

@section('title', 'Elecciones')
@section('page_title', 'Elecciones')
@section('page_subtitle', 'Procesos electorales registrados por campamento')

@section('content')
    <div class="page-header">
        <div>
            <p class="page-kicker">Procesos</p>
            <h2 class="page-title">Listado de elecciones</h2>
        </div>
        <div class="btn-row">
            <a class="btn btn-outline" href="{{ route('elecciones.export-pdf') }}">Exportar PDF</a>
            <a class="btn btn-primary" href="{{ route('elecciones.create') }}">Nueva eleccion</a>
        </div>
    </div>

    <div class="grid grid-2">
        @forelse($elecciones as $eleccion)
            <div class="card card-pad chapter-card">
                <div class="btn-row split-row">
                    <div>
                        <h3 class="chapter-title">{{ $eleccion->titulo }}</h3>
                        <p class="chapter-meta">{{ $eleccion->campamento->nombre ?? 'Sin campamento' }}</p>
                    </div>
                    <span class="status {{ $eleccion->estado }}">{{ $eleccion->estado }}</span>
                </div>
                <p class="muted">{{ $eleccion->descripcion ?? 'Sin descripcion' }}</p>
                <div class="btn-row">
                    <a class="btn btn-primary" href="{{ route('elecciones.show', $eleccion) }}">Ingresar</a>
                    <a class="btn btn-outline" href="{{ route('monitor.show', $eleccion) }}">Monitor</a>
                    <a class="btn btn-outline" href="{{ route('resultados.show', $eleccion) }}">Resultados</a>
                </div>
            </div>
        @empty
            <div class="card card-pad">No hay elecciones registradas.</div>
        @endforelse
    </div>

    <div class="pagination">{{ $elecciones->links() }}</div>
@endsection
