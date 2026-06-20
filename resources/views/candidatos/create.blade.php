@extends('layouts.app')

@section('title', 'Agregar candidato')
@section('page_title', 'Agregar candidato')
@section('page_subtitle', $eleccion->titulo)

@section('content')
    <div class="card card-pad">
        <form method="POST" action="{{ route('candidatos.store', $eleccion) }}" class="form-grid">
            @csrf
            <div class="field">
                <label for="puesto_id">Puesto</label>
                <select id="puesto_id" name="puesto_id" required>
                    @foreach($eleccion->puestos as $puesto)
                        <option value="{{ $puesto->id }}">{{ $puesto->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="usuario_id">Usuario candidato</label>
                <select id="usuario_id" name="usuario_id" required>
                    @foreach($usuarios as $usuario)
                        <option value="{{ $usuario->id }}">{{ $usuario->apellidos }} {{ $usuario->nombres }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field full btn-row">
                <button class="btn btn-primary" type="submit">Agregar candidato</button>
                <a class="btn btn-outline" href="{{ route('elecciones.show', $eleccion) }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
