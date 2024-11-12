<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Facility;
use App\Models\Order;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Inventory Management';
    protected static ?string $navigationLabel = 'Facility Orders';

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
                Select::make('facility_id')
                    ->label('Facility')
                    ->options(Facility::query()->get()->mapWithKeys(function ($facility) {
                        return [
                            $facility->id => "{$facility->facility_name} - {$facility->mfl_code}",
                        ];
                    }))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn($state, callable $set) => $set('inventory_items', $state)),

                Select::make('month')
                    ->label('Month')
                    ->options([
                        1 => 'January',
                        2 => 'February',
                        3 => 'March',
                        4 => 'April',
                        5 => 'May',
                        6 => 'June',
                        7 => 'July',
                        8 => 'August',
                        9 => 'September',
                        10 => 'October',
                        11 => 'November',
                        12 => 'December',
                    ])
                    ->required(),

                TextInput::make('year')
                    ->label('Year')
                    ->numeric()
                    ->default(date('Y'))
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('facility.facility_name')->label('Facility'),
                Tables\Columns\TextColumn::make('month')->label('Month'),
                Tables\Columns\TextColumn::make('year')->label('Year'),
                Tables\Columns\TextColumn::make('orderItems_count')
                    ->label('Total Items')
                    ->counts('orderItems'),
            ])
            ->filters([
                SelectFilter::make('facility_id')
                    ->label('Facility')
                    ->relationship('facility', 'facility_name')
                   ,
                Tables\Filters\SelectFilter::make('month')
                    ->label('Month')
                    ->options([
                        1 => 'January',
                        2 => 'February',
                        3 => 'March',
                        4 => 'April',
                        5 => 'May',
                        6 => 'June',
                        7 => 'July',
                        8 => 'August',
                        9 => 'September',
                        10 => 'October',
                        11 => 'November',
                        12 => 'December',
                    ]),
                Tables\Filters\SelectFilter::make('year')
                    ->label('Year')
                    ->options(
                        range(date('Y'), date('Y') - 5) // Allows selection from recent years
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('new_order')
                ->label('New Order')
                ->color('primary')
                ->icon('heroicon-o-plus-circle')
                ->action(function ($record) {
                    // Redirect to the triage page
                    $url = static::getUrl('order-create', [
                        'OrderId'=>$record->id,
                        'facilityId' => $record->facility_id,
                        'month' => $record->month,
                        'year' => $record->year,
                    ]);
                    return redirect($url);
                })->visible(fn() => !Order::orderExists(request('facility_id'), request('month'), request('year')))

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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'order-create' => Pages\OrderCreate::route('/{OrderId}/{facilityId}/{month}/{year}/create-order'),
            'order-api' => Pages\OrderApiManagementPage::route('/{facilityId}/{month}/{year}/v1'),
        ];
    }
}
