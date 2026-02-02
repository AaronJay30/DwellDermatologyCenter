<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Service;
use App\Models\Promotion;
use App\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create main branch
        $mainBranch = Branch::create([
            'name' => 'Dwell Dermatology Center - Main Branch',
            'address' => '123 Medical Plaza, Health District, City',
            'phone' => '+1-555-0123',
            'email' => 'main@dwellderma.com',
        ]);

        User::create([
            'name' => 'Dr. Dianne Paras',
            'email' => 'dwelldermatology@gmail.com',
            'password' => Hash::make('password123'), // login password: password123
            'role' => 'doctor',
            'phone' => '+63-912-345-6789',
            'date_of_birth' => '1985-07-22',
            'gender' => 'female',
            'address' => '789 Wellness Avenue, Skin City',
            'branch_id' => $mainBranch->id,
        ]);

        // Create categories first
        $consultationCategory = Category::create([
            'name' => 'Consultation',
            'description' => 'General consultation services',
        ]);

        $treatmentCategory = Category::create([
            'name' => 'Treatment',
            'description' => 'Medical treatment services',
        ]);

        $screeningCategory = Category::create([
            'name' => 'Screening',
            'description' => 'Health screening services',
        ]);

        // Create sample services
        $services = [
            [
                'category_id' => $consultationCategory->id,
                'name' => 'General Dermatology Consultation',
                'price' => 150.00,
                'description' => 'Comprehensive skin examination and consultation',
                'is_active' => true,
            ],
            [
                'category_id' => $treatmentCategory->id,
                'name' => 'Acne Treatment',
                'price' => 200.00,
                'description' => 'Professional acne treatment and care',
                'is_active' => true,
            ],
            [
                'category_id' => $screeningCategory->id,
                'name' => 'Skin Cancer Screening',
                'price' => 300.00,
                'description' => 'Comprehensive skin cancer screening',
                'is_active' => true,
            ],
            [
                'category_id' => $treatmentCategory->id,
                'name' => 'Chemical Peel',
                'price' => 250.00,
                'description' => 'Professional chemical peel treatment',
                'is_active' => true,
            ],
            [
                'category_id' => $treatmentCategory->id,
                'name' => 'Laser Hair Removal',
                'price' => 400.00,
                'description' => 'Advanced laser hair removal treatment',
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

        // Create sample promotions
        Promotion::create([
            'name' => 'Summer Acne Treatment Special',
            'type' => 'campaign',
            'description' => 'Get 20% off on acne treatment services this summer',
            'discount_percent' => 20,
            'starts_at' => now()->format('Y-m-d'),
            'ends_at' => now()->addMonths(3)->format('Y-m-d'),
            'is_active' => true,
        ]);

        // Create sample notifications
        Notification::create([
            'title' => 'Welcome to Dwell Dermatology Center',
            'message' => 'Thank you for choosing our clinic. We are committed to providing the best dermatological care.',
            'type' => 'announcement',
            'user_id' => null, // General notification
            'is_read' => false,
        ]);

        // Seed time slots
        $this->call(TimeSlotSeeder::class);
    }
}
