<?php

declare(strict_types=1);

namespace App\Models\Crm;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Note;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmDeal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_deals';

    protected $fillable = [
        'title',
        'pipeline_id',
        'stage_id',
        'organization_id',
        'contact_id',
        'lead_id',
        'customer_id',
        'assigned_to',
        'created_by',
        'project_id',
        'unit_id',
        'value',
        'currency_code',
        'expected_close_date',
        'priority',
        'source',
        'description',
        'status',
        'stage_changed_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'expected_close_date' => 'date',
            'stage_changed_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (CrmDeal $deal): void {
            $deal->stage_changed_at ??= now();
        });

        static::updating(function (CrmDeal $deal): void {
            if ($deal->isDirty('stage_id')) {
                $deal->stage_changed_at = now();
            }
        });
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(CrmPipeline::class, 'pipeline_id');
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(CrmStage::class, 'stage_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(CrmOrganization::class, 'organization_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(CrmContact::class, 'contact_id');
    }

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(CrmContact::class, 'crm_deal_contact', 'deal_id', 'contact_id')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CrmActivity::class, 'deal_id')->latest();
    }

    public function stageHistory(): HasMany
    {
        return $this->hasMany(CrmDealStageHistory::class, 'deal_id')->latest();
    }

    public function recordedNotes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable')->latest();
    }

    public function primaryContact(): ?CrmContact
    {
        return $this->contacts->firstWhere('pivot.is_primary', true)
            ?? $this->contact
            ?? $this->contacts->first();
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->title;
    }
}
