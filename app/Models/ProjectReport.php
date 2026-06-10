<?php

namespace App\Models;

use App\Models\Concerns\HasIqmPortalAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectReport extends Model
{
    use HasIqmPortalAccess;

    protected $fillable = [
        'project_no',
        'job_title',
        'quotation_price',
        'contract_number',
        'contract_price',
        'invoice_amount',
        'corporation',
        'department',
        'user_pic',
        'remark',
        'e_wo_status',
        'report_status',
        'visibility',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'quotation_price' => 'decimal:2',
        'contract_price' => 'decimal:2',
        'invoice_amount' => 'decimal:2',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('created_at');
    }

    public function iqmUsers(): BelongsToMany
    {
        return $this->belongsToMany(IqmUser::class, 'project_report_iqm_user')
            ->withTimestamps();
    }

    public function portalConversations(): HasMany
    {
        return $this->hasMany(PortalConversation::class, 'module_id')
            ->where('module_type', PortalConversation::MODULE_PROJECT_REPORT)
            ->orderBy('created_at');
    }

    public function isPublic(): bool
    {
        return $this->visibility === 'public';
    }

    public function formattedMoney(?string $field): string
    {
        $value = $field ? $this->{$field} : null;

        if ($value === null || $value === '') {
            return '-';
        }

        return 'Rp ' . number_format((float) $value, 0, ',', '.');
    }
}
