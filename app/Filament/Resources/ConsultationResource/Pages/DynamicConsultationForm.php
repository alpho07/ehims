<?php

namespace App\Filament\Resources\ConsultationResource\Pages;

use App\Filament\Resources\ConsultationResource;
use App\Models\Visit;
use App\Models\Consultation;
use App\Models\Clinic;
use App\Models\Queue;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;

class DynamicConsultationForm extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $resource = ConsultationResource::class;
    protected static string $view = 'filament.resources.dynamic-consultation-form';
    protected static ?string $title = '';

    public Visit $visit;
    public array $formData = []; // Holds dynamic form data

    public function mount($record): void
    {
        $this->visit = Visit::with('clinic', 'patient')->findOrFail($record);

        //dd($this->visit);

        $this::$title = "Consultation for " . $this->visit->patient->name . ' > '  . $this->visit->clinic->name;
        // Set initial form data if available (used when editing an existing consultation)
        $this->formData = $this->visit->consultation?->form_data ?? [];

        // Assuming consultations relationship exists on the visit
        $this->form->fill([
            'previous_consultations' => $this->visit->consultations->map(function ($consultation) {
                return [
                    'clinic_name' => $consultation->clinic->name ?? 'N/A',
                    'consultation_date' => $consultation->created_at->format('Y-m-d'),
                    'summary' => json_encode($consultation->form_data), // or process form data for display
                ];
            })->toArray(),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    // Left Column: Tabs for Patient Info, Triage Info, and Consultation History
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\Tabs::make('Patient Information')
                                ->tabs([
                                    // Patient Information Tab
                                    Forms\Components\Tabs\Tab::make('Patient Information')
                                        ->schema([
                                            Forms\Components\Placeholder::make('')
                                                ->content('Patient Name: ' . $this->visit->patient->name)
                                                ->columnSpan(1),

                                            Forms\Components\Placeholder::make('')
                                                ->content('Hospital Number: ' . $this->visit->patient->hospital_number)
                                                ->columnSpan(1),

                                            Forms\Components\Placeholder::make('')
                                                ->content('File Number: ' . $this->visit->patient->file_number)
                                                ->columnSpan(1),


                                            Forms\Components\Placeholder::make('')
                                                ->content('Date of Birth: ' . $this->visit->patient->dob ?? '')
                                                ->columnSpan(1),


                                            Forms\Components\Placeholder::make('')
                                                ->content('Age: ' . \Carbon\Carbon::parse($this->visit->patient->dob)->age . ' Years')
                                                ->columnSpan(1)

                                        ]),

                                    // Triage Information Tab
                                    Forms\Components\Tabs\Tab::make('Triage Information')
                                        ->schema([
                                            Forms\Components\Placeholder::make('')
                                                ->content(new HtmlString('Temperature (°C):') . $this->visit->triage->temperature)
                                                ->columnSpan(1),


                                            Forms\Components\Placeholder::make('')
                                                ->content('Pulse Rate (PR): ' . $this->visit->triage->pulse_rate)
                                                ->columnSpan(1),

                                            Forms\Components\Placeholder::make('')
                                                ->content('Blood Sugar (mg/dL): ' . $this->visit->triage->blood_sugar)
                                                ->columnSpan(1),

                                            Forms\Components\Placeholder::make('')
                                                ->content('Bloods Pressure(BP): ' . $this->visit->triage->bp_systolic . '/' . $this->visit->triage->bp_diastolic . ' - (' . $this->visit->triage->bp_status . '-' . $this->visit->triage->bp_time . ')')
                                                ->columnSpan(1),

                                            Forms\Components\Placeholder::make('')
                                                ->content('Visual Acuity (VA) - Distance Aided: ' . $this->visit->triage->distance_aided)
                                                ->columnSpan(1),
                                            Forms\Components\Placeholder::make('')
                                                ->content('Visual Acuity (VA) - Distance Unaided: ' . $this->visit->triage->distance_unaided)
                                                ->columnSpan(1),
                                            Forms\Components\Placeholder::make('')
                                                ->content('Visual Acuity (VA) - Distance Pinhole: ' . $this->visit->triage->distance_pinhole)
                                                ->columnSpan(1),
                                            Forms\Components\Placeholder::make('')
                                                ->content('Visual Acuity (VA) - Near Aided: ' . $this->visit->triage->near_aided)
                                                ->columnSpan(1),
                                            Forms\Components\Placeholder::make('')
                                                ->content('Visual Acuity (VA) - Near Unaided: ' . $this->visit->triage->near_unaided)
                                                ->columnSpan(1),

                                            Forms\Components\Placeholder::make('')
                                                ->content('Intraocular Pressure (IOP) Right: ' . $this->visit->triage->iop_right)
                                                ->columnSpan(1),

                                            Forms\Components\Placeholder::make('')
                                                ->content('Intraocular Pressure (IOP) Left: ' . $this->visit->triage->iop_left)
                                                ->columnSpan(1),


                                            Forms\Components\Placeholder::make('')
                                                ->content('Weight(KG): ' . $this->visit->triage->weight)
                                                ->columnSpan(1),


                                            Forms\Components\Placeholder::make('')
                                                ->content('Height(CM): ' . $this->visit->triage->height)
                                                ->columnSpan(1),


                                        ]),

                                    // Consultation History Tab
                                    Forms\Components\Tabs\Tab::make('Consultation History')
                                        ->schema([
                                            Forms\Components\Repeater::make('previous_consultations')
                                                ->label('Previous Consultations')
                                                ->schema([
                                                    Forms\Components\TextInput::make('clinic_name')
                                                        ->label('Clinic')
                                                        ->default(fn($record) => $record->clinic->name ?? 'N/A')
                                                        ->disabled(),
                                                    Forms\Components\TextInput::make('consultation_date')
                                                        ->label('Date')
                                                        ->default(fn($item) => $item->created_at ?? 'N/A')
                                                        ->disabled(),
                                                    Forms\Components\Textarea::make('summary')
                                                        ->label('Summary')
                                                        ->default(fn($item) => $item->form_data ?? 'N/A')
                                                        ->disabled(),
                                                ])
                                                ->defaultItems(0)
                                                ->disabled(),
                                        ]),
                                ]),
                        ])
                        ->columnSpan(1),

                    // Right Column: Current Dynamic Form
                    Forms\Components\Card::make()
                        ->schema($this->getFormSchemaForClinic())
                        ->columnSpan(1),
                ])
        ];
    }

    protected function getFormSchemaForClinic(): array
    {
        // Dynamically return the form schema based on the current clinic
        $clinic = $this->visit->clinic->id ?? null;

        return match ($clinic) {
            8 => $this->getFilterClinicForm(),
            9 => $this->getLowVisionClinicForm(),
            10 => $this->getAnteriorSegmentClinicForm(),
            11 => $this->getVitroRetinalClinicForm(),
            12 => $this->getRefractionClinicForm(),
            default => [],
        };
    }




    // Filter Clinic Form Schema
    protected function getFilterClinicForm(): array
    {
        return [
            Forms\Components\Textarea::make('formData.complaints')
                ->label('Complaints')
                ->required(),
            Forms\Components\Textarea::make('formData.history_of_presenting_illness')
                ->label('History of Presenting Illness')
                ->required(),
            Forms\Components\Textarea::make('formData.examination')
                ->label('Examination')
                ->required(),
            Forms\Components\Textarea::make('formData.fundoscopy')
                ->label('Fundoscopy')
                ->required(),
            Forms\Components\Textarea::make('formData.diagnosis')
                ->label('Diagnosis')
                ->required(),
            Forms\Components\Textarea::make('formData.management')
                ->label('Management')
                ->required(),
            Forms\Components\Select::make('formData.referred_to_id')
                ->label('Refer to Clinic')
                ->options(Clinic::all()->pluck('name', 'id'))
                ->nullable()
                ->required(),
            Forms\Components\Textarea::make('formData.reason_for_referral')
                ->label('Reason for Referral')
                ->required(),
        ];
    }

    // Low Vision Clinic Form Schema
    protected function getLowVisionClinicForm(): array
    {
        return [
            Forms\Components\Textarea::make('formData.complaints')
                ->label('Complaints')
                ->required(),
            Forms\Components\Textarea::make('formData.reason_for_referral')
                ->label('Reason for Referral')
                ->required(),
            Forms\Components\Textarea::make('formData.needs')
                ->label('Needs')
                ->required(),
            Forms\Components\Textarea::make('formData.diagnosis')
                ->label('Diagnosis')
                ->required(),
            Forms\Components\Textarea::make('formData.refraction')
                ->label('Refraction')
                ->required(),
            Forms\Components\Textarea::make('formData.optical_devices')
                ->label('Optical Devices')
                ->required(),
            Forms\Components\Textarea::make('formData.non_optical_devices')
                ->label('Non-Optical Devices')
                ->required(),
            Forms\Components\Textarea::make('formData.management')
                ->label('Management')
                ->required(),
            Forms\Components\Select::make('formData.referred_to_id')
                ->label('Refer to Clinic')
                ->options(Clinic::all()->pluck('name', 'id'))
                ->nullable()
                ->required(),
            Forms\Components\DatePicker::make('formData.tca')
                ->label('TCA (Next Appointment)')
                ->required(),
        ];
    }

    // Anterior Segment Clinic Form Schema
    protected function getAnteriorSegmentClinicForm(): array
    {
        return [
            Forms\Components\Textarea::make('formData.complaints')
                ->label('Complaints')
                ->required(),
            Forms\Components\Textarea::make('formData.history_of_presenting_illness')
                ->label('History of Presenting Illness')
                ->required(),
            Forms\Components\Textarea::make('formData.examination')
                ->label('Examination')
                ->required(),
            Forms\Components\Textarea::make('formData.fundoscopy')
                ->label('Fundoscopy')
                ->required(),
            Forms\Components\Textarea::make('formData.diagnosis')
                ->label('Diagnosis')
                ->required(),
            Forms\Components\Textarea::make('formData.management')
                ->label('Management')
                ->required(),
            Forms\Components\Select::make('formData.referred_to_id')
                ->label('Refer to Clinic')
                ->options(Clinic::all()->pluck('name', 'id'))
                ->nullable()
                ->required(),
            Forms\Components\Textarea::make('formData.reason_for_referral')
                ->label('Reason for Referral'),
        ];
    }

    // Vitro Retinal Clinic Form Schema
    protected function getVitroRetinalClinicForm(): array
    {
        return [
            Forms\Components\Textarea::make('formData.complaints')
                ->label('Complaints')
                ->required(),
            Forms\Components\Textarea::make('formData.history_of_presenting_illness')
                ->label('History of Presenting Illness')
                ->required(),
            Forms\Components\Textarea::make('formData.examination')
                ->label('Examination')
                ->required(),
            Forms\Components\Textarea::make('formData.diagnosis')
                ->label('Diagnosis')
                ->required(),
            Forms\Components\Textarea::make('formData.management')
                ->label('Management')
                ->required(),
            Forms\Components\Select::make('formData.referred_to_id')
                ->label('Refer to Clinic')
                ->options(Clinic::all()->pluck('name', 'id'))
                ->nullable()
                ->required(),
            Forms\Components\Textarea::make('formData.reason_for_referral')
                ->label('Reason for Referral'),
        ];
    }

    // Refraction Clinic Form Schema
    protected function getRefractionClinicForm(): array
    {
        return [
            Forms\Components\Textarea::make('formData.history_of_clinics')
                ->label('History of Clinics Visited')
                ->required(),
            Forms\Components\Textarea::make('formData.objective_refraction')
                ->label('Objective Refraction')
                ->required(),
            Forms\Components\Textarea::make('formData.subjective_refraction')
                ->label('Subjective Refraction')
                ->required(),
            Forms\Components\Textarea::make('formData.recommendations')
                ->label('Recommendations')
                ->required(),
            Forms\Components\Select::make('formData.referred_to_id')
                ->label('Refer to Clinic')
                ->options(Clinic::all()->pluck('name', 'id'))
                ->nullable()
                ->required(),
            Forms\Components\Textarea::make('formData.referral')
                ->label('Referrals to Other Clinics'),
        ];
    }



    // Dynamically set the title based on clinic and patient name



    public function submit()
    {

        // Extract the referred_to_id from the formData
        $referredToId = $this->formData['referred_to_id'] ?? null;



        // Save the dynamic form data as JSON
        Consultation::create([
            'visit_id' => $this->visit->id,
            'clinic_id' => $this->visit->clinic_id,
            'triage_id' => $this->visit->triage->id,
            'form_data' => $this->formData, // Save dynamic form data as JSON
            'referred_to_id' => $this->formData['referred_to_id'] ?? null, // Extract the referred clinic ID
            'reason_for_referral' => $this->formData['reason_for_referral'] ?? null,
        ]);

        $filterClinic = Clinic::where('id', $referredToId)->firstOrFail();
        $this->visit->update([
            'clinic_id' => $filterClinic->id,
            'referred_to_id' => $filterClinic->id,
            'status' => $filterClinic->name,
        ]);

        Queue::create([
            'clinic_id' => $filterClinic->id,
            'visit_id' => $this->visit->id,
            'patient_id' => $this->visit->patient_id,
            'position' => Queue::where('clinic_id', $filterClinic->id)->max('position') + 1,
            'status' => 'waiting',
        ]);



        Notification::make()
            ->title('Consultation completed and patient referred.')
            ->success()
            ->send();

        return redirect(static::getResource()::getUrl('index'));
    }
}
