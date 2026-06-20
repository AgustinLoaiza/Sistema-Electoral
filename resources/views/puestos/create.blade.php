@extends('layouts.app')

@section('title', 'Nuevo puesto')
@section('page_title', 'Nuevo puesto')
@section('page_subtitle', 'Crear cargo para elecciones capitulares')

@section('content')
    <div class="card card-pad">
        <form method="POST" action="{{ route('puestos.store') }}" class="form-grid">
            @csrf
            @include('puestos.partials.form', ['puesto' => null])
            <div class="field full btn-row">
                <button class="btn btn-primary" type="submit">Crear puesto</button>
                <a class="btn btn-outline" href="{{ route('puestos.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
