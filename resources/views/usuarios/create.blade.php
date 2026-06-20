@extends('layouts.app')

@section('title', 'Nuevo usuario')
@section('page_title', 'Nuevo usuario')
@section('page_subtitle', 'Registro de usuario habilitable para el sistema')

@section('content')
    <div class="card card-pad">
        <form method="POST" action="{{ route('usuarios.store') }}" class="form-grid">
            @csrf
            @include('usuarios.partials.form', ['usuario' => null])
            <div class="field full">
                <label for="password">Contrasena temporal</label>
                <input id="password" name="password" type="password" required>
            </div>
            <div class="field full btn-row">
                <button class="btn btn-primary" type="submit">Crear usuario</button>
                <a class="btn btn-outline" href="{{ route('usuarios.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
