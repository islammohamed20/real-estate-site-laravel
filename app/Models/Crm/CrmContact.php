<?php

declare(strict_types=1);

namespace App\Models\Crm;

use App\Models\Note;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmContact extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_contacts';

    protected $fillable = [
        'organization_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'mobile',
        'job_title',
        'source',
        'is_primary',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(CrmOrganization::class, 'organization_id');
    }

    public function deals(): BelongsToMany
    {
        return $this->belongsToMany(CrmDeal::class, 'crm_deal_contact', 'contact_id', 'deal_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CrmActivity::class, 'contact_id');
    }

    public function recordedNotes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable')->latest();
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }
}
