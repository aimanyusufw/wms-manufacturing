<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ProductionReceipt;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductionReceiptPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProductionReceipt');
    }

    public function view(AuthUser $authUser, ProductionReceipt $productionReceipt): bool
    {
        return $authUser->can('View:ProductionReceipt');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProductionReceipt');
    }

    public function update(AuthUser $authUser, ProductionReceipt $productionReceipt): bool
    {
        return $authUser->can('Update:ProductionReceipt');
    }

    public function delete(AuthUser $authUser, ProductionReceipt $productionReceipt): bool
    {
        return $authUser->can('Delete:ProductionReceipt');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ProductionReceipt');
    }

    public function restore(AuthUser $authUser, ProductionReceipt $productionReceipt): bool
    {
        return $authUser->can('Restore:ProductionReceipt');
    }

    public function forceDelete(AuthUser $authUser, ProductionReceipt $productionReceipt): bool
    {
        return $authUser->can('ForceDelete:ProductionReceipt');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProductionReceipt');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProductionReceipt');
    }

    public function replicate(AuthUser $authUser, ProductionReceipt $productionReceipt): bool
    {
        return $authUser->can('Replicate:ProductionReceipt');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProductionReceipt');
    }

}