<div class="field">
    <label for="campamento_id">Campamento</label>
    <select id="campamento_id" name="campamento_id" required>
        @foreach($campamentos as $campamento)
            <option value="{{ $campamento->id }}" @selected(old('campamento_id', $eleccion?->campamento_id) == $campamento->id)>
                {{ $campamento->nombre }}
            </option>
        @endforeach
    </select>
</div>

<div class="field">
    <label for="titulo">Titulo</label>
    <input id="titulo" name="titulo" value="{{ old('titulo', $eleccion?->titulo) }}" required>
</div>

<div class="field full">
    <label for="descripcion">Descripcion</label>
    <textarea id="descripcion" name="descripcion">{{ old('descripcion', $eleccion?->descripcion) }}</textarea>
</div>

@if(! $eleccion)
    <div class="field full">
        <label>Puestos incluidos</label>
        <div class="grid grid-2">
            @foreach($puestos as $puesto)
                <label class="candidate-option">
                    <input type="checkbox" name="puestos[]" value="{{ $puesto->id }}" @checked(in_array($puesto->id, old('puestos', [])))>
                    <span>{{ $puesto->nombre }}</span>
                </label>
            @endforeach
        </div>
    </div>
@endif
