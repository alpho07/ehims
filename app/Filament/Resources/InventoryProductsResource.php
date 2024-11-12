<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryProductsResource\Pages;
use App\Models\InventoryProduct;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class InventoryProductsResource extends Resource
{
    protected static ?string $model = InventoryProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Inventory Management';
    protected static ?string $navigationLabel = 'Inventory Products';

    public static function shouldRegisterNavigation(): bool
    {
        // Check if the user has permission to view any appointments
        return Auth::user()->hasAnyPermission([
            'view_any_hub::facility::inventory',

            // Add other permissions as needed
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('item')->required(),
                TextInput::make('description')->required(),
                TextInput::make('system_code')->required()->unique(),
                Select::make('gender')
                    ->options(['Male' => 'Male', 'Female' => 'Female', 'Unisex' => 'Unisex'])
                    ->required(),
                TextInput::make('price')->numeric()->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item')->sortable()->searchable(),
                TextColumn::make('description')->sortable(),
                TextColumn::make('system_code')->sortable()->searchable(),
                TextColumn::make('gender')->sortable(),
                TextColumn::make('price')->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListInventoryProducts::route('/'),
            'create' => Pages\CreateInventoryProducts::route('/create'),
            'edit' => Pages\EditInventoryProducts::route('/{record}/edit'),
        ];
    }
}
