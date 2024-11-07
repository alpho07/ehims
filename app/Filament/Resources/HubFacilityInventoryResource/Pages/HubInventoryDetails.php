<?php

namespace App\Filament\Resources\HubFacilityInventoryResource\Pages;

use App\Filament\Resources\HubFacilityInventoryResource;
use App\Models\HubFacilityInventory;
use Filament\Resources\Pages\Page;

class HubInventoryDetails extends Page
{
    protected static string $resource = HubFacilityInventoryResource::class;

    public $hubInventory;
    public $spokeQuantities;

    protected static ?string $title = 'Hub and Spoke Inventory Details';

    public function mount($id)
    {

        // Fetch the main hub inventory record
        $this->hubInventory = HubFacilityInventory::findOrFail($id);


        // Fetch quantities of spokes for the selected hub
        $this->spokeQuantities = HubFacilityInventory::where('item_id', $this->hubInventory->item_id)
            ->whereIn('facility_id', $this->hubInventory->facility->children()->pluck('id'))
            ->get();
    }

    protected function getViewData(): array
    {
        return [
            'hubInventory' => $this->hubInventory,
            'spokeQuantities' => $this->spokeQuantities,
        ];
    }

    protected static string $view = 'filament.resources.hub-inventory-details';
}
