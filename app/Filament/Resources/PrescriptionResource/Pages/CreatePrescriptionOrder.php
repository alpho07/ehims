<?php

namespace App\Filament\Resources\PrescriptionResource\Pages;

use App\Filament\Resources\PrescriptionResource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\Action;
use App\Models\PrescriptionOrderItem;
use App\Models\Prescription;
use App\Models\HubFacilityInventory;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;

class CreatePrescriptionOrder extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $resource = PrescriptionResource::class;
    protected static string $view = 'filament.resources.create-prescription-order';

    public int $record;
    public array $patientData = [];

    public function mount(int $record)
    {
        $this->record = $record;

        // Load prescription, patient, and triage data
        $prescription = Prescription::with('visit')->find($this->record);



        $this->patientData = [
            'prescription' => $prescription,
            'status' => $this->isOrderDispensed() ? 'Dispensed' : 'Pending',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addPrescriptionOrderItem')
                ->label('Add Prescription Order Item')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->hidden(fn() => $this->isOrderDispensed()) // Hide if dispensed
                ->modalHeading('Add New Prescription Order Item')
                ->form([
                    TextInput::make('prescription_order_id')
                        ->default($this->record)
                        ->disabled()
                        ->hidden(),

                    Select::make('inventory_product_id')
                        ->label('Product (Lens or Frame)')
                        ->options(function () {
                            $hubFacilityId = session('user_facility_id');
                            return HubFacilityInventory::with('item')
                                ->where('facility_id', $hubFacilityId)
                                ->get()
                                ->mapWithKeys(function ($inventory) {
                                    $product = $inventory->item;
                                    return [
                                        $product->id => "{$product->item} - {$product->description} - {$product->system_code} (Available: {$inventory->available_quantity})",
                                    ];
                                });
                        })
                        ->reactive()
                        ->afterStateUpdated(fn($set, $state) => $set('available_stock', $this->getAvailableQuantity($state)))
                        ->searchable()
                        ->required(),

                    TextInput::make('available_stock')
                        ->label('Available Qty')
                        ->numeric()
                        ->readOnly()
                        ->required(),

                    TextInput::make('requested_stock')
                        ->label('Qty. Requested')
                        ->numeric()
                        ->required()
                        ->disabled(fn($get) => $get('available_stock') === 0)
                        ->helperText(fn($get) => $get('available_stock') === 0 ? 'This product is out of stock and cannot be prescribed.' : ''),
                ])
                ->action(function (array $data): void {
                    PrescriptionOrderItem::create([
                        'prescription_order_id' => $this->record,
                        'inventory_product_id' => $data['inventory_product_id'],
                        'requested_stock' => $data['requested_stock'],
                    ]);
                }),

            Action::make('dispenseOrder')
                ->label('Dispense Order')
                ->icon('heroicon-o-check')
                ->color('success')
                ->hidden(fn() => PrescriptionOrderItem::where('prescription_order_id', $this->record)->count() === 0 || $this->isOrderDispensed())
                ->requiresConfirmation() // Require confirmation before dispensing
                ->action(function (): void {
                    // Retrieve all prescription order items for the current order
                    $orderItems = PrescriptionOrderItem::where('prescription_order_id', $this->record)->get();

                    foreach ($orderItems as $item) {
                        $inventory = HubFacilityInventory::where('facility_id', session('user_facility_id'))
                            ->where('item_id', $item->inventory_product_id)
                            ->first();

                        if (!$inventory || $item->requested_stock > $inventory->available_quantity) {
                            Notification::make()
                                ->title('Error')
                                ->body("Insufficient stock for {$inventory->item->item}. Requested: {$item->requested_stock}, Available: {$inventory->available_quantity}.")
                                ->warning()
                                ->send();
                            return;
                        }
                    }

                    foreach ($orderItems as $item) {
                        $inventory = HubFacilityInventory::where('facility_id', session('user_facility_id'))
                            ->where('item_id', $item->inventory_product_id)
                            ->first();

                        if ($inventory) {
                            $inventory->available_quantity -= $item->requested_stock;
                            $inventory->save();
                        }
                    }

                    // Mark the prescription as "dispensed"
                    Prescription::where('id', $this->record)->update(['status' => 'dispensed']);

                    // Display success notification
                    Notification::make()
                        ->title('Success')
                        ->body('Order dispensed successfully, and stock updated.')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function getTableQuery()
    {
        return PrescriptionOrderItem::with('inventoryProduct')->where('prescription_order_id', $this->record);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('inventory_product_id')
                ->label('Product ID')
                ->sortable(),
            TextColumn::make('inventoryProduct.item.item')
                ->label('Item')
                ->sortable(),
            TextColumn::make('inventoryProduct.item.description')
                ->label('Description')
                ->sortable(),
            TextColumn::make('available_quantity')
                ->label('Available Qty (Current)')
                ->getStateUsing(function ($record) {
                    $hubFacilityId = session('user_facility_id');
                    $inventory = HubFacilityInventory::where('facility_id', $hubFacilityId)
                        ->where('item_id', $record->inventory_product_id)
                        ->first();
                    return $inventory ? $inventory->available_quantity : 0;
                }),
            TextColumn::make('requested_stock')
                ->label('Qty. Requested')
                ->sortable(),
            TextColumn::make('status')
                ->label('Status')
                ->getStateUsing(fn($record) => $this->isOrderDispensed() ? 'Dispensed' : 'Pending')
                ->sortable(),
        ];
    }

    // Helper function to check if order is already dispensed
    protected function isOrderDispensed(): bool
    {
        return Prescription::where('id', $this->record)
            ->where('status', 'dispensed')
            ->exists();
    }

    private function getAvailableQuantity($productId)
    {
        $hubFacilityId = session('user_facility_id');

        $inventory = HubFacilityInventory::where('facility_id', $hubFacilityId)
            ->where('item_id', $productId)
            ->first();

        return $inventory ? $inventory->available_quantity : 0;
    }
}
