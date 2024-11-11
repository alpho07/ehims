<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\Page;
use App\Models\Order;
use App\Models\Facility;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class OrderApiManagementPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cloud';
    protected static ?string $navigationLabel = 'Order API Management';
    protected static ?string $slug = 'order-api-management';
    protected static ?string $navigationGroup = 'Inventory Management';
    protected static string $resource = OrderResource::class;


    public $facility;
    public $year;
    public $month;

    protected function getFormSchema(): array
    {
        return [
            Select::make('facility')
                ->label('Facility')
                ->options(Facility::all()->pluck('facility_name', 'id'))
                ->required(),

            TextInput::make('year')
                ->label('Year')
                ->required()
                ->numeric()
                ->minLength(4)
                ->maxLength(4),

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
        ];
    }

    protected function getActions(): array
    {
        return [
            Action::make('fetchOrders')
                ->label('Fetch Orders')
                ->action('fetchOrders')
                ->color('primary')
                ->requiresConfirmation(),
        ];
    }

    public function fetchFacilityOrders($mfl_code, $month, $year, Request $request)
    {
        $token = $request->query('token');
        $validation = $this->isValidToken($token);

        if ($validation !== true) {
            return $validation; // Return the JSON error response if token validation fails
        }



        // Find the facility by mfl_code
        $facility = Facility::where('mfl_code', $mfl_code)->first();

        // If facility not found, return an error response
        if (!$facility) {
            return response()->json(['error' => '404 Facility not found'], 404);
        }

        $facilityId = $facility->id;


        // Fetch orders based on the facility ID, year, and month
        $orders = Order::with(['facility', 'orderItems'])
            ->where('facility_id', $facilityId)
            ->where('year', $year)
            ->where('month', $month)
            ->get();

        // If no orders found, return an error response
        if ($orders->isEmpty()) {
            return response()->json(['error' => '404 Order not found'], 404);
        }



        // Format data for the API response
        $formattedOrders = $orders->flatMap(function ($order) use ($month, $year) {
            return $order->orderItems->map(function ($item) use ($order, $month, $year) {
                return [
                    'id' => $item->id,
                    'name' => $item->inventoryProduct->item,
                    'code' => $item->inventoryProduct->system_code,
                    'requested_qty' => $item->requested_stock,
                    'Period' => $month . '/' . $year,
                    'mflCode' => $order->facility->mfl_code,
                    'hubName' => $order->facility->facility_name,
                ];
            });
        })->values();

        return response()->json([
            'orders' => [
                'order_period' => $month . '/' . $year,
                'request' => $formattedOrders,
            ],
        ]);
    }



    public function fetchAllOrders($month, $year, Request $request)
    {

        $token = $request->query('token');
        $validation = $this->isValidToken($token);

        if ($validation !== true) {
            return $validation; // Return the JSON error response if token validation fails
        }

        // Fetch orders based on the facility ID, year, and month
        $orders = Order::with(['facility', 'orderItems'])
            ->where('year', $year)
            ->where('month', $month)
            ->get();


        // If no orders found, return an error response
        if ($orders->isEmpty()) {
            return response()->json(['error' => '404 Order not found'], 404);
        }



        // Format data for the API response
        $formattedOrders = $orders->flatMap(function ($order) use ($month, $year) {
            return $order->orderItems->map(function ($item) use ($order, $month, $year) {
                return [
                    'id' => $item->id,
                    'name' => $item->inventoryProduct->item,
                    'code' => $item->inventoryProduct->system_code,
                    'requestedQty' => $item->requested_stock,
                    'Period' => $month . '/' . $year,
                    'mflCode' => $order->facility->mfl_code,
                    'facilityName' => $order->facility->facility_name,
                ];
            });
        })->values();

        return response()->json([
            'orders' => [
                'order_period' => $month . '/' . $year,
                'request' => $formattedOrders,
            ],
        ]);
    }


    protected function isValidToken($token)
    {
        // Check if a token was provided
        if (!$token) {
            return response()->json(['error' => '501 User Not authenticated'], 401);
        }

        // Load the expected token from the .env file
        $expectedToken = env('API_ACCESS_TOKEN');

        // Compare the provided token with the expected token
        if ($token !== $expectedToken) {
            return response()->json(['error' => '500 Invalid Token'], 403);
        }

        return true;
    }





    protected static string $view = 'filament.resources.ordes-api';
}
