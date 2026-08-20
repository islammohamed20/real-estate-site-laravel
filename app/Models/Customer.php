<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Crm\CrmActivity;
use App\Models\Crm\CrmDeal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'whatsapp',
        'email',
        'password',
        'address',
        'occupation',
        'budget',
        'budget_min',
        'budget_max',
        'notes',
        'preferred_locale',
        'source',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'budget_min' => 'decimal:2',
            'budget_max' => 'decimal:2',
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'otp_sent_at' => 'datetime',
            'otp_attempts' => 'integer',
            'whatsapp_two_factor_enabled' => 'boolean',
            'authenticator_two_factor_enabled' => 'boolean',
            'two_factor_recovery_codes' => 'array',
        ];
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function interestedProjects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'customer_project');
    }

    public function tasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'taskable')->latest();
    }

    public function recordedNotes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable')->latest();
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(CrmActivity::class, 'activityable')->latest();
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function plans(): HasMany
    {
        return $this->hasMany(InstallmentPlan::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class)->latest();
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(CrmDeal::class, 'customer_id');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable')->latest();
    }
}
