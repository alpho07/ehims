<?php

namespace App\Filament\Resources\ConsultationTypeResource\Pages;

use App\Filament\Resources\ConsultationTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConsultationTypes extends ListRecords
{
    protected static string $resource = ConsultationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
