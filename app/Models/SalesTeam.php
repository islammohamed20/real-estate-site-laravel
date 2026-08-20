<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SalesTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'manager_id',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'sales_team_user')->withTimestamps();
    }

    /**
     * IDs of the salespeople belonging to this team.
     *
     * @return list<int>
     */
    public function memberUserIds(): array
    {
        return $this->members->pluck('id')->all();
    }

    /**
     * Count of non-deleted leads currently assigned to any team member.
     */
    public function activeLeadsCount(): int
    {
        return Lead::query()
            ->whereIn('assigned_sales_id', $this->memberUserIds())
            ->count();
    }
}
