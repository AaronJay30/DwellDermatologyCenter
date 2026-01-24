<?php

namespace App\Http\Controllers;

use App\Models\PersonalInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PersonalInformationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:patient');
    }

    public function index()
    {
        $profiles = Auth::user()->personalInformation()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();
        return response()->json($profiles);
    }

    public function select()
    {
        $profiles = Auth::user()->personalInformation()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();
        return view('personal-information.select', compact('profiles'));
    }

    public function create()
    {
        return view('personal-information.create');
    }

    public function edit(PersonalInformation $personalInformation)
    {
        // Ensure the profile belongs to the authenticated user
        if ($personalInformation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }
        
        // Find related medical information and emergency contact
        // Try to find by matching is_default first, then by user_id
        $medicalInfo = Auth::user()->medicalInformation()
            ->where('is_default', $personalInformation->is_default)
            ->first();
        
        if (!$medicalInfo) {
            $medicalInfo = Auth::user()->medicalInformation()->first();
        }

        $emergencyContact = Auth::user()->emergencyContacts()
            ->where('is_default', $personalInformation->is_default)
            ->first();
        
        if (!$emergencyContact) {
            $emergencyContact = Auth::user()->emergencyContacts()->first();
        }
        
        return view('personal-information.edit', compact('personalInformation', 'medicalInfo', 'emergencyContact'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:1',
            'last_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'birthday' => 'required|date|before:today',
            'contact_number' => 'required|string|max:20',
            'label' => 'nullable|string|max:50',
            'signature' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // If this is the first profile or user wants to set as default, set is_default
            $isDefault = $request->has('is_default') && $request->is_default;
            
            // If setting as default, unset all other defaults
            if ($isDefault) {
                Auth::user()->personalInformation()->update(['is_default' => false]);
            } else {
                // If no profiles exist, make this the default
                $existingCount = Auth::user()->personalInformation()->count();
                if ($existingCount === 0) {
                    $isDefault = true;
                }
            }

            $profile = PersonalInformation::create([
                'user_id' => Auth::id(),
                'first_name' => $request->first_name,
                'middle_initial' => $request->middle_initial,
                'last_name' => $request->last_name,
                'address' => $request->address,
                'birthday' => $request->birthday,
                'contact_number' => $request->contact_number,
                'label' => $request->label ?? 'HOME',
                'is_default' => $isDefault,
                'signature' => $request->signature,
            ]);

            DB::commit();
            
            // Check if request is AJAX/JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'profile' => $profile], 201);
            }
            
            // For regular form submission, redirect back
            return redirect()->route('personal-information.select')->with('success', 'Personal information saved successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            
            // Check if request is AJAX/JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to save personal information.'], 500);
            }
            
            // For regular form submission, redirect back with error
            return back()->withInput()->with('error', 'Failed to save personal information. Please try again.');
        }
    }

    public function update(Request $request, PersonalInformation $personalInformation)
    {
        // Ensure the profile belongs to the authenticated user
        if ($personalInformation->user_id !== Auth::id()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            }
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:1',
            'last_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'birthday' => 'required|date|before:today',
            'contact_number' => 'required|string|max:20',
            'label' => 'nullable|string|max:50',
            'signature' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $isDefault = $request->has('is_default') && $request->is_default;
            
            // If setting as default, unset all other defaults
            if ($isDefault && !$personalInformation->is_default) {
                Auth::user()->personalInformation()->where('id', '!=', $personalInformation->id)->update(['is_default' => false]);
            }

            $personalInformation->update([
                'first_name' => $request->first_name,
                'middle_initial' => $request->middle_initial,
                'last_name' => $request->last_name,
                'address' => $request->address,
                'birthday' => $request->birthday,
                'contact_number' => $request->contact_number,
                'label' => $request->label ?? 'HOME',
                'is_default' => $isDefault,
                'signature' => $request->signature,
            ]);

            DB::commit();
            
            // Check if request is AJAX/JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'profile' => $personalInformation->fresh()]);
            }
            
            // For regular form submission, redirect back
            return redirect()->route('personal-information.select')->with('success', 'Personal information updated successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            
            // Check if request is AJAX/JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to update personal information.'], 500);
            }
            
            // For regular form submission, redirect back with error
            return back()->withInput()->with('error', 'Failed to update personal information. Please try again.');
        }
    }

    public function destroy(PersonalInformation $personalInformation)
    {
        // Ensure the profile belongs to the authenticated user
        if ($personalInformation->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        try {
            $wasDefault = $personalInformation->is_default;
            $personalInformation->delete();

            // If deleted profile was default, set the first remaining profile as default
            if ($wasDefault) {
                $firstProfile = Auth::user()->personalInformation()->first();
                if ($firstProfile) {
                    $firstProfile->update(['is_default' => true]);
                }
            }

            return response()->json(['success' => true, 'message' => 'Personal information deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete personal information.'], 500);
        }
    }

    public function setDefault(PersonalInformation $personalInformation)
    {
        // Ensure the profile belongs to the authenticated user
        if ($personalInformation->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        DB::beginTransaction();
        try {
            // Unset all other defaults
            Auth::user()->personalInformation()->where('id', '!=', $personalInformation->id)->update(['is_default' => false]);
            
            // Set this as default
            $personalInformation->update(['is_default' => true]);

            DB::commit();
            return response()->json(['success' => true, 'profile' => $personalInformation->fresh()]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Failed to set default profile.'], 500);
        }
    }

    public function patientInformationSheet()
    {
        return view('patient-information-sheet');
    }

    public function storePatientInformationSheet(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'birthday' => 'required|date|before:today',
            'address' => 'required|string|max:500',
            'contact_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'civil_status' => 'required|in:Single,Married',
            'sex' => 'required|in:male,female',
            'preferred_pronoun' => 'nullable|string|max:50',
            'hypertension' => 'nullable|boolean',
            'diabetes' => 'nullable|boolean',
            'comorbidities_others' => 'nullable|string|max:255',
            'allergies_medications' => 'nullable|boolean',
            'allergies_anesthetics' => 'nullable|boolean',
            'allergies_others' => 'nullable|string|max:255',
            'previous_hospitalizations_surgeries' => 'nullable|string|max:1000',
            'alcoholic_drinker' => 'nullable|in:Yes,No',
            'smoker' => 'nullable|in:Yes,No',
            'known_family_illnesses' => 'nullable|string|max:1000',
            'emergency_name' => 'required|string|max:255',
            'emergency_relationship' => 'required|string|max:255',
            'emergency_address' => 'required|string|max:500',
            'emergency_contact_number' => 'required|string|max:20',
            'signature' => 'required|string',
            'date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            // Parse name into first, middle, last
            // Handle names like "Aliah Kate M. Taban" or "John Doe" or "Mary Jane Smith"
            $nameParts = array_filter(explode(' ', trim($request->name)), function($part) {
                return !empty(trim($part));
            });
            $nameParts = array_values($nameParts);
            
            $firstName = $nameParts[0] ?? '';
            $middleInitial = '';
            $lastName = '';
            
            if (count($nameParts) >= 2) {
                // Check if second part is a middle initial (single letter or letter with period)
                $secondPart = trim($nameParts[1], '.');
                if (strlen($secondPart) === 1) {
                    $middleInitial = $secondPart;
                    $lastName = count($nameParts) > 2 ? implode(' ', array_slice($nameParts, 2)) : '';
                } else {
                    // Second part is part of last name or first name
                    if (count($nameParts) >= 3) {
                        // Assume format: First Middle Last
                        $middleInitial = substr($nameParts[1], 0, 1);
                        $lastName = implode(' ', array_slice($nameParts, 2));
                    } else {
                        // Only two parts: First Last
                        $lastName = $nameParts[1];
                    }
                }
            }
            
            // If still no last name, use empty string
            if (empty($lastName) && count($nameParts) > 1) {
                $lastName = $nameParts[count($nameParts) - 1];
            }

            // Set as default if this is the first profile
            $isDefault = Auth::user()->personalInformation()->count() === 0;
            if ($isDefault) {
                Auth::user()->personalInformation()->update(['is_default' => false]);
            }

            // Create Personal Information
            $personalInfo = PersonalInformation::create([
                'user_id' => Auth::id(),
                'first_name' => $firstName,
                'middle_initial' => $middleInitial,
                'last_name' => $lastName,
                'address' => $request->address,
                'birthday' => $request->birthday,
                'civil_status' => $request->civil_status,
                'preferred_pronoun' => $request->preferred_pronoun,
                'contact_number' => $request->contact_number,
                'is_default' => $isDefault,
                'signature' => $request->signature,
            ]);

            // Create Medical Information
            $medicalInfo = \App\Models\MedicalInformation::create([
                'user_id' => Auth::id(),
                'hypertension' => $request->has('hypertension') && $request->hypertension,
                'diabetes' => $request->has('diabetes') && $request->diabetes,
                'comorbidities_others' => $request->comorbidities_others,
                'allergies' => $request->allergies_medications || $request->allergies_anesthetics ? 
                    ($request->allergies_medications ? 'Medications' : '') . 
                    ($request->allergies_anesthetics ? ($request->allergies_medications ? ', ' : '') . 'Anesthetics' : '') .
                    ($request->allergies_others ? ($request->allergies_medications || $request->allergies_anesthetics ? ', ' : '') . $request->allergies_others : '') : null,
                'previous_hospitalizations_surgeries' => $request->previous_hospitalizations_surgeries,
                'smoker' => $request->smoker === 'Yes' ? 'yes' : ($request->smoker === 'No' ? 'no' : null),
                'alcoholic_drinker' => $request->alcoholic_drinker === 'Yes' ? 'yes' : ($request->alcoholic_drinker === 'No' ? 'no' : null),
                'known_family_illnesses' => $request->known_family_illnesses,
                'is_default' => $isDefault,
            ]);

            // Create Emergency Contact
            \App\Models\EmergencyContact::create([
                'user_id' => Auth::id(),
                'name' => $request->emergency_name,
                'relationship' => $request->emergency_relationship,
                'address' => $request->emergency_address,
                'contact_number' => $request->emergency_contact_number,
                'is_default' => $isDefault,
            ]);

            DB::commit();

            return redirect()->route('patient-information-sheet')->with('success', 'Patient information sheet submitted successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Failed to save patient information. Please try again.');
        }
    }

    public function addPatientInformation()
    {
        return view('personal-information.add-patient-information');
    }

    public function storeAddPatientInformation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'birthday' => 'required|date|before:today',
            'address' => 'required|string|max:500',
            'contact_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'civil_status' => 'required|in:Single,Married',
            'sex' => 'required|in:male,female',
            'preferred_pronoun' => 'nullable|string|max:50',
            'hypertension' => 'nullable|boolean',
            'diabetes' => 'nullable|boolean',
            'comorbidities_others' => 'nullable|string|max:255',
            'allergies_medications' => 'nullable|boolean',
            'allergies_anesthetics' => 'nullable|boolean',
            'allergies_others' => 'nullable|string|max:255',
            'previous_hospitalizations_surgeries' => 'nullable|string',
            'alcoholic_drinker' => 'nullable|in:Yes,No',
            'smoker' => 'nullable|in:Yes,No',
            'known_family_illnesses' => 'nullable|string',
            'emergency_name' => 'required|string|max:255',
            'emergency_relationship' => 'required|string|max:255',
            'emergency_address' => 'required|string|max:500',
            'emergency_contact_number' => 'required|string|max:20',
            'signature' => 'required|string',
            'date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            // Parse name into first, middle, last
            $nameParts = explode(' ', trim($request->name));
            $first_name = $nameParts[0] ?? '';
            $middle_initial = isset($nameParts[1]) && strlen($nameParts[1]) === 1 ? $nameParts[1] : (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : '');
            $last_name = isset($nameParts[2]) ? implode(' ', array_slice($nameParts, 2)) : (isset($nameParts[1]) && strlen($nameParts[1]) > 1 ? $nameParts[1] : '');

            // Set as default if no profiles exist
            $isDefault = !Auth::user()->personalInformation()->exists();
            if ($isDefault) {
                Auth::user()->personalInformation()->update(['is_default' => false]);
            }

            // Create Personal Information
            $personalInfo = PersonalInformation::create([
                'user_id' => Auth::id(),
                'first_name' => $first_name,
                'middle_initial' => $middle_initial,
                'last_name' => $last_name,
                'address' => $request->address,
                'birthday' => $request->birthday,
                'civil_status' => $request->civil_status,
                'preferred_pronoun' => $request->preferred_pronoun,
                'contact_number' => $request->contact_number,
                'label' => 'HOME',
                'is_default' => $isDefault,
                'signature' => $request->signature,
            ]);

            // Create Medical Information
            $medicalInfo = \App\Models\MedicalInformation::create([
                'user_id' => Auth::id(),
                'hypertension' => $request->has('hypertension') && $request->hypertension == '1',
                'diabetes' => $request->has('diabetes') && $request->diabetes == '1',
                'comorbidities_others' => $request->comorbidities_others ?? null,
                'allergies' => $this->buildAllergiesString($request),
                'medications' => null,
                'anesthetics' => $request->has('allergies_anesthetics') && $request->allergies_anesthetics == '1',
                'anesthetics_others' => $request->allergies_others ?? null,
                'previous_hospitalizations_surgeries' => $request->previous_hospitalizations_surgeries ?? null,
                'smoker' => $request->smoker === 'Yes' ? 'yes' : ($request->smoker === 'No' ? 'no' : null),
                'alcoholic_drinker' => $request->alcoholic_drinker === 'Yes' ? 'yes' : ($request->alcoholic_drinker === 'No' ? 'no' : null),
                'known_family_illnesses' => $request->known_family_illnesses ?? null,
                'is_default' => $isDefault,
            ]);

            // Create Emergency Contact
            $emergencyContact = \App\Models\EmergencyContact::create([
                'user_id' => Auth::id(),
                'name' => $request->emergency_name,
                'relationship' => $request->emergency_relationship,
                'address' => $request->emergency_address,
                'contact_number' => $request->emergency_contact_number,
                'is_default' => $isDefault,
            ]);

            DB::commit();

            return redirect()->route('personal-information.select')->with('success', 'Patient information saved successfully! You can add another entry by clicking "Add address".');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Failed to save patient information: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withInput()->with('error', 'Failed to save patient information: ' . $e->getMessage());
        }
    }

    public function editPatientInformation(PersonalInformation $personalInformation)
    {
        // Ensure the profile belongs to the authenticated user
        if ($personalInformation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        // Find related medical information and emergency contact
        // Try to find by matching is_default first, then by user_id
        $medicalInfo = Auth::user()->medicalInformation()
            ->where('is_default', $personalInformation->is_default)
            ->first();
        
        if (!$medicalInfo) {
            $medicalInfo = Auth::user()->medicalInformation()->first();
        }

        $emergencyContact = Auth::user()->emergencyContacts()
            ->where('is_default', $personalInformation->is_default)
            ->first();
        
        if (!$emergencyContact) {
            $emergencyContact = Auth::user()->emergencyContacts()->first();
        }

        return view('personal-information.edit-patient-information', compact('personalInformation', 'medicalInfo', 'emergencyContact'));
    }

    public function updatePatientInformation(Request $request, PersonalInformation $personalInformation)
    {
        // Ensure the profile belongs to the authenticated user
        if ($personalInformation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'birthday' => 'required|date|before:today',
            'address' => 'required|string|max:500',
            'contact_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'civil_status' => 'nullable|in:Single,Married',
            'sex' => 'nullable|in:male,female',
            'preferred_pronoun' => 'nullable|string|max:50',
            'hypertension' => 'nullable|boolean',
            'diabetes' => 'nullable|boolean',
            'comorbidities_others' => 'nullable|string|max:255',
            'allergies_medications' => 'nullable|boolean',
            'allergies_anesthetics' => 'nullable|boolean',
            'allergies_others' => 'nullable|string|max:255',
            'previous_hospitalizations_surgeries' => 'nullable|string',
            'alcoholic_drinker' => 'nullable|in:Yes,No',
            'smoker' => 'nullable|in:Yes,No',
            'known_family_illnesses' => 'nullable|string',
            'emergency_name' => 'required|string|max:255',
            'emergency_relationship' => 'required|string|max:255',
            'emergency_address' => 'required|string|max:500',
            'emergency_contact_number' => 'required|string|max:20',
            'signature' => 'required|string',
            'date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            // Parse name into first, middle, last
            $nameParts = explode(' ', trim($request->name));
            $first_name = $nameParts[0] ?? '';
            $middle_initial = isset($nameParts[1]) && strlen($nameParts[1]) === 1 ? $nameParts[1] : (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : '');
            $last_name = isset($nameParts[2]) ? implode(' ', array_slice($nameParts, 2)) : (isset($nameParts[1]) && strlen($nameParts[1]) > 1 ? $nameParts[1] : '');

            // Update Personal Information
            $personalInformation->update([
                'first_name' => $first_name,
                'middle_initial' => $middle_initial,
                'last_name' => $last_name,
                'address' => $request->address,
                'birthday' => $request->birthday,
                'civil_status' => $request->civil_status,
                'preferred_pronoun' => $request->preferred_pronoun,
                'contact_number' => $request->contact_number,
                'label' => 'HOME',
                'signature' => $request->signature,
            ]);

            // Find or create Medical Information
            $medicalInfo = Auth::user()->medicalInformation()
                ->where('is_default', $personalInformation->is_default)
                ->first();
            
            if (!$medicalInfo) {
                $medicalInfo = Auth::user()->medicalInformation()->first();
            }

            if ($medicalInfo) {
                $medicalInfo->update([
                    'hypertension' => $request->has('hypertension') ? (bool)$request->hypertension : false,
                    'diabetes' => $request->has('diabetes') ? (bool)$request->diabetes : false,
                    'comorbidities_others' => $request->comorbidities_others,
                    'allergies' => $request->allergies_medications || $request->allergies_anesthetics || $request->allergies_others ? 
                        ($request->allergies_medications ? 'Medications, ' : '') . 
                        ($request->allergies_anesthetics ? 'Anesthetics, ' : '') . 
                        ($request->allergies_others ?? '') : null,
                    'medications' => null,
                    'anesthetics' => $request->has('allergies_anesthetics') ? (bool)$request->allergies_anesthetics : false,
                    'anesthetics_others' => $request->allergies_others,
                    'previous_hospitalizations_surgeries' => $request->previous_hospitalizations_surgeries,
                    'smoker' => $request->smoker === 'Yes' ? 'yes' : ($request->smoker === 'No' ? 'no' : null),
                    'alcoholic_drinker' => $request->alcoholic_drinker === 'Yes' ? 'yes' : ($request->alcoholic_drinker === 'No' ? 'no' : null),
                    'known_family_illnesses' => $request->known_family_illnesses,
                ]);
            } else {
                $medicalInfo = \App\Models\MedicalInformation::create([
                    'user_id' => Auth::id(),
                    'hypertension' => $request->has('hypertension') ? (bool)$request->hypertension : false,
                    'diabetes' => $request->has('diabetes') ? (bool)$request->diabetes : false,
                    'comorbidities_others' => $request->comorbidities_others,
                    'allergies' => $request->allergies_medications || $request->allergies_anesthetics || $request->allergies_others ? 
                        ($request->allergies_medications ? 'Medications, ' : '') . 
                        ($request->allergies_anesthetics ? 'Anesthetics, ' : '') . 
                        ($request->allergies_others ?? '') : null,
                    'medications' => null,
                    'anesthetics' => $request->has('allergies_anesthetics') ? (bool)$request->allergies_anesthetics : false,
                    'anesthetics_others' => $request->allergies_others,
                    'previous_hospitalizations_surgeries' => $request->previous_hospitalizations_surgeries,
                    'smoker' => $request->smoker === 'Yes' ? 'yes' : ($request->smoker === 'No' ? 'no' : null),
                    'alcoholic_drinker' => $request->alcoholic_drinker === 'Yes' ? 'yes' : ($request->alcoholic_drinker === 'No' ? 'no' : null),
                    'known_family_illnesses' => $request->known_family_illnesses,
                    'is_default' => $personalInformation->is_default,
                ]);
            }

            // Find or create Emergency Contact
            $emergencyContact = Auth::user()->emergencyContacts()
                ->where('is_default', $personalInformation->is_default)
                ->first();
            
            if (!$emergencyContact) {
                $emergencyContact = Auth::user()->emergencyContacts()->first();
            }

            if ($emergencyContact) {
                $emergencyContact->update([
                    'name' => $request->emergency_name,
                    'relationship' => $request->emergency_relationship,
                    'address' => $request->emergency_address,
                    'contact_number' => $request->emergency_contact_number,
                ]);
            } else {
                $emergencyContact = \App\Models\EmergencyContact::create([
                    'user_id' => Auth::id(),
                    'name' => $request->emergency_name,
                    'relationship' => $request->emergency_relationship,
                    'address' => $request->emergency_address,
                    'contact_number' => $request->emergency_contact_number,
                    'is_default' => $personalInformation->is_default,
                ]);
            }

            DB::commit();

            return redirect()->route('consultations.medical')->with('success', 'Patient information updated successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Failed to update patient information. Please try again.');
        }
    }

    /**
     * Build allergies string from request
     */
    private function buildAllergiesString(Request $request): ?string
    {
        $allergies = [];
        
        if ($request->has('allergies_medications') && $request->allergies_medications == '1') {
            $allergies[] = 'Medications';
        }
        
        if ($request->has('allergies_anesthetics') && $request->allergies_anesthetics == '1') {
            $allergies[] = 'Anesthetics';
        }
        
        if (!empty($request->allergies_others)) {
            $allergies[] = $request->allergies_others;
        }
        
        return !empty($allergies) ? implode(', ', $allergies) : null;
    }
}
