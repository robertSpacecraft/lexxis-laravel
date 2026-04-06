<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePrintJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'material_id' => ['sometimes', 'integer', 'exists:materials,id'],
            'technology' => ['sometimes', 'string', 'max:50'],
            'color_name' => ['nullable', 'string', 'max:80'],
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:999'],
            'infill_percent' => ['sometimes', 'integer', Rule::in([5, 15, 40])],
            'scale_percent' => ['sometimes', 'integer', 'min:10', 'max:300'],
        ];
    }
}
