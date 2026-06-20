@extends('layouts.app')

@section('title', 'Resultados')
@section('page_title', 'Resultados electorales')
@section('page_subtitle', $eleccion->titulo)

@section('content')
    @php($chapterLogo = str_contains(strtolower($eleccion->campamento->nombre ?? ''), 'antonio jose de sucre') ? asset('images/Capitulo Antonio Jose de Sucre.jpeg') : asset('images/Blazon DeMolay.jpeg'))
    <div class="card card-pad mb-2">
        <div class="chapter-head">
            <img class="chapter-logo" src="{{ $chapterLogo }}" alt="Logo del campamento">
            <div>
                <h2 class="page-title">{{ $eleccion->titulo }}</h2>
                <p class="chapter-meta">{{ $eleccion->campamento->nombre ?? 'Campamento' }} &middot; Estado {{ $eleccion->estado }}</p>
            </div>
            <div class="btn-row">
                <a class="btn btn-outline" href="{{ route('resultados.export-pdf', $eleccion) }}">Exportar PDF</a>
            </div>
        </div>
    </div>

    <div class="results-stack mb-2">
        @forelse($resultadosPorPuesto as $grupo)
            <section class="card card-pad result-card">
                <div class="result-card-head">
                    <div>
                        <p class="page-kicker">Puesto</p>
                        <h3 class="result-title">{{ $grupo['puesto']->nombre }}</h3>
                    </div>
                    <div class="result-total">
                        <strong>{{ $grupo['total'] }}</strong>
                        <span>votos</span>
                    </div>
                </div>

                <div class="bar-chart" aria-label="Resultados para {{ $grupo['puesto']->nombre }}">
                    @foreach($grupo['opciones'] as $opcion)
                        <div class="bar-row">
                            <div class="bar-label">
                                <strong>{{ $opcion['nombre'] }}</strong>
                                <span>{{ ucfirst($opcion['tipo']) }}</span>
                            </div>
                            <div class="bar-track" aria-hidden="true">
                                <span class="bar-fill {{ $opcion['tipo'] }}" style="--bar-width: {{ $opcion['porcentaje'] }}%;"></span>
                            </div>
                            <div class="bar-value">
                                <strong>{{ number_format($opcion['porcentaje'], 1, ',', '.') }}%</strong>
                                <span>{{ $opcion['total'] }} votos</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="card card-pad">
                <p class="muted">Todavia no hay puestos configurados para esta eleccion.</p>
            </div>
        @endforelse
    </div>

    <div class="card table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Puesto</th>
                    <th>Opcion</th>
                    <th>Tipo</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($resultados as $resultado)
                    <tr>
                        <td>{{ $resultado->puesto->nombre ?? 'Sin puesto' }}</td>
                        <td>
                            @if($resultado->tipo_voto === \App\Models\Voto::TIPO_CANDIDATO)
                                {{ $resultado->candidato?->usuario?->nombres }} {{ $resultado->candidato?->usuario?->apellidos }}
                            @else
                                {{ ucfirst($resultado->tipo_voto) }}
                            @endif
                        </td>
                        <td><span class="status">{{ $resultado->tipo_voto }}</span></td>
                        <td><strong>{{ $resultado->total }}</strong></td>
                    </tr>
                @empty
                    <tr><td colspan="4">Todavia no hay votos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
