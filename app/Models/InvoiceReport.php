<?php

namespace App\Models;

use App\Models\Concerns\HasIqmPortalAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceReport extends Model
{
    use HasIqmPortalAccess;

    protected $fillable = [
        'client',
        'invoice_no',
        'po_wo_no',
        'job_title',
        'invoice_date',
        'quantity',
        'unit',
        'unit_price',
        'total_amount',
        'visibility',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('invoice_date')->orderByDesc('created_at');
    }

    public function iqmUsers(): BelongsToMany
    {
        return $this->belongsToMany(IqmUser::class, 'invoice_report_iqm_user')
            ->withTimestamps();
    }

    public function portalConversations(): HasMany
    {
        return $this->hasMany(PortalConversation::class, 'module_id')
            ->where('module_type', PortalConversation::MODULE_INVOICE_REPORT)
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

    public function formattedDate(): string
    {
        return $this->invoice_date ? $this->invoice_date->translatedFormat('d F Y') : '-';
    }
}
