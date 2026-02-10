<?php

namespace App\Repositories;

use App\Models\Contestant;
use Illuminate\Support\Collection;

class ContestantRepository implements ContestantRepositoryInterface
{
    /**
     * Get all active contestants.
     *
     * @param string|null $gender Filter by gender (male, female, or null for all)
     * @return Collection
     */
    public function getActiveContestants(?string $gender = null): Collection
    {
        $query = Contestant::where('is_active', true);
        
        if ($gender && in_array($gender, ['male', 'female'])) {
            $query->where('gender', $gender);
        }
        
        return $query->orderBy('contestant_number')->get();
    }

    /**
     * Check if a contestant can be deleted.
     * A contestant cannot be deleted if they have received any scores.
     *
     * @param Contestant $contestant
     * @return bool
     */
    public function canDelete(Contestant $contestant): bool
    {
        return !$contestant->scores()->exists();
    }

    /**
     * Get all finalists ordered by preliminary rank.
     *
     * @return Collection
     */
    public function getFinalists(): Collection
    {
        return Contestant::where('is_finalist', true)
            ->orderBy('preliminary_rank')
            ->get();
    }

    /**
     * Get active contestants filtered for finals judging.
     * Only returns finalists if they exist, otherwise returns all active contestants.
     *
     * @param string|null $gender Filter by gender (male, female, or null for all)
     * @return Collection
     */
    public function getContestantsForFinalsJudging(?string $gender = null): Collection
    {
        $query = Contestant::where('is_active', true)
            ->where('is_finalist', true);
        
        if ($gender && in_array($gender, ['male', 'female'])) {
            $query->where('gender', $gender);
        }
        
        return $query->orderBy('contestant_number')->get();
    }
}
