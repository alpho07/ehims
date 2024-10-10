<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Clinic;
use App\Models\Service;
use App\Models\ServiceType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static ?string $navigationIcon = 'heroicon-o-lifebuoy';
    protected static ?string $navigationGroup = 'Admin Management';


    public static function shouldRegisterNavigation(): bool
    {
        // Check if the user has permission to view any appointments
        return Auth::user()->can('view_any_service');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('service_type_id')
                ->label('Service Type')
                ->options(ServiceType::all()->pluck('name', 'id')->toArray())  // Load service types
                ->searchable()
                ->required(),

                Forms\Components\Select::make('clinic_id')
                    ->label('Clinic')
                    ->relationship('clinic', 'name')  // Attach service to a clinic
                    ->required()
                    //->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('serviceType.name')->label('Service Name')->searchable() ,
                Tables\Columns\TextColumn::make('clinic.name')->label('Clinic')->searchable()
            ])

            ->filters([
                /*Tables\Filters\SelectFilter::make('clinic')
                    ->label('Filter by Clinic')
                    ->options(function () {
                        // Add "All" as a default option
                        return ['all' => 'All'] + Clinic::all()->pluck('name', 'id')->toArray();
                    })
                    ->default('Eye Clinic(Mondays)')  // Set "All" as the default option
                    ->query(function ($query, $data) {
                        if ($data !== 'all') {
                            $query->where('clinic_id', $data);
                        }
                    }),*/
            ])
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
