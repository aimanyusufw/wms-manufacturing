<?php

namespace App\Filament\Resources\ProductionReceipts\Pages;

use App\Filament\Resources\ProductionReceipts\ProductionReceiptResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductionReceipts extends ListRecords
{
    protected static string $resource = ProductionReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
