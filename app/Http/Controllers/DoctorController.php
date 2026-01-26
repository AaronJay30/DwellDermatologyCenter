<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\TimeSlot;
use App\Models\Branch;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function appointments()
    {
        $appointments = Appointment::where('doctor_id', Auth::id())
            ->with(['patient', 'service', 'doctorSlot'])
            ->latest()
            ->get();
        
        return view('doctor.appointments', compact('appointments'));
    }

    public function dashboard()
    {
        $today = now()->toDateString();

        // Upcoming appointments count for quick stat
        $upcomingCount = Appointment::where('doctor_id', Auth::id())
            ->whereHas('doctorSlot', function ($q) use ($today) {
                $q->where('slot_date', '>=', $today);
            })
            ->count();

        // Today's schedule for left table
        $todaysSchedule = Appointment::where('doctor_id', Auth::id())
            ->whereHas('doctorSlot', function ($q) use ($today) {
                $q->where('slot_date', $today);
            })
            ->with(['patient', 'doctorSlot'])
            ->orderBy(function($q){
                $q->select('start_time')
                    ->from('doctor_slots')
                    ->whereColumn('doctor_slots.id', 'appointments.doctor_slot_id')
                    ->limit(1);
            })
            ->get();

        // Branch cards
        $branches = Branch::withCount(['users' => function($q){
            $q->where('role', 'patient');
        }])->orderBy('name')->get();

        // Available slots (unbooked) for quick list
        $availableSlots = TimeSlot::where('doctor_id', Auth::id())
            ->where('date', '>=', $today)
            ->where('is_booked', false)
            ->with('branch')
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(10)
            ->get();

        // Upcoming appointments (right column list under calendar)
        $upcomingAppointments = Appointment::where('doctor_id', Auth::id())
            ->with(['patient', 'doctorSlot'])
            ->whereHas('doctorSlot', function($q) use ($today){
                $q->where('slot_date', '>=', $today);
            })
            ->orderBy(function($q){
                $q->select('slot_date')
                    ->from('doctor_slots')
                    ->whereColumn('doctor_slots.id', 'appointments.doctor_slot_id')
                    ->limit(1);
            })
            ->orderBy(function($q){
                $q->select('start_time')
                    ->from('doctor_slots')
                    ->whereColumn('doctor_slots.id', 'appointments.doctor_slot_id')
                    ->limit(1);
            })
            ->limit(15)
            ->get();

        return view('doctor.dashboard', compact(
            'upcomingCount',
            'todaysSchedule',
            'branches',
            'availableSlots',
            'upcomingAppointments'
        ));
    }

    public function patientsByBranch(Branch $branch)
    {
        $patients = $branch->users()->where('role', 'patient')->orderBy('name')->get();
        return view('doctor.patients_by_branch', compact('branch', 'patients'));
    }

    public function slots()
    {
        $slots = TimeSlot::where('doctor_id', Auth::id())
            ->with('branch')
            ->orderByDesc('date')
            ->orderBy('start_time')
            ->get();

        $branches = Branch::orderBy('name')->get();
        return view('doctor.slots', compact('slots', 'branches'));
    }

    public function storeSlot(Request $request)
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

        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:h:i A',
            'end_time' => 'required|date_format:h:i A|after:start_time',
        ]);

        TimeSlot::create([
            'branch_id' => $validated['branch_id'],
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'doctor_id' => Auth::id(),
            'is_booked' => false,
        ]);

        return redirect()->route('doctor.slots')->with('success', 'Time slot added.');
    }

    public function patients()
    {
        $patients = User::where('role', 'patient')
            ->with(['appointments', 'patientHistory'])
            ->get();
        
        return view('doctor.patients', compact('patients'));
    }

    public function profile()
    {
        $doctor = Auth::user();
        return view('doctor.profile', compact('doctor'));
    }
}
