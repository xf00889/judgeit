<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Score extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'judge_id',
        'contestant_id',
        'criterion_id',
        'category_id',
        'score_value',
        'submitted_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'score_value' => 'decimal:2',
            'submitted_at' => 'datetime',
        ];
    }

    /**
     * Get the judge who submitted this score.
     */
    public function judge(): BelongsTo
    {
        return $this->belongsTo(User::class, 'judge_id');
    }

    /**
     * Get the contestant who received this score.
     */
    public function contestant(): BelongsTo
    {
        return $this->belongsTo(Contestant::class);
    }

    /**
     * Get the criterion this score is for.
     */
    public function criterion(): BelongsTo
    {
        return $this->belongsTo(Criterion::class);
    }

    /**
     * Get the category this score belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
