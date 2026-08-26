<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Uom;
use Illuminate\Auth\Access\HandlesAuthorization;

class UomPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Uom');
    }

    public function view(AuthUser $authUser, Uom $uom): bool
    {
        return $authUser->can('View:Uom');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Uom');
    }

    public function update(AuthUser $authUser, Uom $uom): bool
    {
        return $authUser->can('Update:Uom');
    }

    public function delete(AuthUser $authUser, Uom $uom): bool
    {
        return $authUser->can('Delete:Uom');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Uom');
    }

    public function restore(AuthUser $authUser, Uom $uom): bool
    {
        return $authUser->can('Restore:Uom');
    }

    public function forceDelete(AuthUser $authUser, Uom $uom): bool
    {
        return $authUser->can('ForceDelete:Uom');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Uom');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Uom');
    }

    public function replicate(AuthUser $authUser, Uom $uom): bool
    {
        return $authUser->can('Replicate:Uom');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Uom');
    }

}