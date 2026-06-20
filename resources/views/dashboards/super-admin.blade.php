@extends('layouts.app')

@section('title', 'Dashboard Super Admin')
@section('page_title', 'Dashboard Super Administrador')
@section('page_subtitle', 'Vista general de campamentos, elecciones y gestion del sistema')

@section('content')
    <div class="page-header">
        <div>
            <p class="page-kicker">Campamentos</p>
            <h2 class="page-title">Elecciones por campamento</h2>
        </div>
        <div class="btn-row">
            <a class="btn btn-primary" href="{{ route('elecciones.create') }}">Nueva eleccion</a>
            <a class="btn btn-outline" href="{{ route('usuarios.index') }}">Gestionar usuarios</a>
        </div>
    </div>

    <div class="grid grid-3">
        @forelse($campamentos as $campamento)
            @php($chapterLogo = str_contains(strtolower($campamento->nombre), 'antonio jose de sucre') ? asset('images/Capitulo Antonio Jose de Sucre.jpeg') : asset('images/Blazon DeMolay.jpeg'))
            <a class="card card-pad chapter-card" href="{{ route('elecciones.index') }}">
                <div class="chapter-head">
                    <img class="chapter-logo" src="{{ $chapterLogo }}" alt="Logo {{ $campamento->nombre }}">
                    <div>
                        <h3 class="chapter-title">{{ $campamento->nombre }}</h3>
                        <p class="chapter-meta">No. {{ $campamento->numero ?? 'Sin numero' }} · {{ $campamento->ciudad ?? 'Sin ciudad' }}</p>
                    </div>
                </div>
                <div class="stat-row">
                    <div class="stat"><strong>{{ $campamento->elecciones_count }}</strong><span>Elecciones</span></div>
                    <div class="stat"><strong>{{ $campamento->usuarios_count }}</strong><span>Usuarios</span></div>
                </div>
            </a>
        @empty
            <div class="card card-pad">No hay campamentos registrados.</div>
        @endforelse
    </div>

    <div class="page-header mt-2">
        <div>
            <p class="page-kicker">Actividad reciente</p>
            <h2 class="page-title">Ultimas elecciones</h2>
        </div>
    </div>

    <div class="grid grid-2">
        @foreach($elecciones as $eleccion)
            <div class="card card-pad">
                <div class="btn-row split-row">
                    <strong>{{ $eleccion->titulo }}</strong>
                    <span class="status {{ $eleccion->estado }}">{{ $eleccion->estado }}</span>
                </div>
                <p class="muted">{{ $eleccion->campamento->nombre }}</p>
                <div class="btn-row">
                    <a class="btn btn-outline" href="{{ route('elecciones.show', $eleccion) }}">Ver detalle</a>
                    <a class="btn btn-dark" href="{{ route('monitor.show', $eleccion) }}">Monitor</a>
                </div>
            </div>
        @endforeach
    </div>
@endsection
