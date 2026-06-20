@extends('layouts.auth')

@section('title', 'Cambiar contrasena')

@section('content')
    <p class="page-kicker">Primer ingreso</p>
    <h2 class="page-title">Cambia tu contrasena</h2>
    <p class="muted">Debes definir una contrasena personal antes de continuar.</p>

    <form method="POST" action="{{ route('password.update') }}" class="grid mt-2">
        @csrf
        @method('PUT')

        <div class="field">
            <label for="password_actual">Contrasena actual</label>
            <input id="password_actual" type="password" name="password_actual" required>
        </div>

        <div class="field">
            <label for="password">Nueva contrasena</label>
            <input id="password" type="password" name="password" required>
        </div>

        <div class="field">
            <label for="password_confirmation">Confirmar nueva contrasena</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required>
        </div>

        <div class="btn-row">
            <button class="btn btn-primary" type="submit">Guardar contrasena</button>
        </div>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-1">
        @csrf
        <button class="btn btn-outline" type="submit">Cerrar sesion</button>
    </form>
@endsection
