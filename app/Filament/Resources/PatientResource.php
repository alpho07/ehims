<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\RelationManagers;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Livewire\Notifications;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Clients';

    protected static ?string $navigationGroup = 'Client Management';

    protected static ?int $navigationSort = -2;


    public static function shouldRegisterNavigation(): bool
    {
        // Check if the user has permission to view any appointments
        return Auth::user()->can('view_any_patient');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('email')->email(),
                TextInput::make('hospital_number')->required(),
                TextInput::make('file_number')->required(),
                TextInput::make('phone')->required(),
                DatePicker::make('dob')
                    ->required()->reactive() // To trigger reactivity
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state) {
                            // Calculate age in years
                            $dob = \Carbon\Carbon::parse($state);
                            $age = $dob->age; // This uses Carbon's in-built age calculation
                            $set('age', $age); // Set the age based on dob
                        } else {
                            $set('age', null); // Reset the age if dob is cleared
                        }
                    }),
                TextInput::make('age')
                    ->label('Age')
                    ->disabled() // Make it read-only
                    ->reactive(), // Ensure it updates automatically
                TextInput::make('address')->required(),
                Select::make('gender')
                    ->options([
                        'Male' => 'Male',
                        'Female' => 'Female',
                        'Other' => 'Other',
                    ])->required(),
                Select::make('source')
                    ->options(['Walk-In' => 'Walk-In', 'Referral' => 'Referral'])
                    ->reactive()
                    ->required()
                    ->default('Walk-In'),
                TextInput::make('referral_facility')->visible(fn(Get $get) => $get('source') === 'Referral')->nullable()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        
            ->columns([
                TextColumn::make('name')
                    ->label('Patient Name')
                    ->searchable()
                    ->sortable(), // Make the 'name' column searchable
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(), // Make the 'email' column searchable
                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->sortable(), // Make the 'phone' column searchable
                TextColumn::make('hospital_number')
                    ->label('Hospital number')
                    ->searchable()
                    ->sortable(), // Make the 'hospital_number' column searchable
                TextColumn::make('file_number')
                    ->label('File Number')
                    ->searchable()
                    ->sortable(), // Make the 'file_number' column searchable
                TextColumn::make('dob')
                    ->label('Date of Birth')
                    ->searchable()
                    ->sortable(), // Make the 'dob' column searchable
                TextColumn::make('address')
                    ->label('Address')
                    ->searchable()
                    ->sortable(), // Make the 'address' column searchable
                TextColumn::make('gender')
                    ->label('Gender')
                    ->searchable()
                    ->sortable(), // Make the 'gender' column searchable
                TextColumn::make('age')
                    ->label('Age (Years)')
                    ->getStateUsing(function ($record) {
                        // Calculate age using the date_of_birth field
                        if ($record->dob) {
                            return \Carbon\Carbon::parse($record->dob)->age;
                        }
                        return null; // Return null if no dob is available
                    }) ->sortable(),
                TextColumn::make('source')
                    ->label('Source')
                    ->searchable()
                    ->sortable(), // Make the 'source' column searchable
                TextColumn::make('referral_facility')
                    ->label('Referral Facility')
                    ->searchable()
                    ->sortable(), // Make the 'referral_facility' column searchable
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                // Start Visit action
                Tables\Actions\Action::make('start_visit')
                    ->label('Start Visit')
                    ->color('success')
                    ->icon('heroicon-o-play')
                    ->requiresConfirmation() // Require confirmation before starting the visit
                    ->action(function ($record) {
                        // Insert a new visit record
                        \App\Models\Visit::create([
                            'patient_id' => $record->id,
                            'visit_start_time' => now(),
                            'status' => 'active',
                        ]);

                        Notification::make()
                            ->title('Visit started successfully')
                            ->success()
                            ->send();
                    })
                    ->visible(fn($record) => \App\Models\Visit::where('patient_id', $record->id)->where('status', 'active')->doesntExist()), // Only show if no active visit

                // End Visit action
                Tables\Actions\Action::make('end_visit')
                    ->label('End Visit')
                    ->color('danger')
                    ->icon('heroicon-o-stop')
                    ->requiresConfirmation() // Require confirmation before ending the visit
                    ->action(function ($record) {
                        $activeVisit = \App\Models\Visit::where('patient_id', $record->id)->where('status', 'active')->first();
                        if ($activeVisit) {
                            $activeVisit->update([
                                'visit_end_time' => now(),
                                'status' => 'completed',
                            ]);

                            Notification::make()
                                ->title('Visit ended successfully')
                                ->success()
                                ->send();
                        }
                    })
                    ->visible(fn($record) => \App\Models\Visit::where('patient_id', $record->id)->where('status', 'active')->exists()), // Only show if an active visit exists

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
