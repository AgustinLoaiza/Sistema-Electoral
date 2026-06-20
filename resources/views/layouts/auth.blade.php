<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Acceso - Sistema Electoral')</title>
    <link rel="stylesheet" href="{{ asset('css/demolay.css') }}?v={{ filemtime(public_path('css/demolay.css')) }}">
</head>
<body>
    <main class="auth-shell">
        <section class="auth-brand">
            <div>
                <img src="{{ asset('images/Logo DeMolay Bolivia.jpeg') }}" alt="Logo DeMolay Bolivia">
                <h1>Sistema Electoral DeMolay</h1>
                <p>Acceso seguro para elecciones capitulares, monitoreo de votacion y publicacion de resultados.</p>
            </div>
            <div class="auth-note">
                <p>Plataforma institucional para campamentos y capitulos DeMolay.</p>
            </div>
        </section>

        <section class="auth-panel">
            <div class="auth-card">
                @if($errors->any())
                    <div class="alert alert-error">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @yield('content')
            </div>
        </section>
    </main>
</body>
</html>
