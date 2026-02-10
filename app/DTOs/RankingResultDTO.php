<?php

namespace App\DTOs;

use App\Models\Contestant;

class RankingResultDTO
{
    public function __construct(
        public readonly int $rank,
        public readonly Contestant $contestant,
        public readonly float $overallScore,
        public readonly array $categoryScores
    ) {}
}
