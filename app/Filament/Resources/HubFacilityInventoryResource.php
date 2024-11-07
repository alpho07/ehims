<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HubFacilityInventoryResource\Pages;
use App\Models\Facility;
use App\Models\HubFacilityInventory;
use App\Models\InventoryProduct;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HubFacilityInventoryResource extends Resource
{
    protected static ?string $model = HubFacilityInventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Inventory Management';
    protected static ?string $navigationLabel = 'Facility Inventory';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('item_id')
                    ->label('Item')
                    ->options(InventoryProduct::query()->pluck('description', 'id'))
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn(callable $set, $state) => $set('available_quantity', InventoryProduct::find($state)?->quantity ?? 0)),

                TextInput::make('available_quantity')
                    ->label('Available Quantity')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->required(),

                Select::make('facility_id')
                    ->label('Facility')
                    ->options(Facility::query()->pluck('facility_name', 'id'))
                    ->required(),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item.item')->label('Item'),
                TextColumn::make('item.description')->label('Description'),
                TextColumn::make('item.system_code')->label('Code'),
                // TextColumn::make('item.type')->label('Type'),
                TextColumn::make('quantity_available')
                    ->label('Total Available Quantity')
                    ->sortable()
                    ->getStateUsing(fn($record) => $record->quantity_available),
                TextColumn::make('facility.facility_name')->label('Facility'),
                TextColumn::make('facility.facility_type')->label('Facility Type'),
                TextColumn::make('facility.mfl_code')->label('MFL Code'),
            ])
            ->filters([
                Filter::make('Only Parent Facilities')
                ->query(fn (Builder $query) => $query->whereHas('facility', function ($query) {
                    $query->where('facility_type','hub');
                })) ->default('hub'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('view_details')
                    ->label('View Details')
                    ->color('primary')
                    ->icon('heroicon-o-plus-circle')
                    ->action(function ($record) {
                        // Redirect to the triage page
                        $url = static::getUrl('details', ['id' => $record]);
                        return redirect($url);
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    protected function getTitle(): string
    {
        return 'Product:' ;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHubFacilityInventories::route('/'),
            'create' => Pages\CreateHubFacilityInventory::route('/create'),
            'edit' => Pages\EditHubFacilityInventory::route('/{record}/edit'),
            'details' => Pages\HubInventoryDetails::route('/{id}/details'),
        ];
    }
}
