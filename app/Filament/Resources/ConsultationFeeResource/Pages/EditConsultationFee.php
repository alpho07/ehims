<?php

namespace App\Filament\Resources\ConsultationFeeResource\Pages;

use App\Filament\Resources\ConsultationFeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConsultationFee extends EditRecord
{
    protected static string $resource = ConsultationFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
