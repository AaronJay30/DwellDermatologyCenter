<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\PersonalInformation;
use App\Models\TimeSlot;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PatientConsultationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:patient');
    }

    /**
     * Find or create a PersonalInformation record for the current user based on the provided name.
     * This ensures each name is treated as a separate patient profile.
     */
    private function findOrCreatePersonalInformation($request)
    {
        // If personal_information_id is provided and belongs to the user, use it
        if ($request->has('personal_information_id') && $request->personal_information_id) {
            $personalInfo = PersonalInformation::where('id', $request->personal_information_id)
                ->where('user_id', Auth::id())
                ->first();
            
            if ($personalInfo) {
                return $personalInfo;
            }
        }

        // Try to find existing PersonalInformation with matching name
        $personalInfo = PersonalInformation::where('user_id', Auth::id())
            ->where('first_name', $request->first_name)
            ->where('last_name', $request->last_name)
            ->where(function($query) use ($request) {
                if ($request->middle_initial) {
                    $query->where('middle_initial', $request->middle_initial);
                } else {
                    $query->whereNull('middle_initial');
                }
            })
            ->first();

        // If found, return it
        if ($personalInfo) {
            return $personalInfo;
        }

        // Create a new PersonalInformation record for this name/profile
        return PersonalInformation::create([
            'user_id' => Auth::id(),
            'first_name' => $request->first_name,
            'middle_initial' => $request->middle_initial,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'birthday' => $request->date_of_birth,
            'contact_number' => $request->contact_number,
            'label' => 'HOME',
            'is_default' => false, // Don't set as default automatically
        ]);
    }

    public function index()
    {
        // Get both consultations (with time_slot_id) and service bookings (with service_id but no time_slot_id)
        $consultations = Appointment::where('patient_id', Auth::id())
            ->where(function($query) {
                $query->whereNotNull('time_slot_id')
                      ->orWhereNotNull('service_id');
            })
            ->with(['branch', 'timeSlot', 'doctor', 'service'])
            ->latest()
            ->paginate(5)
            ->withQueryString();
        
        return view('consultations.index', compact('consultations'));
    }

    public function create(Request $request)
    {
        $ids = $request->get('cart_items', []);
        
        if (empty($ids)) {
            return redirect()->route('cart.index')->with('error', 'Please select items from your cart first.');
        }
        
        $cartItems = null;
        $isDirectBooking = false;
        
        // First, try loading as cart items (existing cart checkout flow)
        $cartItems = \App\Models\Cart::whereIn('id', $ids)
            ->where('user_id', Auth::id())
            ->with([
                'service.category.branch',
                'service.images',
                'service.promoServices.promotion',
            ])
            ->get();
        
        // If cart items found, use them (cart checkout flow)
        if ($cartItems->isNotEmpty() && $cartItems->count() === count($ids)) {
            $isDirectBooking = false;
        } else {
            // Not cart items, try loading as services (direct booking flow)
            $services = \App\Models\Service::whereIn('id', $ids)
                ->with([
                    'category.branch',
                    'images',
                    'promoServices.promotion',
                ])
                ->get();
            
            if ($services->isNotEmpty() && $services->count() === count($ids)) {
                // All IDs are valid service IDs - this is a direct booking
                $isDirectBooking = true;
                
                // Create temporary cart-like structure for the view
                $cartItems = collect();
                foreach ($services as $service) {
                    // Create a temporary object that mimics a cart item
                    $tempCartItem = (object) [
                        'id' => 'temp_' . $service->id, // Temporary ID
                        'service_id' => $service->id,
                        'quantity' => 1,
                        'service' => $service,
                    ];
                    $cartItems->push($tempCartItem);
                }
            } else {
                // Neither cart items nor services found
                return redirect()->route('cart.index')->with('error', 'Selected items not found.');
            }
        }
        
        // Get branch from the first service (assuming all services are from the same branch)
        $branch = $cartItems->first()->service->category->branch;
        
        if (!$branch) {
            return redirect()->route('services.index')->with('error', 'Unable to determine branch for selected services.');
        }
        
        // Get the default personal information profile
        $defaultProfile = Auth::user()->personalInformation()
            ->where('is_default', true)
            ->first();
        
        // If no default, get the first one
        if (!$defaultProfile) {
            $defaultProfile = Auth::user()->personalInformation()->first();
        }
        
        // Calculate total price
        $totalPrice = $cartItems->sum(function ($item) {
            return $item->service->pricing['display_price'] * $item->quantity;
        });
        
        return view('consultations.create', compact('cartItems', 'branch', 'defaultProfile', 'totalPrice', 'isDirectBooking'));
    }

    public function medicalConsultation()
    {
        // Get the default personal information profile
        $defaultProfile = Auth::user()->personalInformation()
            ->where('is_default', true)
            ->first();
        
        // If no default, get the first one
        if (!$defaultProfile) {
            $defaultProfile = Auth::user()->personalInformation()->first();
        }

        $branches = Branch::all();
        
        return view('consultations.medical-consultation', compact('defaultProfile', 'branches'));
    }

    public function store(Request $request)
    {
        // Check if this is a direct service booking (service_ids) or from cart (cart_items)
        $isDirectBooking = $request->has('service_ids') && !empty($request->service_ids);
        
        if ($isDirectBooking || ($request->has('cart_items') && !empty($request->cart_items))) {
            // Service booking (either direct or from cart)
            $validationRules = [
                'branch_id' => 'required|exists:branches,id',
                'date' => 'required|date|after_or_equal:today',
                'description' => 'nullable|string|max:1000',
                'medical_background' => 'nullable|string|max:1000',
                'referral_source' => 'nullable|string|max:255',
                // Personal information fields
                'personal_information_id' => 'nullable|exists:personal_information,id',
                'first_name' => 'required|string|max:255',
                'middle_initial' => 'nullable|string|max:1',
                'last_name' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'date_of_birth' => 'required|date|before:today',
                'contact_number' => 'required|string|max:20',
            ];
            
            if ($isDirectBooking) {
                $validationRules['service_ids'] = 'required|array';
                $validationRules['service_ids.*'] = 'exists:services,id';
            } else {
                $validationRules['cart_items'] = 'required|array';
                $validationRules['cart_items.*'] = 'exists:carts,id';
            }
            
            $request->validate($validationRules);

            // Load services or cart items
            if ($isDirectBooking) {
                // Direct booking - load services directly
                $services = \App\Models\Service::whereIn('id', $request->service_ids)
                    ->with(['promoServices.promotion'])
                    ->get();
                
                if ($services->isEmpty()) {
                    return back()->with('error', 'Selected services not found.')->withInput();
                }
                
                // Create a structure similar to cart items for processing
                $cartItems = collect();
                foreach ($services as $service) {
                    $cartItems->push((object) [
                        'service_id' => $service->id,
                        'quantity' => 1,
                        'service' => $service,
                    ]);
                }
            } else {
                // Cart booking - load cart items
                $cartItems = \App\Models\Cart::whereIn('id', $request->cart_items)
                    ->where('user_id', Auth::id())
                    ->with(['service.promoServices.promotion'])
                    ->get();

                if ($cartItems->isEmpty()) {
                    return back()->with('error', 'Selected cart items not found.')->withInput();
                }
            }

            // Calculate age from date of birth
            $birthday = new \DateTime($request->date_of_birth);
            $today = new \DateTime();
            $age = $today->diff($birthday)->y;

            // Find or create PersonalInformation for this patient profile
            $personalInfo = $this->findOrCreatePersonalInformation($request);

            DB::beginTransaction();
            try {
                $appointments = [];
                
                // Create an appointment for each cart item
                foreach ($cartItems as $cartItem) {
                    for ($i = 0; $i < $cartItem->quantity; $i++) {
                        $notes = $request->description;
                        if ($request->date) {
                            $notes = ($notes ? $notes . "\n\n" : '') . "Preferred Date: " . $request->date;
                        }
                        
                        $appointment = Appointment::create([
                            'patient_id' => Auth::id(),
                            'personal_information_id' => $personalInfo->id,
                            'doctor_id' => null,
                            'service_id' => $cartItem->service_id,
                            'doctor_slot_id' => null,
                            'status' => 'pending',
                            'notes' => $notes,
                            'consultation_type' => null,
                            'description' => $request->description,
                            'medical_background' => $request->medical_background,
                            'referral_source' => $request->referral_source,
                            'branch_id' => $request->branch_id,
                            'time_slot_id' => null,
                            'scheduled_date' => $request->date,
                            'first_name' => $request->first_name,
                            'middle_initial' => $request->middle_initial,
                            'last_name' => $request->last_name,
                            'age' => $age,
                        ]);
                        $appointments[] = $appointment;
                    }
                }

                // Delete cart items after successful booking (only if from cart, not direct booking)
                if (!$isDirectBooking) {
                    foreach ($cartItems as $cartItem) {
                        if (method_exists($cartItem, 'delete')) {
                            $cartItem->delete();
                        }
                    }
                }

                // Send notification to doctors
                $serviceNames = $cartItems->pluck('service.name')->unique()->implode(', ');
                $branchName = \App\Models\Branch::find($request->branch_id)->name ?? 'the clinic';
                
                // First, try to notify doctors in the branch
                $branchDoctors = \App\Models\User::where('branch_id', $request->branch_id)
                    ->where('role', 'doctor')
                    ->get();
                
                if ($branchDoctors->count() > 0) {
                    // Notify all doctors in the branch
                    foreach ($branchDoctors as $doctor) {
                        NotificationService::sendNotification(
                            'New Service Booking',
                            "New service booking(s) for {$serviceNames} on {$request->date} at {$branchName} by {$request->first_name} {$request->last_name}.",
                            'service_booking',
                            $doctor->id
                        );
                    }
                } else {
                    // If no doctors in branch, notify all doctors (fallback)
                    $allDoctors = \App\Models\User::where('role', 'doctor')->get();
                    foreach ($allDoctors as $doctor) {
                        NotificationService::sendNotification(
                            'New Service Booking',
                            "New service booking(s) for {$serviceNames} on {$request->date} at {$branchName} by {$request->first_name} {$request->last_name}.",
                            'service_booking',
                            $doctor->id
                        );
                    }
                }

                // Send confirmation notification to patient
                $serviceNames = $cartItems->pluck('service.name')->unique()->implode(', ');
                NotificationService::sendNotification(
                    'Service Booking Submitted',
                    "Your service booking(s) for {$serviceNames} has been submitted for {$request->date}. You will be notified once it's confirmed.",
                    'service_booking',
                    Auth::id()
                );

                DB::commit();

                return redirect()->route('consultations.index')->with('success', 'Service booking(s) submitted successfully! You will be notified once it\'s confirmed.');
            } catch (\Exception $e) {
                DB::rollback();
                return back()->with('error', 'An error occurred while submitting the service booking. Please try again.')->withInput();
            }
        } else {
            // Original consultation booking logic
            $request->validate([
                'branch_id' => 'required|exists:branches,id',
                'time_slot_id' => 'required|exists:time_slots,id',
                'consultation_type' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'medical_background' => 'nullable|string|max:1000',
                'referral_source' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000',
                // Personal information fields
                'personal_information_id' => 'nullable|exists:personal_information,id',
                'first_name' => 'required|string|max:255',
                'middle_initial' => 'nullable|string|max:1',
                'last_name' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'date_of_birth' => 'required|date|before:today',
                'contact_number' => 'required|string|max:20',
            ]);

            // Find the time slot and verify it's available
            $timeSlot = TimeSlot::where('id', $request->time_slot_id)
                ->forBranch($request->branch_id)
                ->available()
                ->first();

            if (!$timeSlot) {
                return back()->with('error', 'Selected time slot is no longer available.')->withInput();
            }

            // Calculate age from date of birth
            $birthday = new \DateTime($request->date_of_birth);
            $today = new \DateTime();
            $age = $today->diff($birthday)->y;

            // Find or create PersonalInformation for this patient profile
            $personalInfo = $this->findOrCreatePersonalInformation($request);

            DB::beginTransaction();
            try {
                // Create appointment
                $appointment = Appointment::create([
                    'patient_id' => Auth::id(),
                    'personal_information_id' => $personalInfo->id,
                    'doctor_id' => null,
                    'service_id' => null,
                    'doctor_slot_id' => null,
                    'status' => 'pending',
                    'notes' => $request->notes,
                    'consultation_type' => $request->consultation_type,
                    'description' => $request->description,
                    'medical_background' => $request->medical_background,
                    'referral_source' => $request->referral_source,
                    'consultation_fee' => $timeSlot->consultation_fee ?? 700,
                    'branch_id' => $request->branch_id,
                    'time_slot_id' => $request->time_slot_id,
                    'first_name' => $request->first_name,
                    'middle_initial' => $request->middle_initial,
                    'last_name' => $request->last_name,
                    'age' => $age,
                ]);

                // Send notification to ALL doctors in the branch
                $branchDoctors = \App\Models\User::where('branch_id', $request->branch_id)
                    ->where('role', 'doctor')
                    ->get();
                
                if ($branchDoctors->count() > 0) {
                    // Notify all doctors in the branch
                    foreach ($branchDoctors as $doctor) {
                        NotificationService::sendNotification(
                            'New Consultation Request',
                            "You have a new consultation request from {$appointment->first_name} {$appointment->last_name} for {$request->consultation_type} on {$timeSlot->date->format('M d, Y')} at {$timeSlot->start_time}.",
                            'consultation_request',
                            $doctor->id
                        );
                    }
                } else {
                    // If no doctors in the branch, notify all doctors
                    $allDoctors = \App\Models\User::where('role', 'doctor')->get();
                    foreach ($allDoctors as $doctor) {
                        NotificationService::sendNotification(
                            'New Consultation Request',
                            "You have a new consultation request from {$appointment->first_name} {$appointment->last_name} for {$request->consultation_type} on {$timeSlot->date->format('M d, Y')} at {$timeSlot->start_time}.",
                            'consultation_request',
                            $doctor->id
                        );
                    }
                }

                // Send confirmation notification to patient
                NotificationService::sendNotification(
                    'Consultation Request Submitted',
                    "Your consultation request has been submitted for {$timeSlot->date->format('M d, Y')} at {$timeSlot->start_time}. You will be notified once it's confirmed.",
                    'consultation_request',
                    Auth::id()
                );

                DB::commit();

                return redirect()->route('consultations.index')->with('success', 'Consultation request submitted successfully! You will be notified once it\'s confirmed.');
            } catch (\Exception $e) {
                DB::rollback();
                return back()->with('error', 'An error occurred while submitting the consultation request. Please try again.')->withInput();
            }
        }
    }

    public function getAvailableTimeSlots(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $timeSlots = TimeSlot::forBranch($request->branch_id)
            ->forDate($request->date)
            ->available()
            ->orderBy('start_time')
            ->get();

        return response()->json($timeSlots);
    }

    public function show(Appointment $consultation)
    {
        // Ensure the consultation belongs to the authenticated patient
        if ($consultation->patient_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $consultation->load(['branch', 'timeSlot', 'doctor', 'service']);

        // Determine if it's a service booking
        $isServiceBooking = $consultation->service_id && !$consultation->time_slot_id;
        
        // Get date and time
        if ($consultation->timeSlot) {
            $date = $consultation->timeSlot->date->format('M d, Y');
            $time = "{$consultation->timeSlot->start_time} - {$consultation->timeSlot->end_time}";
        } elseif ($consultation->scheduled_date) {
            $date = \Carbon\Carbon::parse($consultation->scheduled_date)->format('M d, Y');
            $time = $consultation->scheduled_time ?? 'To be scheduled';
        } else {
            $date = 'To be scheduled';
            $time = 'Pending';
            if ($consultation->notes) {
                if (preg_match('/Preferred Date:\s*([^\n]+)/i', $consultation->notes, $matches)) {
                    try {
                        $date = \Carbon\Carbon::parse(trim($matches[1]))->format('M d, Y');
                    } catch (\Exception $e) {
                        // Keep default
                    }
                }
            }
        }

        // Return JSON for AJAX requests (as expected by the view)
        return response()->json([
            'consultation' => [
                'id' => $consultation->id,
                'patient_name' => trim("{$consultation->first_name} {$consultation->middle_initial} {$consultation->last_name}"),
                'age' => $consultation->age ?? 'N/A',
                'consultation_type' => $isServiceBooking ? ($consultation->service->name ?? 'Service Booking') : ($consultation->consultation_type ?? 'N/A'),
                'description' => $consultation->description ?? 'N/A',
                'medical_background' => $consultation->medical_background ?? 'N/A',
                'branch_name' => $consultation->branch->name ?? 'N/A',
                'date' => $date,
                'time' => $time,
                'doctor_name' => $consultation->doctor->name ?? 'N/A',
                'status' => $consultation->status ?? 'pending',
                'cancellation_reason' => $consultation->cancellation_reason ?? null,
                'notes' => $consultation->notes ?? null,
                'is_service_booking' => $isServiceBooking,
                'service_name' => $consultation->service->name ?? null,
            ]
        ]);
    }

    public function cancel(Request $request, Appointment $consultation)
    {
        // Ensure the consultation belongs to the authenticated patient
        if ($consultation->patient_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized access.'
            ], 403);
        }

        // Only allow cancellation of pending or confirmed consultations
        if (!in_array($consultation->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'error' => 'This consultation cannot be cancelled.'
            ], 400);
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Store original status before updating
            $originalStatus = $consultation->status;
            
            // Update appointment status
            $consultation->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason,
            ]);

            // Free up the time slot if it was confirmed
            if ($originalStatus === 'confirmed' && $consultation->timeSlot) {
                $consultation->timeSlot->update(['is_booked' => false]);
            }

            // Send cancellation notification to patient
            NotificationService::sendConsultationCancellation($consultation, $request->cancellation_reason);

            // Send notification to assigned doctor if any
            $doctorId = null;
            
            // First, try to get doctor_id from appointment
            if ($consultation->doctor_id) {
                $doctorId = $consultation->doctor_id;
            } 
            // If no doctor_id, try to get from timeSlot
            elseif ($consultation->timeSlot && $consultation->timeSlot->doctor_id) {
                $doctorId = $consultation->timeSlot->doctor_id;
            }
            
            if ($doctorId) {
                // Send notification to specific doctor
                NotificationService::sendNotification(
                    'Appointment Cancelled',
                    "Patient {$consultation->first_name} {$consultation->last_name} has cancelled their appointment scheduled for " . ($consultation->timeSlot ? $consultation->timeSlot->date->format('M d, Y') . ' at ' . $consultation->timeSlot->start_time : 'the scheduled date') . ". Reason: {$request->cancellation_reason}",
                    'appointment_cancelled',
                    $doctorId
                );
            } elseif ($consultation->branch_id) {
                // Notify all doctors in the branch if no specific doctor assigned
                $doctors = \App\Models\User::where('branch_id', $consultation->branch_id)
                    ->where('role', 'doctor')
                    ->get();
                
                if ($doctors->count() > 0) {
                    foreach ($doctors as $doctor) {
                        NotificationService::sendNotification(
                            'Appointment Cancelled',
                            "Patient {$consultation->first_name} {$consultation->last_name} has cancelled their appointment. Reason: {$request->cancellation_reason}",
                            'appointment_cancelled',
                            $doctor->id
                        );
                    }
                } else {
                    // If no doctors in branch, notify all doctors (fallback)
                    $allDoctors = \App\Models\User::where('role', 'doctor')->get();
                    foreach ($allDoctors as $doctor) {
                        NotificationService::sendNotification(
                            'Appointment Cancelled',
                            "Patient {$consultation->first_name} {$consultation->last_name} has cancelled their appointment at branch " . ($consultation->branch ? $consultation->branch->name : 'N/A') . ". Reason: {$request->cancellation_reason}",
                            'appointment_cancelled',
                            $doctor->id
                        );
                    }
                }
            } else {
                // Last resort: notify all doctors if no branch or doctor info
                $allDoctors = \App\Models\User::where('role', 'doctor')->get();
                foreach ($allDoctors as $doctor) {
                    NotificationService::sendNotification(
                        'Appointment Cancelled',
                        "Patient {$consultation->first_name} {$consultation->last_name} has cancelled their appointment. Reason: {$request->cancellation_reason}",
                        'appointment_cancelled',
                        $doctor->id
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Consultation cancelled successfully!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error cancelling consultation: ' . $e->getMessage(), [
                'exception' => $e,
                'consultation_id' => $consultation->id,
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while cancelling the consultation.'
            ], 500);
        }
    }
}

