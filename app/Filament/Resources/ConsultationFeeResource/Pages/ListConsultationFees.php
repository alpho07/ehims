<?php

namespace App\Filament\Resources\ConsultationFeeResource\Pages;

use App\Filament\Resources\ConsultationFeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConsultationFees extends ListRecords
{
    protected static string $resource = ConsultationFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
