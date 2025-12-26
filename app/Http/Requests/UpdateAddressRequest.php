<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'alias' => ['nullable', 'string', 'max:255'],

            'street_id' => ['required', 'integer', Rule::exists('streets', 'id')],

            'street_number' => ['required', 'string', 'max:50'],
            'floor' => ['nullable', 'string', 'max:50'],
            'door' => ['nullable', 'string', 'max:50'],

            'address_type' => ['required', Rule::in(['shipping', 'billing'])],

            'contact_phone' => ['nullable', 'string', 'max:50'],
        ];
    }
}
