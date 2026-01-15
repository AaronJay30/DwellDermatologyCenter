<?php

namespace App\Http\Controllers;

use App\Models\TimeSlot;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeSlotController extends Controller
{
    // Show all slots for the logged-in doctor
    public function index()
    {
        $doctorId = Auth::id();
        $slots = TimeSlot::where('doctor_id', $doctorId)
            ->with('branch')
            ->orderBy('date', 'asc')
            ->get();

        return view('doctor.slots.index', compact('slots'));
    }

    // Show form to create new slot
    public function create()
    {
        $branches = Branch::all();
        return view('doctor.slots.create', compact('branches'));
    }

    // Store new slot
    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'consultation_fee' => 'required|numeric|min:0',
        ]);

        TimeSlot::create([
            'branch_id' => $request->branch_id,
            'doctor_id' => Auth::id(),
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_booked' => false,
            'consultation_fee' => $request->consultation_fee,
        ]);

        return redirect()->route('doctor.slots.index')->with('success', 'Time slot added successfully.');
    }
}
