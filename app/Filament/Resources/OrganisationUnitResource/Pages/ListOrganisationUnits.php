<?php

namespace App\Filament\Resources\OrganisationUnitResource\Pages;

use App\Filament\Resources\OrganisationUnitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrganisationUnits extends ListRecords
{
    protected static string $resource = OrganisationUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
