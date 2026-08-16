<?php

declare(strict_types=1);

namespace App\Enums;

enum InstallmentFrequency: string
{
    case Monthly = 'monthly';
    case Quarterly = 'quarterly';
    case SemiAnnual = 'semi_annual';

    public function label(): string
    {
        return match ($this) {
            self::Monthly => 'Monthly',
            self::Quarterly => 'Quarterly',
            self::SemiAnnual => 'Semi Annual',
        };
    }

    public function monthsPerInstallment(): int
    {
        return match ($this) {
            self::Monthly => 1,
            self::Quarterly => 3,
            self::SemiAnnual => 6,
        };
    }
}
