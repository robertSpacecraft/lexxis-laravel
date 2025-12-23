<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\PrintFileStatus;
use Illuminate\Validation\Rule;

class UpdatePrintFileRequest extends FormRequest
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
            'status' => ['required', Rule::enum(PrintFileStatus::class)],
            'notes' => ['nullable', 'string', 'max:2000'],
            'metadata' => ['nullable', 'string', 'json'],
        ];
    }
}
