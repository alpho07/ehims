<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Facility;
use App\Models\HubFacilityInventory;
use App\Models\InventoryProduct;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class OrderCreate extends Page
{
    protected static string $resource = OrderResource::class;

    protected static ?string $title = 'Create New Order';

    public int $OrderId;
    public int $facilityId;
    public int $month;
    public int $year;
    public string $facility_name;
    public array $orderItems = [];

    public function mount(int $OrderId, int $facilityId, int $month, int $year)
    {
        $this->OrderId = $OrderId;
        $this->facilityId = $facilityId;
        $this->month = $month;
        $this->year = $year;

        // Load inventory items for the selected facility
        // $inventoryItems = HubFacilityInventory::with('item')->where('facility_id', $facilityId)->get();
        $facility = Facility::find($facilityId);
        $this->facility_name = $facility->facility_name;

        // Check for existing order for this period
        $order = Order::where('facility_id', $facilityId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        // Load saved order items if order exists
        $savedOrderItems = $order
            ? OrderItem::where('order_id', $order->id)->get()->keyBy('inventory_product_id')
            : collect();

        // Load base inventory items from HubFacilityInventory
        $orderItems = HubFacilityInventory::with('item')
            ->where('facility_id', $facilityId)
            ->get()
            ->map(function ($inventory) use ($savedOrderItems) {
                // Check if this inventory item has a saved order item
                $savedItem = $savedOrderItems->get($inventory->item_id);

                return [
                    'inventory_product_id' => $inventory->item_id,
                    'description' => $inventory->item->description,
                    'inventory_product_name' => $inventory->item->item,
                    'available_stock' => $inventory->available_quantity,
                    'requested_stock' => $savedItem ? $savedItem->requested_stock : 0, // Prefill if saved, else default to 0
                ];
            })
            ->toArray();


        // Fill the form with the combined data
        $this->form->fill([
            'facility_name' => $this->facility_name,
            'facilityId' => $this->facilityId,
            'month' => $this->month,
            'year' => $this->year,
            'orderItems' => $orderItems,
        ]);

        //dd($this->form->getState('orderItems'));
    }


    protected function getHeaderActions(): array
    {
        return [
            Action::make('addProduct')
                ->label('Add Product')
                ->icon('heroicon-o-plus')
                ->form([
                    Select::make('inventory_product_id')
                        ->label('Product')
                        ->options(
                            InventoryProduct::query()
                                ->get()
                                ->mapWithKeys(function ($product) {
                                    return [
                                        $product->id => "{$product->item} - {$product->description} - {$product->system_code}",
                                    ];
                                })
                        )
                        ->searchable()
                        ->required(),

                    TextInput::make('available_quantity')
                        ->label('Quantity')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->readOnly(),
                ])
                ->action(function (array $data) {
                    $facilityId = session('user_facility_id');

                    HubFacilityInventory::create([
                        'item_id' => $data['inventory_product_id'],
                        'facility_id' => $facilityId,
                        'available_quantity' => 0,
                    ]);

                    Notification::make()
                        ->title('Product  Successfully Added')
                        ->success()
                        ->send();

                      return redirect(request()->header('Referer'));
                    //$this->form->fill(['orderItems' => $this->getOrderItems()]);


                }),
        ];
    }

    protected function getOrderItems(): array
    {
        // Retrieve the hub facility inventory items for the facility
        $inventoryItems = HubFacilityInventory::with('item')
            ->where('facility_id', $this->facilityId)
            ->get();

        // Fetch existing order items for the selected month and year
        $existingOrderItems = OrderItem::whereHas('order', function ($query) {
            $query->where('facility_id', $this->facilityId)
                ->whereMonth('created_at', $this->month)
                ->whereYear('created_at', $this->year);
        })
            ->get()
            ->keyBy('inventory_product_id'); // Use inventory_product_id as the key for quick lookup

        // Map inventory items to the expected repeater format
        return $inventoryItems->map(function ($inventory) use ($existingOrderItems) {
            $existingItem = $existingOrderItems->get($inventory->item_id);

            return [
                'inventory_product_id' => $inventory->item_id,
                'inventory_product_name' => $inventory->item->item,
                'available_stock' => $inventory->available_quantity,
                'requested_stock' => $existingItem ? $existingItem->requested_stock : 0,
            ];
        })->toArray();
    }



    protected function getFormSchema(): array
    {
        return [


            Repeater::make('orderItems')
                ->label('Order Items')
                ->schema([
                    TextInput::make('inventory_product_id')
                        ->label('Product Id')
                        ->readOnly()
                        ->extraAttributes(['class' => 'border px-1 py-2']),

                    TextInput::make('description')
                        ->label('Product Description')
                        ->disabled()
                        ->extraAttributes(['class' => 'border px-1 py-2']),

                    TextInput::make('inventory_product_name')
                        ->label('Inventory Product Name')
                        ->disabled()
                        ->extraAttributes(['class' => 'border px-1 py-2']),

                    TextInput::make('available_stock')
                        ->label('Available Stock')
                        ->readOnly()
                        ->extraAttributes(['class' => 'border px-1 py-2']),

                    TextInput::make('requested_stock')
                        ->label('Requested Stock')
                        ->numeric()
                        ->minValue(0)
                        ->extraAttributes(['class' => 'border px-1 py-2']),
                ])
                ->columns(5)
                ->addable(false)
                ->deletable(false)
                ->reorderable(false)
                ->inlineLabel(false)
                ->disableLabel(true)
                ->extraAttributes(['class' => 'w-full border-collapse'])

        ];
    }

    public function saveOrder()
    {
        $this->validate([
            'facilityId' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000',
            'orderItems.*.inventory_product_id' => 'required|integer',
            'orderItems.*.requested_stock' => 'required|integer|min:0',
        ]);



        \DB::transaction(function () {
            // Step 1: Save or find the main order
            $order = Order::firstOrCreate([
                'facility_id' => $this->facilityId,
                'month' => $this->month,
                'year' => $this->year,
            ]);

            // Step 2: Save or update each order item from the repeater
            foreach ($this->form->getState()['orderItems'] as $item) {
                OrderItem::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'inventory_product_id' => $item['inventory_product_id'],
                    ],
                    [
                        'requested_stock' => $item['requested_stock'],
                        'available_stock' => $item['available_stock'],
                    ]
                );
            }
        });


        Notification::make()
            ->title('Order Successfully Generated')
            ->success()
            ->send();


    }

    protected static string $view = 'filament.resources.order-create';
}
