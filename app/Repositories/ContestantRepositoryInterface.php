<?php

namespace App\Repositories;

use App\Models\Contestant;
use Illuminate\Support\Collection;

interface ContestantRepositoryInterface
{
    /**
     * Get all active contestants.
     *
     * @param string|null $gender Filter by gender (male, female, or null for all)
     * @return Collection
     */
    public function getActiveContestants(?string $gender = null): Collection;

    /**
     * Check if a contestant can be deleted.
     * A contestant cannot be deleted if they have received any scores.
     *
     * @param Contestant $contestant
     * @return bool
     */
    public function canDelete(Contestant $contestant): bool;

    /**
     * Get all finalists ordered by preliminary rank.
     *
     * @return Collection
     */
    public function getFinalists(): Collection;

    /**
     * Get active contestants filtered for finals judging.
     * Only returns finalists if they exist, otherwise returns all active contestants.
     *
     * @param string|null $gender Filter by gender (male, female, or null for all)
     * @return Collection
     */
    public function getContestantsForFinalsJudging(?string $gender = null): Collection;
}
