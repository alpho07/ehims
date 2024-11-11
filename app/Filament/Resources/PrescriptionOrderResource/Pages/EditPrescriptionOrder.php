<?php

namespace App\Filament\Resources\PrescriptionOrderResource\Pages;

use App\Filament\Resources\PrescriptionOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrescriptionOrder extends EditRecord
{
    protected static string $resource = PrescriptionOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
