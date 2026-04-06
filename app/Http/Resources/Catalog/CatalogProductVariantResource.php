<?php

namespace App\Http\Resources\Catalog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CatalogProductVariantResource extends JsonResource
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
            'product_id' => $this->product_id,
            'material_id' => $this->material_id,
            'sku' => $this->sku,
            'size_eu' => $this->size_eu,
            'color_name' => $this->color_name,
            'price' => $this->price,
            'stock' => $this->stock,
            'is_active' => (bool) $this->is_active,
            'created_at' => optional($this->created_at)?->toISOString(),
            'updated_at' => optional($this->updated_at)?->toISOString(),

            'material' => $this->whenLoaded('material', fn () => new MaterialResource($this->material)),
            'main_image' => $this->whenLoaded('mainImage', fn () => $this->mainImage
                ? new ProductVariantImageResource($this->mainImage)
                : null),
            'images' => $this->whenLoaded('images', fn () => ProductVariantImageResource::collection($this->images)),
        ];
    }
}
