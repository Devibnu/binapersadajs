<?php

namespace App\Models\Concerns;

use App\Models\IqmUser;
use Illuminate\Database\Eloquent\Builder;

trait HasIqmPortalAccess
{
    public function scopeVisibleToIqmUser(Builder $query, IqmUser|int $user): Builder
    {
        $iqmUserId = $user instanceof IqmUser ? $user->id : $user;

        return $query->where(function (Builder $query) use ($iqmUserId) {
            $query->where('visibility', 'public')
                ->orWhereHas('iqmUsers', function (Builder $query) use ($iqmUserId) {
                    $query->where('iqm_users.id', $iqmUserId);
                });
        });
    }

    public function canBeViewedByIqmUser(IqmUser|int $user): bool
    {
        if (array_key_exists('is_active', $this->attributes) && ! $this->is_active) {
            return false;
        }

        $iqmUserId = $user instanceof IqmUser ? $user->id : $user;

        return $this->isPublic()
            || $this->iqmUsers()->where('iqm_users.id', $iqmUserId)->exists();
    }
}
