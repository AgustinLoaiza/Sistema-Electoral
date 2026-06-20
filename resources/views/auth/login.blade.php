<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ingreso - Sistema Electoral</title>
</head>
<body>
    <h1>Ingreso al Sistema Electoral</h1>

    @if ($errors->any())
        <div>
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <div>
            <label for="nombre_usuario">Nombre de usuario</label>
            <input
                id="nombre_usuario"
                type="text"
                name="nombre_usuario"
                value="{{ old('nombre_usuario') }}"
                required
                autofocus
            >
        </div>

        <div>
            <label for="password">Contraseña</label>
            <input
                id="password"
                type="password"
                name="password"
                required
            >
        </div>

        <div>
            <label>
                <input type="checkbox" name="remember" value="1">
                Recordar sesión
            </label>
        </div>

        <button type="submit">Ingresar</button>
    </form>
</body>
</html>
