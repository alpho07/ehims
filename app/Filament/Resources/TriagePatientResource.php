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

class TriagePatientResource extends Resource
{
    protected static ?string $model = Visit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Triage Patients';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Columns related to the patient
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
                        'secondary' => 'completed',
                    ])
                    ->sortable(),
            ])
            ->filters([
                // Status Filter with 'active' as default
                Tables\Filters\SelectFilter::make('status')
                    ->label('Visit Status')
                    ->options([
                        'active' => 'Active',
                        'triaged' => 'Triaged',
                        'paid' => 'Paid',
                        'consultation' => 'Consultation',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('active')
                    ->query(function (Builder $query, array $data) {
                        if ($data['value']) {
                            $query->where('status', $data['value']);
                        }
                    }),
            ])
            ->actions([
                // Start Triage action (visible when status is 'active')
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

                // Edit Triage action (visible when status is 'triaged')
                Action::make('edit_triage')
                    ->label('Edit Triage')
                    ->color('warning')
                    ->icon('heroicon-o-pencil-square')
                    ->action(function (Visit $record) {
                        // Redirect to the edit triage page
                        $url = static::getUrl('edit-triage', ['record' => $record]);
                        return redirect($url);
                    })
                    ->visible(fn(Visit $record) => $record->status === 'triaged'),

                // Confirm Payment action (visible when status is 'triaged')
                Action::make('confirm_payment')
                    ->label('Confirm Payment')
                    ->color('success')
                    ->icon('heroicon-o-currency-dollar')
                    ->requiresConfirmation()
                    ->modalHeading('Confirm Payment')
                    ->modalSubheading('Are you sure you want to mark this Visit as paid?')
                    ->modalButton('Yes, Confirm Payment')
                    ->action(function (Visit $record) {
                        $record->update(['status' => 'paid']);

                        Notification::make()
                            ->title('Payment Confirmed')
                            ->body("Visit #{$record->id} has been marked as paid.")
                            ->success()
                            ->send();
                    })
                    ->visible(fn(Visit $record) => $record->status === 'triaged'),

                // Start Consultation action (visible when status is 'paid')
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
                    ->visible(fn(Visit $record) => $record->status === 'paid'),

                // Start Consultation action (visible when status is 'paid')
                Action::make('dispense_medicine')
                ->label('Dispense')
                ->color('success')
                ->icon('heroicon-o-arrow-right-start-on-rectangle')
                ->requiresConfirmation()
                ->modalHeading('Dispense')
                ->modalSubheading('Are you sure you want to dispense?')
                ->modalButton('Yes, Start Dispensing')
                ->action(function (Visit $record) {
                    // Correctly generate the URL using the resource's getUrl method
                    $url = TriagePatientResource::getUrl('dispense', ['record' => $record]);
                    return redirect($url);
                })
                ->visible(fn(Visit $record) => $record->status === 'consultation'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),

                // Confirm Payment Bulk Action
                /*BulkAction::make('confirm_payment_bulk')
                    ->label('Confirm Payment')
                    ->color('success')
                    ->icon('heroicon-o-currency-dollar')
                    ->requiresConfirmation()
                    ->modalHeading('Confirm Bulk Payment')
                    ->modalSubheading('Are you sure you want to mark the selected Visits as paid?')
                    ->modalButton('Yes, Confirm Payment')
                    ->action(function (Collection $records) {
                        $records->each(function (Visit $record) {
                            if ($record->status === 'triaged') {
                                $record->update(['status' => 'paid']);
                            }
                        });

                        Notification::make()
                            ->title('Payments Confirmed')
                            ->body('Selected visits have been marked as paid.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Collection $records) => $records->every(fn ($record) => $record->status === 'triaged')),*/
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTriagePatients::route('/'),
            'triage' => Pages\TriageForm::route('/{record}/triage'),
            'edit-triage' => Pages\EditTriageForm::route('/{record}/edit-triage'),
            'consultation' => Pages\StartConsultation::route('/{record}/consultation'),
        ];
    }
}
