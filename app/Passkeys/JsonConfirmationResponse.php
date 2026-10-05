<?php

declare(strict_types=1);

namespace App\Passkeys;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Passkeys\Contracts\PasskeyConfirmationResponse as PasskeyConfirmationResponseContract;
use Symfony\Component\HttpFoundation\Response;

class JsonConfirmationResponse implements PasskeyConfirmationResponseContract
{
    public function toResponse($request): Response
    {
        $request->session()->put('passkey_confirmed_at', now());

        return new JsonResponse(['confirmed' => true], 200);
    }
}
