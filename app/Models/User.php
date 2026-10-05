<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\NotificationRegistry;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passkeys\Contracts\PasskeyUser;
use Laravel\Passkeys\PasskeyAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements PasskeyUser
{
    public const DASHBOARD_SECTIONS = [
        'dashboard' => 'Dashboard',
        'crm' => 'CRM',
        'sales' => 'Sales Team',
        'projects' => 'Projects',
        'reports' => 'Reports',
        'analytics' => 'Site Analytics',
        'trash' => 'Trash',
        'users' => 'Users',
        'banners' => 'Banners',
        'settings' => 'Settings',
        'tools' => 'Calculator',
    ];

    use CanResetPassword;
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use PasskeyAuthenticatable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'phone',
        'job_title',
        'department',
        'department_id',
        'avatar_path',
        'is_active',
        'notification_preferences',
        'dashboard_sections',
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
        'active_session_id',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'notification_preferences' => 'array',
            'dashboard_sections' => 'array',
            'two_factor_enabled' => 'boolean',
            'two_factor_recovery_codes' => 'array',
            'force_logout_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    public function hasDashboardSection(string $section): bool
    {
        return $this->dashboard_sections === null
            || in_array($section, $this->dashboard_sections, true);
    }

    public function departmentRecord(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
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

    public function rolePermissionNames(): array
    {
        return $this->roles
            ->pluck('permissions')
            ->flatten()
            ->pluck('name')
            ->unique()
            ->values()
            ->all();
    }

    public function allPermissionNames(): array
    {
        return collect($this->rolePermissionNames())
            ->merge($this->permissions->pluck('name')->all())
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Whether the account requires a second factor (Google Authenticator or Passkey)
     * before accessing the dashboard.
     */
    public function needsSecondFactor(): bool
    {
        return $this->two_factor_enabled || $this->passkeys()->exists();
    }
}
