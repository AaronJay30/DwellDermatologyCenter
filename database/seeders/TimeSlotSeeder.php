<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\TimeSlot;
use Illuminate\Database\Seeder;

class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = Branch::all();
        
        if ($branches->isEmpty()) {
            $this->command->warn('No branches found. Please create branches first.');
            return;
        }

        // Create time slots for the next 30 days for each branch
        foreach ($branches as $branch) {
            for ($i = 0; $i < 30; $i++) {
                $date = now()->addDays($i);
                
                // Skip weekends (optional - remove if you want weekend slots)
                if ($date->isWeekend()) {
                    continue;
                }

                // Create morning slots (9 AM - 12 PM)
                for ($hour = 9; $hour < 12; $hour++) {
                    TimeSlot::create([
                        'branch_id' => $branch->id,
                        'date' => $date->format('Y-m-d'),
                        'start_time' => sprintf('%02d:00:00', $hour),
                        'end_time' => sprintf('%02d:00:00', $hour + 1),
                        'is_booked' => false,
                    ]);
                }

                // Create afternoon slots (1 PM - 5 PM)
                for ($hour = 13; $hour < 17; $hour++) {
                    TimeSlot::create([
                        'branch_id' => $branch->id,
                        'date' => $date->format('Y-m-d'),
                        'start_time' => sprintf('%02d:00:00', $hour),
                        'end_time' => sprintf('%02d:00:00', $hour + 1),
                        'is_booked' => false,
                    ]);
                }
            }
        }

        $this->command->info('Time slots created successfully for all branches.');
    }
}