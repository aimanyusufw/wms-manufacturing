<?php

namespace App\Filament\Resources\GoodsReceipts\Pages;

use App\Filament\Resources\GoodsReceipts\GoodsReceiptResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Wezlo\FilamentApproval\Concerns\HasApprovalsResource;

class EditGoodsReceipt extends EditRecord
{
    use HasApprovalsResource;

    protected static string $resource = GoodsReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...$this->getApprovalHeaderActions(),
            DeleteAction::make(),
        ];
    }
}
