@extends('layouts.app')

@section('title', 'Detalle de usuario')
@section('page_title', 'Detalle de usuario')
@section('page_subtitle', $usuario->nombre_usuario)

@section('content')
    <div class="card card-pad">
        <div class="grid grid-2">
            <div><strong>Nombre:</strong> {{ $usuario->nombres }} {{ $usuario->apellidos }}</div>
            <div><strong>Usuario:</strong> {{ $usuario->nombre_usuario }}</div>
            <div><strong>Rol:</strong> {{ $usuario->rol }}</div>
            <div><strong>Estado:</strong> {{ $usuario->activo ? 'Activo' : 'Inactivo' }}</div>
            <div><strong>Campamento:</strong> {{ $usuario->campamento->nombre ?? 'Sin campamento' }}</div>
            <div><strong>Ultimo acceso:</strong> {{ $usuario->ultimo_acceso?->format('d/m/Y H:i') ?? 'Sin acceso' }}</div>
        </div>
        <div class="btn-row mt-2">
            <a class="btn btn-primary" href="{{ route('usuarios.edit', $usuario) }}">Editar</a>
            <a class="btn btn-outline" href="{{ route('usuarios.index') }}">Volver</a>
        </div>
    </div>
@endsection
