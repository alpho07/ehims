<x-filament::page>
    <form wire:submit.prevent="submit">
        {{ $this->form }}
        <div class="mt-4">
            <x-filament::button type="submit" color="primary" wire:loading.attr="disabled" style="margin-top:20px;">
                <span wire:loading.remove wire:target="submit">Submit</span>
                <span wire:loading.flex wire:target="submit">
                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.372 0 0 5.372 0 12h4z">
                        </path>
                    </svg>
                    Submitting...
                </span>
            </x-filament::button>
            <x-filament::button color="secondary" tag="a" :href="static::getResource()::getUrl('index')">
                Cancel
            </x-filament::button>
        </div>
    </form>
</x-filament::page>
