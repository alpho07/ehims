<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Filament\Resources\AppointmentResource\RelationManagers;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Service;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Client Management';

    public static function shouldRegisterNavigation(): bool
    {
        // Check if the user has permission to view any appointments
        return Auth::user()->hasAnyPermission([
            'view_any_patient',
            'view_any_appointments',
            // Add other permissions as needed
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Select Patient
                Forms\Components\Select::make('patient_id')
                    ->label('Patient')
                    ->relationship('patient', 'name')
                    ->required(),

                // Date Picker (Reactively filter services by the day of the week)
                Forms\Components\DatePicker::make('appointment_date')
                    ->label('Appointment Date')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $dayOfWeek = Carbon::parse($state)->format('l');  // Get the day of the week
                        $set('day_of_week', $dayOfWeek);  // Set the day of the week
                    }),

                // Select Service (Filtered by the day of the week and getting service type name)
                Forms\Components\Select::make('service_type_id')
                    ->label('Service')
                    ->options(Clinic::pluck('name','id')->toArray())
                    ->required()
                    ->searchable(),

                Forms\Components\Select::make('appointment_time')
                    ->label('Appointment Time')
                    ->options([
                        '08:00' => '08:00 AM',
                        '08:30' => '08:30 AM',
                        '09:00' => '09:00 AM',
                        '09:30' => '09:30 AM',
                        '10:00' => '10:00 AM',
                        '10:30' => '10:30 AM',
                        '11:00' => '11:00 AM',
                        '11:30' => '11:30 AM',
                        '12:00' => '12:00 PM',
                        '12:30' => '12:30 PM',
                        '13:00' => '01:00 PM',
                        '13:30' => '01:30 PM',
                        '14:00' => '02:00 PM',
                        '14:30' => '02:30 PM',
                        '15:00' => '03:00 PM',
                        '15:30' => '03:30 PM',
                        '16:00' => '04:00 PM',
                        '16:30' => '04:30 PM',
                    ])
                    ->searchable()
                    ->required(),



                // Hidden field for storing the day of the week
                //Forms\Components\Hidden::make('day_of_week'),




                // Show assigned doctor (based on the selected service's clinic)
                /*Forms\Components\TextInput::make('doctor_info')
                    ->label('Assigned Doctor')
                    ->disabled()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $service = Service::find($state);
                        if ($service && $service->clinic && $service->clinic->doctor) {
                            $set('doctor_info', $service->clinic->doctor->name);
                        } else {
                            $set('doctor_info', 'No doctor assigned');
                        }
                    }),*/

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient.name')->label('Patient'),
                Tables\Columns\TextColumn::make('serviceType.name')->label('Service Type'), // Display the ServiceType name
                Tables\Columns\TextColumn::make('appointment_date')->label('Date')->date(),
                Tables\Columns\TextColumn::make('appointment_time')->label('Time'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
