<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    use HasFactory;

    public const DEFAULT_EVOLUTION_API_URL = 'http://144.91.79.64:8096';

    public const DEFAULT_EVOLUTION_INSTANCE_NAME = 'Venecia Developments';

    public const DEFAULT_EVOLUTION_DASHBOARD_URL = 'http://144.91.79.64:8096/manager/instance/9102b9ab-6c2a-49d0-96f9-cd6c7cd11620/dashboard';

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
        'facebook_url',
        'instagram_url',
        'seo_title',
        'seo_description',
        'seo_image_path',
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
        'evolution_dashboard_url',
        'evolution_outgoing_color',
        'evolution_incoming_color',
        'evolution_chat_background',
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
