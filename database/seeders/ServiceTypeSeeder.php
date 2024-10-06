<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceType;

class ServiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $serviceTypes = [
            'Anterior Segment and Glaucoma',
            'Oculoplastic',
            'Consultant Filter',
            'Pediatrics Services',
            'Vitreoretinal (VR)',
            'Emergency (By Doctor on Call)',
            'Eye Ultrasound',
            'Lasers (By Registrars on Appointment)',
            'HVF (Appointment)',
        ];

        foreach ($serviceTypes as $serviceType) {
            ServiceType::create(['name' => $serviceType]);
        }
    }
}
