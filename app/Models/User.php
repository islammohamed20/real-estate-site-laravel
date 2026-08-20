<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\NotificationRegistry;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use CanResetPassword;
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'phone',
        'job_title',
        'department',
        'avatar_path',
        'is_active',
        'notification_preferences',
        'force_logout_at',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'notification_preferences' => 'array',
            'two_factor_enabled' => 'boolean',
            'two_factor_recovery_codes' => 'array',
            'force_logout_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    public function loginHistories(): HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function salesTeams(): BelongsToMany
    {
        return $this->belongsToMany(SalesTeam::class, 'sales_team_user')->withTimestamps();
    }

    /**
     * Teams where this user is the manager (Sales Manager isolation scope).
     */
    public function managedTeams(): HasMany
    {
        return $this->hasMany(SalesTeam::class, 'manager_id');
    }

    public function assignedLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_sales_id');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class, 'sales_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'sales_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Whether the user opted in to the given notification type.
     */
    public function acceptsNotification(string $type): bool
    {
        return ! in_array($type, $this->notification_preferences ?? [], true);
    }

    /**
     * Notification types this user is allowed to receive based on their
     * permissions (before applying their personal opt-outs).
     *
     * @return array<string, array{permission: string, title_en: string, title_ar: string}>
     */
    public function allowedNotificationTypes(): array
    {
        $types = NotificationRegistry::types();

        if ($this->hasPermissionTo('receive notification.all')) {
            return $types;
        }

        return array_filter($types, fn (array $meta) => $this->hasPermissionTo($meta['permission']));
    }
}
