<?php

namespace App\Filament\Resources\PrescriptionResource\Pages;

use App\Filament\Resources\PrescriptionResource;
use App\Models\Consultation;
use App\Models\Visit;
use App\Models\Prescription;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\HtmlString;
use App\Helpers\OlistHelper;
use Nette\Utils\Html;

class ViewPrescription extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $resource = PrescriptionResource::class;
    protected static string $view = 'filament.resources.view-prescription';
    public Prescription $prescription;
    public Visit $visit;
    public array $formData = []; // To store dynamic form data
    public array $previousConsultations = [];

    public function mount($record): void
    {
        // Find the prescription and associated visit
        $this->prescription = Prescription::with('visit')->findOrFail($record);

        $this->visit = $this->prescription->visit;

        // Get previous consultations (if any)
        $this->previousConsultations = Consultation::where('visit_id', $this->visit->id)->get()->map(function ($consultation) {
            // Convert form_data to array
            $summary = json_decode(json_encode($consultation->form_data), true);

            // Remove the 'prescription' key if it exists
            if (isset($summary['prescription'])) {
                unset($summary['prescription']);
            }

            return [
                'clinic_name' => $consultation->clinic->name,
                'consultation_date' => $consultation->created_at->format('Y-m-d'),
                'summary' => $summary, // Return the modified summary
            ];
        })->toArray();

        //dd($this->previousConsultations[0]['summary']);



        // Load the formData for the prescription
        $this->formData = $this->visit->consultation->form_data ?? [];


        $this->form->fill([
            'previous_consultations' => $this->previousConsultations
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    // Left Column: Patient, Triage, and Consultation History
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\Tabs::make('Patient Information')
                                ->tabs([
                                    // Patient Information
                                    Forms\Components\Tabs\Tab::make('Patient Information')
                                        ->schema([
                                            Placeholder::make('')
                                                ->content('Patient Name: ' . $this->visit->patient->name)
                                                ->columnSpan(1),
                                            Placeholder::make('')
                                                ->content('Hospital Number: ' . $this->visit->patient->hospital_number)
                                                ->columnSpan(1),
                                            Placeholder::make('')
                                                ->content('File Number: ' . $this->visit->patient->file_number)
                                                ->columnSpan(1),
                                            Placeholder::make('')
                                                ->content('Date of Birth: ' . $this->visit->patient->dob)
                                                ->columnSpan(1),
                                            Placeholder::make('')
                                                ->content('Age: ' . \Carbon\Carbon::parse($this->visit->patient->dob)->age . ' Years')
                                                ->columnSpan(1),
                                        ]),
                                    // Triage Information
                                    Forms\Components\Tabs\Tab::make('Triage Information')
                                        ->schema([
                                            Placeholder::make('')
                                                ->content(new HtmlString('Temperature (°C):') . $this->visit->triage->temperature)
                                                ->columnSpan(1),
                                            Placeholder::make('')
                                                ->content('Pulse Rate (PR): ' . $this->visit->triage->pulse_rate)
                                                ->columnSpan(1),
                                            Placeholder::make('')
                                                ->content('Blood Sugar (mg/dL): ' . $this->visit->triage->blood_sugar)
                                                ->columnSpan(1),
                                            Placeholder::make('')
                                                ->content('Blood Pressure (BP): ' . $this->visit->triage->bp_systolic . '/' . $this->visit->triage->bp_diastolic)
                                                ->columnSpan(1),
                                            Placeholder::make('')
                                                ->content('Visual Acuity (VA) - Distance Aided: ' . $this->visit->triage->distance_aided)
                                                ->columnSpan(1),
                                        ]),
                                    // Consultation History
                                    Forms\Components\Tabs\Tab::make('Consultation History')
                                        ->schema([
                                            Forms\Components\Repeater::make('previous_consultations')
                                                ->label('Previous Consultations')
                                                ->schema([
                                                    Placeholder::make('clinic_name')
                                                        ->content(fn($get) => $get('clinic_name')),
                                                    Placeholder::make('consultation_date')
                                                        ->content(fn($get) => $get('consultation_date')),
                                                    Placeholder::make('summary')
                                                       ->content(fn($get) => new HtmlString(OlistHelper::renderFormDataAsList($get('summary')))),
                                                ])
                                                ->defaultItems(0)
                                                ->disabled(),
                                        ]),
                                ]),
                        ])
                        ->columnSpan(1),

                    // Right Column: Prescription Form
                    Forms\Components\Card::make()
                        ->schema([
                            // Load refraction clinic prescription form
                            Forms\Components\Fieldset::make('Prescription Details')
                                ->schema($this->getPrescriptionFormSchema())
                                ->columnSpan(1),

                        ])
                        ->columnSpan(1),
                ]),
        ];
    }

    // app/Filament/Resources/PrescriptionResource.php
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('visit.patient.name')->label('Patient Name'),
                TextColumn::make('status')->label('Prescription Status')->sortable(),
                TextColumn::make('created_at')->label('Created At')->dateTime(),
            ])
            ->actions([
                Action::make('view')
                    ->label('View Prescription')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Prescription $record) => PrescriptionResource::getUrl('view', ['record' => $record->id])),
            ]);
    }

    // Render formData as a list
    public static function renderFormDataAsList(array $formData): string
    {
        if (empty($formData)) {
            return '<p>No data available</p>';
        }

        $listItems = '';
        foreach ($formData as $key => $value) {
            $listItems .= '<li><strong>' . ucfirst(str_replace('_', ' ', $key)) . ':</strong> ' . htmlspecialchars($value) . '</li>';
        }

        return new HtmlString('<ul>' . $listItems . '</ul>');
    }

    // Prescription form schema
    protected function getPrescriptionFormSchema(): array
    {
        return [
            // Distance Prescription for each eye
            Forms\Components\Fieldset::make('Distance Prescription')
                ->schema([
                    Forms\Components\TextInput::make('formData.prescription.distance.right.sphere')
                        ->label('Right Eye Sphere')
                        ->numeric()
                        ->disabled(),
                    Forms\Components\TextInput::make('formData.prescription.distance.right.cylinder')
                        ->label('Right Eye Cylinder')
                        ->numeric()
                        ->disabled(),
                    Forms\Components\TextInput::make('formData.prescription.distance.right.axis')
                        ->label('Right Eye Axis')
                        ->numeric()
                        ->disabled(),
                    Forms\Components\TextInput::make('formData.prescription.distance.left.sphere')
                        ->label('Left Eye Sphere')
                        ->numeric()
                        ->disabled(),
                    Forms\Components\TextInput::make('formData.prescription.distance.left.cylinder')
                        ->label('Left Eye Cylinder')
                        ->numeric()
                        ->disabled(),
                    Forms\Components\TextInput::make('formData.prescription.distance.left.axis')
                        ->label('Left Eye Axis')
                        ->numeric()
                        ->disabled(),
                ]),

            // Near Prescription
            Forms\Components\Fieldset::make('Near Prescription')
                ->schema([
                    Forms\Components\TextInput::make('formData.prescription.near.add')
                        ->label('Add (Near)')
                        ->numeric()
                        ->disabled(),
                    Forms\Components\TextInput::make('formData.prescription.near.sphere')
                        ->label('Sphere (Near)')
                        ->numeric()
                        ->disabled(),
                ]),

            // Additional details
            Forms\Components\TextInput::make('formData.prescription.pupillary_distance')
                ->label('Pupillary Distance (PD)')
                ->numeric()
                ->disabled(),
            Forms\Components\TextInput::make('formData.prescription.height')
                ->label('Height')
                ->numeric()
                ->disabled(),
            Forms\Components\TextInput::make('formData.prescription.frame_code')
                ->label('Frame Code')
                ->disabled(),
        ];
    }

    public function approvePrescription()
    {
        $this->prescription->update(['status' => 'approved']);

        Notification::make()
            ->title('Prescription Approved')
            ->success()
            ->send();

        return redirect(static::getResource()::getUrl('index'));
    }

    public function rejectPrescription()
    {
        // Rejecting the prescription
        $this->prescription->update([
            'status' => 'rejected',
            'rejection_reason' => 'Rejection Reason Here', // Add rejection reason in practice
        ]);

        Notification::make()
            ->title('Prescription Rejected')
            ->warning()
            ->send();

        return redirect(static::getResource()::getUrl('index'));
    }
}
