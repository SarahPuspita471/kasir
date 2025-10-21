<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->role === 'admin'; }

    public function rules(): array
    {
        return [
            'name'     => ['required','string','min:3','max:50','unique:users,name'],
            'email'    => ['required','email','max:100','unique:users,email'],
            'role'     => ['required','in:admin,user'],
            'password' => ['nullable','string','min:6'], // jika kosong, akan dibuatkan default
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Gunakan format email yang benar.',
            'role.in'        => 'Role harus admin atau user.',
        ];
    }
}
