<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LeadStage;
use App\Models\Crm\CrmActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'assigned_sales_id',
        'lead_source_id',
        'name',
        'phone',
        'whatsapp',
        'email',
        'address',
        'occupation',
        'budget',
        'stage',
        'status',
        'source',
        'campaign',
        'unit_type',
        'bedrooms',
        'required_area',
        'preferred_payment_plan',
        'priority',
        'notes',
        'last_contacted_at',
        'follow_up_at',
        'converted_at',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'required_area' => 'decimal:2',
            'stage' => LeadStage::class,
            'bedrooms' => 'integer',
            'last_contacted_at' => 'datetime',
            'follow_up_at' => 'datetime',
            'converted_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedSales(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_sales_id');
    }

    public function leadSource(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id');
    }

    public function interestedProjects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'lead_project');
    }

    public function interestedUnits(): HasMany
    {
        return $this->hasMany(LeadUnitInterest::class);
    }

    public function stageHistory(): HasMany
    {
        return $this->hasMany(LeadStageHistory::class);
    }

    public function assignmentHistory(): HasMany
    {
        return $this->hasMany(LeadAssignmentHistory::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
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

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class)->latest();
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable')->latest();
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
