<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class IqmUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'company_name',
        'pic_name',
        'username',
        'email',
        'phone',
        'password',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'last_login_at' => 'datetime',
    ];

    public function inquiries(): HasMany
    {
        return $this->hasMany(InquiryQuotation::class, 'iqm_user_id');
    }

    public function inquiryQuotations(): BelongsToMany
    {
        return $this->belongsToMany(InquiryQuotation::class, 'inquiry_quotation_iqm_user')
            ->withTimestamps();
    }

    public function projectReports(): BelongsToMany
    {
        return $this->belongsToMany(ProjectReport::class, 'project_report_iqm_user')
            ->withTimestamps();
    }

    public function invoiceReports(): BelongsToMany
    {
        return $this->belongsToMany(InvoiceReport::class, 'invoice_report_iqm_user')
            ->withTimestamps();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
