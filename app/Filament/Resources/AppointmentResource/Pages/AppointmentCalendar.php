<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Filament\Resources\AppointmentResource;
use App\Models\Appointment;
use Filament\Resources\Pages\Page;

class AppointmentCalendar extends Page
{
    protected static string $resource = AppointmentResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static string $view = 'filament.resources.calendar-view';
    protected static ?string $navigationLabel = 'Appointment Calendar';

    public array $appointments = [];

    public function mount(): void
    {
        // Load appointments from the database
        $this->appointments = Appointment::with('patient', 'clinic')
            ->get()
            ->map(function ($appointment) {
                return [
                    'title' => $appointment->patient->name . ' - ' . $appointment->clinic->name,
                    'start' => $appointment->appointment_time,
                    'status' => $appointment->status,
                    'id' => $appointment->id,
                ];
            })
            ->toArray();
    }
}
