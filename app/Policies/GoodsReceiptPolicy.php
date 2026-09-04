<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\GoodsReceipt;
use Illuminate\Auth\Access\HandlesAuthorization;

class GoodsReceiptPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:GoodsReceipt');
    }

    public function view(AuthUser $authUser, GoodsReceipt $goodsReceipt): bool
    {
        return $authUser->can('View:GoodsReceipt');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:GoodsReceipt');
    }

    public function update(AuthUser $authUser, GoodsReceipt $goodsReceipt): bool
    {
        return $authUser->can('Update:GoodsReceipt');
    }

    public function delete(AuthUser $authUser, GoodsReceipt $goodsReceipt): bool
    {
        return $authUser->can('Delete:GoodsReceipt');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:GoodsReceipt');
    }

    public function restore(AuthUser $authUser, GoodsReceipt $goodsReceipt): bool
    {
        return $authUser->can('Restore:GoodsReceipt');
    }

    public function forceDelete(AuthUser $authUser, GoodsReceipt $goodsReceipt): bool
    {
        return $authUser->can('ForceDelete:GoodsReceipt');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:GoodsReceipt');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:GoodsReceipt');
    }

    public function replicate(AuthUser $authUser, GoodsReceipt $goodsReceipt): bool
    {
        return $authUser->can('Replicate:GoodsReceipt');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:GoodsReceipt');
    }

}