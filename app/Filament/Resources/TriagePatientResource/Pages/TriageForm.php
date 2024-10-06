<?php

namespace App\Filament\Resources\TriagePatientResource\Pages;

use App\Filament\Resources\TriagePatientResource;
use App\Models\Visit;
use App\Models\Triage;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Notifications\Notification;

class TriageForm extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $resource = TriagePatientResource::class;
    protected static string $view = 'filament.resources.triage-form';

    public Visit $visit;

    // Define public properties
    public $patient_name;
    public $hospital_number;
    public $file_number;
    public $date;
    public $time;
    public $age;
    public $temperature;
    public $pulse_rate;
    public $blood_sugar;
    public $resp;
    public $bp_systolic;
    public $bp_diastolic;
    public $bp_status;
    public $bp_time;
    public $distance_aided;
    public $distance_unaided;
    public $distance_pinhole;
    public $near_aided;
    public $near_unaided;
    public $iop_right;
    public $iop_left;
    public $nurse_name;
    public $nurse_signature;
    public $weight;
    public $height;

    public function mount($record): void
    {
        $this->visit = Visit::with('patient')->findOrFail($record);

        // Initialize public properties
        $this->patient_name = $this->visit->patient->name;
        $this->hospital_number = $this->visit->patient->hospital_number;
        $this->file_number = $this->visit->patient->file_number;

        $this->date = now()->toDateString();
        $this->time = now()->format('H:i');
        $this->age = $this->visit->patient->dob
            ? \Carbon\Carbon::parse($this->visit->patient->dob)->age
            : null;
        $this->nurse_name = auth()->user()->name ?? '';
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\Section::make('Patient Information')
                        ->schema([
                            Forms\Components\TextInput::make('patient_name')
                                ->label('Patient Name')
                                ->disabled(),
                            Forms\Components\TextInput::make('hospital_number')
                                ->label('Hospital Number')
                                ->disabled(),
                            Forms\Components\TextInput::make('file_number')
                                ->label('File Number')
                                ->disabled(),
                        ]),
                    Forms\Components\Section::make('Triage Information')
                        ->schema([
                            Forms\Components\DatePicker::make('date')
                                ->label('Date')
                                ->required()
                                ->hidden(),
                            Forms\Components\TimePicker::make('time')
                                ->label('Time')
                                ->required()
                                ->hidden(),
                            Forms\Components\TextInput::make('age')
                                ->label('Age')
                                ->numeric()
                                ->required(),
                            Forms\Components\TextInput::make('temperature')
                                ->label('Temperature (°C)')
                                ->numeric()
                                ->required(),
                            Forms\Components\TextInput::make('pulse_rate')
                                ->label('Pulse Rate (bpm)')
                                ->numeric()
                                ->required(),
                            Forms\Components\TextInput::make('blood_sugar')
                                ->label('Blood Sugar (mg/dL)')
                                ->numeric()
                                ->required(),
                            Forms\Components\TextInput::make('weight')
                                ->label('Weight (kg)')
                                ->numeric()
                                ->required(),
                            Forms\Components\TextInput::make('height')
                                ->label('Height (cm)')
                                ->numeric()
                                ->required(),
                            Forms\Components\TextInput::make('bp_systolic')
                                ->label('Systolic BP')
                                ->numeric()
                                ->required(),
                            Forms\Components\TextInput::make('bp_diastolic')
                                ->label('Diastolic BP')
                                ->numeric()
                                ->required(),
                            Forms\Components\TextInput::make('bp_status')
                                ->label('BP Status')
                                ->required(),
                            Forms\Components\TimePicker::make('bp_time')
                                ->label('BP Time')
                                ->required(),
                            Forms\Components\Fieldset::make('Visual Acuity')
                                ->schema([
                                    Forms\Components\TextInput::make('distance_aided')
                                        ->label('Distance - Aided')
                                        ->numeric()
                                        ->required(),
                                    Forms\Components\TextInput::make('distance_unaided')
                                        ->label('Distance - Unaided')
                                        ->numeric()
                                        ->required(),
                                    Forms\Components\TextInput::make('distance_pinhole')
                                        ->label('Distance - Pinhole')
                                        ->numeric()
                                        ->required(),
                                    Forms\Components\TextInput::make('near_aided')
                                        ->label('Near - Aided')
                                        ->numeric()
                                        ->required(),
                                    Forms\Components\TextInput::make('near_unaided')
                                        ->label('Near - Unaided')
                                        ->numeric()
                                        ->required(),
                                ]),
                            Forms\Components\Fieldset::make('Intraocular Pressure')
                                ->schema([
                                    Forms\Components\TextInput::make('iop_right')
                                        ->label('Right Eye (mmHg)')
                                        ->numeric()
                                        ->required(),
                                    Forms\Components\TextInput::make('iop_left')
                                        ->label('Left Eye (mmHg)')
                                        ->numeric()
                                        ->required(),
                                ]),
                            Forms\Components\TextInput::make('nurse_name')
                                ->label('Nurse Name')
                                ->required(),
                        ]),
                ]),
        ];
    }

    public function submit()
    {
        // Save the triage data
        $data = Triage::create([
            'visit_id' => $this->visit->id,
            'date' => $this->date,
            'time' => $this->time,
            'age' => $this->age,
            'temperature' => $this->temperature,
            'pulse_rate' => $this->pulse_rate,
            'blood_sugar' => $this->blood_sugar,
            'resp' => 0,
            'bp_systolic' => $this->bp_systolic,
            'bp_diastolic' => $this->bp_diastolic,
            'bp_status' => $this->bp_status,
            'bp_time' => $this->bp_time,
            'distance_aided' => $this->distance_aided,
            'distance_unaided' => $this->distance_unaided,
            'distance_pinhole' => $this->distance_pinhole,
            'near_aided' => $this->near_aided,
            'near_unaided' => $this->near_unaided,
            'iop_right' => $this->iop_right,
            'iop_left' => $this->iop_left,
            'nurse_name' => $this->nurse_name,
            'nurse_signature' => 'N/A',
            'weight' => $this->weight,
            'height' => $this->height,
        ]);

        // Optionally, update the visit status
        $this->visit->update(['status' => 'triaged']);

        Notification::make()
            ->title('Triage information saved successfully!')
            ->success()
            ->send();

        // Redirect back to the triage patients list
        return redirect(static::getResource()::getUrl('index'));
    }
}
