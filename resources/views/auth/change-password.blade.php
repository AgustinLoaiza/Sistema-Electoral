<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar contraseña</title>
</head>
<body>
    <h1>Cambiar contraseña</h1>

    <p>Debes cambiar tu contraseña antes de continuar.</p>

    @if ($errors->any())
        <div>
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        @method('PUT')

        <div>
            <label for="password_actual">Contraseña actual</label>
            <input
                id="password_actual"
                type="password"
                name="password_actual"
                required
            >
        </div>

        <div>
            <label for="password">Nueva contraseña</label>
            <input
                id="password"
                type="password"
                name="password"
                required
            >
        </div>

        <div>
            <label for="password_confirmation">Confirmar nueva contraseña</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
            >
        </div>

        <button type="submit">Guardar contraseña</button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>
</body>
</html>
