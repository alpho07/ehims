<div class="p-4 bg-white shadow rounded mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <h2 class="text-lg font-semibold">Patient Information</h2>
        <p><strong>Name:</strong> {{ $patientData['prescription']->patient->name }}</p>
        <p><strong>Age:</strong> {{ $patientData['prescription']->patient->age }}</p>
        <p><strong>Gender:</strong> {{ $patientData['prescription']->patient->gender }}</p>
    </div>
    <div>
        <h2 class="text-lg font-semibold">Triage Details</h2>
        <p><strong>Blood Pressure:</strong> {{ $patientData['prescription']->triage->blood_pressure }}</p>
        <p><strong>Heart Rate:</strong> {{ $patientData['prescription']->triage->heart_rate }}</p>
        <p><strong>Temperature:</strong> {{ $patientData['prescription']->triage->temperature }}</p>
    </div>
    <div class="col-span-1 md:col-span-2">
        <h2 class="text-lg font-semibold">Consultation History</h2>
        @foreach ($patientData['prescription']->consultationHistory as $history)
            <p><strong>Date:</strong> {{ $history->date }}</p>
            <p><strong>Notes:</strong> {{ $history->notes }}</p>
        @endforeach
    </div>
    <div class="col-span-1 md:col-span-2">
        <h2 class="text-lg font-semibold">Current Status: <span class="font-bold">{{ $patientData['status'] }}</span></h2>
    </div>
</div>

{{ $this->table }}
