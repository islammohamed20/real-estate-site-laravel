<?php

declare(strict_types=1);

namespace App\Enums;

enum UnitStatus: string
{
    case Available = 'available';
    case Reserved = 'reserved';
    case Sold = 'sold';
    case Hidden = 'hidden';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Reserved => 'Reserved',
            self::Sold => 'Sold',
            self::Hidden => 'Hidden',
        };
    }
}
