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
        $recordName = $properties['record_name'] ?? ($subject instanceof Model ? $this->recordName($subject) : null);
        $properties = array_filter(array_merge($properties, [
            'role_name' => $user?->role?->name,
            'user_email' => $user?->email,
            'model_type' => $subject instanceof Model ? $subject::class : null,
            'model_id' => $subject instanceof Model ? $subject->getKey() : null,
            'record_name' => $recordName,
        ]), fn ($value) => $value !== null && $value !== '');

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

    private function recordName(Model $subject): ?string
    {
        foreach ([
            'name',
            'title',
            'job_title',
            'invoice_no',
            'project_no',
            'inquiry_number',
            'subject',
            'email',
            'username',
            'original_name',
            'company_name',
            'page_key',
        ] as $attribute) {
            if (filled($subject->{$attribute} ?? null)) {
                return (string) $subject->{$attribute};
            }
        }

        if (method_exists($subject, 'displayTitle')) {
            return (string) $subject->displayTitle();
        }

        return null;
    }
}
