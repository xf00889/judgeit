<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AuditLogService
{
    /**
     * Log category state change
     */
    public function logCategoryStateChange(int $categoryId, string $categoryName, string $oldStatus, string $newStatus, int $userId): void
    {
        Log::channel('audit')->info('Category State Changed', [
            'action' => 'category_state_change',
            'category_id' => $categoryId,
            'category_name' => $categoryName,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'user_id' => $userId,
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * Log judge assignment
     */
    public function logJudgeAssignment(int $judgeId, string $judgeName, int $categoryId, string $categoryName, int $adminId): void
    {
        Log::channel('audit')->info('Judge Assigned to Category', [
            'action' => 'judge_assignment',
            'judge_id' => $judgeId,
            'judge_name' => $judgeName,
            'category_id' => $categoryId,
            'category_name' => $categoryName,
            'admin_id' => $adminId,
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * Log judge removal
     */
    public function logJudgeRemoval(int $judgeId, string $judgeName, int $categoryId, string $categoryName, int $adminId): void
    {
        Log::channel('audit')->info('Judge Removed from Category', [
            'action' => 'judge_removal',
            'judge_id' => $judgeId,
            'judge_name' => $judgeName,
            'category_id' => $categoryId,
            'category_name' => $categoryName,
            'admin_id' => $adminId,
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * Log contestant creation
     */
    public function logContestantCreated(int $contestantId, string $contestantName, string $contestantNumber, int $adminId): void
    {
        Log::channel('audit')->info('Contestant Created', [
            'action' => 'contestant_created',
            'contestant_id' => $contestantId,
            'contestant_name' => $contestantName,
            'contestant_number' => $contestantNumber,
            'admin_id' => $adminId,
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * Log contestant update
     */
    public function logContestantUpdated(int $contestantId, string $contestantName, array $changes, int $adminId): void
    {
        Log::channel('audit')->info('Contestant Updated', [
            'action' => 'contestant_updated',
            'contestant_id' => $contestantId,
            'contestant_name' => $contestantName,
            'changes' => $changes,
            'admin_id' => $adminId,
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * Log contestant deletion
     */
    public function logContestantDeleted(int $contestantId, string $contestantName, int $adminId): void
    {
        Log::channel('audit')->info('Contestant Deleted', [
            'action' => 'contestant_deleted',
            'contestant_id' => $contestantId,
            'contestant_name' => $contestantName,
            'admin_id' => $adminId,
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * Log category creation
     */
    public function logCategoryCreated(int $categoryId, string $categoryName, int $adminId): void
    {
        Log::channel('audit')->info('Category Created', [
            'action' => 'category_created',
            'category_id' => $categoryId,
            'category_name' => $categoryName,
            'admin_id' => $adminId,
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * Log category update
     */
    public function logCategoryUpdated(int $categoryId, string $categoryName, array $changes, int $adminId): void
    {
        Log::channel('audit')->info('Category Updated', [
            'action' => 'category_updated',
            'category_id' => $categoryId,
            'category_name' => $categoryName,
            'changes' => $changes,
            'admin_id' => $adminId,
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * Log user creation
     */
    public function logUserCreated(int $userId, string $userName, string $email, string $role, int $adminId): void
    {
        Log::channel('audit')->info('User Created', [
            'action' => 'user_created',
            'user_id' => $userId,
            'user_name' => $userName,
            'email' => $email,
            'role' => $role,
            'admin_id' => $adminId,
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * Log user update
     */
    public function logUserUpdated(int $userId, string $userName, array $changes, int $adminId): void
    {
        Log::channel('audit')->info('User Updated', [
            'action' => 'user_updated',
            'user_id' => $userId,
            'user_name' => $userName,
            'changes' => $changes,
            'admin_id' => $adminId,
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * Log user deletion
     */
    public function logUserDeleted(int $userId, string $userName, int $adminId): void
    {
        Log::channel('audit')->info('User Deleted', [
            'action' => 'user_deleted',
            'user_id' => $userId,
            'user_name' => $userName,
            'admin_id' => $adminId,
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * Generic log method for custom audit events
     */
    public function log(string $action, string $entityType, int $entityId, array $data, ?int $userId = null): void
    {
        Log::channel('audit')->info(ucwords(str_replace('_', ' ', $action)), [
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'data' => $data,
            'user_id' => $userId,
            'timestamp' => now()->toIso8601String()
        ]);
    }
}
