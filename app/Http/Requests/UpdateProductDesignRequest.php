<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductDesignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'material_id' => ['sometimes', 'integer', 'exists:materials,id'],
            'color_name' => ['sometimes', 'string', 'max:50'],
            'size_eu' => ['sometimes', 'numeric', 'min:1'],
        ];
    }
}
