<?php

namespace App\Http\Requests;

use App\Support\PrintJobOptions;
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
            'technology' => ['sometimes', 'string', 'max:50', Rule::in(PrintJobOptions::technologies())],
            'color_name' => ['nullable', 'string', 'max:80'],
            'quantity' => ['sometimes', 'integer', 'min:' . PrintJobOptions::QUANTITY_MIN, 'max:' . PrintJobOptions::QUANTITY_MAX],
            'infill_percent' => ['sometimes', 'integer', Rule::in(PrintJobOptions::infillPercents())],
            'scale_percent' => ['sometimes', 'integer', 'min:' . PrintJobOptions::SCALE_PERCENT_MIN, 'max:' . PrintJobOptions::SCALE_PERCENT_MAX],
        ];
    }
}
