<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductDesignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'material_id' => ['required', 'integer', 'exists:materials,id'],
            'color_name' => ['required', 'string', 'max:50'],
            'size_eu' => ['required', 'numeric', 'min:1'],
        ];
    }
}
