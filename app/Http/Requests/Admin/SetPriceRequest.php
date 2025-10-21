<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SetPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'kode_barang' => ['required', 'string', 'max:100'],
            'harga_jual'  => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'harga_jual.required'  => 'Harga jual wajib diisi.',
            'harga_jual.numeric'   => 'Harga jual harus berupa angka.',
            'harga_jual.min'       => 'Harga jual tidak boleh negatif.',
        ];
    }
}
