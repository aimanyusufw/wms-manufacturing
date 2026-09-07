<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PutawayTask;
use Illuminate\Auth\Access\HandlesAuthorization;

class PutawayTaskPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PutawayTask');
    }

    public function view(AuthUser $authUser, PutawayTask $putawayTask): bool
    {
        return $authUser->can('View:PutawayTask');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PutawayTask');
    }

    public function update(AuthUser $authUser, PutawayTask $putawayTask): bool
    {
        return $authUser->can('Update:PutawayTask');
    }

    public function delete(AuthUser $authUser, PutawayTask $putawayTask): bool
    {
        return $authUser->can('Delete:PutawayTask');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PutawayTask');
    }

    public function restore(AuthUser $authUser, PutawayTask $putawayTask): bool
    {
        return $authUser->can('Restore:PutawayTask');
    }

    public function forceDelete(AuthUser $authUser, PutawayTask $putawayTask): bool
    {
        return $authUser->can('ForceDelete:PutawayTask');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PutawayTask');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PutawayTask');
    }

    public function replicate(AuthUser $authUser, PutawayTask $putawayTask): bool
    {
        return $authUser->can('Replicate:PutawayTask');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PutawayTask');
    }

}