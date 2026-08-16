<?php

declare(strict_types=1);

namespace App\Enums;

enum LeadStage: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case Interested = 'interested';
    case Meeting = 'meeting';
    case SiteVisit = 'site_visit';
    case Negotiation = 'negotiation';
    case Reserved = 'reserved';
    case Contract = 'contract';
    case Delivered = 'delivered';

    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Contacted => 'Contacted',
            self::Interested => 'Interested',
            self::Meeting => 'Meeting',
            self::SiteVisit => 'Site Visit',
            self::Negotiation => 'Negotiation',
            self::Reserved => 'Reserved',
            self::Contract => 'Contract',
            self::Delivered => 'Delivered',
        };
    }
}
