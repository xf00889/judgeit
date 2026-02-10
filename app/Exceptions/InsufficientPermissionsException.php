<?php

namespace App\Exceptions;

use Exception;

class InsufficientPermissionsException extends Exception
{
    public static function notAssignedToSegment(int $judgeId, int $segmentId): self
    {
        return new self("Judge {$judgeId} is not assigned to segment {$segmentId}");
    }

    public static function invalidRole(string $requiredRole, string $actualRole): self
    {
        return new self("This action requires '{$requiredRole}' role, but user has '{$actualRole}' role");
    }

    public static function cannotAccessResource(string $resource): self
    {
        return new self("You do not have permission to access {$resource}");
    }

    public static function actionNotAllowed(string $action): self
    {
        return new self("You are not authorized to perform this action: {$action}");
    }
}
