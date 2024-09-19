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
                            Forms\Components\TimePicker::make('bp_time')
                                ->label('BP Time')
                                ->required(),
                            Forms\Components\TextInput::make('visual_acuity')
                                ->label('Visual Acuity')
                                ->required(),
                            Forms\Components\TextInput::make('iop')
                                ->label('Intraocular Pressure')
                                ->numeric()
                                ->required(),
                            Forms\Components\TextInput::make('nurse_name')
                                ->label('Nurse Name')
                                ->required(),
                            Forms\Components\TextInput::make('nurse_signature')
                                ->label('Nurse Signature')
                                ->required(),
                            Forms\Components\TextInput::make('weight')
                                ->label('Weight (kg)')
                                ->required(),
                            Forms\Components\TextInput::make('height')
                                ->label('Height (cm)')
                                ->required(),
                        ]),
                ]),
        ];
    }

    public function submit()
    {
        // Save the triage data
        $data =  Triage::create([
            'visit_id' => $this->visit->id,
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
