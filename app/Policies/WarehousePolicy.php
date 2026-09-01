<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Warehouse;
use Illuminate\Auth\Access\HandlesAuthorization;

class WarehousePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Warehouse');
    }

    public function view(AuthUser $authUser, Warehouse $warehouse): bool
    {
        return $authUser->can('View:Warehouse');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Warehouse');
    }

    public function update(AuthUser $authUser, Warehouse $warehouse): bool
    {
        return $authUser->can('Update:Warehouse');
    }

    public function delete(AuthUser $authUser, Warehouse $warehouse): bool
    {
        return $authUser->can('Delete:Warehouse');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Warehouse');
    }

    public function restore(AuthUser $authUser, Warehouse $warehouse): bool
    {
        return $authUser->can('Restore:Warehouse');
    }

    public function forceDelete(AuthUser $authUser, Warehouse $warehouse): bool
    {
        return $authUser->can('ForceDelete:Warehouse');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Warehouse');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Warehouse');
    }

    public function replicate(AuthUser $authUser, Warehouse $warehouse): bool
    {
        return $authUser->can('Replicate:Warehouse');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Warehouse');
    }

}