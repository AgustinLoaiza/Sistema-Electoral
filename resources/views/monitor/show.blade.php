@extends('layouts.app')

@section('title', 'Monitor en vivo')
@section('page_title', 'Monitor en vivo')
@section('page_subtitle', $eleccion->titulo)

@section('content')
    <div class="grid grid-3">
        <div class="card card-pad">
            <p class="page-kicker">Habilitados</p>
            <h2 class="page-title" id="habilitados">0</h2>
        </div>
        <div class="card card-pad">
            <p class="page-kicker">Votaron</p>
            <h2 class="page-title" id="votaron">0</h2>
        </div>
        <div class="card card-pad">
            <p class="page-kicker">Faltan</p>
            <h2 class="page-title" id="faltan">0</h2>
        </div>
    </div>

    <div class="card card-pad mt-2">
        <div class="btn-row split-row">
            <div>
                <h3 class="page-title">Avance de votacion</h3>
                <p class="muted">Actualizacion automatica cada 5 segundos.</p>
            </div>
            <span class="status {{ $eleccion->estado }}" id="estado">{{ $eleccion->estado }}</span>
        </div>
        <div class="progress mt-2"><span id="barra"></span></div>
        <p class="muted"><strong id="porcentaje">0</strong>% completado</p>
    </div>

    <script>
        async function cargarMonitor() {
            const respuesta = await fetch("{{ route('monitor.data', $eleccion) }}");
            const data = await respuesta.json();

            document.getElementById('habilitados').textContent = data.habilitados;
            document.getElementById('votaron').textContent = data.votaron;
            document.getElementById('faltan').textContent = data.faltan;
            document.getElementById('estado').textContent = data.estado;
            document.getElementById('porcentaje').textContent = data.porcentaje;
            document.getElementById('barra').style.width = data.porcentaje + '%';
        }

        cargarMonitor();
        setInterval(cargarMonitor, 5000);
    </script>
@endsection
