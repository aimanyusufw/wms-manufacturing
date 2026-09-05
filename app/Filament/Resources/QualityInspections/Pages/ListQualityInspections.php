<?php

namespace App\Filament\Resources\QualityInspections\Pages;

use App\Filament\Resources\QualityInspections\QualityInspectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQualityInspections extends ListRecords
{
    protected static string $resource = QualityInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
