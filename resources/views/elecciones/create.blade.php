@extends('layouts.app')

@section('title', 'Nueva eleccion')
@section('page_title', 'Nueva eleccion')
@section('page_subtitle', 'Configura el campamento y los puestos habilitados')

@section('content')
    <div class="card card-pad">
        <form method="POST" action="{{ route('elecciones.store') }}" class="form-grid">
            @csrf
            @include('elecciones.partials.form', ['eleccion' => null])
            <div class="field full btn-row">
                <button class="btn btn-primary" type="submit">Crear eleccion</button>
                <a class="btn btn-outline" href="{{ route('elecciones.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
