<x-filament::page>
    <div class="grid grid-cols-2 gap-4">
        <!-- Left side: Patient Information and Consultation History -->
        <div class="space-y-6">
            <x-filament::card>
                <h2 class="text-lg font-bold">Patient Information</h2>
                <p><strong>Name:</strong> {{ $visit->patient->name }}</p>
                <p><strong>Hospital Number:</strong> {{ $visit->patient->hospital_number }}</p>
            </x-filament::card>

            <x-filament::card>
                <h2 class="text-lg font-bold">Consultation History</h2>
                @foreach ($formData['consultations'] as $consultation)
                    <div>
                        <p><strong>Clinic:</strong> {{ $consultation['clinic_name'] }}</p>
                        <p><strong>Date:</strong> {{ $consultation['consultation_date'] }}</p>
                        <p><strong>Summary:</strong> {{ $consultation['summary'] }}</p>
                    </div>
                @endforeach
            </x-filament::card>
        </div>

        <!-- Right side: Prescription Details and Actions -->
        <div class="space-y-6">
            <x-filament::card>
                <h2 class="text-lg font-bold">Prescription Details</h2>
                <p>{{ $formData['prescription'] }}</p>
            </x-filament::card>

            <x-filament::button action="approve" color="success">
                Approve Prescription
            </x-filament::button>

            <x-filament::button action="reject" color="danger">
                Reject Prescription
            </x-filament::button>
        </div>
    </div>
</x-filament::page>
