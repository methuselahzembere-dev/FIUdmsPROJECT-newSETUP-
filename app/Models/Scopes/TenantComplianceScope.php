<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantComplianceScope implements Scope
{
    /**
     * Apply the multi-tenant isolation guard matrix to all Eloquent builders.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // 1. If executed outside an HTTP context (like running DB Seeds or Artisan Console commands), bypass constraints
        if (!auth()->check()) {
            return;
        }

        $user = auth()->user();

        //  2. ELITE BYPASS GUARD: If they possess any internal FIU management roles, clear them to see all files
        // (Ensure '$user->role' matches your exact column name, such as '$user->role_name' or '$user->role_id')
        $fiuPrivilegedRoles = ['fiu_admin', 'fiu_reviewer'];
        if (in_array($user->role, $fiuPrivilegedRoles)) {
            return;
        }

        // 🔒 3. EXTERNAL TENANT HARD LOCK: Enforce strict multi-tenant database boundaries
        $builder->where('visibility_scope', 'shared')
                ->where(function ($query) use ($user) {
                    $query->where('institution_id', $user->institution_id)
                          ->orWhereNull('institution_id'); // Preserves access to global template assets
                });
    }
}