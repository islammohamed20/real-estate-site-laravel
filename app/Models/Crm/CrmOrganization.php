<?php

declare(strict_types=1);

namespace App\Models\Crm;

use App\Models\Note;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmOrganization extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_organizations';

    protected $fillable = [
        'name',
        'industry',
        'website',
        'phone',
        'email',
        'address',
        'city',
        'country',
        'tax_id',
        'notes',
    ];

    public function contacts(): HasMany
    {
        return $this->hasMany(CrmContact::class, 'organization_id')->orderBy('is_primary', 'desc');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(CrmDeal::class, 'organization_id');
    }

    public function recordedNotes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable')->latest();
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }
}
