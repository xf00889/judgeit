<?php

namespace App\Exceptions;

use Exception;

class CategoryNotActiveException extends Exception
{
    public static function cannotSubmitScores(int $categoryId, string $status): self
    {
        return new self("Cannot submit scores to category {$categoryId}. Category status is '{$status}', but must be 'active'");
    }

    public static function categoryIsLocked(int $categoryId): self
    {
        return new self("Category {$categoryId} is locked and no longer accepting score submissions");
    }

    public static function categoryIsFinalized(int $categoryId): self
    {
        return new self("Category {$categoryId} is finalized and cannot be modified");
    }
}
