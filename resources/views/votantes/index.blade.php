@extends('layouts.app')

@section('title', 'Votantes')
@section('page_title', 'Votantes habilitados')
@section('page_subtitle', $eleccion->titulo)

@section('content')
    @if($eleccion->estado === \App\Models\Eleccion::ESTADO_BORRADOR)
        <div class="card card-pad mb-2">
            <form method="POST" action="{{ route('votantes.store', $eleccion) }}" class="form-grid">
                @csrf
                <div class="field">
                    <label for="usuario_id">Agregar votante</label>
                    <select id="usuario_id" name="usuario_id" required>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->apellidos }} {{ $usuario->nombres }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field align-end">
                    <button class="btn btn-primary" type="submit">Habilitar</button>
                </div>
            </form>
        </div>
    @endif

    <div class="card table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Votante</th>
                    <th>Habilitado</th>
                    <th>Voto emitido</th>
                    <th>Fecha voto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($votantes as $votante)
                    <tr>
                        <td>{{ $votante->usuario->apellidos }} {{ $votante->usuario->nombres }}</td>
                        <td>{{ $votante->habilitado ? 'Si' : 'No' }}</td>
                        <td>{{ $votante->voto_emitido ? 'Si' : 'No' }}</td>
                        <td>{{ $votante->fecha_voto?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td>
                            <div class="btn-row">
                                <form method="POST" action="{{ route('votantes.habilitar', $votante) }}">
                                    @csrf
                                    <button class="btn btn-outline" type="submit">Habilitar</button>
                                </form>
                                <form method="POST" action="{{ route('votantes.inhabilitar', $votante) }}">
                                    @csrf
                                    <button class="btn btn-danger" type="submit">Inhabilitar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">No hay votantes habilitados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">{{ $votantes->links() }}</div>
@endsection
