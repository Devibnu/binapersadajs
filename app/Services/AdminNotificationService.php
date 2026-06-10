<?php

namespace App\Services;

use App\Models\BlogComment;
use App\Models\ContactMessage;
use App\Models\EmailCenterMessage;
use App\Models\InquiryQuotation;
use App\Models\InvoiceReport;
use App\Models\Lead;
use App\Models\PortalConversation;
use App\Models\ProjectReport;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class AdminNotificationService
{
    public function forUser(?User $user): array
    {
        if (! $user) {
            return [
                'totalUnread' => 0,
                'latestNotifications' => collect(),
            ];
        }

        $sources = collect([
            $this->iqmConversationNotifications($user),
            $this->contactMessageNotifications($user),
            $this->leadNotifications($user),
            $this->blogCommentNotifications($user),
            $this->emailNotifications($user),
        ])->flatten(1);

        return [
            'totalUnread' => $this->totalUnread($user),
            'latestNotifications' => $sources
                ->sortByDesc('time')
                ->take(10)
                ->values(),
        ];
    }

    private function iqmConversationNotifications(User $user): Collection
    {
        if (! Schema::hasTable('portal_conversations')) {
            return collect();
        }

        $modules = collect([
            PortalConversation::MODULE_INQUIRY => [
                'permission' => 'inquiry-quotation.view',
                'title' => 'IQM Question',
                'description' => 'Client bertanya pada Inquiry',
                'icon' => 'fa-comments',
                'badge_color' => 'bg-gradient-info',
            ],
            PortalConversation::MODULE_PROJECT_REPORT => [
                'permission' => 'project-reports.view',
                'title' => 'IQM Question',
                'description' => 'Client bertanya pada Project Report',
                'icon' => 'fa-clipboard-list',
                'badge_color' => 'bg-gradient-info',
            ],
            PortalConversation::MODULE_INVOICE_REPORT => [
                'permission' => 'invoice-reports.view',
                'title' => 'IQM Question',
                'description' => 'Client bertanya pada Invoice Report',
                'icon' => 'fa-file-invoice-dollar',
                'badge_color' => 'bg-gradient-info',
            ],
        ])->filter(fn (array $module) => $user->canAccess($module['permission']));

        return $modules->flatMap(function (array $module, string $moduleType) {
            return PortalConversation::query()
                ->where('module_type', $moduleType)
                ->where('sender_type', 'client')
                ->where('is_read', false)
                ->latest()
                ->limit(10)
                ->get()
                ->map(function (PortalConversation $conversation) use ($module, $moduleType) {
                    $url = $this->iqmConversationUrl($moduleType, (int) $conversation->module_id);

                    if (! $url) {
                        return null;
                    }

                    return $this->notificationItem(
                        'iqm_conversation',
                        $module['title'],
                        $module['description'],
                        $conversation->created_at,
                        $url,
                        $module['icon'],
                        $module['badge_color']
                    );
                })
                ->filter();
        })->values();
    }

    private function contactMessageNotifications(User $user): Collection
    {
        if (! $user->canAccess('contact-messages.view') || ! Schema::hasTable('contact_messages')) {
            return collect();
        }

        return ContactMessage::query()
            ->where('status', 'unread')
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn (ContactMessage $message) => $this->notificationItem(
                'contact_message',
                'Contact Message',
                'Pesan baru dari ' . $message->name,
                $message->created_at,
                route('paneladmin.contact-messages.show', $message),
                'fa-envelope',
                'bg-gradient-warning'
            ));
    }

    private function leadNotifications(User $user): Collection
    {
        if (! $user->canAccess('leads.view') || ! Schema::hasTable('leads')) {
            return collect();
        }

        return Lead::query()
            ->where('status', 'new')
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn (Lead $lead) => $this->notificationItem(
                'lead',
                'Lead Baru',
                'Lead baru dari ' . ($lead->name ?: $lead->email),
                $lead->created_at,
                route('paneladmin.leads.show', $lead),
                'fa-user-plus',
                'bg-gradient-success'
            ));
    }

    private function blogCommentNotifications(User $user): Collection
    {
        if (! $user->canAccess('blog-comments.view') || ! Schema::hasTable('blog_comments')) {
            return collect();
        }

        return BlogComment::query()
            ->whereNull('parent_id')
            ->where('status', 'pending')
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn (BlogComment $comment) => $this->notificationItem(
                'blog_comment',
                'Blog Comment',
                'Komentar menunggu persetujuan',
                $comment->created_at,
                route('paneladmin.blog-comments.show', $comment),
                'fa-comment-dots',
                'bg-gradient-danger'
            ));
    }

    private function emailNotifications(User $user): Collection
    {
        $query = $this->emailBaseQuery($user);

        if (! $query) {
            return collect();
        }

        return $query
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn (EmailCenterMessage $message) => $this->notificationItem(
                'email',
                'Email Baru',
                'Email masuk baru' . ($message->from_email ? ' dari ' . $message->from_email : ''),
                $message->created_at,
                route('paneladmin.email-center.index', ['folder' => 'inbox', 'message' => $message->id]),
                'fa-inbox',
                'bg-gradient-primary'
            ));
    }

    private function iqmConversationUrl(string $moduleType, int $moduleId): ?string
    {
        return match ($moduleType) {
            PortalConversation::MODULE_PROJECT_REPORT => ProjectReport::query()->whereKey($moduleId)->exists()
                ? route('paneladmin.project-reports.show', $moduleId)
                : null,
            PortalConversation::MODULE_INVOICE_REPORT => InvoiceReport::query()->whereKey($moduleId)->exists()
                ? route('paneladmin.invoice-reports.show', $moduleId)
                : null,
            default => InquiryQuotation::query()->whereKey($moduleId)->exists()
                ? route('paneladmin.inquiry-quotations.show', $moduleId)
                : null,
        };
    }

    private function notificationItem(
        string $type,
        string $title,
        string $description,
        $time,
        string $url,
        string $icon,
        string $badgeColor
    ): array {
        return [
            'type' => $type,
            'title' => $title,
            'description' => $description,
            'time' => $time,
            'url' => $url,
            'icon' => $icon,
            'badge_color' => $badgeColor,
        ];
    }

    private function totalUnread(User $user): int
    {
        return $this->iqmConversationCount($user)
            + $this->contactMessageCount($user)
            + $this->leadCount($user)
            + $this->blogCommentCount($user)
            + $this->emailCount($user);
    }

    private function iqmConversationCount(User $user): int
    {
        if (! Schema::hasTable('portal_conversations')) {
            return 0;
        }

        $moduleTypes = collect([
            PortalConversation::MODULE_INQUIRY => 'inquiry-quotation.view',
            PortalConversation::MODULE_PROJECT_REPORT => 'project-reports.view',
            PortalConversation::MODULE_INVOICE_REPORT => 'invoice-reports.view',
        ])->filter(fn (string $permission) => $user->canAccess($permission))->keys()->all();

        if (empty($moduleTypes)) {
            return 0;
        }

        return PortalConversation::query()
            ->whereIn('module_type', $moduleTypes)
            ->where('sender_type', 'client')
            ->where('is_read', false)
            ->count();
    }

    private function contactMessageCount(User $user): int
    {
        return $user->canAccess('contact-messages.view') && Schema::hasTable('contact_messages')
            ? ContactMessage::where('status', 'unread')->count()
            : 0;
    }

    private function leadCount(User $user): int
    {
        return $user->canAccess('leads.view') && Schema::hasTable('leads')
            ? Lead::where('status', 'new')->count()
            : 0;
    }

    private function blogCommentCount(User $user): int
    {
        return $user->canAccess('blog-comments.view') && Schema::hasTable('blog_comments')
            ? BlogComment::whereNull('parent_id')->where('status', 'pending')->count()
            : 0;
    }

    private function emailCount(User $user): int
    {
        return $this->emailBaseQuery($user)?->count() ?? 0;
    }

    private function emailBaseQuery(User $user)
    {
        if (! $user->canAccess('email-center.view') || ! Schema::hasTable('email_center_messages')) {
            return null;
        }

        $query = EmailCenterMessage::query();

        if (Schema::hasColumn('email_center_messages', 'folder')) {
            $query->where('folder', 'inbox');
        }

        return $query->where(function ($query) {
            if (Schema::hasColumn('email_center_messages', 'status')) {
                $query->orWhereIn('status', ['unread', 'new']);
            }

            if (Schema::hasColumn('email_center_messages', 'seen')) {
                $query->orWhere('seen', false);
            }
        });
    }
}
