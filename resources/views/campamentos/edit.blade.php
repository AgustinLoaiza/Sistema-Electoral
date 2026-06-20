@extends('layouts.app')

@section('title', 'Editar campamento')
@section('page_title', 'Editar campamento')
@section('page_subtitle', $campamento->nombre)

@section('content')
    <div class="card card-pad">
        <form method="POST" action="{{ route('campamentos.update', $campamento) }}" class="form-grid">
            @csrf
            @method('PUT')
            @include('campamentos.partials.form', ['campamento' => $campamento])
            <div class="field full btn-row">
                <button class="btn btn-primary" type="submit">Guardar cambios</button>
                <a class="btn btn-outline" href="{{ route('campamentos.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
