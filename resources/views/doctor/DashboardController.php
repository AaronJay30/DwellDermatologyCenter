<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\User;
use App\Models\Promotion;
use App\Models\Service;
use App\Models\ServiceImage;
use App\Models\TimeSlot;
use App\Models\Appointment;
use App\Models\PatientHistory;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:doctor');
    }

    public function index()
    {
        // Get all admins with their statistics
        $admins = User::where('role', 'admin')
            ->with('branch')
            ->get()
            ->map(function ($admin) {
                $branchId = $admin->branch_id;
                
                // Get all appointments for this admin's branch
                $appointments = Appointment::where('branch_id', $branchId)
                    ->with(['patient', 'service', 'timeSlot'])
                    ->get();
                
                // Get unique patients
                $uniquePatients = $appointments->pluck('patient_id')->unique()->count();
                
                // Total consultations
                $totalConsultations = $appointments->count();
                
                // Last activity (most recent appointment update)
                $lastActivity = $appointments->max('updated_at');
                
                return [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'branch' => $admin->branch,
                    'total_patients' => $uniquePatients,
                    'total_consultations' => $totalConsultations,
                    'last_activity' => $lastActivity ? $lastActivity->format('M d, Y H:i') : 'No activity',
                    'last_activity_raw' => $lastActivity,
                ];
            });
        
        return view('doctor.dashboard', compact('admins'));
    }

    public function profile()
    {
        return view('doctor.profile');
    }

    public function updateProfile(Request $request)
    {
        $doctor = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $doctor->id,
            'specialty' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'specialty' => $validated['specialty'] ?? null,
            'contact_phone' => $validated['contact_phone'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'address' => $validated['address'] ?? null,
        ];

        // If contact_phone is set, also update phone field for consistency
        if (!empty($validated['contact_phone'])) {
            $userData['phone'] = $validated['contact_phone'];
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($doctor->profile_photo && Storage::disk('public')->exists($doctor->profile_photo)) {
                Storage::disk('public')->delete($doctor->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $userData['profile_photo'] = $path;
        }

        $doctor->update($userData);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!'
            ]);
        }

        return redirect()->route('doctor.profile')->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $doctor = Auth::user();
        
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Check current password
        if (!Hash::check($validated['current_password'], $doctor->password)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The current password is incorrect.'
                ], 422);
            }
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update password
        $doctor->update([
            'password' => Hash::make($validated['new_password'])
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully!'
            ]);
        }

        return redirect()->route('doctor.profile')->with('success', 'Password updated successfully!');
    }

   public function getAdminReports(Request $request, $adminId)
    {
        try {
            $admin = User::where('id', $adminId)->where('role', 'admin')->firstOrFail();

            $query = Appointment::where('branch_id', $admin->branch_id)
                ->with(['patient', 'service']);

            $query = $this->applyFilters($query, $request);

            $appointments = $query->latest()->paginate(3);

            return response()->json([
                'html' => view('doctor.partials.admin-reports-table', compact('appointments', 'admin'))->render(),
                'pagination' => (string) $appointments->links('pagination::bootstrap-4'),
                'total_records' => $appointments->total(),
            ]);
        } catch (\Exception $e) {
            Log::error("Dashboard Error: " . $e->getMessage());
            return response()->json(['error' => 'Data not found'], 404);
        }
    }

    private function applyFilters($query, $request)
    {
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('patient', fn($p) => $p->where('name', 'like', "%{$search}%"))
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $query;
    }


    public function getPatientDetails($appointmentId)
    {
        $appointment = Appointment::with(['patient', 'service', 'timeSlot', 'branch', 'patientHistory'])
            ->findOrFail($appointmentId);
        
        $patient = $appointment->patient;
        
        // Get the most relevant personal information record
        $personalInfo = null;
        if ($patient) {
            // Prefer records that actually have civil status (newer patient sheets),
            // falling back to default, then latest record.
            $personalInfo = $patient->personalInformation()
                ->whereNotNull('civil_status')
                ->orderByDesc('is_default')
                ->orderByDesc('created_at')
                ->first();

            if (!$personalInfo) {
                $personalInfo = $patient->personalInformation()
                    ->where('is_default', true)
                    ->first();
            }

            if (!$personalInfo) {
                $personalInfo = $patient->personalInformation()->latest()->first();
            }
        }
        
        // Get default medical information
        $medicalInfo = null;
        if ($patient) {
            $medicalInfo = $patient->medicalInformation()->where('is_default', true)->first();
            if (!$medicalInfo) {
                $medicalInfo = $patient->medicalInformation()->latest()->first();
            }
        }
        
        // Get default emergency contact
        $emergencyContact = null;
        if ($patient) {
            $emergencyContact = $patient->emergencyContacts()->where('is_default', true)->first();
            if (!$emergencyContact) {
                $emergencyContact = $patient->emergencyContacts()->latest()->first();
            }
        }
        
        $patientHistory = \App\Models\PatientHistory::where('patient_id', $appointment->patient_id)
            ->with(['doctor', 'appointment'])
            ->latest()
            ->get();
        
        return response()->json([
            'appointment' => [
                'id' => $appointment->id,
                'first_name' => $appointment->first_name,
                'middle_initial' => $appointment->middle_initial,
                'last_name' => $appointment->last_name,
                'date_of_birth' => $appointment->date_of_birth,
                'address' => $appointment->address,
                'medical_background' => $appointment->medical_background,
                'status' => $appointment->status,
            ],
            'patient' => $patient ? [
                'id' => $patient->id,
                'name' => $patient->name,
                'email' => $patient->email,
                'phone' => $patient->phone,
                'contact_phone' => $patient->contact_phone,
                'date_of_birth' => $patient->date_of_birth,
                'gender' => $patient->gender,
                'address' => $patient->address,
            ] : null,
            'personal_information' => $personalInfo ? [
                'first_name' => $personalInfo->first_name,
                'middle_initial' => $personalInfo->middle_initial,
                'last_name' => $personalInfo->last_name,
                'full_name' => $personalInfo->full_name,
                'address' => $personalInfo->address,
                'birthday' => $personalInfo->birthday ? $personalInfo->birthday->format('Y-m-d') : null,
                'contact_number' => $personalInfo->contact_number,
                'civil_status' => $personalInfo->civil_status,
                'preferred_pronoun' => $personalInfo->preferred_pronoun,
                'signature' => $personalInfo->signature,
            ] : null,
            'medical_information' => $medicalInfo ? [
                'hypertension' => $medicalInfo->hypertension,
                'diabetes' => $medicalInfo->diabetes,
                'comorbidities_others' => $medicalInfo->comorbidities_others,
                'allergies' => $medicalInfo->allergies,
                'medications' => $medicalInfo->medications,
                'anesthetics' => $medicalInfo->anesthetics,
                'anesthetics_others' => $medicalInfo->anesthetics_others,
                'previous_hospitalizations_surgeries' => $medicalInfo->previous_hospitalizations_surgeries,
                'smoker' => $medicalInfo->smoker,
                'alcoholic_drinker' => $medicalInfo->alcoholic_drinker,
                'known_family_illnesses' => $medicalInfo->known_family_illnesses,
            ] : null,
            'emergency_contact' => $emergencyContact ? [
                'name' => $emergencyContact->name,
                'relationship' => $emergencyContact->relationship,
                'address' => $emergencyContact->address,
                'contact_number' => $emergencyContact->contact_number,
            ] : null,
            'patient_history' => $patientHistory,
        ]);
    }

    public function downloadAdminReport(Request $request, $adminId)
    {
        $admin = User::where('id', $adminId)->where('role', 'admin')->firstOrFail();
        
        $query = Appointment::where('branch_id', $admin->branch_id)
            ->with(['patient', 'service', 'timeSlot', 'branch']);
        
        // Apply same filters as getAdminReports
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('patient', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhere('consultation_type', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('date_from')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('timeSlot', function($q) use ($request) {
                    $q->whereDate('date', '>=', $request->date_from);
                })->orWhereDate('appointments.created_at', '>=', $request->date_from);
            });
        }
        
        if ($request->filled('date_to')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('timeSlot', function($q) use ($request) {
                    $q->whereDate('date', '<=', $request->date_to);
                })->orWhereDate('appointments.created_at', '<=', $request->date_to);
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $appointments = $query->latest()->get();
        
        // Generate PDF using a simple HTML to PDF approach
        $html = view('doctor.partials.pdf-report', compact('appointments', 'admin'))->render();
        
        // For now, return HTML that can be printed. You can integrate a PDF library like dompdf later
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="admin-report-' . $admin->id . '.html"');
    }

    public function branches()
    {
        $branches = Branch::with(['users' => function($query) {
            $query->where('role', 'admin');
        }])->latest()->paginate(5);
        return view('doctor.branches.index', compact('branches'));
    }

    public function createBranch()
    {
        return view('doctor.branches.create');
    }

    public function storeBranch(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'country' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'municipality' => 'nullable|string|max:100',
            'barangay' => 'nullable|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'admin_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Build address from components if address field is not provided
        $address = $validated['address'];
        if (empty($address) && !empty($validated['barangay']) && !empty($validated['municipality']) && !empty($validated['province'])) {
            $address = $validated['barangay'] . ', ' . $validated['municipality'] . ', ' . $validated['province'] . ', ' . ($validated['country'] ?? 'Philippines');
        }

        DB::transaction(function () use ($validated, $request, $address) {
            $branch = Branch::create([
                'name' => $validated['name'],
                'address' => $address,
                'phone' => $validated['phone'],
                'email' => $validated['email'],
            ]);

            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'admin',
                'phone' => $validated['phone'],
                'address' => $address,
                'branch_id' => $branch->id,
            ];

            // Handle admin photo upload
            if ($request->hasFile('admin_photo')) {
                $path = $request->file('admin_photo')->store('admin-photos', 'public');
                $userData['profile_photo'] = $path;
            }

            User::create($userData);
        });

        return redirect()->route('doctor.branches')->with('success', 'Branch and admin user created successfully!');
    }

    public function editBranch(Branch $branch)
    {
        return view('doctor.branches.edit', compact('branch'));
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
            'admin_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        DB::transaction(function () use ($validated, $request, $branch, $adminUser) {
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
                
                // Handle admin photo upload
                if ($request->hasFile('admin_photo')) {
                    $path = $request->file('admin_photo')->store('admin-photos', 'public');
                    $adminUser->profile_photo = $path;
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
                
                // Handle admin photo upload
                if ($request->hasFile('admin_photo')) {
                    $path = $request->file('admin_photo')->store('admin-photos', 'public');
                    $data['profile_photo'] = $path;
                }
                
                User::create($data);
            }
        });

        return redirect()->route('doctor.branches')->with('success', 'Branch updated successfully!');
    }

    public function destroyBranch(Branch $branch)
    {
        DB::transaction(function () use ($branch) {
            User::where('branch_id', $branch->id)->where('role', 'admin')->delete();
            $branch->delete();
        });

        return redirect()->route('doctor.branches')->with('success', 'Branch deleted successfully!');
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
        return view('doctor.categories.index', compact('categories', 'branches'));
    }

    public function createCategory()
    {
        $branches = Branch::orderBy('name')->get();
        return view('doctor.categories.create', compact('branches'));
    }

    public function editCategory(Category $category)
    {
        $branches = Branch::orderBy('name')->get();
        $category->load('branch');

        return view('doctor.categories.edit', [
            'category' => $category,
            'branches' => $branches,
        ]);
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

        return redirect()->route('doctor.categories')->with('success', 'Category created successfully!');
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

        return redirect()->route('doctor.categories')->with('success', 'Category updated successfully!');
    }

    public function destroyCategory(Category $category)
    {
        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }
        $category->delete();

        return redirect()->route('doctor.categories')->with('success', 'Category deleted successfully!');
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

        return view('doctor.services.index', compact('services', 'branches', 'categories', 'selectedBranchId'));
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
        return view('doctor.services.create', compact('categories', 'branches', 'selectedBranchId'));
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
            'images.*' => 'image|mimes:jpg,jpeg,png,webp',
        ]);

        dd($validated);

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

        return redirect()->route('doctor.services')->with('success', 'Service created successfully!');
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
        return view('doctor.services.edit', compact('service', 'branches', 'categories', 'selectedBranchId'));
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
            'images.*' => 'image|mimes:jpg,jpeg,png,webp',
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

        return redirect()->route('doctor.services')->with('success', 'Service updated successfully!');
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

        return redirect()->route('doctor.services')->with('success', 'Service deleted successfully!');
    }

    public function timeSlots(Request $request)
    {
        // Auto-delete past available slots (not booked and no appointments)
        $today = now()->startOfDay();
        TimeSlot::where('date', '<', $today)
            ->where('is_booked', false)
            ->whereDoesntHave('appointments')
            ->delete();

        $branches = Branch::orderBy('name')->get();
        $scope = $request->get('scope', 'all');
        $search = $request->get('search');
        $filterDate = $request->get('date');
        $filterStatus = $request->get('status');
        $selectedBranchId = $request->get('branch_id');

        $baseQuery = TimeSlot::query()
            ->with(['branch', 'doctor', 'appointments.patient'])
            ->orderByDesc('date')
            ->orderBy('start_time');

        if ($scope !== 'all') {
            $baseQuery->where('doctor_id', Auth::id());
        }

        $statsQuery = clone $baseQuery;

        if ($selectedBranchId) {
            $baseQuery->where('branch_id', $selectedBranchId);
            $statsQuery->where('branch_id', $selectedBranchId);
        }

        if ($filterDate) {
            $baseQuery->whereDate('date', $filterDate);
            $statsQuery->whereDate('date', $filterDate);
        }

        if ($search) {
            $baseQuery->where(function ($query) use ($search) {
                $query->whereHas('branch', function ($branchQuery) use ($search) {
                    $branchQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhere('start_time', 'like', "%{$search}%")
                ->orWhere('end_time', 'like', "%{$search}%")
                ->orWhereDate('date', $search)
                ->orWhereHas('doctor', function ($doctorQuery) use ($search) {
                    $doctorQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('appointments', function ($appointmentQuery) use ($search) {
                    $appointmentQuery->whereNotNull('time_slot_id') // Only consultations
                        ->where(function ($q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhereHas('patient', function ($patientQuery) use ($search) {
                                    $patientQuery->where('name', 'like', "%{$search}%");
                                });
                        });
                });
            });
        }

        // Exclude booked slots from the slots page - they should appear in my-appointments instead
        // But still show pending appointments
        // Only show consultations (appointments with time_slot_id), exclude services
        $baseQuery->where(function ($query) {
            $query->where(function ($subQuery) {
                // Available slots (no appointments)
                $subQuery->where('is_booked', false)
                    ->whereDoesntHave('appointments');
            })->orWhere(function ($subQuery) {
                // Pending consultation appointments (still show on slots page)
                // Only show consultations, not services
                $subQuery->whereHas('appointments', function ($appointmentQuery) {
                    $appointmentQuery->where('status', 'pending')
                        ->whereNotNull('time_slot_id'); // Only consultations use time slots
                });
            });
        });

        if ($filterStatus === 'available') {
            $baseQuery->where('is_booked', false)
                ->whereDoesntHave('appointments');
        } elseif ($filterStatus === 'pending') {
            $baseQuery->whereHas('appointments', function ($query) {
                $query->where('status', 'pending')
                    ->whereNotNull('time_slot_id'); // Only consultations
            });
        }

        $slots = $baseQuery->paginate(5)->withQueryString();

        // Today's date for summary counts
        $todayDate = $today->format('Y-m-d');

        $availableCount = (clone $statsQuery)
            ->whereDate('date', $todayDate)
            ->where('is_booked', false)
            ->whereDoesntHave('appointments')
            ->count();

        $pendingCount = (clone $statsQuery)
            ->whereDate('date', $todayDate)
            ->whereHas('appointments', function ($query) {
                $query->where('status', 'pending')
                    ->whereNotNull('time_slot_id'); // Only consultations
            })
            ->count();

        $bookedCount = (clone $statsQuery)
            ->whereDate('date', $todayDate)
            ->where(function ($query) {
                $query->where('is_booked', true)
                    ->orWhereHas('appointments', function ($appointmentQuery) {
                        $appointmentQuery->whereIn('status', ['booked', 'scheduled', 'confirmed', 'completed'])
                            ->whereNotNull('time_slot_id'); // Only consultations
                    });
            })
            ->count();

        // Check for past dates with pending appointments
        $pastPendingAppointments = Appointment::where('status', 'pending')
            ->whereNotNull('time_slot_id')
            ->whereHas('timeSlot', function ($query) use ($today) {
                $query->where('date', '<', $today)
                    ->where('doctor_id', Auth::id());
            })
            ->with(['timeSlot.branch', 'patient'])
            ->get();

        

        return view('doctor.slots.index', [
            'slots' => $slots,
            'branches' => $branches,
            'search' => $search,
            'filterDate' => $filterDate,
            'filterStatus' => $filterStatus,
            'scope' => $scope,
            'selectedBranchId' => $selectedBranchId,
            'availableCount' => $availableCount,
            'pendingCount' => $pendingCount,
            'bookedCount' => $bookedCount,
            'pastPendingAppointments' => $pastPendingAppointments,
        ]);
    }

    public function createTimeSlot()
    {
        $branches = Branch::orderBy('name')->get();
        return view('doctor.slots.create', compact('branches'));
    }

    public function storeTimeSlot(Request $request)
    {
        // Handle date range input - support both single dates and date ranges
        if ($request->filled('date_range')) {
            $dateRange = trim($request->date_range);
            // Check if it contains " to " separator (range) or is a single date
            if (strpos($dateRange, ' to ') !== false) {
                $rangeParts = array_map('trim', explode(' to ', $dateRange));
                $rangeStart = $rangeParts[0] ?? null;
                $rangeEnd = $rangeParts[1] ?? $rangeStart;
            } else {
                // Single date selected
                $rangeStart = $dateRange;
                $rangeEnd = $dateRange;
            }

            // Ensure dates are in Y-m-d format
            if ($rangeStart) {
                try {
                    $rangeStart = \Carbon\Carbon::parse($rangeStart)->format('Y-m-d');
                } catch (\Exception $e) {
                    // If parsing fails, use as-is
                }
            }
            if ($rangeEnd) {
                try {
                    $rangeEnd = \Carbon\Carbon::parse($rangeEnd)->format('Y-m-d');
                } catch (\Exception $e) {
                    // If parsing fails, use as-is
                }
            }

            $mergePayload = [];
            if ($rangeStart && !$request->filled('start_date')) {
                $mergePayload['start_date'] = $rangeStart;
            }

            if ($rangeStart && !$request->filled('end_date')) {
                $mergePayload['end_date'] = $rangeEnd ?? $rangeStart;
            }

            if (!empty($mergePayload)) {
                $request->merge($mergePayload);
            }
        }

        // Ensure end_date is set if start_date is provided (for single-day ranges)
        if ($request->filled('start_date') && !$request->filled('end_date')) {
            $request->merge(['end_date' => $request->start_date]);
        }

        // Check if multiple slots were generated
        // Only process slots_data if it's not empty and is valid JSON
        $hasSlotsData = $request->has('slots_data') && 
                       !empty(trim($request->slots_data)) && 
                       $request->slots_data !== 'null' &&
                       $request->slots_data !== '[]';
        
        if ($hasSlotsData) {
            $slotsData = json_decode($request->slots_data, true);
            
            // Log for debugging
            \Log::info('Slots data received', [
                'slots_data_raw' => $request->slots_data,
                'slots_data_decoded' => $slotsData,
                'is_array' => is_array($slotsData),
                'count' => is_array($slotsData) ? count($slotsData) : 0
            ]);
            
            if (!is_array($slotsData) || empty($slotsData)) {
                \Log::error('Invalid slots data', [
                    'slots_data' => $request->slots_data,
                    'decoded' => $slotsData,
                    'json_error' => json_last_error_msg()
                ]);
                // If slots_data is invalid, fall through to single slot mode instead of returning error
                // This allows the form to still work if slots_data is corrupted
            } else {
                $branchId = $request->branch_id;
                if (!$branchId) {
                    return back()->withErrors(['branch_id' => 'Branch is required.'])->withInput();
                }

                $createdCount = 0;
                $skippedCount = 0;
                $errors = [];

                foreach ($slotsData as $slotData) {
                    // Ensure date is in Y-m-d format
                    try {
                        $parsedDate = \Carbon\Carbon::parse($slotData['date']);
                        $formattedDate = $parsedDate->format('Y-m-d');
                    } catch (\Exception $e) {
                        $skippedCount++;
                        continue;
                    }
                    
                    // Validate each slot
                    $validated = [
                        'branch_id' => $branchId,
                        'date' => $formattedDate,
                        'start_time' => $slotData['start_time'],
                        'end_time' => $slotData['end_time'],
                        'consultation_fee' => $request->consultation_fee ?? null,
                    ];

                    // Validate date - compare date strings directly to avoid timezone issues
                    $selectedDateStr = $formattedDate;
                    $todayStr = \Carbon\Carbon::today()->format('Y-m-d');
                    
                    // Allow today and future dates only
                    if ($selectedDateStr < $todayStr) {
                        $skippedCount++;
                        continue;
                    }

                    // Check for overlapping slots
                    $overlapping = TimeSlot::where('branch_id', $validated['branch_id'])
                        ->where('doctor_id', Auth::id())
                        ->where('date', $validated['date'])
                        ->where(function($query) use ($validated) {
                            // Two slots overlap if: (new_start < existing_end) AND (new_end > existing_start)
                            // This allows consecutive slots where one ends exactly when another begins
                            $query->where('start_time', '<', $validated['end_time'])
                                  ->where('end_time', '>', $validated['start_time']);
                        })
                        ->exists();

                    if (!$overlapping) {
                        $validated['is_booked'] = false;
                        $validated['doctor_id'] = Auth::id();
                        
                        try {
                            TimeSlot::create($validated);
                            $createdCount++;
                        } catch (\Exception $e) {
                            \Log::error('Failed to create time slot', [
                                'error' => $e->getMessage(),
                                'validated' => $validated,
                                'trace' => $e->getTraceAsString()
                            ]);
                            $skippedCount++;
                            $errors[] = "Failed to create slot for {$formattedDate} {$validated['start_time']}-{$validated['end_time']}: " . $e->getMessage();
                        }
                    } else {
                        $skippedCount++;
                    }
                }

                if ($createdCount === 0 && $skippedCount > 0) {
                    $errorMessage = "No slots were created. {$skippedCount} slot(s) were skipped due to overlaps or invalid dates.";
                    if (!empty($errors)) {
                        $errorMessage .= " Errors: " . implode(', ', $errors);
                    }
                    return back()->withErrors(['time' => $errorMessage])->withInput();
                }
                
                $message = "Successfully created {$createdCount} time slot(s).";
                if ($skippedCount > 0) {
                    $message .= " {$skippedCount} slot(s) were skipped due to overlaps or invalid dates.";
                }
                if (!empty($errors)) {
                    $message .= " Some errors occurred: " . implode(', ', array_slice($errors, 0, 3));
                }

                return redirect()->route('doctor.slots')->with('success', $message);
            }
        }

        // Single slot mode (backward compatibility)
        // Ensure end_date equals start_date if not provided or if they're the same (single-day range)
        if ($request->filled('start_date') && (!$request->filled('end_date') || $request->start_date === $request->end_date)) {
            $request->merge(['end_date' => $request->start_date]);
        }
        
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'start_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    // Compare date strings directly to avoid timezone issues
                    $selectedDateStr = \Carbon\Carbon::parse($value)->format('Y-m-d');
                    $todayStr = \Carbon\Carbon::today()->format('Y-m-d');
                    // Allow today and future dates
                    if ($selectedDateStr < $todayStr) {
                        $fail('The start date must be today or a future date.');
                    }
                },
            ],
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) use ($request) {
                    // If the selected date is today, check if the time has passed
                    $startDate = $request->input('start_date');
                    if ($startDate && $value) {
                        try {
                            // Parse the selected date
                            $selectedDate = \Carbon\Carbon::parse($startDate);
                            $selectedDateStr = $selectedDate->format('Y-m-d');
                            
                            // Get today's date
                            $today = \Carbon\Carbon::today();
                            $todayStr = $today->format('Y-m-d');
                            
                            // If the selected date is today, check if the time has passed
                            if ($selectedDateStr === $todayStr) {
                                // Get current date and time
                                $now = \Carbon\Carbon::now();
                                
                                // Combine the selected date with the selected time
                                $selectedDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $selectedDateStr . ' ' . $value);
                                
                                // Compare: if selected time is before current time, it's in the past
                                if ($selectedDateTime->lt($now)) {
                                    $fail('The start time cannot be in the past for today\'s date.');
                                }
                            }
                        } catch (\Exception $e) {
                            // If parsing fails, let other validations handle it
                        }
                    }
                },
            ],
            'end_time' => 'required|date_format:H:i|after:start_time',
            'consultation_fee' => 'required|numeric|min:0',
        ]);

        // Create slots for each date in the range
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = \Carbon\Carbon::parse($validated['end_date']);
        $currentDate = $startDate->copy();
        
        $createdCount = 0;
        $skippedCount = 0;
        $errors = [];
        
        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $todayStr = \Carbon\Carbon::today()->format('Y-m-d');
            
            // Skip past dates
            if ($dateStr < $todayStr) {
                $skippedCount++;
                $currentDate->addDay();
                continue;
            }
            
            // Check if time has passed for today's date
            if ($dateStr === $todayStr) {
                $now = \Carbon\Carbon::now();
                $selectedDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $dateStr . ' ' . $validated['start_time']);
                if ($selectedDateTime->lt($now)) {
                    $skippedCount++;
                    $currentDate->addDay();
                    continue;
                }
            }
            
            // Check for overlapping slots
            $overlapping = TimeSlot::where('branch_id', $validated['branch_id'])
                ->where('doctor_id', Auth::id())
                ->where('date', $dateStr)
                ->where(function($query) use ($validated) {
                    // Two slots overlap if: (new_start < existing_end) AND (new_end > existing_start)
                    // This allows consecutive slots where one ends exactly when another begins
                    $query->where('start_time', '<', $validated['end_time'])
                          ->where('end_time', '>', $validated['start_time']);
                })
                ->exists();
            
            if (!$overlapping) {
                TimeSlot::create([
                    'branch_id' => $validated['branch_id'],
                    'doctor_id' => Auth::id(),
                    'date' => $dateStr,
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'],
                    'consultation_fee' => $validated['consultation_fee'],
                    'is_booked' => false,
                ]);
                $createdCount++;
            } else {
                $skippedCount++;
            }
            
            $currentDate->addDay();
        }
        
        if ($createdCount === 0) {
            return back()->withErrors(['time' => 'No slots were created. All dates either have overlapping slots or are in the past.'])->withInput();
        }
        
        $message = "Successfully created {$createdCount} time slot(s).";
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} slot(s) were skipped due to overlaps or invalid dates.";
        }
        
        return redirect()->route('doctor.slots')->with('success', $message);
    }

    public function editTimeSlot(TimeSlot $slot)
    {
        // Allow doctors to edit any slot (including admin-created slots)
        $branches = Branch::orderBy('name')->get();
        return view('doctor.slots.edit', compact('slot', 'branches'));
    }

    public function updateTimeSlot(Request $request, TimeSlot $slot)
    {
        
        if ($request->filled('start_time')) {
            try {
                $request->merge([
                    'start_time' => Carbon::createFromFormat('H:i', $request->start_time)
                        ->format('h:i A'),
                ]);
            } catch (\Exception $e) {}
        }

        if ($request->filled('end_time')) {
            try {
                $request->merge([
                    'end_time' => Carbon::createFromFormat('H:i', $request->end_time)
                        ->format('h:i A'),
                ]);
            } catch (\Exception $e) {}
        }

        // Allow doctors to update any slot (including admin-created slots)
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:h:i A',
            'end_time' => 'required|date_format:h:i A|after:start_time',
            'consultation_fee' => 'required|numeric|min:0',
        ]);

        $overlapping = TimeSlot::where('branch_id', $validated['branch_id'])
            ->where('doctor_id', Auth::id())
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

        return redirect()->route('doctor.slots')->with('success', 'Time slot updated successfully!');
    }

    public function destroyTimeSlot(TimeSlot $slot)
    {
        // Allow doctors to delete any slot (including admin-created slots)
        $slot->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Time slot deleted successfully!'
        ]);
    }

    public function acceptAppointment(Request $request, Appointment $appointment)
    {
        // Validate request
        $request->validate([
            'doctor_name' => 'required|string|max:255',
        ]);

        // Allow doctors to accept all appointments (removed doctor_id restriction)
        // Verify appointment has a time slot
        if (!$appointment->timeSlot) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment does not have an associated time slot.'
            ], 400);
        }

        // Verify appointment is pending
        if ($appointment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending appointments can be accepted.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Update appointment status to booked and set doctor_id
            // Store doctor name in admin_note or create a note field
            $appointment->update([
                'status' => 'booked',
                'doctor_id' => Auth::id(),
                'admin_note' => 'Doctor: ' . $request->doctor_name,
            ]);

            // Mark time slot as booked
            if ($appointment->timeSlot) {
                $appointment->timeSlot->update(['is_booked' => true]);
            }

            // Send notification to patient
            $message = "Your appointment for {$appointment->timeSlot->date->format('M d, Y')} at {$appointment->timeSlot->start_time} has been confirmed. Assigned doctor: {$request->doctor_name}";

            NotificationService::sendNotification(
                'Appointment Confirmed',
                $message,
                'appointment_confirmed',
                $appointment->patient_id
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment accepted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while accepting the appointment.'
            ], 500);
        }
    }

    public function rejectAppointment(Request $request, Appointment $appointment)
    {
        // Allow doctors to reject all appointments (removed doctor_id restriction)
        // Verify appointment has a time slot
        if (!$appointment->timeSlot) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment does not have an associated time slot.'
            ], 400);
        }

        // Verify appointment is pending
        if ($appointment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending appointments can be rejected.'
            ], 400);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Update appointment status to cancelled
            $appointment->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->rejection_reason,
            ]);

            // Send notification to patient with rejection reason
            $message = "Your appointment request for {$appointment->timeSlot->date->format('M d, Y')} at {$appointment->timeSlot->start_time} has been rejected. Reason: {$request->rejection_reason}";

            NotificationService::sendNotification(
                'Appointment Rejected',
                $message,
                'appointment_rejected',
                $appointment->patient_id
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment rejected successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while rejecting the appointment.'
            ], 500);
        }
    }

    public function promotions()
    {
        // Auto-archive any promotions past end date
        Promotion::whereNotNull('ends_at')
            ->whereDate('ends_at', '<', now()->toDateString())
            ->where('status', '!=', 'archived')
            ->update(['status' => 'archived', 'is_active' => false]);

        $promotions = Promotion::latest()->paginate(5);
        return view('doctor.promotions.index', compact('promotions'));
    }

    public function createPromotion()
    {
        return view('doctor.promotions.create');
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
            'images.*' => 'image|mimes:jpg,jpeg,png,webp',
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

        return redirect()->route('doctor.promotions')->with('success', 'Promotion created successfully!');
    }

    public function editPromotion(Promotion $promotion)
    {
        $promotion->load('images');
        return view('doctor.promotions.edit', compact('promotion'));
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
            'images.*' => 'image|mimes:jpg,jpeg,png,webp',
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

        return redirect()->route('doctor.promotions')->with('success', 'Promotion updated successfully!');
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

        return redirect()->route('doctor.promotions')->with('success', 'Promotion deleted successfully!');
    }

    // My Appointments methods
    public function myAppointments(Request $request)
    {
        $search = $request->input('search', '');
        
        // Only show consultations (appointments with time_slot_id)
        // Show appointments where:
        // 1. doctor_id matches the current doctor, OR
        // 2. status is pending (for doctor to accept), OR
        // 3. time slot's doctor_id matches the current doctor (for accepted appointments)
        $query = Appointment::where(function($q) {
                $q->where('doctor_id', Auth::id())
                  ->orWhere('status', 'pending')
                  ->orWhereHas('timeSlot', function($timeSlotQuery) {
                      $timeSlotQuery->where('doctor_id', Auth::id());
                  });
            })
            ->whereIn('status', ['booked', 'scheduled', 'confirmed', 'completed', 'pending'])
            ->whereNotNull('time_slot_id') // Only consultations use time slots
            ->with(['patient', 'service', 'timeSlot', 'branch', 'doctorSlot']);
        
        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%');
                })
                ->orWhere('first_name', 'like', '%' . $search . '%')
                ->orWhere('last_name', 'like', '%' . $search . '%')
                ->orWhereHas('service', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%');
                })
                ->orWhere('consultation_type', 'like', '%' . $search . '%')
                ->orWhereHas('timeSlot', function ($subQuery) use ($search) {
                    $subQuery->where('date', 'like', '%' . $search . '%')
                        ->orWhere('start_time', 'like', '%' . $search . '%')
                        ->orWhere('end_time', 'like', '%' . $search . '%');
                })
                ->orWhereHas('doctorSlot', function ($subQuery) use ($search) {
                    $subQuery->where('slot_date', 'like', '%' . $search . '%')
                        ->orWhere('start_time', 'like', '%' . $search . '%')
                        ->orWhere('end_time', 'like', '%' . $search . '%');
                });
            });
        }
        
        $appointments = $query->latest()->get();

        return view('doctor.my-appointments.index', compact('appointments', 'search'));
    }

    public function showAppointment(Appointment $appointment)
    {
        $appointment->load(['patient', 'service', 'timeSlot', 'branch']);
        return view('doctor.my-appointments.show', compact('appointment'));
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

        return redirect()->route('doctor.my-appointments')->with('success', 'Appointment confirmed successfully!');
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

        // Note: Doctor is already aware since they cancelled it, but we could notify other doctors in the branch if needed

        return redirect()->route('doctor.my-appointments')->with('success', 'Appointment cancelled successfully!');
    }

    // My Services Schedules methods
    public function myServicesSchedules(Request $request)
    {
        $search = $request->input('search', '');
        
        // Only show pending services (appointments with service_id but no time_slot_id)
        // Confirmed appointments appear on the confirmed page
        $query = Appointment::where('status', 'pending') // Only show pending appointments
            ->where(function($q) {
                $q->where('doctor_id', Auth::id())
                  ->orWhere('status', 'pending'); // This is redundant but kept for consistency with past pending logic
            })
            ->whereNotNull('service_id') // Has a service
            ->whereNull('time_slot_id') // But no time slot (not a consultation)
            ->with(['patient', 'service', 'branch']);
        
        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%');
                })
                ->orWhere('first_name', 'like', '%' . $search . '%')
                ->orWhere('last_name', 'like', '%' . $search . '%')
                ->orWhereHas('service', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('branch', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%');
                });
            });
        }
        
        $appointments = $query->latest()->get();

        // Check for past dates with pending appointments
        $today = \Carbon\Carbon::today();
        $pastPendingAppointments = Appointment::where('status', 'pending')
            ->whereNotNull('service_id')
            ->whereNull('time_slot_id')
            ->where(function($q) {
                $q->where('doctor_id', Auth::id())
                  ->orWhere('status', 'pending');
            })
            ->where(function($q) use ($today) {
                $q->where(function($subQ) use ($today) {
                    $subQ->whereNotNull('scheduled_date')
                        ->whereDate('scheduled_date', '<', $today);
                })->orWhere(function($subQ) use ($today) {
                    $subQ->whereNull('scheduled_date')
                        ->whereDate('created_at', '<', $today);
                });
            })
            ->with(['patient', 'service', 'branch'])
            ->get();

        return view('doctor.my-services-schedules.index', compact('appointments', 'search', 'pastPendingAppointments'));
    }

    public function myServicesSchedulesConfirmed(Request $request)
    {
        $search = $request->input('search', '');
        
        // No branch restrictions - doctors can see all confirmed service appointments
        $query = Appointment::where('status', 'confirmed')
            ->whereNotNull('service_id')
            ->whereNull('time_slot_id')
            ->with(['patient', 'service', 'branch', 'personalInformation']);
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%');
                })
                ->orWhere('first_name', 'like', '%' . $search . '%')
                ->orWhere('last_name', 'like', '%' . $search . '%')
                ->orWhereHas('personalInformation', function ($subQuery) use ($search) {
                    $subQuery->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%')
                        ->orWhere('middle_initial', 'like', '%' . $search . '%');
                })
                ->orWhereHas('service', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('branch', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%');
                });
            });
        }
        
        $appointments = $query->latest()->get();
        
        // Check for past dates with pending appointments (no branch restriction)
        $today = \Carbon\Carbon::today();
        $pastPendingAppointments = Appointment::where('status', 'pending')
            ->whereNotNull('service_id')
            ->whereNull('time_slot_id')
            ->where(function($q) use ($today) {
                $q->where(function($subQ) use ($today) {
                    $subQ->whereNotNull('scheduled_date')
                        ->whereDate('scheduled_date', '<', $today);
                })->orWhere(function($subQ) use ($today) {
                    $subQ->whereNull('scheduled_date')
                        ->whereDate('created_at', '<', $today);
                });
            })
            ->with(['patient', 'service', 'branch'])
            ->get();
        
        // Check for past dates with confirmed appointments (no branch restriction)
        $pastConfirmedAppointments = Appointment::where('status', 'confirmed')
            ->whereNotNull('service_id')
            ->whereNull('time_slot_id')
            ->where(function($q) use ($today) {
                $q->where(function($subQ) use ($today) {
                    $subQ->whereNotNull('scheduled_date')
                        ->whereDate('scheduled_date', '<', $today);
                })->orWhere(function($subQ) use ($today) {
                    $subQ->whereNull('scheduled_date')
                        ->whereDate('created_at', '<', $today);
                });
            })
            ->with(['patient', 'service', 'branch'])
            ->get();
        
        $pageTitle = 'CONFIRMED SERVICE SCHEDULES';
        $searchRoute = 'doctor.my-services-schedules.confirmed';
        
        return view('doctor.my-services-schedules.confirmed', compact('appointments', 'search', 'pageTitle', 'searchRoute', 'pastPendingAppointments', 'pastConfirmedAppointments'));
    }

    public function showServiceSchedule(Appointment $appointment)
    {
        $appointment->load(['patient', 'service', 'branch']);
        return view('doctor.my-services-schedules.show', compact('appointment'));
    }

    public function confirmServiceSchedule(Request $request, Appointment $appointment)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'scheduled_time' => 'required|date_format:H:i',
            'scheduled_date' => 'nullable|date|after_or_equal:today',
        ]);

        $updateData = [
            'status' => 'confirmed',
            'doctor_id' => $request->doctor_id,
            'scheduled_time' => $request->scheduled_time,
        ];

        // Only update scheduled_date if provided (for rescheduling)
        if ($request->filled('scheduled_date')) {
            $updateData['scheduled_date'] = $request->scheduled_date;
        } else {
            // Use the original booking date if no reschedule date provided
            $updateData['scheduled_date'] = $appointment->created_at->toDateString();
        }

        $appointment->update($updateData);

        // Send notification to patient
        $dateStr = \Carbon\Carbon::parse($updateData['scheduled_date'])->format('M d, Y');
        $timeStr = \Carbon\Carbon::parse($request->scheduled_time)->format('g:i A');
        
        NotificationService::sendNotification(
            'Service Appointment Confirmed',
            "Your service appointment has been confirmed for {$dateStr} at {$timeStr}.",
            'service_appointment_confirmed',
            $appointment->patient_id
        );

        // Send notification to assigned doctor if different from current doctor
        if ($request->doctor_id && $request->doctor_id != Auth::id()) {
            $isRescheduled = $request->filled('scheduled_date') && $appointment->scheduled_date != $request->scheduled_date;
            $notificationTitle = $isRescheduled ? 'Appointment Rescheduled' : 'New Appointment Assigned';
            $notificationMessage = $isRescheduled 
                ? "Appointment for {$appointment->first_name} {$appointment->last_name} has been rescheduled to {$dateStr} at {$timeStr}."
                : "You have been assigned a new appointment for {$appointment->first_name} {$appointment->last_name} on {$dateStr} at {$timeStr}.";
            
            NotificationService::sendNotification(
                $notificationTitle,
                $notificationMessage,
                $isRescheduled ? 'appointment_rescheduled' : 'appointment_assigned',
                $request->doctor_id
            );
        }

        // Redirect back to the page we came from (check referrer or default to confirmed page)
        $referrer = request()->headers->get('referer');
        if ($referrer && str_contains($referrer, '/confirmed')) {
            return redirect()->route('doctor.my-services-schedules.confirmed')->with('success', 'Service appointment confirmed successfully!');
        }
        return redirect()->route('doctor.my-services-schedules')->with('success', 'Service appointment confirmed successfully!');
    }

    public function cancelServiceSchedule(Request $request, Appointment $appointment)
    {
        // No branch restrictions - doctors can cancel all appointments
        $request->validate([
            'cancellation_reason' => 'required|string|max:1000',
        ]);

        $appointment->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
        ]);

        // Send notification to patient
        NotificationService::sendNotification(
            'Service Appointment Cancelled',
            "Your service appointment has been cancelled. Reason: {$request->cancellation_reason}",
            'service_appointment_cancelled',
            $appointment->patient_id
        );

        // Redirect back to the page we came from (check referrer or default to confirmed page)
        $referrer = request()->headers->get('referer');
        if ($referrer && str_contains($referrer, '/confirmed')) {
            return redirect()->route('doctor.my-services-schedules.confirmed')->with('success', 'Service appointment cancelled successfully!');
        }
        return redirect()->route('doctor.my-services-schedules')->with('success', 'Service appointment cancelled successfully!');
    }

    public function storeServiceResult(Request $request, Appointment $appointment)
    {
        // No branch restrictions - doctors can add results for all appointments
        $request->validate([
            'before_condition' => 'nullable|array',
            'before_condition.*' => 'nullable|string|max:500',
            'result' => 'nullable|array',
            'result.*' => 'nullable|string|max:500',
            'procedures' => 'nullable|array',
            'procedures.*' => 'nullable|string|max:500',
            'medication' => 'nullable|array',
            'medication.*' => 'nullable|string|max:500',
            'follow_up_required' => 'nullable|boolean',
            'follow_up_date' => 'nullable|date|after_or_equal:today',
        ]);

        try {
            // Build consultation result from bullet point arrays
            $consultationResult = [
                'before_condition' => array_filter($request->before_condition ?? []),
                'result' => array_filter($request->result ?? []),
                'procedures' => array_filter($request->procedures ?? []),
                'medication' => array_filter($request->medication ?? []),
                'follow_up' => [
                    'required' => $request->has('follow_up_required') && $request->follow_up_required,
                    'date' => $request->follow_up_date ?? null,
                ],
            ];

            // Create patient history record
            $patientHistory = \App\Models\PatientHistory::create([
                'patient_id' => $appointment->patient_id,
                'personal_information_id' => $appointment->personal_information_id,
                'appointment_id' => $appointment->id,
                'doctor_id' => Auth::id(),
                'treatment_notes' => 'Service result added',
                'treatment_date' => now()->toDateString(),
                'consultation_result' => json_encode($consultationResult),
            ]);

            // Update appointment status to completed
            $appointment->update([
                'status' => 'completed',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Result added successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the result: ' . $e->getMessage()
            ], 500);
        }
    }

    // Pending Appointments methods
    public function pendingAppointments()
    {
        $appointments = Appointment::where('status', 'confirmed')
            ->where('doctor_id', Auth::id())
            ->with(['patient', 'service', 'timeSlot', 'branch'])
            ->latest()
            ->paginate(5);

        return view('doctor.pending-appointments.index', compact('appointments'));
    }

    public function addResult(Appointment $appointment)
    {
        $appointment->load(['patient', 'service', 'timeSlot', 'branch']);
        return view('doctor.pending-appointments.add-result', compact('appointment'));
    }

    public function storeResult(Request $request, Appointment $appointment)
    {
        $request->validate([
            'before_photos.*' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10240',
            'after_photos.*' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10240',
            'skin_condition_before' => 'nullable|array',
            'skin_condition_before.*' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:2000',
            'results' => 'required|array|min:1',
            'results.*' => 'required|string|max:500',
            'treatment_plan' => 'required|string|max:5000',
            'follow_up_required' => 'boolean',
            'follow_up_date' => 'nullable|date|after:today',
        ]);

        // Verify the appointment belongs to the doctor
        if ($appointment->doctor_id !== Auth::id() && (!$appointment->timeSlot || $appointment->timeSlot->doctor_id !== Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to add result for this appointment.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Handle before photos upload
            $beforePhotos = [];
            if ($request->hasFile('before_photos')) {
                foreach ($request->file('before_photos') as $file) {
                    $path = $file->store('appointment-results/before/photos', 'public');
                    $beforePhotos[] = $path;
                }
            }

            // Handle after photos upload
            $afterPhotos = [];
            if ($request->hasFile('after_photos')) {
                foreach ($request->file('after_photos') as $file) {
                    $path = $file->store('appointment-results/after/photos', 'public');
                    $afterPhotos[] = $path;
                }
            }

            // Filter out empty values from arrays
            $skinConditionBefore = array_filter($request->input('skin_condition_before', []), function($item) {
                return !empty(trim($item));
            });

            $results = array_filter($request->input('results', []), function($item) {
                return !empty(trim($item));
            });

            // Build consultation result JSON
            $consultationResult = [
                'before' => [
                    'photos' => $beforePhotos,
                    'skin_condition' => array_values($skinConditionBefore),
                ],
                'after' => [
                    'photos' => $afterPhotos,
                ],
                'results' => array_values($results),
                'treatment_plan' => $request->treatment_plan,
            ];

            // Create patient history record
            $patientHistory = \App\Models\PatientHistory::create([
                'patient_id' => $appointment->patient_id,
                'personal_information_id' => $appointment->personal_information_id,
                'appointment_id' => $appointment->id,
                'doctor_id' => Auth::id(),
                'consultation_result' => json_encode($consultationResult),
                'treatment_notes' => $request->treatment_plan,
                'notes' => $request->notes,
                'follow_up_required' => $request->boolean('follow_up_required'),
                'follow_up_date' => $request->follow_up_date,
                'treatment_date' => now()->toDateString(),
            ]);

            // Update appointment status
            $appointment->update([
                'status' => 'completed',
            ]);

            DB::commit();

            // Send notification to patient
            NotificationService::sendNotification(
                'Consultation Result Available',
                "Your consultation result is now available. Please check your patient history for details.",
                'consultation_result',
                $appointment->patient_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Result added successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the result: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteAppointment(Appointment $appointment)
    {
        // Verify the appointment belongs to the doctor
        if ($appointment->doctor_id !== Auth::id() && (!$appointment->timeSlot || $appointment->timeSlot->doctor_id !== Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this appointment.'
            ], 403);
        }

        // Only allow deletion of non-completed appointments
        if ($appointment->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete completed appointments.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Check if this is a past appointment (pending or confirmed)
            $today = now()->startOfDay();
            $isPastAppointment = false;
            $isPastPending = false;
            $isPastConfirmed = false;
            
            if ($appointment->status === 'pending') {
                // For consultation appointments (with time slot)
                if ($appointment->timeSlot && $appointment->timeSlot->date < $today) {
                    $isPastAppointment = true;
                    $isPastPending = true;
                }
                // For service appointments (with scheduled_date)
                elseif ($appointment->service_id && !$appointment->time_slot_id) {
                    $appointmentDate = $appointment->scheduled_date 
                        ? \Carbon\Carbon::parse($appointment->scheduled_date)->startOfDay()
                        : $appointment->created_at->startOfDay();
                    if ($appointmentDate < $today) {
                        $isPastAppointment = true;
                        $isPastPending = true;
                    }
                }
            } elseif ($appointment->status === 'confirmed') {
                // For service appointments (with scheduled_date)
                if ($appointment->service_id && !$appointment->time_slot_id) {
                    $appointmentDate = $appointment->scheduled_date 
                        ? \Carbon\Carbon::parse($appointment->scheduled_date)->startOfDay()
                        : $appointment->created_at->startOfDay();
                    if ($appointmentDate < $today) {
                        $isPastAppointment = true;
                        $isPastConfirmed = true;
                    }
                }
            }

            // Free up the time slot if it exists
            if ($appointment->timeSlot) {
                $appointment->timeSlot->update(['is_booked' => false]);
            }

            // If it's a past appointment, send notification to patient
            if ($isPastAppointment) {
                if ($appointment->timeSlot) {
                    // Consultation appointment
                    $dateStr = $appointment->timeSlot->date->format('M d, Y');
                    $timeStr = $appointment->timeSlot->start_time;
                    $message = "Your appointment scheduled for {$dateStr} at {$timeStr} has been declined because you did not show up. Please book another slot if you still need a consultation.";
                } else {
                    // Service appointment
                    $appointmentDate = $appointment->scheduled_date 
                        ? \Carbon\Carbon::parse($appointment->scheduled_date)->format('M d, Y')
                        : $appointment->created_at->format('M d, Y');
                    $timeStr = $appointment->scheduled_time 
                        ? \Carbon\Carbon::parse($appointment->scheduled_time)->format('g:i A')
                        : '';
                    $serviceName = $appointment->service ? $appointment->service->name : 'service';
                    $message = "Your {$serviceName} appointment scheduled for {$appointmentDate}" . ($timeStr ? " at {$timeStr}" : '') . " has been declined because you did not show up. Please book another appointment if you still need the service.";
                }
                
                NotificationService::sendNotification(
                    'Appointment Declined',
                    $message,
                    'appointment_declined',
                    $appointment->patient_id
                );
            }

            // Delete the appointment
            $appointment->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment deleted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the appointment.'
            ], 500);
        }
    }

    // History methods
    public function history(Request $request)
    {
        $search = $request->get('search', '');
        
        // Get all unique patients who have history (doctors can see all patient history)
        $patientsWithHistory = collect();
        
        // Get patients from all completed appointments
        $completedAppointments = Appointment::where('status', 'completed')
            ->with(['patient'])
            ->get();
        
        // Get patients from all history records
        $historyRecords = \App\Models\PatientHistory::with(['patient'])
            ->get();
        
        // Combine and get unique patients
        $allPatients = collect();
        foreach ($completedAppointments as $appointment) {
            if ($appointment->patient) {
                $allPatients->push($appointment->patient);
            }
        }
        foreach ($historyRecords as $hist) {
            if ($hist->patient) {
                $allPatients->push($hist->patient);
            }
        }
        
        // Get unique patients with their history count (doctors can see all)
        $uniquePatients = $allPatients->unique('id')->map(function ($patient) use ($completedAppointments, $historyRecords) {
            $appointmentCount = $completedAppointments->where('patient_id', $patient->id)->count();
            $historyCount = $historyRecords->where('patient_id', $patient->id)->count();
            $totalCount = $appointmentCount + $historyCount;
            
            // Get latest date
            $latestAppointment = $completedAppointments->where('patient_id', $patient->id)->sortByDesc('created_at')->first();
            $latestHistory = $historyRecords->where('patient_id', $patient->id)->sortByDesc('treatment_date')->first();
            
            $latestDate = null;
            if ($latestHistory && $latestHistory->treatment_date) {
                $latestDate = $latestHistory->treatment_date;
            } elseif ($latestAppointment) {
                $latestDate = $latestAppointment->created_at;
            } elseif ($latestHistory) {
                $latestDate = $latestHistory->created_at;
            }
            
            return [
                'patient' => $patient,
                'total_count' => $totalCount,
                'latest_date' => $latestDate,
            ];
        });
        
        // Apply search filter if provided
        if (!empty($search)) {
            $uniquePatients = $uniquePatients->filter(function ($patientData) use ($search) {
                $patient = $patientData['patient'];
                $searchLower = strtolower($search);
                return str_contains(strtolower($patient->name ?? ''), $searchLower) ||
                       str_contains(strtolower($patient->email ?? ''), $searchLower);
            });
        }
        
        $uniquePatients = $uniquePatients->sortByDesc('latest_date')->values();
        
        return view('doctor.history.index', compact('uniquePatients', 'search'));
    }
    
    public function showPatientHistory($patient, Request $request)
    {
        // If $patient is an ID, find the user
        if (!($patient instanceof \App\Models\User)) {
            $patient = \App\Models\User::findOrFail($patient);
        }
        
        // Ensure the user is a patient
        if ($patient->role !== 'patient') {
            abort(404);
        }

        $query = $patient->patientHistory()
            ->with(['doctor', 'appointment.service', 'appointment.branch', 'personalInformation']);

        // Filter by personal_information_id (patient profile) if selected
        if ($request->filled('personal_information_id')) {
            $query->where('personal_information_id', $request->personal_information_id);
        }

        $history = $query
            ->orderBy('treatment_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all personal information profiles for this patient
        $profiles = $patient->personalInformation()->orderBy('is_default', 'desc')->get();

        // Group histories by personal_information_id to show separation between profiles
        $historyByProfile = $history->groupBy('personal_information_id');

        // Load personal and medical information for the selected profile or default
        $selectedProfileId = $request->get('personal_information_id');
        if ($selectedProfileId) {
            $personalInfo = $patient->personalInformation()->find($selectedProfileId);
        } else {
            $personalInfo = $patient->personalInformation()->where('is_default', true)->first();
        }
        
        if (!$personalInfo && $profiles->isNotEmpty()) {
            $personalInfo = $profiles->first();
        }

        $medicalInfo = $patient->medicalInformation()
            ->where('is_default', $personalInfo->is_default ?? false)
            ->first();
        
        if (!$medicalInfo) {
            $medicalInfo = $patient->medicalInformation()->first();
        }

        $emergencyContact = $patient->emergencyContacts()
            ->where('is_default', $personalInfo->is_default ?? false)
            ->first();
        
        if (!$emergencyContact) {
            $emergencyContact = $patient->emergencyContacts()->first();
        }

        // Doctors can edit all history without restrictions
        $canEdit = true;

        // combinedHistory is an alias for history (used in the view)
        $combinedHistory = $history;

        return view('doctor.history.show', compact('patient', 'history', 'combinedHistory', 'personalInfo', 'medicalInfo', 'emergencyContact', 'canEdit', 'profiles', 'historyByProfile', 'selectedProfileId'));
    }

    public function updateHistory(User $patient, Request $request)
    {
        // Ensure the user is a patient
        if ($patient->role !== 'patient') {
            abort(404);
        }

        // Doctors can edit all history - no branch restrictions
        // Get all patient history items with their appointments and branches (doctors can see all)
        $historyItems = $patient->patientHistory()
            ->with(['appointment.branch', 'appointment.service', 'doctor'])
            ->latest()
            ->get();

        // All history items are editable for doctors
        $historyItemsWithAccess = $historyItems->map(function ($history) {
            $history->canEdit = true; // Doctors can edit all
            return $history;
        });

        // If a specific history_id is requested, show the update form
        $historyId = $request->get('history_id');
        $selectedHistory = null;
        $appointments = collect();

        if ($historyId) {
            $selectedHistory = $historyItemsWithAccess->firstWhere('id', $historyId);
            
            if (!$selectedHistory) {
                abort(404, 'History item not found');
            }

            // Get appointments for the form - doctors can see all appointments from all branches
            $appointments = $patient->appointments()
                ->with(['service', 'branch'])
                ->latest()
                ->get();
        }

        // Pass route prefix for doctor
        $routePrefix = 'doctor.history.patient';
        
        return view('admin.patient_history_update', compact(
            'patient', 
            'historyItemsWithAccess', 
            'selectedHistory', 
            'appointments',
            'routePrefix'
        ));
    }

    public function storeHistory(Request $request, User $patient)
    {
        // Ensure the user is a patient
        if ($patient->role !== 'patient') {
            abort(404);
        }

        $isUpdate = $request->has('history_id') && $request->history_id;

        // If updating, verify the history item exists
        if ($isUpdate) {
            $history = PatientHistory::with('appointment')->find($request->history_id);
            
            if (!$history || $history->patient_id !== $patient->id) {
                abort(404, 'History item not found');
            }
            // No branch check for doctors - they can edit all
        }

        $request->validate([
            'appointment_id' => 'nullable|exists:appointments,id',
            'treatment_date' => 'required|date',
            'treatment_notes' => 'required|string|max:5000',
            'diagnosis' => 'nullable|string|max:2000',
            'consultation_result' => 'nullable|string|max:10000',
            'consultation_result_json' => 'nullable|string|max:10000',
            'before_photos.*' => 'nullable|image|max:5120',
            'after_photos.*' => 'nullable|image|max:5120',
            'prescription' => 'nullable|string|max:1000',
            'outcome' => 'nullable|string|max:1000',
            'follow_up_required' => 'boolean',
            'follow_up_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Verify appointment belongs to patient if provided
        if ($request->appointment_id) {
            $appointment = Appointment::find($request->appointment_id);
            if (!$appointment || $appointment->patient_id !== $patient->id) {
                return back()->withErrors(['appointment_id' => 'Invalid appointment selected.'])->withInput();
            }
            // No branch check for doctors - they can select appointments from all branches
        }

        // Get personal_information_id from appointment if provided
        $personalInformationId = null;
        if ($request->appointment_id) {
            $appointment = Appointment::find($request->appointment_id);
            $personalInformationId = $appointment->personal_information_id ?? null;
        } elseif ($isUpdate && $history->personal_information_id) {
            // Keep existing personal_information_id if updating and no new appointment selected
            $personalInformationId = $history->personal_information_id;
        }

        // Handle consultation result JSON
        $consultationResult = null;
        
        // If consultation_result_json is provided, use it
        if ($request->filled('consultation_result_json')) {
            $consultationResult = $request->consultation_result_json;
        } else {
            // Otherwise, build it from form data
            $beforePhotos = [];
            $afterPhotos = [];
            
            // Get existing photos
            if ($request->has('existing_before_photos')) {
                $beforePhotos = array_merge($beforePhotos, $request->existing_before_photos);
            }
            if ($request->has('existing_after_photos')) {
                $afterPhotos = array_merge($afterPhotos, $request->existing_after_photos);
            }
            
            // Handle new before photos uploads
            if ($request->hasFile('before_photos')) {
                foreach ($request->file('before_photos') as $file) {
                    $path = $file->store('appointment-results/before/photos', 'public');
                    $beforePhotos[] = $path;
                }
            }
            
            // Handle new after photos uploads
            if ($request->hasFile('after_photos')) {
                foreach ($request->file('after_photos') as $file) {
                    $path = $file->store('appointment-results/after/photos', 'public');
                    $afterPhotos[] = $path;
                }
            }
            
            // Get medication data
            $medicationInstructions = '';
            if ($request->has('medication_instructions')) {
                $instructions = array_filter($request->medication_instructions, function($item) {
                    return !empty(trim($item));
                });
                $medicationInstructions = implode("\n", array_map(function($item) {
                    return '• ' . trim($item);
                }, $instructions));
            }
            
            $medicationMedicines = $request->medication_medicines ?? '';
            
            // Build consultation result JSON
            $consultationResultData = [
                'before' => [
                    'photos' => $beforePhotos,
                    'videos' => [],
                    'notes' => null
                ],
                'after' => [
                    'photos' => $afterPhotos,
                    'videos' => [],
                    'notes' => null
                ],
                'medication' => [
                    'instructions' => $medicationInstructions,
                    'medicines' => $medicationMedicines
                ]
            ];
            
            $consultationResult = json_encode($consultationResultData);
        }

        $historyData = [
            'patient_id' => $patient->id,
            'personal_information_id' => $personalInformationId,
            'doctor_id' => Auth::id(),
            'appointment_id' => $request->appointment_id,
            'treatment_date' => $request->treatment_date,
            'treatment_notes' => $request->treatment_notes,
            'diagnosis' => $request->diagnosis,
            'consultation_result' => $consultationResult ?? $request->consultation_result,
            'prescription' => $request->prescription,
            'outcome' => $request->outcome,
            'follow_up_required' => $request->boolean('follow_up_required'),
            'follow_up_date' => $request->follow_up_date,
            'notes' => $request->notes,
        ];

        if ($isUpdate) {
            $history->update($historyData);
            $message = 'Patient history updated successfully!';
        } else {
            PatientHistory::create($historyData);
            $message = 'Patient history created successfully!';
        }

        return redirect()->route('doctor.history.patient', $patient->id)
            ->with('success', $message);
    }

    public function deleteHistory(PatientHistory $history)
    {
        // Ensure the history belongs to a patient
        $patient = $history->patient;
        if (!$patient || $patient->role !== 'patient') {
            abort(404);
        }

        // Doctors can delete all patient history - no restrictions
        $history->delete();

        return redirect()->route('doctor.history.patient', $patient->id)
            ->with('success', 'Patient history deleted successfully!');
    }

    // All Appointments (All Branches) methods
    public function allAppointments(Request $request)
    {
        $search = $request->input('search', '');
        
        // Load all appointments from all branches (not filtered by branch_id)
        $query = Appointment::where('status', 'booked')
            ->with(['patient', 'service', 'doctorSlot', 'timeSlot', 'branch']);
        
        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%');
                })
                ->orWhere('first_name', 'like', '%' . $search . '%')
                ->orWhere('last_name', 'like', '%' . $search . '%')
                ->orWhereHas('service', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%');
                })
                ->orWhere('consultation_type', 'like', '%' . $search . '%')
                ->orWhereHas('timeSlot', function ($subQuery) use ($search) {
                    $subQuery->where('date', 'like', '%' . $search . '%')
                        ->orWhere('start_time', 'like', '%' . $search . '%')
                        ->orWhere('end_time', 'like', '%' . $search . '%');
                })
                ->orWhereHas('branch', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%');
                });
            });
        }
        
        $appointments = $query->latest()->get();
        
        return view('doctor.all-appointments', compact('appointments', 'search'));
    }

    public function getPatientInfo(Appointment $appointment)
    {
        $appointment->load(['patient', 'timeSlot', 'branch']);
        
        $patient = $appointment->patient;
        
        // Get the most relevant personal information record
        $personalInfo = null;
        if ($patient) {
            // Prefer records that actually have civil status (newer patient sheets),
            // falling back to default, then latest record.
            $personalInfo = $patient->personalInformation()
                ->whereNotNull('civil_status')
                ->orderByDesc('is_default')
                ->orderByDesc('created_at')
                ->first();

            if (!$personalInfo) {
                $personalInfo = $patient->personalInformation()
                    ->where('is_default', true)
                    ->first();
            }

            if (!$personalInfo) {
                $personalInfo = $patient->personalInformation()->latest()->first();
            }
        }
        
        // Get default medical information
        $medicalInfo = null;
        if ($patient) {
            $medicalInfo = $patient->medicalInformation()->where('is_default', true)->first();
            if (!$medicalInfo) {
                $medicalInfo = $patient->medicalInformation()->latest()->first();
            }
        }
        
        // Get default emergency contact
        $emergencyContact = null;
        if ($patient) {
            $emergencyContact = $patient->emergencyContacts()->where('is_default', true)->first();
            if (!$emergencyContact) {
                $emergencyContact = $patient->emergencyContacts()->latest()->first();
            }
        }
        
        return response()->json([
            'appointment' => [
                'id' => $appointment->id,
                'first_name' => $appointment->first_name,
                'middle_initial' => $appointment->middle_initial,
                'last_name' => $appointment->last_name,
                'date_of_birth' => $appointment->date_of_birth,
                'address' => $appointment->address,
                'medical_background' => $appointment->medical_background,
                'status' => $appointment->status,
            ],
            'patient' => $patient ? [
                'id' => $patient->id,
                'name' => $patient->name,
                'email' => $patient->email,
                'phone' => $patient->phone,
                'contact_phone' => $patient->contact_phone,
                'date_of_birth' => $patient->date_of_birth,
                'gender' => $patient->gender,
                'address' => $patient->address,
            ] : null,
            'personal_information' => $personalInfo ? [
                'first_name' => $personalInfo->first_name,
                'middle_initial' => $personalInfo->middle_initial,
                'last_name' => $personalInfo->last_name,
                'full_name' => $personalInfo->full_name,
                'address' => $personalInfo->address,
                'birthday' => $personalInfo->birthday ? $personalInfo->birthday->format('Y-m-d') : null,
                'contact_number' => $personalInfo->contact_number,
                'civil_status' => $personalInfo->civil_status,
                'preferred_pronoun' => $personalInfo->preferred_pronoun,
                'signature' => $personalInfo->signature,
            ] : null,
            'medical_information' => $medicalInfo ? [
                'hypertension' => $medicalInfo->hypertension,
                'diabetes' => $medicalInfo->diabetes,
                'comorbidities_others' => $medicalInfo->comorbidities_others,
                'allergies' => $medicalInfo->allergies,
                'medications' => $medicalInfo->medications,
                'anesthetics' => $medicalInfo->anesthetics,
                'anesthetics_others' => $medicalInfo->anesthetics_others,
                'previous_hospitalizations_surgeries' => $medicalInfo->previous_hospitalizations_surgeries,
                'smoker' => $medicalInfo->smoker,
                'alcoholic_drinker' => $medicalInfo->alcoholic_drinker,
                'known_family_illnesses' => $medicalInfo->known_family_illnesses,
            ] : null,
            'emergency_contact' => $emergencyContact ? [
                'name' => $emergencyContact->name,
                'relationship' => $emergencyContact->relationship,
                'address' => $emergencyContact->address,
                'contact_number' => $emergencyContact->contact_number,
            ] : null,
        ]);
    }

    public function deleteAllAppointment(Appointment $appointment)
    {
        // Doctors can delete appointments from all branches
        try {
            DB::transaction(function () use ($appointment) {
                $appointment->load(['timeSlot', 'doctorSlot']);

                $appointmentDate = $appointment->timeSlot->date
                    ?? $appointment->doctorSlot->slot_date
                    ?? ($appointment->scheduled_date ? Carbon::parse($appointment->scheduled_date) : null)
                    ?? $appointment->created_at;

                $appointmentTime = $appointment->timeSlot->start_time
                    ?? optional($appointment->doctorSlot)->start_time
                    ?? $appointment->scheduled_time
                    ?? null;

                // Remove any stored consultation files and patient histories linked to this appointment
                $histories = PatientHistory::where('appointment_id', $appointment->id)->get();
                foreach ($histories as $history) {
                    $consultationResult = json_decode($history->consultation_result, true) ?? [];

                    $filePaths = collect($consultationResult['before']['photos'] ?? [])
                        ->merge($consultationResult['before']['videos'] ?? [])
                        ->merge($consultationResult['after']['photos'] ?? [])
                        ->merge($consultationResult['after']['videos'] ?? [])
                        ->filter();

                    $filePaths->each(function ($path) {
                        if ($path) {
                            Storage::disk('public')->delete($path);
                        }
                    });

                    $history->delete();
                }

                // Release booked slots, if any
                if ($appointment->timeSlot) {
                    $appointment->timeSlot->update(['is_booked' => false]);
                }

                if ($appointment->doctorSlot) {
                    $appointment->doctorSlot->update(['is_booked' => false]);
                }

                // Notify patient they missed the appointment
                $dateStr = $appointmentDate ? Carbon::parse($appointmentDate)->format('M d, Y') : 'your scheduled date';
                $timeStr = $appointmentTime ? (Carbon::hasFormat($appointmentTime, 'H:i:s') ? Carbon::createFromFormat('H:i:s', $appointmentTime)->format('h:i A') : $appointmentTime) : 'scheduled time';
                NotificationService::sendNotification(
                    'Missed Appointment',
                    "We noticed you missed your appointment scheduled for {$dateStr} at {$timeStr}. Please book another slot if you still need a consultation.",
                    'appointment_missed',
                    $appointment->patient_id
                );

                $appointment->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Appointment deleted successfully!'
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the appointment.'
            ], 500);
        }
    }

    public function storeAllAppointmentResult(Request $request, Appointment $appointment)
    {
        // Doctors can add results to appointments from all branches
        $request->validate([
            'before_files.*' => 'nullable|mimes:jpeg,jpg,png,gif,mp4,avi,mov,wmv|max:51200',
            'after_files.*' => 'nullable|mimes:jpeg,jpg,png,gif,mp4,avi,mov,wmv|max:51200',
            'before_findings' => 'nullable|string|max:5000',
            'after_results' => 'nullable|string|max:5000',
            'prescription' => 'nullable|string|max:5000',
            'medications_to_take' => 'nullable|string|max:5000',
            'notes' => 'nullable|string|max:5000',
            'follow_up_date' => 'nullable|date|after_or_equal:today',
        ]);

        try {
            $beforePhotos = [];
            $beforeVideos = [];
            $afterPhotos = [];
            $afterVideos = [];

            // Handle before files (photos and videos combined)
            if ($request->hasFile('before_files')) {
                foreach ($request->file('before_files') as $file) {
                    $mimeType = $file->getMimeType();
                    if (str_starts_with($mimeType, 'image/')) {
                        $path = $file->store('appointment-results/before/photos', 'public');
                        $beforePhotos[] = $path;
                    } elseif (str_starts_with($mimeType, 'video/')) {
                        $path = $file->store('appointment-results/before/videos', 'public');
                        $beforeVideos[] = $path;
                    }
                }
            }

            // Handle after files (photos and videos combined)
            if ($request->hasFile('after_files')) {
                foreach ($request->file('after_files') as $file) {
                    $mimeType = $file->getMimeType();
                    if (str_starts_with($mimeType, 'image/')) {
                        $path = $file->store('appointment-results/after/photos', 'public');
                        $afterPhotos[] = $path;
                    } elseif (str_starts_with($mimeType, 'video/')) {
                        $path = $file->store('appointment-results/after/videos', 'public');
                        $afterVideos[] = $path;
                    }
                }
            }

            // Parse bullet point strings into arrays for better storage
            $parseBulletPoints = function($string) {
                if (empty($string)) return [];
                // Split by newline and remove bullet points, then filter empty values
                return array_filter(
                    array_map('trim', 
                        preg_split('/\n/', $string)
                    ),
                    function($item) {
                        $cleaned = preg_replace('/^•\s*/', '', $item);
                        return !empty(trim($cleaned));
                    }
                );
            };

            $beforeFindingsArray = $parseBulletPoints($request->before_findings ?? '');
            $afterResultsArray = $parseBulletPoints($request->after_results ?? '');
            $prescriptionArray = $parseBulletPoints($request->prescription ?? '');
            $medicationsArray = $parseBulletPoints($request->medications_to_take ?? '');

            // Build consultation result JSON structure
            $consultationResult = [
                'before' => [
                    'photos' => $beforePhotos,
                    'videos' => $beforeVideos,
                ],
                'after' => [
                    'photos' => $afterPhotos,
                    'videos' => $afterVideos,
                ],
            ];

            // Add findings if present (store both as string and array for compatibility)
            if (!empty($request->before_findings)) {
                $consultationResult['before_findings'] = $request->before_findings;
                $consultationResult['before']['findings'] = array_values($beforeFindingsArray);
            }

            // Add results if present
            if (!empty($request->after_results)) {
                $consultationResult['after_results'] = $request->after_results;
                $consultationResult['after']['results'] = array_values($afterResultsArray);
            }

            // Add prescription if present
            if (!empty($request->prescription)) {
                $consultationResult['prescription'] = $request->prescription;
                $consultationResult['prescription_array'] = array_values($prescriptionArray);
            }

            // Add medications if present
            if (!empty($request->medications_to_take)) {
                $consultationResult['medications_to_take'] = $request->medications_to_take;
                $consultationResult['medication'] = [
                    'medicines' => array_values($medicationsArray),
                ];
            }

            // Create patient history record with all the data
            $patientHistory = \App\Models\PatientHistory::create([
                'patient_id' => $appointment->patient_id,
                'personal_information_id' => $appointment->personal_information_id,
                'appointment_id' => $appointment->id,
                'doctor_id' => Auth::id(),
                'treatment_notes' => $request->notes ?? 'Result added',
                'treatment_date' => now()->toDateString(),
                'consultation_result' => json_encode($consultationResult),
                'prescription' => $request->prescription ?? null,
                'notes' => $request->notes ?? null,
                'follow_up_required' => !empty($request->follow_up_date),
                'follow_up_date' => $request->follow_up_date ?? null,
            ]);

            // Update appointment status to completed
            $appointment->update([
                'status' => 'completed',
            ]);

            // Send notification to patient
            NotificationService::sendNotification(
                'Appointment Result Available',
                'Your appointment result has been added by the doctor. Please check your history for details.',
                'result',
                $appointment->patient_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Result added successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the result: ' . $e->getMessage()
            ], 500);
        }
    }
}

