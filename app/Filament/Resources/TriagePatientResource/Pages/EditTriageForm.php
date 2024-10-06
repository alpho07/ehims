<?php

namespace App\Filament\Resources\TriagePatientResource\Pages;

use App\Filament\Resources\TriagePatientResource;
use App\Models\Visit;
use App\Models\Triage;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Notifications\Notification;

class EditTriageForm extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $resource = TriagePatientResource::class;
    protected static string $view = 'filament.resources.triage-form';

    public Visit $visit;
    public Triage $triage;

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
        $this->visit = Visit::with(['patient', 'triage'])->findOrFail($record);
        $this->triage = $this->visit->triage;

        // Initialize public properties
        $this->patient_name = $this->visit->patient->name;
        $this->hospital_number = $this->visit->patient->hospital_number;
        $this->file_number = $this->visit->patient->file_number;

        $this->date = $this->triage->date ?? now()->toDateString();
        $this->time = $this->triage->time ?? now()->format('H:i');
        $this->age = $this->visit->patient->dob
            ? \Carbon\Carbon::parse($this->visit->patient->dob)->age
            : null;
        $this->temperature = $this->triage->temperature ?? '';
        $this->pulse_rate = $this->triage->pulse_rate ?? '';
        $this->blood_sugar = $this->triage->blood_sugar ?? '';
        $this->resp = $this->triage->resp ?? '';
        $this->bp_systolic = $this->triage->bp_systolic ?? '';
        $this->bp_diastolic = $this->triage->bp_diastolic ?? '';
        $this->bp_status = $this->triage->bp_status ?? '';
        $this->bp_time = $this->triage->bp_time ?? '';
        $this->distance_aided = $this->triage->distance_aided ?? '';
        $this->distance_unaided = $this->triage->distance_unaided ?? '';
        $this->distance_pinhole = $this->triage->distance_pinhole ?? '';
        $this->near_aided = $this->triage->near_aided ?? '';
        $this->near_unaided = $this->triage->near_unaided ?? '';
        $this->iop_right = $this->triage->iop_right ?? '';
        $this->iop_left = $this->triage->iop_left ?? '';
        $this->nurse_name = $this->triage->nurse_name ?? auth()->user()->name ?? '';
        $this->nurse_signature = $this->triage->nurse_signature ?? '';
        $this->weight = $this->triage->weight ?? '';
        $this->height = $this->triage->height ?? '';
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
                                ->required(),
                            Forms\Components\TimePicker::make('time')
                                ->label('Time')
                                ->required(),
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
                                        ->required(),
                                    Forms\Components\TextInput::make('distance_unaided')
                                        ->label('Distance - Unaided')
                                        ->required(),
                                    Forms\Components\TextInput::make('distance_pinhole')
                                        ->label('Distance - Pinhole')
                                        ->required(),
                                    Forms\Components\TextInput::make('near_aided')
                                        ->label('Near - Aided')
                                        ->required(),
                                    Forms\Components\TextInput::make('near_unaided')
                                        ->label('Near - Unaided')
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
                            Forms\Components\TextInput::make('nurse_signature')
                                ->label('Nurse Signature')
                                ->hidden(),
                        ]),
                ]),
        ];
    }

    public function submit()
    {
        // Update the triage data
        $this->triage->update([
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
            'nurse_signature' => $this->nurse_signature,
            'weight' => $this->weight,
            'height' => $this->height,
        ]);

        Notification::make()
            ->title('Triage information updated successfully!')
            ->success()
            ->send();

        // Redirect back to the triage patients list
        return redirect(static::getResource()::getUrl('index'));
    }
}
