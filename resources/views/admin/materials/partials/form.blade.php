<div>
    <label class="block text-sm font-medium">Nombre</label>
    <input type="text" name="name"
           value="{{ old('name', $material->name ?? '') }}"
           class="w-full border p-2">
</div>

<div>
    <label class="block text-sm font-medium">Tipo de material</label>
    <input type="text" name="material_type"
           value="{{ old('material_type', $material->material_type ?? '') }}"
           class="w-full border p-2">
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Escala Shore</label>
        <select name="shore_scale" class="w-full border p-2">
            <option value="">—</option>
            @foreach(['00','A','D'] as $scale)
                <option value="{{ $scale }}"
                    @selected(old('shore_scale', $material->shore_scale ?? '') === $scale)>
                    {{ $scale }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium">Valor Shore</label>
        <input type="number" name="shore_value"
               value="{{ old('shore_value', $material->shore_value ?? '') }}"
               class="w-full border p-2">
    </div>
</div>

<div>
    <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1"
            @checked(old('is_active', $material->is_active ?? true))>
        Activo
    </label>
</div>
