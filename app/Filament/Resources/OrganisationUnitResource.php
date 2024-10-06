<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganisationUnitResource\Pages;
use App\Filament\Resources\OrganisationUnitResource\RelationManagers;
use App\Models\OrganisationUnit;
use App\Models\OrganizationUnit;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrganisationUnitResource extends Resource
{
    protected static ?string $model = OrganisationUnit::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Organization Units';
    protected static ?string $navigationGroup = 'Admin Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('uid')
                    ->label('UID')
                    ->required()
                    ->unique(),
                TextInput::make('code')
                    ->label('Code')
                    ->nullable(),
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
                Select::make('parent_id')
                    ->label('Parent Unit')
                    ->relationship('parent', 'name')
                    ->nullable(),
                TextInput::make('path')
                    ->label('Path')
                    ->disabled()  // Auto-calculated
                    ->required(),
                TextInput::make('hierarchy_level')
                    ->label('Hierarchy Level')
                    ->disabled()  // Auto-calculated
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uid')
                    ->label('UID'),
                TextColumn::make('name')
                    ->label('Name'),
                TextColumn::make('parent.name')
                    ->label('Parent Unit'),
                TextColumn::make('path')
                    ->label('Path'),
                TextColumn::make('hierarchy_level')
                    ->label('Hierarchy Level'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrganisationUnits::route('/'),
            'create' => Pages\CreateOrganisationUnit::route('/create'),
            'edit' => Pages\EditOrganisationUnit::route('/{record}/edit'),
        ];
    }
}
