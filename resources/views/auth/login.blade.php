@extends('layouts.auth')

@section('title', 'Ingreso - Sistema Electoral')

@section('content')
    <p class="page-kicker">Acceso seguro</p>
    <h2 class="page-title">Ingreso al sistema</h2>
    <p class="muted">Usa el nombre de usuario asignado por la administracion electoral.</p>

    <form method="POST" action="{{ route('login.store') }}" class="grid mt-2">
        @csrf

        <div class="field">
            <label for="nombre_usuario">Nombre de usuario</label>
            <input id="nombre_usuario" type="text" name="nombre_usuario" value="{{ old('nombre_usuario') }}" required autofocus>
        </div>

        <div class="field">
            <label for="password">Contrasena</label>
            <div class="password-field">
                <input id="password" type="password" name="password" required>
                <button class="password-toggle" type="button" aria-label="Mostrar contrasena" aria-pressed="false" data-password-toggle>
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                        <path class="eye-slash" d="M4 4l16 16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <label class="check-row">
            <input type="checkbox" name="remember" value="1">
            <span>Recordar sesion</span>
        </label>

        <button class="btn btn-primary" type="submit">Ingresar</button>
    </form>

    <script>
        document.querySelector('[data-password-toggle]')?.addEventListener('click', function () {
            const input = document.getElementById('password');
            const showing = input.type === 'text';

            input.type = showing ? 'password' : 'text';
            this.setAttribute('aria-pressed', String(! showing));
            this.setAttribute('aria-label', showing ? 'Mostrar contrasena' : 'Ocultar contrasena');
            this.classList.toggle('is-active', ! showing);
        });
    </script>
@endsection
