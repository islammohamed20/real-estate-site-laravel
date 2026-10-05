<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Rules\Turnstile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'remember' => ['sometimes', 'boolean'],
            'turnstile' => Rule::when(
                config('services.turnstile.enabled') && config('services.turnstile.secret_key'),
                ['required', 'string', new Turnstile],
                []
            ),
        ];
    }
}
