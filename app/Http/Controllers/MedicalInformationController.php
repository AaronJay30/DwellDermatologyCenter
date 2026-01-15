<?php

namespace App\Http\Controllers;

use App\Models\MedicalInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MedicalInformationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:patient');
    }

    public function select()
    {
        $medicalInfos = Auth::user()->medicalInformation()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();
        return view('medical-information.select', compact('medicalInfos'));
    }

    public function create()
    {
        return view('medical-information.create');
    }

    public function edit(MedicalInformation $medicalInformation)
    {
        // Ensure the medical info belongs to the authenticated user
        if ($medicalInformation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }
        
        return view('medical-information.edit', compact('medicalInformation'));
    }

    public function show(MedicalInformation $medicalInformation)
    {
        // Ensure the medical info belongs to the authenticated user
        if ($medicalInformation->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        return response()->json(['success' => true, 'medical_info' => $medicalInformation]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'hypertension' => 'nullable|boolean',
            'diabetes' => 'nullable|boolean',
            'comorbidities_others' => 'nullable|string|max:255',
            'allergies' => 'nullable|string',
            'medications' => 'nullable|string',
            'anesthetics' => 'nullable|string',
            'anesthetics_others' => 'nullable|string|max:255',
            'previous_hospitalizations_surgeries' => 'nullable|string',
            'smoker' => 'nullable|in:yes,no',
            'alcoholic_drinker' => 'nullable|in:yes,no',
            'known_family_illnesses' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // If this is the first medical info or user wants to set as default, set is_default
            $isDefault = $request->has('is_default') && $request->is_default;
            
            // If setting as default, unset all other defaults
            if ($isDefault) {
                Auth::user()->medicalInformation()->update(['is_default' => false]);
            } else {
                // If no medical info exists, make this the default
                $existingCount = Auth::user()->medicalInformation()->count();
                if ($existingCount === 0) {
                    $isDefault = true;
                }
            }

            $medicalInfo = MedicalInformation::create([
                'user_id' => Auth::id(),
                'hypertension' => $request->has('hypertension') ? (bool)$request->hypertension : false,
                'diabetes' => $request->has('diabetes') ? (bool)$request->diabetes : false,
                'comorbidities_others' => $request->comorbidities_others,
                'allergies' => $request->allergies,
                'medications' => $request->medications,
                'anesthetics' => $request->anesthetics,
                'anesthetics_others' => $request->anesthetics_others,
                'previous_hospitalizations_surgeries' => $request->previous_hospitalizations_surgeries,
                'smoker' => $request->smoker,
                'alcoholic_drinker' => $request->alcoholic_drinker,
                'known_family_illnesses' => $request->known_family_illnesses,
                'is_default' => $isDefault,
            ]);

            DB::commit();
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'medical_info' => $medicalInfo], 201);
            }
            
            // For regular form submission, redirect back to select page
            return redirect()->route('medical-information.select')->with('success', 'Medical information saved successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to save medical information.'], 500);
            }
            
            return back()->withInput()->with('error', 'Failed to save medical information. Please try again.');
        }
    }

    public function update(Request $request, MedicalInformation $medicalInformation)
    {
        // Ensure the medical info belongs to the authenticated user
        if ($medicalInformation->user_id !== Auth::id()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            }
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'hypertension' => 'nullable|boolean',
            'diabetes' => 'nullable|boolean',
            'comorbidities_others' => 'nullable|string|max:255',
            'allergies' => 'nullable|string',
            'medications' => 'nullable|string',
            'anesthetics' => 'nullable|string',
            'anesthetics_others' => 'nullable|string|max:255',
            'previous_hospitalizations_surgeries' => 'nullable|string',
            'smoker' => 'nullable|in:yes,no',
            'alcoholic_drinker' => 'nullable|in:yes,no',
            'known_family_illnesses' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $isDefault = $request->has('is_default') && $request->is_default;
            
            // If setting as default, unset all other defaults
            if ($isDefault && !$medicalInformation->is_default) {
                Auth::user()->medicalInformation()->where('id', '!=', $medicalInformation->id)->update(['is_default' => false]);
            }

            $medicalInformation->update([
                'hypertension' => $request->has('hypertension') ? (bool)$request->hypertension : false,
                'diabetes' => $request->has('diabetes') ? (bool)$request->diabetes : false,
                'comorbidities_others' => $request->comorbidities_others,
                'allergies' => $request->allergies,
                'medications' => $request->medications,
                'anesthetics' => $request->anesthetics,
                'anesthetics_others' => $request->anesthetics_others,
                'previous_hospitalizations_surgeries' => $request->previous_hospitalizations_surgeries,
                'smoker' => $request->smoker,
                'alcoholic_drinker' => $request->alcoholic_drinker,
                'known_family_illnesses' => $request->known_family_illnesses,
                'is_default' => $isDefault,
            ]);

            DB::commit();
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'medical_info' => $medicalInformation->fresh()]);
            }
            
            // For regular form submission, redirect back to select page
            return redirect()->route('medical-information.select')->with('success', 'Medical information updated successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to update medical information.'], 500);
            }
            
            return back()->withInput()->with('error', 'Failed to update medical information. Please try again.');
        }
    }

    public function destroy(MedicalInformation $medicalInformation)
    {
        // Ensure the medical info belongs to the authenticated user
        if ($medicalInformation->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        try {
            $wasDefault = $medicalInformation->is_default;
            $medicalInformation->delete();

            // If deleted medical info was default, set the first remaining one as default
            if ($wasDefault) {
                $firstMedicalInfo = Auth::user()->medicalInformation()->first();
                if ($firstMedicalInfo) {
                    $firstMedicalInfo->update(['is_default' => true]);
                }
            }

            return response()->json(['success' => true, 'message' => 'Medical information deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete medical information.'], 500);
        }
    }
}
