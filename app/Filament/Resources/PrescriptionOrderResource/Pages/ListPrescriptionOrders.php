<?php

namespace App\Filament\Resources\PrescriptionOrderResource\Pages;

use App\Filament\Resources\PrescriptionOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrescriptionOrders extends ListRecords
{
    protected static string $resource = PrescriptionOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
