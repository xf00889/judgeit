<?php

namespace App\Exceptions;

use Exception;

class SegmentNotActiveException extends Exception
{
    public static function cannotSubmitScores(int $segmentId, string $status): self
    {
        return new self("Cannot submit scores to segment {$segmentId}. Segment status is '{$status}', but must be 'active'");
    }

    public static function segmentIsLocked(int $segmentId): self
    {
        return new self("Segment {$segmentId} is locked and no longer accepting score submissions");
    }

    public static function segmentIsFinalized(int $segmentId): self
    {
        return new self("Segment {$segmentId} is finalized and cannot be modified");
    }
}
