<?php

namespace App\Filament\Resources\ProductionReceipts\Pages;

use App\Filament\Resources\ProductionReceipts\ProductionReceiptResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateProductionReceipt extends CreateRecord
{
    protected static string $resource = ProductionReceiptResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['received_by'] = Auth::id();

        return $data;
    }
}
