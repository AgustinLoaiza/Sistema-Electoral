@extends('layouts.app')

@section('title', 'Editar eleccion')
@section('page_title', 'Editar eleccion')
@section('page_subtitle', $eleccion->titulo)

@section('content')
    <div class="card card-pad">
        <form method="POST" action="{{ route('elecciones.update', $eleccion) }}" class="form-grid">
            @csrf
            @method('PUT')
            @include('elecciones.partials.form', ['eleccion' => $eleccion])
            <div class="field full btn-row">
                <button class="btn btn-primary" type="submit">Guardar cambios</button>
                <a class="btn btn-outline" href="{{ route('elecciones.show', $eleccion) }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
