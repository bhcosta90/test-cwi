<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $this->route('user')],
            'password' => ['required', 'string', Password::default(), 'confirmed'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
