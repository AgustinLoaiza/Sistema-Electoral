@extends('layouts.app')

@section('title', 'Editar puesto')
@section('page_title', 'Editar puesto')
@section('page_subtitle', $puesto->nombre)

@section('content')
    <div class="card card-pad">
        <form method="POST" action="{{ route('puestos.update', $puesto) }}" class="form-grid">
            @csrf
            @method('PUT')
            @include('puestos.partials.form', ['puesto' => $puesto])
            <div class="field full btn-row">
                <button class="btn btn-primary" type="submit">Guardar cambios</button>
                <a class="btn btn-outline" href="{{ route('puestos.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
