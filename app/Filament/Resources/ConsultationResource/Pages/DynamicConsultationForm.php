<?php

namespace App\Filament\Resources\ConsultationResource\Pages;

use App\Filament\Resources\ConsultationResource;
use App\Filament\Resources\TriagePatientResource;
use App\Models\Visit;
use App\Models\Consultation;
use App\Models\Clinic;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\Queue;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;

class DynamicConsultationForm extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $resource = ConsultationResource::class;
    protected static string $view = 'filament.resources.dynamic-consultation-form';
    protected static ?string $title = '';

    protected static string $triagepatientresource = TriagePatientResource::class;

    public Visit $visit;
    public array $formData = []; // Holds dynamic form data
    public  array  $previous_consultations = [];

    public function mount($record): void
    {
        $this->visit = Visit::with('clinic', 'patient')->findOrFail($record);

        //dd($this->visit);

        $this::$title = "Consultation for " . $this->visit->patient->name . ' > '  . $this->visit->clinic->name;
        // Set initial form data if available (used when editing an existing consultation)
        $this->formData = $this->visit->consultation?->form_data ?? [];

        $consultations = Consultation::where('visit_id', $this->visit->id)->get();

        // Map consultations data for repeater

        $this->previous_consultations = $consultations->map(function ($consultation) {
            return [
                'clinic_name' => $consultation->clinic->name ?? 'N/A1',
                'consultation_date' => $consultation->created_at->format('Y-m-d'),
                'summary' => json_decode(json_encode($consultation->form_data), TRUE), // or process form_data to make it readable
            ];
        })->toArray();

        $this->form->fill([
            'previous_consultations' => $this->previous_consultations,  // Populate repeater
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
                                                    Placeholder::make('clinic_name')
                                                        ->content(fn($get) =>  $get('clinic_name') ?? 'N/A'),
                                                    Placeholder::make('consultation_date')
                                                        ->content(fn($get) =>  $get('consultation_date') ?? 'N/A'),
                                                    Placeholder::make('summary')
                                                        ->content(fn($get) => new HtmlString(self::renderFormDataAsList($get('summary')))),
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

    public static function renderFormDataAsList(array $formData): string
    {
        if (empty($formData)) {
            return '<p>No data available</p>';  // Handle the case when form_data is empty
        }

        $listItems = '';
        foreach ($formData as $key => $value) {
            $listItems .= '<li><strong>' . ucfirst(str_replace('_', ' ', $key)) . ':</strong> ' . htmlspecialchars($value) . '</li>';
        }

        return new HtmlString('<ul>' . $listItems . '</ul>');
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
            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\Section::make('Prescription')
                        ->schema([

                            // Distance Prescription for Each Eye
                            Forms\Components\Fieldset::make('Distance Prescription')
                                ->schema([
                                    // Right Eye Fields
                                    Forms\Components\TextInput::make('formData.prescription.distance.right.sphere')
                                        ->label('Right Eye Sphere')
                                        ->numeric()
                                        ->required(),
                                    Forms\Components\TextInput::make('formData.prescription.distance.right.cylinder')
                                        ->label('Right Eye Cylinder')
                                        ->numeric()
                                        ->required(),
                                    Forms\Components\TextInput::make('formData.prescription.distance.right.axis')
                                        ->label('Right Eye Axis')
                                        ->numeric()
                                        ->required(),

                                    // Left Eye Fields
                                    Forms\Components\TextInput::make('formData.prescription.distance.left.sphere')
                                        ->label('Left Eye Sphere')
                                        ->numeric()
                                        ->required(),
                                    Forms\Components\TextInput::make('formData.prescription.distance.left.cylinder')
                                        ->label('Left Eye Cylinder')
                                        ->numeric()
                                        ->required(),
                                    Forms\Components\TextInput::make('formData.prescription.distance.left.axis')
                                        ->label('Left Eye Axis')
                                        ->numeric()
                                        ->required(),
                                ]),

                            // Near Prescription
                            Forms\Components\Fieldset::make('Near Prescription')
                                ->schema([
                                    Forms\Components\TextInput::make('formData.prescription.near.add')
                                        ->label('Add (Near)')
                                        ->numeric()
                                        ->required(),
                                    Forms\Components\TextInput::make('formData.prescription.near.sphere')
                                        ->label('Sphere (Near)')
                                        ->numeric()
                                        ->required(),
                                ]),

                            // Additional Prescription Details
                            Forms\Components\TextInput::make('formData.prescription.pupillary_distance')
                                ->label('Pupillary Distance (PD)')
                                ->numeric()
                                ->required(),

                            Forms\Components\TextInput::make('formData.prescription.height')
                                ->label('Height')
                                ->numeric()
                                ->required(),

                            Forms\Components\TextInput::make('formData.prescription.frame_code')
                                ->label('Frame Code')
                                ->required(),

                        ]),
                ])
        ];
    }



    // Dynamically set the title based on clinic and patient name



    public function submit()
    {
        // Check if the required payment for 'payment_item_id' 3 (Refraction Clinic) is done
        $paymentExists = Payment::where('visit_id', $this->visit->id)
            ->whereHas('paymentDetails', function ($query) {
                $query->where('payment_item_id', 3);  // Payment item for Refraction Clinic
            })
            //->where('is_paid', true)  // Ensure the payment status is 'paid'
            ->exists();

        // If payment is not made, show a notification and block submission
        if (!$paymentExists) {
            Notification::make()
                ->title('Payment Required')
                ->body('Payment for Refraction services is required before proceeding!. Please ask the patient to make a payment and try again.')
                ->warning()
                ->send();

            return;  // Stop submission if payment is not made
        }

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



        // Get the referred clinic if the referral was made
        $filterClinic = Clinic::where('id', $referredToId)->firstOrFail();

        if ($this->visit->clinic_id === 12) {
            Prescription::create([
                'visit_id' => $this->visit->id,
                'clinic_id' => $this->visit->clinic_id,
                'consultation_id' => $this->visit->consultation->id,
                'status' => 'pending', // Initially set as pending
            ]);
        }



        // Update the visit status to reflect the referral and the clinic
        $this->visit->update([
            'clinic_id' => $filterClinic->id,
            'referred_to_id' => $filterClinic->id,
            'status' => $filterClinic->name,  // You can also set a specific status string
        ]);

        // Add the patient to the queue for the referred clinic
        Queue::create([
            'clinic_id' => $filterClinic->id,
            'visit_id' => $this->visit->id,
            'patient_id' => $this->visit->patient_id,
            'position' => Queue::where('clinic_id', $filterClinic->id)->max('position') + 1,
            'status' => 'waiting',
        ]);

        // Notify the user that the consultation was completed and the patient referred
        Notification::make()
            ->title('Consultation completed and patient referred.')
            ->success()
            ->send();

        // Redirect back to the resource's index page
        return redirect(static::$triagepatientresource::getUrl('index'));
    }
}
