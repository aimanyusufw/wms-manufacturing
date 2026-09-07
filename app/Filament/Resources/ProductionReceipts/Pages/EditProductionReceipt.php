<?php

namespace App\Filament\Resources\ProductionReceipts\Pages;

use App\Filament\Resources\ProductionReceipts\ProductionReceiptResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Wezlo\FilamentApproval\Concerns\HasApprovalsResource;

class EditProductionReceipt extends EditRecord
{
    use HasApprovalsResource;

    protected static string $resource = ProductionReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...$this->getApprovalHeaderActions(),
            DeleteAction::make(),
        ];
    }
}
