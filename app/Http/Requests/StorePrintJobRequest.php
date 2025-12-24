<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Enums\PrintJobStatus;
use Illuminate\Validation\Rule;

class StorePrintJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'material_id' => ['required', 'integer', 'exists:materials,id'],

            'technology' => ['required', 'string', 'max:50'],
            'color_name' => ['nullable', 'string', 'max:80'],
            'quantity' => ['required', 'integer', 'min:1'],

            'estimated_material_g' => ['nullable', 'numeric', 'min:0'],
            'estimated_time_min' => ['nullable', 'integer', 'min:0'],

            'unit_price' => ['required', 'numeric', 'min:0'],
            'pricing_breakdown' => ['nullable', 'array'],

            'status' => ['required', Rule::in(PrintJobStatus::values())],
        ];
    }
}
