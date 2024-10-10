<?php

namespace App\Filament\Resources\ClinicFormDataResource\Pages;

use App\Filament\Resources\ClinicFormDataResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClinicFormData extends ListRecords
{
    protected static string $resource = ClinicFormDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
