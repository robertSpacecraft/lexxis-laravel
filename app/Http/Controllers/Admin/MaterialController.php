<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMaterialRequest;
use App\Http\Requests\UpdateMaterialRequest;
use App\Models\Material;
use Illuminate\Support\Str;

class MaterialController extends Controller
{
    //CRUD
    public function index(){
        $materials = Material::query()
            ->latest()
            ->paginate(15);
        return view('admin.materials.index', compact('materials'));
    }

    public function create(){
        return view('admin.materials.create');
    }

    public function store(StoreMaterialRequest $request){
        $validated = $request->validated();

        $validated['slug'] = $this->generateUniqueSlug($validated['name']);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        Material::create($validated);

        return redirect()
            ->route('admin.materials.index')
            ->with('status', 'Material creado correctamente');
    }

    public function edit(Material $material){
        return view('admin.materials.edit', compact('material'));
    }

    public function update(UpdateMaterialRequest $request, Material $material){
        $validated = $request->validated();
        $validated['slug'] = $this->generateUniqueSlug($validated['name'], $material->id);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        $material->update($validated);

        return redirect()
            ->route('admin.materials.index')
            ->with('status', 'Material actualizado correctamente');
    }

    //Función para comprobar si ya existe el slug de un material y le añade un sufijo si es necesario
    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 2;

        while (
        Material::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}

