<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Wezlo\FilamentApproval\Models\ApprovalFlow;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApprovalFlowPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ApprovalFlow');
    }

    public function view(AuthUser $authUser, ApprovalFlow $approvalFlow): bool
    {
        return $authUser->can('View:ApprovalFlow');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ApprovalFlow');
    }

    public function update(AuthUser $authUser, ApprovalFlow $approvalFlow): bool
    {
        return $authUser->can('Update:ApprovalFlow');
    }

    public function delete(AuthUser $authUser, ApprovalFlow $approvalFlow): bool
    {
        return $authUser->can('Delete:ApprovalFlow');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ApprovalFlow');
    }

    public function restore(AuthUser $authUser, ApprovalFlow $approvalFlow): bool
    {
        return $authUser->can('Restore:ApprovalFlow');
    }

    public function forceDelete(AuthUser $authUser, ApprovalFlow $approvalFlow): bool
    {
        return $authUser->can('ForceDelete:ApprovalFlow');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ApprovalFlow');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ApprovalFlow');
    }

    public function replicate(AuthUser $authUser, ApprovalFlow $approvalFlow): bool
    {
        return $authUser->can('Replicate:ApprovalFlow');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ApprovalFlow');
    }

}