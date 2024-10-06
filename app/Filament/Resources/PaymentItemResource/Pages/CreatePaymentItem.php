<?php

namespace App\Filament\Resources\PaymentItemResource\Pages;

use App\Filament\Resources\PaymentItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentItem extends CreateRecord
{
    protected static string $resource = PaymentItemResource::class;
}
