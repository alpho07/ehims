<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClinicResource\Pages;
use App\Filament\Resources\ClinicResource\RelationManagers;
use App\Models\Clinic;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ClinicResource extends Resource
{
    protected static ?string $model = Clinic::class;
    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $navigationGroup = 'Admin Management';



    public static function shouldRegisterNavigation(): bool
    {
        // Check if the user has permission to view any appointments
        return Auth::user()->hasAnyPermission([
            'view_any_clinic',

            // Add other permissions as needed
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Clinic Name')
                    ->required(),

                Forms\Components\Select::make('day')
                    ->label('Day of the Week')
                    ->options([
                        'Monday' => 'Monday',
                        'Tuesday' => 'Tuesday',
                        'Wednesday' => 'Wednesday',
                        'Thursday' => 'Thursday',
                        'Friday' => 'Friday',
                    ])
                    ->required(),

                Forms\Components\Select::make('doctor_id')
                    ->label('Assign Doctor (Optional)')
                    ->options(User::whereHas('roles', function ($query) {
                        $query->where('name', 'doctor');  // Filter users with role 'doctor'
                    })->pluck('name', 'id'))
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Clinic Name'),
                Tables\Columns\TextColumn::make('day')->label('Available Day'),
                Tables\Columns\TextColumn::make('doctor.name')->label('Assigned Doctor'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()
                ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClinics::route('/'),
            'create' => Pages\CreateClinic::route('/create'),
            'edit' => Pages\EditClinic::route('/{record}/edit'),
        ];
    }
}
