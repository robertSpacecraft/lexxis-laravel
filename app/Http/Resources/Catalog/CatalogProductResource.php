<?php

namespace App\Http\Resources\Catalog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CatalogProductResource extends JsonResource
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
            'short_description' => $this->short_description,
            'description' => $this->description,
            'type' => $this->type,
            'base_price' => $this->base_price,
            'is_active' => (bool) $this->is_active,
            'created_at' => optional($this->created_at)?->toISOString(),
            'updated_at' => optional($this->updated_at)?->toISOString(),

            'main_image' => $this->whenLoaded('mainImage', fn () => $this->mainImage
                ? new ProductImageResource($this->mainImage)
                : null),
            'images' => $this->whenLoaded('images', fn () => ProductImageResource::collection($this->images)),
            'variants' => $this->whenLoaded('variants', fn () => CatalogProductVariantResource::collection($this->variants)),
        ];
    }
}
