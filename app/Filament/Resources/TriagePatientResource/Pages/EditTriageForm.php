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
    public $pulse;
    public $resp;
    public $bp_systolic;
    public $bp_diastolic;
    public $bp_status;
    public $bp_time;
    public $visual_acuity;
    public $iop;
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

        $this->date = now()->toDateString();
        $this->time = now()->format('H:i');
        $this->age = $this->visit->patient->dob
            ? \Carbon\Carbon::parse($this->visit->patient->dob)->age
            : null;
        $this->nurse_name = auth()->user()->name ?? '';

        $this->temperature = $this->visit->triage->temperature ?? '';
        $this->pulse = $this->visit->triage->pulse ?? '';
        $this->resp = $this->visit->triage->resp ?? '';
        $this->bp_systolic = $this->visit->triage->bp_systolic ?? '';
        $this->bp_diastolic = $this->visit->triage->bp_diastolic ?? '';
        $this->bp_status = $this->visit->triage->bp_status ?? '';
        $this->visual_acuity = $this->visit->triage->visual_acuity ?? '';
        $this->iop = $this->visit->triage->iop ?? '';
        $this->bp_time = $this->visit->triage->bp_time ?? '';
        $this->nurse_signature = $this->visit->triage->nurse_signature ?? '';
        $this->weight = $this->visit->triage->weight ?? '';
        $this->height = $this->visit->triage->height ?? '';


        // Initialize other properties as needed
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
                            // Add more patient details as needed
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
                            Forms\Components\TextInput::make('pulse')
                                ->label('Pulse (bpm)')
                                ->numeric()
                                ->required(),
                            Forms\Components\TextInput::make('resp')
                                ->label('Respiratory Rate')
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
                            Forms\Components\TextInput::make('bp_time')
                                ->label('BP Time')
                                ->required(),
                            Forms\Components\TextInput::make('visual_acuity')
                                ->label('Visual Acuity')
                                ->required(),
                            Forms\Components\TextInput::make('iop')
                                ->label('Intraocular Pressure')
                                ->numeric()
                                ->required(),
                            Forms\Components\TextInput::make('weight')
                                ->label('Weight (kg)')
                                ->required(),
                            Forms\Components\TextInput::make('height')
                                ->label('Height (cm)')
                                ->required(),
                            Forms\Components\TextInput::make('nurse_name')
                                ->label('Nurse Name')
                                ->required(),
                            Forms\Components\TextInput::make('nurse_signature')
                                ->label('Nurse Signature')
                                ->required(),
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
            'pulse' => $this->pulse,
            'resp' => $this->resp,
            'bp_systolic' => $this->bp_systolic,
            'bp_diastolic' => $this->bp_diastolic,
            'bp_status' => $this->bp_status,
            'bp_time' => $this->bp_time,
            'visual_acuity' => $this->visual_acuity,
            'iop' => $this->iop,
            'nurse_name' => $this->nurse_name,
            'nurse_signature' => $this->nurse_signature,
        ]);

        Notification::make()
            ->title('Triage information updated successfully!')
            ->success()
            ->send();

        // Redirect back to the triage patients list
        return redirect(static::getResource()::getUrl('index'));
    }
}
