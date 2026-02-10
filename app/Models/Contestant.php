<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contestant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'gender',
        'contestant_number',
        'profile_details',
        'is_active',
        'is_finalist',
        'preliminary_rank',
        'preliminary_score',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_finalist' => 'boolean',
            'preliminary_rank' => 'integer',
            'preliminary_score' => 'decimal:2',
        ];
    }

    /**
     * Get the scores for this contestant.
     */
    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    /**
     * Scope a query to only include active contestants.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include finalists.
     */
    public function scopeFinalists($query)
    {
        return $query->where('is_finalist', true);
    }

    /**
     * Check if this contestant is a finalist.
     */
    public function isFinalist(): bool
    {
        return $this->is_finalist === true;
    }
}
