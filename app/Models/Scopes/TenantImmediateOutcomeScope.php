<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantImmediateOutcomeScope implements Scope
{
    /**
     * Apply the mapping constraints to ensure institutions only view assigned IOs.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // 1. Context check for non-HTTP sequences (Console commands or Database Seeds)
        if (!auth()->check()) {
            return;
        }

        $user = auth()->user();

        // 🌟 2. PRIVILEGED ACCESS BYPASS: Let internal management review all 11 core definitions
        $fiuPrivilegedRoles = ['fiu_admin', 'fiu_reviewer'];
        if (in_array($user->role, $fiuPrivilegedRoles)) {
            return;
        }

        // 🔒 3. AUTOMATED LOOKUP BOUNDARY: Scope queries down strictly to rows bound via pivot keys
        $builder->whereHas('institutions', function (Builder $query) use ($user) {
            $query->where('institutions.id', $user->institution_id);
        });
    }
}