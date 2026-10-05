<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Turnstile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secret = config('services.turnstile.secret_key');

        if (! is_string($value) || $value === '' || ! is_string($secret) || $secret === '') {
            $fail(__('The verification challenge could not be completed. Please try again.'));

            return;
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => $secret,
                    'response' => $value,
                    'remoteip' => request()?->ip(),
                ]);

            if ($response->failed() || ! $response->json('success')) {
                $fail(__('The verification challenge failed. Please try again.'));
            }
        } catch (\Throwable $e) {
            $fail(__('The verification challenge could not be completed. Please try again.'));
        }
    }
}
