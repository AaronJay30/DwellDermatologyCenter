<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;

class PatientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:patient');
    }

    public function history(Request $request)
    {
        $query = Auth::user()->patientHistory()->with(['doctor', 'appointment.branch', 'appointment.timeSlot', 'appointment.service', 'personalInformation']);
        
        // Filter by personal_information_id (patient profile) if selected
        // This ensures histories are separated by patient profile
        if ($request->filled('personal_information_id')) {
            $query->where('personal_information_id', $request->personal_information_id);
        }
        
        // Filter by branch if selected
        if ($request->filled('branch_id')) {
            $query->whereHas('appointment', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }
        
        // Filter by type (consult or services)
        if ($request->filled('type')) {
            if ($request->type === 'consult') {
                // Consult: has consultation_type, no service_id
                $query->whereHas('appointment', function ($q) {
                    $q->whereNotNull('consultation_type')
                      ->whereNull('service_id');
                });
            } elseif ($request->type === 'services') {
                // Services: has service_id, no consultation_type
                $query->whereHas('appointment', function ($q) {
                    $q->whereNotNull('service_id')
                      ->whereNull('consultation_type');
                });
            }
        }
        
        $history = $query->latest()->get();
        $branches = Branch::orderBy('name')->get();
        
        // Get all personal information profiles for the user to allow filtering
        $profiles = Auth::user()->personalInformation()->orderBy('is_default', 'desc')->get();
        
        // Group histories by personal_information_id to show separation
        $historyByProfile = $history->groupBy('personal_information_id');
        
        return view('patient.history', compact('history', 'branches', 'profiles', 'historyByProfile'));
    }

    public function historyAjax(Request $request)
    {
        $query = Auth::user()->patientHistory()->with(['doctor', 'appointment.branch', 'appointment.timeSlot', 'appointment.service', 'personalInformation']);

        if ($request->filled('personal_information_id')) {
            $query->where('personal_information_id', $request->personal_information_id);
        }

        if ($request->filled('branch_id')) {
            $query->whereHas('appointment', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        // Filter by type (consult or services)
        if ($request->filled('type')) {
            if ($request->type === 'consult') {
                // Consult: has consultation_type, no service_id
                $query->whereHas('appointment', function ($q) {
                    $q->whereNotNull('consultation_type')
                      ->whereNull('service_id');
                });
            } elseif ($request->type === 'services') {
                // Services: has service_id, no consultation_type
                $query->whereHas('appointment', function ($q) {
                    $q->whereNotNull('service_id')
                      ->whereNull('consultation_type');
                });
            }
        }

        $history = $query->latest()->get();
        $branches = Branch::orderBy('name')->get();
        $profiles = Auth::user()->personalInformation()->orderBy('is_default', 'desc')->get();
        $historyByProfile = $history->groupBy('personal_information_id');

        $stats = [
            'total' => $history->count(),
            'profiles' => $historyByProfile->count(),
            'followups' => $history->where('follow_up_required', true)->count(),
        ];

        $html = view('patient.partials.history-table', compact('history', 'historyByProfile', 'profiles'))->render();

        return response()->json([
            'html' => $html,
            'stats' => $stats,
        ]);
    }
}
