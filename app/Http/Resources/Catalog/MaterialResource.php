<?php

namespace App\Http\Resources\Catalog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'material_type' => $this->material_type,
            'brand' => $this->brand,
            'supplier' => $this->supplier,
            'shore_a' => $this->shore_a,
            'shore_scale' => $this->shore_scale,
            'shore_value' => $this->shore_value,
            'description' => $this->description,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
