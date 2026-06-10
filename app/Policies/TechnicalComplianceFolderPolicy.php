<?php

namespace App\Policies;

use App\Models\ReportingInstitution;
use App\Models\TechnicalComplianceFolder;
use App\Models\User;

class TechnicalComplianceFolderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isFiuUser() || $user->role === User::ROLE_INSTITUTION_USER;
    }

    public function view(User $user, TechnicalComplianceFolder $folder): bool
    {
        if ($user->isFiuUser()) {
            return true;
        }

        return $user->role === User::ROLE_INSTITUTION_USER
            && $user->reportingInstitution
            && $folder->institutions->contains(fn (ReportingInstitution $institution) => $institution->is($user->reportingInstitution));
    }

    public function create(User $user): bool
    {
        return $user->isFiuUser();
    }
}
