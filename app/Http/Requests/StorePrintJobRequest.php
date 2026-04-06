<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePrintJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'material_id' => ['required', 'integer', 'exists:materials,id'],
            'technology' => ['required', 'string', 'max:50'],
            'color_name' => ['nullable', 'string', 'max:80'],
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'infill_percent' => ['required', 'integer', Rule::in([5, 15, 40])],
            'scale_percent' => ['required', 'integer', 'min:10', 'max:300'],
        ];
    }
}
