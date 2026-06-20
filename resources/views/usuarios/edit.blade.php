@extends('layouts.app')

@section('title', 'Editar usuario')
@section('page_title', 'Editar usuario')
@section('page_subtitle', $usuario->nombres . ' ' . $usuario->apellidos)

@section('content')
    <div class="card card-pad">
        <form method="POST" action="{{ route('usuarios.update', $usuario) }}" class="form-grid">
            @csrf
            @method('PUT')
            @include('usuarios.partials.form', ['usuario' => $usuario])
            <div class="field full btn-row">
                <button class="btn btn-primary" type="submit">Guardar cambios</button>
                <a class="btn btn-outline" href="{{ route('usuarios.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
