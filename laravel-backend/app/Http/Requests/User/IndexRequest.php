<?php

declare(strict_types = 1);

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

final class IndexRequest extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
