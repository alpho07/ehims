<?php

namespace App\Filament\Resources\TriagePatientResource\Pages;

use App\Filament\Resources\TriagePatientResource;
use App\Models\Clinic;
use App\Models\Consultation;
use App\Models\Visit;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Notifications\Notification;

class FilterClinicConsultationForm extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $resource = TriagePatientResource::class; // Initialize the resource
    protected static string $view = 'filament.resources.filter-clinic-form';

    public Visit $visit;

    public function mount($record): void
    {
        $this->visit = Visit::with('patient')->findOrFail($record);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\Textarea::make('complaints')->label('Complaints')->required(),
                    Forms\Components\Textarea::make('history_of_presenting_illness')->label('History of Presenting Illness')->required(),
                    Forms\Components\Textarea::make('examination')->label('Examination')->required(),
                    Forms\Components\Textarea::make('fundoscopy')->label('Fundoscopy')->required(),
                    Forms\Components\Textarea::make('diagnosis')->label('Diagnosis')->required(),
                    Forms\Components\Textarea::make('management')->label('Management')->required(),
                    Forms\Components\Select::make('referred_to_id')
                    ->label('Refer to Clinic')
                    ->options(Clinic::all()->pluck('name', 'id'))  // Use the relationship defined in the Visit model
                    ->nullable()  // Allow null values
                    ->required(),
                    Forms\Components\Textarea::make('reason_for_referral')->label('Reason for Referral')->required(),
                ]),
        ];
    }

    public function submit()
    {
        // Save the consultation details and referral
        Consultation::create([
            'visit_id' => $this->visit->id,
            'complaints' => $this->form->getState('complaints'),
            'history_of_presenting_illness' => $this->form->getState('history_of_presenting_illness'),
            'examination' => $this->form->getState('examination'),
            'fundoscopy' => $this->form->getState('fundoscopy'),
            'diagnosis' => $this->form->getState('diagnosis'),
            'management' => $this->form->getState('management'),
            'next_clinic_id' => $this->form->getState('next_clinic_id'),
            'reason_for_referral' => $this->form->getState('reason_for_referral'),
        ]);

        // Update the visit status to the next clinic
        $this->visit->moveToClinic($this->form->getState('next_clinic_id'), auth()->user()->id);

        Notification::make()
            ->title('Consultation completed and patient referred.')
            ->success()
            ->send();

        return redirect(static::getResource()::getUrl('index'));
    }
}
