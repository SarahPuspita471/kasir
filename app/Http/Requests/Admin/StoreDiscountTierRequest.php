<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiscountTierRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->role === 'admin'; }

    public function rules(): array
    {
        return [
            'min_subtotal' => ['required','numeric','min:0'],
            'type' => ['required','in:percent,amount'],
            'value' => ['required','numeric','min:0'],
            'priority' => ['nullable','integer','min:0'],
        ];
    }
}
