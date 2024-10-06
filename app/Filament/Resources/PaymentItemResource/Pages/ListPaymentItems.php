<?php

namespace App\Filament\Resources\PaymentItemResource\Pages;

use App\Filament\Resources\PaymentItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentItems extends ListRecords
{
    protected static string $resource = PaymentItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
