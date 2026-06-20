<div class="field">
    <label for="nombre">Nombre</label>
    <input id="nombre" name="nombre" value="{{ old('nombre', $puesto?->nombre) }}" required>
</div>

<div class="field">
    <label for="orden">Orden</label>
    <input id="orden" name="orden" type="number" min="0" value="{{ old('orden', $puesto?->orden ?? 0) }}" required>
</div>

<div class="field full">
    <label for="descripcion">Descripcion</label>
    <textarea id="descripcion" name="descripcion">{{ old('descripcion', $puesto?->descripcion) }}</textarea>
</div>

<label class="check-row">
    <input type="checkbox" name="activo" value="1" @checked(old('activo', $puesto?->activo ?? true))>
    <span>Puesto activo</span>
</label>
