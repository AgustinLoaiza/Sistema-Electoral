@extends('layouts.app')

@section('title', 'Nuevo campamento')
@section('page_title', 'Nuevo campamento')
@section('page_subtitle', 'Registrar campamento o capitulo para procesos electorales')

@section('content')
    <div class="card card-pad">
        <form method="POST" action="{{ route('campamentos.store') }}" class="form-grid">
            @csrf
            @include('campamentos.partials.form', ['campamento' => null])
            <div class="field full btn-row">
                <button class="btn btn-primary" type="submit">Crear campamento</button>
                <a class="btn btn-outline" href="{{ route('campamentos.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
