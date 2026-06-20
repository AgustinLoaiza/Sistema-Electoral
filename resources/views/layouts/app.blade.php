<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema Electoral DeMolay')</title>
    <link rel="stylesheet" href="{{ asset('css/demolay.css') }}?v={{ filemtime(public_path('css/demolay.css')) }}">
</head>
<body>
    @php($usuario = auth()->user())
    <div class="app-shell">
        <aside class="sidebar">
            <div class="sidebar-banner"></div>
            <div class="brand-block">
                <div class="brand-row">
                    <img src="{{ asset('images/Blazon DeMolay.jpeg') }}" alt="Blazon DeMolay">
                    <div>
                        <p class="brand-title">Sistema Electoral</p>
                        <p class="brand-subtitle">Orden DeMolay Bolivia</p>
                    </div>
                </div>
            </div>

            <nav class="nav-section">
                <p class="nav-label">Principal</p>
                <a class="nav-link" href="{{ route('dashboard') }}">Dashboard <span>&rsaquo;</span></a>

                @if($usuario && in_array($usuario->rol, ['super_admin', 'admin'], true))
                    <p class="nav-label">Gestion</p>
                    <a class="nav-link" href="{{ route('elecciones.index') }}">Elecciones <span>&rsaquo;</span></a>
                    <a class="nav-link" href="{{ route('usuarios.index') }}">Usuarios <span>&rsaquo;</span></a>
                    <a class="nav-link" href="{{ route('puestos.index') }}">Puestos <span>&rsaquo;</span></a>
                @endif

                <p class="nav-label">Sesion</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="nav-button" type="submit">Cerrar sesion <span>&rsaquo;</span></button>
                </form>
            </nav>
        </aside>

        <main class="main">
            <header class="topbar">
                <div>
                    <h1>@yield('page_title', 'Sistema Electoral')</h1>
                    <p>@yield('page_subtitle', 'Gestion de elecciones capitulares')</p>
                </div>
                <div class="brand-row">
                    <img src="{{ asset('images/Logo DeMolay Bolivia.jpeg') }}" alt="Logo DeMolay Bolivia">
                    <div>
                        <strong>{{ $usuario?->nombres }} {{ $usuario?->apellidos }}</strong>
                        <p>{{ $usuario?->rol }}</p>
                    </div>
                </div>
            </header>

            <section class="content">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @yield('content')
            </section>
        </main>
    </div>
</body>
</html>
