<div class="field">
    <label for="campamento_id">Campamento</label>
    <select id="campamento_id" name="campamento_id" required>
        @foreach($campamentos as $campamento)
            <option value="{{ $campamento->id }}" @selected(old('campamento_id', $usuario?->campamento_id) == $campamento->id)>
                {{ $campamento->nombre }}
            </option>
        @endforeach
    </select>
</div>

<div class="field">
    <label for="rol">Rol</label>
    <select id="rol" name="rol" required>
        @foreach(['super_admin' => 'Super admin', 'admin' => 'Administrador', 'votante' => 'Votante'] as $value => $label)
            <option value="{{ $value }}" @selected(old('rol', $usuario?->rol ?? 'votante') === $value)>{{ $label }}</option>
        @endforeach
    </select>
</div>

<div class="field">
    <label for="nombres">Nombres</label>
    <input id="nombres" name="nombres" value="{{ old('nombres', $usuario?->nombres) }}" required>
</div>

<div class="field">
    <label for="apellidos">Apellidos</label>
    <input id="apellidos" name="apellidos" value="{{ old('apellidos', $usuario?->apellidos) }}" required>
</div>

<div class="field">
    <label for="nombre_usuario">Nombre de usuario</label>
    <input id="nombre_usuario" name="nombre_usuario" value="{{ old('nombre_usuario', $usuario?->nombre_usuario) }}" required>
</div>

<label class="check-row">
    <input type="checkbox" name="activo" value="1" @checked(old('activo', $usuario?->activo ?? true))>
    <span>Usuario activo</span>
</label>
