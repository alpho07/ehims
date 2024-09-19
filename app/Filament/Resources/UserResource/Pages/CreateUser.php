<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterSave(array $data): array
    {
        $roles = $data['roles'] ?? [];
        unset($data['roles']);

        $this->record = User::create($data);
        $this->record->syncRoles($roles);

        return $data;
    }

}
