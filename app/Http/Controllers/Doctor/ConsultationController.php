<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\TimeSlot;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConsultationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:doctor');
    }

    public function index()
    {
        $consultations = Appointment::where('status', 'pending')
            ->whereNotNull('time_slot_id')
            ->with(['patient', 'branch', 'timeSlot'])
            ->latest()
            ->get();
        
        return view('doctor.consultations.index', compact('consultations'));
    }

    public function show(Appointment $consultation)
    {
        $consultation->load(['patient', 'branch', 'timeSlot']);
        return view('doctor.consultations.show', compact('consultation'));
    }

    public function confirm(Request $request, Appointment $consultation)
    {
        $request->validate([
            'confirmation_notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Update appointment status
            $consultation->update([
                'status' => 'confirmed',
                'notes' => $request->confirmation_notes,
            ]);

            // Mark time slot as booked
            $consultation->timeSlot->update(['is_booked' => true]);

            // Send confirmation notification to patient
            NotificationService::sendConsultationConfirmation($consultation, $request->confirmation_notes);

            DB::commit();

            return redirect()->route('doctor.consultations.index')->with('success', 'Consultation confirmed successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'An error occurred while confirming the consultation.');
        }
    }

    public function cancel(Request $request, Appointment $consultation)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Update appointment status
            $consultation->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason,
            ]);

            // Send cancellation notification to patient
            NotificationService::sendConsultationCancellation($consultation, $request->cancellation_reason);

            DB::commit();

            return redirect()->route('doctor.consultations.index')->with('success', 'Consultation cancelled successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'An error occurred while cancelling the consultation.');
        }
    }
}

