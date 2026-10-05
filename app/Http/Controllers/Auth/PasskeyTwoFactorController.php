<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Passkeys\Actions\GenerateVerificationOptions;
use Laravel\Passkeys\Actions\VerifyPasskey;
use Laravel\Passkeys\Contracts\PasskeyUser;
use Laravel\Passkeys\Http\Requests\PasskeyVerificationRequest;
use Laravel\Passkeys\Support\WebAuthn;
use RuntimeException;

class PasskeyTwoFactorController extends Controller
{
    public function options(Request $request, GenerateVerificationOptions $generate): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof PasskeyUser) {
            throw new RuntimeException('User model must implement the PasskeyUser contract.');
        }

        $options = $generate($user);

        $serialized = WebAuthn::toJson($options);

        $request->session()->put('passkey.verification_options', $serialized);

        return response()->json([
            'options' => WebAuthn::toBrowserArray($options),
        ]);
    }

    public function verify(PasskeyVerificationRequest $request, VerifyPasskey $verify): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof PasskeyUser) {
            throw new RuntimeException('User model must implement the PasskeyUser contract.');
        }

        $verify($request->credential(), $request->verificationOptions(), $user);

        $request->session()->put('2fa:verified', true);
        $request->session()->forget('2fa:user:id');

        return response()->json([
            'redirect' => route('dashboard.home'),
        ]);
    }
}
