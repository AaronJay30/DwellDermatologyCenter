<?php

namespace App\Http\Controllers;

use App\Models\EmergencyContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmergencyContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:patient');
    }

    public function select()
    {
        $emergencyContacts = Auth::user()->emergencyContacts()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();
        return view('emergency-contact.select', compact('emergencyContacts'));
    }

    public function create()
    {
        return view('emergency-contact.create');
    }

    public function edit(EmergencyContact $emergencyContact)
    {
        // Ensure the emergency contact belongs to the authenticated user
        if ($emergencyContact->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }
        
        return view('emergency-contact.edit', compact('emergencyContact'));
    }

    public function show(EmergencyContact $emergencyContact)
    {
        // Ensure the emergency contact belongs to the authenticated user
        if ($emergencyContact->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        return response()->json(['success' => true, 'emergency_contact' => $emergencyContact]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'relationship' => 'required|string|max:255',
            'address' => 'required|string',
            'contact_number' => 'required|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            // If this is the first emergency contact or user wants to set as default, set is_default
            $isDefault = $request->has('is_default') && $request->is_default;
            
            // If setting as default, unset all other defaults
            if ($isDefault) {
                Auth::user()->emergencyContacts()->update(['is_default' => false]);
            } else {
                // If no emergency contact exists, make this the default
                $existingCount = Auth::user()->emergencyContacts()->count();
                if ($existingCount === 0) {
                    $isDefault = true;
                }
            }

            $emergencyContact = EmergencyContact::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'relationship' => $request->relationship,
                'address' => $request->address,
                'contact_number' => $request->contact_number,
                'is_default' => $isDefault,
            ]);

            DB::commit();
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'emergency_contact' => $emergencyContact], 201);
            }
            
            // For regular form submission, redirect back to select page
            return redirect()->route('emergency-contact.select')->with('success', 'Emergency contact saved successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to save emergency contact.'], 500);
            }
            
            return back()->withInput()->with('error', 'Failed to save emergency contact. Please try again.');
        }
    }

    public function update(Request $request, EmergencyContact $emergencyContact)
    {
        // Ensure the emergency contact belongs to the authenticated user
        if ($emergencyContact->user_id !== Auth::id()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            }
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'relationship' => 'required|string|max:255',
            'address' => 'required|string',
            'contact_number' => 'required|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            $isDefault = $request->has('is_default') && $request->is_default;
            
            // If setting as default, unset all other defaults
            if ($isDefault && !$emergencyContact->is_default) {
                Auth::user()->emergencyContacts()->where('id', '!=', $emergencyContact->id)->update(['is_default' => false]);
            }

            $emergencyContact->update([
                'name' => $request->name,
                'relationship' => $request->relationship,
                'address' => $request->address,
                'contact_number' => $request->contact_number,
                'is_default' => $isDefault,
            ]);

            DB::commit();
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'emergency_contact' => $emergencyContact->fresh()]);
            }
            
            // For regular form submission, redirect back to select page
            return redirect()->route('emergency-contact.select')->with('success', 'Emergency contact updated successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to update emergency contact.'], 500);
            }
            
            return back()->withInput()->with('error', 'Failed to update emergency contact. Please try again.');
        }
    }

    public function destroy(EmergencyContact $emergencyContact)
    {
        // Ensure the emergency contact belongs to the authenticated user
        if ($emergencyContact->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        try {
            $wasDefault = $emergencyContact->is_default;
            $emergencyContact->delete();

            // If deleted emergency contact was default, set the first remaining one as default
            if ($wasDefault) {
                $firstEmergencyContact = Auth::user()->emergencyContacts()->first();
                if ($firstEmergencyContact) {
                    $firstEmergencyContact->update(['is_default' => true]);
                }
            }

            return response()->json(['success' => true, 'message' => 'Emergency contact deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete emergency contact.'], 500);
        }
    }
}
