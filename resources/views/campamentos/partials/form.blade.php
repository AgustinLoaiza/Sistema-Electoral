<div class="field">
    <label for="nombre">Nombre del campamento o capitulo</label>
    <input id="nombre" name="nombre" value="{{ old('nombre', $campamento?->nombre) }}" required>
</div>

<div class="field">
    <label for="numero">Numero</label>
    <input id="numero" name="numero" value="{{ old('numero', $campamento?->numero) }}" placeholder="Ej. 75019">
</div>

<div class="field full">
    <label for="ciudad">Ciudad</label>
    <input id="ciudad" name="ciudad" value="{{ old('ciudad', $campamento?->ciudad) }}" placeholder="Ej. Sucre">
</div>

<label class="check-row">
    <input type="checkbox" name="activo" value="1" @checked(old('activo', $campamento?->activo ?? true))>
    <span>Campamento activo</span>
</label>
