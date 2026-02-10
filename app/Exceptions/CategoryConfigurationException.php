<?php

namespace App\Exceptions;

use Exception;

class CategoryConfigurationException extends Exception
{
    public static function invalidPercentageSum(float $actualSum): self
    {
        return new self("Category percentages must sum to 100%. Current sum: {$actualSum}%");
    }

    public static function incompleteConfiguration(string $reason): self
    {
        return new self("Category configuration is incomplete: {$reason}");
    }

    public static function cannotModifyActiveCategory(): self
    {
        return new self("Cannot modify category configuration after activation");
    }

    public static function onlyOneActiveCategoryAllowed(): self
    {
        return new self("Only one category can be active at a time");
    }

    public static function invalidStateTransition(string $from, string $to): self
    {
        return new self("Invalid category state transition from {$from} to {$to}");
    }
}
