<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'legal_name',
        'logo_path',
        'logo_light_path',
        'logo_dark_path',
        'logo_height_desktop',
        'logo_height_mobile',
        'stamp_path',
        'favicon_path',
        'address',
        'google_maps_api_key',
        'phone',
        'email',
        'website',
        'currency_code',
        'default_language',
        'available_features',
        'maintenance_percent',
        'trash_retention_days',
        'auto_purge_enabled',
        'smtp_from_name',
        'smtp_from_email',
        'sales_manager_whatsapp',
        'evolution_api_url',
        'evolution_api_key',
        'evolution_instance_name',
    ];

    protected function casts(): array
    {
        return [
            'maintenance_percent' => 'decimal:2',
            'trash_retention_days' => 'integer',
            'auto_purge_enabled' => 'boolean',
            'available_features' => 'array',
            'logo_height_desktop' => 'integer',
            'logo_height_mobile' => 'integer',
        ];
    }
}
