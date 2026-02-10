<?php

namespace App\Exceptions;

use Exception;

class DuplicateSubmissionException extends Exception
{
    public static function forJudgeAndSegment(int $judgeId, int $segmentId): self
    {
        return new self("Judge {$judgeId} has already submitted scores for segment {$segmentId}");
    }

    public static function scoreAlreadyExists(): self
    {
        return new self("A score for this judge, contestant, criterion, and segment combination already exists");
    }
}
