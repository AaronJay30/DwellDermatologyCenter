<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\User;
use App\Models\TimeSlot;
use App\Models\DoctorSlot;
use App\Models\Appointment;
use App\Models\Service;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Run this seeder with: php artisan db:seed --class=AdminTestDataSeeder
     */
    public function run(): void
    {
        $this->command->info('Creating test data for Admin Dashboard...');
        
        // 1. Create or get branches
        $branches = $this->createBranches();
        
        // 2. Create test patients for each branch
        $patients = $this->createPatients($branches);
        
        // 3. Create time slots for today and upcoming days
        $timeSlots = $this->createTimeSlots($branches);
        
        // 4. Create doctor slots
        $doctorSlots = $this->createDoctorSlots($branches, $timeSlots);
        
        // 5. Create appointments
        $this->createAppointments($patients, $doctorSlots);
        
        $this->command->info('Test data created successfully!');
        $this->command->info('');
        $this->command->info('Summary:');
        $this->command->info('- Branches: ' . Branch::count());
        $this->command->info('- Patients: ' . User::where('role', 'patient')->count());
        $this->command->info('- Time Slots: ' . TimeSlot::count());
        $this->command->info('- Doctor Slots: ' . DoctorSlot::count());
        $this->command->info('- Appointments: ' . Appointment::count());
    }

    private function createBranches(): array
    {
        $branchData = [
            ['name' => 'Main Branch', 'address' => '123 Main Street, Manila City', 'phone' => '02-1234-5678', 'email' => 'main@dwell.com'],
            ['name' => 'Makati Branch', 'address' => '789 Ayala Avenue, Makati City', 'phone' => '02-8765-4321', 'email' => 'makati@dwell.com'],
            ['name' => 'Cavite Branch', 'address' => '369 Aguinaldo Highway, Cavite', 'phone' => '046-123-4567', 'email' => 'cavite@dwell.com'],
            ['name' => 'Laguna Branch', 'address' => '741 National Highway, Sta. Rosa, Laguna', 'phone' => '049-543-2100', 'email' => 'laguna@dwell.com'],
            ['name' => 'Las Pinas Branch', 'address' => '258 Alabang-Zapote Road, Las Pinas City', 'phone' => '02-8080-1234', 'email' => 'laspinas@dwell.com'],
        ];

        $branches = [];
        foreach ($branchData as $data) {
            $branch = Branch::firstOrCreate(
                ['email' => $data['email']],
                $data
            );
            $branches[] = $branch;
        }

        return $branches;
    }

    private function createPatients(array $branches): array
    {
        $patientNames = [
            'Juan Dela Cruz', 'Maria Santos', 'Pedro Garcia', 'Ana Reyes', 'Jose Rizal',
            'Rosa Luna', 'Carlos Mendoza', 'Elena Villanueva', 'Miguel Torres', 'Carmen Cruz',
            'Roberto Silva', 'Patricia Lim', 'Fernando Aquino', 'Isabella Tan', 'Antonio Ramos',
            'Lucia Fernandez', 'Ricardo Gonzales', 'Sofia Castro', 'Daniel Morales', 'Angela Diaz'
        ];

        $patients = [];
        $patientIndex = 0;

        foreach ($branches as $branch) {
            // Create 3-5 patients per branch
            $numPatients = rand(3, 5);
            for ($i = 0; $i < $numPatients && $patientIndex < count($patientNames); $i++) {
                $name = $patientNames[$patientIndex];
                $email = strtolower(str_replace(' ', '.', $name)) . $patientIndex . '@patient.test';
                
                $patient = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $name,
                        'email' => $email,
                        'password' => Hash::make('password123'),
                        'role' => 'patient',
                        'phone' => '09' . rand(100000000, 999999999),
                        'address' => 'Test Address ' . ($patientIndex + 1),
                        'branch_id' => $branch->id,
                    ]
                );
                $patients[] = $patient;
                $patientIndex++;
            }
        }

        return $patients;
    }

    private function createTimeSlots(array $branches): array
    {
        $timeSlots = [];
        $today = Carbon::today();

        foreach ($branches as $branch) {
            // Create slots for today and next 7 days
            for ($day = 0; $day < 7; $day++) {
                $date = $today->copy()->addDays($day);
                
                // Morning slots
                $morningSlots = [
                    ['09:00', '10:00'],
                    ['10:00', '11:00'],
                    ['11:00', '12:00'],
                ];
                
                // Afternoon slots
                $afternoonSlots = [
                    ['13:00', '14:00'],
                    ['14:00', '15:00'],
                    ['15:00', '16:00'],
                    ['16:00', '17:00'],
                ];

                $allSlots = array_merge($morningSlots, $afternoonSlots);
                
                foreach ($allSlots as $slot) {
                    $timeSlot = TimeSlot::firstOrCreate(
                        [
                            'branch_id' => $branch->id,
                            'date' => $date->format('Y-m-d'),
                            'start_time' => $slot[0],
                            'end_time' => $slot[1],
                        ],
                        [
                            'is_booked' => false,
                        ]
                    );
                    $timeSlots[] = $timeSlot;
                }
            }
        }

        return $timeSlots;
    }

    private function createDoctorSlots(array $branches, array $timeSlots): array
    {
        $doctorSlots = [];
        $doctor = User::where('role', 'doctor')->first();
        
        if (!$doctor) {
            $doctor = User::firstOrCreate(
                ['email' => 'doctor@dwell.test'],
                [
                    'name' => 'Dr. Test Doctor',
                    'email' => 'doctor@dwell.test',
                    'password' => Hash::make('password123'),
                    'role' => 'doctor',
                    'phone' => '09123456789',
                ]
            );
        }

        $today = Carbon::today();
        
        // Create doctor slots for today and next 7 days (without branch_id)
        for ($day = 0; $day < 7; $day++) {
            $date = $today->copy()->addDays($day);
            
            // Morning and afternoon slots
            $slots = [
                ['09:00:00', '10:00:00'],
                ['10:00:00', '11:00:00'],
                ['14:00:00', '15:00:00'],
                ['15:00:00', '16:00:00'],
            ];

            foreach ($slots as $slot) {
                $doctorSlot = DoctorSlot::firstOrCreate(
                    [
                        'doctor_id' => $doctor->id,
                        'slot_date' => $date->format('Y-m-d'),
                        'start_time' => $slot[0],
                        'end_time' => $slot[1],
                    ],
                    [
                        'is_booked' => false,
                    ]
                );
                $doctorSlots[] = $doctorSlot;
            }
        }

        return $doctorSlots;
    }

    private function createAppointments(array $patients, array $doctorSlots): void
    {
        $services = Service::all();
        if ($services->isEmpty()) {
            $this->command->warn('No services found. Skipping appointment creation.');
            return;
        }

        $statuses = ['scheduled', 'confirmed', 'booked', 'pending', 'completed'];
        $today = Carbon::today();
        
        // Get today's doctor slots
        $todaySlots = collect($doctorSlots)->filter(function($slot) use ($today) {
            return Carbon::parse($slot->slot_date)->isSameDay($today);
        });
        
        // Get upcoming slots (not today)
        $upcomingSlots = collect($doctorSlots)->filter(function($slot) use ($today) {
            return Carbon::parse($slot->slot_date)->isAfter($today);
        });

        // Create appointments for today (3-5 appointments)
        $numTodayAppts = min(rand(3, 5), $todaySlots->count(), count($patients));
        $usedTodaySlots = $todaySlots->shuffle()->take($numTodayAppts);
        
        foreach ($usedTodaySlots as $index => $slot) {
            if ($index >= count($patients)) break;
            
            $patient = $patients[$index];
            $service = $services->random();
            
            Appointment::firstOrCreate(
                [
                    'patient_id' => $patient->id,
                    'doctor_slot_id' => $slot->id,
                ],
                [
                    'doctor_id' => $slot->doctor_id,
                    'service_id' => $service->id,
                    'status' => $statuses[array_rand(['scheduled', 'confirmed', 'booked'])],
                    'notes' => 'Test appointment for today - ' . $patient->name,
                    'first_name' => explode(' ', $patient->name)[0] ?? $patient->name,
                    'last_name' => explode(' ', $patient->name)[1] ?? '',
                ]
            );
        }

        // Create upcoming appointments (5-8 appointments)
        $numUpcomingAppts = min(rand(5, 8), $upcomingSlots->count(), count($patients));
        $usedUpcomingSlots = $upcomingSlots->shuffle()->take($numUpcomingAppts);
        
        foreach ($usedUpcomingSlots as $index => $slot) {
            $patientIndex = $index % count($patients);
            $patient = $patients[$patientIndex];
            $service = $services->random();
            
            Appointment::firstOrCreate(
                [
                    'patient_id' => $patient->id,
                    'doctor_slot_id' => $slot->id,
                ],
                [
                    'doctor_id' => $slot->doctor_id,
                    'service_id' => $service->id,
                    'status' => $statuses[array_rand(['scheduled', 'confirmed', 'pending'])],
                    'notes' => 'Test upcoming appointment - ' . $patient->name,
                    'first_name' => explode(' ', $patient->name)[0] ?? $patient->name,
                    'last_name' => explode(' ', $patient->name)[1] ?? '',
                ]
            );
        }

        $this->command->info("Created appointments for today: $numTodayAppts");
        $this->command->info("Created upcoming appointments: $numUpcomingAppts");
    }
}
