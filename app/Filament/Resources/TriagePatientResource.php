<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TriagePatientResource\Pages;
use App\Models\Visit;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Facades\Auth;

class TriagePatientResource extends Resource
{
    protected static ?string $model = Visit::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Client Queue';

    protected static ?string $navigationGroup = 'Queue Management';

    public static function shouldRegisterNavigation(): bool
    {
        // Check if the user has permission to view any triage
        return Auth::user()->can('view_any_triage');
    }

    public static function table(Tables\Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('patient.name')
                ->label('Patient Name')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('patient.hospital_number')
                ->label('Hospital Number')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('visit_start_time')
                ->label('Visit Start Time')
                ->dateTime()
                ->sortable(),
            Tables\Columns\TextColumn::make('status')
                ->label('Visit Status')
                ->sortable()
                ->badge()
                ->colors([
                    'success' => 'paid',
                    'warning' => 'triaged',
                    'danger' => 'cancelled',
                    'primary' => 'active',
                    'info' => 'filter clinic',
                    'secondary' => 'consultation',
                    'completed' => 'completed',
                ]),
        ])
        ->actions([
            Action::make('start_triage')
                ->label('Start Triage')
                ->color('primary')
                ->icon('heroicon-o-plus-circle')
                ->action(function (Visit $record) {
                    // Redirect to the triage page
                    $url = static::getUrl('triage', ['record' => $record]);
                    return redirect($url);
                })
                ->visible(fn(Visit $record) => $record->status === 'active'),

            Action::make('complete_triage')
                ->label('Complete Triage')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->action(function (Visit $record) {
                    // Set status to "Filter Clinic" and move the patient
                    $record->update(['status' => 'Filter Clinic']);

                    Notification::make()
                        ->title('Triage Completed')
                        ->body('Patient has been referred to Filter Clinic.')
                        ->success()
                        ->send();
                })
                ->visible(fn(Visit $record) => $record->status === 'triaged'),

            // New Action: Open Filter Clinic Consultation Form
            Action::make('start_consultation')
                ->label('Start Consultation')
                ->color('secondary')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->requiresConfirmation()
                ->modalHeading('Start Consultation')
                ->modalSubheading('Are you sure you want to start the consultation for this Visit?')
                ->modalButton('Yes, Start Consultation')
                ->action(function (Visit $record) {
                    // Correctly generate the URL using the resource's getUrl method
                    $url = TriagePatientResource::getUrl('consultation', ['record' => $record]);
                    return redirect($url);
                })
                ->visible(fn(Visit $record) => $record->clinic_id == 8 || $record->clinic_id == 9 || $record->clinic_id == 10 || $record->clinic_id == 11 || $record->clinic_id >= 12 ),  // Only show after triage is completed
        ]);
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTriagePatients::route('/'),
            'triage' => Pages\TriageForm::route('/{record}/triage-form'),
            'edit-triage' => Pages\EditTriageForm::route('/{record}/edit-triage'),
            'consultation' => ConsultationResource\Pages\DynamicConsultationForm::route('/{record}/consultation'),
            //'filter-consultation' => Pages\FilterClinicConsultationForm::route('/{record}/filter-consultation'),
        ];
    }
}
