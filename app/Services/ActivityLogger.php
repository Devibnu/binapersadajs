<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public function log(
        string $action,
        string $module,
        ?string $description = null,
        mixed $subject = null,
        array $properties = []
    ): ActivityLog {
        $user = Auth::user();
        $request = request();

        return ActivityLog::create([
            'user_id' => $user?->getAuthIdentifier(),
            'user_name' => $user?->name,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'subject_type' => $subject instanceof Model ? $subject::class : null,
            'subject_id' => $subject instanceof Model ? $subject->getKey() : null,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'properties' => $properties ?: null,
        ]);
    }
}
