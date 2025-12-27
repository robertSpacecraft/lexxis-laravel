<?php

namespace App\Http\Requests\Carts;

use Illuminate\Foundation\Http\FormRequest;

class AddPrintJobToCartRequest extends FormRequest
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
            'quantity' => ['nullable', 'integer', 'min:1', 'max:999'],
        ];
    }
    public function validatedQuantity(): int
    {
        return (int) ($this->validated()['quantity'] ?? 1);
    }
}
