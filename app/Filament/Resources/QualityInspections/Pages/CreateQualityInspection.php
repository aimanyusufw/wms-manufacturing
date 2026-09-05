<?php

namespace App\Filament\Resources\QualityInspections\Pages;

use App\Filament\Resources\QualityInspections\QualityInspectionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateQualityInspection extends CreateRecord
{
    protected static string $resource = QualityInspectionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['inspected_by'] = Auth::id();

        return $data;
    }
}
