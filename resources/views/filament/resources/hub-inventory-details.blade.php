<!-- resources/views/filament/pages/hub-inventory-details.blade.php -->

<x-filament::page>
    <h3 class="text-xl font-semibold mb-4">{{ $hubInventory->facility->facility_name }} Inventory Details</h3>
    <h2 class="text-sm font-semibold mb-4">{{'Product: '. $hubInventory->item->item .' - ('.$hubInventory->item->description .'-'.$hubInventory->item->system_code. ')' }} </h2>

    <table class="min-w-full bg-black border">
        <thead>
            <tr>
                <th class="px-4 py-2 border">Facility</th>
                <th class="px-4 py-2 border text-right">Quantity Available</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background: gray;">
                <td class="px-4 py-2 border font-bold">{{ $hubInventory->facility->facility_name .' - '.$hubInventory->facility->mfl_code }} (Hub - Main)</td>
                <td class="px-4 py-2 border text-right">{{ $hubInventory->available_quantity }}</td>
            </tr>
            @foreach ($spokeQuantities as $spokeInventory)
                <tr>
                    <td class="px-4 py-2 border">{{ $spokeInventory->facility->facility_name .' - '.$spokeInventory->facility->mfl_code  }}</td>
                    <td class="px-4 py-2 border text-right">{{ $spokeInventory->available_quantity }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="font-bold">
                <td class="px-4 py-2 border">Total (Hub + Spokes)</td>
                <td class="px-4 py-2 border text-right">
                    {{ $hubInventory->available_quantity + $spokeQuantities->sum('available_quantity') }}
                </td>
            </tr>
        </tfoot>
    </table>
</x-filament::page>
