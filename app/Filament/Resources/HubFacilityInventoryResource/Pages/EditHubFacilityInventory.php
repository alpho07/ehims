<?php

namespace App\Filament\Resources\HubFacilityInventoryResource\Pages;

use App\Filament\Resources\HubFacilityInventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHubFacilityInventory extends EditRecord
{
    protected static string $resource = HubFacilityInventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
