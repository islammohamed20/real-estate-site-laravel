<?php

declare(strict_types=1);

namespace App\Services\Security;

use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

/**
 * Google Authenticator (TOTP) helpers for the dashboard users:
 * secret generation, provisioning URI, code verification, QR rendering
 * and recovery codes (stored hashed, one-time use).
 */
class TwoFactorService
{
    public function __construct(private readonly Google2FA $google2fa)
    {
    }

    /**
     * Generate a new base32 TOTP secret (32 chars, ~160 bits).
     */
    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey(32);
    }

    /**
     * otpauth:// URI that Google Authenticator can scan.
     */
    public function provisioningUri(User $user, string $secret): string
    {
        return $this->google2fa->getQRCodeUrl(
            (string) (config('app.name') ?: 'Venecia Developments'),
            $user->email,
            $secret
        );
    }

    /**
     * Verify a 6-digit code against a secret (allows ±1 time window drift).
     */
    public function verify(string $secret, string $code): bool
    {
        $code = preg_replace('/\s+/', '', trim($code)) ?? '';

        if (! preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        return $this->google2fa->verifyKey($secret, $code, 1);
    }

    /**
     * Render the provisioning URI as an SVG data-URI QR code (server-side, offline).
     */
    public function qrCodeDataUri(string $provisioningUri): string
    {
        $renderer = new ImageRenderer(new RendererStyle(220, 4), new SvgImageBackEnd());

        $writer = new Writer($renderer);

        return 'data:image/svg+xml;base64,'.base64_encode($writer->writeString($provisioningUri));
    }

    /**
     * Generate a fresh set of one-time recovery codes.
     */
    public function generateRecoveryCodes(int $count = 10): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(Str::random(10));
        }

        return $codes;
    }

    /**
     * Store recovery codes as SHA-256 hashes (never plaintext).
     */
    public function hashRecoveryCode(string $code): string
    {
        return hash('sha256', strtoupper(trim($code)));
    }

    /**
     * Verify a recovery code; on success the used code is consumed (removed).
     */
    public function verifyRecoveryCode(User $user, string $code): bool
    {
        $codes = is_array($user->two_factor_recovery_codes) ? $user->two_factor_recovery_codes : [];

        if ($codes === []) {
            return false;
        }

        $hashed = $this->hashRecoveryCode($code);
        $index = array_search($hashed, $codes, true);

        if ($index === false) {
            return false;
        }

        unset($codes[$index]);
        $user->forceFill(['two_factor_recovery_codes' => array_values($codes)])->save();

        return true;
    }
}
