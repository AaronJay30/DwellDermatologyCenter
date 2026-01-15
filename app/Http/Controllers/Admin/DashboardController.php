<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\TimeSlot;
use App\Models\Branch;
use App\Models\User;
use App\Models\PatientHistory;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $today = now()->toDateString();
        $branchId = Auth::user()->branch_id;

        // Upcoming appointments count for quick stat
        $upcomingCount = Appointment::where('branch_id', $branchId)
            ->where(function($q) use ($today) {
                $q->whereHas('doctorSlot', function ($q) use ($today) {
                    $q->where('slot_date', '>=', $today);
                })->orWhereHas('timeSlot', function ($q) use ($today) {
                    $q->whereDate('date', '>=', $today);
                });
            })
            ->count();

        // Today's schedule for left table
        $todaysSchedule = Appointment::where('branch_id', $branchId)
            ->where(function($q) use ($today) {
                $q->whereHas('doctorSlot', function ($q) use ($today) {
                    $q->where('slot_date', $today);
                })->orWhereHas('timeSlot', function ($q) use ($today) {
                    $q->whereDate('date', $today);
                });
            })
            ->with(['patient', 'doctorSlot', 'timeSlot'])
            ->get()
            ->sortBy(function($appointment) {
                if ($appointment->timeSlot) {
                    return $appointment->timeSlot->start_time;
                } elseif ($appointment->doctorSlot) {
                    return $appointment->doctorSlot->start_time;
                }
                return '99:99';
            });

        // Branch cards
        $branches = Branch::withCount(['users' => function($q){
            $q->where('role', 'patient');
        }])->orderBy('name')->get();

        // Available slots (unbooked) for quick list
        $availableSlots = TimeSlot::where('branch_id', $branchId)
            ->where('date', '>=', $today)
            ->where('is_booked', false)
            ->with('branch')
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(10)
            ->get();

        // Upcoming appointments (right column list under calendar)
        $upcomingAppointments = Appointment::where('branch_id', $branchId)
            ->with(['patient', 'doctorSlot', 'timeSlot'])
            ->where(function($q) use ($today){
                $q->whereHas('doctorSlot', function($q) use ($today){
                    $q->where('slot_date', '>=', $today);
                })->orWhereHas('timeSlot', function($q) use ($today){
                    $q->whereDate('date', '>=', $today);
                });
            })
            ->get()
            ->sortBy(function($appointment) {
                if ($appointment->timeSlot) {
                    return $appointment->timeSlot->date . ' ' . $appointment->timeSlot->start_time;
                } elseif ($appointment->doctorSlot) {
                    return $appointment->doctorSlot->slot_date . ' ' . $appointment->doctorSlot->start_time;
                }
                return '9999-99-99 99:99';
            })
            ->take(15);

        return view('admin.dashboard', compact(
            'upcomingCount',
            'todaysSchedule',
            'branches',
            'availableSlots',
            'upcomingAppointments'
        ));
    }

    public function appointments(Request $request)
    {
        $branchId = Auth::user()->branch_id;
        $search = $request->input('search', '');
        
        $query = Appointment::where('branch_id', $branchId)
            ->where('status', 'booked')
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
        
        return view('admin.appointments', compact('appointments', 'search'));
    }

    public function deleteAppointment(Appointment $appointment)
    {
        // Verify the appointment belongs to the authenticated admin's branch
        $adminBranchId = Auth::user()->branch_id;
        if (!$adminBranchId || $appointment->branch_id !== $adminBranchId) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete this appointment.'
            ], 403);
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
                
                \App\Services\NotificationService::sendNotification(
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

    public function storeResult(Request $request, Appointment $appointment)
    {
        // Verify the appointment belongs to the authenticated admin's branch
        $adminBranchId = Auth::user()->branch_id;
        if (!$adminBranchId || $appointment->branch_id !== $adminBranchId) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to add results for this appointment.'
            ], 403);
        }

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

            // Build consultation result JSON structure
            $consultationResult = [
                'before' => [
                    'photos' => $beforePhotos,
                    'videos' => $beforeVideos,
                    'findings' => $request->before_findings ?? null,
                ],
                'after' => [
                    'photos' => $afterPhotos,
                    'videos' => $afterVideos,
                    'results' => $request->after_results ?? null,
                ],
                'prescription' => $request->prescription ?? null,
                'medications_to_take' => $request->medications_to_take ?? null,
                'notes' => $request->notes ?? null,
                'follow_up' => [
                    'required' => $request->filled('follow_up_date'),
                    'date' => $request->follow_up_date ?? null,
                ],
            ];

            // Create treatment notes from the data
            $treatmentNotes = [];
            if ($request->before_findings) {
                $treatmentNotes[] = "Before Consultation Findings:\n" . $request->before_findings;
            }
            if ($request->after_results) {
                $treatmentNotes[] = "After Consultation Results:\n" . $request->after_results;
            }
            if ($request->prescription) {
                $treatmentNotes[] = "Prescription:\n" . $request->prescription;
            }
            if ($request->medications_to_take) {
                $treatmentNotes[] = "Medications to Take:\n" . $request->medications_to_take;
            }
            if ($request->notes) {
                $treatmentNotes[] = "Notes:\n" . $request->notes;
            }
            $treatmentNotesText = !empty($treatmentNotes) ? implode("\n\n", $treatmentNotes) : 'Service result added';

            // Create patient history record with all the data
            $patientHistory = PatientHistory::create([
                'patient_id' => $appointment->patient_id,
                'personal_information_id' => $appointment->personal_information_id,
                'appointment_id' => $appointment->id,
                'doctor_id' => Auth::id(),
                'treatment_notes' => $treatmentNotesText,
                'treatment_date' => now()->toDateString(),
                'consultation_result' => json_encode($consultationResult),
                'prescription' => $request->prescription ?? null,
                'notes' => $request->notes ?? null,
                'follow_up_required' => $request->filled('follow_up_date'),
                'follow_up_date' => $request->follow_up_date ?? null,
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

    public function patientsByBranch(Branch $branch)
    {
        $patients = $branch->users()->where('role', 'patient')->orderBy('name')->get();
        return view('admin.patients_by_branch', compact('branch', 'patients'));
    }

    public function patients(Request $request)
    {
        $search = $request->get('search', '');
        
        $query = User::where('role', 'patient')
            ->whereHas('appointments')
            ->whereHas('patientHistory')
            ->with(['appointments.service', 'patientHistory']);
        
        // Apply search filter
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $patients = $query->orderBy('name')->get();
        
        // Add canEdit flag for each patient
        $patients = $patients->map(function($patient) {
            $patient->canEdit = $this->patientBelongsToAdminBranch($patient);
            return $patient;
        });
        
        return view('admin.patients', compact('patients', 'search'));
    }

    public function profile()
    {
        $admin = Auth::user()->load('branch');
        return view('admin.profile', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $admin->id,
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($admin->profile_photo && Storage::disk('public')->exists($admin->profile_photo)) {
                Storage::disk('public')->delete($admin->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $userData['profile_photo'] = $path;
        }

        $admin->update($userData);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!'
            ]);
        }

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $admin = Auth::user();
        
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Check current password
        if (!\Hash::check($validated['current_password'], $admin->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update password
        $admin->update([
            'password' => \Hash::make($validated['new_password'])
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully!'
            ]);
        }

        return redirect()->route('admin.profile')->with('success', 'Password updated successfully!');
    }

    /**
     * Check if a patient belongs to the admin's branch
     * A patient belongs to a branch if they have at least one appointment with that branch
     */
    protected function patientBelongsToAdminBranch(User $patient): bool
    {
        $adminBranchId = Auth::user()->branch_id;
        
        if (!$adminBranchId) {
            return false;
        }

        // Check if patient has any appointments with admin's branch
        return $patient->appointments()
            ->where('branch_id', $adminBranchId)
            ->exists();
    }

    public function viewHistory(User $patient, Request $request)
    {
        // Ensure the user is a patient
        if ($patient->role !== 'patient') {
            abort(404);
        }

        $query = $patient->patientHistory()
            ->with(['doctor', 'appointment.service', 'appointment.branch', 'personalInformation']);

        // Filter by personal_information_id (patient profile) if selected
        // This ensures histories are separated by patient profile as per requirement
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

        // Check if admin can perform CRUD operations (only if patient belongs to admin's branch)
        $canEdit = $this->patientBelongsToAdminBranch($patient);

        return view('admin.patient_history_view', compact('patient', 'history', 'personalInfo', 'medicalInfo', 'emergencyContact', 'canEdit', 'profiles', 'historyByProfile', 'selectedProfileId'));
    }

    public function updateHistory(User $patient, Request $request)
    {
        // Ensure the user is a patient
        if ($patient->role !== 'patient') {
            abort(404);
        }

        $adminBranchId = Auth::user()->branch_id;
        
        // Get all patient history items with their appointments and branches
        $historyItems = $patient->patientHistory()
            ->with(['appointment.branch', 'appointment.service', 'doctor'])
            ->latest()
            ->get();

        // Check which history items belong to admin's branch
        $historyItemsWithAccess = $historyItems->map(function ($history) use ($adminBranchId) {
            $history->canEdit = false;
            if ($history->appointment && $history->appointment->branch_id == $adminBranchId) {
                $history->canEdit = true;
            }
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

            // Check if admin can edit this specific history item
            if (!$selectedHistory->canEdit) {
                abort(403, 'You can only update patient history for items from your branch. This item belongs to a different branch.');
            }

            // Get appointments for the form
            $appointments = $patient->appointments()
                ->with(['service', 'branch'])
                ->where('branch_id', $adminBranchId) // Only show appointments from admin's branch
                ->latest()
                ->get();
        }

        // Pass route prefix for admin
        $routePrefix = 'admin.patients.history';
        
        return view('admin.patient_history_update', compact(
            'patient', 
            'historyItemsWithAccess', 
            'selectedHistory', 
            'appointments',
            'adminBranchId',
            'routePrefix'
        ));
    }

    public function storeHistory(Request $request, User $patient)
    {
        // Ensure the user is a patient
        if ($patient->role !== 'patient') {
            abort(404);
        }

        $adminBranchId = Auth::user()->branch_id;
        $isUpdate = $request->has('history_id') && $request->history_id;

        // If updating, verify the history item exists and belongs to admin's branch
        if ($isUpdate) {
            $history = PatientHistory::with('appointment')->find($request->history_id);
            
            if (!$history || $history->patient_id !== $patient->id) {
                abort(404, 'History item not found');
            }

            // Check if this history item belongs to admin's branch
            if (!$history->appointment || $history->appointment->branch_id !== $adminBranchId) {
                abort(403, 'You can only update patient history for items from your branch.');
            }
        } else {
            // For new history, check if patient belongs to admin's branch
            if (!$this->patientBelongsToAdminBranch($patient)) {
                abort(403, 'You can only create patient history for patients in your branch. You can view history from other branches, but cannot edit or delete.');
            }
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

            // Also verify appointment belongs to admin's branch
            if ($appointment->branch_id !== $adminBranchId) {
                return back()->withErrors(['appointment_id' => 'You can only select appointments from your branch.'])->withInput();
            }
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

        return redirect()->route('admin.patients.history', $patient->id)
            ->with('success', $message);
    }

    public function deleteHistory(PatientHistory $history)
    {
        // Ensure the history belongs to a patient
        $patient = $history->patient;
        if (!$patient || $patient->role !== 'patient') {
            abort(404);
        }

        // RBAC: Admins can only delete patient history if patient belongs to their branch
        if (!$this->patientBelongsToAdminBranch($patient)) {
            abort(403, 'You can only delete patient history for patients in your branch. You can view history from other branches, but cannot edit or delete.');
        }

        $history->delete();

        return redirect()->route('admin.patients.history', $patient->id)
            ->with('success', 'Patient history deleted successfully!');
    }

    public function myServicesSchedules(Request $request)
    {
        $search = $request->input('search', '');
        $branchId = Auth::user()->branch_id;
        
        // Only show services (appointments with service_id but no time_slot_id)
        // Exclude completed - they appear in history page
        // Filter by admin's branch only
        $query = Appointment::where('branch_id', $branchId)
            ->whereIn('status', ['booked', 'scheduled', 'pending']) // exclude confirmed (separate page)
            ->whereNotNull('service_id') // Has a service
            ->whereNull('time_slot_id') // But no time slot (not a consultation)
            ->with(['patient', 'service', 'branch', 'personalInformation']);
        
        // Apply search filter
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

        // Check for past dates with pending appointments
        $today = \Carbon\Carbon::today();
        $pastPendingAppointments = Appointment::where('status', 'pending')
            ->where('branch_id', $branchId)
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

        $pageTitle = 'MY SERVICES SCHEDULES';
        $searchRoute = 'admin.my-services-schedules';
        return view('admin.my-services-schedules.index', compact('appointments', 'search', 'pageTitle', 'searchRoute', 'pastPendingAppointments'));
    }

    public function myServicesSchedulesConfirmed(Request $request)
    {
        $search = $request->input('search', '');
        $branchId = Auth::user()->branch_id;
        
        $query = Appointment::where('branch_id', $branchId)
            ->where('status', 'confirmed')
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
        
        // Check for past dates with pending appointments
        $today = \Carbon\Carbon::today();
        $pastPendingAppointments = Appointment::where('status', 'pending')
            ->where('branch_id', $branchId)
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
        
        // Check for past dates with confirmed appointments
        $pastConfirmedAppointments = Appointment::where('status', 'confirmed')
            ->where('branch_id', $branchId)
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
        $searchRoute = 'admin.my-services-schedules.confirmed';
        
        return view('admin.my-services-schedules.index', compact('appointments', 'search', 'pageTitle', 'searchRoute', 'pastPendingAppointments', 'pastConfirmedAppointments'));
    }

    public function showServiceSchedule(Appointment $appointment)
    {
        // Verify the appointment belongs to the authenticated admin's branch
        $adminBranchId = Auth::user()->branch_id;
        if (!$adminBranchId || $appointment->branch_id !== $adminBranchId) {
            abort(403, 'You do not have permission to view this appointment.');
        }

        $appointment->load(['patient', 'service', 'branch']);
        return view('admin.my-services-schedules.show', compact('appointment'));
    }

    public function confirmServiceSchedule(Request $request, Appointment $appointment)
    {
        // Verify the appointment belongs to the authenticated admin's branch
        $adminBranchId = Auth::user()->branch_id;
        if (!$adminBranchId || $appointment->branch_id !== $adminBranchId) {
            return redirect()->route('admin.my-services-schedules')->with('error', 'You do not have permission to confirm this appointment.');
        }

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
            // Preserve existing booked date; fallback to original creation date
            $updateData['scheduled_date'] = $appointment->scheduled_date
                ? $appointment->scheduled_date
                : $appointment->created_at->toDateString();
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

        // Send notification to assigned doctor
        if ($request->doctor_id) {
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

        // Redirect back to the page we came from (check referrer or default to main page)
        $referrer = request()->headers->get('referer');
        if ($referrer && str_contains($referrer, '/confirmed')) {
            return redirect()->route('admin.my-services-schedules.confirmed')->with('success', 'Service appointment confirmed successfully!');
        }
        return redirect()->route('admin.my-services-schedules')->with('success', 'Service appointment confirmed successfully!');
    }

    public function cancelServiceSchedule(Request $request, Appointment $appointment)
    {
        // Verify the appointment belongs to the authenticated admin's branch
        $adminBranchId = Auth::user()->branch_id;
        if (!$adminBranchId || $appointment->branch_id !== $adminBranchId) {
            return redirect()->route('admin.my-services-schedules')->with('error', 'You do not have permission to cancel this appointment.');
        }

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

        // Send notification to assigned doctor if any
        if ($appointment->doctor_id) {
            NotificationService::sendNotification(
                'Appointment Cancelled',
                "Appointment for {$appointment->first_name} {$appointment->last_name} has been cancelled. Reason: {$request->cancellation_reason}",
                'appointment_cancelled',
                $appointment->doctor_id
            );
        }

        // Redirect back to the page we came from (check referrer or default to main page)
        $referrer = request()->headers->get('referer');
        if ($referrer && str_contains($referrer, '/confirmed')) {
            return redirect()->route('admin.my-services-schedules.confirmed')->with('success', 'Service appointment cancelled successfully!');
        }
        return redirect()->route('admin.my-services-schedules')->with('success', 'Service appointment cancelled successfully!');
    }

    public function storeServiceResult(Request $request, Appointment $appointment)
    {
        // Verify the appointment belongs to the authenticated admin's branch
        $adminBranchId = Auth::user()->branch_id;
        if (!$adminBranchId || $appointment->branch_id !== $adminBranchId) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to add results for this appointment.'
            ], 403);
        }

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
            $patientHistory = PatientHistory::create([
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

    public function getPatientInfo(Appointment $appointment)
    {
        $appointment->load(['patient', 'timeSlot', 'branch', 'personalInformation']);
        
        $patient = $appointment->patient;
        
        // PRIORITY 1: Use the appointment's specific personal_information_id if it exists
        $personalInfo = null;
        if ($appointment->personal_information_id) {
            $personalInfo = $appointment->personalInformation;
        }
        
        // PRIORITY 2: If no personal_information_id, try to find matching personal info by appointment name
        if (!$personalInfo && $patient && $appointment->first_name && $appointment->last_name) {
            $personalInfo = $patient->personalInformation()
                ->where('first_name', $appointment->first_name)
                ->where('last_name', $appointment->last_name)
                ->first();
        }
        
        // PRIORITY 3: Fall back to default personal information from patient account
        if (!$personalInfo && $patient) {
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
        
        // Get medical information - try to match by is_default flag of personal info, then fall back
        $medicalInfo = null;
        if ($patient) {
            // PRIORITY 1: Try to match by is_default flag of personal info
            if ($personalInfo && $personalInfo->is_default !== null) {
                $medicalInfo = $patient->medicalInformation()
                    ->where('is_default', $personalInfo->is_default)
                    ->first();
            }
            
            // PRIORITY 2: Fall back to default medical information
            if (!$medicalInfo) {
                $medicalInfo = $patient->medicalInformation()->where('is_default', true)->first();
            }
            
            // PRIORITY 3: Fall back to latest medical information
            if (!$medicalInfo) {
                $medicalInfo = $patient->medicalInformation()->latest()->first();
            }
            
            // PRIORITY 4: If still no medical info, try to get any medical information
            if (!$medicalInfo) {
                $medicalInfo = $patient->medicalInformation()->first();
            }
        }
        
        // Get emergency contact - try to match by is_default flag of personal info, then fall back
        $emergencyContact = null;
        if ($patient) {
            if ($personalInfo && $personalInfo->is_default !== null) {
                $emergencyContact = $patient->emergencyContacts()
                    ->where('is_default', $personalInfo->is_default)
                    ->first();
            }
            
            if (!$emergencyContact) {
                $emergencyContact = $patient->emergencyContacts()->where('is_default', true)->first();
            }
            
            if (!$emergencyContact) {
                $emergencyContact = $patient->emergencyContacts()->latest()->first();
            }
        }
        
        // Build response - prioritize appointment-specific data
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
                'scheduled_date' => $appointment->scheduled_date,
                'created_at' => $appointment->created_at?->toIso8601String(),
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

    // Time Slot Management Methods (branch-scoped)
    public function timeSlots(Request $request)
    {
        $adminBranchId = Auth::user()->branch_id;
        
        if (!$adminBranchId) {
            return redirect()->route('admin.dashboard')->with('error', 'You are not assigned to a branch.');
        }

        // Auto-delete past available slots (not booked and no appointments) for this branch
        $today = now()->startOfDay();
        TimeSlot::where('branch_id', $adminBranchId)
            ->where('date', '<', $today)
            ->where('is_booked', false)
            ->whereDoesntHave('appointments')
            ->delete();

        $search = $request->get('search');
        $filterDate = $request->get('date');
        $filterStatus = $request->get('status');

        $baseQuery = TimeSlot::query()
            ->where('branch_id', $adminBranchId) // Only show slots for admin's branch
            ->with(['branch', 'doctor', 'appointments.patient'])
            ->orderByDesc('date')
            ->orderBy('start_time');

        $statsQuery = clone $baseQuery;

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

        // Exclude booked slots from the slots page - they should appear in appointments instead
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

        $slots = $baseQuery->paginate(10)->withQueryString();

        // Stats should only show today's data (always, regardless of filters)
        $todayDate = now()->toDateString();
        $statsQueryToday = TimeSlot::query()
            ->where('branch_id', $adminBranchId)
            ->whereDate('date', $todayDate);

        $availableCount = (clone $statsQueryToday)
            ->where('is_booked', false)
            ->whereDoesntHave('appointments')
            ->count();

        $pendingCount = (clone $statsQueryToday)
            ->whereHas('appointments', function ($query) {
                $query->where('status', 'pending')
                    ->whereNotNull('time_slot_id'); // Only consultations
            })
            ->count();

        $bookedCount = (clone $statsQueryToday)
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
            ->whereHas('timeSlot', function ($query) use ($today, $adminBranchId) {
                $query->where('date', '<', $today)
                    ->where('branch_id', $adminBranchId);
            })
            ->with(['timeSlot.branch', 'patient'])
            ->get();

        return view('admin.slots.index', [
            'slots' => $slots,
            'search' => $search,
            'filterDate' => $filterDate,
            'filterStatus' => $filterStatus,
            'availableCount' => $availableCount,
            'pendingCount' => $pendingCount,
            'bookedCount' => $bookedCount,
            'pastPendingAppointments' => $pastPendingAppointments,
        ]);
    }

    public function createTimeSlot()
    {
        $adminBranchId = Auth::user()->branch_id;
        
        if (!$adminBranchId) {
            return redirect()->route('admin.dashboard')->with('error', 'You are not assigned to a branch.');
        }

        // Only show the admin's branch
        $branch = Branch::findOrFail($adminBranchId);
        return view('admin.slots.create', compact('branch'));
    }

    public function storeTimeSlot(Request $request)
    {
        $adminBranchId = Auth::user()->branch_id;
        
        if (!$adminBranchId) {
            return redirect()->route('admin.dashboard')->with('error', 'You are not assigned to a branch.');
        }

        // Force branch_id to admin's branch
        $request->merge(['branch_id' => $adminBranchId]);

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
            \Log::info('Admin Slots data received', [
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
                        'branch_id' => $adminBranchId, // Force admin's branch
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

                    // Check for overlapping slots (only for this branch)
                    $overlapping = TimeSlot::where('branch_id', $adminBranchId)
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
                        $validated['doctor_id'] = null; // Admin-created slots don't have a doctor_id
                        
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

                return redirect()->route('admin.slots')->with('success', $message);
            }
        }

        // Single slot mode (backward compatibility)
        // Ensure end_date equals start_date if not provided or if they're the same (single-day range)
        if ($request->filled('start_date') && (!$request->filled('end_date') || $request->start_date === $request->end_date)) {
            $request->merge(['end_date' => $request->start_date]);
        }
        
        $validated = $request->validate([
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
            
            // Check for overlapping slots (only for this branch)
            $overlapping = TimeSlot::where('branch_id', $adminBranchId)
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
                    'branch_id' => $adminBranchId, // Force admin's branch
                    'doctor_id' => null, // Admin-created slots don't have a doctor_id
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
        
        return redirect()->route('admin.slots')->with('success', $message);
    }

    public function editTimeSlot(TimeSlot $slot)
    {
        $adminBranchId = Auth::user()->branch_id;
        
        if (!$adminBranchId) {
            return redirect()->route('admin.dashboard')->with('error', 'You are not assigned to a branch.');
        }

        // Ensure admin can only edit slots for their branch
        if ($slot->branch_id !== $adminBranchId) {
            return redirect()->route('admin.slots')->with('error', 'You can only edit slots for your branch.');
        }

        // Only show the admin's branch
        $branch = Branch::findOrFail($adminBranchId);
        return view('admin.slots.edit', compact('slot', 'branch'));
    }

    public function updateTimeSlot(Request $request, TimeSlot $slot)
    {
        $adminBranchId = Auth::user()->branch_id;
        
        if (!$adminBranchId) {
            return redirect()->route('admin.dashboard')->with('error', 'You are not assigned to a branch.');
        }

        // Ensure admin can only update slots for their branch
        if ($slot->branch_id !== $adminBranchId) {
            return redirect()->route('admin.slots')->with('error', 'You can only update slots for your branch.');
        }

        // Force branch_id to admin's branch
        $request->merge(['branch_id' => $adminBranchId]);

        // Normalize time format - ensure it's in H:i format
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');
        
        // If time includes seconds, remove them
        if ($startTime && strlen($startTime) > 5) {
            $startTime = substr($startTime, 0, 5);
            $request->merge(['start_time' => $startTime]);
        }
        
        if ($endTime && strlen($endTime) > 5) {
            $endTime = substr($endTime, 0, 5);
            $request->merge(['end_time' => $endTime]);
        }

        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'consultation_fee' => 'required|numeric|min:0',
        ]);

        $overlapping = TimeSlot::where('branch_id', $adminBranchId)
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
        $adminBranchId = Auth::user()->branch_id;
        
        if (!$adminBranchId) {
            return response()->json([
                'success' => false,
                'message' => 'You are not assigned to a branch.'
            ], 403);
        }

        // Ensure admin can only delete slots for their branch
        if ($slot->branch_id !== $adminBranchId) {
            return response()->json([
                'success' => false,
                'message' => 'You can only delete slots for your branch.'
            ], 403);
        }

        $slot->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Time slot deleted successfully!'
        ]);
    }

    public function acceptAppointment(Request $request, Appointment $appointment)
    {
        $adminBranchId = Auth::user()->branch_id;
        
        if (!$adminBranchId) {
            return response()->json([
                'success' => false,
                'message' => 'You are not assigned to a branch.'
            ], 403);
        }

        // Ensure appointment belongs to admin's branch
        if ($appointment->branch_id !== $adminBranchId) {
            return response()->json([
                'success' => false,
                'message' => 'You can only accept appointments for your branch.'
            ], 403);
        }

        // Validate request
        $request->validate([
            'doctor_name' => 'required|string|max:255',
        ]);

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
                'doctor_id' => null, // Admin accepts, no specific doctor assigned yet
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
        $adminBranchId = Auth::user()->branch_id;
        
        if (!$adminBranchId) {
            return response()->json([
                'success' => false,
                'message' => 'You are not assigned to a branch.'
            ], 403);
        }

        // Ensure appointment belongs to admin's branch
        if ($appointment->branch_id !== $adminBranchId) {
            return response()->json([
                'success' => false,
                'message' => 'You can only reject appointments for your branch.'
            ], 403);
        }

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
}

