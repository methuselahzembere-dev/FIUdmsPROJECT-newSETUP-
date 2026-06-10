<?php

namespace App\Policies;

use App\Models\TechnicalComplianceDocument;
use App\Models\User;

class TechnicalComplianceDocumentPolicy
{
    public function view(User $user, TechnicalComplianceDocument $document): bool
    {
        if ($user->isFiuUser()) {
            return true;
        }

        return $user->role === User::ROLE_INSTITUTION_USER
            && $document->reporting_institution_id === $user->reporting_institution_id
            && $document->folder->institutions->contains('id', $user->reporting_institution_id);
    }

    public function create(User $user): bool
    {
        return $user->isFiuUser() || $user->role === User::ROLE_INSTITUTION_USER;
    }

    public function update(User $user, TechnicalComplianceDocument $document): bool
    {
        return $user->role === User::ROLE_INSTITUTION_USER
            && $document->reporting_institution_id === $user->reporting_institution_id
            && $document->canBeEditedByInstitution();
    }

    public function review(User $user): bool
    {
        return $user->isFiuUser();
    }
}
