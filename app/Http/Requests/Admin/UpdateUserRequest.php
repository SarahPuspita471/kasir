<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->role === 'admin'; }

    public function rules(): array
    {
        $id = $this->route('user')->id ?? null;

        return [
            'name'     => ['required','string','min:3','max:50', Rule::unique('users','name')->ignore($id)],
            'email'    => ['required','email','max:100', Rule::unique('users','email')->ignore($id)],
            'role'     => ['required','in:admin,user'],
            'password' => ['nullable','string','min:6'], // opsional; isi kalau mau ganti password
        ];
    }
}
