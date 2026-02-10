<?php

namespace App\Models;

use App\Enums\CategoryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
        'order',
        'status',
        'is_finals',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'status' => CategoryStatus::class,
            'is_finals' => 'boolean',
        ];
    }

    /**
     * Get the criteria for this category.
     */
    public function criteria(): HasMany
    {
        return $this->hasMany(Criterion::class, 'category_id');
    }

    /**
     * Get the judges assigned to this category.
     */
    public function judges(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'judge_category', 'category_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Get the scores for this category.
     */
    public function scores(): HasMany
    {
        return $this->hasMany(Score::class, 'category_id');
    }

    /**
     * Check if the category is active.
     */
    public function isActive(): bool
    {
        return $this->status === CategoryStatus::ACTIVE;
    }

    /**
     * Check if the category is locked.
     */
    public function isLocked(): bool
    {
        return $this->status === CategoryStatus::LOCKED;
    }

    /**
     * Check if the category is finalized.
     */
    public function isFinalized(): bool
    {
        return $this->status === CategoryStatus::FINALIZED;
    }

    /**
     * Activate the category.
     */
    public function activate(): void
    {
        $this->status = CategoryStatus::ACTIVE;
        $this->save();
    }

    /**
     * Lock the category.
     */
    public function lock(): void
    {
        $this->status = CategoryStatus::LOCKED;
        $this->save();
    }

    /**
     * Finalize the category.
     */
    public function finalize(): void
    {
        $this->status = CategoryStatus::FINALIZED;
        $this->save();
    }

    /**
     * Check if all assigned judges have submitted scores for this category.
     */
    public function allJudgesSubmitted(): bool
    {
        $this->load('judges');
        
        $totalJudges = $this->judges->count();
        
        if ($totalJudges === 0) {
            return false;
        }

        // Get unique judges who have submitted scores for this category
        $submittedJudges = Score::where('category_id', $this->id)
            ->distinct('judge_id')
            ->pluck('judge_id')
            ->count();

        return $submittedJudges >= $totalJudges;
    }

    /**
     * Check if at least 3 judges have submitted scores for this category.
     * This is used to determine if category winners can be displayed.
     */
    public function hasMinimumJudgesSubmitted(): bool
    {
        // Get unique judges who have submitted scores for this category
        $submittedJudges = Score::where('category_id', $this->id)
            ->distinct('judge_id')
            ->pluck('judge_id')
            ->count();

        return $submittedJudges >= 3;
    }

    /**
     * Check if this is a finals category.
     */
    public function isFinals(): bool
    {
        return $this->is_finals === true;
    }

    /**
     * Scope a query to only include finals categories.
     */
    public function scopeFinals($query)
    {
        return $query->where('is_finals', true);
    }

    /**
     * Scope a query to only include preliminary categories.
     */
    public function scopePreliminary($query)
    {
        return $query->where('is_finals', false);
    }
}
