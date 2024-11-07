<?php

namespace App\Filament\Resources\InventoryProductsResource\Pages;

use App\Filament\Resources\InventoryProductsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInventoryProducts extends EditRecord
{
    protected static string $resource = InventoryProductsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
