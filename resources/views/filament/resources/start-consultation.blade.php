<x-filament::page>
    <form wire:submit.prevent="submit">
        {{ $this->form }}
        <p style="margin-top: 20px"></p>
        {{ $this->getConsultationForm() }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit">
                Save Consultation
            </x-filament::button>
        </div>
    </form>
</x-filament::page>
