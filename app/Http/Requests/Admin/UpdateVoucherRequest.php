<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVoucherRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->role === 'admin'; }

    public function rules(): array
    {
        return [
            'code' => ['required','string','max:100', Rule::unique('vouchers','code')->ignore($this->voucher->id)],
            'description' => ['nullable','string'],
            'type' => ['required','in:percent,amount'],
            'value' => ['required','numeric','min:0'],
            'min_order_total' => ['required','numeric','min:0'],
            'starts_at' => ['nullable','date'],
            'ends_at' => ['nullable','date','after_or_equal:starts_at'],
            'usage_limit_total' => ['nullable','integer','min:1'],
            'usage_limit_per_user' => ['nullable','integer','min:1'],
            'max_discount_amount' => ['nullable','numeric','min:0'],
            'is_active' => ['required','boolean'],
        ];
    }
}
