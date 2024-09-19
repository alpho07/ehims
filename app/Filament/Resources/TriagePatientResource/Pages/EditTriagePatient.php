<?php

namespace App\Filament\Resources\TriagePatientResource\Pages;

use App\Filament\Resources\TriagePatientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTriagePatient extends EditRecord
{
    protected static string $resource = TriagePatientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
