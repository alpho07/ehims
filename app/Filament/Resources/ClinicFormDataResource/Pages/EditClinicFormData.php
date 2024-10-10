<?php

namespace App\Filament\Resources\ClinicFormDataResource\Pages;

use App\Filament\Resources\ClinicFormDataResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClinicFormData extends EditRecord
{
    protected static string $resource = ClinicFormDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
