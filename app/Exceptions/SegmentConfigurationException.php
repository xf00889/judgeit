<?php

namespace App\Exceptions;

use Exception;

class SegmentConfigurationException extends Exception
{
    public static function invalidPercentageSum(float $actualSum): self
    {
        return new self("Segment percentages must sum to 100%. Current sum: {$actualSum}%");
    }

    public static function incompleteConfiguration(string $reason): self
    {
        return new self("Segment configuration is incomplete: {$reason}");
    }

    public static function cannotModifyActiveSegment(): self
    {
        return new self("Cannot modify segment configuration after activation");
    }

    public static function onlyOneActiveSegmentAllowed(): self
    {
        return new self("Only one segment can be active at a time");
    }

    public static function invalidStateTransition(string $from, string $to): self
    {
        return new self("Invalid segment state transition from {$from} to {$to}");
    }
}
