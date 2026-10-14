<?php

namespace App\Policies;

use App\Models\CaseRecord;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CasePolicy
{
    /**
     * Determine whether the user can view any cases.
     * Admin, Viewer, and Engineer can all view cases.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'viewer', 'engineer'], true);
    }

    /**
     * Determine whether the user can view the specific case.
     */
    public function view(User $user, CaseRecord $caseRecord): bool
    {
        return in_array($user->role, ['admin', 'viewer', 'engineer'], true);
    }

    /**
     * Determine whether the user can create cases.
     * Admin and Engineer can create; Viewer cannot.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'engineer'], true);
    }

    /**
     * Determine whether the user can update the case.
     * Admin and Engineer can update; Viewer cannot.
     */
    public function update(User $user, CaseRecord $caseRecord): bool
    {
        return in_array($user->role, ['admin', 'engineer'], true);
    }

    /**
     * Determine whether the user can delete the case.
     * ONLY Admin can delete. Viewer and Engineer cannot delete.
     */
    public function delete(User $user, CaseRecord $caseRecord): bool
    {
        return $user->role === 'admin';
    }
}
