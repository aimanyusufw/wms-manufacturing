<?php

namespace App\Filament\Resources\PutawayTasks\Pages;

use App\Filament\Resources\PutawayTasks\PutawayTaskResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePutawayTasks extends ManageRecords
{
    protected static string $resource = PutawayTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->slideOver(),
        ];
    }
}
