<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\User;
use App\Models\Promotion;
use App\Models\Service;
use App\Models\ServiceImage;
use App\Models\TimeSlot;
use App\Models\Appointment;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:doctor');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;
        
        // Get branches with patient count (users with role 'patient' linked to that branch)
        $branches = Branch::withCount(['users' => function($query) {
            $query->where('role', 'patient');
        }])->orderBy('name')->get();
        
        // Get today's schedule - appointments with time slots for today at user's branch
        $today = Carbon::today();
        $todaysSchedule = Appointment::with(['patient', 'doctorSlot'])
            ->whereHas('doctorSlot', function($q) use ($today, $branchId) {
                $q->whereDate('slot_date', $today);
                if ($branchId) {
                    $q->where('branch_id', $branchId);
                }
            })
            ->whereIn('status', ['scheduled', 'confirmed', 'booked'])
            ->orderBy('created_at')
            ->limit(10)
            ->get();
        
        // Get upcoming appointments
        $upcomingAppointments = Appointment::with(['patient', 'doctorSlot'])
            ->whereHas('doctorSlot', function($q) use ($today, $branchId) {
                $q->whereDate('slot_date', '>', $today);
                if ($branchId) {
                    $q->where('branch_id', $branchId);
                }
            })
            ->whereIn('status', ['scheduled', 'confirmed', 'booked', 'pending'])
            ->orderBy('created_at')
            ->limit(5)
            ->get();
        
        $counts = [
            'branches' => Branch::count(),
            'categories' => Category::count(),
            'services' => Service::count(),
            'slots' => TimeSlot::count(),
            'promotions' => Promotion::count(),
        ];
        
        return view('admin.dashboard', compact('counts', 'branches', 'todaysSchedule', 'upcomingAppointments'));
    }

    public function branches()
    {
        $branches = Branch::latest()->paginate(5);
        return view('admin.branches.index', compact('branches'));
    }

    public function createBranch()
    {
        return view('admin.branches.create');
    }

    public function storeBranch(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'admin_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $branch = Branch::create([
                'name' => $validated['name'],
                'address' => $validated['address'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
            ]);

            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'admin',
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'branch_id' => $branch->id,
            ];

            // Handle admin photo upload
            if ($request->hasFile('admin_photo')) {
                $path = $request->file('admin_photo')->store('admin-photos', 'public');
                $userData['profile_photo'] = $path;
            }

            User::create($userData);
        });

        return redirect()->route('admin.branches')->with('success', 'Branch and admin user created successfully!');
    }

    public function editBranch(Branch $branch)
    {
        return view('admin.branches.edit', compact('branch'));
    }

    public function updateBranch(Request $request, Branch $branch)
    {
        $adminUser = User::where('branch_id', $branch->id)->where('role', 'admin')->first();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255' . ($adminUser ? ('|unique:users,email,' . $adminUser->id) : ''),
            'password' => 'nullable|string|min:8',
        ]);

        DB::transaction(function () use ($validated, $branch, $adminUser) {
            $branch->update([
                'name' => $validated['name'],
                'address' => $validated['address'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
            ]);

            if ($adminUser) {
                $adminUser->name = $validated['name'];
                $adminUser->email = $validated['email'];
                $adminUser->phone = $validated['phone'];
                $adminUser->address = $validated['address'];
                if (!empty($validated['password'])) {
                    $adminUser->password = Hash::make($validated['password']);
                }
                $adminUser->save();
            } else {
                // Create linked admin user if missing
                $data = [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'role' => 'admin',
                    'phone' => $validated['phone'],
                    'address' => $validated['address'],
                    'branch_id' => $branch->id,
                ];
                if (!empty($validated['password'])) {
                    $data['password'] = Hash::make($validated['password']);
                } else {
                    // Require a password if creating new user on update
                    $data['password'] = Hash::make(str()->random(12));
                }
                User::create($data);
            }
        });

        return redirect()->route('admin.branches')->with('success', 'Branch updated successfully!');
    }

    public function destroyBranch(Branch $branch)
    {
        DB::transaction(function () use ($branch) {
            User::where('branch_id', $branch->id)->where('role', 'admin')->delete();
            $branch->delete();
        });

        return redirect()->route('admin.branches')->with('success', 'Branch deleted successfully!');
    }

    public function categories()
    {
        $branches = Branch::orderBy('name')->get();
        $query = Category::query()->with(['branch'])->withCount('services');
        if (request('branch_id')) {
            $query->where('branch_id', request('branch_id'));
        }
        if (request('q')) {
            $search = request('q');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        $categories = $query->latest()->paginate(5)->withQueryString();
        return view('admin.categories.index', compact('categories', 'branches'));
    }

    public function createCategory()
    {
        $branches = Branch::orderBy('name')->get();
        return view('admin.categories.create', compact('branches'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'branch_id' => $validated['branch_id'],
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $data['image_path'] = $path;
        }

        Category::create($data);

        return redirect()->route('admin.categories')->with('success', 'Category created successfully!');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'branch_id' => $validated['branch_id'],
        ];

        if ($request->hasFile('image')) {
            if ($category->image_path) {
                Storage::disk('public')->delete($category->image_path);
            }
            $path = $request->file('image')->store('categories', 'public');
            $data['image_path'] = $path;
        }

        $category->update($data);

        return redirect()->route('admin.categories')->with('success', 'Category updated successfully!');
    }

    public function destroyCategory(Category $category)
    {
        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }
        $category->delete();

        return redirect()->route('admin.categories')->with('success', 'Category deleted successfully!');
    }

    public function services()
    {
        $branches = Branch::orderBy('name')->get();
        $selectedBranchId = request('branch_id');

        // Categories available for the selected branch (or all if none selected)
        $categories = Category::when($selectedBranchId, function($q) use ($selectedBranchId) {
                $q->where('branch_id', $selectedBranchId);
            })
            ->orderBy('name')
            ->get();

        $query = Service::query()->with(['category.branch', 'images']);

        if ($selectedBranchId) {
            $query->whereHas('category', function($q) use ($selectedBranchId) {
                $q->where('branch_id', $selectedBranchId);
            });
        }

        if (request('category_id')) {
            $query->where('category_id', request('category_id'));
        }

        if (request('q')) {
            $search = request('q');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $services = $query->latest()->paginate(5)->withQueryString();

        return view('admin.services.index', compact('services', 'branches', 'categories', 'selectedBranchId'));
    }

    public function createService()
    {
        $branches = Branch::orderBy('name')->get();
        $selectedBranchId = request('branch_id');
        $categories = Category::when($selectedBranchId, function($q) use ($selectedBranchId) {
                $q->where('branch_id', $selectedBranchId);
            })
            ->orderBy('name')
            ->get();
        return view('admin.services.create', compact('categories', 'branches', 'selectedBranchId'));
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1|max:480',
            'is_active' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated['is_active'] = $request->has('is_active');

        DB::transaction(function () use ($validated, $request) {
            $service = Service::create([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'duration_minutes' => $validated['duration_minutes'],
                'is_active' => $validated['is_active'],
            ]);

            if ($request->hasFile('images')) {
                $order = 0;
                foreach ($request->file('images') as $imageFile) {
                    $path = $imageFile->store('services', 'public');
                    ServiceImage::create([
                        'service_id' => $service->id,
                        'image_path' => $path,
                        'display_order' => $order++,
                    ]);
                }
            }
        });

        return redirect()->route('admin.services')->with('success', 'Service created successfully!');
    }

    public function editService(Service $service)
    {
        $branches = Branch::orderBy('name')->get();
        // Pre-select the branch by the service's category branch if present
        $selectedBranchId = optional($service->category)->branch_id;
        $categories = Category::when($selectedBranchId, function($q) use ($selectedBranchId) {
                $q->where('branch_id', $selectedBranchId);
            })
            ->orderBy('name')
            ->get();

        $service->load(['images', 'category.branch']);
        return view('admin.services.edit', compact('service', 'branches', 'categories', 'selectedBranchId'));
    }

    public function updateService(Request $request, Service $service)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1|max:480',
            'is_active' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_image_ids' => 'nullable|array',
            'remove_image_ids.*' => 'integer|exists:service_images,id',
        ]);

        $validated['is_active'] = $request->has('is_active');

        DB::transaction(function () use ($validated, $request, $service) {
            $service->update([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'duration_minutes' => $validated['duration_minutes'],
                'is_active' => $validated['is_active'],
            ]);

            // Remove selected existing images
            if (!empty($validated['remove_image_ids'])) {
                $images = ServiceImage::whereIn('id', $validated['remove_image_ids'])->where('service_id', $service->id)->get();
                foreach ($images as $img) {
                    Storage::disk('public')->delete($img->image_path);
                    $img->delete();
                }
            }

            // Append new uploaded images
            if ($request->hasFile('images')) {
                $maxOrder = (int) $service->images()->max('display_order');
                $order = $maxOrder + 1;
                foreach ($request->file('images') as $imageFile) {
                    $path = $imageFile->store('services', 'public');
                    ServiceImage::create([
                        'service_id' => $service->id,
                        'image_path' => $path,
                        'display_order' => $order++,
                    ]);
                }
            }
        });

        return redirect()->route('admin.services')->with('success', 'Service updated successfully!');
    }

    public function destroyService(Service $service)
    {
        DB::transaction(function () use ($service) {
            foreach ($service->images as $img) {
                Storage::disk('public')->delete($img->image_path);
                $img->delete();
            }
            $service->delete();
        });

        return redirect()->route('admin.services')->with('success', 'Service deleted successfully!');
    }

    public function timeSlots()
    {
        $branches = Branch::orderBy('name')->get();
        $query = TimeSlot::query()->with(['branch']);
        if (request('branch_id')) {
            $query->where('branch_id', request('branch_id'));
        }
        if (request('date')) {
            $query->whereDate('date', request('date'));
        }
        $slots = $query->latest()->paginate(5)->withQueryString();
        return view('admin.slots.index', compact('slots', 'branches'));
    }

    public function createTimeSlot()
    {
        $branches = Branch::orderBy('name')->get();
        return view('admin.slots.create', compact('branches'));
    }

    public function storeTimeSlot(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:h:i A',
            'end_time' => 'required|date_format:h:i A|after:start_time',
        ]);

        // Check for overlapping slots
        $overlapping = TimeSlot::where('branch_id', $validated['branch_id'])
            ->where('date', $validated['date'])
            ->where(function($query) use ($validated) {
                // Two slots overlap if: (new_start < existing_end) AND (new_end > existing_start)
                // This allows consecutive slots where one ends exactly when another begins
                $query->where('start_time', '<', $validated['end_time'])
                      ->where('end_time', '>', $validated['start_time']);
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors(['time' => 'This time slot overlaps with an existing slot.'])->withInput();
        }

        $validated['is_booked'] = false;
        TimeSlot::create($validated);

        return redirect()->route('admin.slots')->with('success', 'Time slot created successfully!');
    }

    public function editTimeSlot(TimeSlot $slot)
    {
        $branches = Branch::orderBy('name')->get();
        return view('admin.slots.edit', compact('slot', 'branches'));
    }

    public function updateTimeSlot(Request $request, TimeSlot $slot)
    {
        // Convert 12-hour format to 24-hour format if needed
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');
        
        // Try to parse as 12-hour format and convert to 24-hour
        if ($startTime && !preg_match('/^\d{2}:\d{2}$/', $startTime)) {
            try {
                $parsed = Carbon::createFromFormat('h:i A', $startTime);
                $request->merge(['start_time' => $parsed->format('H:i')]);
            } catch (\Exception $e) {
                try {
                    $parsed = Carbon::createFromFormat('g:i A', $startTime);
                    $request->merge(['start_time' => $parsed->format('H:i')]);
                } catch (\Exception $e2) {
                    // If parsing fails, let validation handle it
                }
            }
        }
        
        if ($endTime && !preg_match('/^\d{2}:\d{2}$/', $endTime)) {
            try {
                $parsed = Carbon::createFromFormat('h:i A', $endTime);
                $request->merge(['end_time' => $parsed->format('H:i')]);
            } catch (\Exception $e) {
                try {
                    $parsed = Carbon::createFromFormat('g:i A', $endTime);
                    $request->merge(['end_time' => $parsed->format('H:i')]);
                } catch (\Exception $e2) {
                    // If parsing fails, let validation handle it
                }
            }
        }
        
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:h:i A',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $overlapping = TimeSlot::where('branch_id', $validated['branch_id'])
            ->where('date', $validated['date'])
            ->where('id', '!=', $slot->id)
            ->where(function($query) use ($validated) {
                // Two slots overlap if: (new_start < existing_end) AND (new_end > existing_start)
                // This allows consecutive slots where one ends exactly when another begins
                $query->where('start_time', '<', $validated['end_time'])
                      ->where('end_time', '>', $validated['start_time']);
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors(['time' => 'This time slot overlaps with an existing slot.'])->withInput();
        }

        $slot->update($validated);

        return redirect()->route('admin.slots')->with('success', 'Time slot updated successfully!');
    }

    public function destroyTimeSlot(TimeSlot $slot)
    {
        $slot->delete();
        return redirect()->route('admin.slots')->with('success', 'Time slot deleted successfully!');
    }

    public function promotions()
    {
        // Auto-archive any promotions past end date
        Promotion::whereNotNull('ends_at')
            ->whereDate('ends_at', '<', now()->toDateString())
            ->where('status', '!=', 'archived')
            ->update(['status' => 'archived', 'is_active' => false]);

        $promotions = Promotion::latest()->paginate(5);
        return view('admin.promotions.index', compact('promotions'));
    }

    public function createPromotion()
    {
        return view('admin.promotions.create');
    }

    public function storePromotion(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:birthday,campaign',
            'description' => 'nullable|string|max:2000',
            'discount_percent' => 'nullable|integer|min:0|max:100',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'required|in:draft,active,archived,expired,upcoming',
            'is_active' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $promotion = null;
        DB::transaction(function () use (&$promotion, $validated, $request) {
            $promotion = Promotion::create([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'description' => $validated['description'] ?? null,
                'discount_percent' => $validated['discount_percent'] ?? null,
                'starts_at' => $validated['starts_at'] ?? null,
                'ends_at' => $validated['ends_at'] ?? null,
                'status' => $validated['status'],
                'is_active' => $validated['is_active'],
            ]);

            if ($request->hasFile('images')) {
                $order = 0;
                foreach ($request->file('images') as $imageFile) {
                    $path = $imageFile->store('promotions', 'public');
                    \App\Models\PromotionImage::create([
                        'promotion_id' => $promotion->id,
                        'image_path' => $path,
                        'display_order' => $order++,
                    ]);
                }
            }
        });

        return redirect()->route('admin.promotions')->with('success', 'Promotion created successfully!');
    }

    public function editPromotion(Promotion $promotion)
    {
        $promotion->load('images');
        return view('admin.promotions.edit', compact('promotion'));
    }

    public function updatePromotion(Request $request, Promotion $promotion)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:birthday,campaign',
            'description' => 'nullable|string|max:2000',
            'discount_percent' => 'nullable|integer|min:0|max:100',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'required|in:draft,active,archived,expired,upcoming',
            'is_active' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_image_ids' => 'nullable|array',
            'remove_image_ids.*' => 'integer|exists:promotion_images,id',
        ]);

        $validated['is_active'] = $request->has('is_active');

        DB::transaction(function () use ($validated, $request, $promotion) {
            $promotion->update([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'description' => $validated['description'] ?? null,
                'discount_percent' => $validated['discount_percent'] ?? null,
                'starts_at' => $validated['starts_at'] ?? null,
                'ends_at' => $validated['ends_at'] ?? null,
                'status' => $validated['status'],
                'is_active' => $validated['is_active'],
            ]);

            if (!empty($validated['remove_image_ids'])) {
                $images = \App\Models\PromotionImage::whereIn('id', $validated['remove_image_ids'])->where('promotion_id', $promotion->id)->get();
                foreach ($images as $img) {
                    Storage::disk('public')->delete($img->image_path);
                    $img->delete();
                }
            }

            if ($request->hasFile('images')) {
                $maxOrder = (int) $promotion->images()->max('display_order');
                $order = $maxOrder + 1;
                foreach ($request->file('images') as $imageFile) {
                    $path = $imageFile->store('promotions', 'public');
                    \App\Models\PromotionImage::create([
                        'promotion_id' => $promotion->id,
                        'image_path' => $path,
                        'display_order' => $order++,
                    ]);
                }
            }
        });

        return redirect()->route('admin.promotions')->with('success', 'Promotion updated successfully!');
    }

    public function destroyPromotion(Promotion $promotion)
    {
        DB::transaction(function () use ($promotion) {
            foreach ($promotion->images as $img) {
                Storage::disk('public')->delete($img->image_path);
                $img->delete();
            }
            $promotion->delete();
        });

        return redirect()->route('admin.promotions')->with('success', 'Promotion deleted successfully!');
    }

    // My Appointments methods
    public function myAppointments()
    {
        $appointments = Appointment::where('doctor_id', Auth::id())
            ->orWhere('status', 'pending')
            ->with(['patient', 'service', 'timeSlot', 'branch'])
            ->latest()
            ->paginate(5);

        return view('admin.my-appointments.index', compact('appointments'));
    }

    public function showAppointment(Appointment $appointment)
    {
        $appointment->load(['patient', 'service', 'timeSlot', 'branch']);
        return view('admin.my-appointments.show', compact('appointment'));
    }

    public function confirmAppointment(Request $request, Appointment $appointment)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
        ]);

        $appointment->update([
            'status' => 'confirmed',
            'doctor_id' => $request->doctor_id,
        ]);

        // Send notification to patient
        NotificationService::sendNotification(
            'Appointment Confirmed',
            "Your appointment has been confirmed for {$appointment->timeSlot->date->format('M d, Y')} at {$appointment->timeSlot->start_time}.",
            'appointment_confirmed',
            $appointment->patient_id
        );

        return redirect()->route('admin.my-appointments')->with('success', 'Appointment confirmed successfully!');
    }

    public function cancelAppointment(Request $request, Appointment $appointment)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:1000',
        ]);

        $appointment->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
        ]);

        // Send notification to patient
        NotificationService::sendNotification(
            'Appointment Cancelled',
            "Your appointment has been cancelled. Reason: {$request->cancellation_reason}",
            'appointment_cancelled',
            $appointment->patient_id
        );

        return redirect()->route('admin.my-appointments')->with('success', 'Appointment cancelled successfully!');
    }

    // Pending Appointments methods
    public function pendingAppointments()
    {
        $appointments = Appointment::where('status', 'confirmed')
            ->where('doctor_id', Auth::id())
            ->with(['patient', 'service', 'timeSlot', 'branch'])
            ->latest()
            ->paginate(5);

        return view('admin.pending-appointments.index', compact('appointments'));
    }

    public function addResult(Appointment $appointment)
    {
        $appointment->load(['patient', 'service', 'timeSlot', 'branch']);
        return view('admin.pending-appointments.add-result', compact('appointment'));
    }

    public function storeResult(Request $request, Appointment $appointment)
    {
        $request->validate([
            'consultation_result' => 'required|string|max:2000',
            'prescription' => 'nullable|string|max:1000',
            'follow_up_required' => 'boolean',
            'follow_up_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Create patient history record
        $patientHistory = \App\Models\PatientHistory::create([
            'patient_id' => $appointment->patient_id,
            'personal_information_id' => $appointment->personal_information_id,
            'appointment_id' => $appointment->id,
            'doctor_id' => Auth::id(),
            'consultation_result' => $request->consultation_result,
            'prescription' => $request->prescription,
            'follow_up_required' => $request->boolean('follow_up_required'),
            'follow_up_date' => $request->follow_up_date,
            'notes' => $request->notes,
        ]);

        // Update appointment status
        $appointment->update([
            'status' => 'completed',
        ]);

        // Send notification to patient
        NotificationService::sendNotification(
            'Consultation Result Available',
            "Your consultation result is now available. Please check your patient history for details.",
            'consultation_result',
            $appointment->patient_id
        );

        return redirect()->route('admin.pending-appointments')->with('success', 'Consultation result added successfully!');
    }
}
