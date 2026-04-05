<?php

namespace App\Http\Requests\Carts;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemQuantityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ];
    }

    public function validatedQuantity(): int
    {
        return (int) $this->validated()['quantity'];
    }
}
