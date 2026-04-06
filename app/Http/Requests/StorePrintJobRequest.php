<?php

namespace App\Http\Requests;

use App\Support\PrintJobOptions;
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
            'technology' => ['required', 'string', 'max:50', Rule::in(PrintJobOptions::technologies())],
            'color_name' => ['nullable', 'string', 'max:80'],
            'quantity' => ['required', 'integer', 'min:' . PrintJobOptions::QUANTITY_MIN, 'max:' . PrintJobOptions::QUANTITY_MAX],
            'infill_percent' => ['required', 'integer', Rule::in(PrintJobOptions::infillPercents())],
            'scale_percent' => ['required', 'integer', 'min:' . PrintJobOptions::SCALE_PERCENT_MIN, 'max:' . PrintJobOptions::SCALE_PERCENT_MAX],
        ];
    }
}
