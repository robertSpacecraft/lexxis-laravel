<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'material_type' => ['required', 'string', 'max:50'],
            'brand' => ['nullable', 'string', 'max:100'],
            'supplier' => ['nullable', 'string', 'max:100'],

            //El shore es la escala de dureza y se basa en la escala y el valor
            'shore_scale' => ['nullable', 'in:00,A,D', 'required_with:shore_value'],
            'shore_value' => ['nullable', 'integer', 'min:0', 'max:100', 'required_with:shore_scale'],

            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
