<x-filament::page>
    <h2 class="text-xl font-semibold mb-4">Create Order No.#{{$OrderId}} for {{ $facility_name }}</h2>
    <p>Order Period: {{ $month . '/' . $year }}</p>

    {{ $this->form }}

    <button wire:click="saveOrder" class="btn btn-secondary mt-4" wire:loading.attr="disabled" style="background: gray">
        Save Order
    </button>
    <!-- Loading indicator -->
    <span wire:loading wire:target="saveOrder" class="ml-2 text-blue-600">Saving...</span>
</x-filament::page>
