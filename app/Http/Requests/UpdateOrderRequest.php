<?php

namespace App\Http\Requests;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
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
            'status' => ['required', Rule::in(array_column(OrderStatus::cases(), 'value'))],
            'payment_status' => ['required', Rule::in(array_column(PaymentStatus::cases(), 'value'))],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('payment_method')) {
            $value = trim((string) $this->payment_method);

            $this->merge([
                'payment_method' => $value === '' ? null : $value,
            ]);
        }

        if ($this->has('notes')) {
            $value = trim((string) $this->notes);

            $this->merge([
                'notes' => $value === '' ? null : $value,
            ]);
        }
    }

}
