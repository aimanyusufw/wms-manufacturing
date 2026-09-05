<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\QualityInspection;
use Illuminate\Auth\Access\HandlesAuthorization;

class QualityInspectionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:QualityInspection');
    }

    public function view(AuthUser $authUser, QualityInspection $qualityInspection): bool
    {
        return $authUser->can('View:QualityInspection');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:QualityInspection');
    }

    public function update(AuthUser $authUser, QualityInspection $qualityInspection): bool
    {
        return $authUser->can('Update:QualityInspection');
    }

    public function delete(AuthUser $authUser, QualityInspection $qualityInspection): bool
    {
        return $authUser->can('Delete:QualityInspection');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:QualityInspection');
    }

    public function restore(AuthUser $authUser, QualityInspection $qualityInspection): bool
    {
        return $authUser->can('Restore:QualityInspection');
    }

    public function forceDelete(AuthUser $authUser, QualityInspection $qualityInspection): bool
    {
        return $authUser->can('ForceDelete:QualityInspection');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:QualityInspection');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:QualityInspection');
    }

    public function replicate(AuthUser $authUser, QualityInspection $qualityInspection): bool
    {
        return $authUser->can('Replicate:QualityInspection');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:QualityInspection');
    }
}
