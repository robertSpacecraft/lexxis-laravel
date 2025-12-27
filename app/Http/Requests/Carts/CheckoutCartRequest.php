<?php

namespace App\Http\Requests\Carts;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutCartRequest extends FormRequest
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
            'shipping_address_id' => ['nullable', 'integer', 'exists:addresses,id'],
            'billing_address_id'  => ['nullable', 'integer', 'exists:addresses,id'],
            'payment_method'      => ['nullable', 'string', 'max:50'],
            'notes'               => ['nullable', 'string', 'max:2000'],
        ];
    }
    public function shippingAddressId(): ?int
    {
        $value = $this->validated('shipping_address_id');
        return $value === null ? null : (int) $value;
    }

    public function billingAddressId(): ?int
    {
        $value = $this->validated('billing_address_id');
        return $value === null ? null : (int) $value;
    }

    public function paymentMethod(): ?string
    {
        return $this->validated('payment_method');
    }

    public function notes(): ?string
    {
        return $this->validated('notes');
    }
}
