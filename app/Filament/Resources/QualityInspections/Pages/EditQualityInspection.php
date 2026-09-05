<?php

namespace App\Filament\Resources\QualityInspections\Pages;

use App\Filament\Resources\QualityInspections\QualityInspectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Wezlo\FilamentApproval\Concerns\HasApprovalsResource;

class EditQualityInspection extends EditRecord
{
    use HasApprovalsResource;

    protected static string $resource = QualityInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...$this->getApprovalHeaderActions(),
            DeleteAction::make(),
        ];
    }
}
