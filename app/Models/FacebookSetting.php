<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FacebookSetting extends Model
{
    protected $fillable = [
        'page_id',
        'access_token',
        'verify_token',
        'app_secret',
        'is_active',
    ];

    protected $hidden = [
        'access_token',
        'app_secret',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(WhatsAppConversation::class, 'platform_page_id', 'page_id')
            ->where('platform', 'facebook');
    }

    /**
     * Get the active Facebook settings.
     */
    public static function active(): ?static
    {
        return static::query()->where('is_active', true)->first();
    }
}
