<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantDocumentScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // 🔒 Bypass restriction rules entirely for central FIU Administrative staff
        if (Auth::check() && Auth::user()->role === 'fiu_admin') {
            return;
        }

        // 🏢 Institutional Tenant Sandboxing Rule
        if (Auth::check() && isset(Auth::user()->institution_id)) {
            $tenantId = Auth::user()->institution_id;

            $builder->where(function (Builder $query) use ($tenantId) {
                // Match documents explicitly mapped to this tenant in the relationship pivot graph
                $query->whereHas('institutions', function (Builder $sub) use ($tenantId) {
                    $sub->where('institutions.id', $tenantId);
                })
                // Fallback protection check to maintain older data row architecture integrity
                ->orWhere('documents.institution_id', $tenantId);
            });
        } else {
            // 🛑 If a user is unauthenticated or has no valid context, force an empty dataset return
            $builder->whereRaw('1 = 0');
        }
    }
}