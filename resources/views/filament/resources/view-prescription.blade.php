<x-filament::page>
    {{-- Prescription Form --}}
    @if ($this->status == 'pending')
        <x-filament::badge color="secondary" icon="heroicon-m-pencil">
            Pending
        </x-filament::badge>
    @elseif($this->status == 'approved')
        <x-filament::badge color="success" icon="heroicon-m-check-badge">
            Approved
        </x-filament::badge>
    @elseif($this->status == 'rejected')
        <x-filament::badge color="danger" icon="heroicon-m-x-circle">
            Rejected
        </x-filament::badge>
    @endif

    {{ $this->form }}

</x-filament::page>
