<?php

declare(strict_types=1);

namespace App\Support;

class WhatsApp
{
    /**
     * Normalize any phone number into the international format WhatsApp needs
     * (no leading zero, country code first). Handles local Egyptian numbers
     * (010..., 011..., 012..., 015...), numbers with +, and 00 prefixes.
     */
    public static function number(?string $number): ?string
    {
        if ($number === null || trim($number) === '') {
            return null;
        }

        // Strip everything except digits
        $digits = preg_replace('/\D+/', '', (string) $number);

        // Remove leading 00 (international dialing prefix)
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        // Already international (e.g. 20..., 966..., 971...)
        if (str_starts_with($digits, '20') || strlen($digits) >= 12) {
            return $digits;
        }

        // Egyptian mobile: 0XX... -> 20XX...
        if (str_starts_with($digits, '0')) {
            return '20'.substr($digits, 1);
        }

        // Egyptian mobile without leading zero: 1XXXXXXXXX -> 201XXXXXXXXX
        if (strlen($digits) === 10 && str_starts_with($digits, '1')) {
            return '20'.$digits;
        }

        return $digits;
    }

    /**
     * Build a wa.me deep-link. Falls back to the primary phone when no
     * dedicated WhatsApp number is set.
     */
    public static function link(?string $whatsapp, ?string $phone = null, ?string $message = null): ?string
    {
        $number = self::number($whatsapp ?? $phone);
        if ($number === null) {
            return null;
        }

        $url = 'https://wa.me/'.$number;
        if ($message !== null && trim($message) !== '') {
            $url .= '?text='.rawurlencode($message);
        }

        return $url;
    }
}
