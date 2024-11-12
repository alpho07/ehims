<?php

namespace App\Filament\Resources\TriagePatientResource\Pages;

use App\Filament\Resources\TriagePatientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListTriagePatients extends ListRecords
{
    protected static string $resource = TriagePatientResource::class;


    protected function getHeaderActions(): array
    {
        return [
           // Actions\CreateAction::make(),
        ];
    }

    protected function shouldDisableCreate(): bool
    {
        return true; // Disables the create action
    }
}
