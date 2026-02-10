<?php

namespace App\DTOs;

class ScoreSubmissionDTO
{
    public function __construct(
        public readonly int $contestantId,
        public readonly int $criterionId,
        public readonly float $scoreValue
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            contestantId: $data['contestant_id'],
            criterionId: $data['criterion_id'],
            scoreValue: (float) $data['score_value']
        );
    }
}
