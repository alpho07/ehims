<?php

namespace App\Filament\Resources\EyewearResource\Pages;

use App\Filament\Resources\EyewearResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEyewears extends ListRecords
{
    protected static string $resource = EyewearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
