@extends('layouts.app')

@section('title', 'Detalle de eleccion')
@section('page_title', $eleccion->titulo)
@section('page_subtitle', $eleccion->campamento->nombre ?? 'Eleccion capitular')

@section('content')
    <div class="grid grid-3">
        <div class="card card-pad">
            <p class="page-kicker">Estado</p>
            <h3 class="page-title"><span class="status {{ $eleccion->estado }}">{{ $eleccion->estado }}</span></h3>
            <p class="muted">Inicio: {{ $eleccion->fecha_inicio?->format('d/m/Y H:i') ?? 'Sin iniciar' }}</p>
            <p class="muted">Fin: {{ $eleccion->fecha_fin?->format('d/m/Y H:i') ?? 'Sin cierre' }}</p>
        </div>
        <div class="card card-pad">
            <p class="page-kicker">Puestos</p>
            <h3 class="page-title">{{ $eleccion->puestos->count() }}</h3>
            <p class="muted">Cargos incluidos en esta eleccion.</p>
        </div>
        <div class="card card-pad">
            <p class="page-kicker">Votantes</p>
            <h3 class="page-title">{{ $eleccion->votantesEleccion->count() }}</h3>
            <p class="muted">Usuarios habilitados o registrados.</p>
        </div>
    </div>

    <div class="card card-pad mt-2">
        <div class="btn-row">
            @if($eleccion->estado === \App\Models\Eleccion::ESTADO_BORRADOR)
                <a class="btn btn-outline" href="{{ route('elecciones.edit', $eleccion) }}">Editar</a>
                <a class="btn btn-outline" href="{{ route('candidatos.create', $eleccion) }}">Agregar candidato</a>
                <a class="btn btn-outline" href="{{ route('votantes.index', $eleccion) }}">Gestionar votantes</a>
                <form method="POST" action="{{ route('elecciones.abrir', $eleccion) }}">
                    @csrf
                    <button class="btn btn-primary" type="submit">Abrir eleccion</button>
                </form>
            @endif

            @if($eleccion->estado === \App\Models\Eleccion::ESTADO_ABIERTA)
                <a class="btn btn-dark" href="{{ route('monitor.show', $eleccion) }}">Monitor en vivo</a>
                <form method="POST" action="{{ route('elecciones.cerrar', $eleccion) }}">
                    @csrf
                    <button class="btn btn-danger" type="submit">Cerrar eleccion</button>
                </form>
            @endif

            @if($eleccion->estado === \App\Models\Eleccion::ESTADO_CERRADA)
                <a class="btn btn-outline" href="{{ route('resultados.show', $eleccion) }}">Ver resultados</a>
                <form method="POST" action="{{ route('elecciones.publicar', $eleccion) }}">
                    @csrf
                    <button class="btn btn-primary" type="submit">Publicar resultados</button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-2 mt-2">
        <div class="card card-pad">
            <h3>Puestos</h3>
            <div class="table-wrap">
                <table class="table">
                    <tbody>
                        @foreach($eleccion->puestos as $puesto)
                            <tr><td>{{ $puesto->pivot->orden }}</td><td>{{ $puesto->nombre }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card card-pad">
            <h3>Candidatos</h3>
            <div class="table-wrap">
                <table class="table">
                    <tbody>
                        @foreach($eleccion->candidatos as $candidato)
                            <tr>
                                <td>{{ $candidato->usuario->nombres }} {{ $candidato->usuario->apellidos }}</td>
                                <td>{{ $candidato->puesto->nombre }}</td>
                                <td>{{ $candidato->estado }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
