<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;

class AuditService
{
    public function logChange(string $action, string $entityType, ?int $entityId, array $before, array $after, User $user): AuditLog
    {
        return AuditLog::create([
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'before_json' => $before,
            'after_json' => $after,
            'user_id' => $user->id,
        ]);
    }
}
