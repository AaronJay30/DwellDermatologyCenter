<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AvailableDoctorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Show the AVAILABLE DOCTOR page for the admin's branch.
     */
    public function edit(Request $request)
    {
        $admin = Auth::user();
        $branch = $admin->branch;

        if (!$branch) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'You are not assigned to any branch. Please contact the system administrator.');
        }

        return view('admin.available-doctor', compact('branch'));
    }

    /**
     * Upload / replace the available doctor schedule photo for the admin's branch.
     */
    public function update(Request $request)
    {
        $admin = Auth::user();
        $branch = $admin->branch;

        if (!$branch) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'You are not assigned to any branch. Please contact the system administrator.');
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        // Remove previous available doctor photo (single photo only)
        if ($branch->available_doctor_image_path && Storage::disk('public')->exists($branch->available_doctor_image_path)) {
            Storage::disk('public')->delete($branch->available_doctor_image_path);
        }

        $path = $request->file('photo')->store('available-doctor', 'public');

        $branch->available_doctor_image_path = $path;
        $branch->save();

        return redirect()
            ->route('admin.available-doctor.edit')
            ->with('success', 'Available doctor schedule photo has been updated successfully.');
    }
}

