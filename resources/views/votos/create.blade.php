@extends('layouts.app')

@section('title', 'Emitir voto')
@section('page_title', 'Emitir voto')
@section('page_subtitle', $eleccion->titulo)

@section('content')
    <form method="POST" action="{{ route('votos.store', $eleccion) }}" class="grid">
        @csrf

        @foreach($eleccion->puestos as $index => $puesto)
            @php($candidatos = $eleccion->candidatos->where('puesto_id', $puesto->id)->where('estado', \App\Models\Candidato::ESTADO_APROBADO))
            <div class="card card-pad vote-group">
                <p class="page-kicker">Puesto {{ $index + 1 }}</p>
                <h3 class="page-title">{{ $puesto->nombre }}</h3>

                <input type="hidden" name="votos[{{ $index }}][puesto_id]" value="{{ $puesto->id }}">
                <div class="candidate-list mt-1">
                    @foreach($candidatos as $candidato)
                        <label class="candidate-option">
                            <input type="radio" name="votos[{{ $index }}][seleccion]" value="candidato:{{ $candidato->id }}" required>
                            <span>{{ $candidato->usuario->nombres }} {{ $candidato->usuario->apellidos }}</span>
                        </label>
                    @endforeach

                    <label class="candidate-option">
                        <input type="radio" name="votos[{{ $index }}][seleccion]" value="blanco" required>
                        <span>Voto blanco</span>
                    </label>

                    <label class="candidate-option">
                        <input type="radio" name="votos[{{ $index }}][seleccion]" value="nulo" required>
                        <span>Voto nulo</span>
                    </label>
                </div>
            </div>
        @endforeach

        <div class="card card-pad">
            <div class="btn-row">
                <button class="btn btn-primary" type="submit">Registrar voto</button>
                <a class="btn btn-outline" href="{{ route('votante.dashboard') }}">Cancelar</a>
            </div>
        </div>
    </form>
@endsection
