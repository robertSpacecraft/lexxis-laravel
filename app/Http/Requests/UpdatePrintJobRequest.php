<?php

namespace App\Http\Requests;

use App\Enums\PrintJobStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePrintJobRequest extends FormRequest
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
            'material_id' => ['sometimes', 'integer', 'exists:materials,id'],

            'technology' => ['sometimes', 'string', 'max:50'],
            'color_name' => ['nullable', 'string', 'max:80'],
            'quantity' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
