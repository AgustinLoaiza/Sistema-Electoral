@extends('layouts.app')

@section('title', 'Dashboard Votante')
@section('page_title', 'Dashboard del Votante')
@section('page_subtitle', 'Elecciones habilitadas y resultados publicados')

@section('content')
    <div class="page-header">
        <div>
            <p class="page-kicker">Mis elecciones</p>
            <h2 class="page-title">Participacion electoral</h2>
        </div>
    </div>

    <div class="grid grid-2">
        @forelse($participaciones as $participacion)
            @php($eleccion = $participacion->eleccion)
            @php($chapterLogo = str_contains(strtolower($eleccion->campamento?->nombre ?? ''), 'antonio jose de sucre') ? asset('images/Capitulo Antonio Jose de Sucre.jpeg') : asset('images/Blazon DeMolay.jpeg'))
            <div class="card card-pad chapter-card">
                <div class="chapter-head">
                    <img class="chapter-logo" src="{{ $chapterLogo }}" alt="Logo {{ $eleccion->campamento->nombre }}">
                    <div>
                        <h3 class="chapter-title">{{ $eleccion->titulo }}</h3>
                        <p class="chapter-meta">{{ $eleccion->campamento?->nombre ?? 'Campamento' }}</p>
                    </div>
                </div>

                <div class="stat-row">
                    <div class="stat"><strong>{{ $eleccion->estado }}</strong><span>Estado</span></div>
                    <div class="stat"><strong>{{ $participacion->voto_emitido ? 'Si' : 'No' }}</strong><span>Voto emitido</span></div>
                </div>

                <div class="btn-row">
                    @if($eleccion->estado === \App\Models\Eleccion::ESTADO_ABIERTA && ! $participacion->voto_emitido && $participacion->habilitado)
                        <a class="btn btn-primary" href="{{ route('votos.create', $eleccion) }}">Emitir voto</a>
                    @endif

                    @if($eleccion->resultados_publicados)
                        <a class="btn btn-outline" href="{{ route('resultados.show', $eleccion) }}">Ver resultados</a>
                    @endif
                </div>
            </div>
        @empty
            <div class="card card-pad">No tienes elecciones habilitadas por el momento.</div>
        @endforelse
    </div>
@endsection
