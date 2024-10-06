<?php

namespace App\Filament\Resources\OrganisationUnitResource\Pages;

use App\Filament\Resources\OrganisationUnitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrganisationUnit extends EditRecord
{
    protected static string $resource = OrganisationUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
