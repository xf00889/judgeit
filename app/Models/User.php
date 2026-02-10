<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'judging_gender',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * The 'password' => 'hashed' cast ensures passwords are automatically
     * hashed using bcrypt when set, providing secure password storage.
     * Laravel uses bcrypt by default, which is a secure hashing algorithm.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * Get the categories assigned to this judge.
     */
    public function assignedCategories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'judge_category', 'user_id', 'category_id')
            ->withTimestamps();
    }

    /**
     * Get the scores submitted by this judge.
     */
    public function scores(): HasMany
    {
        return $this->hasMany(Score::class, 'judge_id');
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    /**
     * Check if the user is a judge.
     */
    public function isJudge(): bool
    {
        return $this->role === UserRole::JUDGE;
    }

    /**
     * Check if the user is a tabulator.
     */
    public function isTabulator(): bool
    {
        return $this->role === UserRole::TABULATOR;
    }
}
