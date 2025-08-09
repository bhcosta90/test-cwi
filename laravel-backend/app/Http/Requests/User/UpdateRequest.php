<?php

declare(strict_types = 1);

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

final class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique(User::class)->ignore($this->route('user'))],
            'password' => ['nullable', 'string', Password::default(), 'confirmed'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
