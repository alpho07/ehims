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

        $this::$title = "Consultation for " . $this->visit->patient->name . ' > '  . $this->visit->clinic->name;
        $this->formData = $this->visit->consultation?->form_data ?? [];

        $consultations = Consultation::where('visit_id', $this->visit->id)->get();

        $this->previous_consultations = $consultations->map(function ($consultation) {
            $formData = json_decode(json_encode($consultation->form_data), true);

            if (isset($formData['prescription'])) {
                unset($formData['prescription']);
            }

            return [
                'clinic_name' => $consultation->clinic->name ?? 'N/A',
                'consultation_date' => $consultation->created_at->format('Y-m-d'),
                'summary' => $formData,
            ];
        })->toArray();

        // Check if clinic ID is >= 12 (Refraction Clinic)
        if ($this->visit->clinic->id >= 12) {
            // Fetch the refraction-specific consultation data
            $refractionConsultation = Consultation::where('visit_id', $this->visit->id)
                ->where('clinic_id', 12)
                ->first();

            if ($refractionConsultation) {
                $this->formData = $refractionConsultation->form_data;
                $this->formData['prescription'] = $refractionConsultation->form_data['prescription'];
            }
        }
        // dd($this->formData);

        $this->form->fill([
            'previous_consultations' => $this->previous_consultations,
            'formData' => $this->formData,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\Tabs::make('Patient Information')
                                ->tabs([
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

                    Forms\Components\Card::make()
                        ->schema($this->getFormSchemaForClinic())
                        ->columnSpan(1),
                ])
        ];
    }

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

    protected function getFormSchemaForClinic(): array
    {
        $clinic = $this->visit->clinic->id ?? null;

        if ($clinic >= 12) {
            return $this->getRefractionClinicForm($clinic);
        }

        return match ($clinic) {
            8 => $this->getFilterClinicForm(),
            9 => $this->getLowVisionClinicForm(),
            10 => $this->getAnteriorSegmentClinicForm(),
            11 => $this->getVitroRetinalClinicForm(),
            default => [],
        };
    }

    // Filter Clinic Form Schema
    protected function getFilterClinicForm(): array
    {
        $currentClinicId = $this->visit->clinic_id;  // Get the current clinic ID

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
                ->options(
                    Clinic::all()->pluck('name', 'id')->filter(function ($name, $id) use ($currentClinicId) {
                        // Exclude current clinic
                        if ($id === $currentClinicId) {
                            return false;
                        }
                        // Exclude clinics with 'hub' unless current clinic is Refraction Clinic (ID 12)
                        if (str_contains(strtolower($name), 'hub') && $currentClinicId !== 12) {
                            return false;
                        }
                        return true;
                    })
                )
                ->nullable()
                ->required()
                ->afterStateHydrated(function ($set, $get) use ($currentClinicId) {
                    // If the current clinic is Refraction Clinic, add "Hub" option
                    if ($currentClinicId >= 12) {
                        $set('formData.referred_to_id', array_merge(
                            $get('formData.referred_to_id', []),
                            ['Hub' => 'Hub']
                        ));
                    }
                }),

            Forms\Components\Textarea::make('formData.reason_for_referral')
                ->label('Reason for Referral')
                ->required(),
        ];
    }

    protected function getLowVisionClinicForm(): array
    {
        $currentClinicId = $this->visit->clinic_id;  // Get the current clinic ID

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
                ->options(
                    Clinic::all()->pluck('name', 'id')->filter(function ($name, $id) use ($currentClinicId) {
                        if ($id === $currentClinicId) {
                            return false;
                        }
                        if (str_contains(strtolower($name), 'hub') && $currentClinicId !== 12) {
                            return false;
                        }
                        return true;
                    })
                )
                ->nullable()
                ->required()
                ->afterStateHydrated(function ($set, $get) use ($currentClinicId) {
                    if ($currentClinicId == 12) {
                        $set('formData.referred_to_id', array_merge(
                            $get('formData.referred_to_id', []),
                            ['Hub' => 'Hub']
                        ));
                    }
                }),

            Forms\Components\DatePicker::make('formData.tca')
                ->label('TCA (Next Appointment)')
                ->required(),
        ];
    }

    protected function getAnteriorSegmentClinicForm(): array
    {
        $currentClinicId = $this->visit->clinic_id;

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
                ->options(
                    Clinic::all()->pluck('name', 'id')->filter(function ($name, $id) use ($currentClinicId) {
                        if ($id === $currentClinicId) {
                            return false;
                        }
                        if (str_contains(strtolower($name), 'hub') && $currentClinicId !== 12) {
                            return false;
                        }
                        return true;
                    })
                )
                ->nullable()
                ->required()
                ->afterStateHydrated(function ($set, $get) use ($currentClinicId) {
                    if ($currentClinicId == 12) {
                        $set('formData.referred_to_id', array_merge(
                            $get('formData.referred_to_id', []),
                            ['Hub' => 'Hub']
                        ));
                    }
                }),

            Forms\Components\Textarea::make('formData.reason_for_referral')
                ->label('Reason for Referral')
                ->required(),
        ];
    }

    protected function getVitroRetinalClinicForm(): array
    {
        $currentClinicId = $this->visit->clinic_id;

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
                ->options(
                    Clinic::all()->pluck('name', 'id')->filter(function ($name, $id) use ($currentClinicId) {
                        if ($id === $currentClinicId) {
                            return false;
                        }
                        if (str_contains(strtolower($name), 'hub') && $currentClinicId !== 12) {
                            return false;
                        }
                        return true;
                    })
                )
                ->nullable()
                ->required()
                ->afterStateHydrated(function ($set, $get) use ($currentClinicId) {
                    if ($currentClinicId == 12) {
                        $set('formData.referred_to_id', array_merge(
                            $get('formData.referred_to_id', []),
                            ['Hub' => 'Hub']
                        ));
                    }
                }),

            Forms\Components\Textarea::make('formData.reason_for_referral')
                ->label('Reason for Referral')
                ->required(),
        ];
    }

    protected function getRefractionClinicForm($clinic): array
    {
        $currentClinicId = $clinic;

        return [
            Forms\Components\Textarea::make('formData.objective_refraction')
                ->label('Objective Refraction')
                ->default($this->formData['objective_refraction'] ?? '')
                ->required(),
            Forms\Components\Textarea::make('formData.subjective_refraction')
                ->label('Subjective Refraction')
                ->default($this->formData['subjective_refraction'] ?? '')
                ->required(),
            Forms\Components\Textarea::make('formData.recommendations')
                ->label('Recommendations')
                ->default($this->formData['recommendations'] ?? '')
                ->required(),

            Forms\Components\Select::make('formData.referred_to_id')
                ->label('Refer to Clinic')
                ->options(
                    Clinic::all()->pluck('name', 'id')->filter(function ($name, $id) use ($currentClinicId) {
                        if ($id === $currentClinicId) {
                            return false;
                        }
                        if (str_contains(strtolower($name), 'hub') && $currentClinicId > 12) {
                            return false;
                        }
                        return true;
                    })
                )
                ->nullable()
                ->required()
                ->default($this->formData['referred_to_id'] ?? null),

            Forms\Components\Textarea::make('formData.referral')
                ->label('Referrals to Other Clinics'),

            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\Section::make('Prescription')
                        ->schema([
                            Forms\Components\Fieldset::make('Distance Prescription')
                                ->schema([
                                    Forms\Components\TextInput::make('formData.prescription.distance.right.sphere')
                                        ->label('Right Eye Sphere')
                                        ->default($this->formData['prescription']['distance']['right']['sphere'] ?? 12)
                                        ->numeric()
                                        ->required(),
                                    Forms\Components\TextInput::make('formData.prescription.distance.right.cylinder')
                                        ->label('Right Eye Cylinder')
                                        ->default($this->formData['prescription']['distance']['right']['cylinder'] ?? '')
                                        ->numeric()
                                        ->required(),
                                    Forms\Components\TextInput::make('formData.prescription.distance.right.axis')
                                        ->label('Right Eye Axis')
                                        ->default($this->formData['prescription']['distance']['right']['axis'] ?? '')
                                        ->numeric()
                                        ->required(),

                                    Forms\Components\TextInput::make('formData.prescription.distance.left.sphere')
                                        ->label('Left Eye Sphere')
                                        ->default($this->formData['prescription']['distance']['left']['sphere'] ?? '')
                                        ->numeric()
                                        ->required(),
                                    Forms\Components\TextInput::make('formData.prescription.distance.left.cylinder')
                                        ->label('Left Eye Cylinder')
                                        ->default($this->formData['prescription']['distance']['left']['cylinder'] ?? '')
                                        ->numeric()
                                        ->required(),
                                    Forms\Components\TextInput::make('formData.prescription.distance.left.axis')
                                        ->label('Left Eye Axis')
                                        ->default($this->formData['prescription']['distance']['left']['axis'] ?? '')
                                        ->numeric()
                                        ->required(),
                                ]),

                            Forms\Components\Fieldset::make('Near Prescription')
                                ->schema([
                                    Forms\Components\TextInput::make('formData.prescription.near.add')
                                        ->label('Add (Near)')
                                        ->default($this->formData['prescription']['near']['add'] ?? '12')
                                        ->numeric()
                                        ->required(),
                                    Forms\Components\TextInput::make('formData.prescription.near.sphere')
                                        ->label('Sphere (Near)')
                                        ->default($this->formData['prescription']['near']['sphere'] ?? '')
                                        ->numeric()
                                        ->required(),
                                ]),

                            Forms\Components\TextInput::make('formData.prescription.pupillary_distance')
                                ->label('Pupillary Distance (PD)')
                                ->default($this->formData['prescription']['pupillary_distance'] ?? '')
                                ->numeric()
                                ->required(),
                            Forms\Components\TextInput::make('formData.prescription.height')
                                ->label('Height')
                                ->default($this->formData['prescription']['height'] ?? '')
                                ->numeric()
                                ->required(),
                            Forms\Components\TextInput::make('formData.prescription.frame_code')
                                ->label('Frame Code')
                                ->default($this->formData['prescription']['frame_code'] ?? '')
                                ->required(),
                        ]),
                ]),
        ];
    }

    public function submit()
    {
        $referredToId = $this->formData['referred_to_id'] ?? null;

        if ($referredToId == 12) {
            $paymentExists = Payment::where('visit_id', $this->visit->id)
                ->whereHas('paymentDetails', function ($query) {
                    $query->where('payment_item_id', 3);  // Payment item for Refraction Clinic
                })
                ->exists();

            if (!$paymentExists) {
                Notification::make()
                    ->title('Payment Required')
                    ->body('Payment for Refraction services is required before proceeding! Please ask the patient to make a payment and try again.')
                    ->warning()
                    ->send();

                return;
            }
        }

        Consultation::create([
            'visit_id' => $this->visit->id,
            'clinic_id' => $this->visit->clinic_id,
            'triage_id' => $this->visit->triage->id,
            'form_data' => $this->formData,
            'referred_to_id' => $this->formData['referred_to_id'] ?? null,
            'reason_for_referral' => $this->formData['reason_for_referral'] ?? null,
        ]);

        $filterClinic = Clinic::where('id', $referredToId)->firstOrFail();

        if ($this->visit->clinic_id === 12) {
            Prescription::create([
                'visit_id' => $this->visit->id,
                'clinic_id' => $this->visit->clinic_id,
                'consultation_id' => $this->visit->consultation->id,
                'status' => 'pending',
            ]);
        }

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

        return redirect(static::$triagepatientresource::getUrl('index'));
    }
}
