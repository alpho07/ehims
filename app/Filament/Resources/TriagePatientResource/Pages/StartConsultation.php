<?php

namespace App\Filament\Resources\TriagePatientResource\Pages;

use App\Filament\Resources\TriagePatientResource;
use App\Models\Consultation;
use App\Models\Drug;
use App\Models\Triage;
use App\Models\Visit;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\HtmlString;

class StartConsultation extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $resource = TriagePatientResource::class;
    protected static string $view = 'filament.resources.start-consultation';
    protected static ?string $navigationLabel = 'Triage Patients';

    public Visit $visit;
    public Triage $triage;
    public Consultation $consultation;
    public Drug $drug;

    // Define public properties
    public $patient_name;
    public $hospital_number;
    public $file_number;
    public $dob;
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
    public $weight;
    public $height;
    public $nurse_signature;
    public $doctors_comments;
    public $prescription;
    public $right_eye_distance_sphere;
    public $right_eye_distance_cylinder;
    public $right_eye_distance_axis;
    public $right_eye_reading_sphere;
    public $right_eye_reading_cylinder;
    public $right_eye_reading_axis;
    public $left_eye_distance_sphere;
    public $left_eye_distance_cylinder;
    public $left_eye_distance_axis;
    public $left_eye_reading_sphere;
    public $left_eye_reading_cylinder;
    public $left_eye_reading_axis;

    public $consultationForm;
    public $prescriptionForm;

    public function mount($record): void
    {

        // Load visit and related triage data
        $this->visit = Visit::with('patient')->findOrFail($record);
        $this->triage = $this->visit->triage;
        //$this->consultation = $this->triage->consultation;




        $this->patient_name = $this->visit->patient->name;

        // Initialize public properties
        $this->patient_name = $this->visit->patient->name;
        $this->hospital_number = $this->visit->patient->hospital_number;
        $this->file_number = $this->visit->patient->file_number;

        $this->date = now()->toDateString();
        $this->time = now()->format('H:i');
        $this->age = $this->visit->patient->dob
            ? \Carbon\Carbon::parse($this->visit->patient->dob)->age
            : null;
        $this->dob = $this->visit->patient->dob;
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


        $this->getConsultationForm()->fill([
            'doctors_comments' => $this->triage->consultation->doctors_comments ?? '',
            'prescription' => $this->triage->consultation->prescription ?? '',
            'right_eye_distance_sphere' => $this->triage->consultation->right_eye_distance_sphere ?? null,
            'right_eye_distance_cylinder' => $this->triage->consultation->right_eye_distance_cylinder ?? null,
            'right_eye_distance_axis' => $this->triage->consultation->right_eye_distance_axis ?? null,
            'right_eye_reading_sphere' => $this->triage->consultation->right_eye_reading_sphere ?? null,
            'right_eye_reading_cylinder' => $this->triage->consultation->right_eye_reading_cylinder ?? null,
            'right_eye_reading_axis' => $this->triage->consultation->right_eye_reading_axis ?? null,
            'left_eye_distance_sphere' => $this->triage->consultation->left_eye_distance_sphere ?? null,
            'left_eye_distance_cylinder' => $this->triage->consultation->left_eye_distance_cylinder ?? null,
            'left_eye_distance_axis' => $this->triage->consultation->left_eye_distance_axis ?? null,
            'left_eye_reading_sphere' => $this->triage->consultation->left_eye_reading_sphere ?? null,
            'left_eye_reading_cylinder' => $this->triage->consultation->left_eye_reading_cylinder ?? null,
            'left_eye_reading_axis' => $this->triage->consultation->left_eye_reading_axis ?? null,
        ]);
    }


    protected function getFormSchema(): array
    {
        return [

            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\Section::make('Patient Information')
                        ->schema([
                            Forms\Components\Grid::make(2) // Create a grid with 2 columns
                                ->schema([
                                    Forms\Components\Placeholder::make('')
                                        ->content('Patient Name:')
                                        ->columnSpan(1),
                                    Forms\Components\Placeholder::make('')
                                        ->content($this->patient_name)
                                        ->columnSpan(1),

                                    Forms\Components\Placeholder::make('')
                                        ->content('Hospital Number:')
                                        ->columnSpan(1),
                                    Forms\Components\Placeholder::make('')
                                        ->content($this->hospital_number)
                                        ->columnSpan(1),

                                    Forms\Components\Placeholder::make('')
                                        ->content('File Number:')
                                        ->columnSpan(1),
                                    Forms\Components\Placeholder::make('')
                                        ->content($this->file_number)
                                        ->columnSpan(1),

                                    Forms\Components\Placeholder::make('')
                                        ->content('Date of Birth:')
                                        ->columnSpan(1),
                                    Forms\Components\Placeholder::make('')
                                        ->content($this->dob ?? '')
                                        ->columnSpan(1),

                                    Forms\Components\Placeholder::make('')
                                        ->content('Age:')
                                        ->columnSpan(1),
                                    Forms\Components\Placeholder::make('')
                                        ->content($this->age . ' Years')
                                        ->columnSpan(1),
                                ]),
                        ])
                        ->columnSpan(1),

                    Forms\Components\Section::make('Triage Information')
                        ->schema([
                            Forms\Components\Grid::make(2) // Create a grid with 2 columns
                                ->schema([
                                    Forms\Components\Placeholder::make('')
                                        ->content('Temperature (°C):')
                                        ->columnSpan(1),
                                    Forms\Components\Placeholder::make('')
                                        ->content($this->temperature)
                                        ->columnSpan(1),

                                    Forms\Components\Placeholder::make('')
                                        ->content('Pulse Rate (PR):')
                                        ->columnSpan(1),
                                    Forms\Components\Placeholder::make('')
                                        ->content($this->pulse)
                                        ->columnSpan(1),

                                    Forms\Components\Placeholder::make('')
                                        ->content('Bloods Pressure(BP):')
                                        ->columnSpan(1),
                                    Forms\Components\Placeholder::make('')
                                        ->content($this->bp_systolic . '/' . $this->bp_diastolic . ' - (' . $this->bp_status . '-' . $this->bp_time . ')')
                                        ->columnSpan(1),

                                    Forms\Components\Placeholder::make('')
                                        ->content('Visual Acuity (VA):')
                                        ->columnSpan(1),
                                    Forms\Components\Placeholder::make('')
                                        ->content($this->visual_acuity ?? '')
                                        ->columnSpan(1),

                                    Forms\Components\Placeholder::make('')
                                        ->content('Intraocular Pressure (IOP):')
                                        ->columnSpan(1),
                                    Forms\Components\Placeholder::make('')
                                        ->content($this->iop ?? '')
                                        ->columnSpan(1),

                                    Forms\Components\Placeholder::make('')
                                        ->content('Weight(KG):')
                                        ->columnSpan(1),
                                    Forms\Components\Placeholder::make('')
                                        ->content($this->weight ?? '')
                                        ->columnSpan(1),

                                    Forms\Components\Placeholder::make('')
                                        ->content('Height(CM):')
                                        ->columnSpan(1),
                                    Forms\Components\Placeholder::make('')
                                        ->content($this->height ?? '')
                                        ->columnSpan(1),

                                    Forms\Components\Placeholder::make('')
                                        ->content('Nurse:')
                                        ->columnSpan(1),
                                    Forms\Components\Placeholder::make('')
                                        ->content($this->iop ?? '')
                                        ->columnSpan(1),
                                ]),
                        ])->columnSpan(1),


                ]),

        ];
    }

    public function getConsultationForm(): Forms\ComponentContainer
    {
        return $this->makeForm()
            ->schema($this->getConsultationFormSchema());
    }

    public function getDrugForm(): Forms\ComponentContainer
    {
        return $this->makeForm()
            ->schema($this->getDrugFormSchema());
    }



    protected function getConsultationFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Ophthalmic Prescription')
                ->schema([

                    Forms\Components\Grid::make(2) // 2 columns for Right and Left Eye
                        ->schema([
                            // Right Eye Grid
                            Forms\Components\Section::make('Right Eye')
                                ->schema([
                                    Forms\Components\Placeholder::make('')
                                        ->content(new HtmlString('<strong>Distance</strong>')),
                                    Forms\Components\TextInput::make('right_eye_distance_sphere')
                                        ->label('SPH')
                                        ->numeric()
                                        ->step(0.25)
                                        ->required(),
                                    Forms\Components\TextInput::make('right_eye_distance_cylinder')
                                        ->label('CYL')
                                        ->numeric()
                                        ->step(0.25)
                                        ->nullable(),
                                    Forms\Components\TextInput::make('right_eye_distance_axis')
                                        ->label('AXIS')
                                        ->numeric()
                                        ->rules(['integer', 'min:0', 'max:180'])
                                        ->nullable(),

                                    Forms\Components\Placeholder::make('')
                                        ->content(new HtmlString('<strong>Reading</strong>')),
                                    Forms\Components\TextInput::make('right_eye_reading_sphere')
                                        ->label('SPH')
                                        ->numeric()
                                        ->step(0.25)
                                        ->required(),
                                    Forms\Components\TextInput::make('right_eye_reading_cylinder')
                                        ->label('CYL')
                                        ->numeric()
                                        ->step(0.25)
                                        ->nullable(),
                                    Forms\Components\TextInput::make('right_eye_reading_axis')
                                        ->label('AXIS')
                                        ->numeric()
                                        ->rules(['integer', 'min:0', 'max:180'])
                                        ->nullable(),
                                ])
                                ->columnSpan(1), // Right Eye section takes 1 column

                            // Left Eye Grid
                            Forms\Components\Section::make('Left Eye')
                                ->schema([
                                    Forms\Components\Placeholder::make('')
                                        ->content(new HtmlString('<strong>Distance</strong>')),
                                    Forms\Components\TextInput::make('left_eye_distance_sphere')
                                        ->label('SPH')
                                        ->numeric()
                                        ->step(0.25)
                                        ->required(),
                                    Forms\Components\TextInput::make('left_eye_distance_cylinder')
                                        ->label('CYL')
                                        ->numeric()
                                        ->step(0.25)
                                        ->nullable(),
                                    Forms\Components\TextInput::make('left_eye_distance_axis')
                                        ->label('AXIS')
                                        ->numeric()
                                        ->rules(['integer', 'min:0', 'max:180'])
                                        ->nullable(),

                                    Forms\Components\Placeholder::make('')
                                        ->content(new HtmlString('<strong><h3>Reading</h3></strong>')),
                                    Forms\Components\TextInput::make('left_eye_reading_sphere')
                                        ->label('SPH')
                                        ->numeric()
                                        ->step(0.25)
                                        ->required(),
                                    Forms\Components\TextInput::make('left_eye_reading_cylinder')
                                        ->label('CYL')
                                        ->numeric()
                                        ->step(0.25)
                                        ->nullable(),
                                    Forms\Components\TextInput::make('left_eye_reading_axis')
                                        ->label('AXIS')
                                        ->numeric()
                                        ->rules(['integer', 'min:0', 'max:180'])
                                        ->nullable(),
                                ])
                                ->columnSpan(1), // Left Eye section takes 1 column
                        ]),

                    Forms\Components\Section::make('Consultation Details')
                        ->schema([
                            Forms\Components\Textarea::make('prescription')
                                ->label('Prescription Narrative')
                                ->required()
                                ->rows(4),
                            Forms\Components\Textarea::make('doctors_comments')
                                ->label('Doctor\'s Comments')
                                ->required()
                                ->rows(4),

                        ])
                ]),
        ];
    }





    protected function rules(): array
    {
        return [
            'doctors_comments' => 'required|string',
            'prescription' => 'required|string',
            // Right Eye Validation
            'right_eye_distance_sphere' => 'nullable|numeric',
            'right_eye_distance_cylinder' => 'nullable|numeric',
            'right_eye_distance_axis' => 'nullable|integer|min:0|max:180',
            'right_eye_reading_sphere' => 'nullable|numeric',
            'right_eye_reading_cylinder' => 'nullable|numeric',
            'right_eye_reading_axis' => 'nullable|integer|min:0|max:180',
            // Left Eye Validation
            'left_eye_distance_sphere' => 'nullable|numeric',
            'left_eye_distance_cylinder' => 'nullable|numeric',
            'left_eye_distance_axis' => 'nullable|integer|min:0|max:180',
            'left_eye_reading_sphere' => 'nullable|numeric',
            'left_eye_reading_cylinder' => 'nullable|numeric',
            'left_eye_reading_axis' => 'nullable|integer|min:0|max:180',
        ];
    }


    public function submit()
    {


        $data = $this->getConsultationForm()->getState();
        //$data2 = $this->getDrugForm()->getState();

        try {
            // Create a new consultation record
            $consultation = Consultation::create([
                'visit_id' => $this->visit->id,
                'triage_id' => $this->triage->id,
                'doctor_id'=>auth()->user()->id,
                'doctors_comments' => $this->doctors_comments,
                'prescription' => $data['prescription'],
                // Ophthalmic Prescription data for Right Eye
                'right_eye_distance_sphere' => $data['right_eye_distance_sphere'],
                'right_eye_distance_cylinder' => $data['right_eye_distance_cylinder'],
                'right_eye_distance_axis' => $data['right_eye_distance_axis'],
                'right_eye_reading_sphere' => $data['right_eye_reading_sphere'],
                'right_eye_reading_cylinder' => $data['right_eye_reading_cylinder'],
                'right_eye_reading_axis' => $data['right_eye_reading_axis'],
                // Ophthalmic Prescription data for Left Eye
                'left_eye_distance_sphere' => $data['left_eye_distance_sphere'],
                'left_eye_distance_cylinder' => $data['left_eye_distance_cylinder'],
                'left_eye_distance_axis' => $data['left_eye_distance_axis'],
                'left_eye_reading_sphere' => $data['left_eye_reading_sphere'],
                'left_eye_reading_cylinder' => $data['left_eye_reading_cylinder'],
                'left_eye_reading_axis' => $data['left_eye_reading_axis'],
            ]);

            $this->visit->update(['status' => 'consultation']);

            Notification::make()
                ->title('Consultation Started')
                ->body('Consultation for Visit #' . $this->visit->id . ' created successfully.')
                ->success()
                ->send();

            // Redirect after successful consultation creation
            return redirect(static::getResource()::getUrl('index'));
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('There was an issue starting the consultation. Please try again.' . $e->getMessage())
                ->danger()
                ->send();

            // Log error for debugging
            \Log::error("Consultation Creation Failed: " . $e->getMessage());
            return;
        }
    }
}
