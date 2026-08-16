<?php

declare(strict_types=1);

namespace App\Services\Security;

class DeviceDetector
{
    public function deviceName(?string $userAgent): string
    {
        if ($userAgent === null || $userAgent === '') {
            return 'Unknown Device';
        }

        return match (true) {
            str_contains(strtolower($userAgent), 'iphone') => 'iPhone',
            str_contains(strtolower($userAgent), 'ipad') => 'iPad',
            str_contains(strtolower($userAgent), 'android') => 'Android Device',
            str_contains(strtolower($userAgent), 'windows') => 'Windows Device',
            str_contains(strtolower($userAgent), 'macintosh') => 'Mac Device',
            default => 'Web Browser',
        };
    }

    public function deviceType(?string $userAgent): string
    {
        if ($userAgent === null || $userAgent === '') {
            return 'unknown';
        }

        $lower = strtolower($userAgent);

        return match (true) {
            str_contains($lower, 'mobile') || str_contains($lower, 'iphone') || str_contains($lower, 'android') => 'mobile',
            str_contains($lower, 'ipad') || str_contains($lower, 'tablet') => 'tablet',
            default => 'desktop',
        };
    }
}
